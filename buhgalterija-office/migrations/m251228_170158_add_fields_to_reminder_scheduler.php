<?php

use app\models\ReminderSchedule;
use yii\db\Migration;

class m251228_170158_add_fields_to_reminder_scheduler extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(ReminderSchedule::tableName(), "type", $this->string(16));
        $this->addColumn(ReminderSchedule::tableName(), "template_id", $this->integer());
        $this->addColumn(ReminderSchedule::tableName(), "message", $this->string(512));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(ReminderSchedule::tableName(), "type");
        $this->dropColumn(ReminderSchedule::tableName(), "message");
        $this->dropColumn(ReminderSchedule::tableName(), "template_id");
    }
}
