<?php

    // premissions
    $cp_update = __cp(['controller' => 'users', 'action' => 'update'], $auth);
    $cp_delete = __cp(['controller' => 'users', 'action' => 'delete'], $auth);

    $icons = count_true([$cp_update, $cp_delete]);
    
?>
<div class="<?= strtolower($this->name); ?> list">
    <?php if(count($users) < 1){ ?>
    <div class="message info"><?= __d('be', 'No data available') ?></div>
    <?php }else{ ?>
    <table class="list" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('firstname', __d('be', 'Firstname')) ?></th>
                <th><?= $this->Paginator->sort('lastname', __d('be', 'Lastname')) ?></th>
                <th><?= $this->Paginator->sort('username', __d('be', 'Username')) ?></th>
                <th width="<?= action_width($icons, false); ?>" class="actions">&nbsp;</th>
            </tr>
        </thead>
        <?= $this->element('Backend.paginator', ['colspan' => 4]); ?>
        <tbody>
            <?php foreach($users as $nr => $user){ ?>
            <tr class="<?= $nr%2 ? 'alternate' : ''; ?>">
                <td><?= $user['firstname']; ?></td>
                <td><?= $user['lastname']; ?></td>
                <td><?= $user['username']; ?></td>
                <td class="actions">
                    <?php if($cp_update){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'pencil', 'text' => __d('be', 'Edit'), 'url' => ['action' => 'update', $user['id']]]) ?>
                    <?php } ?>
                    <?php if($cp_delete && $user['id'] != $auth['User']['id']){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'trash', 'text' => __d('be', 'Delete'), 'url' => ['action' => 'delete', $user['id']], 'confirm' => __d('be', 'Do you really want to delete this user?')]) ?>
                    <?php } ?>
                    <div class="clear"></div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>