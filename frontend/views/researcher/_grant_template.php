<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="grant-template" class="hidden space-y-sm">

    <div class="grant-item">
        <div class="flex items-center justify-between mb-2">

            <button type="button"
                class="remove-grant flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                ✕
            </button>

        </div>

        <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group">
            <div class="grid grid-cols-1 gap-xs">
                <div class="flex gap-xs">
                    <?= $form->field($model, "[__index__]grant_number", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(), 'placeholder' => 'Grant Number', 'disabled' => 'disabled']) ?>
                    <?= $form->field($model, "[__index__]title", FormUi::fieldConfig())->textarea(['class' => FormUi::inputClassStandard(), 'placeholder' => 'Grant Title', 'disabled' => 'disabled']) ?>
                </div>

                <div class="flex gap-xs">
                    <?= $form->field($model, "[__index__]funding_agency_id", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getFundingAgencyOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...', 'disabled' => 'disabled']) ?>
                    <?= $form->field($model, "[__index__]amount", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(), 'placeholder' => 'Award Amount', 'disabled' => 'disabled']) ?>
                    <?= $form->field($model, "[__index__]currency", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getCurrencyOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...', 'disabled' => 'disabled']) ?>
                </div>

                <div class="flex gap-xs">
                    <?= $form->field($model, "[__index__]role_id", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getRoleOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...', 'disabled' => 'disabled']) ?>
                    <?= $form->field($model, "[__index__]status_id", FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherGrant::getStatusOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...', 'disabled' => 'disabled']) ?>
                </div>
            </div>
        </div>

    </div>


</div>
<!-- /template -->