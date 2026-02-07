<?php

use yii\db\Migration;
use app\models\Accountant;
use app\models\Task;
use app\models\TaskComment;

/**
 * Handles the creation of table `{{%task_comment}}`.
 */
class m251211_160058_create_task_comments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(TaskComment::tableName(), [
            'id' => $this->primaryKey(),
            'task_id' => $this->bigInteger(),
            'accountant_id' => $this->bigInteger()->notNull(),
            'text' => $this->string(256),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_task_comments_task_id', TaskComment::tableName(), 'task_id');
        $this->createIndex('idx_task_comments_accountant_id', TaskComment::tableName(), 'accountant_id');
        $this->addForeignKey(
            'fk_task_comments_task_id',
            TaskComment::tableName(),
            'task_id',
            Task::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_task_comments_accountant_id',
            TaskComment::tableName(),
            'accountant_id',
            Accountant::tableName(),
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_task_comments_task_id', TaskComment::tableName());
        $this->dropForeignKey('fk_task_comments_accountant_id', TaskComment::tableName());
        $this->dropIndex('idx_task_comments_task_id', TaskComment::tableName());
        $this->dropIndex('idx_task_comments_accountant_id', TaskComment::tableName());
        $this->dropTable(TaskComment::tableName());
    }
}
