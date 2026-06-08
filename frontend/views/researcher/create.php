<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\Researcher $model */

$this->title = Yii::t('app', 'Create Researcher');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Researchers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="researcher-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelEducation' => $modelEducation,
        'modelPublications' => $modelPublications
    ]) ?>

</div>
