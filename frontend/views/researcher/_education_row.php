<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="education-container"  class="space-y-sm">
    <div class="education-item" data-index="<?= $index ?>">
        <div class="flex items-center justify-between mb-2">

                    <button type="button"
                        class="remove-source flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                        ✕
                    </button>

        </div>

        <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group">
            <div class="grid grid-cols-1 gap-xs">
                <div class="flex gap-xs">
                        <?= $form->field($model, 'degree',FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherEducation::getDegreeOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select Source Type']) ?>
                        <?= $form->field($model, 'graduation_year', FormUi::fieldConfig())->textInput(['type' => 'number','pattern' => '[0-9]{4}','class' => FormUi::inputClassStandard(),'placeholder' => 'Graduation Year']) ?>
                    </div>
                <?= $form->field($model, 'institution_name', FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(),'placeholder' => 'Institution Name']) ?>
                    <?= $form->field($model, 'field_of_study', FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(),'placeholder' => 'Field of Study (e.g., Immunology)']) ?>
                </div>
        </div>
    </div>

</div>