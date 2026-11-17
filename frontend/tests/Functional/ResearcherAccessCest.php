<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\models\User;
use frontend\models\Researcher;
use frontend\tests\Support\FunctionalTester;
use Yii;
use yii\helpers\Url;

/**
 * Researcher profiles are private to their owner; admins can access all of them.
 */
final class ResearcherAccessCest
{
    private int $ownerId;
    private int $otherId;
    private int $adminId;
    private int $ownProfileId;
    private int $otherProfileId;

    public function _before(FunctionalTester $I): void
    {
        $this->ownerId = $this->haveUser($I, 'owner');
        $this->otherId = $this->haveUser($I, 'other');
        $this->adminId = $this->haveUser($I, 'boss');

        $this->ownProfileId = $I->haveRecord(Researcher::class, [
            'user_id' => $this->ownerId,
            'full_name' => 'Owner Researcher',
        ]);
        $this->otherProfileId = $I->haveRecord(Researcher::class, [
            'user_id' => $this->otherId,
            'full_name' => 'Other Researcher',
        ]);

        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole('admin'), $this->adminId);
    }

    private function haveUser(FunctionalTester $I, string $username): int
    {
        return $I->haveRecord(User::class, [
            'username' => $username,
            'email' => $username . '@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password_0'),
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function guestIsSentToLoginForProfileAndReport(FunctionalTester $I): void
    {
        $I->amOnRoute('researcher/view', ['id' => $this->ownProfileId]);
        $I->seeInCurrentUrl('login');

        $I->amOnRoute('researcher/report', ['id' => $this->ownProfileId]);
        $I->seeInCurrentUrl('login');
    }

    public function ownerCanViewOwnProfile(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnRoute('researcher/view', ['id' => $this->ownProfileId]);
        $I->seeResponseCodeIs(200);
        $I->see('Owner Researcher');
    }

    public function userCannotAccessSomeoneElsesProfile(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);

        foreach (['researcher/view', 'researcher/update', 'researcher/report'] as $route) {
            $I->amOnRoute($route, ['id' => $this->otherProfileId]);
            $I->seeResponseCodeIs(403);
        }

        // Valid CSRF token so the request reaches the ownership check.
        [$csrfParam, $csrfToken] = $I->createAndSetCsrfCookie('test-csrf-token');
        $I->sendAjaxPostRequest(
            Url::to(['researcher/delete', 'id' => $this->otherProfileId]),
            [$csrfParam => $csrfToken],
        );
        $I->seeResponseCodeIs(403);
        $I->seeRecord(Researcher::class, ['id' => $this->otherProfileId]);
    }

    public function missingProfileIs404(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnRoute('researcher/view', ['id' => 999999]);
        $I->seeResponseCodeIs(404);
    }

    public function adminCanViewAnyProfile(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $I->amOnRoute('researcher/view', ['id' => $this->otherProfileId]);
        $I->seeResponseCodeIs(200);
        $I->see('Other Researcher');
    }

    public function employeeLookupIsForGuestsOnly(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnRoute('site/lookup-employee', ['employeeNumber' => '90296']);
        $I->seeResponseCodeIs(403);
    }
}
