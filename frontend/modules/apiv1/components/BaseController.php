<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\components;

use frontend\models\Researcher;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Base for all authenticated API v1 resource controllers.
 */
abstract class BaseController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => ['application/json' => Response::FORMAT_JSON],
        ];
        // CORS must run before authentication so preflight requests succeed.
        $behaviors['corsFilter'] = ['class' => Cors::class];

        $authMethods = [HttpBearerAuth::class];
        if (YII_ENV_DEV) {
            // ?access-token=... convenience for local testing only (tokens in URLs end up in logs).
            $authMethods[] = QueryParamAuth::class;
        }
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => $authMethods,
            'except' => ['options'],
        ];

        return $behaviors;
    }

    protected function isAdmin(): bool
    {
        return Yii::$app->user->can('admin');
    }

    protected function ownsOrAdmin(Researcher $researcher): bool
    {
        return (int) $researcher->user_id === (int) Yii::$app->user->id || $this->isAdmin();
    }

    /** Active profiles are readable by any API user; drafts only by their owner or an admin. */
    protected function canRead(Researcher $researcher): bool
    {
        return (int) $researcher->status === 1 || $this->ownsOrAdmin($researcher);
    }
}
