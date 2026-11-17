<?php

return [
    'container' => [
        'singletons' => [
            \yii\mail\MailerInterface::class => [
                'class' => \yii\symfonymailer\Mailer::class,
                'viewPath' => '@common/mail',
                'useFileTransport' => false,
                // SMTP settings come from .env (SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD).
                // Credentials are URL-encoded so values containing "@" or ":" are safe in the DSN.
                'transport' => [
                    'dsn' => 'smtp://' . rawurlencode((string) env('SMTP_USERNAME', ''))
                        . ':' . rawurlencode((string) env('SMTP_PASSWORD', ''))
                        . '@' . env('SMTP_HOST', 'localhost') . ':' . env('SMTP_PORT', 587),
                ],
            ],
        ],
    ],
    'components' => [
        // 'db' is configured in common/config/main.php from the DB_* variables in .env;
        // do not override it here.
        'mailer' => \yii\mail\MailerInterface::class,
    ],
];
