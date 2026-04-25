<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_onetime".
 *
 * @property int $id
 * @property string $deadline
 * @property string|null $type_ru
 * @property string|null $type_rs
 * @property string|null $text_ru
 * @property string|null $text_rs
 *
 * @property ReminderOnetimeCompany[] $reminderOnetimeCompanies
 */
class ReminderOneTime extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_onetime';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_ru', 'type_rs', 'text_ru', 'text_rs'], 'default', 'value' => null],
            [['deadline'], 'required'],
            [['deadline'], 'safe'],
            [['type_ru', 'type_rs'], 'string', 'max' => 64],
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
            'deadline' => 'Deadline',
            'type_ru' => 'Type Ru',
            'type_rs' => 'Type Rs',
            'text_ru' => 'Text Ru',
            'text_rs' => 'Text Rs',
        ];
    }

    /**
     * Gets query for [[ReminderOnetimeCompanies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReminderOnetimeCompanies()
    {
        return $this->hasMany(ReminderOnetimeCompany::class, ['reminder_id' => 'id']);
    }

}
