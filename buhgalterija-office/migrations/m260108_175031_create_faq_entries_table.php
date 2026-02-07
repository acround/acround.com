<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%faq_entries}}`.
 */
class m260108_175031_create_faq_entries_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%faq_entries}}', [
            'id' => $this->primaryKey(),
            'question_ru' => $this->text()->notNull(),
            'answer_ru' => $this->text(),
            'question_rs' => $this->text(),
            'answer_rs' => $this->text(),
            'client_type' => $this->string(50),
            'embedding' => 'vector(768)',
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%faq_entries}}');
    }
}
