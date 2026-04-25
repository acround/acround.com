<?php

use app\services\DictionaryService;
use PSpell\Dictionary;
?>
<div class="bg-card rounded-xl border p-6">
    <div class="flex items-center gap-4 pb-4 border-b">
        <textarea id="note_textarea" rows="3" class="w-full resize-none bg-transparent border rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" placeholder="Add a new note..."></textarea>
        <!-- <div class="bg-card rounded-xl border p-6 text-center text-muted-foreground">
        </div> -->
    </div>
    <div class="text-right">
        <button id="add_note_button" type="button" class="font-medium text-sm inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" data-company-id="<?= htmlspecialchars($company->id) ?>">
            <?= DictionaryService::getWord('addNote', $user->lang) ?>
        </button>
    </div>
</div>
<?php
foreach ($notes as $note) {
?>
    <div class="bg-card rounded-xl border p-6 mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm text-muted-foreground">
                <?= htmlspecialchars($note->accountant->firstname . ' ' . $note->accountant->lastname) ?>
            </div>
            <div class="text-xs text-muted-foreground">
                <?= date('d.m.Y H:i', strtotime($note->created_at)) ?>
            </div>
        </div>
        <div class="text-sm">
            <?= nl2br(htmlspecialchars($note->note)) ?>
        </div>
    </div>
<?php
}
?>