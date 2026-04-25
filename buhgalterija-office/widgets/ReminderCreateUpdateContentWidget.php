<?php

namespace app\widgets;

use yii\base\Widget;

class ReminderCreateUpdateContentWidget extends Widget
{
    public $user;
    public $reminder;
    public $type;

    public function run()
    {
        return $this->render('remindercreateupdatecontent', [
            'user' => $this->user,
            'reminder' => $this->reminder,
            'type' => $this->type,
        ]);
    }
}
