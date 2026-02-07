<?php

use yii\db\Migration;
use app\models\DocumentComment;

/**
 * Handles the creation of table `{{%document_comment}}`.
 */
class m251213_084810_create_document_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DocumentComment::tableName(), [
            'id' => $this->primaryKey(),
            'document_id' => $this->bigInteger()->notNull(),
            'accountant_id' => $this->bigInteger()->notNull(),
            'text' => $this->string(256),
            'created_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_document_comment_document_id', DocumentComment::tableName(), 'document_id');
        $this->createIndex('idx_document_comment_accountant_id', DocumentComment::tableName(), 'accountant_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_document_comment_document_id', DocumentComment::tableName()); 
        $this->dropIndex('idx_document_comment_accountant_id', DocumentComment::tableName());
        $this->dropTable(DocumentComment::tableName());
    }
}
