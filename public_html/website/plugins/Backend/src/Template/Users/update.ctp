<?php
    use Cake\Core\Configure;

    // premissions
    $cp_groups = __cp(['controller' => 'groups', 'action' => 'update'], $auth);
    
?>
<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __d('be', 'User information') ?></legend>
        <?= $this->Form->input('firstname', ['label' => __d('be', 'Firstname'), 'placeholder' => __d('be', 'Firstname')]) ?>
        <?= $this->Form->input('lastname', ['label' => __d('be', 'Lastname'), 'placeholder' => __d('be', 'Lastname')]) ?>
        <?= $this->Form->input('username', ['label' => __d('be', 'Username'), 'placeholder' => __d('be', 'Username')]) ?>
        <?= $this->Form->input('password', ['label' => __d('be', 'Password'), 'placeholder' => __d('be', 'Password'), 'value' => '', 'templateVars' => ['help' => $id ? '<div class="help-message">' . __d('be', 'Leave blank if the password should not be changed.') . '</div>' : '']]) ?>
        <?php if($admin || $cp_groups){ ?>
            <?= $this->Form->input('group_id', [
                'label' => __d('be', 'Group'),
                'empty' => __d('be', '-- Select group --'),
                'options' => $groups
            ]) ?>
        <?php }else{ ?>
            <?= $this->Form->input('group_id', ['type' => 'hidden', 'value' => $id ? $user['group_id'] : $auth['Group']['id']]); ?>
        <?php } ?>
   </fieldset>
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>