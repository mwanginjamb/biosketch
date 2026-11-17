<?php

declare(strict_types=1);

namespace frontend\tests\Unit\Models;

use Codeception\Test\Unit;
use frontend\models\Publications;
use frontend\models\Researcher;
use frontend\models\ResearcherEducation;
use frontend\models\ResearcherGrant;
use frontend\models\ResearcherIdentifier;
use frontend\tests\Support\UnitTester;

/**
 * Display labels and format validation for biosketch data (developer-notes #1, #2, #14, #15, #17).
 */
final class BiosketchFieldsTest extends Unit
{
    protected UnitTester $tester;

    public function testIdentifierVerificationLabelsMatchTheFormOptions(): void
    {
        $expected = [
            ResearcherIdentifier::VERIFICATION_UNVERIFIED => 'Unverified',
            ResearcherIdentifier::VERIFICATION_PENDING => 'Pending',
            ResearcherIdentifier::VERIFICATION_VERIFIED => 'Verified',
            ResearcherIdentifier::VERIFICATION_REJECTED => 'Rejected',
        ];
        foreach ($expected as $status => $label) {
            $identifier = new ResearcherIdentifier(['verification_status' => $status]);
            verify($identifier->getVerificationLabel())->equals($label);
        }

        verify((new ResearcherIdentifier())->getVerificationLabel())->equals('Unverified');
    }

    public function testGrantLabels(): void
    {
        $grant = new ResearcherGrant(['funding_agency_id' => 33, 'status_id' => 2, 'role_id' => 1]);
        verify($grant->getFundingAgencyLabel())->equals('Wellcome Trust');
        verify($grant->getStatusLabel())->equals('Completed');
        verify($grant->getRoleLabel())->equals('Principal Investigator');
        verify($grant->isActive())->false();

        verify((new ResearcherGrant(['status_id' => 1]))->isActive())->true();
        verify((new ResearcherGrant())->getFundingAgencyLabel())->null();
    }

    public function testLocalOptionsAreAvailable(): void
    {
        verify(ResearcherGrant::getCurrencyOptions())->arrayHasKey('KES');
        verify(ResearcherGrant::getFundingAgencyOptions())->arrayContains('National Research Fund (NRF) Kenya');
        verify(ResearcherGrant::getFundingAgencyOptions()[33])->equals('Wellcome Trust');   // existing IDs unchanged
        foreach (['MBChB', 'MSc', 'BSc', 'MPH', 'Diploma', 'Ph.D.', 'B.S.'] as $degree) {
            verify(ResearcherEducation::getDegreeOptions())->arrayHasKey($degree);
        }
    }

    public function testOrcidValidation(): void
    {
        $model = new Researcher(['orcid' => '0000-0002-1825-0097']);
        verify($model->validate(['orcid']))->true();

        // URL form is accepted and normalised.
        $model = new Researcher(['orcid' => 'https://orcid.org/0000-0002-1825-0097']);
        verify($model->validate(['orcid']))->true();
        verify($model->orcid)->equals('0000-0002-1825-0097');

        // X check digit.
        verify((new Researcher(['orcid' => '0000-0002-9079-593X']))->validate(['orcid']))->true();

        $badChecksum = new Researcher(['orcid' => '0000-0002-1825-0098']);
        verify($badChecksum->validate(['orcid']))->false();
        verify($badChecksum->getFirstError('orcid'))->stringContainsString('check digit');

        verify((new Researcher(['orcid' => '1234']))->validate(['orcid']))->false();
        verify((new Researcher(['orcid' => '']))->validate(['orcid']))->true();
    }

    public function testDoiAndYearValidation(): void
    {
        foreach (['10.1186/s12936-023-04500-0', 'https://doi.org/10.5555/bmcid.2019.0733'] as $doi) {
            verify((new Publications(['doi' => $doi]))->validate(['doi']))->true();
        }
        verify((new Publications(['doi' => 'not a doi']))->validate(['doi']))->false();

        verify((new Publications(['publication_year' => 2023]))->validate(['publication_year']))->true();
        verify((new Publications(['publication_year' => 1900]))->validate(['publication_year']))->false();
        verify((new Publications(['publication_year' => (int) date('Y') + 5]))->validate(['publication_year']))->false();

        verify((new ResearcherEducation(['graduation_year' => 2015]))->validate(['graduation_year']))->true();
        verify((new ResearcherEducation(['graduation_year' => 1800]))->validate(['graduation_year']))->false();
    }

    public function testGrantAmountMustNotBeNegative(): void
    {
        verify((new ResearcherGrant(['amount' => 250000]))->validate(['amount']))->true();
        verify((new ResearcherGrant(['amount' => -5]))->validate(['amount']))->false();
        verify((new ResearcherGrant(['amount' => 'abc']))->validate(['amount']))->false();
    }

    public function testLabelWording(): void
    {
        verify((new Researcher())->getAttributeLabel('orcid'))->equals('ORCID');
        verify((new Researcher())->getAttributeLabel('era_commons_id'))->equals('eRA Commons ID');
        verify((new ResearcherGrant())->getAttributeLabel('status_id'))->equals('Status');
    }
}
