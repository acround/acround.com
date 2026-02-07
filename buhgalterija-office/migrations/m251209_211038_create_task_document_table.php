<?php

use app\models\TaskDocument;
use app\models\Document;
use app\models\Task;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%task_document}}`.
 */
class m251209_211038_create_task_document_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(TaskDocument::tableName(), [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'document_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey(
            'fk-task_document-task_id',
            TaskDocument::tableName(),
            'task_id',
            Task::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-task_document-document_id',
            TaskDocument::tableName(),
            'document_id',
            Document::tableName(),
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-task_document-task_id', TaskDocument::tableName());
        $this->dropForeignKey('fk-task_document-document_id', TaskDocument::tableName());
        $this->dropTable(TaskDocument::tableName());
    }
}
