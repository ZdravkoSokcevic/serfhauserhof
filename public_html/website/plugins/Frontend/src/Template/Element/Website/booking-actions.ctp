<?php use Cake\Core\Configure; ?>
<?php
    $ra = isset($request) && is_string($request) && strlen($request) == 36 ? [$type => $request] : false;
    $ba = isset($book) && is_string($book) && !empty($book) == 36 ? [$type => $book] : false;
?>
<section class="booking-actions <?= $type; ?><?php echo isset($standalone) && $standalone === true ? ' standalone inner' : ' inline'; ?>">
    <div>
        <a class="button pos-1 s2u" href="<?= $this->Url->build(['node' => Configure::read('config.default.request.0.org'), 'language' => $this->request->params['language'], '?' => $ra]); ?>"><?= __d('fe', 'request'); ?></a>
        <a class="button pos-2 dark s2u" href="<?= $this->Url->build(['node' => Configure::read('config.default.book.0.org'), 'language' => $this->request->params['language'], '?' => $ba]); ?>"><?= __d('fe', 'book'); ?></a>
        <a class="button pos-3 light s2u" href="<?= $this->Url->build(['node' => Configure::read('config.default.services.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'included services'); ?></a>
        <a class="button pos-4 light s2u" href="<?= $this->Url->build(['node' => Configure::read('config.default.children.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'children prices'); ?></a>
        <div class="clear"></div>
    </div>
</section>