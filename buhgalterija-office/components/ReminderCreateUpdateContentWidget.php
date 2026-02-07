<?php

namespace app\components;

use yii\base\Widget;

class ReminderCreateUpdateContentWidget extends Widget
{
    public $user;
    public $reminder;

    public function run()
    {
        return $this->render('remindercreateupdatecontent', [
            'user' => $this->user,
            'reminder' => $this->reminder,
        ]);
    }
}
