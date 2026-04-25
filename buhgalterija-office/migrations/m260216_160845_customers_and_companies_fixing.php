<?php

use app\models\Company;
use app\models\CompanyCustomer;
use yii\db\Migration;

class m260216_160845_customers_and_companies_fixing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $quazyPib = 0;
        $companiesFix = json_decode(file_get_contents(__DIR__ . '/companyFix.json'), true, 512, JSON_UNESCAPED_UNICODE);
        if (!empty($companiesFix)) {
            $this->db->createCommand()->delete(CompanyCustomer::tableName())->execute();
            foreach ($companiesFix as $companyJson) {
                $company = Company::find()->where(['name' => $companyJson['name']])->one();
                if (!$company) {
                    echo "Company with name {$companyJson['name']} not found\n";
                    continue;
                }
                if ($company->pib != $companyJson['pib']) {
                    $otherCompany = Company::find()->where(['pib' => $companyJson['pib']])->one();
                    if ($otherCompany) {
                        $otherCompany->pib = $quazyPib++;
                        if (!$otherCompany->save()) {
                            echo "Failed to update other company with name {$otherCompany->name} and pib {$otherCompany->pib}\n";
                            print_r($otherCompany->getErrors());
                        }
                    }
                    $company->pib = $companyJson['pib'];
                    if (!$company->save()) {
                        echo "Failed to update company with name {$companyJson['name']} and pib {$companyJson['pib']}\n";
                        print_r($company->getErrors());
                        echo "\n";
                    }
                }
                if (!isset($companyJson['customers']) || empty($companyJson['customers'])) {
                    echo "Company with name {$companyJson['name']} has no customers\n";
                    continue;
                }
                foreach ($companyJson['customers'] as $customerJson) {
                    $customer = \app\models\Customer::find()
                        ->where(['tg_id' => $customerJson['tg_id']])
                        ->orWhere(['username' => $customerJson['username']])
                        ->orWhere(['username' => trim($customerJson['username'], '@')])
                        ->one();
                    if (!$customer) {
                        $customer = new \app\models\Customer();
                        $customer->tg_id = $customerJson['tg_id'];
                        $customer->username = $customerJson['username'];
                        if (!$customer->save()) {
                            echo "Failed to save customer with tg_id {$customerJson['tg_id']} and username {$customerJson['username']}\n";
                            print_r($customer->getErrors());
                            echo "\n";
                            continue;
                        }
                    } else {
                        if ($customer->tg_id != $customerJson['tg_id'] || $customer->username != $customerJson['username']) {
                            $customer->tg_id = $customerJson['tg_id'];
                            $customer->username = $customerJson['username'];
                            if (!$customer->save()) {
                                echo "Failed to update customer with id {$customer->id} and tg_id {$customerJson['tg_id']} and username {$customerJson['username']}\n";
                                print_r($customer->getErrors());
                                echo "\n";
                                continue;
                            }
                        }
                    }
                    $companyCustomer = new CompanyCustomer();
                    $companyCustomer->company_id = $company->id;
                    $companyCustomer->customer_id = $customer->id;
                    if (!$companyCustomer->save()) {
                        echo "Failed to save company_customer with company_id {$company->id} and customer_id {$customer->id}\n";
                        print_r($companyCustomer->getErrors());
                        echo "\n";
                    }
                }
            }

            $this->db->createCommand()->update(Company::tableName(), ['status' => Company::COMPANY_STATUS_ACTIVE])->execute();
            $this->db->createCommand()->update(\app\models\Customer::tableName(), ['status' => \app\models\Customer::CUSTOMER_STATUS_ACTIVE])->execute();
            $query = (new \yii\db\Query())
                ->select(['c.id as cid', 'cc.id as ccid'])
                ->from(['c' => Company::tableName()])
                ->leftJoin(['cc' => CompanyCustomer::tableName()], 'cc.company_id = c.id')
                ->where(['cc.id' => null]);
            $command = $query->createCommand();
            $result = $command->queryAll();
            if (!empty($result)) {
                echo "Companies with no customers:\n";
                foreach ($result as $row) {
                    $this->update(Company::tableName(), ['status' => Company::COMPANY_STATUS_ONBOARDING], ['id' => $row['cid']]);
                }
            }
            $this->db->createCommand()->update(Company::tableName(), ['status' => Company::COMPANY_STATUS_INACTIVE], 'pib IS NULL OR pib = 0')->execute();

            $query = (new \yii\db\Query())
                ->select(['c.id as cid', 'cc.id as ccid'])
                ->from(['c' => \app\models\Customer::tableName()])
                ->leftJoin(['cc' => CompanyCustomer::tableName()], 'cc.customer_id = c.id')
                ->where(['cc.id' => null]);
            $command = $query->createCommand();
            $result = $command->queryAll();
            if (!empty($result)) {
                echo "Customers with no companies:\n";
                foreach ($result as $row) {
                    $this->update(\app\models\Customer::tableName(), ['status' => \app\models\Customer::CUSTOMER_STATUS_INACTIVE], ['id' => $row['cid']]);
                }
            }
            echo "Companies and customers fixed\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {}
}
