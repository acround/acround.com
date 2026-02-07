<?php

use app\models\Document;
use app\models\DocumentType;
use yii\db\Migration;

class m251209_092641_document_types extends Migration
{
    private $types = [
        'unknown',
        'invoice',
        'bankStatement',
        'payroll',
        'contract',
        'taxReturn',
        'other',

    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DocumentType::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->unique(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        foreach ($this->types as $type) {
            $this->insert(DocumentType::tableName(), [
                'name' => $type,
            ]);
        }

        $this->addColumn(Document::tableName(), 'type_id', $this->integer()->defaultValue(1)->after('status'));
        $this->addForeignKey(
            'fk_documents_type_id',
            Document::tableName(),
            'type_id',
            DocumentType::tableName(),
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_documents_type_id', Document::tableName());
        $this->dropColumn(Document::tableName(), 'type_id');
        $this->dropTable(DocumentType::tableName());
    }
}
