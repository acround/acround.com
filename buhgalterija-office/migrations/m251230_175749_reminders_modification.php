<?php

use app\models\TaxCalendar;
use yii\db\Migration;

class m251230_175749_reminders_modification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(TaxCalendar::tableName(), 'notification_date', 'reminder_1_date');
        $this->addColumn(TaxCalendar::tableName(), 'reminder_2_date', $this->date()->after('reminder_1_date'));
        $this->addColumn(TaxCalendar::tableName(), 'escalation_date', $this->date()->after('reminder_2_date'));
        $this->addColumn(TaxCalendar::tableName(), 'target_month', $this->date()->after('escalation_date'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn(TaxCalendar::tableName(), 'reminder_1_date', 'notification_date');
        $this->dropColumn(TaxCalendar::tableName(), 'reminder_2_date');
        $this->dropColumn(TaxCalendar::tableName(), 'escalation_date');
        $this->dropColumn(TaxCalendar::tableName(), 'target_month');
    }

}
