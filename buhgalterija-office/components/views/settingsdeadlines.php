<?php

use app\services\DictionaryService;
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-shadow cursor-pointer">
    <div class="flex flex-col space-y-1.5 p-6 pb-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-5 w-5 text-primary">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('defaultDeadlines', $user->lang) ?></h3>
        </div>
    </div>
    <div class="p-6 pt-0">
        <p class="text-sm text-muted-foreground"><?= DictionaryService::getWord('defaultDeadlinesForVariousTasks', $user->lang) ?></p>
        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 mt-4">
            <?= DictionaryService::getWord('set', $user->lang) ?>
        </button>
    </div>
</div>