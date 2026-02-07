<?php

use app\models\ReminderRegularCompany;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%reminder_reg_company}}`.
 */
class m260101_145241_create_reminder_reg_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(ReminderRegularCompany::tableName(), [
            'id' => $this->primaryKey(),
            'reminder_id' => $this->bigInteger(),
            'company_id' => $this->bigInteger(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(ReminderRegularCompany::tableName());
    }
}
