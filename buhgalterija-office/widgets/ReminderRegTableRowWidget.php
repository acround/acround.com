<?php

namespace app\widgets;

use app\models\TaxCalendar;
use yii\base\Widget;

class ReminderRegTableRowWidget extends Widget
{
    public $user;
    public $reminder;
    public $class = [];

    public function run()
    {
        return $this->render('regremindertablerow', [
            'user' => $this->user,
            'reminder' => $this->reminder,
            'class' => $this->class,
        ]);
    }
}
