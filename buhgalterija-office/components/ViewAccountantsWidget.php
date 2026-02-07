<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class ViewAccountantsWidget extends Widget
{
    // public $accountant;
    public $data;
    public $user;

    public function run()
    {
        return $this->render('viewaccountants', [
            // 'accountant' => $this->accountant,
            'data' => $this->data,
            'user'=> $this->user,
        ]);
    }
}
