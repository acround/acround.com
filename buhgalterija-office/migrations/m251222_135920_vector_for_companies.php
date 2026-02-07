<?php

use app\models\Company;
use yii\db\Migration;

class m251222_135920_vector_for_companies extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Company::tableName(),"embedding", 'vector(768)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Company::tableName(), "embedding");
    }

}
