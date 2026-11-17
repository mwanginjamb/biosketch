<?php

declare(strict_types=1);

namespace frontend\modules\apiv1\models;

use frontend\models\Researcher as BaseResearcher;
use yii\helpers\Url;

/**
 * API representation of a researcher profile.
 */
class Researcher extends BaseResearcher
{
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['created_by'], $fields['updated_by'], $fields['version']);

        // Absolute URL so API clients can load the image directly.
        $fields['profile_photo'] = fn(self $m) => $m->profile_photo ? Url::to($m->profile_photo, true) : null;
        // Employee number from the linked account (searchable via ?staff_id=).
        $fields['staff_id'] = fn(self $m) => $m->user?->staff_id;

        return $fields;
    }

    /** Available via ?expand=education,publications,... */
    public function extraFields()
    {
        return [
            'education' => 'researcherEducations',
            'identifiers' => 'researcherIdentifiers',
            'media' => 'researcherMedia',
            'statement' => 'researcherStatement',
            'grants' => 'researcherGrants',
            'publications',
        ];
    }
}
