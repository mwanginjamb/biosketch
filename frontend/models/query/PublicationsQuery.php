<?php

namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[\frontend\models\Publications]].
 *
 * @see \frontend\models\Publications
 */
class PublicationsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \frontend\models\Publications[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\Publications|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
