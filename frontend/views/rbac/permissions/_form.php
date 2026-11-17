<?php

/** @var yii\web\View $this */
/** @var frontend\models\PermissionForm $model */
/** @var array<string, string> $parentOptions */
/** @var string[] $otherParents */

use common\components\RbacHelper;
use common\library\FormUi;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$isProtected = $model->originalName !== null && RbacHelper::isProtected($model->originalName);
$otherParents = $otherParents ?? [];
?>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md md:p-lg max-w-3xl">
    <?php $form = ActiveForm::begin(FormUi::formConfig('permission-form')); ?>

    <?= $form->errorSummary($model, \common\library\FormUi::errorSummaryOptions()) ?>

    <?= $form->field($model, 'name')->textInput([
        'class' => FormUi::inputClassMono(),
        'readonly' => $isProtected,
        'placeholder' => 'e.g. exportBiosketch',
    ])->hint($isProtected ? 'Built-in permission: the name cannot be changed.' : null) ?>

    <?= $form->field($model, 'description')->textInput(['class' => FormUi::inputClass()]) ?>

    <?= $form->field($model, 'parent')->dropDownList($parentOptions, [
        'class' => FormUi::selectClass(),
        'prompt' => '— none —',
    ])->label('Granted by (parent role or permission)')
        ->hint($otherParents
            ? 'Also granted by: ' . Html::encode(implode(', ', $otherParents)) . ' (manage those under the parent\'s hierarchy).'
            : 'The selected role or permission will include this permission.') ?>

    <div class="flex flex-col sm:flex-row gap-sm pt-sm">
        <?= Html::submitButton('Save Permission', ['class' => FormUi::buttonClass()]) ?>
        <?= FormUi::secondaryButton('Cancel', 'close', ['permissions']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
