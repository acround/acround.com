<?php

namespace app\widgets;

use yii\base\Widget;

class ReminderOneTimeTableRowWidget extends Widget
{
    public $user;
    public $reminder;
    public $class = [];

    public function run()
    {
        return $this->render('reminderonetimetableRow', [
            'user' => $this->user,
            'reminder' => $this->reminder,
            'class' => $this->class,
        ]);
    }
}
