<?php

use app\components\DocUploadBodyWidget;
use app\components\DocUploadTopWidget;

?>
<div class="p-6">
    <div class="space-y-6">
        <?= DocUploadTopWidget::widget([
            'user' => $user,
        ]) ?>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6">
                <?= DocUploadBodyWidget::widget([
                    'user' => $user,
                ]) ?>
            </div>
        </div>
    </div>
</div>