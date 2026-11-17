<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\components\RbacHelper;
use common\models\User;
use frontend\models\Researcher;
use frontend\tests\Support\FunctionalTester;
use Yii;
use yii\helpers\Url;

/**
 * Biosketch access by role:
 *   researcher: own biosketch only; drd: view list + any biosketch (read-only); admin: everything.
 */
final class ResearcherAccessCest
{
    private int $ownerId;
    private int $otherId;
    private int $adminId;
    private int $drdId;
    private int $ownProfileId;
    private int $otherProfileId;

    public function _before(FunctionalTester $I): void
    {
        $this->ownerId = $this->haveUser($I, 'owner', [RbacHelper::ROLE_RESEARCHER]);
        $this->otherId = $this->haveUser($I, 'other', [RbacHelper::ROLE_RESEARCHER]);
        $this->adminId = $this->haveUser($I, 'boss', [RbacHelper::ROLE_ADMIN]);
        $this->drdId = $this->haveUser($I, 'drd_officer', [RbacHelper::ROLE_DRD]);

        $this->ownProfileId = $I->haveRecord(Researcher::class, [
            'user_id' => $this->ownerId,
            'full_name' => 'Owner Researcher',
        ]);
        $this->otherProfileId = $I->haveRecord(Researcher::class, [
            'user_id' => $this->otherId,
            'full_name' => 'Other Researcher',
        ]);
    }

    private function haveUser(FunctionalTester $I, string $username, array $roles): int
    {
        $id = $I->haveRecord(User::class, [
            'username' => $username,
            'email' => $username . '@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password_0'),
            'status' => User::STATUS_ACTIVE,
        ]);
        foreach ($roles as $role) {
            RbacHelper::assign($role, $id);
        }

        return $id;
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

        foreach (['researcher/view', 'researcher/update', 'researcher/report', 'researcher/report-pdf'] as $route) {
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

    public function adminCanEditAnyProfile(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->adminId);
        $I->amOnRoute('researcher/update', ['id' => $this->otherProfileId]);
        $I->seeResponseCodeIs(200);
    }

    public function drdSeesTheFullListReadOnly(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->drdId);
        $I->amOnRoute('site/index');
        $I->seeResponseCodeIs(200);
        $I->see('Owner Researcher');
        $I->see('Other Researcher');
        // View and report actions are offered, edit and create are not.
        $I->seeElement('a[title="View"]');
        $I->seeElement('a[title="View Report"]');
        $I->dontSeeElement('a[title="Edit"]');
        $I->dontSee('Create New');
    }

    public function drdCanViewAnyBiosketchButNotChangeIt(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->drdId);

        $I->amOnRoute('researcher/view', ['id' => $this->otherProfileId]);
        $I->seeResponseCodeIs(200);
        $I->see('Other Researcher');

        $I->amOnRoute('researcher/report', ['id' => $this->otherProfileId]);
        $I->seeResponseCodeIs(200);

        $I->amOnRoute('researcher/update', ['id' => $this->otherProfileId]);
        $I->seeResponseCodeIs(403);

        [$csrfParam, $csrfToken] = $I->createAndSetCsrfCookie('test-csrf-token');
        $I->sendAjaxPostRequest(Url::to(['researcher/delete', 'id' => $this->otherProfileId]), [$csrfParam => $csrfToken]);
        $I->seeResponseCodeIs(403);
        $I->seeRecord(Researcher::class, ['id' => $this->otherProfileId]);
    }

    public function researcherHomeIsTheirLandingPageNotTheList(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnRoute('site/index');
        $I->seeInCurrentUrl('home');
        $I->dontSee('Other Researcher');
    }

    public function accountWithoutRolesCannotUseBiosketches(FunctionalTester $I): void
    {
        $noRoleId = $this->haveUser($I, 'norole', []);
        $profileId = $I->haveRecord(Researcher::class, ['user_id' => $noRoleId, 'full_name' => 'No Role']);

        $I->amLoggedInAs($noRoleId);
        $I->amOnRoute('researcher/view', ['id' => $profileId]);
        $I->seeResponseCodeIs(403);
        $I->amOnRoute('researcher/create');
        $I->seeResponseCodeIs(403);
    }

    public function employeeLookupIsForGuestsOnly(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnRoute('site/lookup-employee', ['employeeNumber' => '90296']);
        $I->seeResponseCodeIs(403);
    }
}
