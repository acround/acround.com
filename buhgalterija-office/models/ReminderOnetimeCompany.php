<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_onetime_company".
 *
 * @property int $id
 * @property int $reminder_id
 * @property int $company_id
 *
 * @property Company $company
 * @property ReminderOneTime $reminder
 */
class ReminderOnetimeCompany extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_onetime_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reminder_id', 'company_id'], 'required'],
            [['reminder_id', 'company_id'], 'default', 'value' => null],
            [['reminder_id', 'company_id'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['reminder_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReminderOneTime::class, 'targetAttribute' => ['reminder_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reminder_id' => 'Reminder ID',
            'company_id' => 'Company ID',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * Gets query for [[Reminder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReminder()
    {
        return $this->hasOne(ReminderOneTime::class, ['id' => 'reminder_id']);
    }

}
