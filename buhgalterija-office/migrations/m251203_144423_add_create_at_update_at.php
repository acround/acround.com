<?php

use app\models\Accountant;
use app\models\Company;
use app\models\Customer;
use app\models\Document;
use app\models\Event;
use app\models\Reminder;
use app\models\Task;
use yii\db\Migration;

class m251203_144423_add_create_at_update_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Company::tableName(), 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Company::tableName(), 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Customer::tableName(), 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Customer::tableName(), 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Accountant::tableName(), 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Accountant::tableName(), 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Reminder::tableName(), 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Reminder::tableName(), 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Event::tableName(), 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Event::tableName(), 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Task::tableName(), 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Task::tableName(), 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Document::tableName(), 'created_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(Document::tableName(), 'updated_at', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Company::tableName(), 'updated_at');
        $this->dropColumn(Company::tableName(), 'created_at');
        $this->dropColumn(Customer::tableName(), 'updated_at');
        $this->dropColumn(Customer::tableName(), 'created_at');
        $this->dropColumn(Accountant::tableName(), 'updated_at');
        $this->dropColumn(Accountant::tableName(), 'created_at');
        $this->dropColumn(Reminder::tableName(), 'updated_at');
        $this->dropColumn(Reminder::tableName(), 'created_at');
        $this->dropColumn(Event::tableName(), 'updated_at');
        $this->dropColumn(Event::tableName(), 'created_at');
        $this->dropColumn(Task::tableName(), 'updated_at');
        $this->dropColumn(Task::tableName(), 'created_at');
        $this->dropColumn(Document::tableName(), 'updated_at');
        $this->dropColumn(Document::tableName(), 'created_at');
    }
}
