<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\fixtures\UserFixture;
use common\models\User;
use frontend\tests\Support\FunctionalTester;

final class ResendVerificationEmailCest
{
    protected string $formId = '#resend-verification-email-form';
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function _before(FunctionalTester $I): void
    {
        $I->amOnRoute('/site/resend-verification-email');
    }

    protected function formParams(string $email): array
    {
        return [
            'ResendVerificationEmailForm[email]' => $email,
        ];
    }

    public function checkSendSuccessfully(FunctionalTester $I): void
    {
        $I->submitForm($this->formId, $this->formParams('test@mail.com'));
        $I->canSeeEmailIsSent();
        $I->seeRecord(
            User::class, 
            [
                'email' => 'test@mail.com',
                'username' => 'test.test',
                'status' => User::STATUS_INACTIVE,
            ],
        );
        $I->see('Check your email for further instructions.');
    }
}
