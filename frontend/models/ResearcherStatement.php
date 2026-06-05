<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "researcher_statement".
 *
 * @property int $id
 * @property int|null $researcher_id
 * @property string|null $statement_type
 * @property string|null $content
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Researcher $researcher
 */
class ResearcherStatement extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'researcher_statement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['researcher_id', 'statement_type', 'content', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['researcher_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['content'], 'string'],
            [['statement_type'], 'string', 'max' => 255],
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
            'statement_type' => Yii::t('app', 'Statement Type'),
            'content' => Yii::t('app', 'Content'),
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
     * @return \frontend\models\query\ResearcherStatementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\ResearcherStatementQuery(get_called_class());
    }

}
