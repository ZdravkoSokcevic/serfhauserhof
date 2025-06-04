<?php
    $this->layout('print');
?>
<?php if(!empty($data) && !empty($details)){ ?>
    <h1><?= $title; ?></h1>
    <div class="details-inner">
        <h2><?= __d('be', 'Data'); ?></h2>
        <?= $data; ?>
        <h2><?= __d('be', 'Details'); ?></h2>
        <?= $details; ?>
    </div>
<?php }else{ ?>
    <div class="error message"><?= __d('be', 'No data available!'); ?></div>
<?php } ?>
<div class="clear"></div>