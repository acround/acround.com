<?php

namespace app\components;

use yii\base\Widget;

class SettingsTopWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('settingstop', [
            'user' => $this->user,
        ]);
    }
}
