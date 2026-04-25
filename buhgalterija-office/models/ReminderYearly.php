<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_yearly".
 *
 * @property int $id
 * @property int $deadline_day
 * @property int $deadline_month
 * @property string|null $type_ru
 * @property string|null $type_rs
 * @property string|null $text_ru
 * @property string|null $text_rs
 *
 * @property ReminderYearlyCompany[] $reminderYearlyCompanies
 */
class ReminderYearly extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_yearly';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_ru', 'type_rs', 'text_ru', 'text_rs'], 'default', 'value' => null],
            [['deadline_day', 'deadline_month'], 'required'],
            [['deadline_day', 'deadline_month'], 'default', 'value' => null],
            [['deadline_day', 'deadline_month'], 'integer'],
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
            'deadline_day' => 'Deadline Day',
            'deadline_month' => 'Deadline Month',
            'type_ru' => 'Type Ru',
            'type_rs' => 'Type Rs',
            'text_ru' => 'Text Ru',
            'text_rs' => 'Text Rs',
        ];
    }

    /**
     * Gets query for [[ReminderYearlyCompanies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReminderYearlyCompanies()
    {
        return $this->hasMany(ReminderYearlyCompany::class, ['reminder_id' => 'id']);
    }

}
