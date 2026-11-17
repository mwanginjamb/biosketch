<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\components;

use frontend\models\Researcher;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Base for resources nested under a researcher:
 *   /apiv1/researchers/<researcher_id>/<resource>[/<id>]
 *
 * The parent researcher always comes from the URL. Records are scoped to it, and
 * `researcher_id` in a request body is ignored, so records cannot be moved between
 * researchers.
 */
abstract class ChildController extends BaseController
{
    protected ?Researcher $researcher = null;

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if ($action->id === 'options') {
            return true;
        }

        $researcher = Researcher::findOne((int) Yii::$app->request->get('researcher_id'));
        // Unknown researchers and drafts the caller may not see are both 404.
        if ($researcher === null || !$this->canRead($researcher)) {
            throw new NotFoundHttpException('Researcher not found.');
        }
        $this->researcher = $researcher;

        return true;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = fn() => new ActiveDataProvider([
            'query' => ($this->modelClass)::find()->where(['researcher_id' => $this->researcher->id]),
        ]);
        $actions['view']['findModel'] = fn($id) => $this->findChild($id);
        $actions['delete']['findModel'] = fn($id) => $this->findChild($id);

        // create and update are implemented below so researcher_id is always taken from the URL.
        unset($actions['create'], $actions['update']);

        return $actions;
    }

    public function actionCreate()
    {
        $this->checkAccess('create');

        /** @var ActiveRecord $model */
        $model = new $this->modelClass();
        $model->load(Yii::$app->request->bodyParams, '');
        $model->researcher_id = $this->researcher->id;

        if ($model->save()) {
            Yii::$app->response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the record.');
        }

        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->findChild($id);
        $this->checkAccess('update', $model);

        $model->load(Yii::$app->request->bodyParams, '');
        $model->researcher_id = $this->researcher->id;

        if (!$model->save() && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the record.');
        }

        return $model;
    }

    /**
     * Reads follow the parent researcher's visibility (checked in beforeAction);
     * writes require owning the researcher profile, or the admin role.
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, ['create', 'update', 'delete'], true) && !$this->ownsOrAdmin($this->researcher)) {
            throw new ForbiddenHttpException('You do not own this profile.');
        }
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['update'] = ['PUT', 'PATCH'];

        return $verbs;
    }

    protected function findChild($id): ActiveRecord
    {
        return ($this->modelClass)::findOne([
            'id' => (int) $id,
            'researcher_id' => $this->researcher->id,
        ]) ?? throw new NotFoundHttpException('Record not found.');
    }
}
