<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($season) ?>
    <fieldset>
        <legend><?= __d('be', 'Information') ?></legend>
        <?= $this->Form->input('internal', ['label' => __d('be', 'Internal title'), 'placeholder' => __d('be', 'Internal title')]) ?>
        <?= $this->Form->input('container', [
            'label' => __d('be', 'Group'),
            'empty' => __d('be', '-- Select group --'),
            'options' => $containers
        ]) ?>
    </fieldset>
    <?php if(array_key_exists('fields', $settings['prices']['seasons']) && ((array_key_exists('title', $settings['prices']['seasons']['fields']) && $settings['prices']['seasons']['fields']['title'] === true) || (array_key_exists('content', $settings['prices']['seasons']['fields']) && $settings['prices']['seasons']['fields']['content'] === true))){ ?>
    <fieldset>
        <legend><?= __d('be', 'Translations') ?></legend>
        <?= array_key_exists('title', $settings['prices']['seasons']['fields']) && $settings['prices']['seasons']['fields']['title'] === true ? $this->Form->input('title', ['label' => __d('be', 'Title'), 'placeholder' => __d('be', 'Title')]) : $this->Form->input('title', ['type' => 'hidden']); ?>
        <?= array_key_exists('content', $settings['prices']['seasons']['fields']) && $settings['prices']['seasons']['fields']['content'] === true ? $this->Form->input('content', ['type' => 'textarea', 'label' => __d('be', 'Content'), 'placeholder' => __d('be', 'Content'), 'class' => 'wysiwyg']) : $this->Form->input('content', ['type' => 'hidden']); ?>
   </fieldset>
   <?php }else{ ?>
        <?= $this->Form->input('title', ['type' => 'hidden']); ?>
        <?= $this->Form->input('content', ['type' => 'hidden']); ?>
   <?php } ?>
    <?php if(is_array($link)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Linked element') ?></legend>
        <?= $this->Form->input('link', ['label' => __d('be', 'Element'), 'class' => 'selector', 'data-selector-max' => 1, 'data-selector-element' => $link['code'], 'data-selector-text' => __d('be', 'Select element')]) ?>
   </fieldset>
    <?php }else{ ?>
    <?= $this->Form->input('link', ['type' => 'hidden']); ?>
    <?php } ?>
    <fieldset>
        <legend><?= __d('be', 'Periods') ?></legend>
        <?= $this->Form->input('times', ['label' => __d('be', 'Periods'), 'class' => 'times']) ?>
   </fieldset>
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>