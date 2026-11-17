<?php

declare(strict_types=1);

namespace frontend\models;

use common\components\RbacHelper;
use yii\base\Model;

/**
 * Create/update an RBAC permission and its (optional) parent item.
 */
class PermissionForm extends Model
{
    public string $name = '';
    public ?string $description = null;
    /** Optional parent role or permission that should include this permission. */
    public ?string $parent = null;
    /** Name before editing (update scenario). */
    public ?string $originalName = null;

    public function scenarios(): array
    {
        return [
            'create' => ['name', 'description', 'parent'],
            'update' => ['name', 'description', 'parent'],
        ];
    }

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_.-]{1,63}$/',
                'message' => 'Use 2–64 letters, digits, ".", "-" or "_", starting with a letter.'],
            ['name', 'validateName'],
            ['description', 'string', 'max' => 255],
            ['parent', 'default', 'value' => null],
            ['parent', 'validateParent'],
        ];
    }

    public function validateName(string $attribute): void
    {
        $renamed = $this->scenario === 'create' || $this->name !== $this->originalName;
        if (!$renamed) {
            return;
        }

        if ($this->originalName !== null && RbacHelper::isProtected($this->originalName)) {
            $this->addError($attribute, "The built-in permission \"{$this->originalName}\" cannot be renamed.");
            return;
        }
        if (RbacHelper::item($this->name) !== null) {
            $this->addError($attribute, "A role or permission named \"{$this->name}\" already exists.");
        }
    }

    public function validateParent(string $attribute): void
    {
        if ($this->parent === null || $this->parent === '') {
            $this->parent = null;
            return;
        }
        if ($this->parent === $this->name || $this->parent === $this->originalName) {
            $this->addError($attribute, 'A permission cannot be its own parent.');
            return;
        }
        if (RbacHelper::item($this->parent) === null) {
            $this->addError($attribute, 'Unknown parent role or permission.');
        }
    }
}
