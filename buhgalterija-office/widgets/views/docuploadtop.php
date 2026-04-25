<?php

use app\services\DictionaryService;
?>
<div id="page-header" class="flex items-center gap-4">
    <div class="flex-1">
        <h1 class="text-2xl font-heading font-semibold text-foreground"><?= DictionaryService::getWord('uploadDocuments', $user->lang) ?></h1>
    </div>
</div>