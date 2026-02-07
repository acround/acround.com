<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_template".
 *
 * @property int $id
 * @property string|null $description
 * @property string|null $text_ru
 * @property string|null $text_rs
 */
class ReminderTemplate extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'text_ru', 'text_rs'], 'default', 'value' => null],
            [['text_ru', 'text_rs'], 'string'],
            [['description'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'text_ru' => 'Text Ru',
            'text_rs' => 'Text Rs',
        ];
    }

}
