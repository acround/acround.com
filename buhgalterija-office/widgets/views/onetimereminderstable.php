<?php

use app\widgets\ReminderOneTimeTableRowWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<table id="one-time-reminder-table" class="w-full table-auto">
    <thead>
        <tr class="border-t bg-muted/50">
            <th class="p-6 text-left text-sm font-semibold tracking-tight">ID</th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('deadlineDay', $user->lang)  ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('topic', $user->lang)  ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('text', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"></th>
        </tr>
    </thead>
    <tbody id="one-time-reminder-table-body">
        <?php
        foreach ($reminders as $item) {
            $class = ['reg-reminder-btn'];
            echo ReminderOneTimeTableRowWidget::widget([
                'user' => $user,
                'reminder' => $item,
                'class' => $class,
            ]);
        ?>
        <?php
        }
        ?>
    </tbody>
</table>