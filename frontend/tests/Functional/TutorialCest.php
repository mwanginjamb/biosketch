<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use frontend\controllers\SiteController;
use frontend\tests\Support\FunctionalTester;
use Yii;

/**
 * /tutorial renders the instructional video player.
 */
final class TutorialCest
{
    private string $file;
    private ?string $backup = null;

    public function _before(FunctionalTester $I): void
    {
        $this->file = Yii::getAlias('@frontend/web/' . SiteController::TUTORIAL_VIDEO);
        // Don't disturb a real video sitting in the working copy.
        if (is_file($this->file)) {
            $this->backup = $this->file . '.bak-test';
            rename($this->file, $this->backup);
        }
    }

    public function _after(FunctionalTester $I): void
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }
        if ($this->backup !== null) {
            rename($this->backup, $this->file);
        }
    }

    public function homeLinksToTutorial(FunctionalTester $I): void
    {
        $I->amOnPage('/index-test.php/site/home');
        $I->seeElement('a[href$="/tutorial"]');
    }

    public function brandLinksHomeWithLogo(FunctionalTester $I): void
    {
        foreach (['/index-test.php/site/login', '/index-test.php/tutorial'] as $page) {   // guest + plain layouts
            $I->amOnPage($page);
            $I->seeElement('a[href$="/site/home"] img[src$="/icons/icon-144x144.png"]');
        }
    }

    public function loginLinksToTutorial(FunctionalTester $I): void
    {
        $I->amOnPage('/index-test.php/site/login');
        $I->seeLink('New here? Watch the 3-minute tutorial');
    }

    public function showsPlayerWhenVideoExists(FunctionalTester $I): void
    {
        @mkdir(dirname($this->file), 0775, true);
        file_put_contents($this->file, 'not-really-an-mp4');

        $I->amOnPage('/index-test.php/tutorial');
        $I->seeResponseCodeIs(200);
        $I->seeElement('video source[type="video/mp4"]');
        $I->seeInSource('media/biosketch-tutorial.mp4?v=');
        $I->see('Sign up');   // guest call-to-action
    }

    public function showsNoticeWhenVideoMissing(FunctionalTester $I): void
    {
        $I->amOnPage('/index-test.php/tutorial');
        $I->seeResponseCodeIs(200);
        $I->dontSeeElement('video');
        $I->see('not available yet');
    }
}
