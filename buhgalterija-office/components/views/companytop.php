<?php

use app\components\MakeTelegramLinkWidget;
use app\services\DictionaryService;
use app\services\SvgService;
use yii\debug\models\timeline\Svg;

?>
<div class="bg-card rounded-xl border p-6">
    <div class="flex items-start justify-between">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center">
                <?= SvgService::svg('company-top') ?>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-heading font-bold"><?= $company['name'] ?></h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-success/10 text-success border-success/20">
                        <?php
                        $statusName = 'status' . ucfirst($company['status']);
                        echo DictionaryService::getWord($statusName, $user->lang);
                        ?>
                    </span>
                </div>
                <p class="text-muted-foreground mt-1"><?= DictionaryService::getWord('pib', $user->lang) ?>: <?= $company->pib ?></p>
            </div>
        </div>
        <button id="company-edit-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" data-id="<?= $company['id'] ?>">
            <?= SvgService::svg('edit') ?>
            <?= DictionaryService::getWord('edit', $user->lang) ?>
        </button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t">
        <!-- <div class="flex items-center gap-2">
            <?= SvgService::svg('location-gray') ?>
            <span class="text-sm">Белград</span>
        </div> -->
        <?php
        if (count($customers) > 0) {
        ?>
            <div class="flex items-center gap-2">
                <?= SvgService::svg('telegram') ?>
                <?php
                foreach ($customers as $customer) {
                ?>
                    <span class="text-sm">
                        <?= MakeTelegramLinkWidget::widget(['username' => $customer['username']]) ?>
                    </span>
                <?php
                }
                ?>
            </div>
        <?php
        }
        $accountants = $company->getCompanyAccountants();
        if ($accountants->count() > 0) {
            foreach ($accountants->all() as $accountant)
        ?>
            <div class="flex items-center gap-2">
                <span class="text-sm"><?= DictionaryService::getWord('responsibleAccountant', $user->lang) ?>: <?= $accountant->firstname . ' ' . $accountant->lastname ?></span>
            </div>
        <?php
        }
        ?>
    </div>
</div>