<?php if(isset($back) && is_array($back) && array_key_exists(0, $back) && is_array($back[0]) && array_key_exists('org', $back[0]) && array_key_exists('type', $back[0]) && $back[0]['type'] == 'node'){ ?>
<section class="back-link<?php echo isset($next) && strlen($next) == 36 ? ' next-link' : ''; ?>">
    <a href="<?= $this->Url->build(['node' => $back[0]['org'], 'language' => $this->request->params['language']]); ?>" class="button left"><?= __d('fe', 'Back to overview'); ?></a>
    <?php if(isset($next) && strlen($next) == 36){ ?>
        <a href="<?= $this->Url->build(['node' => 'node:' . $next, 'language' => $this->request->params['language']]); ?>" class="button right"><?= __d('fe', 'Next room type'); ?></a>
    <?php } ?>
</section>
<?php } ?>