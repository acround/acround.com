<?php

use app\components\SelectWidget;
use app\services\DictionaryService;
use app\services\SvgService;

$title = $accountant->id ? 'accountantEdit' : 'accountantCreate';
?>
<div class="col-span-1 space-y-3">
    <div class="rounded-lg text-card-foreground text-sm font-medium" data-v0-t="card">
        <div class="p-2">
            <h2 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord($title, $user->lang) ?></h2>
            <table class="accountant-page table-auto w-full">
                <tbody>
                    <tr>
                        <th class="align-left px-4 py-2" style="width:20%;">Email</th>
                        <td class="align-left px-4 py-2">
                            <input type="text" name="accountant-email" class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" value="<?= $accountant->email ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th class="align-left px-4 py-2" style="width:20%;"><?= DictionaryService::getWord('firstName', $user->lang) ?></th>
                        <td class="align-left px-4 py-2">
                            <input type="text" name="accountant-firstname" class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" value="<?= $accountant->firstname ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th class="align-left px-4 py-2" style="width:20%;"><?= DictionaryService::getWord('lastName', $user->lang) ?></th>
                        <td class="align-left px-4 py-2">
                            <input type="text" name="accountant-lastname" class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" value="<?= $accountant->lastname ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th class="align-left px-4 py-2"><?= DictionaryService::getWord('role', $user->lang) ?></th>
                        <td class="align-left px-4 py-2">
                            <?= SelectWidget::widget([
                                'user' => $user,
                                'id' => 'accountant-role',
                                'options' => [
                                    ['id' => 'accountant', 'name' => DictionaryService::getWord('accountant', $user->lang)],
                                    ['id' => 'admin', 'name' => DictionaryService::getWord('admin', $user->lang)],
                                    ['id' => 'ceo', 'name' => DictionaryService::getWord('ceo', $user->lang)],
                                ],
                                'selected' => $accountant->rule
                            ]) ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="align-left px-4 py-2"><?= DictionaryService::getWord('language', $user->lang) ?></th>
                        <td class="align-left px-4 py-2">
                            <?= SelectWidget::widget([
                                'user' => $user,
                                'id' => 'accountant-lang',
                                'options' => [
                                    ['id' => 'ru', 'name' => DictionaryService::getWord('ru', $user->lang)],
                                    ['id' => 'rs', 'name' => DictionaryService::getWord('rs', $user->lang)],
                                    ['id' => 'en', 'name' => DictionaryService::getWord('en', $user->lang)],
                                ],
                                'selected' => $accountant->lang
                            ]) ?>
                        </td>
                    </tr>
                    <?php
                    if (!$accountant->id) {
                    ?>
                    <tr>
                        <th class="align-left px-4 py-2"><?= DictionaryService::getWord('password', $user->lang) ?></th>
                        <td class="align-left px-4 py-2">
                            <input type="text" name="accountant-password" class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" value="<?= $accountant->password ?>" />
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <button id="accountant-save-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2" data-id="<?= $accountant->id ?>">
                <?= SvgService::svg('finish') ?>
                <?= DictionaryService::getWord('save', $user->lang) ?>
            </button>
            <button id="accountant-cancel-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" data-id="<?= $accountant->id ?>">
                <?= SvgService::svg('x') ?>
                <?= DictionaryService::getWord('cancel', $user->lang) ?>
            </button>
        </div>
    </div>
</div>