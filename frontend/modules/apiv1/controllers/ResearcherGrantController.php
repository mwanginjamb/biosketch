<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use frontend\models\ResearcherGrant;
use frontend\modules\apiv1\components\ChildController;

/**
 * /apiv1/researchers/<researcher_id>/grants
 */
class ResearcherGrantController extends ChildController
{
    public $modelClass = ResearcherGrant::class;
}
