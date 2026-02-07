<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\components\ModalCreateRegReminderWidget;
use app\components\ModalEditCalendarWidget;
use app\components\ModalWindowWidget;

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUHGALTERIJA - Бэк-офис</title>
    <meta name="description" content="Внутренняя система управления бухгалтерской компанией BUHGALTERIJA">
    <meta name="author" content="BUHGALTERIJA">
    <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
    <?php $this->registerCsrfMetaTags() ?>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,&lt;svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'&gt;&lt;text y='.9em' font-size='90'&gt;📊&lt;/text&gt;&lt;/svg&gt;">
    <?php
    $this->registerJsFile('@web/js/jquery.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => \yii\web\View::POS_END,]);
    $this->registerJsFile('@web/js/index.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => \yii\web\View::POS_END,]);
    $this->registerJsFile('@web/js/dictionary.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => \yii\web\View::POS_END,]);
    $this->registerJsFile('@web/js/modal.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => \yii\web\View::POS_END,]);
    $this->registerJsFile('@web/js/tiff.min.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => \yii\web\View::POS_END,]);
    $this->registerJsFile('@web/js/select.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => \yii\web\View::POS_END,]);
    $this->registerJsFile('@web/js/chat.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => \yii\web\View::POS_END,]);
    ?>
    <link rel="stylesheet" href="/css/site.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/modal.css">
    <link rel="stylesheet" href="/css/select.css">
    <!-- <link rel="stylesheet" href="/css/chat.css"> -->
    <link rel="stylesheet" href="/css/chat-cl.css">
</head>

<body>
    <?php $this->beginBody() ?>
    <div id="root"></div>
    <div id="error-tab" role="region" aria-label="Notifications (F8)" tabindex="-1" style="pointer-events: none;">
        <ol tabindex="-1" class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]"></ol>
    </div>
    <?= ModalWindowWidget::widget([
        'user' => Yii::$app->view->params['accountant'],
    ]) ?>
    <?= ModalEditCalendarWidget::widget([
        'user' => Yii::$app->view->params['accountant'],
        'token' => Yii::$app->view->params['token'],
    ]) ?>
    <?= ModalCreateRegReminderWidget::widget([
        'user' => Yii::$app->view->params['accountant'],
    ]) ?>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>