<?php

use yii\db\Migration;

class m260120_134736_event_table_modify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('event','company_id');
        $this->addColumn('event', 'username', $this->string(64));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'username');
        $this->addColumn('event', 'company_id', $this->integer(11));
    }

}
