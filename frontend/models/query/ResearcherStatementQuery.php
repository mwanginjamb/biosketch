<?php

namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[\frontend\models\ResearcherStatement]].
 *
 * @see \frontend\models\ResearcherStatement
 */
class ResearcherStatementQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \frontend\models\ResearcherStatement[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\ResearcherStatement|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
