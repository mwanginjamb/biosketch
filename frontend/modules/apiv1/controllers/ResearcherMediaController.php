<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use frontend\models\ResearcherMedia;
use frontend\modules\apiv1\components\ChildController;

/**
 * /apiv1/researchers/<researcher_id>/media
 */
class ResearcherMediaController extends ChildController
{
    public $modelClass = ResearcherMedia::class;
}
