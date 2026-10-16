<?php
// frontend/modules/apiv1/controllers/ResearcherController.php
namespace frontend\modules\apiv1\controllers;

use frontend\modules\apiv1\components\BaseController;
use frontend\modules\apiv1\models\Researcher;
use frontend\modules\apiv1\models\search\ResearcherSearch;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

class ResearcherController extends BaseController
{
    public $modelClass = Researcher::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function () {
            return (new ResearcherSearch())->search(\Yii::$app->request->queryParams);
        };
        return $actions;
    }

    /** Never trust user_id from the client. */
    public function actionCreate()
    {
        $model = new Researcher();
        $model->load(\Yii::$app->request->bodyParams, '');
        $model->user_id = \Yii::$app->user->id;

        if ($model->save()) {
            \Yii::$app->response->setStatusCode(201);
            return $model;
        }
        return $model;   // 422 is handled by validation errors
    }

    public function actionUploadPhoto($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess('update', $model);

        $model->attachment = UploadedFile::getInstanceByName('attachment');
        if ($model->validate(['attachment']) && $model->attachment) {
            $name = uniqid('res_') . '.' . $model->attachment->extension;
            $model->attachment->saveAs(\Yii::getAlias('@uploads') . '/' . $name);
            $model->profile_photo = $name;
            $model->save(false, ['profile_photo']);
        }
        return $model;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (
            in_array($action, ['update', 'delete']) && $model
            && (int) $model->user_id !== (int) \Yii::$app->user->id
        ) {
            throw new ForbiddenHttpException('You do not own this profile.');
        }
    }

    protected function findModel($id)
    {
        return Researcher::findOne($id)
            ?? throw new \yii\web\NotFoundHttpException('Researcher not found.');
    }
}