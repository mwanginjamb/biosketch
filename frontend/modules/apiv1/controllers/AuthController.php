<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use common\components\Throttle;
use common\models\User;
use common\models\UserApiToken;
use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\rest\OptionsAction;
use yii\web\BadRequestHttpException;
use yii\web\TooManyRequestsHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * API token issuance.
 *
 *   POST   /apiv1/auth/token   {"username": "...", "password": "...", "name": "optional label"}
 *                              -> 201 {"token": "...", "expires_at": 1767225600}
 *   DELETE /apiv1/auth/token   revokes the bearer token used for the request -> 204
 */
class AuthController extends Controller
{
    /** Token requests allowed per client IP per window. */
    private const LOGIN_LIMIT = 10;
    private const LOGIN_WINDOW = 600;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = ['class' => Cors::class];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'token' => ['POST'],
                'revoke' => ['DELETE'],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
                'collectionOptions' => ['POST', 'DELETE', 'OPTIONS'],
            ],
        ];
    }

    public function actionToken()
    {
        if (!Throttle::hit('api-token', (string) Yii::$app->request->userIP, self::LOGIN_LIMIT, self::LOGIN_WINDOW)) {
            throw new TooManyRequestsHttpException('Too many token requests. Try again later.');
        }

        $body = Yii::$app->request->bodyParams;
        $username = trim((string) ($body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');
        if ($username === '' || $password === '') {
            throw new BadRequestHttpException('username and password are required.');
        }

        // Only active (email-verified) accounts can obtain tokens.
        $user = User::findByUsername($username);
        if ($user === null || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Incorrect username or password.');
        }

        [$token, $plain] = UserApiToken::issue($user, isset($body['name']) ? (string) $body['name'] : null);

        Yii::$app->response->setStatusCode(201);

        return [
            'token' => $plain,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at,
        ];
    }

    public function actionRevoke()
    {
        $header = (string) Yii::$app->request->headers->get('Authorization', '');
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            throw new UnauthorizedHttpException('Send the token to revoke as a Bearer Authorization header.');
        }

        $token = UserApiToken::findActive(trim($matches[1]));
        if ($token === null) {
            throw new UnauthorizedHttpException('Invalid or expired token.');
        }

        $token->delete();
        Yii::$app->response->setStatusCode(204);

        return null;
    }
}
