<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\components\RbacHelper;
use common\models\User;
use frontend\models\Researcher;
use frontend\tests\Support\FunctionalTester;
use Yii;

/**
 * The header bell lists biosketch sections a researcher still needs to complete.
 */
final class NotificationBellCest
{
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

    public function researcherWithoutBiosketchIsPromptedToCreateOne(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->haveUser($I, 'bell_new', [RbacHelper::ROLE_RESEARCHER]));
        $I->amOnRoute('site/home');

        $I->seeElement('details[data-notification-menu]');
        $I->see('1', '[data-notification-count]');
        $I->see('Create your BioSketch', '[data-notification="create-biosketch"]');
    }

    public function researcherSeesMissingSectionsLinkingToTheForm(FunctionalTester $I): void
    {
        $userId = $this->haveUser($I, 'bell_draft', [RbacHelper::ROLE_RESEARCHER]);
        $profileId = $I->haveRecord(Researcher::class, ['user_id' => $userId, 'full_name' => 'Draft Person']);

        $I->amLoggedInAs($userId);
        $I->amOnRoute('researcher/view', ['id' => $profileId]);

        $I->see('9', '[data-notification-count]');
        $I->see('Add your research statement', '[data-notification="missing-statement"]');
        $href = $I->grabAttributeFrom('[data-notification="missing-statement"] a', 'href');
        verify($href)->stringContainsString('researcher/update');
        verify($href)->stringEndsWith('#section-statement');

        // The form has the anchor the notification points to.
        $I->amOnRoute('researcher/update', ['id' => $profileId]);
        $I->seeElement('#section-statement');
        $I->seeElement('#section-education');
    }

    public function drdWithoutResearcherRoleHasNoBell(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->haveUser($I, 'bell_drd', [RbacHelper::ROLE_DRD]));
        $I->amOnRoute('site/home');
        $I->dontSeeElement('details[data-notification-menu]');
    }
}
