<?php

use app\components\ButtonBackWidget;
use app\services\DictionaryService;

?>
<div class="p-6">
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('accountants', $user->lang) ?></h1>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <div class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                    <div class="col-span-1 space-y-3">
                        <?php
                        foreach ($accountants as $accountant) {
                        ?>
                            <div class="p-2">
                                <div class="load-by-link hover:underline cursor-pointer space-y-2" data-link="/accountant/page/<?= $accountant->id ?>" data-target="accountant-map"><?= $accountant->firstname . ' ' . $accountant->lastname ?></div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="p-2">
                    <button class="load-by-link inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2" data-link="/accountant/change/" data-target="accountant-map"><?= DictionaryService::getWord('accountantCreate', $user->lang) ?></button>
                </div>
            </div>
            <div>
                <div id="accountant-map" class="rounded-xl border p-5 transition-shadow hover:shadow-md bg-card">
                </div>
            </div>
        </div>
    </div>
</div>