<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($category) ?>
    <fieldset>
        <legend><?= __d('be', 'Information') ?></legend>
        <?= $this->Form->input('internal', ['label' => __d('be', 'Internal title'), 'placeholder' => __d('be', 'Internal title')]) ?>
        <?= $this->Form->input('parent_id', [
            'label' => __d('be', 'Parent category'),
            'empty' => __d('be', '-- Select category --'),
            'options' => $categories,
            'escape' => false
        ]) ?>
        <?= $this->Form->hidden('model', ['value' => $model]) ?>
        <?= $this->Form->hidden('code', ['value' => $code]) ?>
        <?= $settings['infos']['rel'] ? $this->Form->input('rel', ['label' => __d('be', 'Related page'), 'type' => 'text', 'class' => 'selector', 'data-selector-max' => 1, 'data-selector-node' => true, 'data-selector-text' => __d('be', 'Select page')]) : $this->Form->input('rel', ['type' => 'hidden']); ?>
   </fieldset>
    <?php if(in_array(true, $settings['translations'], true)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Translations') ?></legend>
        <?= $settings['translations']['title'] ? $this->Form->input('title', ['label' => __d('be', 'Title'), 'placeholder' => __d('be', 'Title')]) : $this->Form->input('title', ['type' => 'hidden']); ?>
        <?= $settings['translations']['content'] ? $this->Form->input('content', ['type' => 'textarea', 'label' => __d('be', 'Content'), 'placeholder' => __d('be', 'Content'), 'class' => 'wysiwyg']) : $this->Form->input('content', ['type' => 'hidden']); ?>
        <?= $settings['translations']['seo'] ? $this->Form->input('seo', ['label' => __d('be', 'SEO title'), 'placeholder' => __d('be', 'SEO title')]) : $this->Form->input('seo', ['type' => 'hidden']); ?>
   </fieldset>
   <?php }else{ ?>
        <?= $this->Form->input('title', ['type' => 'hidden']); ?>
        <?= $this->Form->input('content', ['type' => 'hidden']); ?>
        <?= $this->Form->input('seo', ['type' => 'hidden']); ?>       
   <?php } ?>
    <?php if(in_array(true, $settings['fields'], true)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Settings') ?></legend>
        <?= $settings['fields']['special'] ? $this->Form->input('special', ['label' => __d('be', 'Special behavior'), 'placeholder' => __d('be', 'Special'), 'type' => 'checkbox']) : $this->Form->input('special', ['type' => 'hidden']); ?>
   </fieldset>
   <?php }else{ ?>
        <?= $this->Form->input('special', ['type' => 'hidden']); ?>
   <?php } ?>
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>