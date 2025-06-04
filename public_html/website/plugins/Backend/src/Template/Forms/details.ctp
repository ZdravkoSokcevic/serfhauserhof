<?php
    $this->layout('selector');
?>
<?php if(!empty($data) && !empty($details)){ ?>
    <div class="tabs">
        <a href="javascript:detailsTab(1);" class="tab-1 active"><?= __d('be', 'Data'); ?></a>
        <a href="javascript:detailsTab(2);" class="tab-2"><?= __d('be', 'Details'); ?></a>
        <div class="clear"></div>
    </div>
    <div class="tab tab-1">
        <div class="details-inner">
            <?= $data; ?>
        </div>
        <div class="clear"></div>
        <div class="actions">
            <a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Ok'); ?></a>
        </div>
    </div>
    <div class="tab tab-2 hidden">
        <div class="details-inner">
            <?= $details; ?>
        </div>
        <div class="clear"></div>
        <div class="actions">
            <a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Ok'); ?></a>
        </div>
    </div>
    <script>
    
        function detailsTab(idx){
            $('.selector-wrapper .tabs a').removeClass('active');
            $('.selector-wrapper .tabs a.tab-' + idx).addClass('active');
            $('.selector-wrapper div.tab').addClass('hidden');
            $('.selector-wrapper div.tab.tab-' + idx).removeClass('hidden');
        }
        
    </script>
<?php }else{ ?>
    <div class="error message"><?= __d('be', 'No data available!'); ?></div>
    <div class="actions error-actions">
        <a href="javascript:void(0);" class="submit cancel"><?= __d('be', 'OK'); ?></a>
        <div class="clear"></div>
    </div>
<?php } ?>