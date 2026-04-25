<?php

use app\services\DictionaryService;

$company = $document->getCompany();
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-base flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-4 w-4">
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                <path d="M10 6h4"></path>
                <path d="M10 10h4"></path>
                <path d="M10 14h4"></path>
                <path d="M10 18h4"></path>
            </svg>
            <?= DictionaryService::getWord('company', $user->lang) ?>
        </h3>
    </div>
    <div class="p-6 pt-0">
        <button data-id="<?= $company ? $company->id : 'None' ?>" class="<?= $company ? 'company_open_profile ' : '' ?>text-left hover:underline w-full">
            <p class="font-medium text-primary"><strong><?= $company ? $company->name : '' ?></strong></p>
        </button>
        <p class="text-sm text-muted-foreground mt-1"><?= DictionaryService::getWord('pib', $user->lang) ?>: <?= $company ? $company->pib : '' ?></p>
    </div>
</div>