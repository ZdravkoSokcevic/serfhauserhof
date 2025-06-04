<?php
	use Cake\Core\Configure;

    // premissions
    $cp_drafts_update = __cp(['controller' => 'drafts', 'action' => 'update', 'elements', $code], $auth);
    $cp_drafts_delete = __cp(['controller' => 'drafts', 'action' => 'delete', 'elements', $code], $auth);
    $cp_drafts_order = count($drafts) > 1 && __cp(['controller' => 'drafts', 'action' => 'order', 'elements', $code], $auth);
    
?>
<div class="<?= strtolower($this->name); ?> form<?= is_array($options) ? ' with-options' : ' no-options'; ?>">
    <?php if(count($items) < 1){ ?>
    <div class="message info"><?= __d('be', 'No data available') ?></div>
    <?php }else{ ?>
        <?php if(count($drafts) > 0){ ?>
        <div class="drafts">
            <?= $this->Form->input('draft', [
                'label' => false,
                'options' => $drafts,
                'value' => $draft,
                'templates' => ['inputContainer' => '{{content}}']
            ]) ?>
            <?php if(count($flags) > 1){ ?>
            <?= $this->Form->input('flag', [
                'label' => false,
                'options' => $flags,
                'value' => $flag,
                'templates' => ['inputContainer' => '{{content}}']
            ]) ?>
            <?php }else{ ?>
            <?= $this->Form->input('flag', [
                'label' => false,
                'value' => $flag,
                'type' => 'hidden'
            ]) ?>
            <?php } ?>
            <?php if($cp_drafts_update){ ?>
                <?= $this->element('Backend.icon', ['icon' => 'pencil', 'cls' => 'edit', 'text' => __d('be', 'Edit price draft'), 'url' => ['controller' => 'drafts', 'action' => 'update', $model, $code, $category, $season ? $season : 'false', '__ID__', $related]]); ?>
            <?php } ?>
            <?php if($cp_drafts_delete){ ?>
                <?= $this->element('Backend.icon', ['icon' => 'trash', 'cls' => 'delete', 'text' => __d('be', 'Delete price draft'), 'url' => ['controller' => 'drafts', 'action' => 'delete', $model, $code, $category, $season ? $season : 'false', '__ID__', $related]]); ?>
            <?php } ?>
            <?= $this->element('Backend.icon', ['icon' => 'reply-all fa-rotate-270', 'cls' => 'all', 'text' => __d('be', 'Add price draft to all items'), 'url' => []]); ?>
            <?php if($cp_drafts_update){ ?>
                <?= $this->element('Backend.icon', ['icon' => 'plus', 'cls' => 'add', 'text' => __d('be', 'Create new price draft'), 'url' => ['controller' => 'drafts', 'action' => 'update', $model, $code, $category, $season ? $season : 'false', 'false', $related]]); ?>
            <?php } ?>
            <?php if($cp_drafts_order){ ?>
                <?= $this->element('Backend.icon', ['icon' => 'sort', 'cls' => 'order', 'text' => __d('be', 'Sort price drafts'), 'url' => ['controller' => 'drafts', 'action' => 'order', $model, $code, $category, $season ? $season : 'false']]); ?>
            <?php } ?>
            <?= $this->element('Backend.icon', ['icon' => 'arrows', 'cls' => 'move', 'text' => __d('be', 'Move draft'), 'url' => []]); ?>
            <div class="clear"></div>
        </div>
        <?php }else if($cp_drafts_update){ ?>
        <div class="no-drafts">
            <span><?= __d('be', 'Create price draft to add prices') ?>:</span>
            <?= $this->element('Backend.icon', ['icon' => 'plus', 'cls' => 'add', 'text' => __d('be', 'Create new price draft'), 'url' => ['controller' => 'drafts', 'action' => 'update', $model, $code, $category, $season ? $season : 'false', 'false', $related]]); ?>
            <div class="clear"></div>
        </div>
        <?php } ?>
        <?= $this->Form->create(null, ['enctype' => 'multipart/form-data', 'onsubmit' => 'return checkPrices();']) ?>
            <?php foreach($items as $nr => $item){ ?>
                <?php $_options = array_key_exists($item['id'], $prices) ? $prices[$item['id']] : []; ?>
                <fieldset data-item="<?= $item['id']; ?>" data-option="false" data-element="false">
                    <?php if($related === false){ ?><legend><?= $item['internal']; ?></legend><?php } ?>
                    <?php if(is_array($options) && is_array($elements)){ ?>
                        <?php foreach($options as $k1 => $v1){ ?>
                            <fieldset data-item="<?= $item['id']; ?>" data-option="<?= $k1; ?>" data-element="false">
                                <legend><?= $v1; ?></legend>
                                <?php foreach($elements as $k2 => $v2){ ?>
                                    <fieldset data-item="<?= $item['id']; ?>" data-option="<?= $k1; ?>" data-element="<?= $k2; ?>">
                                        <legend><?= $v2; ?></legend>
                                        <?php if(array_key_exists($k1, $_options) && array_key_exists($k2, $_options[$k1])){ ?>
                                            <?php foreach($_options[$k1][$k2] as $draft => $input){ ?>
                                                <?php if(array_key_exists($draft, $drafts)){ ?>
                                            		<?php foreach($_options[$k1][$k2][$draft] as $flag => $input){ ?>
                                                    	<?= $this->element('Backend.price', ['parms' => ['settings' => $settings, 'text' => $drafts[$draft], 'item' => $item['id'], 'option' => $k1, 'element' => $k2, 'draft' => $draft, 'value' => $input['value'], 'flag' => $flag]]); ?>
                                                	<?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </fieldset>
                                <?php } ?>
                            </fieldset>
                        <?php } ?>
                    <?php }else if(is_array($options)){ ?>
                        <?php foreach($options as $k => $v){ ?>
                            <fieldset data-item="<?= $item['id']; ?>" data-option="<?= $k; ?>" data-element="false">
                                <legend><?= $v; ?></legend>
                                <?php if(array_key_exists($k, $_options) && array_key_exists('false', $_options[$k])){ ?>
                                    <?php foreach($_options[$k]['false'] as $draft => $flags){ ?>
                                        <?php if(array_key_exists($draft, $drafts)){ ?>
                                            <?php foreach($flags as $flag => $input){ ?>
                                            <?= $this->element('Backend.price', ['parms' => ['settings' => $settings, 'text' => $drafts[$draft], 'item' => $item['id'], 'option' => $k, 'element' => 'false', 'draft' => $draft, 'value' => $input['value'], 'flag' => $flag]]); ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </fieldset>
                        <?php } ?>
                    <?php }else if(is_array($elements)){ ?>
                        <?php foreach($elements as $k => $v){ ?>
                            <fieldset data-item="<?= $item['id']; ?>" data-option="false" data-element="<?= $k; ?>">
                                <legend><?= $v; ?></legend>
                                <?php if(array_key_exists('false', $_options) && array_key_exists($k, $_options['false'])){ ?>
                                    <?php foreach($_options['false'][$k] as $draft => $flags){ ?>
                                        <?php if(array_key_exists($draft, $drafts)){ ?>
                                            <?php foreach($flags as $flag => $input){ ?>
                                                <?= $this->element('Backend.price', ['parms' => ['settings' => $settings, 'text' => $drafts[$draft], 'item' => $item['id'], 'option' => 'false', 'element' => $k, 'draft' => $draft, 'value' => $input['value'], 'flag' => $flag]]); ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </fieldset>
                        <?php } ?>
                    <?php }else if(is_array($_options) && array_key_exists('false', $_options) && is_array($_options['false']) && array_key_exists('false', $_options['false']) && is_array($_options['false']['false'])){ ?>
                        <?php foreach($_options['false']['false'] as $draft => $flags){ ?>
                            <?php if(array_key_exists($draft, $drafts)){ ?>
                                <?php foreach($flags as $flag => $input){ ?>
                                    <?= $this->element('Backend.price', ['parms' => ['settings' => $settings, 'text' => $drafts[$draft], 'item' => $item['id'], 'option' => 'false', 'element' => 'false', 'draft' => $draft, 'value' => $input['value'], 'flag' => $flag]]); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </fieldset>
            <?php } ?>
            <?= $this->element('Backend.save') ?>
        <?= $this->Form->end() ?>
    <?php } ?>
</div>
<script>
    
    var __order = <?= is_array($order) ? json_encode($order) : '{}'; ?>;
    var __drafts = <?= is_array($drafts) ? json_encode($drafts) : '{}'; ?>;
    var __options = <?= is_array($options) ? json_encode($options) : 'false'; ?>;
    
    function removePrice(item, option, element, draft, flag){
        if(confirm('<?= __d('be', 'Do you really want to delete this price?'); ?>')){
            $('.prices.form form fieldset<?= $selector['delete']; ?>[data-item="' + item + '"][data-option="' + option + '"][data-element="' + element + '"] > div.prices').each(function(k,v){
                if($(v).data('draft') == draft && $(v).data('flag') == flag){
                    $(v).remove();
                }
            });
        }
    }
    
    function checkPrices(){
        var submit = true;
        var skip = false;
        $('.prices.form form > fieldset<?= $selector['find']; ?> .prices input').each(function(k,v){
            skip = $(this).attr('name').slice(-7) == '[value]' ? false : true;
            if(skip === false){
                var __check = $(this).val();
                if(__check.search(/^\d+$/) < 0 && __check.search(/^\d+\,\d{0,2}$/) < 0){
                    $(this).addClass('form-error');
                    submit = false;
                }else{
                    $(this).removeClass('form-error');
                }
            }
        });
        return submit;
    }
    
    function sortPrices(e){
        if(e === false){
            $('.prices.form form > fieldset<?= $selector['find']; ?>').each(function(k,v){
                sortPrices($(v));
            })
        }else{
            $(e).find('div.prices').sort(function(a,b){
                return (parseInt(__order[$(a).data('draft')]) < parseInt(__order[$(b).data('draft')])) ? -1 : (parseInt(__order[$(a).data('draft')]) > parseInt(__order[$(b).data('draft')])) ? 1 : 0;
            }).appendTo($(e));
        }
    }
    
    function saveDraftOrder(){

        var order = {};
        $('.' + __system['popup']['class'] + ' ul.sort li').each(function(k,v){
            order[$(v).data('draft')] = k;
        });
        
        $.ajax({
            url: $('div.drafts > a.icon.order').attr('href'),
            method: 'POST',
            data: {
                'order': order
            },
            dataType: 'json',
            success: function(data, status, request){
                if(data.success === true){
                    __order = data.order;
                    sortPrices(false);
                    $('.' + __system['popup']['class'] + ' a.submit.cancel').trigger('click');
                }else{
                    alert(data.msg);
                }
            }
        });
    }
    
    function addToAll(){
        if(confirm('<?= __d('be','Do you really want to add this price draft to every item?'); ?>')){
            var __draft = $('.drafts select#draft').val();
            var __flag = $('.drafts select#flag').val();
            <?php if(count($flags) > 1){ ?>
            var __flag = $('.drafts select#flag').val();
            <?php } else { ?>
            var __flag = $('.drafts input#flag').val();
            <?php }?>
            $('.prices.form form > fieldset<?= $selector['find']; ?>').each(function(k,v){
                if($(this).find('div.prices[data-draft="' + __draft + '"]').length == 0 || $(this).find('div.prices[data-flag="' + __flag + '"]').length == 0){
                    $(v).append(buildPriceDom($(v).data('item'), $(v).data('option'), $(v).data('element'), __draft, __flag));
                    sortPrices($(v));
                }
            });
        }
    }
    
    function buildPriceDom(item, option, element, draft, flag){
        var __dom = '<?= addcslashes($this->element('Backend.price', ['parms' => ['settings' => $settings]]),"'"); ?>';
        var __flag_cfg = <?= json_encode(Configure::read($model . '.' . $code . '.prices.flags')) ?>;
        __dom = __dom.replace(/%item/g, item);
        __dom = __dom.replace(/%option/g, option);
        __dom = __dom.replace(/%element/g, element);
        __dom = __dom.replace(/%draft/g, draft);
        __dom = __dom.replace(/%flag_label/g, <?php if(count($flags) > 1){ ?>__flag_cfg[flag]<?php } else { ?>''<?php } ?>);
        __dom = __dom.replace(/%flag/g, flag);
        __dom = __dom.replace(/%text/g, __drafts[draft]);
        return __dom;
    }
    
    $(document).ready(function(){
        
        $('.prices.form > div.drafts').draggable({
            appendTo: "body",
            handle: ".icon.move",
            helper: function(){
                var __draft = $('.drafts select#draft').val();
                <?php if(count($flags) > 1){ ?>
                var __flag = $('.drafts select#flag').val();
                <?php } else { ?>
                var __flag = $('.drafts input#flag').val();
                <?php }?>
                var __text = __drafts[__draft];
                return $('<div data-draft="' + __draft + '" data-flag="' + __flag + '" class="draft-drag-helper">' + __text + '</div>');
            }
        });
        
        $('.prices.form form > fieldset<?= $selector['find']; ?>').droppable({
            activeClass: "ui-state-default",
            hoverClass: "ui-state-hover",
            accept: ".drafts",
            drop: function( event, ui ) {
                var __check = true;
                var __draft = $(ui.helper).data('draft');
                var __flag = $(ui.helper).data('flag');
                if(__check){
                    // check
                    $(this).find('div.prices').each(function(k,v){
                        if($(v).data('draft') == __draft && $(v).data('flag') == __flag){
                            __check = false;
                        }
                    });
                    if(__check){
                        $(this).append(buildPriceDom($(this).data('item'), $(this).data('option'), $(this).data('element'), __draft, __flag));
                        sortPrices($(this));
                    }else{
                        alert('<?= __d('be', 'This price draft already exists!'); ?>');
                    }
                    
                }else{
                    alert('<?= __d('be', 'The price option "%s" is not allowed here!'); ?>'.replace('%s', __options[__option]));
                }

            }
        });
        
        // sort
        sortPrices(false);
        
        // categories
        $('select#price-category').change(function(){
            window.location.href = '<?= urldecode( $this->Url->build(['action' => 'update', $model, $code, '%s', $season ? $season : 'false', $related]) . DS); ?>'.replace("%s", $(this).val());
        });
        
        // seasons
        $('select#price-season').change(function(){
            window.location.href = '<?= urldecode($this->Url->build(['action' => 'update', $model, $code, $category, '%s', $related]) . DS); ?>'.replace("%s", $(this).val());
        });
        
        $('.drafts .icon').click(function(event){
            event.preventDefault();
            var __url = $(this).attr('href');
            var __draft = $('.drafts select#draft').val();
            if($(this).hasClass('add') == true){
                window.location.href = __url;
            }else if($(this).hasClass('edit') == true){
                window.location.href = __url.replace("<?= DS ?>__ID__", "<?= DS ?>" + __draft);
            }else if($(this).hasClass('delete') == true){
                if(confirm('<?= __d('be', 'Do you really want to delete this price draft and all related prices?'); ?>')){
                    window.location.href = __url.replace("<?= DS ?>__ID__", "<?= DS ?>" + __draft);
                }
            }else if($(this).hasClass('all') == true){
                addToAll();
            }else if($(this).hasClass('order') == true){
             
                var order = '';
                $.each(__order, function(k,v){
                    order += '<li data-draft="' + k + '">' + __drafts[k] + '</li>';
                });
             
                $('<div class="' + __system['popup']['class'] + ' popup-wrapper inline period"><h2><?= __d('be', 'Price draft order'); ?></h2><ul class="sort">' + order + '</ul><div class="actions"><a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Cancel'); ?></a><a href="javascript:saveDraftOrder();" class="submit save"><?= __d('be', 'Save'); ?></a><div class="clear"></div></div></div>').bPopup({
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
                
            }
        });
        
    });
    
</script>