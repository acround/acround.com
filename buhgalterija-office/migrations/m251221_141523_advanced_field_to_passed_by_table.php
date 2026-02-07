<?php

use app\models\PassedBy;
use yii\db\Migration;

class m251221_141523_advanced_field_to_passed_by_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(PassedBy::tableName(), 'summary', $this->string(512)->null()->after('lang'));
        $this->addColumn(PassedBy::tableName(), 'dialog', $this->text()->null()->after('summary'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(PassedBy::tableName(), 'dialog');
        $this->dropColumn(PassedBy::tableName(), 'summary');
    }

}
