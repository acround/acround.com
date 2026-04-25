<?php

use app\models\Task;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div id="task-comment-list" class="p-6 pt-0">
    <?php

    foreach ($comments as $comment) {
        $commentUser = $comment->getAccountant();
        $date = $comment->created_at;
        $date = date('d.m.Y', strtotime($date));
        $time = date('H:i', strtotime($date));
        $icon = strtoupper(substr($commentUser->firstname, 0, 1) . substr($commentUser->lastname, 0, 1));
        $timeAgo = Yii::$app->formatter->asRelativeTime($comment->created_at);
    ?>
        <div class="space-y-4">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-medium"><?= $icon ?></div>
                <div class="flex-1">
                    <div class="flex items-center gap-2"><span class="text-sm font-medium"><?= $commentUser->firstname, ' ', $commentUser->lastname ?></span><span class="text-xs text-muted-foreground"><?= $timeAgo ?></span></div>
                    <p class="text-sm text-muted-foreground mt-1"><?= $comment->text ?></p>
                </div>
            </div>
        </div>
    <?php
    }
    if ($task->status !== Task::STATUS_ARCHIVED) {
    ?>
        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full my-4"></div>
        <div class="flex gap-2">
            <input id="commentInput" type="text" placeholder="<?= DictionaryService::getWord('addComment', $user->lang) ?>…" class="flex-1 px-3 py-2 text-sm border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary/20">
            <button id="sendComment" data-task-id="<?= $task->id ?>" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50    bg-primary text-primary-foreground hover:bg-primary/90 h-10 w-10">
                <?= SvgService::svg('telegram-white') ?>
                </svg>
            </button>
        </div>
    <?php
    }
