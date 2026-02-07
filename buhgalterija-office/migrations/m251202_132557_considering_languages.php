<?php

use app\models\Accountant;
use app\models\Customer;
use yii\db\Migration;

class m251202_132557_considering_languages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Customer::tableName(), 'lang', $this->string(8)->notNull()->defaultValue('ru'));
        $this->addColumn(Accountant::tableName(), 'lang', $this->string(8)->notNull()->defaultValue('ru'));
        $this->dropColumn(Accountant::tableName(), 'username');
        $this->addColumn(Accountant::tableName(), 'email', $this->string(64)->notNull());
        $this->addColumn(Accountant::tableName(), 'password', $this->string(64)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Customer::tableName(), 'lang');
        $this->dropColumn(Accountant::tableName(), 'lang');
        $this->dropColumn(Accountant::tableName(), 'email');
        $this->dropColumn(Accountant::tableName(), 'password');
        $this->addColumn(Accountant::tableName(), 'username', $this->string(32));
    }
}
