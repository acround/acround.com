<?php

use app\components\TaskCommentListWidget;
use app\services\DictionaryService;

$comments = $task->getComments();
if (!$comments) {
    $comments = [];
}
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square h-5 w-5">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <?= DictionaryService::getWord('comments', $user->lang) ?> (<?= $comments ? count($comments) : 0 ?>)
        </h3>
    </div>
    <?= TaskCommentListWidget::widget([
        'task' => $task,
        'user' => $user,
        'comments' => $comments,
    ])
    ?>
</div>
</div>