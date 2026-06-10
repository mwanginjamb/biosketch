<?php

namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[\frontend\models\Grants]].
 *
 * @see \frontend\models\Grants
 */
class GrantsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \frontend\models\Grants[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\Grants|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
