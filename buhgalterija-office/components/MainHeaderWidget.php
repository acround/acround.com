<?php

namespace app\components;

use yii\base\Widget;

class MainHeaderWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('mainheader', [
            'user' => $this->user,
        ]);
    }
}
