<?php

use app\services\DictionaryService;
use app\services\SvgService;
$topic = $user->lang == 'ru' ? 'type_ru' : 'type_rs';
$text = $user->lang == 'ru' ? 'text_ru' : 'text_rs';
?>
<tr id="reg-reminder-row-<?= $reminder['id'] ?>" class="<?= implode(' ', $class) ?>" data-item-id="<?= $reminder['id'] ?>">
    <td class="p-6 pt-0"><?= $reminder['id'] ?></td>
    <td class="p-6 pt-0"><?= $reminder['deadline_day'] ?></td>
    <td class="p-6 pt-0"><?= $reminder[$topic] ?></td>
    <td class="p-6 pt-0"><?= $reminder[$text] ?></td>
    <td class="reg-reminder-activity p-6 pt-0" style="white-space: nowrap;">
        <button class="company-reg-reminder-btn inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 mt-4" data-item-id="<?= $reminder['id'] ?>" title="<?= DictionaryService::getWord('companies', $user->lang) ?>">
            <?= SvgService::svg('taskList') ?>
        </button>
        <button class="edit-reg-reminder-btn inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 mt-4" data-item-id="<?= $reminder['id'] ?>" title="<?= DictionaryService::getWord('edit', $user->lang) ?>">
            <?= SvgService::svg('edit') ?>
        </button>
        <button class="cancel-reg-reminder-btn p-1 rounded-md bg-primary border border-input disabled:opacity-50 disabled:pointer-events-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-ring font-medium gap-2 h-9 hover:bg-accent hover:text-accent-foreground inline-flex items-center justify-center mt-4 px-3 ring-offset-background [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg]:size-4 text-sm text-primary-foreground transition-colors whitespace-nowrap" data-item-id="<?= $reminder['id'] ?>" title="<?= DictionaryService::getWord('delete', $user->lang) ?>">
            <?= SvgService::svg('delete') ?>
        </button>
    </td>
</tr>