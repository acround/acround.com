<?php

use app\models\Document;
use yii\db\Migration;

class m251209_090546_fix_content_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn(Document::tableName(), "content");
        $this->addColumn(Document::tableName(), "content", 'BYTEA');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Document::tableName(), "content");
        $this->addColumn(Document::tableName(), "content", $this->text());
    }
}
