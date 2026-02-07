<?php

use app\models\RemindersQueue;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%reminders_queue}}`.
 */
class m251225_085618_create_reminders_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(RemindersQueue::tableName(), [
            'id' => $this->primaryKey(),
            'company_id' => $this->bigInteger()->notNull(),
            'reminder_type' => $this->string(50),
            'deadline_date' => $this->datetime(),
            'status' => $this->string(20),
            'attempts_made' => $this->integer()->defaultValue(0),
            'last_attempt_at' => $this->datetime(),
            'next_attempt_at' => $this->datetime(),
        ]);
        $this->createIndex('idx_reminders_queue_company_id', RemindersQueue::tableName(), 'company_id');
        $this->createIndex('idx_reminders_queue_status', RemindersQueue::tableName(), 'status');
        $this->createIndex('idx_reminders_queue_deadline_date', RemindersQueue::tableName(), 'deadline_date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_reminders_queue_company_id', RemindersQueue::tableName());
        $this->dropIndex('idx_reminders_queue_status', RemindersQueue::tableName());
        $this->dropIndex('idx_reminders_queue_deadline_date', RemindersQueue::tableName());
        $this->dropTable(RemindersQueue::tableName());
    }
}
