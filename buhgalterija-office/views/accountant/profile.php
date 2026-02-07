<?php

use app\components\ButtonBackWidget;
use app\services\DictionaryService;

?>
<div class="p-6">
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <?= ButtonBackWidget::widget(['user' => $user]) ?>
            <div>
                <h1 class="text-2xl font-heading font-bold"><?= $user->firstname . ' ' . $user->lastname ?></h1>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4">
            <div>
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight"><?= DictionaryService::getWord('email', $user->lang) ?></h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-2"><?= $user->email ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="whitespace-nowrap text-2xl font-semibold leading-none tracking-tight"><?= DictionaryService::getWord('password', $user->lang) ?></h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-2">&bull;&bull;&bull;&bull;&bull;&bull;</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1">
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <button type="button" class="go-to-link whitespace-nowrap text-2xl font-semibold leading-none tracking-tight" data-link="/accountant/edit/<?= $user->id ?>"><?= DictionaryService::getWord('edit', $user->lang) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>