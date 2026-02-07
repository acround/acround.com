<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $name_tg
 * @property int|null $type_id
 * @property bool|null $is_pdv
 * @property int|null $activity_id
 * @property string|null $specific_reports
 * @property string|null $reminder
 * @property int|null $pib
 * @property string|null $status
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $report_date
//  * @property string|null $embedding
 *
 * @property Accountant[] $accountants
 * @property CompanyActivities $activity
 * @property CompanyAccountant[] $companyAccountants
 * @property CompanyCustomer[] $companyCustomers
 * @property CompanyNotes[] $companyNotes
 * @property ReminderSchedule[] $reminderSchedules
 * @property CompanyType $type
 */
class Company extends \yii\db\ActiveRecord
{

    const COMPANY_STATUS_ACTIVE = 'active';
    const COMPANY_STATUS_ONBOARDING = 'onboarding';
    const COMPANY_STATUS_PAUSED = 'paused';
    const COMPANY_STATUS_INACTIVE = 'inactive';

    public static $statuses = [
        self::COMPANY_STATUS_ACTIVE,
        self::COMPANY_STATUS_ONBOARDING,
        self::COMPANY_STATUS_PAUSED,
        self::COMPANY_STATUS_INACTIVE,
    ];

    public static $types = [
        'DOO',
        'Knigaš',
        'Paušal,'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'name_tg', 'type_id', 'activity_id', 'specific_reports', 'reminder', 'pib', 'report_date'], 'default', 'value' => null],
            [['is_pdv'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 'onboarding'],
            [['type_id', 'activity_id', 'pib'], 'default', 'value' => null],
            [['type_id', 'activity_id', 'pib'], 'integer'],
            [['is_pdv'], 'boolean'],
            [['created_at', 'updated_at', 'report_date'], 'safe'],
            // [['embedding'], 'string'],
            [['name', 'name_tg'], 'string', 'max' => 512],
            [['specific_reports', 'reminder'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 32],
            [['pib'], 'unique'],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompanyActivities::class, 'targetAttribute' => ['activity_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompanyType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'name_tg' => 'Name Tg',
            'type_id' => 'Type ID',
            'is_pdv' => 'Is Pdv',
            'activity_id' => 'Activity ID',
            'specific_reports' => 'Specific Reports',
            'reminder' => 'Reminder',
            'pib' => 'Pib',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'report_date' => 'Report Date',
            // 'embedding' => 'Embedding',
        ];
    }

    /**
     * Gets query for [[Accountants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyAccountants()
    {
        return $this->hasMany(Accountant::class, ['id' => 'accountant_id'])->viaTable('company_accountant', ['company_id' => 'id']);
    }

    /**
     * Gets query for [[Activity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivityQuery()
    {
        return $this->hasOne(CompanyActivities::class, ['id' => 'activity_id']);
    }

    /**
     * Gets [[Activity]].
     *
     * @return CompanyActivities
     */
    public function getActivity()
    {
        return CompanyActivities::findOne($this->activity_id);
    }

    /**
     * Gets query for [[CompanyCustomers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyCustomersQuery()
    {
        return $this->hasMany(CompanyCustomer::class, ['company_id' => 'id']);
    }

    public function getCustomers()
    {
        $query = Customer::find()
            ->leftJoin(CompanyCustomer::tableName(), CompanyCustomer::tableName() . '.customer_id = customer.id')
            ->where([CompanyCustomer::tableName() . '.company_id' => $this->id]);
        return $query->all();
    }

    public function getCustomer()
    {
        $query = Customer::find()
            ->leftJoin(CompanyCustomer::tableName(), CompanyCustomer::tableName() . '.customer_id = customer.id')
            ->where([CompanyCustomer::tableName() . '.company_id' => $this->id]);
        return $query->one();
    }

    /**
     * Gets query for [[CompanyNotes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyNotesQuery()
    {
        return $this->hasMany(CompanyNotes::class, ['company_id' => 'id']);
    }

    /**
     * Gets query for [[ReminderSchedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReminderSchedulesQuery()
    {
        return $this->hasMany(ReminderSchedule::class, ['company_id' => 'id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypeQuery()
    {
        return $this->hasOne(CompanyType::class, ['id' => 'type_id']);
    }

    public function getDocumentsNumber()
    {
        return Document::find()->where(['company_id' => $this->id])->count();
    }

    public function getNotesNumber()
    {
        return CompanyNotes::find()->where(['company_id' => $this->id, 'status' => 'active'])->count();
    }
}
