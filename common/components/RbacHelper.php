<?php

declare(strict_types=1);

namespace common\components;

use Yii;
use yii\db\Query;
use yii\rbac\DbManager;
use yii\rbac\Item;

/**
 * Role/permission names and shared RBAC helpers, used by the web RBAC
 * management screens, the console `rbac/*` commands and signup.
 *
 * Roles
 *   researcher  default role for every account; manages only their own biosketch
 *   drd         can view the biosketch list and any biosketch
 *   admin       everything above + edit/delete any biosketch + RBAC administration
 */
final class RbacHelper
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESEARCHER = 'researcher';
    public const ROLE_DRD = 'drd';

    public const PERM_VIEW_LIST = 'viewBiosketchList';
    public const PERM_VIEW = 'viewBiosketch';
    public const PERM_VIEW_OWN = 'viewOwnBiosketch';
    public const PERM_CREATE = 'createBiosketch';
    public const PERM_UPDATE = 'updateBiosketch';
    public const PERM_UPDATE_OWN = 'updateOwnBiosketch';
    public const PERM_DELETE = 'deleteBiosketch';
    public const PERM_DELETE_OWN = 'deleteOwnBiosketch';
    public const PERM_MANAGE_RBAC = 'manageRbac';

    /** Built-in items the application depends on: they cannot be renamed or deleted from the UI. */
    public const PROTECTED_ITEMS = [
        self::ROLE_ADMIN,
        self::ROLE_RESEARCHER,
        self::ROLE_DRD,
        self::PERM_VIEW_LIST,
        self::PERM_VIEW,
        self::PERM_VIEW_OWN,
        self::PERM_CREATE,
        self::PERM_UPDATE,
        self::PERM_UPDATE_OWN,
        self::PERM_DELETE,
        self::PERM_DELETE_OWN,
        self::PERM_MANAGE_RBAC,
    ];

    public static function auth(): DbManager
    {
        return Yii::$app->authManager;
    }

    public static function isProtected(string $name): bool
    {
        return in_array($name, self::PROTECTED_ITEMS, true);
    }

    /** Assigns the default `researcher` role unless the user already has it. */
    public static function assignDefaultRole(int $userId): void
    {
        self::assign(self::ROLE_RESEARCHER, $userId);
    }

    /** Assigns $roleName to $userId if not already assigned. Returns false when the role does not exist. */
    public static function assign(string $roleName, int $userId): bool
    {
        $auth = self::auth();
        $role = $auth->getRole($roleName);
        if ($role === null) {
            return false;
        }
        if ($auth->getAssignment($roleName, $userId) === null) {
            $auth->assign($role, $userId);
        }

        return true;
    }

    /** @return string[] role names assigned to the user */
    public static function roleNamesForUser(int $userId): array
    {
        return array_keys(self::auth()->getRolesByUser($userId));
    }

    /**
     * True when replacing $userId's roles with $newRoles would leave the system
     * without any admin.
     *
     * @param string[] $newRoles
     */
    public static function wouldRemoveLastAdmin(int $userId, array $newRoles): bool
    {
        if (in_array(self::ROLE_ADMIN, $newRoles, true)) {
            return false;
        }

        $adminIds = array_map('intval', self::auth()->getUserIdsByRole(self::ROLE_ADMIN));

        return $adminIds === [$userId];
    }

    /**
     * Direct parents (roles or permissions) of an item.
     *
     * @return Item[] keyed by name
     */
    public static function parentsOf(string $name): array
    {
        $auth = self::auth();
        $names = (new Query())
            ->select('parent')
            ->from($auth->itemChildTable)
            ->where(['child' => $name])
            ->column($auth->db);

        $parents = [];
        foreach ($names as $parentName) {
            $item = $auth->getRole($parentName) ?? $auth->getPermission($parentName);
            if ($item !== null) {
                $parents[$parentName] = $item;
            }
        }

        return $parents;
    }

    /** Role or permission by name. */
    public static function item(string $name): ?Item
    {
        $auth = self::auth();

        return $auth->getRole($name) ?? $auth->getPermission($name);
    }
}
