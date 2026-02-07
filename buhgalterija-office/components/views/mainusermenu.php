<?php

use app\services\DictionaryService;

?>
<div id="user-menu" data-radix-popper-content-wrapper="" style="display:none; position: fixed; left: 0px; top: 0px; transform: translate(852px, 62px); min-width: max-content; z-index: 50; --radix-popper-available-width: 1100px; --radix-popper-available-height: 793.5px; --radix-popper-anchor-width: 152.64999389648438px; --radix-popper-anchor-height: 52px; --radix-popper-transform-origin: 100% 0px;" dir="ltr">
    <div data-side="bottom" data-align="end" role="menu" aria-orientation="vertical" data-state="open" data-radix-menu-content="" dir="ltr" id="radix-:ro:" aria-labelledby="user-card-mini" class="z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 w-56" style="outline: none; --radix-dropdown-menu-content-transform-origin: var(--radix-popper-transform-origin); --radix-dropdown-menu-content-available-width: var(--radix-popper-available-width); --radix-dropdown-menu-content-available-height: var(--radix-popper-available-height); --radix-dropdown-menu-trigger-width: var(--radix-popper-anchor-width); --radix-dropdown-menu-trigger-height: var(--radix-popper-anchor-height); pointer-events: auto;" tabindex="-1" data-orientation="vertical">
        <div class="px-2 py-1.5 text-sm font-semibold">
            <div class="flex flex-col">
                <span class="ptm-name"><?= $user->firstname . ' ' . $user->lastname ?></span>
                <span class="ptm-email text-xs font-normal text-muted-foreground"><?= $user->email ?></span>
            </div>
        </div>
        <div role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-muted"></div>
        <div role="menuitem" class="go-to-link user-menu-item relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50 focus:bg-accent focus:text-accent-foreground cursor-pointer" tabindex="-1" data-orientation="vertical" data-radix-collection-item="" data-link="/accountant/profile/">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user mr-2 h-4 w-4">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <?= DictionaryService::getWord('profile', $user->lang) ?>
        </div>
        <div role="separator" aria-orientation="horizontal" class="-mx-1 my-1 h-px bg-muted"></div>
        <div role="menuitem" class="user-menu-item relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50 focus:bg-accent focus:text-accent-foreground text-destructive cursor-pointer" tabindex="-1" data-orientation="vertical" data-radix-collection-item="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out mr-2 h-4 w-4">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" x2="9" y1="12" y2="12"></line>
            </svg>
            <span class="ptm-logout"><?= DictionaryService::getWord('logout', $user->lang) ?></span>
        </div>
    </div>
</div>