<?php use Cake\Core\Configure; ?>
<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __d('be', 'User information') ?></legend>
        <?= $this->Form->input('firstname', ['label' => __d('be', 'Firstname'), 'placeholder' => __d('be', 'Firstname')]) ?>
        <?= $this->Form->input('lastname', ['label' => __d('be', 'Lastname'), 'placeholder' => __d('be', 'Lastname')]) ?>
        <?= $this->Form->input('username', ['label' => __d('be', 'Username'), 'placeholder' => __d('be', 'Username')]) ?>
        <?= $this->Form->input('password', ['label' => __d('be', 'Password'), 'placeholder' => __d('be', 'Password'), 'value' => '', 'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Leave blank if the password should not be changed.') . '</div>']]) ?>
   </fieldset>
    <?= $this->Form->button(__d('be', 'Save'), ['name' => '_submit', 'value' => 'stay', 'class' => 'submit']); ?>
    <div class="clear"></div>
<?= $this->Form->end() ?>
</div>