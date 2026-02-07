<?php

use app\models\ReminderSchedule;
use app\services\DictionaryService;
use app\services\SvgService;
?>
<div class="accordeon-item mb-4 flex items-center justify-between lg:grid-cols-2">
    <h3 class="font-semibold text-left cursor-pointer" style="padding-left: 20px; color:gray;"><?= DictionaryService::getWord('regularReminders', $user->lang) ?></h3>
    <?= SvgService::svg('arrow-down') ?>
</div>
<table class="accordeon-table w-full caption-bottom text-sm hidden">
    <thead class="border-b bg-secondary/50">
        <tr class="border-b transition-colors hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('topic', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('text', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('deadlineDay', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('activity', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('status', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('stopForThisMonth', $user->lang) ?></th>
        </tr>
    </thead>
    <tbody class="">
        <?php
        foreach ($regReminders as $reminder) {
            $checked = $reminder['schedule_id'] ? ' checked' : '';
            $statusLastNotified = '';
            switch ($reminder['last_notified_type']) {
                case 'first':
                    $statusLastNotified = DictionaryService::getWord('firstReminder', $user->lang);
                    break;
                case 'second':
                    $statusLastNotified = DictionaryService::getWord('secondReminder', $user->lang);
                    break;
                case 'escalation':
                    $statusLastNotified = DictionaryService::getWord('escalation', $user->lang);
                    break;
                default:
                    $statusLastNotified = DictionaryService::getWord('pending', $user->lang);
            }
            $status = $reminder['status'] ?? ReminderSchedule::STATUS_NOT_ASSIGNED;
            $buttonVisibility = [
                'stopBtn' => $status == ReminderSchedule::STATUS_PENDING ? '' : ' hidden',
                'stoppedBtn' => $status == ReminderSchedule::STATUS_STOPPED ? '' : ' hidden',
                'notAssignedBtn' => $status == ReminderSchedule::STATUS_NOT_ASSIGNED ? '' : ' hidden',
            ];
        ?>
            <tr data-tax-id="<?= $reminder['reminder_id'] ?>" class="tax-row border-b hover:bg-secondary/30 transition-colors cursor-pointer">
                <td class="p-4 align-middle">
                    <div class="flex items-center gap-2">
                        <div>
                            <p class="font-medium text-sm truncate max-w-[200px]"><?= $user->lang == 'ru' ? $reminder['type_ru'] : $reminder['type_rs'] ?></p>
                        </div>
                    </div>
                </td>
                <td class="p-4 align-middle text-sm"><?= $user->lang == 'ru' ? $reminder['text_ru'] : $reminder['text_rs'] ?></td>
                <td class="p-4 align-middle text-sm"><?= $reminder['deadline_day'] ?></td>
                <td class="p-4 align-middle">
                    <input class="reminder-activity" type="checkbox" <?= $checked ?> data-rm-id="<?= $reminder['reminder_id'] ?>" data-sc-id="<?= $reminder['schedule_id'] ?>" data-co-id="<?= $reminder['company_id'] ?>" data-type="rr">
                </td>
                <td class="p-4 align-middle text-sm"><?= $statusLastNotified ?></td>
                <td class="p-4 align-middle">
                    <button data-rm-id="<?= $reminder['reminder_id'] ?>" data-sc-id="<?= $reminder['schedule_id'] ?>" data-co-id="<?= $reminder['company_id'] ?>" data-type="rs" class="stop-reminder-btn<?= $buttonVisibility['stopBtn'] ?> inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        <?= SvgService::svg('stop') ?>
                        <?= DictionaryService::getWord('stop', $user->lang) ?>
                    </button>
                    <div class="stopped-reminder<?= $buttonVisibility['stoppedBtn'] ?> inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background h-10 px-4 py-2"><?= DictionaryService::getWord('stopped', $user->lang) ?></div>
                    <div class="not-assigned-reminder<?= $buttonVisibility['notAssignedBtn'] ?> inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background h-10 px-4 py-2"><?= DictionaryService::getWord('notAssigned', $user->lang) ?></div>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>

<div class="accordeon-item mb-4 flex items-center justify-between lg:grid-cols-2">
    <h3 class="font-semibold text-left cursor-pointer" style="padding-left: 20px; color:gray;"><?= DictionaryService::getWord('taxCalendar', $user->lang) ?></h3>
    <?= SvgService::svg('arrow-down') ?>
</div>
<table class="accordeon-table w-full caption-bottom text-sm hidden">
    <thead class="border-b bg-secondary/50">
        <tr class="border-b transition-colors hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('topic', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('text', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('deadlineDay', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('status', $user->lang) ?></th>
        </tr>
    </thead>
    <tbody class="">
        <?php
        foreach ($taxCalendarReminders as $reminder) {
            $checked = $reminder['schedule_id'] ? ' checked' : '';
        ?>
            <tr data-tax-id="<?= $reminder['reminder_id'] ?>" class="tax-row border-b hover:bg-secondary/30 transition-colors cursor-pointer">
                <td class="p-4 align-middle">
                    <div class="flex items-center gap-2">
                        <div>
                            <p class="font-medium text-sm truncate max-w-[200px]"><?= $user->lang == 'ru' ? $reminder['topic_ru'] : $reminder['topic_rs'] ?></p>
                        </div>
                    </div>
                </td>
                <td class="p-4 align-middle text-sm"><?= $user->lang == 'ru' ? $reminder['text_ru'] : $reminder['text_rs'] ?></td>
                <td class="p-4 align-middle text-sm"><?= $reminder['deadline_date'] ?></td>
                <td class="p-4 align-middle">
                    <input class="tax-activity" type="checkbox" <?= $checked ?> data-rm-id="<?= $reminder['reminder_id'] ?>" data-sc-id="<?= $reminder['schedule_id'] ?>" data-co-id="<?= $reminder['company_id'] ?>" data-type="tx">
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>