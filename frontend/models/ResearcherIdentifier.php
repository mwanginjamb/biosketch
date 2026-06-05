<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "researcher_identifier".
 *
 * @property int $id
 * @property int $researcher_id
 * @property string|null $identifier_type
 * @property string|null $identifier_value
 * @property int|null $verification_status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Researcher $researcher
 */
class ResearcherIdentifier extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'researcher_identifier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['identifier_type', 'identifier_value', 'verification_status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['researcher_id'], 'required'],
            [['researcher_id', 'verification_status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['identifier_type', 'identifier_value'], 'string', 'max' => 255],
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
            'identifier_type' => Yii::t('app', 'Identifier Type'),
            'identifier_value' => Yii::t('app', 'Identifier Value'),
            'verification_status' => Yii::t('app', 'Verification Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
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
     * @return \frontend\models\query\ResearcherIdentifierQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\ResearcherIdentifierQuery(get_called_class());
    }

}
