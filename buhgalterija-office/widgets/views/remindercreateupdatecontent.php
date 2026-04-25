<?php

use app\services\DictionaryService;

$topic = '';
$text = '';
switch ($user->lang) {
    case DictionaryService::LANG_RUSSIAN:
        $topic = $reminder->type_ru;
        $text = $reminder->text_ru;
        break;

    case DictionaryService::LANG_SERBIAN:
        $topic = $reminder->type_rs;
        $text = $reminder->text_rs;
        break;

    default:
        $topic = $reminder->type_rs;
        $text = $reminder->text_rs;
        break;
}
?>
<input type="hidden" name="reminderId" value="<?= $reminder->id ?>" />
<input type="hidden" name="reminder-lang" value="<?= $user->lang ?>" />
<input type="hidden" name="reminder-type" value="<?= $type ?>" />
<table class="w-full table-auto">
    <tr class="bg-muted/50">
        <th class="p-6 text-left text-sm font-semibold tracking-tight" width="20%"><?= DictionaryService::getWord('reminderDate', $user->lang) ?>:</th>
        <td class="p-6 pt-0">
            <?php
            switch ($type) {
                case 'regular':
            ?>
                    <input class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" style="width: 100%;" name="deadlineDay" type="text" value="<?= $reminder->deadline_day ?>" />
                <?php
                    break;
                case 'yearly':
                    $day = $reminder->deadline_day;
                    if ($day < 10) {
                        $day = '0' . $day;
                    }
                    $month = $reminder->deadline_month;
                    if ($month < 10) {
                        $month = '0' . $month;
                    }
                    $date = date('Y') . '-' . $month . '-' . $day;
                ?>
                    <input class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" style="width: 100%;" name="deadlineDay" type="date" value="<?= $date ?>" />
                <?php
                    break;
                case 'one-time':
                ?>
                    <input class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" style="width: 100%;" name="deadlineDay" type="date" value="<?= $reminder->deadline ?>" />
            <?php
                    break;
            }
            ?>
        </td>
    </tr>
    <tr class="bg-muted/50">
        <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('topic', $user->lang) ?>:</th>
        <td class="p-6 pt-0"><input class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" style="width: 100%;" name="topic" type="text" value="<?= $topic ?>" /></td>
    </tr>
    <tr class="bg-muted/50">
        <th class="p-6 text-left text-sm font-semibold tracking-tight"><?= DictionaryService::getWord('text', $user->lang) ?>:</th>
        <td class="p-6 pt-0"><input class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" style="width: 100%;" name="text" type="text" value="<?= $text ?>" /></td>
    </tr>
</table>