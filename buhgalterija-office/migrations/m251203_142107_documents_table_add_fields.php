<?php

use app\models\Document;
use yii\db\Migration;

class m251203_142107_documents_table_add_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Document::tableName(), 'tg_id', $this->bigInteger()->null()->after('id'));
        $this->addColumn(Document::tableName(), 'company_id', $this->bigInteger()->null()->after('tg_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Document::tableName(), 'company_id');
        $this->dropColumn(Document::tableName(), 'tg_id');
    }
}
