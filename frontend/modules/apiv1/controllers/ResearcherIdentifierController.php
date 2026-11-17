<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use frontend\models\ResearcherIdentifier;
use frontend\modules\apiv1\components\ChildController;

/**
 * /apiv1/researchers/<researcher_id>/identifiers
 */
class ResearcherIdentifierController extends ChildController
{
    public $modelClass = ResearcherIdentifier::class;
}
