<?php

namespace frontend\controllers;

use frontend\models\Publications;
use frontend\models\Researcher;
use frontend\models\ResearcherEducation;
use frontend\models\ResearcherIdentifier;
use frontend\models\ResearcherMedia;
use frontend\models\ResearcherSearch;
use frontend\models\ResearcherStatement;
use Yii;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

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
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['logout', 'signup', 'index', 'view', 'create', 'update', 'delete'],
                    'rules' => [
                        [
                            'actions' => ['login', 'error'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['logout', 'index', 'view', 'create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
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
    public function actionView($id)
    {
        $researcher = Researcher::find()->where(['id' => $id])->with([
            'researcherEducations',
            'publications',
            'researcherIdentifiers',
            'researcherStatement',
            'researcherMedia'
        ])->one();
        $researcher->profile_photo = 'https://randomuser.me/api/portraits/men/75.jpg';
        return $this->render('view', [
            'model' => $researcher,
        ]);
    }

    /**
     * Creates a new Researcher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    /* public function actionCreate()
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
     }*/


    public function actionCreate()
    {
        $this->layout = 'create';
        $modelProfile = new \frontend\models\Researcher();
        $modelEducations = [new \frontend\models\ResearcherEducation()];
        $modelPublications = [new \frontend\models\Publications()];
        $modelIdentifiers = [new \frontend\models\ResearcherIdentifier()];
        $modelStatements = new \frontend\models\ResearcherStatement();
        $modelMedia = [new \frontend\models\ResearcherMedia()];

        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();

            // Parent model
            $modelProfile->load($post);

            // Child models
            $modelEducations = $this->createMultipleModels(
                \frontend\models\ResearcherEducation::class,
                $post
            );

            $modelPublications = $this->createMultipleModels(
                \frontend\models\Publications::class,
                $post
            );

            $modelIdentifiers = $this->createMultipleModels(
                \frontend\models\ResearcherIdentifier::class,
                $post
            );

            $modelStatements = new \frontend\models\ResearcherStatement();

            $modelMedia = $this->createMultipleModels(
                \frontend\models\ResearcherMedia::class,
                $post
            );

            // Remove completely empty rows
            $modelEducations = $this->filterEmptyModels(
                $modelEducations,
                ['degree', 'institution_name']
            );

            $modelPublications = $this->filterEmptyModels(
                $modelPublications,
                ['title']
            );

            $modelIdentifiers = $this->filterEmptyModels(
                $modelIdentifiers,
                ['identifier_type', 'identifier_value']
            );



            // Validate everything
            $modelProfile->user_id = Yii::$app->user->id; // Set user_id before validation
            $valid = $modelProfile->validate();

            $valid = Model::validateMultiple($modelEducations) && $valid;
            $valid = Model::validateMultiple($modelPublications) && $valid;
            $valid = Model::validateMultiple($modelIdentifiers) && $valid;
            $valid = $modelStatements->validate() && $valid;
            $valid = Model::validateMultiple($modelMedia) && $valid;

            if ($valid) {

                $transaction = Yii::$app->db->beginTransaction();

                try {

                    $modelProfile->save(false);

                    foreach ($modelEducations as $education) {

                        $education->researcher_id = $modelProfile->id;

                        $education->save(false);
                    }

                    foreach ($modelPublications as $publication) {

                        $publication->researcher_id = $modelProfile->id;

                        $publication->save(false);
                    }

                    foreach ($modelIdentifiers as $identifier) {

                        $identifier->researcher_id = $modelProfile->id;

                        $identifier->save(false);
                    }

                    $modelStatements->researcher_id = $modelProfile->id;
                    $modelStatements->save(false);


                    foreach ($modelMedia as $media) {

                        $media->researcher_id = $modelProfile->id;

                        $media->save(false);
                    }

                    $transaction->commit();

                    Yii::$app->session->setFlash(
                        'success',
                        'Researcher profile created successfully.'
                    );

                    return $this->redirect([
                        'view',
                        'id' => $modelProfile->id
                    ]);

                } catch (\Throwable $e) {

                    $transaction->rollBack();

                    $modelProfile->addError(
                        '_form',
                        $e->getMessage()
                    );
                }
            }

            // Collect validation errors for errorSummary
            $this->collectErrors(
                $modelProfile,
                [
                    'Education' => $modelEducations,
                    'Publication' => $modelPublications,
                    'Identifier' => $modelIdentifiers,
                    //'Statement' => $modelStatements,
                    'Media' => $modelMedia,
                ]
            );

            // Prevent JS errors when all rows removed
            if (empty($modelEducations)) {
                $modelEducations = [new ResearcherEducation()];
            }

            if (empty($modelPublications)) {
                $modelPublications = [new Publications()];
            }

            if (empty($modelIdentifiers)) {
                $modelIdentifiers = [new ResearcherIdentifier()];
            }

            if (empty($modelStatements)) {
                $modelStatements = new ResearcherStatement();
            }

            if (empty($modelMedia)) {
                $modelMedia = [new ResearcherMedia()];
            }
        }

        return $this->render('create', [
            'model' => $modelProfile,
            'modelEducation' => $modelEducations,
            'modelPublications' => $modelPublications,
            'modelIdentifiers' => $modelIdentifiers,
            'modelStatements' => $modelStatements,
            'modelMedia' => $modelMedia,
        ]);
    }


    
protected function createMultipleModels(
    string $className,
    array $post,
    array $existingModels = []
): array {

    $model = new $className();

    $formName = $model->formName();

    if (
        !isset($post[$formName]) ||
        !is_array($post[$formName])
    ) {
        return [];
    }

    $existingMap = [];

    foreach ($existingModels as $existing) {

        if (!$existing->isNewRecord) {

            $existingMap[$existing->id] = $existing;
        }
    }

    $models = [];

    foreach ($post[$formName] as $row) {

        if (
            !empty($row['id']) &&
            isset($existingMap[$row['id']])
        ) {

            $instance = $existingMap[$row['id']];

        } else {

            $instance = new $className();
        }

        $instance->load($row, '');

        $models[] = $instance;
    }

    return $models;
}




    protected function filterEmptyModels(
        array $models,
        array $attributes
    ): array {

        return array_values(
            array_filter(
                $models,
                function ($model) use ($attributes) {

                    foreach ($attributes as $attribute) {

                        if (!empty($model->$attribute)) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }


    protected function collectErrors(
        Researcher $parent,
        array $modelGroups
    ): void {

        foreach ($modelGroups as $groupName => $models) {

            foreach ($models as $index => $model) {

                foreach ($model->getErrors() as $attribute => $errors) {

                    foreach ($errors as $error) {

                        $parent->addError(
                            '_form',
                            sprintf(
                                '[%s #%s] %s: %s',
                                $groupName,
                                $index + 1,
                                $attribute,
                                $error
                            )
                        );
                    }
                }
            }
        }
    }





    /**
     * Updates an existing Researcher model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
   /* public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/


public function actionUpdate($id)
{
     $this->layout = 'create';
    $modelProfile = $this->findModel($id);

    $modelEducations = $modelProfile->researcherEducations ?: [new ResearcherEducation()];
    $modelPublications = $modelProfile->publications ?: [new Publications()];
    $modelIdentifiers = $modelProfile->researcherIdentifiers ?: [new ResearcherIdentifier()];
    $modelStatements = $modelProfile->researcherStatement ?: new ResearcherStatement();
    $modelMedia = $modelProfile->researcherMedia ?: [new ResearcherMedia()];

    if (Yii::$app->request->isPost) {

        $post = Yii::$app->request->post();

        // Store old IDs before rebuilding arrays
        $oldEducationIds = ArrayHelper::getColumn(
            array_filter($modelEducations, fn($m) => !$m->isNewRecord),
            'id'
        );

        $oldPublicationIds = ArrayHelper::getColumn(
            array_filter($modelPublications, fn($m) => !$m->isNewRecord),
            'id'
        );

        $oldIdentifierIds = ArrayHelper::getColumn(
            array_filter($modelIdentifiers, fn($m) => !$m->isNewRecord),
            'id'
        );

        $oldMediaIds = ArrayHelper::getColumn(
            array_filter($modelMedia, fn($m) => !$m->isNewRecord),
            'id'
        );

        // Load parent
        $modelProfile->load($post);

        // Rebuild child arrays
        $modelEducations = $this->createMultipleModels(
            ResearcherEducation::class,
            $post,
            $modelProfile->researcherEducations
        );

        $modelPublications = $this->createMultipleModels(
            Publications::class,
            $post,
            $modelProfile->publications
        );

        $modelIdentifiers = $this->createMultipleModels(
            ResearcherIdentifier::class,
            $post,
            $modelProfile->researcherIdentifiers
        );

        $modelMedia = $this->createMultipleModels(
            ResearcherMedia::class,
            $post,
            $modelProfile->researcherMedia
        );

        $modelStatements->load($post);

        // Remove empty rows
        $modelEducations = $this->filterEmptyModels(
            $modelEducations,
            ['degree', 'institution_name']
        );

        $modelPublications = $this->filterEmptyModels(
            $modelPublications,
            ['title']
        );

        $modelIdentifiers = $this->filterEmptyModels(
            $modelIdentifiers,
            ['identifier_type', 'identifier_value']
        );

        // Determine deleted rows

        $deletedEducationIds = array_diff(
            $oldEducationIds,
            ArrayHelper::getColumn(
                array_filter($modelEducations, fn($m) => !$m->isNewRecord),
                'id'
            )
        );

        $deletedPublicationIds = array_diff(
            $oldPublicationIds,
            ArrayHelper::getColumn(
                array_filter($modelPublications, fn($m) => !$m->isNewRecord),
                'id'
            )
        );

        $deletedIdentifierIds = array_diff(
            $oldIdentifierIds,
            ArrayHelper::getColumn(
                array_filter($modelIdentifiers, fn($m) => !$m->isNewRecord),
                'id'
            )
        );

        $deletedMediaIds = array_diff(
            $oldMediaIds,
            ArrayHelper::getColumn(
                array_filter($modelMedia, fn($m) => !$m->isNewRecord),
                'id'
            )
        );

        // Validate

        $valid = $modelProfile->validate();

        $valid = Model::validateMultiple($modelEducations) && $valid;
        $valid = Model::validateMultiple($modelPublications) && $valid;
        $valid = Model::validateMultiple($modelIdentifiers) && $valid;
        $valid = Model::validateMultiple($modelMedia) && $valid;
        $valid = $modelStatements->validate() && $valid;

        if ($valid) {

            $transaction = Yii::$app->db->beginTransaction();

            try {

                $modelProfile->save(false);

                // Delete removed rows

                if (!empty($deletedEducationIds)) {
                    ResearcherEducation::deleteAll([
                        'id' => $deletedEducationIds
                    ]);
                }

                if (!empty($deletedPublicationIds)) {
                    Publications::deleteAll([
                        'id' => $deletedPublicationIds
                    ]);
                }

                if (!empty($deletedIdentifierIds)) {
                    ResearcherIdentifier::deleteAll([
                        'id' => $deletedIdentifierIds
                    ]);
                }

                if (!empty($deletedMediaIds)) {
                    ResearcherMedia::deleteAll([
                        'id' => $deletedMediaIds
                    ]);
                }

                // Save education

                foreach ($modelEducations as $education) {

                    $education->researcher_id = $modelProfile->id;

                    $education->save(false);
                }

                // Save publications

                foreach ($modelPublications as $publication) {

                    $publication->researcher_id = $modelProfile->id;

                    $publication->save(false);
                }

                // Save identifiers

                foreach ($modelIdentifiers as $identifier) {

                    $identifier->researcher_id = $modelProfile->id;

                    $identifier->save(false);
                }

                // Save statement

                $modelStatements->researcher_id = $modelProfile->id;

                $modelStatements->save(false);

                // Save media

                foreach ($modelMedia as $media) {

                    $media->researcher_id = $modelProfile->id;

                    $media->save(false);
                }

                $transaction->commit();

                Yii::$app->session->setFlash(
                    'success',
                    'Researcher profile updated successfully.'
                );

                return $this->redirect([
                    'view',
                    'id' => $modelProfile->id
                ]);

            } catch (\Throwable $e) {

                $transaction->rollBack();

                $modelProfile->addError(
                    '_form',
                    $e->getMessage()
                );
            }
        }

        $this->collectErrors(
            $modelProfile,
            [
                'Education' => $modelEducations,
                'Publication' => $modelPublications,
                'Identifier' => $modelIdentifiers,
                'Media' => $modelMedia,
            ]
        );
    }

    return $this->render('update', [
        'model' => $modelProfile,
        'modelEducation' => $modelEducations,
        'modelPublications' => $modelPublications,
        'modelIdentifiers' => $modelIdentifiers,
        'modelStatements' => $modelStatements,
        'modelMedia' => $modelMedia,
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


}
