<?php
namespace frontend\modules\apiv1\models\search;

use frontend\models\Researcher;
use yii\data\ActiveDataProvider;
// api/modules/v1/models/search/ResearcherSearch.php
class ResearcherSearch extends Researcher
{
    public $q;
    public $staff_id;

    public function rules()
    {
        return [[['q', 'staff_id', 'primary_institution', 'department', 'orcid'], 'safe']];
    }

    public function search($params)
    {
        $query = Researcher::find()
            ->alias('r')
            ->joinWith(['user u'], false)
            ->where(['r.status' => 1]);

        $dp = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSizeLimit' => [1, 100]],
            'sort' => [
                'defaultOrder' => ['full_name' => SORT_ASC],
                'attributes' => [
                    'full_name' => ['asc' => ['r.full_name' => SORT_ASC], 'desc' => ['r.full_name' => SORT_DESC]],
                    'primary_institution' => [
                        'asc' => ['r.primary_institution' => SORT_ASC],
                        'desc' => ['r.primary_institution' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dp;
        }

        $query->andFilterWhere(['like', 'r.primary_institution', $this->primary_institution])
            ->andFilterWhere(['like', 'r.department', $this->department])
            ->andFilterWhere(['r.orcid' => $this->orcid])
            ->andFilterWhere(['u.staff_id' => $this->staff_id]);   // exact match

        // free-text search: name, tags, or exact staff ID
        if ($this->q) {
            $query->andWhere([
                'or',
                ['like', 'r.full_name', $this->q],
                ['like', 'r.research_tags', $this->q],
                ['u.staff_id' => trim($this->q)],
            ]);
        }
        return $dp;
    }
}