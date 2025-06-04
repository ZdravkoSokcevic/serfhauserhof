<?php if(isset($caution) && is_array($caution) && count($caution) > 0){ ?>
    <?php foreach($caution as $c){ ?>
        <div class="message caution"><i class="fa fa-exclamation-triangle"></i> <?= __d('be', '%s %s (%s) is currently also active on this page!', $c['Auth']['User']['firstname'], $c['Auth']['User']['lastname'], $c['Auth']['User']['username']); ?></div>
    <?php } ?>
<?php } ?>