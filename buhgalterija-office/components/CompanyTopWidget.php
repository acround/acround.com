<?php

namespace app\components;

use app\models\CompanyNotes;
use yii\base\Widget;

class CompanyTopWidget extends Widget
{
    public $user;
    public $company;
    public $customers;

    public function run()
    {
        return $this->render('companytop', [
            'user' => $this->user,
            'company' => $this->company,
            'customers' => $this->customers,
        ]);
    }
}
