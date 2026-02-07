<?php

use yii\db\Migration;
use app\models\DocumentActivity;

/**
 * Handles the creation of table `{{%document_activity}}`.
 */
class m251213_084834_create_document_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DocumentActivity::tableName(), [
            'id' => $this->primaryKey(),
            'document_id' => $this->bigInteger()->notNull(),
            'accountant_id' => $this->bigInteger()->notNull(),
            'step_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_document_activity_document_id', DocumentActivity::tableName(), 'document_id');
        $this->createIndex('idx_document_activity_accountant_id', DocumentActivity::tableName(), 'accountant_id');
        $this->createIndex('idx_document_activity_step_id', DocumentActivity::tableName(), 'step_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_document_activity_document_id', DocumentActivity::tableName());
        $this->dropIndex('idx_document_activity_accountant_id', DocumentActivity::tableName());
        $this->dropIndex('idx_document_activity_step_id', DocumentActivity::tableName());
        $this->dropTable(DocumentActivity::tableName());
    }
}
