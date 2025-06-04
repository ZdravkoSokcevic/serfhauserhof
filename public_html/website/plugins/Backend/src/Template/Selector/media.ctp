<?php if((is_array($images) && count($images) > 0) || (is_array($elements) && count($elements) > 0) || (is_array($nodes) && count($nodes) > 0) || (is_array($categories) && count($categories) > 0)){ ?>
    <?php if(is_array($editor) && array_key_exists('content', $editor) && $editor['content'] == 'links'){ ?>
    <div class="tabs">
        <a href="javascript:mediaTab(1);" class="tab-1 active"><?= __d('be', 'List'); ?></a>
        <a href="javascript:mediaTab(2);" class="tab-2"><?= __d('be', 'Custom'); ?></a>
        <div class="clear"></div>
    </div>
    <?php } ?>
    <div class="tab tab-1">
        <div class="filter<?= is_array($sizes) && $selected == 'image' ? ' multi' : ''; ?>">
            <select id="selector-category" name="selector-category">
                <?php foreach($filter as $k => $v){ ?>
                    <?= $this->element('Backend.options', ['key' => $k, 'option' => $v, 'labels' => array_flip($keys), 'selected' => $selected]); ?>
                <?php } ?>
            </select>
            <?php if(is_array($sizes)){ ?>
            <select id="image-size" name="image-size" class="<?php echo $selected == 'image' ? '' : 'hidden'; ?>">
                <?php foreach($sizes as $k => $v){ ?>
                <?= $this->element('Backend.options', ['key' => $k, 'option' => $v]); ?>
                <?php } ?>
            </select>
            <?php } ?>
            <div class="clear"></div>
            <input class="search" value="" placeholder="<?= __d('be', 'Search'); ?>" />
            <div class="clear"></div>
        </div>
        <?php if(is_array($editor)){ ?>
        <div class="options">
            <?php foreach($editor['options'] as $code => $options){ ?>
                <?php foreach($options as $_name => $_settings){ ?>
                    <?php if($_settings['type'] == 'text'){ ?>
                        <input data-code="element:<?= $code; ?>" data-required="<?= array_key_exists('required', $_settings) && $_settings['required'] == true ? 'true' : 'false'; ?>" class="<?= 'element:'. $code == $selected ? '' : 'hidden'; ?>" type="text" id="<?= 'element_'. $code . '_' . $_name; ?>" name="<?= 'element:'. $code . ':' . $_name; ?>" value="" placeholder="<?= $_settings['text']; ?>" />
                    <?php } else if($_settings['type'] == 'select' && array_key_exists('options', $_settings) && is_array($_settings['options'])){ ?>
                        <select data-code="element:<?= $code; ?>" data-required="<?= array_key_exists('required', $_settings) && $_settings['required'] == true ? 'true' : 'false'; ?>" class="<?= 'element:'. $code == $selected ? '' : 'hidden'; ?>" id="<?= 'element_'. $code . '_' . $_name; ?>" name="<?= 'element:'. $code . ':' . $_name; ?>">
                            <option value="">-- <?= $_settings['text']; ?> --</option>
                            <?php foreach($_settings['options'] as $_value => $_text){ ?>
                                <option value="<?= $_value; ?>"><?= $_text; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <div class="html-preview hidden"></div>
            <input type="text" placeholder="<?= __d('be', 'Link text'); ?>" value="" name="node:node:title" id="node_title" class="<?= substr($selected,0,5) == 'node:' ? '' : 'hidden'; ?>" data-required="true" data-code="node">
            <select data-code="node" data-required="false" class="<?= substr($selected,0,5) == 'node:' ? '' : 'hidden'; ?>" id="node_class" name="node:node:class">
                <option value="">-- <?= __d('be', 'CSS-Class'); ?> --</option>
                <?php foreach($link_classes as $_value => $_text){ ?>
                    <option value="<?= $_value; ?>"><?= $_text; ?></option>
                <?php } ?>
            </select>
            <?php if($multi == false){ ?>
            <select data-code="node" disabled="disabled" data-required="false" class="<?= substr($selected,0,5) == 'node:' ? '' : 'hidden'; ?>" id="node_anchor" name="node:node:anchor">
                <option value="">-- <?= __d('be', 'Anchor'); ?> --</option>
            </select>
            <?php } ?>
        </div>
        <?php }else if(false && $multi == false){ // TODO: add anchor functionality! ?>
        <div class="options">
            <select data-code="node" disabled="disabled" data-required="false" class="<?= substr($selected,0,5) == 'node:' ? '' : 'hidden'; ?>" id="node_anchor" name="node:node:anchor">
                <option value="">-- <?= __d('be', 'Anchor'); ?> --</option>
            </select>
        </div>
        <?php } ?>
        <div class="clear"></div>
        <div class="selector-inner">
            <form class="selector-form">
            <?php if(is_array($images)){ ?>
                <?php foreach($images as $image){ ?>
                    <div class="item image<?php if(!empty($image['purpose'])){ echo ' purpose-' . join(' purpose-', array_filter(explode(",", $image['purpose']))); }; ?><?php echo $selected == 'image' ? '' : ' hidden'; ?>" id="<?= $image['id']; ?>" data-code="image" data-category="<?= $image['category_id']; ?>">
                        <div class="title">
                            <input type="checkbox" name="image[]" id="image-<?= $image['id']; ?>" data-type="image" data-filename="<?= $image['id']; ?>.<?= $image['extension']; ?>" data-title="<?= $image['title']; ?>" data-org="<?= $image['original']; ?>" value="<?= $image['id']; ?>" />
                            <label title="<?= $image['title']; ?>" for="image-<?= $image['id']; ?>"><?= $this->Text->truncate($image['title'],40); ?></label>
                            <div class="clear"></div>
                        </div>
                        <span class="original" title="<?= $image['original']; ?>"><?= $this->Text->truncate($image['original'], 40); ?></span>
                        <div class="preview" style="background-image: url('/img/thumbs/<?= $image['id']; ?>.<?= $image['extension']; ?>');"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if(is_array($elements)){ ?>
                <?php foreach($elements as $element){ ?>
                    <div class="item element<?php echo 'element:' . $element['code'] == $selected ? '' : ' hidden'; ?>" id="<?= $element['id']; ?>" data-code="element:<?= $element['code']; ?>" data-category="<?= $element['category_id']; ?>">
                        <div class="title">
                            <i class="fa fa-<?= $settings[$element['code']]['icon']; ?>"></i>
                            <input type="checkbox" name="element[]" id="element-<?= $element['id']; ?>" data-icon="<?= $settings[$element['code']]['icon']; ?>" data-title="<?= $element['internal']; ?>" data-type="<?= $element['code']; ?>" data-desc="<?= $settings[$element['code']]['translations']['type']; ?>" value="<?= $element['id']; ?>" />
                            <label title="<?= $element['internal']; ?>" for="element-<?= $element['id']; ?>">
                                <?= $this->Text->truncate($element['internal'],40); ?>
                                <span><?= $settings[$element['code']]['translations']['type']; ?></span>
                            </label>
                            <div class="clear"></div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if(is_array($nodes)){ ?>
                <?php foreach($nodes as $node){ ?>
                    <div class="item node<?php echo 'node:' . $node['structure_id'] == $selected ? '' : ' hidden'; ?>" id="<?= $node['id']; ?>" data-code="node" data-category="<?= $node['structure_id']; ?>">
                        <div class="title">
                            <i class="fa fa-file-text-o"></i>
                            <input type="checkbox" name="node[]" id="node-<?= $node['id']; ?>" data-icon="file-text-o" data-title="<?= $node['internal']; ?>" data-type="node" data-desc="<?= __d('be','Node'); ?>" value="<?= $node['id']; ?>" />
                            <label title="<?= $node['internal']; ?>" for="node-<?= $node['id']; ?>">
                                <?= $this->Text->truncate($node['internal'],40); ?>
                                <span><?= __d('be', 'Node'); ?></span>
                            </label>
                            <div class="clear"></div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if(is_array($categories)){ ?>
                <?php foreach($categories as $id => $category){ ?>
                    <div class="item category<?php echo $selected == 'category' ? '' : ' hidden'; ?>" id="<?= $id; ?>" data-code="category" data-category="<?= $category['category']; ?>">
                        <div class="title">
                            <i class="fa fa-folder-o"></i>
                            <input type="checkbox" name="category[]" id="category-<?= $id; ?>" data-icon="folder-o" data-title="<?= $category['title']; ?>" data-type="category" data-desc="<?= __d('be','Category'); ?>" value="<?= $id; ?>" />
                            <label title="<?= $category['title']; ?>" for="category-<?= $id; ?>">
                                <?= $this->Text->truncate($category['title'],40); ?>
                                <span><?= __d('be', 'Category'); ?></span>
                            </label>
                            <div class="clear"></div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            </form>
        </div>
        <div class="clear"></div>
        <div class="clear"></div>
        <div class="actions">
            <a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Cancel'); ?></a>
            <?php if($multi && false){ ?><a href="javascript:deselect();" class="submit deselect multi"><?= __d('be', 'Deselect'); ?></a><?php } ?>
            <a href="javascript:finish();" class="submit save inactive"><?= is_array($editor) ? __d('be', 'Insert') : __d('be', 'OK'); ?><span></span></a>
        </div>
    </div>
    <?php if(is_array($editor) && array_key_exists('content', $editor) && $editor['content'] == 'links'){ ?>
    <div class="custom tab tab-2 hidden">
        <select data-required="true" name="custom:link:type" id="custom-type" class="first">
            <option value=""><?= __d('be', 'Type'); ?></option>
            <option value="mail"><?= __d('be', 'E-Mail'); ?></option>
            <option value="tel"><?= __d('be', 'Telephone'); ?></option>
            <option value="www"><?= __d('be', 'Website'); ?></option>
        </select>
        <div class="html-preview hidden"></div>
        <input data-required="true" type="text" value="" placeholder="<?= __d('be', 'Link text'); ?>" id="custom-text" name="custom:link:text" class="selection" />
        <input data-required="true" type="text" value="" placeholder="<?= __d('be', 'Link URL'); ?>" id="custom-url" name="custom:link:url" />
        <select data-required="false" name="custom:link:class" id="custom-class">
            <option value=""><?= __d('be', 'CSS-Class'); ?></option>
            <?php foreach($link_classes as $_value => $_text){ ?>
                <option value="<?= $_value; ?>"><?= $_text; ?></option>
            <?php } ?>
        </select>
        <select data-required="false" name="custom:link:target" id="custom-target">
            <option value=""><?= __d('be', 'Target'); ?></option>
            <option value="_blank"><?= __d('be', 'New window'); ?></option>
            <option value="_self"><?= __d('be', 'Same window'); ?></option>
        </select>
        <div class="clear"></div>
        <div class="actions">
            <a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Cancel'); ?></a>
            <a href="javascript:custom();" class="submit save"><?= __d('be', 'Insert'); ?></a>
        </div>
    </div>
    <?php } ?>

    <script>

        var __act = <?php echo (int) $act; ?>;
        var __max = <?php echo $max === false ? 'false' : $max; ?>;
        var __selected = 0;
        var __editor = <?= is_array($editor) ? json_encode($editor) : '{}'; ?>;

        function mediaTab(idx){
            $('.selector-wrapper .tabs a').removeClass('active');
            $('.selector-wrapper .tabs a.tab-' + idx).addClass('active');
            $('.selector-wrapper div.tab').addClass('hidden');
            $('.selector-wrapper div.tab.tab-' + idx).removeClass('hidden');
        }

        function getType(name){
            return name.slice(0,-2);
        }

        function getModel(name){
            var model = false;
            switch(name.slice(0,-2)){
                case 'image':
                    model = 'images';
                    break;
                case 'element':
                    model = 'elements';
                    break;
                case 'node':
                    model = 'nodes';
                    break;
                case 'category':
                    model = 'categories';
                    break;
            }
            return model;
        }

        function deselect(){
            __selected = 0;
            $('.selector-wrapper .item').removeClass('selected');
            $('.selector-wrapper .item input[type="checkbox"]').prop( "checked", false);
            $('.selector-wrapper a.submit.save span').text('');
            $('.selector-wrapper a.submit.save').addClass('inactive');
        }

        function refreshAnchors(node){

            // clear
            $('#node_anchor').attr('disabled', 'disabled');
            $('#node_anchor').find('optgroup').remove();

            // fill
            if(node !== false && $(node).data('type') == 'node'){
                $.ajax({
                    url: '/admin/' + __system['locale'] + '/selector/anchors/',
                    method: 'POST',
                    data: {
                        'id': $(node).val(),
                        'structure': $('#selector-category').val()
                    },
                    dataType: 'json',
                    success: function(data, status, request){
                        if(data.success === true){
                            $.each(data.anchors, function(label, options){
                                var og = $('<optgroup label="' + label + '">');
                                $.each(options, function(value,text){
                                    $(og).append('<option value="' + value + '">' + text + '</option>');
                                });
                                $('#node_anchor').append(og);
                            });
                        }else{
                            alert(data.msg);
                        }
                    }
                });
                $('#node_anchor').removeAttr('disabled');
            }
        }

        function custom(){
            var __error = false;
            var output = false;
            $('.custom.tab input').removeClass('form-error');
            $('.custom.tab select').removeClass('form-error');
            <?php if(is_array($editor) && array_key_exists('content', $editor) && $editor['content'] == 'links'){ ?>
                output = '<a href="%url" class="%class" target="%target">%text</a>';
                var _type = false;
                $.each(['type', 'text','url','class','target'], function(_k,_v){
                    var _val = $('#custom-' + _v).val();
                    var _required = $('#custom-' + _v).data('required');

                    if(_v == 'type'){
                        _type = _val;
                    }

                    if(_val == '' && _required == true){
                        __error = true;
                        output = false;
                        $('#custom-' + _v).addClass('form-error');
                    }else if(__error == false){
                        if(_v == 'url'){
                            if(_type == 'www' && _val.slice(0,7) != 'http://' && _val.slice(0,8) != 'https://'){
                                __error = true;
                                output = false;
                            }else if(_type == 'mail' && _val.slice(0,7) != 'mailto:'){
                                __error = true;
                                output = false;
                            }else if(_type == 'tel' && _val.slice(0,4) != 'tel:'){
                                __error = true;
                                output = false;
                            }
                        }
                        if(__error == false){
                            var regex = new RegExp('%' + _v, 'g');
                            output = output.replace(regex, _val);
                        }else{
                            alert(__translations['selector']['custom'][_type]);
                        }
                    }
                });
            <?php }else{ ?>
                __error = true;
            <?php } ?>

            if(output){
                tinyMCE.activeEditor.execCommand('mceInsertContent', false, output);
            }

            if(__error === false){
                $('.selector-wrapper a.submit.cancel').trigger('click');
            }
        }

        function finish(){
            if($('.selector-wrapper a.submit.save').hasClass('inactive') === false){

                var __error = false;

                <?php if(isset($mode) && $mode == 'node'){ ?>
                    $.ajax({
                        url: '/admin/' + __system['locale'] + '/nodes/create/<?= $structure; ?>',
                        method: 'POST',
                        data: $('form.selector-form').serializeArray(),
                        dataType: 'json',
                        success: function(data, status, request){
                            if(data.success === true){
                                var node = $("#tree").fancytree("getActiveNode");
                                if(node == null){
                                    node = $("#tree").fancytree("getRootNode");
                                }
                                node.addChildren(data.node);
                            }else{
                                alert(data.msg);
                            }
                        }
                    });
                <?php }else if(isset($mode) && $mode == 'button'){ ?>
                    <?php if(is_array($editor)){ ?>
                        $('.selector-wrapper .item input[type="checkbox"]:checked').each(function(k,v){

                            var output = false;
                            var title = $(v).data('title');
                            var model = getModel($(v).attr('name'));
                            var code = $(v).data('type');
                            var id = $(v).val();

                            if($(v).data('type') == 'image'){
                                var filename = $(v).data('filename');
                                var purpose = <?= $size ? "'".$size."'" : "$('select#image-size').val()"; ?>;
                                output = '<img data-model="' + model + '" data-code="' + code + '" data-purpose="' + purpose + '" data-id="' + id + '" src="/img/' + purpose + '/' + filename + '" />';
                            }else if($(v).data('type') == 'node'){
                                output = '<a href="#" class="%class" data-anchor="%anchor" data-model="' + model + '" data-code="' + code + '" data-id="' + id + '">%title</a>';

                                $('.selector-wrapper .options input').removeClass('form-error');
                                $('.selector-wrapper .options select').removeClass('form-error');

                                $.each({'title': true, 'class': true, 'anchor': true}, function(_k,_v){
                                    var _val = $('#node_' + _k).val();
                                    var _required = $('#node_' + _k).data('required');
                                    if(_val == '' && _required == true){
                                        __error = true;
                                        output = false;
                                        $('#node_' + _k).addClass('form-error');
                                    }else if(__error == false){
                                        var regex = new RegExp('%' + _k, 'g');
                                        output = output.replace(regex, _val);
                                    }
                                });

                            }else if(__editor['templates'][$(v).data('type')]){
                                output = __editor['templates'][$(v).data('type')];

                                // special options
                                if(__editor['options'] && __editor['options'][$(v).data('type')]){
                                    $.each(__editor['options'][$(v).data('type')], function(_k,_v){
                                        if(__error === false){
                                            var _val = $('#element_' + $(v).data('type') + '_' + _k).val();
                                            var _required = $('#element_' + $(v).data('type') + '_' + _k).data('required');
                                            $('.selector-wrapper .options input').removeClass('form-error');
                                            if(_val == '' && _required == true){
                                                __error = true;
                                                output = false;
                                                $('#element_' + $(v).data('type') + '_' + _k).addClass('form-error');
                                            }else{
                                                var regex = new RegExp('%' + _k, 'g');
                                                output = output.replace(regex, _val);
                                            }
                                        }
                                    });
                                }

                                // default settings
                                if(__error === false){
                                    output = output.replace(/%title/g, title);
                                    output = output.replace(/%model/g, model);
                                    output = output.replace(/%code/g, code);
                                    output = output.replace(/%id/g, id);
                                }

                            }else{
                                output = '<div data-model="' + model + ' data-code="' + code + '" data-id="' + id +'" class="">' + title + '</div>';
                            }
                            if(output){
                                tinyMCE.activeEditor.execCommand('mceInsertContent', false, output);
                            }
                        });
                    <?php }else{ ?>
                        var act = $('.<?= $rel; ?> input').val();
                        var obj = $('form.selector-form').serializeArray();
                        var sel = '';
                        var sep = '';
                        <?php if($multi){ ?>
                        if(act.length > 0){
                            sel = act;
                            sep = ';';
                        }
                        $.each(obj, function(k,v){
                            var type = getType(v['name']);
                            sel += sep + type + ':' + v['value'];
                            sep = ';';
                        });
                        <?php }else{ ?>
                        $.each(obj, function(k,v){
                            var type = getType(v['name']);
                            sel = type + ':' + v['value'];
                        });
                        <?php } ?>
                        $('.<?= $rel; ?> input').val(sel); // replace
                        selectorpreview('<?= $rel; ?>');
                    <?php } ?>
                <?php }else{ ?>
                    $('.selector-wrapper .item input[type="checkbox"]:checked').each(function(k,v){
                        var __dom = '';
                        if($(v).data('type') == 'image'){
                            __dom = '<?= addcslashes($this->element('Backend.media', ['type' => 'image']),"'"); ?>';
                            __dom = __dom.replace(/%type/g, $(v).data('type'));
                            __dom = __dom.replace(/%id/g, $(v).val());
                            __dom = __dom.replace(/%filename/g, $(v).data('filename'));
                            __dom = __dom.replace(/%title/g, $(v).data('title'));
                            __dom = __dom.replace(/%original/g, $(v).data('org'));
                        }else if($(v).data('type') == 'category'){
                            __dom = '<?= addcslashes($this->element('Backend.media', ['type' => 'category']),"'"); ?>';
                            __dom = __dom.replace(/%type/g, $(v).data('type'));
                            __dom = __dom.replace(/%id/g, $(v).val());
                            __dom = __dom.replace(/%title/g, $(v).data('title'));
                            __dom = __dom.replace(/%desc/g, $(v).data('desc'));
                        }else{
                            __dom = '<?= addcslashes($this->element('Backend.media', ['type' => 'element']),"'"); ?>';
                            __dom = __dom.replace(/%type/g, $(v).data('type'));
                            __dom = __dom.replace(/%id/g, $(v).val());
                            __dom = __dom.replace(/%icon/g, $(v).data('icon'));
                            __dom = __dom.replace(/%title/g, $(v).data('title'));
                            __dom = __dom.replace(/%desc/g, $(v).data('desc'));
                        }
                        $('.block[data-block="<?= $block; ?>"] > div.clear').before(__dom);
                    });
                <?php } ?>
                if(__error === false){
                    $('.selector-wrapper a.submit.cancel').trigger('click');
                }
            }
        }

        function searching(e){
            var term = $(e).val();
            $('.popup-wrapper .selector-inner form.selector-form div.item').removeClass('match');
            if(term.length > 0){
                $('.popup-wrapper .selector-inner form.selector-form').addClass('searching');
                $('.popup-wrapper .selector-inner form.selector-form div.item').not('.hidden').each(function(k,v){
                    var label = $(this).find('label').attr('title');
                    if($(this).hasClass('image')){
                        var org = $(this).find('span.original').attr('title');
                        if(label.search(new RegExp(term, "i")) >= 0 || org.search(new RegExp(term, "i")) >= 0){
                            $(this).addClass('match');
                        }
                    }else{
                        if(label.search(new RegExp(term, "i")) >= 0){
                            $(this).addClass('match');
                        }
                    }
                });
            }else{
                $('.popup-wrapper .selector-inner form.selector-form').removeClass('searching');
            }
        }

        $(document).ready(function(){

            <?php if(is_array($editor)){ ?>
            // prefill
            var prefill = tinyMCE.activeEditor.selection.getContent({format : 'text'});
            if(prefill){
                <?php foreach($editor['options'] as $code => $options){ ?>
                    <?php foreach($options as $_name => $_settings){ ?>
                        <?php if(array_key_exists('prefill', $_settings) && $_settings['prefill'] == 'selected'){ ?>
                            $('#<?= 'element_'. $code . '_' . $_name; ?>').val(prefill);
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                $('#node_title, #element_link_title, #element_download_title').val(prefill);
                $('input.selection').val(prefill);
            }else{
                prefill = tinyMCE.activeEditor.selection.getContent();
                if(prefill.substr(0,4) == '<img'){
                    $('div.html-preview').html(prefill).removeClass('hidden');
                    $('#node_title, #element_link_title, #element_download_title').val(prefill).addClass('hidden');
                    $('input.selection').val(prefill).addClass('hidden');
                }
            }
            <?php } ?>

            // search
            $('.popup-wrapper .filter input.search').on('keyup', function(event){
                searching($(this));
            });

            // categories
            $('select#selector-category, select#image-size').change(function(){

                var type = $('select#selector-category').find('option:selected').data('type');
                var val = $('select#selector-category').val();
                var size = $('select#image-size').val();
                var sel = type == 'image' && size != undefined && size != 'original' ? '.purpose-' + size : '';

                if(type == 'image'){
                    $('select#image-size').removeClass('hidden');
                    if(size != undefined){
                        $('.selector-wrapper .filter').addClass('multi');
                    }
                }else{
                    $('select#image-size').addClass('hidden');
                    if(size != undefined){
                        $('.selector-wrapper .filter').removeClass('multi');
                    }
                }

                if(val.indexOf('all:') >=  0){
                    $('.selector-wrapper .item').addClass('hidden');
                    $('.selector-wrapper .item' + sel + '[data-code="' + val.substring(4) + '"]').removeClass('hidden');
                }else{
                    $('.selector-wrapper .item').addClass('hidden');
                    $('.selector-wrapper .item' + sel + '[data-category="' + val + '"]').removeClass('hidden');
                }

                // options?
                $('.selector-wrapper .options').addClass('hidden');
                $('.selector-wrapper .options input').addClass('hidden').removeClass('form-error');
                $('.selector-wrapper .options select').addClass('hidden').removeClass('form-error');
                if(type.indexOf('element:') >= 0){
                    var __code = type.slice(8);
                    if(__editor['options'] && __editor['options'][__code]){
                        $('.selector-wrapper .options input[data-code="' + type + '"]').removeClass('hidden');
                        $('.selector-wrapper .options select[data-code="' + type + '"]').removeClass('hidden');
                        $('.selector-wrapper .options').removeClass('hidden');
                    }
                }else if(type == 'node'){
                    $('.selector-wrapper .options input[data-code="' + type + '"]').removeClass('hidden');
                    $('.selector-wrapper .options select[data-code="' + type + '"]').removeClass('hidden');
                    $('.selector-wrapper .options').removeClass('hidden');
                }

                // search
                searching($('.popup-wrapper .filter input.search'));

            });

            $('.selector-wrapper .item input[type="checkbox"]').click(function(){
                if($(this).is(':checked')){
                    <?php if($multi){ ?>
                    if(__max === false || (__selected + __act) < __max){
                        __selected++;
                        $(this).parents('.item').addClass('selected');
                        $('.selector-wrapper a.submit.save span').text(' (' + __selected + ')');
                    }else{
                        $(this).prop( "checked", false);
                        alert('<?= __d('be', 'Max. %s elements allowed!', $max); ?>');
                    }
                    <?php }else{ ?>
                    deselect();
                    refreshAnchors($(this));
                    __selected = 1;
                    $(this).parents('.item').addClass('selected');
                    $(this).prop( "checked", true);
                    <?php } ?>
                }else{
                    __selected--;
                    <?php if($multi){ ?>
                    if(__selected > 0){
                        $('.selector-wrapper a.submit.save span').text(' (' + __selected + ')');
                    }else{
                        deselect();
                    }
                    <?php }else{ ?>
                    refreshAnchors(false);
                    <?php } ?>
                    $(this).parents('.item').removeClass('selected');
                }

                if(__selected > 0){
                    $('.selector-wrapper a.submit.save').removeClass('inactive');
                }else{
                    $('.selector-wrapper a.submit.save').addClass('inactive');
                }

            });

        });

    </script>
<?php }else{ ?>
    <div class="error message"><?= __d('be', 'No data available!'); ?></div>
    <div class="actions error-actions">
        <a href="javascript:void(0);" class="submit cancel"><?= __d('be', 'OK'); ?></a>
        <div class="clear"></div>
    </div>
<?php } ?>
