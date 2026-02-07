<?php

use app\models\TaxCalendar;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%poreski_kalendar}}`.
 */
class m251215_105442_create_poreski_kalendar_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(TaxCalendar::tableName(), [
            'id' => $this->primaryKey(),
            'input_date' => $this->dateTime()->notNull(),
            'notification_date' => $this->dateTime()->notNull(),
            'activity_type' => $this->string(256)->notNull(),
            'activity_text' => $this->string(1024)->notNull(),
            'activity'=> $this->string(32)->notNull()->defaultValue(''),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(TaxCalendar::tableName());
    }
}
