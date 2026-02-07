<?php

namespace app\components;

use yii\base\Widget;

class ModalEditCalendarWidget extends Widget
{
    public $user;
    public $token;

    public function run()
    {
        return $this->render('modaleditcalendar', [
            'user' => $this->user,
            'token' => $this->token,
        ]);
    }
}
