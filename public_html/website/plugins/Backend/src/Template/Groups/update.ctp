<?php use Cake\Core\Configure; ?>
<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($group) ?>
    <?= $this->element('Backend.save', ['padding' => 'bottom']) ?>
    <fieldset>
        <legend><?= __d('be', 'Group information') ?></legend>
        <?= $this->Form->input('name', ['label' => __d('be', 'Name'), 'placeholder' => __d('be', 'Name')]) ?>
    </fieldset>
    <?php foreach($rights as $type1 => $set1){ ?>
        <?php if(is_array($set1)){ ?>
            <?php if(array_key_exists('desc', $set1) && array_key_exists('value', $set1)){ ?>
            <?= $this->Form->input('settings[' . $type1 . ']', ['type' => 'checkbox', 'label' => $set1['desc'], 'checked' => $set1['value']]) ?>
            <?php }else{ ?>
            <fieldset>
                <legend><?php echo array_key_exists($type1, $map) ? $map[$type1] : $type1; ?></legend>
                <?php foreach($set1 as $type2 => $set2){ ?>
                    <?php if(is_array($set2)){ ?>
                        <?php if(array_key_exists('desc', $set2) && array_key_exists('value', $set2)){ ?>
                        <?= $this->Form->input('settings[' . $type1 . '][' . $type2 . ']', ['type' => 'checkbox', 'label' => $set2['desc'], 'checked' => $set2['value']]) ?>
                        <?php }else{ ?>
                        <fieldset>
                            <legend><?php echo array_key_exists($type2, $map) ? $map[$type2] : $type2; ?></legend>
                            <?php foreach($set2 as $type3 => $set3){ ?>
                                <?php if(is_array($set3)){ ?>
                                    <?php if(array_key_exists('desc', $set3) && array_key_exists('value', $set3)){ ?>
                                    <?= $this->Form->input('settings[' . $type1 . '][' . $type2 . '][' . $type3 . ']', ['type' => 'checkbox', 'label' => $set3['desc'], 'checked' => $set3['value']]) ?>
                                    <?php }else{ ?>
                                    <fieldset>
                                        <legend><?php echo array_key_exists($type3, $map) ? $map[$type3] : $type3; ?></legend>
                                        <?php foreach($set3 as $type4 => $set4){ ?>
                                            <?php if(is_array($set4)){ ?>
                                                <?php if(array_key_exists('desc', $set4) && array_key_exists('value', $set4)){ ?>
                                                <?= $this->Form->input('settings[' . $type1 . '][' . $type2 . '][' . $type3 . '][' . $type4 . ']', ['type' => 'checkbox', 'label' => $set4['desc'], 'checked' => $set4['value']]) ?>
                                                <?php } ?>
                                            <?php } ?>
                                       <?php } ?>
                                    </fieldset>
                                    <?php } ?>
                                <?php } ?>
                           <?php } ?>
                       </fieldset>
                        <?php } ?>    
                    <?php } ?>
               <?php } ?>
           </fieldset>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>