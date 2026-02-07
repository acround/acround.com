<?php

use app\components\DocViewActivityWidget;
use app\components\DocViewCommentsWidget;
use app\components\DocViewStatusWidget;
use app\components\DocViewTopWidget;
use app\components\DocViewInfoWidget;
use app\components\DocViewCompanyWidget;

?>
<div class="p-6">
    <div class="space-y-6">
        <?= DocViewTopWidget::widget([
            'user' => $user,
            'document' => $document,
        ]) ?>
        <div class="grid gap-6 lg:grid-cols-3">
            <?= DocViewInfoWidget::widget([
                'user' => $user,
                'document' => $document,
            ]) ?>
            <div class="space-y-6">
                <?= DocViewCompanyWidget::widget([
                    'user' => $user,
                    'document' => $document,
                ])
                ?>
                <?= DocViewStatusWidget::widget([
                    'user' => $user,
                    'document' => $document,
                ])
                ?>
                <?= DocViewActivityWidget::widget([
                    'user' => $user,
                    'document' => $document,
                ])
                ?>
                <?= DocViewCommentsWidget::widget([
                    'user' => $user,
                    'document' => $document,
                ])
                ?>
            </div>
        </div>
    </div>
</div>