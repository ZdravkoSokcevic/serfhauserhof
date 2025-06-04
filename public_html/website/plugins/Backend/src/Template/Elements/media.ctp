<?php
    echo $this->Html->css('Backend.media.css', ['block' => true]);
?>
<div class="<?= strtolower($this->name); ?> media">
    <?php if(!is_array($themes) || count($themes) < 1){ ?>
        <div class="message info"><?= __d('be', 'At least one theme has to be specified!') ?></div>
    <?php }else if(!array_key_exists($theme, $settings['media'])){ ?>
        <div class="message info"><?= __d('be', 'There are no settings for this theme available!') ?></div>
    <?php }else{ ?>
        <?= $this->Form->create(false, ['enctype' => 'multipart/form-data', 'onsubmit' => 'return prepareMedia();']) ?>
            <?= $this->element('Backend.save', ['padding' => 'bottom']) ?>
            <?php foreach($settings['media'][$theme] as $block => $info){ ?>
                <?= $this->Form->hidden('media[' . $theme . '][' . $block . ']', ['id' => $block, 'value' => '']) ?>
                <?php if($info['type'] == 'bar'){ ?>
                <div class="clear"></div>
                <?php } ?>
                <?php if($info['type'] == 'center'){ ?>
                <div class="block-spacer <?= $info['type']; ?>">
                <?php } ?>
                <div data-block="<?= $block; ?>"<?php echo array_key_exists('max', $info) ? ' data-max="' . $info['max'] . '"' : ''; ?> class="block <?= $info['type']; ?>">
                    <div class="label"><?= $info['label']; ?></div>
                    <a class="add" href="javascript:media('<?= $block; ?>');"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-plus fa-stack-1x fa-inverse"></i></span></a>
                    <?php if(is_array($infos) && array_key_exists($theme, $infos) && array_key_exists($block, $infos[$theme])){ ?>
                        <?php foreach($infos[$theme][$block] as $b){ ?>
                            <?= $this->element('Backend.media', $b); ?>
                        <?php } ?>
                    <?php } ?>
                    <div class="clear"></div>
                </div>
                <?php if($info['type'] == 'center'){ ?>
                </div>
                <?php } ?>
                <?php if($info['type'] == 'bar'){ ?>
                <div class="clear"></div>
                <?php } ?>
            <?php } ?>
            <?= $this->element('Backend.save') ?>
        <?= $this->Form->end() ?>
    <?php } ?>
</div>
<script>
    
    function prepareMedia(){
        $('.block').each(function(k,v){
            if($(v).data('block')){
                var val = '';
                var sep = '';
                $(v).find('.item').each(function(_k,_v){
                    switch($(_v).data('type')){
                        case 'image':
                        case 'category':
                        case 'node':
                            var type = $(_v).data('type');
                            break;
                        default:
                            var type = 'element';
                            break;
                    }
                    val += sep + type + ':' + $(_v).data('id');
                    sep = ';';
                });
                $('input#' + $(v).data('block')).val(val);
            }
        });
        return true;
    }
    
    function media(block){
        
        var _block = $('div[data-block="' + block + '"]');
        var _added = $(_block).find('div.item');
        var _max = $(_block).data('max') ? $(_block).data('max') : false;
        
        if(_max === false || _max > _added.length){
            var _url = '/admin/' + __system['locale'] + '/selector/media/<?= $code; ?>/<?= $theme; ?>/' + block + '/' + _max + '/' + _added.length;
        }else{
            var _url = '/admin/' + __system['locale'] + '/selector/error/max/' + _max;
        }
            
        $('<div class="' + __system['popup']['class'] + '">').bPopup({
            opacity: __system['popup']['opacity'],
            modalClose: __system['popup']['modalClose'],
            content: 'ajax',
            closeClass: 'actions .cancel',
            loadUrl: _url,
            onClose: function(){
                destroyPopup();
            }
        });
    
    }
    
    $(document).ready(function(){
        
        // categories
        $('select#theme-dropdown').change(function(){
            window.location.href = '<?= $this->Url->build(['action' => 'media', $code, $category, $id]) . DS; ?>' + $(this).val();
        });
        
        // delete
        $(document).on("click", ".block .item a.remove", function(event) {
            event.preventDefault();
            if(confirm('<?= __d('be', 'Do you really want to remove this item?'); ?>')){
                $(this).parents('.item').remove();
            }
        });
        
        // sort
        $('div.block').sortable({
            opacity: 0.8,
            items: '.item'
        }).disableSelection();
        
    });
    
</script>