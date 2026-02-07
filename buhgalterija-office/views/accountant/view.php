<?php

use app\components\ButtonBackWidget;
use app\components\TaskListWidget;
use app\services\DictionaryService;

?>
<div class="p-6">
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <?php
            // if ($back) {
            ?>
            <?= ButtonBackWidget::widget(['user' => $user]) ?>
            <?php
            // }
            ?>
            <div>
                <h1 class="text-2xl font-heading font-bold"><?= $accountant->firstname . ' ' . $accountant->lastname ?></h1>
                <p class="text-muted-foreground mt-1"><?= count($tasks) ?> <?= DictionaryService::getWord('tasks', $user->lang) ?></p>
            </div>
        </div>
        <div class="border rounded-lg overflow-hidden">
            <div class="relative w-full overflow-auto">
                <?= TaskListWidget::widget(['user' => $user, 'tasks' => $tasks]) ?>
            </div>
        </div>
    </div>
</div>