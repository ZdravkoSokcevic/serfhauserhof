<?php

    // premissions
    $cp_update = __cp(['controller' => 'groups', 'action' => 'update'], $auth);
    $cp_delete = __cp(['controller' => 'groups', 'action' => 'delete'], $auth);

    $icons = count_true([$cp_update, $cp_delete]);
    
?>
<div class="<?= strtolower($this->name); ?> list">
    <?php if(count($groups) < 1){ ?>
    <div class="message info"><?= __d('be', 'No data available') ?></div>
    <?php }else{ ?>
    <table class="list" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('name', __d('be', 'Name')) ?></th>
                <th width="<?= action_width($icons, false); ?>" class="actions">&nbsp;</th>
            </tr>
        </thead>
        <?= $this->element('Backend.paginator', ['colspan' => 4]); ?>
        <tbody>
            <?php foreach($groups as $nr => $group){ ?>
            <tr class="<?= $nr%2 ? 'alternate' : ''; ?>">
                <td><?= $group['name']; ?></td>
                <td class="actions">
                    <?php if($cp_update){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'pencil', 'text' => __d('be', 'Edit'), 'url' => ['action' => 'update', $group['id']]]) ?>
                    <?php } ?>
                    <?php if($cp_delete){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'trash', 'text' => __d('be', 'Delete'), 'url' => ['action' => 'delete', $group['id']], 'confirm' => __d('be', 'Do you really want to delete this group?')]) ?>
                    <?php } ?>
                    <div class="clear"></div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>