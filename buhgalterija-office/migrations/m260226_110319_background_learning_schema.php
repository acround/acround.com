<?php

use yii\db\Migration;

class m260226_110319_background_learning_schema extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_questions_background}}', [
            'id'           => $this->bigPrimaryKey(),
            'message_id'   => $this->integer(),
            'user_id'      => $this->bigInteger()->notNull(),
            'chat_id'      => $this->bigInteger()->notNull(),
            'topic'        => $this->integer(),
            'language'     => $this->string(2)->defaultValue('ru'),
            'username'     => $this->string(50),
            'firstname'    => $this->string(50),
            'lastname'     => $this->string(50),
            'question_ru'  => $this->text()->notNull(),
            'question_rs'  => $this->text()->notNull(),
            'answer_ru'    => $this->text(),
            'answer_rs'    => $this->text(),
            'embedding_ru' => 'vector(768)',
            'embedding_rs' => 'vector(768)',
            'faq_ids'      => 'bigint[]',
            'scores'       => 'float8[]',
            'best_score'   => $this->float(),
            'status'       => $this->string(16),
            'created_at'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('{{%idx-user_questions_background-user_id}}', '{{%user_questions_background}}', 'user_id');
        $this->createIndex('{{%idx-user_questions_background-chat_id}}', '{{%user_questions_background}}', 'chat_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_questions_background}}');
    }
}
