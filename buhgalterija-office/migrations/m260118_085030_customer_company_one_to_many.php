<?php

use yii\db\Migration;
use yii\db\Query;

class m260118_085030_customer_company_one_to_many extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company_customer', [
            'id' => 'BIGSERIAL PRIMARY KEY',
            'company_id' => $this->integer(),
            'customer_id' => $this->integer(),
            'FOREIGN KEY(company_id) REFERENCES company(id)',
            'FOREIGN KEY(customer_id) REFERENCES customer(id)',
        ]);
        $query = (new Query)
            ->select(['customer_id' => 'id', 'company_id'])
            ->from('customer');
        $rows = $query->all($this->db);
        foreach($rows as $row) {
            $this->insert('company_customer', $row);
        }
        $this->dropColumn('customer', 'company_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('customer', 'company_id', $this->integer());
        $query = (new Query)
            ->select(['customer_id', 'company_id'])
            ->from('company_customer');
        $rows = $query->all($this->db);
        print_r($rows);
        foreach($rows as $row) {
            $u = $this->update('customer', ['company_id' => $row['company_id']], ['id' => $row['customer_id']]);
            var_dump($u);
        }
        $this->dropTable('company_customer');
    }
}
