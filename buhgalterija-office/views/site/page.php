<?php

use app\services\AuthService;
use app\services\DictionaryService;
use \app\components\DeadLinesWidget;
use app\components\ViewAccountantsWidget;
use app\components\LastActivityWidget;
use app\components\OverviewTopPanelWidget;

/** @var $user app\models\Accountant */
/** @var $data array */

?>
<div class="p-6">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('welcomeBack', $user->lang) ?>, <?= $user->firstname . ' ' . $user->lastname ?>!</h1>
            <p class="text-muted-foreground mt-1"><?= DictionaryService::getWord('overview', $user->lang) ?></p>
        </div>

        <?php
        echo OverviewTopPanelWidget::widget([
            'data' => $data,
            'user' => $user,
        ]);
        ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php
            echo DeadLinesWidget::widget([
                'viewAccountants' => $data['viewAccountants'],
                'data' => $data,
                'user' => $user,
            ]);

            if ($data['viewAccountants']) {
                echo ViewAccountantsWidget::widget([
                    'data' => $data,
                    'user' => $user,
                ]);
            } else {
                echo LastActivityWidget::widget([
                    'data' => $data,
                    'user' => $user,
                ]);
            }
            ?>
        </div>
    </div>
</div>