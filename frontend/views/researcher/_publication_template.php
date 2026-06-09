<?php
use yii\helpers\Html;
use common\library\FormUi;

?>

<div id="education-template" class="hidden space-y-sm">

    <div class="education-item">
        <div class="flex items-center justify-between mb-2">

                    <button type="button"
                        class="remove-source flex items-center justify-center w-8 h-8 rounded-md hover:bg-red-50 text-red-500 transition">
                        ✕
                    </button>

        </div>

        <div class="p-xs bg-surface-container-low border border-outline-variant rounded relative group">
            <div class="grid grid-cols-1 gap-xs">
                <div class="flex gap-xs">
                    <?= $form->field(new \frontend\models\ResearcherEducation, "[__index__]degree",FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherEducation::getDegreeOptions(), ['class' => FormUi::selectClass(), 'prompt' => 'Select Degree']) ?>
                    <?= $form->field(new \frontend\models\ResearcherEducation, "[__index__]graduation_year", FormUi::fieldConfig())->textInput(['type' => 'number','pattern' => '[0-9]{4}','class' => FormUi::inputClassStandard(),'placeholder' => 'Graduation Year']) ?>
                    </div>
                <?= $form->field(new \frontend\models\ResearcherEducation, "[__index__]institution_name", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(),'placeholder' => 'Institution Name']) ?>
                    <?= $form->field(new \frontend\models\ResearcherEducation, "[__index__]field_of_study", FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard(),'placeholder' => 'Field of Study (e.g., Immunology)']) ?>
                </div>
        </div>

    </div>

    
</div>
<!-- /template -->