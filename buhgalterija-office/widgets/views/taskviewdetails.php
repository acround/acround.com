<?php

use app\models\Task;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('taskDetails', $user->lang) ?></h3>
    </div>
    <div class="p-6 pt-0 space-y-4">
        <div>
            <h4 class="text-sm font-medium text-muted-foreground mb-1"><?= DictionaryService::getWord('description', $user->lang) ?></h4>
            <p class="text-sm"><?= $task->request ?></p>
        </div>
        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full"></div>
        <div class="grid grid-cols-2 gap-4">
            <?php
            if ($task->status == Task::STATUS_OVERDUE) {
            ?>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-destructive/10">
                        <?= SvgService::svg('clock-red') ?>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('dueDate', $user->lang) ?></p>
                        <p class="text-sm font-medium text-destructive"><?= date('Y-m-d', strtotime($task->due_date)) ?><span class="ml-2 text-xs">(<?= DictionaryService::getWord('overdue', $user->lang) ?>)</span></p>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-secondary">
                        <?= SvgService::svg('clock') ?>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('dueDate', $user->lang) ?></p>
                        <p class="text-sm font-medium "><?= date('Y-m-d', strtotime($task->due_date)) ?></p>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-secondary rounded-lg">
                    <?= SvgService::svg('person') ?>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('assignedTo', $user->lang) ?></p>
                    <p class="text-sm font-medium">
                        <?php
                        $accountant = $task->getAccountant();
                        if ($accountant) {
                            echo $accountant->firstname . ' ' . $accountant->lastname;
                        } else {
                            echo DictionaryService::getWord('notAssigned', $user->lang);
                        }
                        ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-secondary rounded-lg">
                    <?= SvgService::svg('clock') ?>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('lastUpdate', $user->lang) ?></p>
                    <p class="text-sm font-medium"><?= date('Y-m-d H:i:s', strtotime($task->updated_at)) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>