<?php

use yii\bootstrap5\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;
use frontend\models\Researcher;
use frontend\models\ResearcherStatement;

/** @var yii\web\View $this */
/** @var frontend\models\Researcher $model */
/** @var yii\widgets\ActiveForm $form */
?>

<!-- Welcome / Active Tab Indicator -->
<div class="flex items-center gap-xs mb-sm">
    <span class="material-symbols-outlined text-on-surface-variant" data-icon="edit_note">edit_note</span>
    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Editor / Data
        Entry</span>
</div>

<?php $form = ActiveForm::begin(FormUi::formConfig('researcher-form', true)); ?>
<?= FormUi::beginSection('Personal Information', 'person') ?>

<?= $form->errorSummary($model) ?>
<!-- Personal Information -->


<!-- Photo upload slot (non-model, handled by separate upload action) -->
<?= FormUi::photoUploadWidget('attachment', $model->profile_photo ?? '') ?>

<?php $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->user->id]) ?>

<?= $form->field($model, 'title', FormUi::fieldConfig())->dropDownList($model->titles, ['prompt' => 'Select your title', 'class' => FormUi::selectClass()]) ?>

<?= $form->field($model, 'full_name', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(), 'placeholder' => 'Enter your full name.']) ?>

<?= $form->field($model, 'primary_institution', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(), 'placeholder' => 'e.g. Kenya Medical Research Institute']) ?>

<?= $form->field($model, 'department', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(), 'placeholder' => 'e.g Center for Virus Research']) ?>

<?= $form->field($model, 'role_title', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(), 'placeholder' => 'e.g Research Scientist']) ?>

<?= $form->field($model, 'email', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(), 'placeholder' => 'Enter your email.']) ?>

<?= $form->field($model, 'website', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(), 'placeholder' => 'Enter your website URL.']) ?>

<?= $form->field($model, 'location', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassStandard(), 'placeholder' => 'Enter your location.']) ?>

<div class="grid grid-cols-2 gap-xs">

    <?= $form->field($model, 'era_commons_id', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassMono(), 'placeholder' => 'ARIVERA_77']) ?>

    <?= $form->field($model, 'orcid', FormUi::fieldConfig())->textInput(['maxlength' => true, 'class' => FormUi::inputClassMono(), 'placeholder' => '0000-0002-1825-0097']) ?>

</div>



<?= $form->field($model, 'status', FormUi::fieldConfig())->dropDownList(Researcher::statusOptions(),['prompt' => 'Select ...','class' => FormUi::selectClass()]) ?>

<?= $form->field($model, 'research_tags', FormUi::fieldConfig())->textInput(['class' => FormUi::inputClassMono(),'placeholder' => 'e.g Genomics, Cell Bio, Virology']) ?>

<?php $form->field($model, 'version', FormUi::fieldConfig())->hiddenInput(['class' => FormUi::inputClassStandard()]) ?>

<?= FormUi::endSection() ?>



<!-- Education and Training  -->


<section class="bg-surface-container-lowest border border-outline-variant rounded p-sm space-y-sm">
    <div class="flex items-center justify-between border-b border-outline-variant pb-xs mb-sm">
        <h2 class="font-headline-md text-headline-md">Education &amp; Training</h2>

        <button type="button" class="text-secondary flex items-center" id="add-education">
            <span class="material-symbols-outlined" data-icon="add_circle">add_circle</span>
        </button>
    </div>


    <div id="education-wrapper" class="space-y-4">
        <?php foreach ($eduLines as $index => $edu): ?>
            <?= $this->render('_education_row', ['model' => $edu, 'index' => $index, 'form' => $form]) ?>
        <?php endforeach; ?>
    </div>

    <!-- Template for new education entries -->
    <?= $this->render('_education_template', ['model' => $edu, 'form' => $form]) ?>

</section>

<!-- Researcher Statement -->
<?= FormUi::beginSection('Researcher Statement', 'description') ?>

    <?= $form->field($modelStatements, 'statement_type', FormUi::fieldConfig())->dropDownList(\frontend\models\ResearcherStatement::getStatementTypeOptions(), ['prompt' => 'Select statement type', 'class' => FormUi::selectClass()]) ?>

    <?= $form->field($modelStatements, 'content', FormUi::fieldConfig())->textarea(['rows' => 6, 'class' => FormUi::textareaClass(), 'placeholder' => 'Enter a brief statement about your research interests and expertise.']) ?>

<?= FormUi::endSection() ?>


<!-- Accomplishments and Intellectual Property -->
<?= FormUi::beginSection('Accomplishments & Intellectual Property', 'emoji_events') ?>

<?= $form->field($model, 'major_breakthrough', FormUi::fieldConfig())->textarea(['rows' => 6, 'class' => FormUi::textareaClass(), 'placeholder' => 'Describe your most significant scientific contribution...']) ?>

<?= $form->field($model, 'patent_filed', FormUi::fieldConfig())->textarea(['rows' => 6, 'class' => FormUi::textareaClass(), 'placeholder' => 'Enter patent numbers, titles, or status...']) ?>
<?= FormUi::endSection() ?>



<!-- Publications -->


<section class="bg-surface-container-lowest border border-outline-variant rounded p-sm space-y-sm">
    <div class="flex items-center justify-between border-b border-outline-variant pb-xs mb-sm">
        <h2 class="font-headline-md text-headline-md">Select Publications</h2>

        <button type="button" class="text-secondary flex items-center" id="add-publication">
            <span class="material-symbols-outlined" data-icon="add_circle">add_circle</span>
        </button>
    </div>


    <div id="publication-wrapper" class="space-y-4">
        <?php

        foreach ($publicationLines as $index => $pub): ?>
            <?= $this->render('_publication_row', ['model' => $pub, 'index' => $index, 'form' => $form]) ?>
        <?php endforeach; ?>
    </div>

    <!-- Template for new publication entries -->
    <?= $this->render('_publication_template', ['model' => new \frontend\models\Publications(), 'form' => $form]) ?>

</section>


<!-- Publications -->






<!-- Save Actions -->
<div class="pt-sm pb-lg space-y-sm">
    <?= Html::submitButton('Save Progress', ['class' => FormUi::buttonClass('auth')]) ?>
    <?= ($model->id) ? FormUi::secondaryButton('Preview BioSketch', 'visibility', ['biosketch/preview', 'id' => $model->id ?? null]) : '' ?>
</div>

<!-- End composite form -->
<?php ActiveForm::end(); ?>


<?php $this->registerJsFile('@web/js/form.js', ['position' => \yii\web\View::POS_END]); ?>