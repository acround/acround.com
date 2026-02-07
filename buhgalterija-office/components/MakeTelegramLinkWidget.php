<?php

namespace app\components;

use yii\base\Widget;

class MakeTelegramLinkWidget extends Widget
{
    public $username;

    public function run()
    {
        return $this->render('maketelegramlink', [
            'username' => $this->username,
        ]);
    }
}
