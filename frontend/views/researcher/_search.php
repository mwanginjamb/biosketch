<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\ResearcherSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="researcher-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'full_name') ?>

    <?= $form->field($model, 'primary_institution') ?>

    <?php // echo $form->field($model, 'department') ?>

    <?php // echo $form->field($model, 'role_title') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'website') ?>

    <?php // echo $form->field($model, 'location') ?>

    <?php // echo $form->field($model, 'era_commons_id') ?>

    <?php // echo $form->field($model, 'orcid') ?>

    <?php // echo $form->field($model, 'profile_photo') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'version') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
