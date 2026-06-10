<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "researcher_media".
 *
 * @property int $id
 * @property int $researcher_id
 * @property string|null $media_type
 * @property string|null $file_path
 * @property string|null $file_name
 * @property string|null $mime_type
 * @property int|null $file_size
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Researcher $researcher
 */
class ResearcherMedia extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'researcher_media';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['media_type', 'file_path', 'file_name', 'mime_type', 'file_size', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            // [['researcher_id'], 'required'],
            [['researcher_id', 'file_size', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['media_type', 'file_path', 'file_name', 'mime_type'], 'string', 'max' => 255],
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
            'media_type' => Yii::t('app', 'Media Type'),
            'file_path' => Yii::t('app', 'File Path'),
            'file_name' => Yii::t('app', 'File Name'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'file_size' => Yii::t('app', 'File Size'),
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
     * @return \frontend\models\query\ResearcherMediaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\ResearcherMediaQuery(get_called_class());
    }

}
