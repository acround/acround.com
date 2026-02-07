<?php

use yii\db\Migration;

class m260111_092242_create_faq_structure extends Migration
{
    public function safeUp()
    {
        // 1. Установка расширения vector
        $this->execute('CREATE EXTENSION IF NOT EXISTS vector;');

        // 2. Таблица faq
        $this->createTable('{{%faq}}', [
            'id' => $this->primaryKey(),
            'question_ru' => $this->text()->notNull(),
            'answer_ru' => $this->text()->notNull(),
            'question_rs' => $this->text()->notNull(),
            'answer_rs' => $this->text()->notNull(),
            'embedding_ru' => 'vector(768)',
            'embedding_rs' => 'vector(768)',
            'client_type' => $this->string(20)->notNull()->defaultValue(''),
            'cluster_id' => $this->integer()->notNull()->defaultValue(0),
            'cluster_size' => $this->integer()->defaultValue(1),
            'variants' => $this->json(),
            'reviewed' => $this->boolean()->defaultValue(false),
            'status' => $this->string(20)->defaultValue('pending'),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        // 3. Таблица faq_raw
        $this->createTable('{{%faq_raw}}', [
            'id' => $this->primaryKey(),
            'question' => $this->text()->notNull(),
            'answer' => $this->text()->notNull(),
            'client_type' => $this->string(20)->notNull()->defaultValue(''),
            'cluster_id' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        // 4. Таблица faq_review_log
        $this->createTable('{{%faq_review_log}}', [
            'id' => $this->primaryKey(),
            'faq_id' => $this->integer(),
            'action' => $this->string(50),
            'reviewer_id' => $this->string(100),
            'old_data' => $this->json(),
            'new_data' => $this->json(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        // Внешний ключ для логов
        $this->addForeignKey(
            'fk-faq_review_log-faq_id',
            '{{%faq_review_log}}',
            'faq_id',
            '{{%faq}}',
            'id',
            'CASCADE'
        );

        // 5. Индексы
        // Семантический поиск pgvector
        $this->execute('CREATE INDEX ON {{%faq}} USING ivfflat (embedding_ru vector_cosine_ops) WITH (lists = 100);');
        $this->execute('CREATE INDEX ON {{%faq}} USING ivfflat (embedding_rs vector_cosine_ops) WITH (lists = 100);');

        // Полнотекстовый поиск (GIN)
        $this->execute("CREATE INDEX faq_question_ru_idx ON {{%faq}} USING GIN (to_tsvector('russian', question_ru));");
        $this->execute("CREATE INDEX faq_answer_ru_idx ON {{%faq}} USING GIN (to_tsvector('russian', answer_ru));");
        $this->execute("CREATE INDEX faq_question_rs_idx ON {{%faq}} USING GIN (to_tsvector('russian', question_rs));");
        $this->execute("CREATE INDEX faq_answer_rs_idx ON {{%faq}} USING GIN (to_tsvector('russian', answer_rs));");

        // Обычные индексы
        $this->createIndex('idx-faq-status_reviewed', '{{%faq}}', ['status', 'reviewed']);

        // 6. Триггер для updated_at
        $this->execute("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        $this->execute("
            CREATE TRIGGER update_faq_updated_at 
            BEFORE UPDATE ON {{%faq}}
            FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
        ");

        // 7. Функции
        $this->execute("
CREATE OR REPLACE FUNCTION public.find_similar_questions(
    query_embedding vector, 
    threshold double precision DEFAULT 0.8, 
    max_results integer DEFAULT 10
)
RETURNS TABLE(id integer, question_ru text, answer_ru text, question_rs text, answer_rs text, similarity double precision)
LANGUAGE plpgsql
AS \$function$
BEGIN
    RETURN QUERY
    SELECT 
        f.id,
        f.question_ru,
        f.answer_ru,
        f.question_rs,
        f.answer_rs,
        (1 - (f.embedding_ru <=> query_embedding))::FLOAT AS similarity
    FROM {{%faq}} f
    WHERE f.status = 'approved'
      -- Используем чистый оператор расстояния. 
      -- Если сходство > 0.8, то расстояние < 0.2 (1 - 0.8)
      AND (f.embedding_ru <=> query_embedding) < (1 - threshold)
    ORDER BY f.embedding_ru <=> query_embedding
    LIMIT max_results;
END;
\$function$;
        ");

        $this->execute("DROP FUNCTION IF EXISTS get_faq_stats();");
        $this->execute("
            CREATE OR REPLACE FUNCTION get_faq_stats()
            RETURNS TABLE (
                total_questions INTEGER,
                approved INTEGER,
                pending INTEGER,
                rejected INTEGER,
                avg_cluster_size NUMERIC
            ) AS $$
            BEGIN
                RETURN QUERY
                SELECT 
                    COUNT(*)::INTEGER,
                    COUNT(*) FILTER (WHERE status = 'approved')::INTEGER,
                    COUNT(*) FILTER (WHERE status = 'pending')::INTEGER,
                    COUNT(*) FILTER (WHERE status = 'rejected')::INTEGER,
                    AVG(cluster_size)
                FROM {{%faq}};
            END;
            $$ LANGUAGE plpgsql;
        ");

        // 8. Представление (View)
        $this->execute("
            CREATE OR REPLACE VIEW faq_view AS
            SELECT 
                id,
                question_ru,
                LEFT(answer_ru, 200) || CASE WHEN LENGTH(answer_ru) > 200 THEN '...' ELSE '' END AS answer_ru_preview,
                question_rs,
                LEFT(answer_rs, 200) || CASE WHEN LENGTH(answer_rs) > 200 THEN '...' ELSE '' END AS answer_rs_preview,
                cluster_size,
                jsonb_array_length(variants) AS variant_count,
                status,
                reviewed,
                created_at
            FROM {{%faq}}
            ORDER BY cluster_size DESC, created_at DESC;
        ");
    }

    public function safeDown()
    {
        $this->dropIndex('idx-faq-status_reviewed', '{{%faq}}');
        $this->dropIndex('faq_question_ru_idx', '{{%faq}}');
        $this->dropIndex('faq_answer_ru_idx', '{{%faq}}');
        $this->dropIndex('faq_question_rs_idx', '{{%faq}}');
        $this->dropIndex('faq_answer_rs_idx', '{{%faq}}');

        $this->execute('DROP VIEW IF EXISTS faq_view');
        $this->execute('DROP FUNCTION IF EXISTS get_faq_stats()');
        $this->execute('DROP FUNCTION IF EXISTS find_similar_questions(vector, FLOAT, INTEGER)');

        $this->dropTable('{{%faq_review_log}}');
        $this->dropTable('{{%faq_raw}}');

        // Удаляем триггер и функцию отдельно
        $this->execute('DROP TRIGGER IF EXISTS update_faq_updated_at ON {{%faq}}');
        $this->execute('DROP FUNCTION IF EXISTS update_updated_at_column()');

        $this->dropTable('{{%faq}}');

        // Расширение обычно не удаляют в safeDown, чтобы не затронуть другие таблицы,
        // но если нужно: $this->execute('DROP EXTENSION IF EXISTS vector CASCADE;');
    }
}
