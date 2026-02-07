<?php

use yii\db\Migration;

class m260118_122617_embedding_indexes_for_faq_entries extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX idx_faq_entries_embedding_ru ON public.faq_entries USING hnsw (embedding_ru vector_cosine_ops)');
        $this->execute('CREATE INDEX idx_faq_entries_embedding_rs ON public.faq_entries USING hnsw (embedding_rs vector_cosine_ops)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_faq_entries_embedding_ru', 'public.faq_entries');
        $this->dropIndex('idx_faq_entries_embedding_rs', 'public.faq_entries');
    }
}
