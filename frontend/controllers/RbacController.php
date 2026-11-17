<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\components\RbacHelper;
use common\models\User;
use frontend\models\PermissionForm;
use frontend\models\RoleForm;
use frontend\models\UserRoleForm;
use Throwable;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rbac\Item;
use yii\rbac\Role;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * RBAC administration: roles, permissions, hierarchy and user role assignments.
 * Requires the manageRbac permission (held by the admin role).
 */
class RbacController extends Controller
{
    public $layout = 'list';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [RbacHelper::PERM_MANAGE_RBAC],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-role' => ['POST'],
                    'delete-permission' => ['POST'],
                ],
            ],
        ];
    }

    /* ─── Roles ─────────────────────────────────────────────────────────── */

    public function actionIndex(): string
    {
        $auth = RbacHelper::auth();
        $rows = [];
        foreach ($auth->getRoles() as $role) {
            $rows[] = [
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => array_keys($auth->getPermissionsByRole($role->name)),
                'childRoles' => array_keys(array_filter($auth->getChildren($role->name), fn($c) => $c instanceof Role)),
                'users' => count($auth->getUserIdsByRole($role->name)),
                'protected' => RbacHelper::isProtected($role->name),
            ];
        }

        return $this->render('index', [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $rows,
                'key' => 'name',
                'sort' => ['attributes' => ['name'], 'defaultOrder' => ['name' => SORT_ASC]],
                'pagination' => ['pageSize' => 20],
            ]),
        ]);
    }

    public function actionCreateRole(): string|Response
    {
        $auth = RbacHelper::auth();
        $model = new RoleForm(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $saved = $this->inTransaction(function () use ($auth, $model) {
                $role = $auth->createRole($model->role_name);
                $role->description = $model->description;
                $auth->add($role);
                $this->syncPermissions($role, (array) $model->permissions);
            }, 'Error creating role');

            if ($saved) {
                Yii::$app->session->setFlash('success', "Role \"{$model->role_name}\" created.");
                return $this->redirect(['index']);
            }
        }

        return $this->render('create-role', [
            'model' => $model,
            'permissions' => $auth->getPermissions(),
        ]);
    }

    public function actionUpdateRole(string $name): string|Response
    {
        $auth = RbacHelper::auth();
        $role = $auth->getRole($name) ?? throw new NotFoundHttpException("Role \"{$name}\" not found.");

        $model = new RoleForm(['scenario' => 'update']);
        $model->role_name = $role->name;
        $model->originalName = $role->name;
        $model->description = $role->description;
        $model->permissions = $this->directPermissionNames($role->name);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $saved = $this->inTransaction(function () use ($auth, $model, $role) {
                $oldName = $role->name;
                $role->name = $model->role_name;
                $role->description = $model->description;
                // DbManager::update renames the item and carries its children and assignments over.
                $auth->update($oldName, $role);
                $this->syncPermissions($role, (array) $model->permissions);
            }, 'Error updating role');

            if ($saved) {
                Yii::$app->session->setFlash('success', 'Role updated.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update-role', [
            'model' => $model,
            'permissions' => $auth->getPermissions(),
            'isUpdate' => true,
        ]);
    }

    public function actionDeleteRole(string $name): Response
    {
        $auth = RbacHelper::auth();
        $role = $auth->getRole($name);

        if ($role === null) {
            Yii::$app->session->setFlash('error', "Role \"{$name}\" not found.");
        } elseif (RbacHelper::isProtected($name)) {
            Yii::$app->session->setFlash('error', "The built-in role \"{$name}\" cannot be deleted.");
        } elseif ($this->inTransaction(fn() => $auth->remove($role), "Error deleting role \"{$name}\"")) {
            // remove() also deletes the role's assignments and hierarchy links.
            Yii::$app->session->setFlash('success', "Role \"{$name}\" deleted, with its permission links and user assignments.");
        }

        return $this->redirect(['index']);
    }

    /* ─── User role assignment ──────────────────────────────────────────── */

    public function actionUserRoles(): string
    {
        $users = User::find()->orderBy(['username' => SORT_ASC])->all();
        $rolesByUser = [];
        foreach ($users as $user) {
            $rolesByUser[$user->id] = RbacHelper::roleNamesForUser((int) $user->id);
        }

        return $this->render('assignment/user-roles', [
            'users' => $users,
            'rolesByUser' => $rolesByUser,
            'allRoles' => RbacHelper::auth()->getRoles(),
        ]);
    }

    public function actionAssignRole(int $userId): string|Response
    {
        $auth = RbacHelper::auth();
        $user = User::findOne($userId) ?? throw new NotFoundHttpException('User not found.');

        $model = new UserRoleForm();
        $model->userId = $userId;
        $model->roles = RbacHelper::roleNamesForUser($userId);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $saved = $this->inTransaction(function () use ($auth, $model, $userId) {
                $auth->revokeAll($userId);
                foreach ((array) $model->roles as $roleName) {
                    RbacHelper::assign($roleName, $userId);
                }
            }, 'Error updating roles');

            if ($saved) {
                Yii::$app->session->setFlash('success', "Roles updated for {$user->username}.");
                return $this->redirect(['user-roles']);
            }
        }

        return $this->render('assignment/assign-role', [
            'model' => $model,
            'user' => $user,
            'allRoles' => $auth->getRoles(),
        ]);
    }

    /* ─── Permissions ───────────────────────────────────────────────────── */

    public function actionPermissions(): string
    {
        $rows = [];
        foreach (RbacHelper::auth()->getPermissions() as $permission) {
            $rows[] = [
                'name' => $permission->name,
                'description' => $permission->description,
                'rule' => $permission->ruleName,
                'parents' => array_keys(RbacHelper::parentsOf($permission->name)),
                'protected' => RbacHelper::isProtected($permission->name),
            ];
        }

        return $this->render('permissions/index', [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $rows,
                'key' => 'name',
                'sort' => ['attributes' => ['name'], 'defaultOrder' => ['name' => SORT_ASC]],
                'pagination' => ['pageSize' => 20],
            ]),
        ]);
    }

    public function actionCreatePermission(): string|Response
    {
        $auth = RbacHelper::auth();
        $model = new PermissionForm(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $saved = $this->inTransaction(function () use ($auth, $model) {
                $permission = $auth->createPermission($model->name);
                $permission->description = $model->description;
                $auth->add($permission);
                if ($model->parent !== null) {
                    $auth->addChild(RbacHelper::item($model->parent), $permission);
                }
            }, 'Error creating permission');

            if ($saved) {
                Yii::$app->session->setFlash('success', "Permission \"{$model->name}\" created.");
                return $this->redirect(['permissions']);
            }
        }

        return $this->render('permissions/create', [
            'model' => $model,
            'parentOptions' => $this->parentOptions(),
        ]);
    }

    public function actionUpdatePermission(string $name): string|Response
    {
        $auth = RbacHelper::auth();
        $permission = $auth->getPermission($name)
            ?? throw new NotFoundHttpException("Permission \"{$name}\" not found.");

        $currentParents = RbacHelper::parentsOf($name);

        $model = new PermissionForm(['scenario' => 'update']);
        $model->name = $permission->name;
        $model->originalName = $permission->name;
        $model->description = $permission->description;
        $model->parent = array_key_first($currentParents);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $saved = $this->inTransaction(function () use ($auth, $model, $permission, $currentParents) {
                $oldName = $permission->name;
                $permission->name = $model->name;
                $permission->description = $model->description;
                $auth->update($oldName, $permission);

                // The form edits a single parent link; other parents are left untouched.
                $previousParent = array_key_first($currentParents);
                if ($previousParent !== $model->parent) {
                    if ($previousParent !== null) {
                        $auth->removeChild($currentParents[$previousParent], $permission);
                    }
                    if ($model->parent !== null) {
                        $auth->addChild(RbacHelper::item($model->parent), $permission);
                    }
                }
            }, 'Error updating permission');

            if ($saved) {
                Yii::$app->session->setFlash('success', 'Permission updated.');
                return $this->redirect(['permissions']);
            }
        }

        return $this->render('permissions/update', [
            'model' => $model,
            'parentOptions' => $this->parentOptions($name),
            'otherParents' => array_slice(array_keys($currentParents), 1),
        ]);
    }

    public function actionDeletePermission(string $name): Response
    {
        $auth = RbacHelper::auth();
        $permission = $auth->getPermission($name);

        if ($permission === null) {
            Yii::$app->session->setFlash('error', 'Permission not found.');
        } elseif (RbacHelper::isProtected($name)) {
            Yii::$app->session->setFlash('error', "The built-in permission \"{$name}\" cannot be deleted.");
        } elseif ($this->inTransaction(fn() => $auth->remove($permission), 'Error deleting permission')) {
            Yii::$app->session->setFlash('success', "Permission \"{$name}\" deleted.");
        }

        return $this->redirect(['permissions']);
    }

    /* ─── Hierarchy ─────────────────────────────────────────────────────── */

    /**
     * Choose the direct children (roles and/or permissions) of a role or permission.
     */
    public function actionManageHierarchy(string $name): string|Response
    {
        $auth = RbacHelper::auth();
        $item = RbacHelper::item($name) ?? throw new NotFoundHttpException("Item \"{$name}\" not found.");
        $isRole = $item instanceof Role;

        // A permission may only contain permissions; a role may contain roles and permissions.
        $candidates = $auth->getPermissions() + ($isRole ? $auth->getRoles() : []);
        unset($candidates[$name]);

        if (Yii::$app->request->isPost) {
            $selected = array_values(array_intersect(
                (array) Yii::$app->request->post('children', []),
                array_keys($candidates),
            ));

            $saved = $this->inTransaction(function () use ($auth, $item, $selected, $candidates) {
                $auth->removeChildren($item);
                foreach ($selected as $childName) {
                    // addChild() throws on loops (e.g. making a parent its own child).
                    $auth->addChild($item, $candidates[$childName]);
                }
            }, 'Error updating hierarchy');

            if ($saved) {
                Yii::$app->session->setFlash('success', "Hierarchy for \"{$name}\" updated.");
                return $this->redirect($isRole ? ['index'] : ['permissions']);
            }
        }

        return $this->render('manage-hierarchy', [
            'item' => $item,
            'isRole' => $isRole,
            'candidates' => $candidates,
            'currentChildren' => $auth->getChildren($name),
        ]);
    }

    /* ─── Helpers ───────────────────────────────────────────────────────── */

    /**
     * Runs $work in a DB transaction and verifies the admin role still holds
     * manageRbac afterwards (so nobody can lock RBAC administration out).
     * Flashes $errorPrefix + message and returns false on failure.
     */
    private function inTransaction(callable $work, string $errorPrefix): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $work();

            $adminPerms = RbacHelper::auth()->getPermissionsByRole(RbacHelper::ROLE_ADMIN);
            if (!isset($adminPerms[RbacHelper::PERM_MANAGE_RBAC])) {
                throw new \RuntimeException('the admin role must keep the manageRbac permission.');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $errorPrefix . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Makes $role's direct *permission* children exactly $permissionNames.
     * Child roles (e.g. admin → drd) are left as they are; use Manage hierarchy for those.
     */
    private function syncPermissions(Item $role, array $permissionNames): void
    {
        $auth = RbacHelper::auth();
        $current = $this->directPermissionNames($role->name);

        foreach (array_diff($current, $permissionNames) as $name) {
            $auth->removeChild($role, $auth->getPermission($name));
        }
        foreach (array_diff($permissionNames, $current) as $name) {
            if ($permission = $auth->getPermission($name)) {
                $auth->addChild($role, $permission);
            }
        }
    }

    /** @return string[] names of permissions that are direct children of $name */
    private function directPermissionNames(string $name): array
    {
        $children = RbacHelper::auth()->getChildren($name);

        return array_keys(array_filter($children, fn($child) => $child->type === Item::TYPE_PERMISSION));
    }

    /** @return array<string, string> parent choices for a permission: name => "name (role|permission)" */
    private function parentOptions(?string $exclude = null): array
    {
        $auth = RbacHelper::auth();
        $options = [];
        foreach ($auth->getRoles() as $role) {
            $options[$role->name] = $role->name . ' (role)';
        }
        foreach ($auth->getPermissions() as $permission) {
            $options[$permission->name] = $permission->name . ' (permission)';
        }
        unset($options[$exclude]);
        ksort($options);

        return $options;
    }
}
