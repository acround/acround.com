<?php

namespace app\components;

use yii\base\Widget;

class ModalCreateRegReminderWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('modalcreatereminder', [
            'user' => $this->user,
        ]);
    }
}
