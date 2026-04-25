<?php

use app\models\Document;
use yii\db\Migration;

class m260220_102341_fix_field_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(Document::tableName(), 'mimetype', $this->string(128));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn(Document::tableName(), 'mimetype', $this->string(64));
    }
}
