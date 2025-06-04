<?php use Cake\Core\Configure; ?>
<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($structure) ?>
    <fieldset>
        <legend><?= __d('be', 'Information') ?></legend>
        <?= $this->Form->input('title', ['label' => __d('be', 'Title'), 'placeholder' => __d('be', 'Title')]) ?>
        <?= $this->Form->input('filter', ['label' => __d('be', 'URL filter'), 'placeholder' => __d('be', 'URL filter'), 'templateVars' => ['help' => '<div class="help-message"><div>' . __d('be', 'Examples') . ':</div><ul><li>medienjaeger.at (' . __d('be', 'a domain') . ')</li><li>subdomain.medienjaeger.at (' . __d('be', 'a subdomain') . ')</li><li>medienjaeger.at/part (' . __d('be', 'a url part') . ')</li></ul></div>']]) ?>
        <?= $this->Form->input('theme', [
            'label' => __d('be', 'Theme'),
            'empty' => __d('be', '-- Select theme --'),
            'options' => $themes
        ]) ?>
   </fieldset>
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>