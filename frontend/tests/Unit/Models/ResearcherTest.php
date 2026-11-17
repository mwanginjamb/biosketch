<?php

declare(strict_types=1);

namespace frontend\tests\Unit\Models;

use Codeception\Test\Unit;
use frontend\models\Researcher;
use frontend\tests\Support\UnitTester;

final class ResearcherTest extends Unit
{
    protected UnitTester $tester;

    public function testServerControlledAttributesAreNotMassAssignable(): void
    {
        $model = new Researcher();
        $model->user_id = 5;
        $model->profile_photo = '/uploads/researchers/profile/researcher_1_abc.png';

        $model->load([
            'Researcher' => [
                'full_name' => 'Jane Doe',
                'user_id' => 99,
                'profile_photo' => '/../../common/config/main-local.php',
                'created_by' => 99,
                'version' => 42,
            ],
        ]);

        verify($model->full_name)->equals('Jane Doe');
        verify($model->user_id)->equals(5);
        verify($model->profile_photo)->equals('/uploads/researchers/profile/researcher_1_abc.png');
        verify($model->created_by)->null();
        verify($model->version)->null();
    }

    public function testStatusRemainsEditable(): void
    {
        $model = new Researcher();
        $model->load(['Researcher' => ['status' => 1]]);

        verify($model->status)->equals(1);
    }
}
