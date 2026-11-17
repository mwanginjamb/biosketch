<?php

/** @var yii\web\View $this */
/** @var frontend\models\UserRoleForm $model */
/** @var common\models\User $user */
/** @var yii\rbac\Role[] $allRoles */

use common\library\FormUi;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Assign Roles: ' . $user->username;

ksort($allRoles);
$roleItems = [];
foreach ($allRoles as $role) {
    $roleItems[$role->name] = $role->name;
}
?>
<div class="px-gutter py-lg max-w-[1280px] w-full mx-auto">
    <?= $this->render('../_page_header', [
        'title' => 'Assign Roles',
        'subtitle' => $user->username . ' · ' . $user->email . ($user->staff_id ? ' · Staff ID ' . $user->staff_id : ''),
    ]) ?>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md md:p-lg max-w-3xl">
        <?php $form = ActiveForm::begin(FormUi::formConfig('assign-role-form')); ?>

        <?= $form->errorSummary($model, ['class' => 'text-sm text-red-600']) ?>

        <?= $form->field($model, 'roles')->checkboxList($roleItems, [
            'class' => 'grid grid-cols-1 sm:grid-cols-2 gap-xs',
            'item' => function ($index, $label, $name, $checked, $value) use ($allRoles) {
                return Html::tag('label',
                    Html::checkbox($name, $checked, ['value' => $value, 'class' => 'mt-1 w-4 h-4 text-primary'])
                    . Html::tag('span',
                        Html::tag('span', Html::encode($label), ['class' => 'font-bold block'])
                        . Html::tag('span', Html::encode($allRoles[$value]->description ?? ''), ['class' => 'text-xs text-on-surface-variant'])
                    ),
                    ['class' => 'flex items-start gap-xs p-xs border border-outline-variant rounded cursor-pointer hover:bg-surface-container']
                );
            },
        ])->label('Roles') ?>

        <div class="flex flex-col sm:flex-row gap-sm pt-sm">
            <?= Html::submitButton('Save Roles', ['class' => FormUi::buttonClass()]) ?>
            <?= FormUi::secondaryButton('Back', 'arrow_back', ['user-roles']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
