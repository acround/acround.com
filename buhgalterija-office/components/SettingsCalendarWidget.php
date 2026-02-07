<?php

namespace app\components;

use yii\base\Widget;

class SettingsCalendarWidget extends Widget
{
    public $user;
    public $taxCalendar;
    public $month;
    public $year;
    public $monthList;

    public function run()
    {
        if (!$this->taxCalendar) {
            $this->taxCalendar = [];
        }
        return $this->render('settingscalendar', [
            'user' => $this->user,
            'taxCalendar' => $this->taxCalendar,
            'month' => $this->month,
            'year' => $this->year,
            'monthList' => $this->monthList,
        ]);
    }
}
