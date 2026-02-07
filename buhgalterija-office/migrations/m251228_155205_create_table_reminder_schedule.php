<?php

use app\models\ReminderSchedule;
use yii\db\Migration;

class m251228_155205_create_table_reminder_schedule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ReminderSchedule::tableName(), [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer(),
            'target_month' => $this->date(),
            'deadline_date' => $this->date(),
            'reminder_1_date' => $this->date(),
            'reminder_2_date' => $this->date(),
            'escalation_date' => $this->date(),
            'status' => $this->string(20)->defaultValue('pending'),
            'last_notified_type' => $this->string(20),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);
        // Индекс для внешнего ключа
        $this->createIndex(
            '{{%idx-reminder_schedule-company_id}}',
            ReminderSchedule::tableName(),
            'company_id'
        );
        $this->createIndex(
            '{{%idx-reminder_schedule-deadline_date}}',
            ReminderSchedule::tableName(),
            'deadline_date'
        );
        $this->createIndex(
            '{{%idx-reminder_schedule-reminder_1_date}}',
            ReminderSchedule::tableName(),
            'reminder_1_date'
        );
        $this->createIndex(
            '{{%idx-reminder_schedule-reminder_2_date}}',
            ReminderSchedule::tableName(),
            'reminder_2_date'
        );
        $this->createIndex(
            '{{%idx-reminder_schedule-escalation_date}}',
            ReminderSchedule::tableName(),
            'escalation_date'
        );

        // Внешний ключ к таблице customer
        $this->addForeignKey(
            '{{%fk-reminder_schedule-company_id}}',
            ReminderSchedule::tableName(),
            'company_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-reminder_schedule-company_id}}', ReminderSchedule::tableName());
        $this->dropIndex('{{%idx-reminder_schedule-deadline_date}}', ReminderSchedule::tableName());
        $this->dropIndex('{{%idx-reminder_schedule-reminder_1_date}}', ReminderSchedule::tableName());
        $this->dropIndex('{{%idx-reminder_schedule-reminder_2_date}}', ReminderSchedule::tableName());
        $this->dropIndex('{{%idx-reminder_schedule-escalation_date}}', ReminderSchedule::tableName());
        $this->dropForeignKey('{{%fk-reminder_schedule-company_id}}', ReminderSchedule::tableName());
        $this->dropTable(ReminderSchedule::tableName());
    }
}
