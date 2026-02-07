<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_schedule".
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $template_id
 * @property string|null $target_month
 * @property string|null $deadline_date
 * @property string|null $reminder_1_date
 * @property string|null $reminder_2_date
 * @property string|null $escalation_date
 * @property string|null $status
 * @property string|null $type
 * @property string|null $message
 * @property string|null $last_notified_type
 * @property string|null $updated_at
 *
 * @property Customer $company
 */
class ReminderSchedule extends \yii\db\ActiveRecord
{

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_STOPPED = 'stopped';
    const STATUS_ESCALATED = 'escalated';
    const STATUS_NOT_ASSIGNED = 'notAssigned';

    const TYPE_TAX_CALENDAR = 'calendar';
    const TYPE_REGULAR = 'regular';
    const TYPE_CUSTOM = 'custom';

    public static $statuses = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_STOPPED,
        self::STATUS_ESCALATED,
        self::STATUS_NOT_ASSIGNED,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'target_month', 'deadline_date', 'reminder_1_date', 'reminder_2_date', 'escalation_date', 'last_notified_type'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'pending'],
            [['company_id', 'template_id'], 'default', 'value' => null],
            [['company_id', 'template_id'], 'integer'],
            [['target_month', 'deadline_date', 'reminder_1_date', 'reminder_2_date', 'escalation_date', 'updated_at'], 'safe'],
            [['status', 'last_notified_type'], 'string', 'max' => 20],
            [['type'], 'string', 'max' => 16],
            [['message'], 'string', 'max' => 512],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'template_id' => 'Template ID',
            'target_month' => 'Target Month',
            'deadline_date' => 'Deadline Date',
            'reminder_1_date' => 'Reminder 1 Date',
            'reminder_2_date' => 'Reminder 2 Date',
            'escalation_date' => 'Escalation Date',
            'status' => 'Status',
            'type' => 'Type',
            'message'=> 'Message',
            'last_notified_type' => 'Last Notified Type',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Customer::class, ['id' => 'company_id']);
    }
}
