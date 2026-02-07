<?php

use app\services\DictionaryService;
?>
<div id="modal-create-reg-reminder" class="modal-overlay">
  <div class="modal-window modal-big">
    
    <div class="modal-header">
      <h3 class="modal-title">Header</h3>
      <button class="modal-close-btn">&times;</button>
    </div>

    <div class="modal-body"></div>

    <div class="modal-footer">
      <button class="modal-close-btn btn-cancel border border-input"><?= DictionaryService::getWord('cancel', $user->lang) ?></button>
      <button id="save-reg-reminder" class="btn-save inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3">
        <?= DictionaryService::getWord('save', $user->lang) ?>
    </button>
    </div>

  </div>
</div>
