<?php

namespace app\widgets;

use app\models\TaxCalendar;
use yii\base\Widget;

class RemindersYearlyTableWidget extends Widget
{
    public $user;
    public $reminders;

    public function run()
    {
        if (!$this->reminders) {
            $this->reminders = [];
        }
        return $this->render('yearlyreminderstable', [
            'user' => $this->user,
            'reminders' => $this->reminders,
        ]);
    }
}
