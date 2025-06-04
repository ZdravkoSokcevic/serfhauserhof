<?php

    // premissions
    $cp_update = __cp(['controller' => 'elements', 'action' => 'update', $code], $auth);
    $cp_settings = $es && __cp(['controller' => 'elements', 'action' => 'settings', $code], $auth) ? true : false;
    $cp_copy = __cp(['controller' => 'elements', 'action' => 'copy', $code], $auth);
    $cp_sort = $sortable && __cp(['controller' => 'elements', 'action' => 'order', $code], $auth) ? true : false;
    $cp_media = $media && __cp(['controller' => 'elements', 'action' => 'media', $code], $auth) ? true : false;
    $cp_delete = __cp(['controller' => 'elements', 'action' => 'delete', $code], $auth);
    $cp_prices = array_key_exists('prices', $settings) && is_array($settings['prices']) && array_key_exists('per_element', $settings['prices']) && $settings['prices']['per_element'] === true && __cp(['controller' => 'prices', 'action' => 'update', 'elements', $code], $auth) ? true : false;
    $cp_group = $menu['right'][0]['show'] || $menu['right'][1]['show'] ? true : false;

    $icons = count_true([$cp_settings, $cp_copy, ($cp_sort && !$global_sorting), $cp_media, $cp_delete, $cp_prices]);
    
    // colspan
    $colspan = 1;
    if($icons > 0 && $cp_group){
        $colspan = 3;
    }else if($icons > 0 || $cp_group){
        $colspan = 2;
    }
?>
<div class="<?= strtolower($this->name); ?> list">
    <?php if(count($elements) < 1){ ?>
    <div class="message info"><?= __d('be', 'No data available') ?></div>
    <?php }else{ ?>
    <table class="list" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <?php if($cp_group){ ?><th class="checkbox">&nbsp;</th><?php } ?>
                <th><?= $this->Paginator->sort('internal', __d('be', 'Internal name')) ?></th>
                <th width="<?= action_width($icons, $cp_update ? true : false); ?>" class="actions">&nbsp;</th>
            </tr>
        </thead>
        <?= $this->element('Backend.paginator', ['colspan' => $colspan]); ?>
        <tbody>
            <?php foreach($elements as $nr => $element){ ?>
            <tr data-id="<?= $element['id']; ?>" class="element<?= $nr%2 ? ' alternate' : ''; ?>">
                <?php if($cp_group){ ?><td class="checkbox"><input type="checkbox" name="element[]" id="element-<?= $element['id']; ?>" value="<?= $element['id']; ?>" /></td><?php } ?>
                <td><?= $element['internal']; ?></td>
                <td class="actions">
                    <?php if($cp_update){ ?>
                    <?= $this->element('Backend.flags', ['translations' => $element['_translations'], 'fallback' => true, 'url' => ['action' => 'update', $code, $category['id'], $element['id']]]); ?>
                    <?php } ?>
                    <?php if($cp_media){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'cubes', 'cls' => !is_array($element['media']) || count($element['media']) == 0 ? 'missing' : '', 'text' => __d('be', 'Media'), 'url' => ['action' => 'media', $code, $category['id'], $element['id']]]) ?>
                    <?php } ?>
                    <?php if($cp_settings){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'cogs', 'text' => __d('be', 'Settings'), 'url' => ['action' => 'settings', $code, $category['id'], $element['id']]]); ?>
                    <?php } ?>
                    <?php if($cp_prices){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'euro', 'text' => __d('be', 'Prices'), 'url' => ['controller' => 'prices', 'action' => 'update', 'elements', $code, $category['id'], 'false', $element['id']]]); ?>
                    <?php } ?>
                    <?php if($cp_copy){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'copy', 'text' => __d('be', 'Copy'), 'url' => ['action' => 'copy', $code, $category['id'], $element['id']]]) ?>
                    <?php } ?>
                    <?php if($cp_delete){ ?>
                        <?= $this->element('Backend.icon', ['icon' => 'trash', 'text' => __d('be', 'Delete'), 'url' => ['action' => 'delete', $code, $category['id'], $element['id']], 'confirm' => $settings['translations']['buttons']['delete']]) ?>
                    <?php } ?>
                    <?php if($cp_sort && !$global_sorting){ ?>
                        <?= $this->element('Backend.icon', ['cls' => 'sort', 'type' => 'span', 'icon' => 'sort', 'text' => __d('be', 'Sort')]) ?>
                    <?php } ?>
                    <div class="clear"></div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>
