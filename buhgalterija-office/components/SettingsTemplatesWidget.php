<?php

namespace app\components;

use yii\base\Widget;

class SettingsTemplatesWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('settingstemplates', [
            'user' => $this->user,
        ]);
    }
}
