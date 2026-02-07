<?php

namespace app\components;

use yii\base\Widget;

class MainUserMenuWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('mainusermenu', [
            'user' => $this->user,
        ]);
    }
}
