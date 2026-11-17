<?php

declare(strict_types=1);

namespace frontend\tests\Unit\Components;

use Codeception\Test\Unit;
use common\components\RbacHelper;
use common\models\User;
use common\models\UserApiToken;
use console\controllers\ApiTokenController;
use frontend\tests\Support\UnitTester;
use Yii;
use yii\console\ExitCode;

/**
 * php yii api-token/... (console\controllers\ApiTokenController).
 */
final class ApiTokenCommandTest extends Unit
{
    protected UnitTester $tester;

    private function runCommand(string $action, array $params = []): int
    {
        $controller = new class ('api-token', Yii::$app) extends ApiTokenController {
            public string $out = '';

            public function stdout($string)
            {
                $this->out .= $string;
                return strlen($string);
            }

            public function stderr($string)
            {
                $this->out .= $string;
                return strlen($string);
            }
        };

        return $controller->runAction($action, $params);
    }

    public function testCreateIntegrationMakesApiOnlyUserWithWorkingToken(): void
    {
        $this->assertSame(ExitCode::OK, $this->runCommand('create-integration', ['hr-system', 'name' => 'HRIS', 'days' => '0']));

        $user = User::findOne(['username' => 'hr-system']);
        $this->assertNotNull($user);
        $this->assertSame(User::STATUS_ACTIVE, (int) $user->status);
        $this->assertSame('hr-system@integration.invalid', $user->email);
        $this->assertSame([], RbacHelper::roleNamesForUser((int) $user->id), 'no role by default');

        $token = UserApiToken::findOne(['user_id' => $user->id]);
        $this->assertSame('HRIS', $token->name);
        $this->assertNull($token->expires_at, '--days=0 never expires');
    }

    public function testPrintedTokenAuthenticatesTheUser(): void
    {
        $controller = new class ('api-token', Yii::$app) extends ApiTokenController {
            public string $out = '';

            public function stdout($string)
            {
                $this->out .= $string;
                return strlen($string);
            }
        };
        $controller->runAction('create-integration', ['crm-sync', 'role' => RbacHelper::ROLE_DRD]);

        $this->assertMatchesRegularExpression('/^  (\S{48})$/m', $controller->out);
        preg_match('/^  (\S{48})$/m', $controller->out, $m);

        $identity = User::findIdentityByAccessToken($m[1]);
        $this->assertSame('crm-sync', $identity?->username);
        $this->assertContains(RbacHelper::ROLE_DRD, RbacHelper::roleNamesForUser((int) $identity->id));

        $token = UserApiToken::findOne(['user_id' => $identity->id]);
        $this->assertEqualsWithDelta(time() + ApiTokenController::DEFAULT_DAYS * 86400, (int) $token->expires_at, 5);
    }

    public function testRejectsAdminRoleExistingUserAndBadDays(): void
    {
        $this->assertSame(ExitCode::USAGE, $this->runCommand('create-integration', ['x-admin', 'role' => RbacHelper::ROLE_ADMIN]));
        $this->assertSame(ExitCode::USAGE, $this->runCommand('create-integration', ['x-days', 'days' => '-1']));
        $this->assertSame(ExitCode::USAGE, $this->runCommand('create-integration', ['x-days', 'days' => '1.5']));
        $this->assertNull(User::findOne(['username' => 'x-admin']));

        $this->runCommand('create-integration', ['dup-user']);
        $this->assertSame(ExitCode::DATAERR, $this->runCommand('create-integration', ['dup-user']));
    }

    public function testIssueListAndRevoke(): void
    {
        $this->runCommand('create-integration', ['multi-token']);
        $user = User::findOne(['username' => 'multi-token']);

        $this->assertSame(ExitCode::OK, $this->runCommand('issue', ['multi-token', 'name' => 'second', 'days' => '7']));
        $this->assertSame(2, (int) UserApiToken::find()->where(['user_id' => $user->id])->count());
        $this->assertSame(ExitCode::OK, $this->runCommand('list', ['multi-token']));

        $first = UserApiToken::find()->where(['user_id' => $user->id])->orderBy(['id' => SORT_ASC])->one();
        $this->assertSame(ExitCode::OK, $this->runCommand('revoke', [(string) $first->id]));
        $this->assertSame(1, (int) UserApiToken::find()->where(['user_id' => $user->id])->count());

        $this->assertSame(ExitCode::OK, $this->runCommand('revoke-all', ['multi-token']));
        $this->assertSame(0, (int) UserApiToken::find()->where(['user_id' => $user->id])->count());

        $this->assertSame(ExitCode::DATAERR, $this->runCommand('issue', ['no-such-user']));
    }
}
