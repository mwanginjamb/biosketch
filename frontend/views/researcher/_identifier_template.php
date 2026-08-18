<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="identifier-template" class="hidden space-y-sm">
    <div class="identifier-item">
        <div class="flex items-center justify-between mb-2">

            <button type="button"
                class="remove-identifier flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                ✕
            </button>

        </div>

        <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group space-y-xs">
            <!-- <div class="flex items-center justify-between mb-2">
                    <span class="font-label-caps text-[10px] text-secondary font-bold">SELECTED FOR PROFILE</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked="">
                        <div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary"></div>
                    </label>
                </div> -->
            <div class="grid grid-cols-1 gap-xs">
                <div class="flex gap-xs">
                    <?= $form->field($model, '[__index__]identifier_type', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassMono(), 'placeholder' => 'e.g., Orcid', 'disabled' => 'disabled']) ?>
                    <?= $form->field($model, '[__index__]identifier_value', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassMono(), 'placeholder' => 'Identifier Value', 'disabled' => 'disabled']) ?>
                    <?= $form->field($model, '[__index__]verification_status', FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherIdentifier::getVerificationOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select ...', 'disabled' => 'disabled']) ?>
                </div>
            </div>
        </div>
    </div>

</div>