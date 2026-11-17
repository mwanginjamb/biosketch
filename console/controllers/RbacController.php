<?php

declare(strict_types=1);

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manages RBAC role assignments.
 *
 *   php yii rbac/assign-admin <username>
 *   php yii rbac/revoke-admin <username>
 *   php yii rbac/list-admins
 */
class RbacController extends Controller
{
    public function actionAssignAdmin(string $username): int
    {
        $user = $this->findUser($username);
        if ($user === null) {
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole('admin');
        if ($role === null) {
            $this->stderr("The admin role does not exist. Run `php yii migrate` first.\n", Console::FG_RED);
            return ExitCode::CONFIG;
        }

        if ($auth->getAssignment('admin', $user->id) !== null) {
            $this->stdout("{$user->username} is already an admin.\n");
            return ExitCode::OK;
        }

        $auth->assign($role, $user->id);
        $this->stdout("{$user->username} is now an admin.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    public function actionRevokeAdmin(string $username): int
    {
        $user = $this->findUser($username);
        if ($user === null) {
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole('admin');
        if ($role === null || !$auth->revoke($role, $user->id)) {
            $this->stdout("{$user->username} was not an admin.\n");
            return ExitCode::OK;
        }

        $this->stdout("{$user->username} is no longer an admin.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    public function actionListAdmins(): int
    {
        $ids = Yii::$app->authManager->getUserIdsByRole('admin');
        if (!$ids) {
            $this->stdout("No admins assigned.\n");
            return ExitCode::OK;
        }

        foreach (User::find()->where(['id' => $ids])->orderBy('username')->all() as $user) {
            $this->stdout("{$user->id}\t{$user->username}\t{$user->email}\n");
        }

        return ExitCode::OK;
    }

    private function findUser(string $username): ?User
    {
        $user = User::findOne(['username' => $username]);
        if ($user === null) {
            $this->stderr("User '{$username}' not found.\n", Console::FG_RED);
        }

        return $user;
    }
}
