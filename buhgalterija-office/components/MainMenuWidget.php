<?php

namespace app\components;

use yii\base\Widget;

class MainMenuWidget extends Widget
{
    public $user;
    public $menu;

    public function run()
    {
        return $this->render('mainmenu', [
            'user' => $this->user,
            'menu' => $this->menu,
        ]);
    }
}
