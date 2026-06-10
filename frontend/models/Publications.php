<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "publications".
 *
 * @property int $id
 * @property int|null $researcher_id
 * @property string|null $title
 * @property string|null $journal
 * @property int|null $publication_year
 * @property string|null $doi
 * @property string|null $pmid
 * @property string|null $citation_text
 * @property int|null $is_selected
 * @property int|null $sort_order
 * @property string|null $imported_from
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Researcher $researcher
 */
class Publications extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'publications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['researcher_id', 'title', 'journal', 'publication_year', 'doi', 'pmid', 'citation_text', 'is_selected', 'sort_order', 'imported_from', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['researcher_id', 'publication_year', 'is_selected', 'sort_order', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['title', 'citation_text'], 'string'],
            [['journal', 'doi', 'pmid', 'imported_from'], 'string', 'max' => 255],
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
            'title' => Yii::t('app', 'Title'),
            'journal' => Yii::t('app', 'Journal'),
            'publication_year' => Yii::t('app', 'Publication Year'),
            'doi' => Yii::t('app', 'DOI'),
            'pmid' => Yii::t('app', 'PMID'),
            'citation_text' => Yii::t('app', 'Citation Text'),
            'is_selected' => Yii::t('app', 'Is Selected'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'imported_from' => Yii::t('app', 'Imported From'),
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
     * @return \frontend\models\query\PublicationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\PublicationsQuery(get_called_class());
    }

}
