<?php

use app\models\Event;
use yii\db\Migration;

class m251203_162057_add_chat_id_to_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Event::tableName(), 'chat_id', $this->bigInteger()->notNull()->defaultValue(0)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Event::tableName(), 'chat_id');
    }
}
