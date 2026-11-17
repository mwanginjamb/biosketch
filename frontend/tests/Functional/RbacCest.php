<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\components\RbacHelper;
use common\models\User;
use frontend\tests\Support\FunctionalTester;
use Yii;
use yii\helpers\Url;

/**
 * RBAC administration screens (admin only) and their safety guards.
 */
final class RbacCest
{
    private int $adminId;
    private int $researcherId;
    private int $drdId;

    public function _before(FunctionalTester $I): void
    {
        $this->adminId = $this->haveUser($I, 'rbac_admin', [RbacHelper::ROLE_ADMIN]);
        $this->researcherId = $this->haveUser($I, 'rbac_researcher', [RbacHelper::ROLE_RESEARCHER]);
        $this->drdId = $this->haveUser($I, 'rbac_drd', [RbacHelper::ROLE_DRD]);
    }

    private function haveUser(FunctionalTester $I, string $username, array $roles): int
    {
        $id = $I->haveRecord(User::class, [
            'username' => $username,
            'email' => $username . '@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password_0'),
            'status' => User::STATUS_ACTIVE,
        ]);
        foreach ($roles as $role) {
            RbacHelper::assign($role, $id);
        }

        return $id;
    }

    private function postWithCsrf(FunctionalTester $I, array $route, array $params = []): void
    {
        [$csrfParam, $csrfToken] = $I->createAndSetCsrfCookie('test-csrf-token');
        $I->sendAjaxPostRequest(Url::to(['/' . ltrim($route[0], '/')] + $route), $params + [$csrfParam => $csrfToken]);
    }

    public function nonAdminsCannotOpenRbacScreens(FunctionalTester $I): void
    {
        foreach ([$this->researcherId, $this->drdId] as $userId) {
            $I->amLoggedInAs($userId);
            foreach (['rbac/index', 'rbac/permissions', 'rbac/user-roles', 'site/users'] as $route) {
                $I->amOnRoute($route);
                $I->seeResponseCodeIs(403);
            }
        }
    }

    public function sidebarShowsRbacMenuOnlyToAdmins(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $I->amOnRoute('site/index');
        $I->see('RBAC Mgt');
        $I->seeLink('User Role Assignment');
        $I->seeLink('System Users');

        $I->amLoggedInAs($this->drdId);
        $I->amOnRoute('site/index');
        $I->see('BioSketch List');
        $I->dontSee('RBAC Mgt');
    }

    public function adminSeesBuiltInRolesAndPermissions(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);

        $I->amOnRoute('rbac/index');
        $I->seeResponseCodeIs(200);
        foreach (['admin', 'researcher', 'drd'] as $role) {
            $I->see($role, 'td');
        }

        $I->amOnRoute('rbac/permissions');
        $I->seeResponseCodeIs(200);
        $I->see(RbacHelper::PERM_VIEW_LIST, 'td');
        $I->see('isBiosketchOwner', 'td');

        $I->amOnRoute('site/users');
        $I->seeResponseCodeIs(200);
        $I->see('rbac_drd', 'td');
    }

    public function adminCreatesAndDeletesACustomRole(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $I->amOnRoute('rbac/create-role');
        $I->submitForm('#role-form', [
            'RoleForm[role_name]' => 'reviewer',
            'RoleForm[description]' => 'Reviews biosketches',
            'RoleForm[permissions]' => [RbacHelper::PERM_VIEW_LIST, RbacHelper::PERM_VIEW],
        ]);
        $I->see('Role "reviewer" created.');

        $auth = Yii::$app->authManager;
        verify($auth->getRole('reviewer'))->notNull();
        verify(array_keys($auth->getChildren('reviewer')))->equalsCanonicalizing([RbacHelper::PERM_VIEW_LIST, RbacHelper::PERM_VIEW]);

        $this->postWithCsrf($I, ['rbac/delete-role', 'name' => 'reviewer']);
        verify($auth->getRole('reviewer'))->null();
    }

    public function builtInRolesCannotBeDeletedOrRenamed(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);

        $this->postWithCsrf($I, ['rbac/delete-role', 'name' => RbacHelper::ROLE_DRD]);
        verify(Yii::$app->authManager->getRole(RbacHelper::ROLE_DRD))->notNull();

        $I->amOnRoute('rbac/update-role', ['name' => RbacHelper::ROLE_DRD]);
        $I->submitForm('#role-form', ['RoleForm[role_name]' => 'drd2']);
        $I->see('cannot be renamed');
        verify(Yii::$app->authManager->getRole(RbacHelper::ROLE_DRD))->notNull();
    }

    public function adminRoleMustKeepManageRbac(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $I->amOnRoute('rbac/update-role', ['name' => RbacHelper::ROLE_ADMIN]);
        // Submit with no direct permissions ticked.
        $I->submitForm('#role-form', ['RoleForm[permissions]' => '']);
        $I->see('must keep the manageRbac permission');

        $perms = Yii::$app->authManager->getPermissionsByRole(RbacHelper::ROLE_ADMIN);
        verify($perms)->arrayHasKey(RbacHelper::PERM_MANAGE_RBAC);
    }

    public function adminAssignsRolesToAUser(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $I->amOnRoute('rbac/assign-role', ['userId' => $this->researcherId]);
        $I->submitForm('#assign-role-form', [
            'UserRoleForm[roles]' => [RbacHelper::ROLE_RESEARCHER, RbacHelper::ROLE_DRD],
        ]);
        $I->see('Roles updated for rbac_researcher.');

        verify(RbacHelper::roleNamesForUser($this->researcherId))
            ->equalsCanonicalizing([RbacHelper::ROLE_RESEARCHER, RbacHelper::ROLE_DRD]);
    }

    public function theOnlyAdminCannotBeDemoted(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $I->amOnRoute('rbac/assign-role', ['userId' => $this->adminId]);
        $I->submitForm('#assign-role-form', ['UserRoleForm[roles]' => [RbacHelper::ROLE_RESEARCHER]]);
        $I->see('This user is the only admin');

        verify(RbacHelper::roleNamesForUser($this->adminId))->arrayContains(RbacHelper::ROLE_ADMIN);
    }

    public function adminManagesCustomPermissions(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $auth = Yii::$app->authManager;

        $I->amOnRoute('rbac/create-permission');
        $I->submitForm('#permission-form', [
            'PermissionForm[name]' => 'exportBiosketch',
            'PermissionForm[description]' => 'Export biosketches',
            'PermissionForm[parent]' => RbacHelper::ROLE_DRD,
        ]);
        $I->see('Permission "exportBiosketch" created.');
        verify($auth->hasChild($auth->getRole(RbacHelper::ROLE_DRD), $auth->getPermission('exportBiosketch')))->true();

        $I->amOnRoute('rbac/update-permission', ['name' => 'exportBiosketch']);
        $I->submitForm('#permission-form', [
            'PermissionForm[name]' => 'exportBiosketches',
            'PermissionForm[parent]' => RbacHelper::ROLE_ADMIN,
        ]);
        $I->see('Permission updated.');
        verify($auth->getPermission('exportBiosketch'))->null();
        verify($auth->hasChild($auth->getRole(RbacHelper::ROLE_ADMIN), $auth->getPermission('exportBiosketches')))->true();
        verify($auth->hasChild($auth->getRole(RbacHelper::ROLE_DRD), $auth->getPermission('exportBiosketches')))->false();

        $this->postWithCsrf($I, ['rbac/delete-permission', 'name' => 'exportBiosketches']);
        verify($auth->getPermission('exportBiosketches'))->null();

        // Built-in permissions are protected.
        $this->postWithCsrf($I, ['rbac/delete-permission', 'name' => RbacHelper::PERM_MANAGE_RBAC]);
        verify($auth->getPermission(RbacHelper::PERM_MANAGE_RBAC))->notNull();
    }

    public function hierarchyCanAddChildRoles(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $auth = Yii::$app->authManager;
        $current = array_keys($auth->getChildren(RbacHelper::ROLE_DRD));

        $I->amOnRoute('rbac/manage-hierarchy', ['name' => RbacHelper::ROLE_DRD]);
        $I->submitForm('form', ['children' => array_merge($current, [RbacHelper::ROLE_RESEARCHER])]);
        $I->see('Hierarchy for "drd" updated.');

        verify($auth->hasChild($auth->getRole(RbacHelper::ROLE_DRD), $auth->getRole(RbacHelper::ROLE_RESEARCHER)))->true();
    }
}
