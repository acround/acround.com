<?php

use app\services\DictionaryService;

?>
<div class="bg-card rounded-xl border p-5">
    <h3 class="font-heading font-semibold text-lg mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-5 w-5 text-primary">
            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
            <polyline points="16 7 22 7 22 13"></polyline>
        </svg>
        <?= DictionaryService::getWord('recentActivity', $user->lang) ?>
    </h3>
    <div class="space-y-3">
        <?php
        foreach ($data['activities'] as $activity) {
            $stepName = $activity->getStep()->getName($user->lang);
            $task = $activity->getTask();
            $timeAgo = Yii::$app->formatter->asRelativeTime($activity->created_at);
        ?>
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <p class="text-sm font-medium"><?= $stepName ?></p>
                    <p class="text-xs text-muted-foreground"><?= $task->category ?> — <?= $task->getCompany()->name ?></p>
                </div>
                <span class="text-xs text-muted-foreground"><?= $timeAgo ?></span>
            </div>
        <?php
        }
        ?>
    </div>
</div>