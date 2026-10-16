<?php
// apiv1/modules/v1/models/Researcher.php
namespace frontend\modules\apiv1\models;

use frontend\models\Researcher as BaseResearcher;

class Researcher extends BaseResearcher
{
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['created_by'], $fields['updated_by'], $fields['version']);
        // build a full URL for the photo
        $fields['profile_photo'] = fn($m) => $m->profile_photo
            ? \Yii::$app->params['uploadsUrl'] . '/' . $m->profile_photo : null;
        return $fields;
    }

    // available via ?expand=
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