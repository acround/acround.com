<?php

use app\widgets\DocViewActivityWidget;
use app\widgets\DocViewCommentsWidget;
use app\widgets\DocViewStatusWidget;
use app\widgets\DocViewTopWidget;
use app\widgets\DocViewInfoWidget;
use app\widgets\DocViewCompanyWidget;

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