<?php

namespace app\components;

use app\models\Company;
use app\models\CompanyCustomer;
use app\models\Customer;
use app\models\Document;
use yii\base\Widget;
use yii\db\Query;

class CompanyChatWidget extends Widget
{
    public $user;
    public $company;

    public function run()
    {
        $customerQuery = (new Query())
            ->select('cr.*')
            ->from(['c' => Company::tableName()])
            ->leftJoin(['cc' => CompanyCustomer::tableName()], 'cc.company_id = c.id')
            ->leftJoin(['cr' => Customer::tableName()], 'cr.id = cc.customer_id')
            ->where(['c.id' => $this->company->id]);
        $customer = $customerQuery->one();
        return $this->render('companychat', [
            'user' => $this->user,
            'company' => $this->company,
            'customer' => $customer,
        ]);
    }
}
