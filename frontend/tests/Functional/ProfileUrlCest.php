<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\components\RbacHelper;
use common\models\User;
use frontend\controllers\ResearcherController;
use frontend\models\Researcher;
use frontend\tests\Support\FunctionalTester;
use ReflectionClass;
use ReflectionMethod;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * /researcher/<username> is an alias of /researcher/view?id=<id>.
 */
final class ProfileUrlCest
{
    private const BASE = '/index-test.php/researcher/';

    private int $ownerId;
    private int $otherId;
    private int $profileId;

    public function _before(FunctionalTester $I): void
    {
        $this->ownerId = $this->haveUser($I, 'jane.doe', [RbacHelper::ROLE_RESEARCHER]);
        $this->otherId = $this->haveUser($I, 'other.person', [RbacHelper::ROLE_RESEARCHER]);
        $this->profileId = $I->haveRecord(Researcher::class, ['user_id' => $this->ownerId, 'full_name' => 'Jane Doe']);
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

    public function ownerOpensProfileByUsername(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnPage(self::BASE . 'jane.doe');
        $I->seeResponseCodeIs(200);
        $I->see('Jane Doe', 'h2');
        $I->seeInTitle('Jane Doe');
    }

    public function sameAccessRulesAsViewById(FunctionalTester $I): void
    {
        // Another researcher: forbidden, exactly like view?id=.
        $I->amLoggedInAs($this->otherId);
        $I->amOnPage(self::BASE . 'jane.doe');
        $I->seeResponseCodeIs(403);

        // drd can view any biosketch.
        $drdId = $this->haveUser($I, 'drd.viewer', [RbacHelper::ROLE_DRD]);
        $I->amLoggedInAs($drdId);
        $I->amOnPage(self::BASE . 'jane.doe');
        $I->seeResponseCodeIs(200);
    }

    public function unknownUserOrUserWithoutBiosketchIs404(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnPage(self::BASE . 'no.such.user');
        $I->seeResponseCodeIs(404);

        $I->amOnPage(self::BASE . 'other.person');   // exists, has no biosketch
        $I->seeResponseCodeIs(404);
    }

    public function guestIsSentToLogin(FunctionalTester $I): void
    {
        $I->amOnPage(self::BASE . 'jane.doe');
        $I->seeInCurrentUrl('login');
    }

    public function controllerActionUrlsStillWork(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);

        $I->amOnPage(self::BASE . 'view?id=' . $this->profileId);
        $I->seeResponseCodeIs(200);
        $I->amOnPage(self::BASE . 'update?id=' . $this->profileId);
        $I->seeResponseCodeIs(200);
        $I->amOnPage(self::BASE . 'report?id=' . $this->profileId);
        $I->seeResponseCodeIs(200);
        $I->amOnPage(self::BASE . 'create');
        $I->seeResponseCodeIs(200);
    }

    public function appLinksUseTheUsernameUrl(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->ownerId);
        $I->amOnPage(self::BASE . 'view?id=' . $this->profileId);

        $myBiosketch = $I->grabAttributeFrom('//a[contains(normalize-space(.), "My BioSketch")]', 'href');
        verify($myBiosketch)->stringEndsWith('/researcher/jane.doe');

        // Reserved names are never generated as profile URLs.
        verify(Url::to(['/researcher/profile', 'username' => 'create']))->stringNotContainsString('/researcher/create');
        verify(Url::to(['/researcher/profile', 'username' => 'Report-PDF']))->stringNotContainsString('/researcher/Report-PDF');
    }

    public function everyControllerActionIsReserved(FunctionalTester $I): void
    {
        $actions = [];
        foreach ((new ReflectionClass(ResearcherController::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^action([A-Z]\w*)$/', $method->getName(), $m)) {
                $actions[] = Inflector::camel2id($m[1]);
            }
        }

        verify(array_diff($actions, ResearcherController::RESERVED_SLUGS))->empty();
    }
}
