<?php

use frontend\models\Researcher;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var frontend\models\ResearcherSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Researchers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="researcher-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Researcher'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'title',
            'full_name',
            'primary_institution',
            //'department',
            //'role_title',
            //'email:email',
            //'website',
            //'location',
            //'era_commons_id',
            //'orcid',
            //'profile_photo',
            //'status',
            //'version',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Researcher $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    },
                'template' => '{view} {update} {delete} {report}',
                'visibleButtons' => [
                    'delete' => function ($model, $key, $index) {
                            // return Yii::$app->user->can('deleteClinicalTrial', ['trial' => $model]);
                            return $model->created_by == Yii::$app->user->id;
                        }
                ],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="material-symbols-outlined" style="font-size:18px;">visibility</span>',
                                $url,
                                [
                                    'title' => 'View',
                                    'encode' => false,
                                    'class' => 'inline-flex items-center justify-center w-8 h-8 rounded-lg '
                                        . 'hover:bg-surface-container-high text-on-surface-variant '
                                        . 'transition-colors',
                                ]
                            );
                        },
                    'update' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="material-symbols-outlined" style="font-size:18px;">edit</span>',
                                $url,
                                [
                                    'title' => 'Edit',
                                    'encode' => false,
                                    'class' => 'inline-flex items-center justify-center w-8 h-8 rounded-lg '
                                        . 'hover:bg-primary-fixed text-primary transition-colors',
                                ]
                            );
                        },
                    'delete' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="material-symbols-outlined" style="font-size:18px;">delete</span>',
                                $url,
                                [
                                    'title' => 'Delete',
                                    'encode' => false,
                                    'class' => 'inline-flex items-center justify-center w-8 h-8 rounded-lg '
                                        . 'hover:bg-error-container text-error transition-colors',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this clinical trial? This action cannot be undone.',
                                        'method' => 'post',
                                    ],
                                ]
                            );
                        },
                    // report button to generate and view the Researcher BioSketch PDF
                    'report' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="material-symbols-outlined" style="font-size:18px;">description</span>',
                                $url,
                                [
                                    'title' => 'Report',
                                    'encode' => false,
                                    'class' => 'inline-flex items-center justify-center w-8 h-8 rounded-lg '
                                        . 'hover:bg-primary-container text-on-primary-container transition-colors',
                                ]
                            );
                        },




                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>