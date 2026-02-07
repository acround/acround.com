<?php

use app\models\PassedBy;
use yii\db\Migration;

class m251221_173625_passed_by_field_summary_gets_1024 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(PassedBy::tableName(), 'summary', $this->string(1024)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn(PassedBy::tableName(), 'summary', $this->string(512)->null());
    }
}
