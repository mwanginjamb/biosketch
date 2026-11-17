<?php

declare(strict_types=1);

namespace frontend\models;

use common\components\RbacHelper;
use yii\base\Model;

/**
 * Replace the set of roles assigned to one user.
 */
class UserRoleForm extends Model
{
    public int $userId = 0;
    /** @var string[] role names */
    public $roles = [];

    public function rules(): array
    {
        return [
            ['roles', 'default', 'value' => []],
            // Keep only a flat list of strings (nested arrays in the POST would break re-rendering).
            ['roles', 'filter', 'filter' => fn($v) => is_array($v) ? array_values(array_filter($v, 'is_string')) : []],
            ['roles', 'each', 'rule' => ['in', 'range' => array_keys(RbacHelper::auth()->getRoles())]],
            ['roles', 'validateKeepsAnAdmin'],
        ];
    }

    /** The system must always keep at least one admin. */
    public function validateKeepsAnAdmin(string $attribute): void
    {
        if (RbacHelper::wouldRemoveLastAdmin($this->userId, (array) $this->roles)) {
            $this->addError($attribute, 'This user is the only admin. Assign the admin role to someone else first.');
        }
    }
}
