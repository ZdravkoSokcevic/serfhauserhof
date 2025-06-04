<div class="<?= strtolower($this->name); ?> form">
<?= $this->Form->create($ns, ['enctype' => 'multipart/form-data']) ?>
    <?php if(in_array($dummy, $fieldsets)){ ?>
    <fieldset>
        <legend><?= __d('be', 'Settings') ?></legend>
        <?php foreach($settings as $name => $info){ ?>
            <?php if(!array_key_exists('fieldset', $info) || $info['fieldset'] == false || $info['fieldset'] == __d('be', 'Information')){ ?>
                <?php if(array_key_exists('multi', $info) && $info['multi'] == true){ ?>
                <?php
                    $_fieldset = '';
                    $key = false;
                    if(array_key_exists('label', $info['attr']) && $info['attr']['label']){
                        $_fieldset = $info['attr']['label'];
                        if(count($languages) > 1){
                            $key = 'placeholder';
                            $info['attr']['label'] = false;
                        }else{
                            $key = 'data-language';
                        }
                    }else if(array_key_exists('placeholder', $info['attr']) && $info['attr']['placeholder']){
                        if(count($languages) > 1){
                            $_fieldset = $info['attr']['placeholder'];
                            $key = 'placeholder';
                        }else{
                            $key = 'data-language';
                        }
                    }
                ?>
                <?php if(count($languages) > 1){ ?>
                <fieldset>
                    <legend><?= $_fieldset ?></legend>
                <?php } ?>
                    <?php foreach($languages as $k => $v){ ?>
                        <?php
                            if($key){
                                $info['attr'][$key] = $v['title'];
                            }
                            
                            // keep provided class!
                            $cn = array_key_exists('class', $info['attr']) && !empty($info['attr']['class']) ? ' ' . $info['attr']['class'] : '';
                            
                            // add "special" label for editor!
                            if($info['attr']['type'] == 'textarea' && strpos($cn, 'wysiwyg') !== false){
                                $sl = '<div class="special-label"><span class="flag ' . $k . '"></span>' . $v['title'] . '<div class="clear"></div></div>';
                            }else{
                                $sl = '';
                            }
                            
                            $attr = count($languages) > 1 ? array_merge($info['attr'], ['class' => 'input-flag ' . $k . $cn]) : $info['attr'];
                        ?>
                        <?= $this->Form->input($name.'-'. $k, $attr) ?>
                    <?php } ?>
                <?php if(count($languages) > 1){ ?>
                </fieldset>
                <?php } ?>
                <?php }else{ ?>
                <?= $this->Form->input($name, $info['attr']) ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </fieldset>
    <?php } ?>
    <?php foreach($fieldsets as $fieldset){ ?>
        <?php if(!in_array($fieldset, [__d('be', 'Settings')])){ ?>
        <fieldset>
            <legend><?= $fieldset ?></legend>
            <?php foreach($settings as $name => $info){ ?>
                <?php if(array_key_exists('fieldset', $info) && $info['fieldset'] == $fieldset){ ?>
                    <?php if(array_key_exists('multi', $info) && $info['multi'] == true){ ?>
                    <?php
                        $_fieldset = '';
                        $key = false;
                        if(array_key_exists('label', $info['attr']) && $info['attr']['label']){
                            $_fieldset = $info['attr']['label'];
                            if(count($languages) > 1){
                                $key = 'placeholder';
                                $info['attr']['label'] = false;
                            }else{
                                $key = 'data-language';
                            }
                        }else if(array_key_exists('placeholder', $info['attr']) && $info['attr']['placeholder']){
                            if(count($languages) > 1){
                                $_fieldset = $info['attr']['placeholder'];
                                $key = 'placeholder';
                            }else{
                                $key = 'data-language';
                            }
                        }
                    ?>
                    <?php if(count($languages) > 1){ ?>
                    <fieldset>
                        <legend><?= $_fieldset ?></legend>
                    <?php } ?>
                        <?php foreach($languages as $k => $v){ ?>
                            <?php
                                if($key){
                                    $info['attr'][$key] = $v['title'];
                                }
                                
                                // keep provided class!
                                $cn = array_key_exists('class', $info['attr']) && !empty($info['attr']['class']) ? ' ' . $info['attr']['class'] : '';
                                
                                // add "special" label for editor!
                                if($info['attr']['type'] == 'textarea' && strpos($cn, 'wysiwyg') !== false){
                                    $sl = '<div class="special-label"><span class="flag ' . $k . '"></span>' . $v['title'] . '<div class="clear"></div></div>';
                                }else{
                                    $sl = '';
                                }
                                
                                $attr = count($languages) > 1 ? array_merge($info['attr'], ['class' => 'input-flag ' . $k . $cn]) : $info['attr'];
                            ?>
                            <?= $this->Form->input($name.'-'. $k, $attr) ?>
                        <?php } ?>
                    <?php if(count($languages) > 1){ ?>
                    </fieldset>
                    <?php } ?>
                    <?php }else{ ?>
                    <?= $this->Form->input($name, $info['attr']) ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </fieldset>
        <?php } ?>
    <?php } ?>
    <?= $this->element('Backend.save') ?>
<?= $this->Form->end() ?>
</div>
<script>
    
    $(document).ready(function(){
        
        // categories
        $('select#config-label').change(function(){
            window.location.href = '<?= urldecode( $this->Url->build(['action' => 'index', '%s']) . DS); ?>'.replace("%s", $(this).val());
        });
        
    });
    
</script>