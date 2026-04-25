<?php

use app\widgets\RemindersYearlyTableWidget;
use app\services\DictionaryService;

?>
<div class="grid grid-cols-1 md:grid-cols-1 gap-4">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow">
        <div class="flex flex-col space-y-1.5 p-6 pb-3">
            <div class="flex items-center gap-3 grid grid-cols-3">
                <div class="">
                    <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('yearlyReminders', $user->lang) ?></h3>
                </div>
                <div class="">
                </div>
                <div class="">
                    <button id="yearly-reminder-create" class="reminder-create btn btn-sm ml-2 p-3 rounded-md bg-primary border border-input font-medium inline-flex items-center justify-center text-sm text-primary-foreground whitespace-nowrap m3" style="float: right;">
                        <?= DictionaryService::getWord('createYearlyReminder', $user->lang) ?>
                    </button>
                </div>
            </div>
        </div>
        <?= RemindersYearlyTableWidget::widget([
            'user' => $user,
            'reminders' => $reminders
        ]) ?>
    </div>
</div>