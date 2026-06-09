<?php

namespace frontend\controllers;

use frontend\models\Researcher;
use frontend\models\ResearcherSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\ResearcherEducation;
use yii\web\BadRequestHttpException;

use Yii;

/**
 * ResearcherController implements the CRUD actions for Researcher model.
 */
class ResearcherController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Researcher models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ResearcherSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Researcher model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id = 1)
    {
        //$model = $this->findModel($id),
        $model = new Researcher();
        $model->id = $id;
        $model->avatar_url = 'https://randomuser.me/api/portraits/men/75.jpg';
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Researcher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->layout = 'create';
        $model = new Researcher();
        $model->user_id = 1; // default to user ID 1 for testing

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        //

        return $this->render('create', [
            'model' => $model,
            'modelEducation' => [new \frontend\models\ResearcherEducation()],
            'modelPublications' => [new \frontend\models\Publications()]
        ]);
    }

    /**
     * Updates an existing Researcher model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Researcher model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Researcher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Researcher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Researcher::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionSaveEducation()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    if (!Yii::$app->request->isPost && !Yii::$app->request->isPut || !Yii::$app->request->isAjax) {
        throw new BadRequestHttpException('Invalid request.');
    }

    $body = Json::decode(Yii::$app->request->rawBody, true);
    if (empty($body)) {
        return ['success' => false, 'message' => 'Empty request body.'];
    }

    $id = $body['id'] ?? null;
    if ($id) {
        $model = ResearcherEducation::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Record not found.'];
        }
    } else {
        $model = new ResearcherEducation();
    }

    // Map attributes (adjust if your field names differ)
    $model->setAttributes([
        'degree'          => $body['degree'] ?? null,
        'institution_name'=> $body['institution_name'] ?? null,
        'field_of_study'  => $body['field_of_study'] ?? null,
        'graduation_year' => $body['graduation_year'] ?? null,
        'sort_order'      => $body['sort_order'] ?? 0,
        // researcher_id will be set after main researcher is saved? For now, you may need to link:
        // 'researcher_id' => Yii::$app->user->identity->researcher_id ?? null,
    ]);

    // If you have a researcher_id from session or logged-in user, set it
    if (!$id && !$model->researcher_id) {
        // Example: get from logged-in user's profile
        $model->researcher_id = Yii::$app->user->identity->id ?? null;
    }

    if ($model->save()) {
        return [
            'success' => true,
            'id'      => $model->id,
            'message' => $id ? 'Entry updated.' : 'Entry saved.',
        ];
    }

    return [
        'success' => false,
        'message' => 'Validation failed',
        'errors'  => $model->getFirstErrors(),
    ];
}
}
