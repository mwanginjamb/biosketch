<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\controllers;

use frontend\models\Publications;
use frontend\modules\apiv1\components\ChildController;

/**
 * /apiv1/researchers/<researcher_id>/publications
 */
class PublicationsController extends ChildController
{
    public $modelClass = Publications::class;
}
