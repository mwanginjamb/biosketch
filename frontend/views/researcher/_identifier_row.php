<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="identifier-container" class="space-y-sm">
    <div class="identifier-item" data-index="<?= $index + 1 ?>">
        <div class="flex items-center justify-between mb-2">

            <button type="button"
                class="remove-identifier flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                ✕
            </button>

        </div>

        <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group">
            <div class="grid grid-cols-1 gap-xs">
                <div class="flex gap-xs">
                    <?= $form->field($model, "[{$index}]identifier_type", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(), 'placeholder' => 'e.g., Orcid']) ?>
                    <?= $form->field($model, "[{$index}]identifier_value", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(), 'placeholder' => 'Identifier Value']) ?>
                    <?= $form->field($model, "[{$index}]verification_status", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherIdentifier::getVerificationOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...']) ?>
                </div>

            </div>
        </div>
    </div>

</div>