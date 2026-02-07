<?php

use app\models\Document;
use yii\db\Migration;

class m251216_103521_add_field_ocr_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Document::tableName(), "ocr_text", $this->text()->after('type_id')->null());
        $this->addColumn(Document::tableName(), "summary", $this->text()->after('ocr_text')->null());
        $this->addColumn(Document::tableName(), "category", $this->text()->after('summary')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Document::tableName(), "category");
        $this->dropColumn(Document::tableName(), "summary");
        $this->dropColumn(Document::tableName(), "ocr_text");
    }
}
