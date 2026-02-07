<?php

use yii\db\Migration;

class m251207_090230_insert_rules extends Migration
{
    private $rules = [
        ['name' => 'ceo'],
        ['name' => 'admin'],
        ['name' => 'accountant'],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%auth_rule}}', 'created_at');
        $this->dropColumn('{{%auth_rule}}', 'updated_at');
        $this->addColumn('{{%auth_rule}}', 'created_at', 'timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP');
        $this->addColumn('{{%auth_rule}}', 'updated_at', 'timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP');
        foreach ($this->rules as $rule) {
            $this->insert('{{%auth_rule}}', $rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM auth_rule");
    }
}
