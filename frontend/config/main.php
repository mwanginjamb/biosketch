<?php
declare(strict_types=1);
use frontend\controllers\ResearcherController;
use kartik\mpdf\Pdf;

// /researcher/<username> profile URLs must not swallow the controller's own actions
// (researcher/create, researcher/update, ...): exclude them with a negative lookahead.
$reservedResearcherSlugs = implode('|', array_map(
    static fn(string $slug): string => preg_quote($slug, '#'),
    ResearcherController::RESERVED_SLUGS,
));

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
                'samesite' => 'Lax',             // same-site app: no need to send the session cookie cross-site
                'secure' => YII_ENV !== 'dev',   // HTTPS-only cookie everywhere except local dev
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
                // Readable profile URL: /researcher/<username> (same page as researcher/view?id=<id>)
                "researcher/<username:(?!(?i:{$reservedResearcherSlugs})$)[A-Za-z0-9._+\-]+>" => 'researcher/profile',

                // API token issuance / revocation
                'POST apiv1/auth/token' => 'apiv1/auth/token',
                'DELETE apiv1/auth/token' => 'apiv1/auth/revoke',
                'OPTIONS apiv1/auth/token' => 'apiv1/auth/options',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['apiv1/researchers' => 'apiv1/researcher'],
                    'extraPatterns' => ['POST {id}/photo' => 'upload-photo']
                ],
                // Nested resources: /apiv1/researchers/<researcher_id>/<resource>[/<id>].
                // The placeholder must live in `prefix`: rest\UrlRule matches controller keys
                // as literal substrings of the path, so a "<...>" inside a key never matches.
                [
                    'class' => 'yii\rest\UrlRule',
                    'prefix' => 'apiv1/researchers/<researcher_id:\d+>',
                    'pluralize' => false,
                    'controller' => [
                        'educations' => 'apiv1/researcher-education',
                        'identifiers' => 'apiv1/researcher-identifier',
                        'media' => 'apiv1/researcher-media',
                        'grants' => 'apiv1/researcher-grant',
                        'publications' => 'apiv1/publications',
                    ],
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
