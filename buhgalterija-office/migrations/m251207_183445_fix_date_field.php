<?php

use app\models\Company;
use app\models\Reminder;
use app\models\Task;
use yii\db\Migration;

class m251207_183445_fix_date_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn(Task::tableName(), 'due_date');
        $this->addColumn(Task::tableName(), 'due_date', $this->dateTime()->null());
        $this->dropColumn(Company::tableName(), 'report_date');
        $this->addColumn(Company::tableName(), 'report_date', $this->dateTime()->null());
        $this->dropColumn(Reminder::tableName(), 'send_date');
        $this->addColumn(Reminder::tableName(), 'send_date', $this->dateTime()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

}
