<?php

namespace frontend\controllers;

use frontend\models\Publications;
use frontend\models\Researcher;
use frontend\models\ResearcherEducation;
use frontend\models\ResearcherGrant;
use frontend\models\ResearcherIdentifier;
use frontend\models\ResearcherMedia;
use frontend\models\ResearcherSearch;
use frontend\models\ResearcherStatement;
use Yii;
use yii\base\Model;
use yii\bootstrap5\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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
            'researcherMedia',
            'researcherGrants'
        ])->one();
        //$researcher->profile_photo = 'https://randomuser.me/api/portraits/men/75.jpg';
        return $this->render('view', [
            'model' => $researcher,
        ]);
    }

    // Report Generation

    public function actionReport($id)
    {
        $researcher = Researcher::find()->where(['id' => $id])->with([
            'researcherEducations',
            'publications',
            'researcherIdentifiers',
            'researcherStatement',
            'researcherMedia',
            'researcherGrants'
        ])->one();

        $footerHtml = '
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="text-align:left; font-size:9px; color:#4a5568; padding-top:5px;">
                Researcher BioSketch &nbsp;|&nbsp; Page {PAGENO} of {nb}
            </td>
            <td style="text-align:right; font-size:9px; color:#4a5568; padding-top:5px;">
                 &nbsp;|&nbsp; Powered by ' . Html::encode(env('DEVELOPER')) . '
            </td>
        </tr>
    </table>';

        if (!$researcher) {
            throw new NotFoundHttpException('Researcher BioSketch not found.');
        }

        $content = $this->renderPartial('_report', [
            'biosketch' => $researcher,
        ]);

        $title = $researcher->full_name . ' - Researcher BioSketch';

        $pdf = Yii::$app->pdf;

        $pdf->content = $content;

        $pdf->marginBottom = 25;   // room reserved above the physical footer
        $pdf->marginFooter = 10;   // gap between footer content and page edge

        //$pdf->cssFile = Yii::getAlias('@webroot/css/report.css');

        $pdf->methods = [
            'SetHeader' => [$title . ' Generated On: ' . date("r")],
            'SetFooter' => [$footerHtml],

            'SetAuthor' => env('DEVELOPER'),
            'SetCreator' => Yii::$app->name,

            'SetTitle' => $researcher->full_name .
                ' - Researcher BioSketch',
        ];

        $binary = $pdf->render();

        $base64Content = chunk_split(
            base64_encode($binary)
        );

        return $this->renderAjax('_report_render', [
            'content' => $base64Content,
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
        $modelProfile = new Researcher();
        $modelEducations = [new ResearcherEducation()];
        $modelPublications = [new Publications()];
        $modelIdentifiers = [new ResearcherIdentifier()];
        $modelStatements = new ResearcherStatement();
        $modelMedia = [new ResearcherMedia()];
        $modelGrant = [new ResearcherGrant()];

        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();

            // Parent model
            $modelProfile->load($post);

            // Handle file upload for profile photo
            $modelProfile->attachment = UploadedFile::getInstanceByName('attachment');

            if ($modelProfile->attachment instanceof UploadedFile) {
                $newPhotoPath = $this->saveProfilePhoto(
                    $modelProfile,
                    $modelProfile->attachment
                );
                $modelProfile->profile_photo = $newPhotoPath;
                $modelProfile->save(false); // persist the profile photo path before validation of child models
            }


            // Child models
            $modelEducations = $this->createMultipleModels(
                ResearcherEducation::class,
                $post
            );

            $modelPublications = $this->createMultipleModels(
                Publications::class,
                $post
            );

            $modelIdentifiers = $this->createMultipleModels(
                ResearcherIdentifier::class,
                $post
            );

            $modelStatements = new ResearcherStatement();

            $modelMedia = $this->createMultipleModels(
                ResearcherMedia::class,
                $post
            );

            $modelGrant = $this->createMultipleModels(
                ResearcherGrant::class,
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

            $modelGrant = $this->filterEmptyModels(
                $modelGrant,
                ['funding_agency_id', 'grant_type_id', 'role_id']
            );

            // Validate everything
            $modelProfile->user_id = Yii::$app->user->id; // Set user_id before validation
            $valid = $modelProfile->validate();

            $valid = Model::validateMultiple($modelEducations) && $valid;
            $valid = Model::validateMultiple($modelPublications) && $valid;
            $valid = Model::validateMultiple($modelIdentifiers) && $valid;
            $valid = $modelStatements->validate() && $valid;
            $valid = Model::validateMultiple($modelMedia) && $valid;
            $valid = Model::validateMultiple($modelGrant) && $valid;
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

                    foreach ($modelGrant as $grant) {
                        $grant->researcher_id = $modelProfile->id;
                        $grant->save(false);
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

                    // role back even file system activity if profile photo was uploaded
                    if ($newPhotoPath !== null) {
                        $this->deleteProfilePhoto($newPhotoPath);
                    }

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
                    'Grant' => $modelGrant,
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

            if (empty($modelGrant)) {
                $modelGrant = [new ResearcherGrant()];
            }
        }

        return $this->render('create', [
            'model' => $modelProfile,
            'modelEducation' => $modelEducations,
            'modelPublications' => $modelPublications,
            'modelIdentifiers' => $modelIdentifiers,
            'modelStatements' => $modelStatements,
            'modelMedia' => $modelMedia,
            'modelGrant' => $modelGrant,
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
        $modelGrant = $modelProfile->researcherGrants ?: [new ResearcherGrant()];

        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();

            /* echo '<pre>';
             print_r($post);
             echo '</pre>';
             exit;*/

            $modelProfile->attachment = UploadedFile::getInstanceByName('attachment');
            $oldProfilePhoto = $modelProfile->profile_photo;
            $newProfilePhoto = null;

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

            $oldGrantIds = ArrayHelper::getColumn(
                array_filter($modelGrant, fn($m) => !$m->isNewRecord),
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

            $modelGrant = $this->createMultipleModels(
                ResearcherGrant::class,
                $post,
                $modelProfile->researcherGrants
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

            $modelGrant = $this->filterEmptyModels(
                $modelGrant,
                ['funding_agency_id', 'grant_type_id', 'role_id']
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

            $deletedGrantIds = array_diff(
                $oldGrantIds,
                ArrayHelper::getColumn(
                    array_filter($modelGrant, fn($m) => !$m->isNewRecord),
                    'id'
                )
            );

            // Validate

            $valid = $modelProfile->validate();

            $valid = Model::validateMultiple($modelEducations) && $valid;
            $valid = Model::validateMultiple($modelPublications) && $valid;
            $valid = Model::validateMultiple($modelIdentifiers) && $valid;
            $valid = Model::validateMultiple($modelMedia) && $valid;
            $valid = Model::validateMultiple($modelGrant) && $valid;
            $valid = $modelStatements->validate() && $valid;

            if ($valid) {

                $transaction = Yii::$app->db->beginTransaction();

                try {

                    $modelProfile->save(false);

                    if ($modelProfile->attachment instanceof UploadedFile) {

                        $newProfilePhoto = $this->saveProfilePhoto(
                            $modelProfile,
                            $modelProfile->attachment
                        );

                        $modelProfile->profile_photo = $newProfilePhoto;

                        $modelProfile->save(false);
                    }

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

                    if (!empty($deletedGrantIds)) {
                        ResearcherGrant::deleteAll([
                            'id' => $deletedGrantIds
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

                    // Save grants

                    foreach ($modelGrant as $grant) {

                        $grant->researcher_id = $modelProfile->id;

                        $grant->save(false);
                    }

                    $transaction->commit();

                    // cleanup old profile photo if a new one was uploaded
                    if (
                        $newProfilePhoto !== null &&
                        $oldProfilePhoto &&
                        $oldProfilePhoto !== $newProfilePhoto
                    ) {
                        $this->deleteProfilePhoto($oldProfilePhoto);
                    }

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

                    // role back even file system activity if profile photo was uploaded
                    if ($newProfilePhoto !== null) {
                        $this->deleteProfilePhoto($newProfilePhoto);
                    }

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
                    'Grant' => $modelGrant,
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
            'modelGrant' => $modelGrant,
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

    /**
     * File operations helper (upload and delete) for profile photo.
     * @param int $id Researcher ID
     */

    protected function saveProfilePhoto(Researcher $model, UploadedFile $file): string
    {

        $uploadDir = Yii::getAlias('@frontend/web/uploads/researchers/profile');

        if (!is_dir($uploadDir)) {
            //mkdir($uploadDir, 0777, true);
            FileHelper::createDirectory($uploadDir, 0775, true);
        }

        $extension = strtolower($file->extension);

        // $fileName = 'researcher_' . $model->id . '_' . time() . '.' . $file->extension;
        $filename = sprintf(
            'researcher_%d_%s.%s',
            $model->id,
            Yii::$app->security->generateRandomString(6),
            $extension
        );

        $absolutePath = $uploadDir . DIRECTORY_SEPARATOR . $filename;


        if (!$file->saveAs($absolutePath)) {
            throw new \RuntimeException(
                'Unable to save researcher profile photo.'
            );
        }

        return '/uploads/researchers/profile/' . $filename;
    }

    protected function deleteProfilePhoto(?string $photoPath): void
    {
        if ($photoPath) {
            $absolutePath = Yii::getAlias('@frontend/web') . $photoPath;
            if (is_file($absolutePath)) {
                unlink($absolutePath);
            }
        }

        return;
    }


}
