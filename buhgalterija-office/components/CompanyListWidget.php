<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class CompanyListWidget extends Widget
{
    public $user;
    public $companies = [];

    public function run()
    {
        return $this->render('companylist', [
            'user' => $this->user,
            'companies' => $this->companies,
        ]);
    }
}
