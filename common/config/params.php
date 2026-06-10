<?php

declare(strict_types=1);

return [
    'adminEmail' => env('SMTP_USERNAME'),
    'supportEmail' => env('SMTP_USERNAME'),
    'senderEmail' => env('SMTP_USERNAME'),
    'senderName' => 'KEMRI BIO-SKETCH Mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
];
