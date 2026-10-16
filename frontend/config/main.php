<?php
declare(strict_types=1);
use kartik\mpdf\Pdf;
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php',
);

return [
    'id' => 'app-frontend',
    'name' => env('APP_NAME', 'Researcher Bio Sketch App'),
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'parsers' => ['application/json' => 'yii\web\JsonParser']
        ],
        'user' => [
            'identityClass' => \common\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            'class' => 'yii\web\Session',
            'timeout' => 3600, // 60 min
            'useCookies' => true,
            'cookieParams' => [
                'httponly' => true,
                'lifetime' => 3600, // one hour
                'samesite' => YII_ENV === 'dev' ? 'Lax' : 'None', // Allow cross-origin requests only in dev
                'secure' => YII_ENV === 'dev' ? false : true,     // Ensure secure (HTTPS) transmission only on dev
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'pdf' => [
            'class' => Pdf::class,
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_LEGAL,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_STRING
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['apiv1/researchers' => 'apiv1/researcher'],
                    'extraPatterns' => ['POST {id}/photo' => 'upload-photo']
                ],
                // nested resources: researcher_id is passed to the child controller
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['apiv1/researchers/<researcher_id:\d+>/educations' => 'apiv1/researcher-education']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['apiv1/researchers/<researcher_id:\d+>/identifiers' => 'apiv1/researcher-identifier']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['apiv1/researchers/<researcher_id:\d+>/media' => 'apiv1/researcher-media']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['apiv1/researchers/<researcher_id:\d+>/grants' => 'apiv1/researcher-grant']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['apiv1/researchers/<researcher_id:\d+>/publications' => 'apiv1/publications']
                ],
            ],
        ],

    ],
    'modules' => [
        'apiv1' => [
            'class' => \frontend\modules\apiv1\Module::class,
        ],
    ],
    'params' => $params,
];
