<?php

namespace app\widgets;

use yii\base\Widget;

class RemindersOneTimeTableWidget extends Widget
{
    public $user;
    public $reminders;

    public function run()
    {
        if (!$this->reminders) {
            $this->reminders = [];
        }
        return $this->render('onetimereminderstable', [
            'user' => $this->user,
            'reminders' => $this->reminders,
        ]);
    }
}
