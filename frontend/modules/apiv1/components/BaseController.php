<?php
// apiv1/modules/v1/components/BaseController.php
namespace api\modules\v1\components;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;

abstract class BaseController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $b = parent::behaviors();
        unset($b['authenticator']);

        $b['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => ['application/json' => Response::FORMAT_JSON],
        ];
        $b['corsFilter'] = ['class' => Cors::class];   // must come before authenticator
        $b['authenticator'] = [
            //'class' => HttpBearerAuth::class,
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
                YII_ENV === 'dev' ? QueryParamAuth::class : null,   // dev only, remove before production
            ],
            'except' => ['options'],
        ];
        return $b;
    }
}