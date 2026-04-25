<?php

namespace app\widgets;

use yii\base\Widget;

class SettingsDedlinesWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('settingsdeadlines', [
            'user' => $this->user,
        ]);
    }
}
