<?php

use yii\db\Migration;

class m260110_144642_vector_index_for_faq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = 'CREATE INDEX IF NOT EXISTS faq_entries_embedding_hnsw_cos ON faq_entries USING hnsw (embedding vector_cosine_ops)';
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('faq_entries_embedding_hnsw_cos', 'faq_entries');
    }

}
