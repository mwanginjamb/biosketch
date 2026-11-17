<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use common\components\RbacHelper;
use common\models\User;
use frontend\components\ProfilePhotoStorage;
use frontend\modules\apiv1\components\BaseController;
use frontend\modules\apiv1\models\Researcher;
use frontend\modules\apiv1\models\search\ResearcherSearch;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ConflictHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * /apiv1/researchers
 *
 *   GET    /researchers                 search active profiles (?q=, staff_id, primary_institution, department, orcid, expand=)
 *   GET    /researchers/<id>            one profile (drafts: owner/drd/admin only)
 *   GET    /researchers/by-username/<username>   the same, looked up by the owner's username
 *   POST   /researchers                 create the caller's own profile (one per user)
 *   PUT    /researchers/<id>            update (owner/admin)
 *   DELETE /researchers/<id>            delete (owner/admin)
 *   POST   /researchers/<id>/photo      upload profile photo, multipart field "attachment" (owner/admin)
 */
class ResearcherController extends BaseController
{
    public $modelClass = Researcher::class;

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = fn() => (new ResearcherSearch())->search(Yii::$app->request->queryParams);
        $actions['view']['findModel'] = fn($id) => $this->findModel($id);
        $actions['update']['findModel'] = fn($id) => $this->findModel($id);
        $actions['delete']['findModel'] = fn($id) => $this->findModel($id);

        // Implemented below: the owner always comes from the token, never the request body.
        unset($actions['create']);

        return $actions;
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['upload-photo'] = ['POST'];
        $verbs['view-by-username'] = ['GET', 'HEAD'];

        return $verbs;
    }

    /**
     * GET /apiv1/researchers/by-username/<username>
     *
     * Same representation, visibility rules and ?expand= support as GET /researchers/<id>.
     */
    public function actionViewByUsername(string $username): Researcher
    {
        $user = User::findOne(['username' => $username]);
        $model = $user !== null ? Researcher::findOne(['user_id' => $user->id]) : null;
        if ($model === null) {
            throw new NotFoundHttpException('Researcher not found.');
        }

        $this->checkAccess('view', $model);   // drafts: 404 unless owner/drd/admin

        return $model;
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can(RbacHelper::PERM_CREATE)) {
            throw new ForbiddenHttpException('You are not allowed to create a biosketch.');
        }

        $userId = (int) Yii::$app->user->id;
        if (Researcher::find()->where(['user_id' => $userId])->exists()) {
            throw new ConflictHttpException('You already have a researcher profile.');
        }

        $model = new Researcher();
        $model->load(Yii::$app->request->bodyParams, '');
        $model->user_id = $userId;

        if ($model->save()) {
            Yii::$app->response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the profile.');
        }

        return $model;   // validation errors are serialized as 422
    }

    public function actionUploadPhoto($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess('upload-photo', $model);

        $model->attachment = UploadedFile::getInstanceByName('attachment');
        if ($model->attachment === null) {
            throw new BadRequestHttpException('Send the image as multipart field "attachment".');
        }
        if (!$model->validate(['attachment'])) {
            return $model;   // 422 with the attachment error
        }

        $oldPhoto = $model->profile_photo;
        $model->profile_photo = ProfilePhotoStorage::save($model, $model->attachment);
        $model->save(false, ['profile_photo', 'updated_at']);
        ProfilePhotoStorage::delete($oldPhoto);

        return $model;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (!$model instanceof Researcher) {
            return;   // index/create: scoping handled by the search model / actionCreate
        }

        if ($action === 'view') {
            if (!$this->canRead($model)) {
                // Don't reveal that a draft profile exists.
                throw new NotFoundHttpException('Researcher not found.');
            }
            return;
        }

        $permission = $action === 'delete' ? RbacHelper::PERM_DELETE : RbacHelper::PERM_UPDATE;
        if (!$this->can($permission, $model)) {
            throw new ForbiddenHttpException('You do not own this profile.');
        }
    }

    protected function findModel($id): Researcher
    {
        return Researcher::findOne((int) $id)
            ?? throw new NotFoundHttpException('Researcher not found.');
    }
}
