<?php

declare(strict_types=1);

namespace console\controllers;

use common\components\RbacHelper;
use common\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manages RBAC role assignments from the command line (e.g. to create the first admin,
 * after which the web RBAC screens can be used).
 *
 *   php yii rbac/assign <role> <username>      e.g. rbac/assign drd jlangat
 *   php yii rbac/revoke <role> <username>
 *   php yii rbac/roles <username>              roles held by a user
 *   php yii rbac/list <role>                   users holding a role
 *
 *   php yii rbac/assign-admin <username>       shortcuts for the admin role
 *   php yii rbac/revoke-admin <username>
 *   php yii rbac/list-admins
 */
class RbacController extends Controller
{
    public function actionAssign(string $role, string $username): int
    {
        $user = $this->findUser($username);
        if ($user === null) {
            return ExitCode::DATAERR;
        }
        if (Yii::$app->authManager->getRole($role) === null) {
            $this->stderr("Role '{$role}' does not exist. Run `php yii migrate` first.\n", Console::FG_RED);
            return ExitCode::CONFIG;
        }

        RbacHelper::assign($role, (int) $user->id);
        $this->stdout("{$user->username} has role '{$role}'.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    public function actionRevoke(string $role, string $username): int
    {
        $user = $this->findUser($username);
        if ($user === null) {
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $roleItem = $auth->getRole($role);
        if ($roleItem === null || $auth->getAssignment($role, $user->id) === null) {
            $this->stdout("{$user->username} does not have role '{$role}'.\n");
            return ExitCode::OK;
        }

        $remaining = array_values(array_diff(RbacHelper::roleNamesForUser((int) $user->id), [$role]));
        if (RbacHelper::wouldRemoveLastAdmin((int) $user->id, $remaining)) {
            $this->stderr("{$user->username} is the only admin. Assign admin to someone else first.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $auth->revoke($roleItem, $user->id);
        $this->stdout("Revoked '{$role}' from {$user->username}.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    public function actionRoles(string $username): int
    {
        $user = $this->findUser($username);
        if ($user === null) {
            return ExitCode::DATAERR;
        }

        $roles = RbacHelper::roleNamesForUser((int) $user->id);
        $this->stdout($user->username . ': ' . ($roles ? implode(', ', $roles) : '(no roles)') . "\n");

        return ExitCode::OK;
    }

    public function actionList(string $role): int
    {
        $ids = Yii::$app->authManager->getUserIdsByRole($role);
        if (!$ids) {
            $this->stdout("No users have role '{$role}'.\n");
            return ExitCode::OK;
        }

        foreach (User::find()->where(['id' => $ids])->orderBy('username')->all() as $user) {
            $this->stdout("{$user->id}\t{$user->username}\t{$user->email}\n");
        }

        return ExitCode::OK;
    }

    public function actionAssignAdmin(string $username): int
    {
        return $this->actionAssign(RbacHelper::ROLE_ADMIN, $username);
    }

    public function actionRevokeAdmin(string $username): int
    {
        return $this->actionRevoke(RbacHelper::ROLE_ADMIN, $username);
    }

    public function actionListAdmins(): int
    {
        return $this->actionList(RbacHelper::ROLE_ADMIN);
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
