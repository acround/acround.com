<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminders_queue".
 *
 * @property int $id
 * @property int $company_id
 * @property string|null $reminder_type
 * @property string|null $deadline_date
 * @property string|null $status
 * @property int|null $attempts_made
 * @property string|null $last_attempt_at
 * @property string|null $next_attempt_at
 */
class RemindersQueue extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminders_queue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reminder_type', 'deadline_date', 'status', 'last_attempt_at', 'next_attempt_at'], 'default', 'value' => null],
            [['attempts_made'], 'default', 'value' => 0],
            [['company_id'], 'required'],
            [['company_id', 'attempts_made'], 'default', 'value' => null],
            [['company_id', 'attempts_made'], 'integer'],
            [['deadline_date', 'last_attempt_at', 'next_attempt_at'], 'safe'],
            [['reminder_type'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 20],
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
            'reminder_type' => 'Reminder Type',
            'deadline_date' => 'Deadline Date',
            'status' => 'Status',
            'attempts_made' => 'Attempts Made',
            'last_attempt_at' => 'Last Attempt At',
            'next_attempt_at' => 'Next Attempt At',
        ];
    }

}
