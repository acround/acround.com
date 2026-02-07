<?php

use app\models\Document;
use yii\db\Migration;

class m251222_162711_document_ocr_status_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Document::tableName(), 'ocr_status', $this->string(16)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Document::tableName(), 'ocr_status');
    }
}
