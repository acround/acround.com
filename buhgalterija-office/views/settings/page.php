<?php

use app\widgets\SettingsTopWidget;
use app\widgets\SettingsDedlinesWidget;
use app\widgets\SettingsCalendarWidget;
use app\widgets\SettingsTemplatesWidget;
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
