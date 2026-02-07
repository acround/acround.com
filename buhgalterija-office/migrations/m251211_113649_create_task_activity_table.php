<?php

use yii\db\Migration;
use app\models\Accountant;
use app\models\Task;
use app\models\TaskActivity;
use app\models\TaskStep;

/**
 * Handles the creation of table `{{%task_activity}}`.
 */
class m251211_113649_create_task_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(TaskStep::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->insert(TaskStep::tableName(), ['name' => 'created']);
        $this->insert(TaskStep::tableName(), ['name' => 'assigned']);
        $this->insert(TaskStep::tableName(), ['name' => 'in_progress']);
        $this->insert(TaskStep::tableName(), ['name' => 'waiting']);
        $this->insert(TaskStep::tableName(), ['name' => 'overdue']);
        $this->insert(TaskStep::tableName(), ['name' => 'done']);
        $this->insert(TaskStep::tableName(), ['name' => 'closed']);
        $this->insert(TaskStep::tableName(), ['name' => 'archived']);
        $this->insert(TaskStep::tableName(), ['name' => 'priority_changed']);
        $this->insert(TaskStep::tableName(), ['name' => 'due_date_changed']);
        $this->createTable(TaskActivity::tableName(), [
            'id' => $this->primaryKey(),
            'task_id' => $this->bigInteger()->notNull(),
            'accountant_id' => $this->bigInteger()->notNull(),
            'step_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_task_activity_task_id', TaskActivity::tableName(), 'task_id');
        $this->createIndex('idx_task_activity_accountant_id', TaskActivity::tableName(), 'accountant_id');
        $this->addForeignKey(
            'fk_task_activity_task_id',
            TaskActivity::tableName(),
            'task_id',
            Task::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_task_activity_accountant_id',
            TaskActivity::tableName(),
            'accountant_id',
            Accountant::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_task_activity_step_id',
            TaskActivity::tableName(),
            'step_id',
            TaskStep::tableName(),
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_task_activity_task_id', TaskActivity::tableName());
        $this->dropForeignKey('fk_task_activity_accountant_id', TaskActivity::tableName());
        $this->dropForeignKey('fk_task_activity_step_id', TaskActivity::tableName());
        $this->dropIndex('idx_task_activity_task_id', TaskActivity::tableName());
        $this->dropIndex('idx_task_activity_accountant_id', TaskActivity::tableName());
        $this->dropTable(TaskStep::tableName());
        $this->dropTable(TaskActivity::tableName());
    }
}
