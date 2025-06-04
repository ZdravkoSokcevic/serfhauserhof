<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($draft) ?>
    <?= $this->Form->hidden('model', ['value' => $model]) ?>
    <?= $this->Form->hidden('code', ['value' => $code]) ?>
    <fieldset>
        <legend><?= __d('be', 'Information') ?></legend>
        <?= $this->Form->input('internal', ['label' => __d('be', 'Internal title'), 'placeholder' => __d('be', 'Internal title')]) ?>
    </fieldset>
    
    <?php if(array_key_exists('drafts', $settings['prices']) && is_array($settings['prices']['drafts']) && array_key_exists('fields', $settings['prices']['drafts']) && ((array_key_exists('title', $settings['prices']['drafts']['fields']) && $settings['prices']['drafts']['fields']['title'] === true) || (array_key_exists('caption', $settings['prices']['drafts']['fields']) && $settings['prices']['drafts']['fields']['caption'] === true))){ ?>
    <fieldset>
        <legend><?= __d('be', 'Translations') ?></legend>
        <?= array_key_exists('title', $settings['prices']['drafts']['fields']) && $settings['prices']['drafts']['fields']['title'] === true ? $this->Form->input('title', ['label' => __d('be', 'Title'), 'placeholder' => __d('be', 'Title')]) : $this->Form->input('title', ['type' => 'hidden']); ?>
        <?= array_key_exists('caption', $settings['prices']['drafts']['fields']) && $settings['prices']['drafts']['fields']['caption'] === true ? $this->Form->input('caption', ['label' => __d('be', 'Caption'), 'placeholder' => __d('be', 'Caption')]) : $this->Form->input('caption', ['type' => 'hidden']); ?>
   </fieldset>
   <?php }else{ ?>
        <?= $this->Form->input('title', ['type' => 'hidden']); ?>
        <?= $this->Form->input('caption', ['type' => 'hidden']); ?>
   <?php } ?>
   
   
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>