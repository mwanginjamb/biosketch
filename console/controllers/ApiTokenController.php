<?php

declare(strict_types=1);

namespace console\controllers;

use common\components\RbacHelper;
use common\models\User;
use common\models\UserApiToken;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Issues and manages API bearer tokens (see docs/API.md).
 *
 *   php yii api-token/create-integration <username> [--email=] [--role=] [--name=] [--days=]
 *       Creates an API-only integration user and prints its token.
 *   php yii api-token/issue <username> [--name=] [--days=]
 *       Issues another token for an existing active user.
 *   php yii api-token/list [username]          tokens (never the token values)
 *   php yii api-token/revoke <token-id>
 *   php yii api-token/revoke-all <username>
 *
 * --days: token lifetime in days (default 365; 0 = never expires).
 * --role: RBAC role for a new integration user. Default none: the token can read
 *         published (active) profiles only. Use --role=drd to also read drafts.
 *
 * The token is shown once; only its SHA-256 hash is stored.
 */
class ApiTokenController extends Controller
{
    public const DEFAULT_DAYS = 365;

    /** @var string|null e-mail for a new integration user (default <username>@integration.invalid) */
    public $email;

    /** @var string|null RBAC role for a new integration user */
    public $role;

    /** @var string|null token label, e.g. the consuming system */
    public $name;

    /** @var int|string|null token lifetime in days; 0 = never expires */
    public $days;

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        return match ($actionID) {
            'create-integration' => [...$options, 'email', 'role', 'name', 'days'],
            'issue' => [...$options, 'name', 'days'],
            default => $options,
        };
    }

    public function optionAliases(): array
    {
        return [...parent::optionAliases(), 'e' => 'email', 'r' => 'role', 'n' => 'name', 'd' => 'days'];
    }

    public function actionCreateIntegration(string $username): int
    {
        $ttl = $this->ttlSeconds();
        if ($ttl === null) {
            return ExitCode::USAGE;
        }

        if (User::find()->where(['username' => $username])->exists()) {
            $this->stderr("User '{$username}' already exists. Use `php yii api-token/issue {$username}` instead.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $role = $this->role !== null && $this->role !== '' ? (string) $this->role : null;
        if ($role !== null && Yii::$app->authManager->getRole($role) === null) {
            $this->stderr("Role '{$role}' does not exist. See `php yii rbac/...` or run `php yii migrate`.\n", Console::FG_RED);
            return ExitCode::CONFIG;
        }
        if ($role === RbacHelper::ROLE_ADMIN) {
            $this->stderr("Refusing to give an integration account the admin role.\n", Console::FG_RED);
            return ExitCode::USAGE;
        }

        $email = $this->email ?: $username . '@integration.invalid';   // .invalid: reserved, never deliverable
        if (User::find()->where(['email' => $email])->exists()) {
            $this->stderr("E-mail '{$email}' is already used by another account. Pass a different --email.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->status = User::STATUS_ACTIVE;   // no e-mail verification for a machine account
        // Nobody knows this password, so the account cannot sign in to the web app.
        $user->setPassword(Yii::$app->security->generateRandomString(64));
        $user->generateAuthKey();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$user->save()) {
                $transaction->rollBack();
                foreach ($user->getFirstErrors() as $error) {
                    $this->stderr($error . "\n", Console::FG_RED);
                }
                return ExitCode::DATAERR;
            }
            if ($role !== null) {
                RbacHelper::assign($role, (int) $user->id);
            }
            [$token, $plain] = UserApiToken::issue($user, $this->name ?: 'integration', $ttl);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->stdout("Created integration user '{$user->username}' (id {$user->id}, role: " . ($role ?? 'none') . ").\n", Console::FG_GREEN);
        $this->printToken($token, $plain);

        return ExitCode::OK;
    }

    public function actionIssue(string $username): int
    {
        $ttl = $this->ttlSeconds();
        if ($ttl === null) {
            return ExitCode::USAGE;
        }

        $user = User::findByUsername($username);   // active users only
        if ($user === null) {
            $this->stderr("No active user '{$username}'.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        [$token, $plain] = UserApiToken::issue($user, $this->name ?: 'console', $ttl);
        $this->printToken($token, $plain);

        return ExitCode::OK;
    }

    public function actionList(?string $username = null): int
    {
        $query = UserApiToken::find()->with('user')->orderBy(['id' => SORT_ASC]);
        if ($username !== null) {
            $user = User::findOne(['username' => $username]);
            if ($user === null) {
                $this->stderr("No user '{$username}'.\n", Console::FG_RED);
                return ExitCode::DATAERR;
            }
            $query->andWhere(['user_id' => $user->id]);
        }

        $tokens = $query->all();
        if ($tokens === []) {
            $this->stdout("No tokens.\n");
            return ExitCode::OK;
        }

        $date = static fn(?int $ts): string => $ts ? date('Y-m-d H:i', $ts) : '-';
        $this->stdout(sprintf("%-6s %-20s %-20s %-16s %-16s %-16s\n", 'ID', 'USER', 'NAME', 'CREATED', 'EXPIRES', 'LAST USED'), Console::BOLD);
        foreach ($tokens as $token) {
            $expired = $token->expires_at !== null && (int) $token->expires_at <= time();
            $line = sprintf(
                "%-6d %-20s %-20s %-16s %-16s %-16s\n",
                $token->id,
                $token->user->username ?? '?',
                (string) $token->name,
                $date((int) $token->created_at),
                $token->expires_at === null ? 'never' : $date((int) $token->expires_at),
                $date($token->last_used_at === null ? null : (int) $token->last_used_at),
            );
            $expired ? $this->stdout($line, Console::FG_GREY) : $this->stdout($line);
        }

        return ExitCode::OK;
    }

    public function actionRevoke(int $id): int
    {
        $token = UserApiToken::findOne($id);
        if ($token === null) {
            $this->stderr("No token with id {$id}.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $token->delete();
        $this->stdout("Revoked token {$id}.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    public function actionRevokeAll(string $username): int
    {
        $user = User::findOne(['username' => $username]);
        if ($user === null) {
            $this->stderr("No user '{$username}'.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $count = UserApiToken::deleteAll(['user_id' => $user->id]);
        $this->stdout("Revoked {$count} token(s) for {$user->username}.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /** --days as seconds (0 = never expires), or null after printing an error. */
    private function ttlSeconds(): ?int
    {
        $days = $this->days ?? self::DEFAULT_DAYS;
        if (!is_numeric($days) || (int) $days < 0 || (string) (int) $days !== (string) $days) {
            $this->stderr("--days must be a whole number of days (0 = never expires).\n", Console::FG_RED);
            return null;
        }

        return (int) $days * 86400;
    }

    private function printToken(UserApiToken $token, string $plain): void
    {
        $expires = $token->expires_at === null ? 'never' : date('Y-m-d H:i', (int) $token->expires_at);
        $this->stdout("\nToken (id {$token->id}, expires {$expires}). Copy it now; it cannot be shown again:\n\n");
        $this->stdout("  {$plain}\n\n", Console::BOLD);
        $this->stdout("Use it as:  Authorization: Bearer <token>\n");
    }
}
