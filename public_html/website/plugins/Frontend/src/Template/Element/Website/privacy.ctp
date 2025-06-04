<?php
    use Cake\Core\Configure;
?>
<section class="newsletter hidden-print">
    <h3><?= __d('fe', 'Data protection'); ?></h3>
    <?= $this->CustomForm->input('privacy', ['type' => 'checkbox', 'label' => __d('fe', 'Yes, I have read and accepted the <a href="%s" target="_blank">data protection plan</a>.', $this->Url->build(['node' => Configure::read('config.default.privacy.0.org'), 'language' => $this->request->params['language']]), ''), 'escape' => false, 'id' => 'privacy']); ?>
</section>
