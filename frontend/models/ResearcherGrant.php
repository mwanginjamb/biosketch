<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "researcher_grant".
 *
 * @property int $id
 * @property int|null $researcher_id
 * @property string|null $grant_number
 * @property string|null $title
 * @property int|null $funding_agency_id
 * @property int|null $grant_type_id
 * @property int|null $role_id
 * @property float|null $amount
 * @property string|null $currency
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $status_id
 * @property string|null $description
 * @property int|null $created_by
 * @property int|null $update_by
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Researcher $researcher
 * @property User $user
 * @property User $user0
 */
class ResearcherGrant extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'researcher_grant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['researcher_id', 'grant_number', 'title', 'funding_agency_id', 'grant_type_id', 'role_id', 'amount', 'currency', 'start_date', 'end_date', 'status_id', 'description', 'created_by', 'update_by', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['researcher_id', 'funding_agency_id', 'grant_type_id', 'role_id', 'status_id', 'created_by', 'update_by', 'created_at', 'updated_at'], 'integer'],
            [['title', 'description'], 'string'],
            [['amount'], 'number'],
            [['start_date', 'end_date'], 'safe'],
            [['grant_number', 'currency'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['researcher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Researcher::class, 'targetAttribute' => ['researcher_id' => 'id']],
            [['update_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['update_by' => 'id']],
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
            'grant_number' => Yii::t('app', 'Grant Number'),
            'title' => Yii::t('app', 'Title'),
            'funding_agency_id' => Yii::t('app', 'Funding Agency ID'),
            'grant_type_id' => Yii::t('app', 'Grant Type ID'),
            'role_id' => Yii::t('app', 'Role ID'),
            'amount' => Yii::t('app', 'Amount'),
            'currency' => Yii::t('app', 'Currency'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'status_id' => Yii::t('app', 'Status ID'),
            'description' => Yii::t('app', 'Description'),
            'created_by' => Yii::t('app', 'Created By'),
            'update_by' => Yii::t('app', 'Update By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Researcher]].
     *
     * @return \yii\db\ActiveQuery|ResearcherQuery
     */
    public function getResearcher()
    {
        return $this->hasOne(Researcher::class, ['id' => 'researcher_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[User0]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::class, ['id' => 'update_by']);
    }

    /**
     * {@inheritdoc}
     * @return ResearcherGrantQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ResearcherGrantQuery(get_called_class());
    }

}
