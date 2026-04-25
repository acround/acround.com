<?php

namespace app\widgets;

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
