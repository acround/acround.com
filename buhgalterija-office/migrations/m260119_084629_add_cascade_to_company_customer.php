<?php

use app\models\Company;
use app\models\CompanyCustomer;
use app\models\Customer;
use yii\db\Migration;

class m260119_084629_add_cascade_to_company_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Удаляем старые внешние ключи
        $this->dropForeignKey('company_customer_company_id_fkey', CompanyCustomer::tableName());
        $this->dropForeignKey('company_customer_customer_id_fkey', CompanyCustomer::tableName());

        // 2. Создаем новые внешние ключи с CASCADE
        $this->addForeignKey(
            'company_customer_company_id_fkey',
            CompanyCustomer::tableName(),
            'company_id',
            Company::tableName(),
            'id',
            'CASCADE', // ON DELETE
            'CASCADE'  // ON UPDATE
        );

        $this->addForeignKey(
            'company_customer_customer_id_fkey',
            CompanyCustomer::tableName(),
            'customer_id',
            Customer::tableName(),
            'id',
            'CASCADE', // ON DELETE
            'CASCADE'  // ON UPDATE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // В методе safeDown возвращаем всё как было (без CASCADE)
        $this->dropForeignKey('company_customer_company_id_fkey', CompanyCustomer::tableName());
        $this->dropForeignKey('company_customer_customer_id_fkey', CompanyCustomer::tableName());

        $this->addForeignKey(
            'company_customer_company_id_fkey',
            CompanyCustomer::tableName(),
            'company_id',
            Company::tableName(),
            'id',
            'RESTRICT', // Или null, если по умолчанию было так
            'RESTRICT'
        );

        $this->addForeignKey(
            'company_customer_customer_id_fkey',
            CompanyCustomer::tableName(),
            'customer_id',
            Customer::tableName(),
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }
}
