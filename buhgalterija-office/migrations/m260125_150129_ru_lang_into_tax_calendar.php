<?php

use app\models\TaxCalendar;
use yii\db\Migration;

class m260125_150129_ru_lang_into_tax_calendar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(TaxCalendar::tableName(), 'activity_type', 'activity_type_rs');
        $this->renameColumn(TaxCalendar::tableName(), 'activity_text', 'activity_text_rs');
        $this->addColumn(TaxCalendar::tableName(), 'activity_type_ru', $this->string(256));
        $this->addColumn(TaxCalendar::tableName(), 'activity_text_ru', $this->text(1024));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(TaxCalendar::tableName(), 'activity_type_ru');
        $this->dropColumn(TaxCalendar::tableName(), 'activity_text_ru');
        $this->renameColumn(TaxCalendar::tableName(), 'activity_type_rs', 'activity_type');
        $this->renameColumn(TaxCalendar::tableName(), 'activity_text_rs', 'activity_text');
    }
}
