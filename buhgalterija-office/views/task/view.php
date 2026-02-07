<?php

use app\components\TaskViewActivityWidget;
use app\components\TaskViewCommentsWidget;
use app\components\TaskViewCompanyInfoWidget;
use app\components\TaskViewDetailsWidget;
use app\components\TaskViewDocumentsWidget;
use app\components\TaskViewTopWidget;

?>
<div class="p-6">
    <div class="space-y-6">
        <?php
        echo TaskViewTopWidget::widget([
            'user' => $user,
            'task' => $task,
        ]);
        ?>
        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <?php
                echo TaskViewDetailsWidget::widget([
                    'user' => $user,
                    'task' => $task,
                ]);
                echo TaskViewDocumentsWidget::widget([
                    'user' => $user,
                    'task' => $task,
                ]);
                echo TaskViewCommentsWidget::widget([
                    'user' => $user,
                    'task' => $task,
                ]);
                ?>
            </div>
            <div class="space-y-6">
                <?php
                echo TaskViewCompanyInfoWidget::widget([
                    'user' => $user,
                    'task' => $task,
                ]);
                echo TaskViewActivityWidget::widget([
                    'user' => $user,
                    'task' => $task,
                ]);
                ?>
            </div>
        </div>
    </div>
</div>