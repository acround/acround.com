<?php

use app\components\SettingsCalendarWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>

<div id="reminders-button-list" role="tablist" aria-orientation="horizontal" class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground" tabindex="0" data-orientation="horizontal" style="outline: none;">
    <button id="tax-calendar-button" type="button" data-link="/reminder/tax-calendar" data-controls="tax-calendar-div" data-state="active" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="0" data-orientation="horizontal" data-radix-collection-item="">
        <?= SvgService::svg('reminder') ?>
        <span>
            <?= DictionaryService::getWord("taxCalendar", $user->lang) ?>
        </span>
    </button>
    <button id="reg-reminders-button" type="button" data-link="/reminder/reg" data-controls="reg-reminders-div" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
        <?= SvgService::svg('calendar') ?>
        <span>
            <?= DictionaryService::getWord("regularReminders", $user->lang) ?>
        </span>
    </button>
</div>
<div id="reminders-div-list">
    <div id="tax-calendar-div" data-state="active" data-orientation="horizontal" role="tabpanel" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 space-y-4">
        <?= SettingsCalendarWidget::widget([
            'user' => $user,
            'taxCalendar' => $taxCalendar,
            'month' => $month,
            'year' => $year,
            'monthList' => $monthList
        ]) ?>
    </div>
    <div id="reg-reminders-div" data-state="active" data-orientation="horizontal" role="tabpanel" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 space-y-4">
    </div>
</div>