<?php

use app\services\DictionaryService;
use app\services\SvgService;

?>
<aside id="sidebar" class="fixed left-0 top-0 z-40 h-screen bg-sidebar border-r border-sidebar-border transition-all duration-300 w-64">
    <div class="flex items-center h-16 px-4 border-b border-sidebar-border justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                <span class="text-primary-foreground font-bold text-sm">B</span>
            </div>
            <span class="blink font-heading font-semibold text-sidebar-foreground">BUHGALTERIJA</span>
        </div>
    </div>
    <nav class="flex flex-col gap-1 p-3 mt-2">
        <?php
        foreach ($menu as $name => $value) {
            if (!$value['active']) {
                continue;
            }
            if (!in_array($user->rule, $value['rules'])) {
                continue;
            }
            if ($name === 'dashboard') {
                $style = 'bg-sidebar-primary text-sidebar-primary-foreground active';
            } else {
                $style = 'text-sidebar-foreground';
            }
        ?>
            <a aria-current="page" class="menuitem flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground <?= $style ?>" href="<?= $value['url'] ?>" title="<?= DictionaryService::getWord($name, $user->lang) ?>">
                <?= $value['picture'] ?>
                <span class="blink"><?= DictionaryService::getWord($name, $user->lang) ?></span>
            </a>
        <?php
        }
        ?>
    </nav>
    <button id="close-menu-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:text-accent-foreground h-10 w-10 absolute bottom-4 text-sidebar-foreground hover:bg-sidebar-accent right-3">
        <?= SvgService::svg('chevron-left') ?>
    </button>
</aside>