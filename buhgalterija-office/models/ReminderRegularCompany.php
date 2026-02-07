<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_reg_company".
 *
 * @property int $id
 * @property int|null $reminder_id
 * @property int|null $company_id
 */
class ReminderRegularCompany extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_reg_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reminder_id', 'company_id'], 'default', 'value' => null],
            [['reminder_id', 'company_id'], 'default', 'value' => null],
            [['reminder_id', 'company_id'], 'integer'],
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

}
