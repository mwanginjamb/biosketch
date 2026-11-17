<?php

declare(strict_types=1);

return [
    'id' => 'app-common-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'user' => [
            'class' => \yii\web\User::class,
            'identityClass' => \common\models\User::class,
        ],
        // Test apps share the runtime file cache with dev; never cache RBAC data from the
        // test database, or the dev app could be served the test roles/permissions.
        'authManager' => [
            'cache' => null,
        ],
    ],
];
