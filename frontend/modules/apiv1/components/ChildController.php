<?php
// frontend/modules/v1/components/ChildController.php
namespace frontend\modules\v1\components;

use api\modules\v1\components\BaseController;
use frontend\modules\apiV1\models\Researcher;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

abstract class ChildController extends BaseController
{
    protected ?Researcher $researcher = null;

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->researcher = Researcher::findOne(\Yii::$app->request->get('researcher_id'))
            ?? throw new NotFoundHttpException('Researcher not found.');
        return true;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = fn() => new ActiveDataProvider([
            'query' => ($this->modelClass)::find()->where(['researcher_id' => $this->researcher->id]),
        ]);
        // scope single-record lookups to the parent
        foreach (['view', 'update', 'delete'] as $a) {
            $actions[$a]['findModel'] = fn($id) => ($this->modelClass)::findOne([
                'id' => $id,
                'researcher_id' => $this->researcher->id,
            ]) ?? throw new NotFoundHttpException('Record not found.');
        }
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $this->checkAccess('create');
        $model = new $this->modelClass();
        $model->load(\Yii::$app->request->bodyParams, '');
        $model->researcher_id = $this->researcher->id;   // from the URL, not the body
        if ($model->save()) {
            \Yii::$app->response->setStatusCode(201);
        }
        return $model;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (
            in_array($action, ['create', 'update', 'delete'])
            && (int) $this->researcher->user_id !== (int) \Yii::$app->user->id
        ) {
            throw new ForbiddenHttpException('You do not own this profile.');
        }
    }
}