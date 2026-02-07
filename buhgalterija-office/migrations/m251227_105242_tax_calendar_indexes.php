<?php

use app\models\TaxCalendar;
use yii\db\Migration;

class m251227_105242_tax_calendar_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-tax_calendar-input_date-activity_type',
            TaxCalendar::tableName(),
            ['input_date', 'activity_type']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-tax_calendar-input_date-activity_type',
            TaxCalendar::tableName()
        );
    }
}
