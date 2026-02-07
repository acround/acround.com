<?php

use app\components\ButtonBackWidget;
use app\services\DictionaryService;
?>
<div class="flex items-center gap-4">
    <?= ButtonBackWidget::widget(['user' => $user]) ?>
    <div class="flex-1">
        <h1 class="text-2xl font-heading font-semibold text-foreground"><?= DictionaryService::getWord('uploadDocuments', $user->lang) ?></h1>
    </div>
</div>