<script>

    var selected = 0;
    var __categories_order = <?= is_array($categories_order) ? json_encode($categories_order) : '{}'; ?>;
    var __categories = <?= is_array($categories) ? json_encode($categories) : '{}'; ?>;
    var __elements_order = <?= is_array($elements_order) ? json_encode($elements_order) : '{}'; ?>;
    var __elements = <?= is_array($all_elements) ? json_encode($all_elements) : '{}'; ?>;

    function saveCategoriesOrder(){
        <?php if(is_array($categories_order)){ ?>
        var order = {};
        $('.' + __system['popup']['class'] + ' ul.sort li').each(function(k,v){
            order[$(v).data('id')] = k;
        });
        
        $.ajax({
            url: $('nav .left .icon.sort').attr('href'),
            method: 'POST',
            data: {
                'order': order
            },
            dataType: 'json',
            success: function(data, status, request){
                if(data.success === true){
                    $('.' + __system['popup']['class'] + ' a.submit.cancel').trigger('click');
                }else{
                    alert(data.msg);
                }
            }
        });
        <?php } ?>
    }
    
    function saveElementsOrder(){
        <?php if($sortable && $global_sorting){ ?>
        var order = {};
        $('.' + __system['popup']['class'] + ' ul.sort li').each(function(k,v){
            order[k] = $(v).data('id');
        });
        
        $.ajax({
            url: $('nav .right .icon.sort').attr('href'),
            method: 'POST',
            data: {
                'order': order
            },
            dataType: 'json',
            success: function(data, status, request){
                if(data.success === true){
                    $('.' + __system['popup']['class'] + ' a.submit.cancel').trigger('click');
                }else{
                    alert(data.msg);
                }
            }
        });
        <?php } ?>
    }

    $(document).ready(function(){
        
        // categories
        $('select#element-category').change(function(){
            window.location.href = '<?= $this->Url->build(['action' => 'index', $code]) . DS; ?>' + $(this).val();
        });
        
        // group actions
        $('.button.group.move i').click(function(event){
            event.preventDefault();
            $(this).parents('.button.group.move').addClass('show');
        });
        
        $('.button.group.move a.cancel').click(function(event){
            event.preventDefault();
            $(this).parents('.button.group.move').removeClass('show');
        });
        
        $('.button.group.move a.send').click(function(event){
            event.preventDefault();
            if($("#move-category option:selected").val() == '<?= $category['id']; ?>'){
                alert('<?= __d('be', 'The elements are already in this category!'); ?>');
            }else if(confirm('<?= __d('be', 'Do you realy want to move the selected elements to "%s"?'); ?>'.replace('%s', $("#move-category option:selected").text()))){
                elements = [];
                $.each($('input:checked[name="element[]"]'), function(k,v){
                    elements[k] = $(v).val();
                });
                $.ajax({
                    url: $(this).parents('.button.group.move').data('url'),
                    method: 'POST',
                    data: {
                        'code': '<?= $code; ?>',
                        'category': $("#move-category option:selected").val(),
                        'elements': elements
                    },
                    dataType: 'json',
                    success: function(data, status, request){
                        if(data.success === true){
                            location.reload();
                        }else{
                            alert(data.msg);
                        }
                    }
                });
            }
            $(this).parents('.button.group.move').removeClass('show');
        });

        $('.icon.group.delete').click(function(event){
            event.preventDefault();
            if(confirm('<?= __d('be', 'Do you realy want to delete the selected elements?'); ?>')){
                elements = [];
                $.each($('input:checked[name="element[]"]'), function(k,v){
                    elements[k] = $(v).val();
                });
                $.ajax({
                    url: $(this).attr('href'),
                    method: 'POST',
                    data: {
                        'code': '<?= $code; ?>',
                        'elements': elements
                    },
                    dataType: 'json',
                    success: function(data, status, request){
                        if(data.success === true){
                            location.reload();
                        }else{
                            alert(data.msg);
                        }
                    }
                });
            }
        });

        $('.icon.reverse-selection').click(function(event){
            event.preventDefault();
            $('table.list tr.element input[type="checkbox"]').each(function(k,v){
                $(this).trigger('click');
            });
        });
        
        $('table.list tr.element input[type="checkbox"]').click(function(){
            if($(this).is(':checked')){
                selected++;
                $(this).parents('tr.element').addClass('selected');
            }else{
                selected--;
                $(this).parents('tr.element').removeClass('selected');
            }
            
            if(selected > 0){
                $('nav .icon.group, nav .button.group').removeClass('hidden');
            }else{
                $('nav .icon.group, nav .button.group').addClass('hidden');
            }
            
        });
        
        <?php if(is_array($categories_order)){ ?>
        $('nav .left .icon.sort').click(function(event){
            event.preventDefault();
             
            var order = '';
            $.each(__categories_order, function(k,v){
                order += '<li data-id="' + k + '">' + __categories[k] + '</li>';
            });

            $('<div class="' + __system['popup']['class'] + ' popup-wrapper inline period"><h2><?= __d('be', 'Categories order'); ?></h2><ul class="sort">' + order + '</ul><div class="actions"><a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Cancel'); ?></a><a href="javascript:saveCategoriesOrder();" class="submit save"><?= __d('be', 'Save'); ?></a><div class="clear"></div></div></div>').bPopup({
                opacity: __system['popup']['opacity'],
                modalClose: __system['popup']['modalClose'],
                closeClass: 'actions .cancel',
                onClose: function(){
                    destroyPopup();
                }
            }, function(){
                $('.' + __system['popup']['class'] + ' ul.sort').sortable({
                    axis: "y",
                    opacity: 0.8
                }).disableSelection();
            });
                
        });
        <?php } ?>
        
        <?php if($cp_sort && $global_sorting){ ?>
        $('nav .right .icon.sort').click(function(event){
            event.preventDefault();
             
            var order = '';
            $.each(__elements_order, function(k,v){
                order += '<li data-id="' + k + '">' + __elements[k] + '</li>';
            });

            $('<div class="' + __system['popup']['class'] + ' popup-wrapper inline period"><h2><?= __d('be', 'Elements order'); ?></h2><ul class="sort">' + order + '</ul><div class="actions"><a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Cancel'); ?></a><a href="javascript:saveElementsOrder();" class="submit save"><?= __d('be', 'Save'); ?></a><div class="clear"></div></div></div>').bPopup({
                opacity: __system['popup']['opacity'],
                modalClose: __system['popup']['modalClose'],
                closeClass: 'actions .cancel',
                onClose: function(){
                    destroyPopup();
                }
            }, function(){
                $('.' + __system['popup']['class'] + ' ul.sort').sortable({
                    axis: "y",
                    opacity: 0.8
                }).disableSelection();
            });
                
        });
        <?php } ?>
        
    });
    
</script>