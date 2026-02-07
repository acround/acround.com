<?php

use app\services\DictionaryService;
use app\services\SvgService;
?>
<table id="tax-calendar-table" class="w-full table-auto">
    <thead>
        <tr class="border-t bg-muted/50">
            <!-- <th class="p-6 text-left text-sm font-semibold tracking-tight">ID</th> -->
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('actionDate', $user->lang)  ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('reminderDate', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('activityType', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('reminderText', $user->lang) ?></th>
            <!-- <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('activityType', $user->lang) ?></th>
            <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('reminderText', $user->lang) ?></th> -->
            <th class="p-6 text-left text-sm font-semibold tracking-tight"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($taxCalendar as $item) {
        ?>
            <tr class="open-modal-btn" data-item-id="<?= $item->id ?>">
                <!-- <td class="p-6 pt-0"><?php //= $item->id 
                                            ?></td> -->
                <td class="calendar-action-date p-6 pt-0"><?= date('Y-m-d', strtotime($item->input_date)) ?></td>
                <td class="calendar-date p-6 pt-0"><?= date('Y-m-d', strtotime($item->reminder_1_date)) ?></td>
                <td class="calendar-activity-type p-6 pt-0"><?= $user->lang == 'rs' ? $item->activity_type_rs : $item->activity_type_ru ?></td>
                <td class="calendar-text p-6 pt-0"><?= $user->lang == 'rs' ? $item->activity_text_rs : $item->activity_text_ru ?></td>
                <!-- <td class="calendar-activity-type p-6 pt-0"><?= $item->activity_type_ru ?></td>
                <td class="calendar-text p-6 pt-0"><?= $item->activity_text_ru ?></td> -->
                <td class="calendar-activity p-6 pt-0" style="white-space: nowrap;">
                    <button class="company-tax-reminder-btn inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 mt-4" data-item-id="<?= $item['id'] ?>" title="<?= DictionaryService::getWord('companies', $user->lang) ?>">
                        <?= SvgService::svg('taskList') ?>
                    </button>
                    <button class="edit-calendar-btn inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 mt-4" data-item-id="<?= $item->id ?>" title="<?= DictionaryService::getWord('edit', $user->lang) ?>">
                        <?= SvgService::svg('edit') ?>
                    </button>
                    <button class="delete-calendar-btn p-1 rounded-md bg-primary border border-input disabled:opacity-50 disabled:pointer-events-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-ring font-medium gap-2 h-9 hover:bg-accent hover:text-accent-foreground inline-flex items-center justify-center mt-4 px-3 ring-offset-background [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg]:size-4 text-sm text-primary-foreground transition-colors whitespace-nowrap" data-item-id="<?= $item->id ?>" title="<?= DictionaryService::getWord('delete', $user->lang) ?>">
                        <?= SvgService::svg('delete') ?>
                    </button>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>