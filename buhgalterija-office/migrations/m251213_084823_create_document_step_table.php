<?php

use yii\db\Migration;
use app\models\DocumentStep;
use app\models\Document;

/**
 * Handles the creation of table `{{%document_step}}`.
 */
class m251213_084823_create_document_step_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DocumentStep::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'created_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_document_step_name',DocumentStep::tableName(),'name', true);
        $this->insert(DocumentStep::tableName(), ['name' => Document::STATUS_UPLOADED, 'created_at' => date('Y-m-d H:i:s')]);
        $this->insert(DocumentStep::tableName(), ['name' => Document::STATUS_CHECKED, 'created_at' => date('Y-m-d H:i:s')]);
        $this->insert(DocumentStep::tableName(), ['name' => Document::STATUS_NEEDS_REVISION, 'created_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(DocumentStep::tableName());
    }
}
