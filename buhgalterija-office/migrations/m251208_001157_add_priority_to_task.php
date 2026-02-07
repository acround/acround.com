<?php

use app\models\Task;
use yii\db\Migration;

class m251208_001157_add_priority_to_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Task::tableName(), 'priority', $this->string(16)->defaultValue('normal'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Task::tableName(), 'priority');
    }

}
