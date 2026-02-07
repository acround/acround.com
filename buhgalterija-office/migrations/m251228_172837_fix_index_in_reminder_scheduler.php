<?php

use app\models\Company;
use yii\db\Migration;
use app\models\ReminderSchedule;

class m251228_172837_fix_index_in_reminder_scheduler extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('{{%fk-reminder_schedule-company_id}}', ReminderSchedule::tableName());
        $this->addForeignKey(
            '{{%fk-reminder_schedule-company_id}}',
            ReminderSchedule::tableName(),
            'company_id',
            Company::tableName(),
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-reminder_schedule-company_id}}', ReminderSchedule::tableName());
        $this->addForeignKey(
            '{{%fk-reminder_schedule-company_id}}',
            ReminderSchedule::tableName(),
            'company_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );
    }
}
