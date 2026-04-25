<?php

use app\services\DictionaryService;
use app\services\SvgService;

?>
<div id="dropArea" class="cursor-pointer" style="border: 2px dashed #ccc; padding: 20px;" data-task-id="<?= $taskId ?>">
    <p class="text-sm text-muted-foreground">
    <form id="uploadForm" action="/document/upload" method="post" enctype="multipart/form-data">
        <input type="hidden" name="task_id" value="<?= $taskId ?>" />
        <input type="file" name="document" id="document-to-upload" class="sr-only" data-task-id="<?= $taskId ?>">
        <label for="document-to-upload" class="cursor-pointer">
            <span class="text-muted-foreground"><?= DictionaryService::getWord('dragDropCaption', $user->lang) ?></span>
        </label>
    </form>
    </p>
</div>