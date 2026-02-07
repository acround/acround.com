<?php

use app\services\DictionaryService;
use app\services\SvgService;

foreach ($companies as $company) {
?>
    <div class="bg-card rounded-xl border hover:shadow-lg transition-all duration-200 overflow-hidden group">
        <div class="p-4 border-b bg-secondary/30">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="font-heading font-semibold text-base truncate group-hover:text-primary transition-colors"><?= $company['company_name'] ?></h3>
                    <p class="text-xs text-muted-foreground mt-0.5"><?= DictionaryService::getWord('pib', $user->lang) ?>: <?= $company['pib'] ?></p>
                    <?php
                    $statusName = 'status' . ucfirst($company['company_status']);
                    ?>
                </div>
                <?php
                if ($user->rule == 'ceo') {
                ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border bg-success/10 text-success border-success/20"><?= DictionaryService::getWord($statusName, $user->lang) ?></span>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="p-4 space-y-3">
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2 text-muted-foreground">
                    <?= SvgService::svg('location') ?>
                    <span class="truncate">Белград</span>
                </div>
                <div class="flex items-center gap-2 text-muted-foreground">
                    <?= SvgService::svg('activity') ?>
                    <span class="truncate"><?= $company['company_activity'] ?></span>
                </div>
                <!-- <div class="flex items-center gap-2 text-muted-foreground">
                    <?= SvgService::svg('person') ?>
                    <span class="truncate">Ольга Сидорова</span>
                </div> -->
            </div>
            <div class="flex items-center gap-4 pt-2 border-t">
                <div class="flex items-center gap-1.5">
                    <?= SvgService::svg('taskList') ?>
                    <span class="text-sm font-medium"><?= $company['openTasks'] ?></span>
                    <span class="text-xs text-muted-foreground"><?= DictionaryService::getWord('openTasks', $user->lang) ?></span>
                </div>
                <?php
                if ($company['overdue']) {
                ?>
                    <div class="flex items-center gap-1.5">
                        <?= SvgService::svg('exclamation') ?>
                        <span class="text-sm font-medium text-destructive"><?= $company['overdue'] ?></span>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="px-4 pb-4 flex gap-2">
            <button data-id="<?= $company['company_id'] ?>" class="company_open_profile inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3 flex-1">
                <?= SvgService::svg('edit') ?>
                Открыть профиль
            </button>
            <!-- <button class="company_open_tasks inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                <input type="hidden" class="company" value="<?= $company['company_id'] ?>" />
                <?= SvgService::svg('taskList') ?>
            </button>
            <button class="company_open_docs inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                <input type="hidden" class="company" value="<?= $company['company_id'] ?>" />
                <?= SvgService::svg('document') ?>
            </button> -->
        </div>
    </div>
<?php
}
