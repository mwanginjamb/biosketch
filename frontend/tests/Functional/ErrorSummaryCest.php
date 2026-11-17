<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use frontend\tests\Support\FunctionalTester;

/**
 * Form error summaries use the shared FormUi::errorSummaryOptions() alert.
 */
final class ErrorSummaryCest
{
    public function signupShowsStyledSummary(FunctionalTester $I): void
    {
        $I->amOnPage('/index-test.php/site/signup');
        $I->submitForm('#signup-form', []);

        $I->seeElement('#signup-form .alert-danger.bg-error-container[role="alert"]');
        $I->see('Please fix the following:', '#signup-form .alert-danger');
        $I->see('Username cannot be blank.', '#signup-form .alert-danger li');
    }
}
