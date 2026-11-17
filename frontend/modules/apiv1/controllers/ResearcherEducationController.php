<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use frontend\models\ResearcherEducation;
use frontend\modules\apiv1\components\ChildController;

/**
 * /apiv1/researchers/<researcher_id>/educations
 */
class ResearcherEducationController extends ChildController
{
    public $modelClass = ResearcherEducation::class;
}
