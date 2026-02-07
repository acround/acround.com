<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_reg".
 *
 * @property int $id
 * @property int $deadline_day
 * @property string|null $type_ru
 * @property string|null $type_rs
 * @property string|null $text_ru
 * @property string|null $text_rs
 */
class ReminderRegular extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_reg';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_ru', 'type_rs', 'text_ru', 'text_rs'], 'default', 'value' => null],
            [['deadline_day'], 'required'],
            [['deadline_day'], 'safe'],
            [['type_ru', 'type_rs'], 'string', 'max' => 32],
            [['text_ru', 'text_rs'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'deadline_day' => 'Deadline Day',
            'type_ru' => 'Type Ru',
            'type_rs' => 'Type Rs',
            'text_ru' => 'Text Ru',
            'text_rs' => 'Text Rs',
        ];
    }

}
