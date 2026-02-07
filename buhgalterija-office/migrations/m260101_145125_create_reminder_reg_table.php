<?php

use app\models\ReminderRegular;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%reminder_reg}}`.
 */
class m260101_145125_create_reminder_reg_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ReminderRegular::tableName(), [
            'id' => $this->primaryKey(),
            'deadline_day' => $this->integer()->notNull(),
            'type_ru' => $this->string(32),
            'type_rs' => $this->string(32),
            'text_ru' => $this->string(1024),
            'text_rs' => $this->string(1024),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ReminderRegular::tableName());
    }
}
