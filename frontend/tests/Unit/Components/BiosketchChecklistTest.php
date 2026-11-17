<?php

declare(strict_types=1);

namespace frontend\tests\Unit\Components;

use Codeception\Test\Unit;
use common\models\User;
use frontend\components\BiosketchChecklist;
use frontend\models\Publications;
use frontend\models\Researcher;
use frontend\models\ResearcherEducation;
use frontend\models\ResearcherGrant;
use frontend\models\ResearcherIdentifier;
use frontend\models\ResearcherStatement;
use frontend\tests\Support\UnitTester;
use Yii;

final class BiosketchChecklistTest extends Unit
{
    protected UnitTester $tester;

    private function haveResearcher(array $attributes = []): Researcher
    {
        $userId = $this->tester->haveRecord(User::class, [
            'username' => 'checklist_' . Yii::$app->security->generateRandomString(6),
            'email' => Yii::$app->security->generateRandomString(8) . '@example.com',
            'auth_key' => 'x',
            'password_hash' => 'x',
            'status' => User::STATUS_ACTIVE,
        ]);
        $id = $this->tester->haveRecord(Researcher::class, $attributes + ['user_id' => $userId, 'full_name' => 'Test']);

        return Researcher::findOne($id);
    }

    private function complete(Researcher $r): void
    {
        $r->updateAttributes([
            'status' => 1,
            'profile_photo' => '/uploads/researchers/profile/x.png',
            'primary_institution' => 'KEMRI',
            'department' => 'SSRC',
            'role_title' => 'Scientist',
            'email' => 'a@b.org',
            'orcid' => '0000-0002-1825-0097',
            'research_tags' => 'Malaria',
            'major_breakthrough' => 'Something',
        ]);
        foreach ([
            ResearcherStatement::class => ['content' => 'Statement'],
            ResearcherEducation::class => ['degree' => 'PhD', 'institution_name' => 'UoN'],
            Publications::class => ['title' => 'Paper'],
            ResearcherIdentifier::class => ['identifier_type' => 'Scopus', 'identifier_value' => '1'],
            ResearcherGrant::class => ['title' => 'Grant'],
        ] as $class => $data) {
            $this->tester->haveRecord($class, $data + ['researcher_id' => $r->id]);
        }
    }

    private function keys(array $items): array
    {
        return array_column($items, 'key');
    }

    public function testEmptyDraftProfileListsMissingSections(): void
    {
        $r = $this->haveResearcher();
        $keys = $this->keys(BiosketchChecklist::forResearcher($r, time()));

        verify($keys)->equalsCanonicalizing([
            'draft', 'missing-photo', 'missing-personal', 'missing-statement', 'missing-education',
            'missing-publications', 'missing-identifiers', 'missing-grants', 'missing-accomplishments',
        ]);
    }

    public function testMissingPersonalFieldsAreNamed(): void
    {
        $r = $this->haveResearcher(['primary_institution' => 'KEMRI', 'department' => 'SSRC', 'role_title' => 'x', 'email' => 'a@b.org']);
        $items = BiosketchChecklist::forResearcher($r, time());
        $personal = array_values(array_filter($items, fn($i) => $i['key'] === 'missing-personal'))[0];

        verify($personal['detail'])->equals('Missing: ORCID, research areas.');
        verify($personal['url'])->equals(['/researcher/update', 'id' => $r->id, '#' => 'section-personal']);
    }

    public function testCompleteRecentProfileHasNoItems(): void
    {
        $r = $this->haveResearcher();
        $this->complete($r);

        verify(BiosketchChecklist::forResearcher(Researcher::findOne($r->id), time()))->empty();
    }

    public function testSectionsNotUpdatedForAYearAreFlagged(): void
    {
        $r = $this->haveResearcher();
        $this->complete($r);
        $r = Researcher::findOne($r->id);

        $nextYear = time() + 400 * 86400;
        $keys = $this->keys(BiosketchChecklist::forResearcher($r, $nextYear));
        verify($keys)->equalsCanonicalizing([
            'stale-personal', 'stale-statement', 'stale-education', 'stale-publications', 'stale-identifiers', 'stale-grants',
        ]);

        // Updating one section clears its reminder.
        $edu = $r->researcherEducations[0];
        $edu->updateAttributes(['updated_at' => $nextYear]);
        $keys = $this->keys(BiosketchChecklist::forResearcher(Researcher::findOne($r->id), $nextYear));
        verify($keys)->arrayNotContains('stale-education');
        verify($keys)->arrayContains('stale-publications');
    }

    public function testSectionTimestampsAreRecorded(): void
    {
        $r = $this->haveResearcher();
        $edu = new ResearcherEducation(['researcher_id' => $r->id, 'degree' => 'PhD', 'institution_name' => 'UoN']);
        verify($edu->save())->true();
        verify($edu->created_at)->notEmpty();
        verify($edu->updated_at)->notEmpty();
    }
}
