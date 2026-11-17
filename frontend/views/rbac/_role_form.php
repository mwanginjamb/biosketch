<?php

/** @var yii\web\View $this */
/** @var frontend\models\RoleForm $model */
/** @var yii\rbac\Permission[] $permissions */

use common\components\RbacHelper;
use common\library\FormUi;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$isProtected = $model->originalName !== null && RbacHelper::isProtected($model->originalName);
$permissionItems = [];
foreach ($permissions as $permission) {
    $permissionItems[$permission->name] = $permission->name;
}
ksort($permissionItems);
?>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md md:p-lg max-w-3xl">
    <?php $form = ActiveForm::begin(FormUi::formConfig('role-form')); ?>

    <?= $form->errorSummary($model, ['class' => 'text-sm text-red-600']) ?>

    <?= $form->field($model, 'role_name')->textInput([
        'class' => FormUi::inputClass(),
        'readonly' => $isProtected,
        'placeholder' => 'e.g. reviewer',
    ])->hint($isProtected ? 'Built-in role: the name cannot be changed.' : null) ?>

    <?= $form->field($model, 'description')->textInput(['class' => FormUi::inputClass(), 'placeholder' => 'What this role is for']) ?>

    <?= $form->field($model, 'permissions')->checkboxList($permissionItems, [
        'class' => 'grid grid-cols-1 sm:grid-cols-2 gap-xs',
        'item' => function ($index, $label, $name, $checked, $value) use ($permissions) {
            $description = $permissions[$value]->description ?? '';
            return Html::tag('label',
                Html::checkbox($name, $checked, ['value' => $value, 'class' => 'mt-1 w-4 h-4 text-primary'])
                . Html::tag('span',
                    Html::tag('span', Html::encode($label), ['class' => 'font-data-mono text-sm block'])
                    . Html::tag('span', Html::encode($description), ['class' => 'text-xs text-on-surface-variant'])
                ),
                ['class' => 'flex items-start gap-xs p-xs border border-outline-variant rounded cursor-pointer hover:bg-surface-container']
            );
        },
    ])->hint('Direct permissions of this role. Child roles are managed under "Manage hierarchy".') ?>

    <div class="flex flex-col sm:flex-row gap-sm pt-sm">
        <?= Html::submitButton('Save Role', ['class' => FormUi::buttonClass()]) ?>
        <?= FormUi::secondaryButton('Cancel', 'close', ['index']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
