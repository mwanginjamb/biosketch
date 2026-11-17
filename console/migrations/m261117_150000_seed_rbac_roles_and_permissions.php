<?php

declare(strict_types=1);

use common\components\RbacHelper as R;
use common\rbac\BiosketchOwnerRule;
use yii\db\Migration;
use yii\db\Query;

/**
 * Seeds the built-in roles and permissions and gives every existing account
 * without a role the default `researcher` role.
 *
 *   researcher : createBiosketch, viewOwnBiosketch, updateOwnBiosketch, deleteOwnBiosketch
 *   drd        : viewBiosketchList, viewBiosketch
 *   admin      : researcher + drd + updateBiosketch + deleteBiosketch + manageRbac
 *
 * The *Own* permissions carry the isBiosketchOwner rule and grant their
 * unrestricted counterpart only for the user's own biosketch.
 *
 * The `admin` role itself is created by m261117_130000_create_admin_role.
 */
class m261117_150000_seed_rbac_roles_and_permissions extends Migration
{
    public function safeUp(): void
    {
        $auth = Yii::$app->authManager;

        $rule = new BiosketchOwnerRule();
        $auth->add($rule);

        $perm = function (string $name, string $description, ?string $ruleName = null) use ($auth) {
            $p = $auth->createPermission($name);
            $p->description = $description;
            $p->ruleName = $ruleName;
            $auth->add($p);
            return $p;
        };

        $viewList = $perm(R::PERM_VIEW_LIST, 'View the list of all biosketches');
        $view = $perm(R::PERM_VIEW, 'View any biosketch (profile and PDF report)');
        $viewOwn = $perm(R::PERM_VIEW_OWN, 'View own biosketch', $rule->name);
        $create = $perm(R::PERM_CREATE, 'Create a biosketch');
        $update = $perm(R::PERM_UPDATE, 'Update any biosketch');
        $updateOwn = $perm(R::PERM_UPDATE_OWN, 'Update own biosketch', $rule->name);
        $delete = $perm(R::PERM_DELETE, 'Delete any biosketch');
        $deleteOwn = $perm(R::PERM_DELETE_OWN, 'Delete own biosketch', $rule->name);
        $manageRbac = $perm(R::PERM_MANAGE_RBAC, 'Manage roles, permissions and user role assignments');

        $auth->addChild($viewOwn, $view);
        $auth->addChild($updateOwn, $update);
        $auth->addChild($deleteOwn, $delete);

        $researcher = $auth->createRole(R::ROLE_RESEARCHER);
        $researcher->description = 'Researcher: manages their own biosketch (default role)';
        $auth->add($researcher);
        foreach ([$create, $viewOwn, $updateOwn, $deleteOwn] as $p) {
            $auth->addChild($researcher, $p);
        }

        $drd = $auth->createRole(R::ROLE_DRD);
        $drd->description = 'DRD: views the biosketch list and every biosketch';
        $auth->add($drd);
        $auth->addChild($drd, $viewList);
        $auth->addChild($drd, $view);

        $admin = $auth->getRole(R::ROLE_ADMIN);
        if ($admin === null) {
            $admin = $auth->createRole(R::ROLE_ADMIN);
            $auth->add($admin);
        }
        $admin->description = 'Administrator: full access to all biosketches and RBAC administration';
        $auth->update(R::ROLE_ADMIN, $admin);
        foreach ([$researcher, $drd, $update, $delete, $manageRbac] as $child) {
            $auth->addChild($admin, $child);
        }

        // Default role for existing accounts that have no role yet.
        $assigned = (new Query())->select('user_id')->distinct()->from($auth->assignmentTable)->column();
        $userIds = (new Query())->select('id')->from('{{%user}}')
            ->where(['not in', 'id', $assigned ?: [0]])
            ->column();
        foreach ($userIds as $userId) {
            $auth->assign($researcher, (int) $userId);
        }
        echo '    > assigned default role "researcher" to ' . count($userIds) . " existing user(s)\n";
    }

    public function safeDown(): void
    {
        $auth = Yii::$app->authManager;

        foreach ([R::ROLE_RESEARCHER, R::ROLE_DRD] as $name) {
            if ($role = $auth->getRole($name)) {
                $auth->remove($role);   // also removes its assignments and hierarchy
            }
        }
        foreach ([
            R::PERM_VIEW_LIST, R::PERM_VIEW, R::PERM_VIEW_OWN, R::PERM_CREATE, R::PERM_UPDATE,
            R::PERM_UPDATE_OWN, R::PERM_DELETE, R::PERM_DELETE_OWN, R::PERM_MANAGE_RBAC,
        ] as $name) {
            if ($p = $auth->getPermission($name)) {
                $auth->remove($p);
            }
        }
        if ($rule = $auth->getRule('isBiosketchOwner')) {
            $auth->remove($rule);
        }
    }
}
