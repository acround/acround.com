<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "poreski_kalendar".
 *
 * @property int $id
 * @property string $input_date
 * @property string $reminder_1_date
 * @property string $reminder_2_date
 * @property string $escalation_date
 * @property string $target_month
 * @property string $activity_type_rs
 * @property string $activity_text_rs
 * @property string $activity_type_ru
 * @property string $activity_text_ru
 * @property string $activity
 */
class TaxCalendar extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poreski_kalendar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activity'], 'default', 'value' => ''],
            [['input_date', 'activity_type_rs', 'activity_text_rs', 'activity_type_ru', 'activity_text_ru'], 'required'],
            [['input_date', 'reminder_1_date', 'reminder_2_date', 'escalation_date', 'target_month'], 'safe'],
            [['activity_type_rs', 'activity_type_ru'], 'string', 'max' => 256],
            [['activity_text_rs', 'activity_text_ru'], 'string', 'max' => 1024],
            [['activity'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'input_date' => 'Input Date',
            'reminder_1_date' => 'First notification Date',
            'reminder_2_date' => 'Second notification Date',
            'escalation_date' => 'Escalation Date',
            'target_month' => 'Target Month',
            'activity_type_rs' => 'Activity Type',
            'activity_text_rs' => 'Activity Text',
            'activity_type_ru' => 'Activity Type',
            'activity_text_ru' => 'Activity Text',
            'activity' => 'Activity',
        ];
    }

}
