<?php

namespace frontend\modules\apiv1;

use Yii;
use yii\web\Response;

/**
 * REST API v1.
 *
 * Stateless: clients authenticate every request with `Authorization: Bearer <token>`,
 * obtained from POST /apiv1/auth/token.
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\apiv1\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // No sessions/cookies and no login redirects for API requests: 401 instead.
        Yii::$app->user->enableSession = false;
        Yii::$app->user->loginUrl = null;

        // Errors raised anywhere inside the module (404s, auth failures) are returned as JSON.
        Yii::$app->response->format = Response::FORMAT_JSON;
    }
}
