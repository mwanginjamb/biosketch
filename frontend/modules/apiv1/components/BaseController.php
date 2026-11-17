<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\components;

use common\components\RbacHelper;
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

    /**
     * RBAC check against a biosketch. Owners pass the *Own* permissions via the
     * isBiosketchOwner rule; drd/admin hold the unrestricted ones.
     */
    protected function can(string $permission, Researcher $researcher): bool
    {
        return Yii::$app->user->can($permission, ['researcher' => $researcher]);
    }

    /** Active profiles are readable by any API user; drafts need the viewBiosketch permission (owner, drd, admin). */
    protected function canRead(Researcher $researcher): bool
    {
        return (int) $researcher->status === 1 || $this->can(RbacHelper::PERM_VIEW, $researcher);
    }
}
