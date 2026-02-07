<?php

use app\models\Accountant;
use yii\db\Migration;

class m251204_145550_add_token_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Accountant::tableName(), 'token', $this->string(32)->notNull()->defaultValue('')->after('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Accountant::tableName(), 'token');
    }
}
