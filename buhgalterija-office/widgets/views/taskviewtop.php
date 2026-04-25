<?php

use app\models\Accountant;
use app\models\Task;
use app\services\DictionaryService;
use app\services\AuthService;
use app\services\SvgService;

/** @var app\models\Task $task */
/** @var app\models\Accountant $user */
?>
<div id="page-header" class="flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold"><?= $task->category ?></h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-destructive/10 text-destructive border-destructive/20"><?= $task->getStatusText($user->lang) ?></span>
                <?php
                $priorityWord = $task->getPriorityWord();
                $prioritySign = DictionaryService::$prioritySign[$task->priority];
                ?>
                <span class="inline-flex items-center gap-1 text-xs font-medium text-destructive">
                    <span>
                        <?= $prioritySign ?>
                    </span><?= DictionaryService::getWord($priorityWord, $user->lang) ?>
                </span>
            </div>
            <p class="text-muted-foreground"><?= $task->request ?></p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <?php
        if (AuthService::hasPermission($user, AuthService::PERMISSION_MANAGE_TASKS)) {
        ?>
            <button id="task-edit-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" data-id="<?= $task->id ?>">
                <?= SvgService::svg('edit') ?>
                <?= DictionaryService::getWord('edit', $user->lang) ?>
            </button>
        <?php
        }
        $completeButtonStatus = in_array($task->status, Task::getStatusesCompleted()) ? ' disabled' : '';
        ?>
            <button id="finish-task"<?= $completeButtonStatus ?> data-task-id="<?= $task->id ?>" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                <?= SvgService::svg('finish') ?>
                <?= DictionaryService::getWord('finishTask', $user->lang) ?>
            </button>
        <?php
        if ($user->rule == Accountant::RULE_CEO) {
            $arhiveButtonStatus = in_array($task->status, [Task::STATUS_DONE, Task::STATUS_CLOSED]) ? '' : ' disabled';
        ?>
            <button id="archive-task"<?= $arhiveButtonStatus ?> data-task-id="<?= $task->id ?>" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                <?= SvgService::svg('finish') ?>
                <?= DictionaryService::getWord('archiveTask', $user->lang) ?>
            </button>
        <?php
        }
        ?>
    </div>
</div>