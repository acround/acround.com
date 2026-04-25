<?php

namespace app\widgets;

use yii\base\Widget;

class ModalWindowWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('modalwindow', [
            'user' => $this->user,
        ]);
    }
}
