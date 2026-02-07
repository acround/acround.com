<?php

use app\components\RegRemindersTableWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="grid grid-cols-1 md:grid-cols-1 gap-4">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow">
        <div class="flex flex-col space-y-1.5 p-6 pb-3">
            <div class="flex items-center gap-3 grid grid-cols-3">
                <div class="">
                    <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('reminders', $user->lang) ?></h3>
                </div>
                <div class="">
                </div>
                <div class="">
                    <button id="reg-reminder-create" class="btn btn-sm ml-2 p-3 rounded-md bg-primary border border-input font-medium inline-flex items-center justify-center text-sm text-primary-foreground whitespace-nowrap m3" style="float: right;"><?= DictionaryService::getWord('createReminder', $user->lang) ?></button>
                </div>
            </div>
        </div>
        <?= RegRemindersTableWidget::widget([
            'user' => $user,
            'reminders' => $reminders
        ]) ?>
    </div>
</div>