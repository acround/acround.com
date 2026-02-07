<?php

use yii\db\Migration;

class m260112_124750_another_embedding_to_faq_entries extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%faq_entries}}', 'embedding', 'embedding_ru');
        $this->addColumn('{{%faq_entries}}', 'embedding_rs', 'vector(768)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%faq_entries}}', 'embedding_rs');
        $this->renameColumn('{{%faq_entries}}', 'embedding_ru', 'embedding');
    }
}
