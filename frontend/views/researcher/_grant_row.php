<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="grants-container" class="space-y-sm">
    <div class="grant-item" data-index="<?= $index ?>">
        <div class="flex items-center justify-between mb-2">

            <button type="button"
                class="remove-grant flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                ✕
            </button>

        </div>

        <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group">
            <div class="grid grid-cols-1 gap-xs">
                <div class="flex gap-xs">
                    <?= $form->field($model, "[{$index}]grant_number", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(), 'placeholder' => 'Grant Number']) ?>
                    <?= $form->field($model, "[{$index}]title", FormUi::fieldConfig())->textarea(['class' => FormUi::inputClassStandard(), 'placeholder' => 'Grant Title']) ?>
                </div>

                <div class="flex gap-xs">
                    <?= $form->field($model, "[{$index}]funding_agency_id", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getFundingAgencyOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...']) ?>
                    <?= $form->field($model, "[{$index}]amount", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(), 'placeholder' => 'Award Amount']) ?>
                    <?= $form->field($model, "[{$index}]currency", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getCurrencyOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...']) ?>
                </div>

                <div class="flex gap-xs">
                    <?= $form->field($model, "[{$index}]role_id", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getRoleOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...']) ?>
                    <?= $form->field($model, "[{$index}]status_id", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getStatusOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...']) ?>
                </div>

            </div>
        </div>
    </div>

</div>