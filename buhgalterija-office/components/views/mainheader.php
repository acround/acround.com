<?php

use app\services\DictionaryService;

$initials = strtoupper($user->firstname[0] . $user->lastname[0]);
?>
<header class="fixed top-0 right-0 left-64 z-30 h-16 bg-card border-b border-border flex items-center justify-between px-6">
    <div class="flex-1 max-w-xl">
        <!-- @TODO: Search feature -->
        <!-- <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground">
                <circle cx="11" cy="11" r="8">
                </circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <input type="search" class="flex h-10 w-full rounded-md border-input px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10 bg-secondary border-0 focus-visible:ring-1" placeholder="Поиск по компании, ПИБ, задаче...">
        </div> -->
    </div>
    <div class="flex items-center gap-4">
        <button id="user-card-mini" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground gap-3 h-auto py-2 px-3" type="button" aria-haspopup="menu" aria-expanded="false" data-state="closed">
            <span class="relative flex shrink-0 overflow-hidden rounded-full h-8 w-8">
                <span class="flex h-full w-full items-center justify-center rounded-full bg-primary text-primary-foreground text-xs"><?= $initials ?></span>
            </span>
            <div class="flex flex-col items-start">
                <span class="ptm-name text-sm font-medium"><?= $user->firstname . ' ' . $user->lastname ?></span>
                <div class="ptm-position inline-flex items-center rounded-full border font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80 text-[10px] px-1.5 py-0 h-4"><?= DictionaryService::getWord($user->rule, $user->lang) ?></div>
            </div>
        </button>
    </div>
</header>