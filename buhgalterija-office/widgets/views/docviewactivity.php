<?php

use app\models\Accountant;
use app\services\DictionaryService;
?>
<div id="doc-activity-block" class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('activity', $user->lang) ?></h3>
    </div>
    <div class="p-6 pt-0 space-y-2">
        <?php foreach ($activities as $activity): ?>
            <div class="border-b pb-2 last:border-0 last:pb-0">
                <div class="text-sm text-muted-foreground">
                    <?= $activity->getStepName($user->lang) ?>
                </div>
                <div class="text-xs text-muted-foreground">
                    <?= Yii::$app->formatter->asDatetime($activity->created_at) ?>
                </div>
                <div class="text-xs text-muted-foreground">
                    <?php  
                    $accountant = Accountant::findOne(['id' => $activity->accountant_id]);
                    if ($accountant) {
                        echo DictionaryService::getWord('by', $user->lang) . ' ' . $accountant->getFullName();
                    } else {
                        echo DictionaryService::getWord('by_unknown', $user->lang);
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>