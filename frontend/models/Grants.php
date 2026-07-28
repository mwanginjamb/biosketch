<?php

namespace frontend\models;
use frontend\models\Researcher;

use Yii;

/**
 * This is the model class for table "grants".
 *
 * @property int $id
 * @property int|null $researcher_id
 * @property string|null $sponsor
 * @property string|null $grant_number
 * @property string|null $title
 * @property string|null $role
 * @property float|null $total_award_amount
 * @property string|null $currency
 * @property string|null $status
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Researcher $researcher
 */
class Grants extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['researcher_id', 'sponsor', 'grant_number', 'title', 'role', 'total_award_amount', 'start_date', 'end_date', 'description', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['currency'], 'default', 'value' => 'USD'],
            [['status'], 'default', 'value' => 'ACTIVE'],
            [['researcher_id', 'created_at', 'updated_at'], 'integer'],
            [['total_award_amount'], 'number'],
            [['start_date', 'end_date'], 'safe'],
            [['description'], 'string'],
            [['sponsor', 'title'], 'string', 'max' => 255],
            [['grant_number', 'role'], 'string', 'max' => 100],
            [['currency'], 'string', 'max' => 4],
            [['status'], 'string', 'max' => 25],
            [['researcher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Researcher::class, 'targetAttribute' => ['researcher_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'researcher_id' => Yii::t('app', 'Researcher ID'),
            'sponsor' => Yii::t('app', 'Sponsor'),
            'grant_number' => Yii::t('app', 'Grant Number'),
            'title' => Yii::t('app', 'Title'),
            'role' => Yii::t('app', 'Role'),
            'total_award_amount' => Yii::t('app', 'Total Award Amount'),
            'currency' => Yii::t('app', 'Currency'),
            'status' => Yii::t('app', 'Status'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Researcher]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\ResearcherQuery
     */
    public function getResearcher()
    {
        return $this->hasOne(Researcher::class, ['id' => 'researcher_id']);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\query\GrantsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\GrantsQuery(get_called_class());
    }

}
