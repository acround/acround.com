<?php

use app\services\DictionaryService;

?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="go-to-link rounded-xl border p-5 transition-shadow hover:shadow-md bg-card cursor-pointer" data-link="/company/page/back" data-count="<?= $data['clents'] ?>">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('totalClients', $user->lang) ?></p>
                <p class="mt-2 text-3xl font-bold font-heading"><?= $data['clents'] ?></p>
            </div>
            <div class="rounded-lg p-2.5 bg-primary/10 text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-5 w-5">
                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                    <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                    <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                    <path d="M10 6h4"></path>
                    <path d="M10 10h4"></path>
                    <path d="M10 14h4"></path>
                    <path d="M10 18h4"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="go-to-link rounded-xl border p-5 transition-shadow hover:shadow-md bg-card cursor-pointer" data-link="/task/page/inProgress" data-count="<?= $data['activeTasks'] ?>">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('activeTasks', $user->lang) ?></p>
                <p class="mt-2 text-3xl font-bold font-heading"><?= $data['activeTasks'] ?></p>
            </div>
            <div class="rounded-lg p-2.5 bg-primary/10 text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-todo h-5 w-5">
                    <rect x="3" y="5" width="6" height="6" rx="1"></rect>
                    <path d="m3 17 2 2 4-4"></path>
                    <path d="M13 6h8"></path>
                    <path d="M13 12h8"></path>
                    <path d="M13 18h8"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="go-to-link rounded-xl border p-5 transition-shadow hover:shadow-md bg-destructive/5 border-destructive/20 cursor-pointer" data-link="/task/page/overdue" data-count="<?= count($data['overdueTasks']) ?>">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('overdueTasks', $user->lang) ?></p>
                <p class="mt-2 text-3xl font-bold font-heading"><?= count($data['overdueTasks']) ?></p>
            </div>
            <div class="rounded-lg p-2.5 bg-destructive/10 text-destructive">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-5 w-5">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" x2="12" y1="8" y2="12"></line>
                    <line x1="12" x2="12.01" y1="16" y2="16"></line>
                </svg>
            </div>
        </div>
    </div>
    <div class="go-to-link rounded-xl border p-5 transition-shadow hover:shadow-md bg-warning/5 border-warning/20 cursor-pointer" data-link="/document/page/uploaded" data-count="<?= $data['docsToCheck'] ?>">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-muted-foreground font-medium"><?= DictionaryService::getWord('pendingDocuments', $user->lang) ?></p>
                <p class="mt-2 text-3xl font-bold font-heading"><?= $data['docsToCheck'] ?></p>
            </div>
            <div class="rounded-lg p-2.5 bg-warning/10 text-warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                    <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                    <path d="M10 9H8"></path>
                    <path d="M16 13H8"></path>
                    <path d="M16 17H8"></path>
                </svg>
            </div>
        </div>
    </div>
</div>