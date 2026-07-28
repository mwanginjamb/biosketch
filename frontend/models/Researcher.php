<?php

namespace frontend\models;
use common\models\User;
use Override;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "researcher".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string $full_name
 * @property string|null $primary_institution
 * @property string|null $department
 * @property string|null $role_title
 * @property string|null $email
 * @property string|null $website
 * @property string|null $location
 * @property string|null $era_commons_id
 * @property string|null $orcid
 * @property string|null $profile_photo
 * @property int|null $status
 * @property int|null $version
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Publications[] $publications
 * @property ResearcherEducation[] $researcherEducations
 * @property ResearcherIdentifier[] $researcherIdentifiers
 * @property ResearcherMedia[] $researcherMedia
 * @property ResearcherStatement|null $researcherStatement
 * @property Grants[] $grants
 * @property User $user
 */
class Researcher extends \yii\db\ActiveRecord
{

    public $attachment;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'researcher';
    }

    
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'primary_institution', 'department', 'role_title', 'email', 'website', 'location', 'era_commons_id', 'orcid', 'profile_photo', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 0],
            [['version'], 'default', 'value' => 1],
            [['user_id', 'full_name'], 'required'],
            [['user_id', 'status', 'version', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['title', 'full_name', 'primary_institution', 'department', 'role_title', 'email', 'location'], 'string', 'max' => 255],
            [['website', 'profile_photo'], 'string', 'max' => 500],
            [['era_commons_id', 'orcid'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            // Attachment  - jpeg, jpg,png
            [['attachment'], 'file', 'mimeTypes' => ['image/jpeg', 'image/png']],
            [['attachment'], 'file', 'maxSize' => '5120'], //5mb

            [['major_breakthrough','patent_filed','research_tags'],'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'title' => Yii::t('app', 'Title'),
            'full_name' => Yii::t('app', 'Full Name'),
            'primary_institution' => Yii::t('app', 'Primary Institution'),
            'department' => Yii::t('app', 'Department'),
            'role_title' => Yii::t('app', 'Role Title'),
            'email' => Yii::t('app', 'Email'),
            'website' => Yii::t('app', 'Website'),
            'location' => Yii::t('app', 'Location'),
            'era_commons_id' => Yii::t('app', 'Era Commons ID'),
            'orcid' => Yii::t('app', 'Orcid'),
            'profile_photo' => Yii::t('app', 'Profile Photo'),
            'status' => Yii::t('app', 'Status'),
            'version' => Yii::t('app', 'Version'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Publications]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\PublicationsQuery
     */
    public function getPublications()
    {
        return $this->hasMany(Publications::class, ['researcher_id' => 'id']);
    }

    /**
     * Gets query for [[ResearcherEducations]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\ResearcherEducationQuery
     */
    public function getResearcherEducations()
    {
        return $this->hasMany(ResearcherEducation::class, ['researcher_id' => 'id']);
    }

    /**
     * Gets query for [[ResearcherIdentifiers]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\ResearcherIdentifierQuery
     */
    public function getResearcherIdentifiers()
    {
        return $this->hasMany(ResearcherIdentifier::class, ['researcher_id' => 'id']);
    }

    /**
     * Gets query for [[ResearcherMedia]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\ResearcherMediaQuery
     */
    public function getResearcherMedia()
    {
        return $this->hasMany(ResearcherMedia::class, ['researcher_id' => 'id']);
    }

    /**
     * Gets query for [[ResearcherStatement]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\ResearcherStatementQuery
     */
    public function getResearcherStatement()
    {
        return $this->hasOne(ResearcherStatement::class, ['researcher_id' => 'id']);
    }

    public function getGrants()
    {
        return $this->hasOne(Grants::class,['researcher_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\query\ResearcherQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\ResearcherQuery(get_called_class());
    }

    //get titles list for dropdown
    public function getTitles()
    {
        return [
            'Dr.' => 'Dr.',
            'Prof.' => 'Prof.',
            'Mr.' => 'Mr.',
            'Ms.' => 'Ms.',
            'Mrs.' => 'Mrs.',

        ];
    }

    public static function statusOptions()
    {
        return [
            0 => 'Draft',
            1 => 'Active',
        ];
    }

}
