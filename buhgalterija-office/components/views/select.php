<?php

/**
 * @var $user app\models\Accountant
 * @var $id string
 * @var $options array[]
 * @var $selected string
 */

use app\services\DictionaryService;

$selectedStr = DictionaryService::getWord('selectOption', $user->lang) . '…';
foreach ($options as $option) {
    if ($option['id'] == $selected) {
        $selectedStr = $option['name'];
        break;
    }
}
?>
<div id="<?= $id ?>" class="select-widget-wrapper">
    <input type="hidden" class="select-widget-value" value="<?= $selected ?>" />
    <div class="select-widget">
        <div class="select-widget-trigger"><?= $selectedStr ?></div>
        <div class="select-widget-options">
            <?php foreach ($options as $option): ?>
                <span class="select-widget-option" data-value="<?= $option['id'] ?>"><?= $option['name'] ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>