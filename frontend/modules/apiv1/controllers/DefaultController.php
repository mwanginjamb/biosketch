<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use yii\rest\Controller;

/**
 * GET /apiv1 — public summary of the API.
 */
class DefaultController extends Controller
{
    public function actionIndex(): array
    {
        return [
            'name' => 'BioSketch API',
            'version' => 'v1',
            'auth' => 'POST /apiv1/auth/token with {username, password}; send "Authorization: Bearer <token>"',
            'resources' => [
                'GET|POST /apiv1/researchers',
                'GET|PUT|PATCH|DELETE /apiv1/researchers/{id}',
                'GET /apiv1/researchers/by-username/{username}',
                'POST /apiv1/researchers/{id}/photo',
                'GET|POST /apiv1/researchers/{id}/{educations|identifiers|media|grants|publications}',
                'GET|PUT|PATCH|DELETE /apiv1/researchers/{id}/{resource}/{child_id}',
            ],
        ];
    }
}
