<?php

use app\components\MainMenuWidget;
use app\components\MainHeaderWidget;
use app\components\MainUserMenuWidget;

/** @var $user app\models\Accountant */
/** @var $data array */
?>
<div role="region" aria-label="Notifications (F8)" tabindex="-1" style="pointer-events: none;">
    <ol tabindex="-1" class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]">
    </ol>
</div>
<section aria-label="Notifications alt+T" tabindex="-1" aria-live="polite" aria-relevant="additions text" aria-atomic="false">
</section>
<div class="min-h-screen bg-background">
    <?= MainMenuWidget::widget(['user' => $user, 'menu' => $menu]) ?>
    <?= MainHeaderWidget::widget(['user' => $user]) ?>
    <main class="ml-64 pt-16 min-h-screen">
        <?= $this->render('page', ['user' => $user, 'data' => $data]) ?>
    </main>
</div>
<?= MainUserMenuWidget::widget(['user' => $user]) ?>