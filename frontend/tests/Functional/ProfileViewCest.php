<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\components\RbacHelper;
use common\models\User;
use frontend\models\Researcher;
use frontend\models\ResearcherGrant;
use frontend\models\ResearcherIdentifier;
use frontend\models\ResearcherStatement;
use frontend\tests\Support\FunctionalTester;
use Yii;

/**
 * Profile page content and header navigation (developer-notes #5, #6, #7, #8, #12).
 */
final class ProfileViewCest
{
    private int $userId;
    private int $profileId;

    public function _before(FunctionalTester $I): void
    {
        $this->userId = $I->haveRecord(User::class, [
            'username' => 'profile_owner',
            'email' => 'profile_owner@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password_0'),
            'status' => User::STATUS_ACTIVE,
        ]);
        RbacHelper::assignDefaultRole($this->userId);

        $this->profileId = $I->haveRecord(Researcher::class, [
            'user_id' => $this->userId,
            'title' => 'Dr.',
            'full_name' => 'Faith Mwangi',
            'primary_institution' => 'Kenya Medical Research Institute',
            'era_commons_id' => 'FNMWANGI',
            'orcid' => '0000-0002-1825-0097',
        ]);
        $I->haveRecord(ResearcherStatement::class, [
            'researcher_id' => $this->profileId,
            'statement_type' => 'personal_statement',
            'content' => 'My research focuses on antimalarial drug resistance.',
        ]);
        $I->haveRecord(ResearcherIdentifier::class, [
            'researcher_id' => $this->profileId,
            'identifier_type' => 'eRA Commons',
            'identifier_value' => 'FNMWANGI',
            'verification_status' => ResearcherIdentifier::VERIFICATION_PENDING,
        ]);
        $I->haveRecord(ResearcherGrant::class, [
            'researcher_id' => $this->profileId,
            'title' => 'Completed Wellcome grant',
            'funding_agency_id' => 33,
            'status_id' => 2,
            'role_id' => 2,
        ]);
        $I->haveRecord(ResearcherGrant::class, [
            'researcher_id' => $this->profileId,
            'title' => 'Active Gates grant',
            'funding_agency_id' => 32,
            'status_id' => 1,
            'role_id' => 1,
        ]);
    }

    public function profileShowsAllSavedSections(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->userId);
        $I->amOnRoute('researcher/view', ['id' => $this->profileId]);

        $I->seeInTitle('Dr. Faith Mwangi');
        $I->see('Kenya Medical Research Institute');
        $I->see('Personal Statement', 'h3');
        $I->see('My research focuses on antimalarial drug resistance.');
        $I->see('eRA Commons ID');
        $I->see('Pending');   // identifier verification label

        // Grants: one neutral heading, funder and status shown, active grants first.
        $I->see('Grants & Funding', 'h3');
        $I->dontSee('Ongoing Grants');
        $I->see('Funder: Wellcome Trust');
        $I->see('Completed');
        $source = $I->grabPageSource();
        verify(strpos($source, 'Active Gates grant'))->lessThan(strpos($source, 'Completed Wellcome grant'));

        // "Research Highlights" heading appears once.
        verify(substr_count($source, 'Research Highlights'))->equals(1);
    }

    public function pdfReportRendersWithIdentifiersGrantsAndTags(FunctionalTester $I): void
    {
        Researcher::updateAll(['research_tags' => 'Malaria Epidemiology, Antimalarial Drug Resistance'], ['id' => $this->profileId]);

        $I->amLoggedInAs($this->userId);

        // The PDF itself, inline.
        $I->amOnRoute('researcher/report-pdf', ['id' => $this->profileId]);
        $I->seeResponseCodeIs(200);
        verify($I->grabResponseHeader('Content-Type'))->stringStartsWith('application/pdf');
        verify($I->grabResponseHeader('Content-Disposition'))->stringStartsWith('inline;');
        verify(substr($I->grabPageSource(), 0, 5))->equals('%PDF-');

        // Download: attachment with a readable filename.
        $I->amOnRoute('researcher/report-pdf', ['id' => $this->profileId, 'download' => 1]);
        $disposition = $I->grabResponseHeader('Content-Disposition');
        verify($disposition)->stringStartsWith('attachment;');
        verify($disposition)->stringContainsString('BioSketch-faith-mwangi.pdf');
    }

    public function reportPageHasNavigationAndEmbedsThePdf(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->userId);
        $I->amOnRoute('researcher/report', ['id' => $this->profileId]);
        $I->seeResponseCodeIs(200);

        // Rendered inside the app layout, with ways back.
        $I->seeElement('details[data-account-menu]');
        $I->seeLink('Back to my BioSketch');
        $I->seeLink('Home');
        $I->seeLink('Download PDF');
        $I->dontSeeLink('BioSketch List');   // researchers only see their own

        $src = $I->grabAttributeFrom('iframe', 'src');
        verify($src)->stringContainsString('researcher/report-pdf');
        verify($src)->stringNotContainsString('data:');
    }

    public function headerLinksToProfileAndOffersAccountMenu(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->userId);
        $I->amOnRoute('researcher/view', ['id' => $this->profileId]);

        // "Public Profile" goes to the profile page, not the PDF.
        $href = $I->grabAttributeFrom('//a[normalize-space(.)="Public Profile"]', 'href');
        verify($href)->stringEndsWith('/researcher/profile_owner');   // readable username URL
        verify($href)->stringNotContainsString('report');

        // Account menu with sign-out as a POST form.
        $I->seeElement('details[data-account-menu]');
        $I->seeElement('form[action$="site/logout"][method="post"] button');
        $I->see('My BioSketch');
    }

    public function signOutFromTheAccountMenuWorks(FunctionalTester $I): void
    {
        $I->amLoggedInAs($this->userId);
        $I->amOnRoute('researcher/view', ['id' => $this->profileId]);
        $I->submitForm('form[action$="site/logout"]', []);
        $I->amOnRoute('researcher/view', ['id' => $this->profileId]);
        $I->seeInCurrentUrl('login');
    }
}
