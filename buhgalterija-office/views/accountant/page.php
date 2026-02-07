<?php

use app\components\SelectWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="col-span-1 space-y-3">
    <div class="rounded-lg text-card-foreground text-sm font-medium" data-v0-t="card">
        <div class="p-2">
            <h2 class="text-2xl font-heading font-bold"><?= $accountant->firstname . ' ' . $accountant->lastname ?></h2>
            <table class="accountant-page table-auto w-full">
                <tbody>
                    <tr>
                        <th class="align-left px-4 py-2" style="width:20%;">Email</th>
                        <td class="align-left px-4 py-2">
                            <?= $accountant->email ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="align-left px-4 py-2"><?= DictionaryService::getWord('role', $user->lang) ?></th>
                        <td class="align-left px-4 py-2"><?= DictionaryService::getWord($accountant->rule, $user->lang) ?></td>
                    </tr>
                    <tr>
                        <th class="align-left px-4 py-2"><?= DictionaryService::getWord('language', $user->lang) ?></th>
                        <td class="align-left px-4 py-2"><?= DictionaryService::getWord($accountant->lang, $user->lang) ?></td>
                    </tr>
                </tbody>
            </table>
            <button id="accountant-edit-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2" data-id="<?= $accountant->id ?>">
                <?= SvgService::svg('edit') ?>
                <?= DictionaryService::getWord('edit', $user->lang) ?>
            </button>
        </div>
    </div>
</div>