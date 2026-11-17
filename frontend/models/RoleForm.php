<?php

declare(strict_types=1);

namespace frontend\models;

use common\components\RbacHelper;
use yii\base\Model;

/**
 * Create/update an RBAC role and its permissions.
 */
class RoleForm extends Model
{
    public string $role_name = '';
    public ?string $description = null;
    /** @var string[] permission names */
    public $permissions = [];
    /** Name before editing (update scenario). */
    public ?string $originalName = null;

    public function scenarios(): array
    {
        return [
            'create' => ['role_name', 'description', 'permissions'],
            'update' => ['role_name', 'description', 'permissions'],
        ];
    }

    public function rules(): array
    {
        return [
            ['role_name', 'trim'],
            ['role_name', 'required'],
            ['role_name', 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_-]{1,63}$/',
                'message' => 'Use 2–64 letters, digits, "-" or "_", starting with a letter.'],
            ['role_name', 'validateName'],
            ['description', 'string', 'max' => 255],
            ['permissions', 'default', 'value' => []],
            // Keep only a flat list of strings (nested arrays in the POST would break re-rendering).
            ['permissions', 'filter', 'filter' => fn($v) => is_array($v) ? array_values(array_filter($v, 'is_string')) : []],
            ['permissions', 'each', 'rule' => ['in', 'range' => array_keys(RbacHelper::auth()->getPermissions())]],
        ];
    }

    public function validateName(string $attribute): void
    {
        $renamed = $this->scenario === 'create' || $this->role_name !== $this->originalName;
        if (!$renamed) {
            return;
        }

        if ($this->originalName !== null && RbacHelper::isProtected($this->originalName)) {
            $this->addError($attribute, "The built-in role \"{$this->originalName}\" cannot be renamed.");
            return;
        }
        // Roles and permissions share one namespace.
        if (RbacHelper::item($this->role_name) !== null) {
            $this->addError($attribute, "A role or permission named \"{$this->role_name}\" already exists.");
        }
    }

    public function attributeLabels(): array
    {
        return ['role_name' => 'Role name'];
    }
}
