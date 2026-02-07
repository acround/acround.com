<?php

use app\components\LastActivityWidget;
use app\services\DictionaryService;
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('activity', $user->lang) ?></h3>
    </div>
    <?php
    $activities = $task->getActivities();
    foreach ($activities as $activity) {
        $formattedDate = Yii::$app->formatter->asDatetime(
            $task->created_at,
            'php:d M Y г. в H:m'
        );
    ?>
        <div class="p-6 pt-0">
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
                    <div>
                        <p class="text-sm"><?= $activity->getStep()->getName($user->lang) ?></p>
                        <p class="text-xs text-muted-foreground"><?= $formattedDate ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>