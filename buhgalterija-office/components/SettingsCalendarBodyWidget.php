<?php

namespace app\components;

use yii\base\Widget;

class SettingsCalendarBodyWidget extends Widget
{
    public $user;
    public $taxCalendar;

    public function run()
    {
        if (!$this->taxCalendar) {
            $this->taxCalendar = [];
        }
        return $this->render('settingscalendarbody', [
            'user' => $this->user,
            'taxCalendar' => $this->taxCalendar,
        ]);
    }
}
