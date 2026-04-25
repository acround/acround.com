<?php

use app\models\Company;
use app\models\CompanyCustomer;
use app\models\CompanyType;
use app\models\Customer;
use yii\db\Migration;

class m260209_161452_companies_new_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $typePaushalac = CompanyType::findOne(['name' => 'Paušal']);
        if ($typePaushalac) {
            $typePaushalac->name = 'Paušalac';
            $typePaushalac->save();
        }
        $this->alterColumn(Customer::tableName(), 'tg_id', $this->bigInteger()->null());
        $this->alterColumn(Customer::tableName(), 'firstname', $this->string(32)->null());
        $this->alterColumn(Customer::tableName(), 'lastname', $this->string(32)->null());
        $this->alterColumn(Customer::tableName(), 'username', $this->string(128)->null());
        $this->alterColumn(Company::tableName(), 'name_tg', $this->string(512)->null());
        $this->alterColumn(Company::tableName(), 'pib', $this->bigInteger()->null());
        $this->alterColumn(Company::tableName(), 'activity_id', $this->bigInteger()->null());
        $fileName = __DIR__ . '/company_all_new.json';
        $file = json_decode(file_get_contents($fileName), true);
        foreach ($file as $item) {
            $new = [
                'name' => $item['new']['company'],
                'pib' => $item['new']['pib'],
            ];
            $customerData = [
                'username' => $item['new']['username'],
                'tg_id' => $item['new']['tg_id'],
            ];
            $customer = Customer::find()
                ->where(
                    [
                        'OR',
                        ['username' => $customerData['username']],
                        ['tg_id' => $customerData['tg_id']]
                    ]
                )
                ->one();
            if (!$customer) {
                $customerData['status'] = Customer::CUSTOMER_STATUS_ACTIVE;
                $customer = new Customer($customerData);
                $customer->save();
                if ($customer->hasErrors()) {
                    print_r([$customer->toArray(), $customer->getErrors()]);
                }
            }
            $oldName = $item['old'];
            $company = null;
            $company = Company::findOne(['name' => $oldName]);
            if (!$company) {
                $company = Company::findOne(['name' => $item['new']['company']]);
                if (!$company) {
                    $company = new Company();
                }
                $company->name = $item['new']['company'];
                $company->pib = $item['new']['pib'];
                $type = CompanyType::findOne(['name' => $item['new']['type']]);
                $company->type_id = $type->id;
                $company->status = Company::COMPANY_STATUS_ACTIVE;
                $company->save();
                if ($company->hasErrors()) {
                    print_r([$company->toArray(), $company->getErrors()]);
                }
            } else {
                $this->update(Company::tableName(), $new, ['name' => $oldName]);
            }
            if ($company->id && $customer->id) {
                $companyCustomer = CompanyCustomer::find()
                    ->where(
                        [
                            'company_id' => $company->id,
                            'customer_id' => $customer->id
                        ]
                    )
                    ->one();
                if (!$companyCustomer) {
                    $companyCustomer = new CompanyCustomer([
                        'company_id' => $company->id,
                        'customer_id' => $customer->id,
                    ]);
                    $companyCustomer->save();
                }
                if ($companyCustomer->hasErrors()) {
                    print_r([$companyCustomer->toArray(), $companyCustomer->getErrors()]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {}
}
