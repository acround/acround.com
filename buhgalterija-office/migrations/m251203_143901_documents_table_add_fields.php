<?php

use app\models\Document;
use yii\db\Migration;

class m251203_143901_documents_table_add_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Document::tableName(), 'filename', $this->string(512)->null()->after('content'));
        $this->addColumn(Document::tableName(), 'mimetype', $this->string(64)->null()->after('filename'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Document::tableName(), 'mimetype');
        $this->dropColumn(Document::tableName(), 'filename');
    }

}
