<?php

use app\widgets\DocUploadBodyWidget;
use app\widgets\TaskViewDocumentListWidget;
use app\models\Task;
use app\services\DictionaryService;

?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="space-y-1.5 p-6 flex flex-row items-center justify-between">
        <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('linkedDocuments', $user->lang) ?></h3>
        <?= ($task->status == Task::STATUS_ARCHIVED) ? '' : DocUploadBodyWidget::widget(['user' => $user, 'taskId' => $task ? $task->id : 0]) ?>
    </div>
    <div id="task-documents-list" class="p-6 pt-0">
        <?php
        $documents = $task ? $task->getDocuments() : [];
        echo TaskViewDocumentListWidget::widget(['task'=>$task, 'documents' => $documents, 'user' => $user]);
        ?>
    </div>
</div>