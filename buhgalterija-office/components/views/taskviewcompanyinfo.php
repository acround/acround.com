<?php

use app\components\MakeTelegramLinkWidget;
use app\services\DictionaryService;

$company = $task->getCompany();
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-5 w-5">
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                <path d="M10 6h4"></path>
                <path d="M10 10h4"></path>
                <path d="M10 14h4"></path>
                <path d="M10 18h4"></path>
            </svg><?= DictionaryService::getWord('companyInformation', $user->lang) ?>
        </h3>
    </div>
    <div class="p-6 pt-0 space-y-3">
        <button data-id="<?= $company->id ?>" class="company_open_profile text-left hover:underline">
            <p class="font-medium text-primary"><strong><?= $company->name ?></strong></p>
        </button>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-muted-foreground">ПИБ:</span><span class="font-mono"><?= $company->pib ?></span></div>
            <div class="flex justify-between"><span class="text-muted-foreground">Город:</span><span>Белград</span></div>
            <div class="flex justify-between"><span class="text-muted-foreground">Сектор:</span><span><?= $company->getActivity()->name ?></span></div>
        </div>
        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full"></div>
        <?php
        $customer = $company->getCustomer();
        if ($customer) {
        ?>
        <div class="space-y-1">
            <p class="text-sm font-medium"><?= $customer->firstname . ' ' . $customer->lastname ?></p>
            <!-- <p class="text-xs text-muted-foreground">dragan@balkan.rs</p>
            <p class="text-xs text-muted-foreground">+381 11 234 5678</p> -->
            <p class="text-xs text-muted-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-muted-foreground">
                    <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path>
                    <path d="m21.854 2.147-10.94 10.939"></path>
                </svg> 
                <?= MakeTelegramLinkWidget::widget(['username' => $customer->username]) ?>
            </p>
        </div>
        <?php
        }
        ?>
    </div>
</div>