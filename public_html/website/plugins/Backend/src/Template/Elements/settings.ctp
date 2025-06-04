<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($element_settings, ['enctype' => 'multipart/form-data']) ?>
    <?= $this->element('Backend.save', ['padding' => 'bottom', 'show' => 'stay']) ?>
    <?= $this->Form->hidden('foreign_id', ['value' => $element_id]) ?>
    <?= $this->Form->hidden('selection', ['value' => $selection]) ?>
    <?= $this->Form->hidden('subselection', ['value' => $subselection]) ?>
    
    <?php foreach($settings['settings']['fields'] as $name => $info){ ?>
        <?php if((!array_key_exists('translate', $info) || !$info['translate']) && (!array_key_exists('fieldset', $info) || $info['fieldset'] == false || $info['fieldset'] == __d('be', 'Information'))){ ?>
            <?php
                if(!in_array(__d('be', 'Information'), $fieldsets)){
                    $fieldsets[] = __d('be', 'Information');
                }
            ?>
        <?php }else{ ?>
            <?php
                if($info['translate'] && (!array_key_exists('fieldset', $info) || $info['fieldset'] == false)){
                    if(!in_array(__d('be', 'Translations'), $fieldsets)){
                        $fieldsets[] = __d('be', 'Translations');
                    }
                }else{
                    if(!in_array($info['fieldset'], $fieldsets)){
                        $fieldsets[] = $info['fieldset'];
                    }
                }
            ?>
        <?php } ?>
    <?php } ?>
    <?php if(in_array(__d('be', 'Information'), $fieldsets)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Information') ?></legend>
        <?php foreach($settings['settings']['fields'] as $name => $info){ ?>
            <?php if(!array_key_exists('translate', $info) ||  $info['translate'] === false && (!array_key_exists('fieldset', $info) || $info['fieldset'] == false || $info['fieldset'] == __d('be', 'Information'))){ ?>
                <?= $this->Form->input($name, $info['attr']) ?>
            <?php } ?>
        <?php } ?>
    </fieldset>
    <?php } ?>
    
    <?php if(in_array(__d('be', 'Translations'), $fieldsets)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Translations') ?></legend>
        <?php foreach($settings['settings']['fields'] as $name => $info){ ?>
            <?php if(array_key_exists('translate', $info) && $info['translate'] && (!array_key_exists('fieldset', $info) || $info['fieldset'] == false || $info['fieldset'] == __d('be', 'Translations'))){ ?>
                <?= $this->Form->input($name, $info['attr']) ?>
            <?php } ?>
        <?php } ?>
    </fieldset>
    <?php } ?>
        
    <?php foreach($fieldsets as $fieldset){ ?>
        <?php if(!in_array($fieldset, [__d('be', 'Information'), __d('be', 'Translations'), __d('be', 'Display')])){ ?>
        <fieldset>
            <legend><?= $fieldset ?></legend>
            <?php foreach($settings['settings']['fields'] as $name => $info){ ?>
                <?php if(array_key_exists('fieldset', $info) && $info['fieldset'] == $fieldset){ ?>
                    <?= $this->Form->input($name, $info['attr']) ?>
                <?php } ?>
            <?php } ?>
        </fieldset>
        <?php } ?>
    <?php } ?>
    <?= $this->element('Backend.save', ['show' => 'stay']) ?>
<?= $this->Form->end() ?>
</div>
<script>
    
    $(document).ready(function(){

        // selection
        $('select#selection').change(function(){
            window.location.href = '<?= urldecode($this->Url->build(['action' => 'settings', $code, $category, $element_id])); ?>?s1=%s&s2=<?= $subselection; ?>'.replace("%s", $(this).val());
        });
        
        // subselection
        $('select#subselection').change(function(){
            window.location.href = '<?= urldecode($this->Url->build(['action' => 'settings', $code, $category, $element_id])); ?>?s1=<?= $selection; ?>&s2=%s'.replace("%s", $(this).val());
        });

    });
    
</script>