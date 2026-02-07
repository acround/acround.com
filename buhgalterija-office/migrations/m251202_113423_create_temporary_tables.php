<?php

use yii\db\Migration;

class m251202_113423_create_temporary_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('client_questions', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'tg_id' => $this->integer()->notNull(),
            'question' => $this->string(512)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('client_questions_tg_id_index', 'client_questions', 'tg_id');

        $this->createTable('steps', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'tg_id' => $this->integer()->notNull(),
            'step' => $this->string(32)->notNull(),
            'type' => $this->string(32)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('steps_tg_id_index', 'steps', 'tg_id');

        $this->createTable('log', [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'tg_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('log_tg_id_index', 'log', 'tg_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('client_questions_tg_id_index', 'client_questions');
        $this->dropIndex('steps_tg_id_index', 'steps');
        $this->dropIndex('log_tg_id_index', 'log');
        $this->dropTable('client_questions');
        $this->dropTable('steps');
        $this->dropTable('log');
    }
}
