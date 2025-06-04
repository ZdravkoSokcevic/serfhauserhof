<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($element, ['enctype' => 'multipart/form-data']) ?>
    <?= $this->element('Backend.save', ['padding' => 'bottom']) ?>
    <fieldset>
        <legend><?= __d('be', 'Information') ?></legend>
        <?= $this->Form->hidden('category_id', ['value' => $category]) ?>
        <?= $this->Form->hidden('code', ['value' => $code]) ?>
        <?= $this->Form->hidden('show_from', ['value' => '']) ?>
        <?= $this->Form->hidden('show_to', ['value' => '']) ?>
        <?= $this->Form->hidden('active', ['value' => 1]) ?>
        <?= $this->Form->input('internal', ['label' => __d('be', 'Internal title'), 'placeholder' => __d('be', 'Internal title')]) ?>
        <?php foreach($settings['fields'] as $name => $info){ ?>
            <?php if((!array_key_exists('translate', $info) || !$info['translate']) && (!array_key_exists('fieldset', $info) || $info['fieldset'] == false || $info['fieldset'] == __d('be', 'Information'))){ ?>
                <?php if($info['attr']['type'] == 'file' && isset($element->{$name}) && is_array($element->{$name}) && array_key_exists('name', $element->{$name}) && array_key_exists('title', $element->{$name})){ ?>
                    <?php $info['attr']['templateVars'] = ['help' => '<div class="help-message">' . __d('be', 'Actual file') . ': <a href="' . $url . $element->{$name}['name'] . '" target="_blank">' . $element->{$name}['title'] . '</a></div>']; ?>
                <?php } ?>
                <?= $this->Form->input($name, $info['attr']) ?>
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
    </fieldset>
    
    <?php if(in_array(__d('be', 'Translations'), $fieldsets)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Translations') ?></legend>
        <?php foreach($settings['fields'] as $name => $info){ ?>
            <?php if(array_key_exists('translate', $info) && $info['translate'] && (!array_key_exists('fieldset', $info) || $info['fieldset'] == false || $info['fieldset'] == __d('be', 'Translations'))){ ?>
                <?php if($info['attr']['type'] == 'file' && isset($element->{$name}) && is_array($element->{$name}) && array_key_exists('name', $element->{$name}) && array_key_exists('title', $element->{$name})){ ?>
                    <?php $info['attr']['templateVars'] = ['help' => '<div class="help-message">' . __d('be', 'Actual file') . ': <a href="' . $url . $element->{$name}['name'] . '" target="_blank">' . $element->{$name}['title'] . '</a></div>']; ?>
                <?php } ?>
                <?= $this->Form->input($name, $info['attr']) ?>
            <?php } ?>
        <?php } ?>
    </fieldset>
    <?php } ?>
        
    <?php foreach($fieldsets as $fieldset){ ?>
        <?php if(!in_array($fieldset, [__d('be', 'Information'), __d('be', 'Translations'), __d('be', 'Display')])){ ?>
        <fieldset>
            <legend><?= $fieldset ?></legend>
            <?php foreach($settings['fields'] as $name => $info){ ?>
                <?php if(array_key_exists('fieldset', $info) && $info['fieldset'] == $fieldset){ ?>
                    <?php if($info['attr']['type'] == 'file' && isset($element->{$name}) && is_array($element->{$name}) && array_key_exists('name', $element->{$name}) && array_key_exists('title', $element->{$name})){ ?>
                        <?php $info['attr']['templateVars'] = ['help' => '<div class="help-message">' . __d('be', 'Actual file') . ': <a href="' . $url . $element->{$name}['name'] . '" target="_blank">' . $element->{$name}['title'] . '</a></div>']; ?>
                    <?php } ?>
                    <?= $this->Form->input($name, $info['attr']) ?>
                <?php } ?>
            <?php } ?>
        </fieldset>
        <?php } ?>
    <?php } ?>
    <?php if(in_array(__d('be', 'Period'), $fieldsets) || (!array_key_exists('config', $settings) || !array_key_exists('times', $settings['config']) || $settings['config']['times'] === true)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Period') ?></legend>
        <?php foreach($settings['fields'] as $name => $info){ ?>
            <?php if(array_key_exists('fieldset', $info) && $info['fieldset'] == __d('be', 'Period')){ ?>
                <?php if($info['attr']['type'] == 'file' && isset($element->{$name}) && is_array($element->{$name}) && array_key_exists('name', $element->{$name}) && array_key_exists('title', $element->{$name})){ ?>
                    <?php $info['attr']['templateVars'] = ['help' => '<div class="help-message">' . __d('be', 'Actual file') . ': <a href="' . $url . $element->{$name}['name'] . '" target="_blank">' . $element->{$name}['title'] . '</a></div>']; ?>
                <?php } ?>
                <?= $this->Form->input($name, $info['attr']) ?>
            <?php } ?>
        <?php } ?>
        <?php if(!array_key_exists('config', $settings) || !array_key_exists('times', $settings['config']) || $settings['config']['times'] === true){ ?>
            <?= $this->Form->input('valid_times', ['type' => 'text', 'label' => __d('be', 'Times'), 'placeholder' => __d('be', 'Times'), 'class' => 'times']) ?>
            <?= $this->Form->input('valid_fallback', ['type' => 'text', 'label' => __d('be', 'Fallback'), 'data-selector-max' => 1, 'data-selector-node' => 'true', 'data-selector-text' => __d('be', 'Select page'), 'class' => 'selector']) ?>
            
        <?php } ?>
    </fieldset>
    <?php } ?>
    <?php if(in_array(__d('be', 'Display'), $fieldsets) || (!array_key_exists('config', $settings) || !array_key_exists('range', $settings['config']) || $settings['config']['range'] === true) || (!array_key_exists('config', $settings) || !array_key_exists('active', $settings['config']) || $settings['config']['active'] === true)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Display') ?></legend>
        <?php foreach($settings['fields'] as $name => $info){ ?>
            <?php if(array_key_exists('fieldset', $info) && $info['fieldset'] == __d('be', 'Display')){ ?>
                <?php if($info['attr']['type'] == 'file' && isset($element->{$name}) && is_array($element->{$name}) && array_key_exists('name', $element->{$name}) && array_key_exists('title', $element->{$name})){ ?>
                    <?php $info['attr']['templateVars'] = ['help' => '<div class="help-message">' . __d('be', 'Actual file') . ': <a href="' . $url . $element->{$name}['name'] . '" target="_blank">' . $element->{$name}['title'] . '</a></div>']; ?>
                <?php } ?>
                <?= $this->Form->input($name, $info['attr']) ?>
            <?php } ?>
        <?php } ?>
        <?php if(!array_key_exists('config', $settings) || !array_key_exists('range', $settings['config']) || $settings['config']['range'] === true){ ?>
            <?= $this->Form->input('show_from', ['type' => 'text', 'label' => __d('be', 'Display from'), 'placeholder' => __d('be', 'Display from'), 'class' => 'date date-range date-from', 'data-date-range' => 'element-range']) ?>
            <?= $this->Form->input('show_to', ['type' => 'text', 'label' => __d('be', 'Display to'), 'placeholder' => __d('be', 'Display to'), 'class' => 'date date-range date-to', 'data-date-range' => 'element-range']) ?>
        <?php } ?>
        <?php if(!array_key_exists('config', $settings) || !array_key_exists('active', $settings['config']) || $settings['config']['active'] === true){ ?>
            <?= $this->Form->input('active', ['type' => 'checkbox', 'label' => __d('be', 'Active')]) ?>
        <?php } ?>
    </fieldset>
    <?php } ?>
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>
<?php if(array_key_exists('dynamic', $settings) && is_array($settings['dynamic']) && array_key_exists('depends', $settings['dynamic']) && array_key_exists('fields', $settings['dynamic'])){ ?>
<script>
    
    var __skip = <?= json_encode(array_merge($skip, [$settings['dynamic']['depends']])); ?>;
    var __settings = <?= json_encode($settings['dynamic']['fields']); ?>; 
    var __hidden = true;
    
    function dynamic(){
        
        var value = $('#<?= $settings['dynamic']['depends']; ?>').val();
        $('div.elements.form input, div.elements.form select, div.elements.form textarea').each(function(k,v){
            if(!in_array($(v).attr('name'), __skip)){
                if(__settings[value] == undefined || !in_array($(v).attr('name'), __settings[value])){
                    $(v).parents('div.input').addClass('hidden');
                }else{
                    $(v).parents('div.input').removeClass('hidden');
                }
            }
        });
        
        // show/hide fieldsets
        $('div.elements.form fieldset').each(function(k,v){
            __hidden = true;
            $(v).children('div.input').each(function(_k,_v){
                if($(_v).hasClass('hidden') === false){
                    __hidden = false;
                }
            });
            if(__hidden === true){
                $(v).addClass('hidden');
            }else{
                $(v).removeClass('hidden');
            }
        });
        
        // show/hide buttons
        if(value == ''){
            $('div.elements.form button').addClass('hidden');
        }else{
            $('div.elements.form button').removeClass('hidden');
        }
    }
    
    $(document).ready(function(){
        $('#<?= $settings['dynamic']['depends']; ?>').change(function(){
            dynamic();
        });
        dynamic();
    });
    
</script>
<?php } ?>