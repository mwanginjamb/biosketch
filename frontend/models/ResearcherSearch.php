<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Researcher;

/**
 * ResearcherSearch represents the model behind the search form of `frontend\models\Researcher`.
 */
class ResearcherSearch extends Researcher
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status', 'version', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['title', 'full_name', 'primary_institution', 'department', 'role_title', 'email', 'website', 'location', 'era_commons_id', 'orcid', 'profile_photo'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Researcher::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'version' => $this->version,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'primary_institution', $this->primary_institution])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'role_title', $this->role_title])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'era_commons_id', $this->era_commons_id])
            ->andFilterWhere(['like', 'orcid', $this->orcid])
            ->andFilterWhere(['like', 'profile_photo', $this->profile_photo]);

        return $dataProvider;
    }
}
