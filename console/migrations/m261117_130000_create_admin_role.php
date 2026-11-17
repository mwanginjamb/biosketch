<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Creates the `admin` RBAC role. Admins can view, edit, delete and report on
 * every researcher profile; other users only on their own.
 *
 * Requires the RBAC tables from @yii/rbac/migrations (included in the migrate
 * controller's migrationPath, and ordered before this migration).
 *
 * Assign it with: php yii rbac/assign-admin <username>
 */
class m261117_130000_create_admin_role extends Migration
{
    public function safeUp(): void
    {
        $auth = Yii::$app->authManager;
        if ($auth->getRole('admin') === null) {
            $role = $auth->createRole('admin');
            $role->description = 'Administrator: full access to all researcher profiles';
            $auth->add($role);
        }
    }

    public function safeDown(): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('admin');
        if ($role !== null) {
            $auth->remove($role);
        }
    }
}
