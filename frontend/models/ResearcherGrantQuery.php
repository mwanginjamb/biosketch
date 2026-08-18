<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[ResearcherGrant]].
 *
 * @see ResearcherGrant
 */
class ResearcherGrantQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ResearcherGrant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ResearcherGrant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
