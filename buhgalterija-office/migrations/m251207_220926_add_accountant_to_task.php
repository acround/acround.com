<?php

use app\models\Task;
use yii\db\Migration;

class m251207_220926_add_accountant_to_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Task::tableName(), 'accountant_id', $this->bigInteger()->after('company_id')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Task::tableName(), 'accountant_id');
    }
}
