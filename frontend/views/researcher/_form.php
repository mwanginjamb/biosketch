<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use common\library\FormUi;

/** @var yii\web\View $this */
/** @var frontend\models\Researcher $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<!-- Welcome / Active Tab Indicator -->
<div class="flex items-center gap-xs mb-sm">
    <span class="material-symbols-outlined text-on-surface-variant" data-icon="edit_note">edit_note</span>
    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Editor / Data Entry</span>
</div>

<?php $form = ActiveForm::begin(FormUi::formConfig('researcher-form', true));?>
<?= FormUi::beginSection('Personal Information', 'person') ?>

<?= $form->errorSummary($model) ?>
    <!-- Personal Information -->

 
    <!-- Photo upload slot (non-model, handled by separate upload action) -->
    <?=  FormUi::photoUploadWidget('attachment', $model->profile_photo ?? '') ?>

    <?php $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->user->id]) ?>

    <?= $form->field($model, 'title', FormUi::fieldConfig())->dropDownList($model->titles,['prompt' => 'Select your title','maxlength' => true,'class' => FormUi::selectClass()]) ?>

    <?= $form->field($model, 'full_name', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'Enter your full name.']) ?>

    <?= $form->field($model, 'primary_institution', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'e.g. Kenya Medical Research Institute']) ?>

    <?= $form->field($model, 'department', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'e.g Center for Virus Research']) ?>

    <?= $form->field($model, 'role_title', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'e.g Research Scientist']) ?>

    <?= $form->field($model, 'email', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'Enter your email.']) ?>

    <?= $form->field($model, 'website', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'Enter your website URL.']) ?>

    <?= $form->field($model, 'location', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'Enter your location.']) ?>

    <div class="grid grid-cols-2 gap-xs">

        <?= $form->field($model, 'era_commons_id', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'Enter your ERA Commons ID.']) ?>

        <?= $form->field($model, 'orcid', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(),'placeholder' => 'Enter your ORCID.']) ?>

    </div>

    

    <?= $form->field($model, 'status', FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard()]) ?>

    <?= $form->field($model, 'version', FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassStandard()]) ?>

    

  


    
    <?= FormUi::endSection() ?>   
    
    
    
    <!-- Education and Training  -->
    
    <?= FormUi::beginSection('Education and Training', 'school') ?>
    
    
    

    
    
    <?= FormUi::endSection() ?>  
    
    
    <!-- Save Actions -->
    <div class="pt-sm pb-lg space-y-sm">
      <?= Html::submitButton('Save Progress', ['class' => FormUi::buttonClass('auth')]) ?>
    <?= ($model->id)?FormUi::secondaryButton('Preview BioSketch', 'visibility', ['biosketch/preview', 'id' => $model->id ?? null]):'' ?>
    </div>
    
    <!-- End composite form -->
    <?php ActiveForm::end(); ?>
    