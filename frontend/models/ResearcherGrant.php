<?php

namespace frontend\models;
use common\models\User;

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

    const ROLE_PI = 1;
    const ROLE_CO_PI = 2;
    const ROLE_INVESTIGATOR = 3;
    const ROLE_COLLABORATOR = 4;
    const ROLE_OTHER = 5;
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
            'funding_agency_id' => Yii::t('app', 'Funding Agency'),
            'grant_type_id' => Yii::t('app', 'Grant Type'),
            'role_id' => Yii::t('app', 'Researcher Role'),
            'amount' => Yii::t('app', 'Award Amount'),
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


    // Role Options
    public static function getRoleOptions()
    {
        return [
            self::ROLE_PI => 'Principal Investigator',
            self::ROLE_CO_PI => 'Co-Principal Investigator',
            self::ROLE_INVESTIGATOR => 'Investigator',
            self::ROLE_COLLABORATOR => 'Collaborator',
            self::ROLE_OTHER => 'Other'
        ];
    }

    // Status Options
    public static function getStatusOptions()
    {
        return [
            1 => 'Active',
            2 => 'Completed',
            3 => 'Pending',
            4 => 'Terminated',
        ];
    }

    // Currency Options
    public static function getCurrencyOptions()
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'INR' => 'Indian Rupee',
            'AED' => 'UAE Dirham',
            'AUD' => 'Australian Dollar',
            'BRL' => 'Brazilian Real',
            'CAD' => 'Canadian Dollar',
            'CHF' => 'Swiss Franc',
            'CNY' => 'Chinese Yuan',
            'CZK' => 'Czech Koruna',
            'DKK' => 'Danish Krone',
            'HKD' => 'Hong Kong Dollar',
            'HUF' => 'Hungarian Forint',
            'IDR' => 'Indonesian Rupiah',
            'ILS' => 'Israeli Shekel',
            'JPY' => 'Japanese Yen',
            'KRW' => 'South Korean Won',
            'MXN' => 'Mexican Peso',
            'MYR' => 'Malaysian Ringgit',
            'NOK' => 'Norwegian Krone',
            'NZD' => 'New Zealand Dollar',
            'PHP' => 'Philippine Peso',
            'PLN' => 'Polish Zloty',
            'RUB' => 'Russian Ruble',
            'SEK' => 'Swedish Krona',
            'SGD' => 'Singapore Dollar',
            'THB' => 'Thai Baht',
            'TRY' => 'Turkish Lira',
            'ZAR' => 'South African Rand',
        ];
    }

    // Funding Agency Options
    public static function getFundingAgencyOptions()
    {
        return [
            1 => 'National Institutes of Health (NIH)',
            2 => 'National Science Foundation (NSF)',
            3 => 'Department of Defense (DoD)',
            4 => 'Department of Energy (DOE)',
            5 => 'Centers for Disease Control and Prevention (CDC)',
            6 => 'Food and Drug Administration (FDA)',
            7 => 'Environmental Protection Agency (EPA)',
            8 => 'United States Department of Agriculture (USDA)',
            9 => 'Department of Veterans Affairs (VA)',
            10 => 'Department of Homeland Security (DHS)',
            11 => 'Department of Education (ED)',
            12 => 'Department of Transportation (DOT)',
            13 => 'Department of Housing and Urban Development (HUD)',
            14 => 'Department of Commerce (DOC)',
            15 => 'Department of the Interior (DOI)',
            16 => 'Department of Labor (DOL)',
            17 => 'Department of State (DOS)',
            18 => 'Department of the Treasury (USDT)',
            19 => 'Department of Justice (DOJ)',
            20 => 'Department of Health and Human Services (HHS)',
            21 => 'European Research Council (ERC)',
            22 => 'Horizon Europe',
            23 => 'Marie Skłodowska-Curie Actions (MSCA)',
            24 => 'European Institute of Innovation and Technology (EIT)',
            25 => 'European Space Agency (ESA)',
            26 => 'European Medicines Agency (EMA)',
            27 => 'European Centre for Disease Prevention and Control (ECDC)',
            28 => 'European Environment Agency (EEA)',
            29 => 'European Investment Bank (EIB)',
            30 => 'European Bank for Reconstruction and Development (EBRD)',
            31 => 'European Defence Agency (EDA)',
            32 => 'Bill & Melinda Gates Foundation',
            33 => 'Wellcome Trust',
            34 => 'Howard Hughes Medical Institute (HHMI)',
            35 => 'Rockefeller Foundation',
            36 => 'Ford Foundation',
            37 => 'Carnegie Corporation of New York',
            38 => 'Open Society Foundations',
            39 => 'Robert Wood Johnson Foundation',
            40 => 'W.K. Kellogg Foundation',
            41 => 'MacArthur Foundation',
            42 => 'David and Lucile Packard Foundation',
            43 => 'John D. and Catherine T. MacArthur Foundation',
            44 => 'The Kresge Foundation',
            45 => 'The William and Flora Hewlett Foundation',
            46 => 'The Andrew W. Mellon Foundation',
            47 => 'The Walton Family Foundation',
            48 => 'The Gordon and Betty Moore Foundation',
            49 => 'The Simons Foundation',
            50 => 'The Alfred P. Sloan Foundation',
        ];
    }

}
