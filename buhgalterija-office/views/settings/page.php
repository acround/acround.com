<?php

use app\components\SettingsTopWidget;
use app\components\SettingsDedlinesWidget;
use app\components\SettingsCalendarWidget;
use app\components\SettingsTemplatesWidget;
use app\services\SvgService;

?>
<div class="p-6">
    <div class="space-y-6">
        <?= SettingsTopWidget::widget([
            'user' => $user,
        ]) ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= SettingsDedlinesWidget::widget([
                'user' => $user,
            ]) ?>
            <?= SettingsTemplatesWidget::widget([
                'user' => $user,
            ]) ?>


        </div>
        <?php //= SettingsCalendarWidget::widget([
        //     'user' => $user,
        //     'taxCalendar' => $taxCalendar,
        //     'month' => $month,
        //     'year' => $year,
        //     'monthList' => $monthList
        // ]) 
        ?>
    </div>
</div>
<?php
// echo '<ul>';
// foreach (SvgService::svgList() as $name) {
//     echo '<li>' . $name . ': ' . SvgService::svg($name) . '</li>';
// }
// echo '</ul>';
