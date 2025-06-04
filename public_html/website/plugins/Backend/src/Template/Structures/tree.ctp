<?php

    use Cake\Core\Configure;

    echo $this->Html->script('Backend.jquery.fancytree.js', ['block' => true]);
    echo $this->Html->script('Backend.jquery.fancytree.dnd.js', ['block' => true]);
    echo $this->Html->script('Backend.jquery.fancytree.glyph.js', ['block' => true]);
    echo $this->Html->script('Backend.jquery.fancytree.edit.js', ['block' => true]);
    echo $this->Html->script('Backend.jquery.fancytree.table.js', ['block' => true]);
    echo $this->Html->script('Backend.jquery.fancytree.gridnav.js', ['block' => true]);
    echo $this->Html->css('Backend.ui.fancytree.css', ['block' => true]);

    // node settings
    $ns = Configure::read('node-settings');

    // premissions
    $cp_create = __cp(['controller' => 'nodes', 'action' => 'create'], $auth);
    $cp_toggle = __cp(['controller' => 'nodes', 'action' => 'toggle'], $auth);
    $cp_period = __cp(['controller' => 'nodes', 'action' => 'period'], $auth);
    $cp_delete = __cp(['controller' => 'nodes', 'action' => 'delete'], $auth);
    $cp_settings = __cp(['controller' => 'nodes', 'action' => 'settings'], $auth) && is_array($ns) && count($ns) > 0 ? true : false;

?>
<div class="<?= strtolower($this->name); ?> tree">
    <table id="tree" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="nodes"><i class="fa fa-sitemap"></i> <?= $structure['title']; ?></th>
                <th class="actions actions-<?= count_true([$cp_toggle,$cp_period,$cp_toggle,$cp_toggle,$cp_toggle,$cp_toggle,true,true,$cp_delete]); ?>"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script>

    var tree;

    function sortnodes(){

        // init
        var json = {};
        var tree = $("#tree").fancytree("getTree");
        var structure = tree.toDict(true);

        // get order
        json = getpositiondata(json, structure['children'], false);

        // save order
        $.ajax({
            url: "<?= $this->Url->build(['controller' => 'nodes', 'action' => 'sort', $structure['id']]); ?>",
            method: 'POST',
            data: json,
            dataType: 'json',
            success: function(data, status, request){
                if(data.success === true){
                    // nothing
                }else{
                    alert(data.msg);
                }
            }
        });

    }

    function getpositiondata(data, nodes, parent){
        var position = 0;
        $.each(nodes, function(k,v){
            data[v['data']['info']['id']] = {
                'id': v['data']['info']['id'],
                'title': v['title'],
                'position': position,
                'parent': parent ? parent : '',
            };

            if(v['children'] !== undefined){
                getpositiondata(data, v['children'], v['data']['info']['id']);
            }
            position++;
        });
        return data;
    }

    function changeNode(id, field, value){
        <?php if($cp_toggle){ ?>
        $.ajax({
            url: "<?= $this->Url->build(['controller' => 'nodes', 'action' => 'toggle', $structure['id']]); ?>",
            method: 'POST',
            data: {
                'id': id,
                'field': field,
                'value': value
            },
            dataType: 'json',
            success: function(data, status, request){
                if(data.success === true){
                    $('#' + id + ' a.icon.' + field).data("value", data['value']);
                    if(data['value'] == 1){
                        $('#' + id + ' a.icon.' + field).removeClass('inactive');
                    }else{
                        $('#' + id + ' a.icon.' + field).addClass('inactive');
                    }
                    if(field == 'active'){
                        if(data['value'] == 1){
                            $('#' + id + ' a.icon.' + field + ' i.lock').removeClass('hidden');
                            $('#' + id + ' a.icon.' + field + ' i.unlock').addClass('hidden');
                        }else{
                            $('#' + id + ' a.icon.' + field + ' i.lock').addClass('hidden');
                            $('#' + id + ' a.icon.' + field + ' i.unlock').removeClass('hidden');
                        }
                    }
                }else{
                    alert(data.msg);
                }
            }
        });
        <?php } ?>
    }

    function deleteNode(){
        <?php if($cp_delete){ ?>
        if(confirm('<?= __d('be', 'Do you really want to delete this and all containing nodes?'); ?>')){
            var node = $("#tree").fancytree("getActiveNode");
            if(node){
                $.ajax({
                    url: "<?= $this->Url->build(['controller' => 'nodes', 'action' => 'delete', $structure['id']]); ?>",
                    method: 'POST',
                    data: {
                        'id': node['data']['info']['id'],
                    },
                    dataType: 'json',
                    success: function(data, status, request){
                        if(data.success === true){
                            node.remove();
                            sortnodes();
                        }else{
                            alert(data.msg);
                        }
                    }
                });
            }
        }
        <?php } ?>
    }

    function infoNode(){
        var node = $("#tree").fancytree("getActiveNode");
        if(node){
            $('<div class="' + __system['popup']['class'] + ' popup-wrapper inline info"><h2>' + node.title + '</h2><div class="name">Route:</div><div class="val">' + node['data']['info']['route'] + '</div><div class="clear"></div><div class="name">ID:</div><div class="val">' + node['data']['info']['id'] + '</div><div class="clear"></div><div class="name">Code:</div><div class="val">' + node['data']['element']['code'] + '</div><div class="clear"></div><div class="actions"><a href="javascript:void(0);" class="submit cancel"><?= __d('be', 'Close'); ?></a><div class="clear"></div></div></div>').bPopup({
                opacity: __system['popup']['opacity'],
                modalClose: __system['popup']['modalClose'],
                closeClass: 'actions .cancel',
                onClose: function(){
                    destroyPopup();
                }
            });
        }
    }

    function periodNode(id){
        <?php if($cp_period){ ?>
        var from_field = '<?= $this->Form->input('period-from', ['value' => '%s', 'type' => 'text', 'templates' => ['inputContainer' => '{{content}}'], 'label' => false, 'placeholder' => __d('be', 'Display from'), 'class' => 'first date date-range date-from', 'data-date-range' => 'node-period']) ?>'.replace("%s", $('#' + id + ' a.icon.display').data('from'));
        var to_field = '<?= $this->Form->input('period-to', ['value' => '%s', 'type' => 'text', 'templates' => ['inputContainer' => '{{content}}'], 'label' => false, 'placeholder' => __d('be', 'Display to'), 'class' => 'date date-range date-to', 'data-date-range' => 'node-period']) ?>'.replace("%s", $('#' + id + ' a.icon.display').data('to'));

        $('<div class="' + __system['popup']['class'] + ' popup-wrapper inline period"><h2><?= __d('be','Display period'); ?></h2>' + from_field + to_field + '<div class="actions"><a href="javascript:void(0);" class="submit cancel multi"><?= __d('be', 'Cancel'); ?></a><a href="javascript:saveDisplayPeriod(\'' + id + '\');" class="submit save"><?= __d('be', 'Save'); ?></a><div class="clear"></div></div></div>').bPopup({
            opacity: __system['popup']['opacity'],
            modalClose: __system['popup']['modalClose'],
            closeClass: 'actions .cancel',
            onClose: function(){
                destroyPopup();
            }
        }, function(){
            initdatepicker();
        });
        <?php } ?>
    }

    function saveDisplayPeriod(id){
        <?php if($cp_period){ ?>
        $.ajax({
            url: "<?= $this->Url->build(['controller' => 'nodes', 'action' => 'period', $structure['id']]); ?>",
            method: 'POST',
            data: {
                'id': id,
                'from': $('input[name="period-from"]').val(),
                'to': $('input[name="period-to"]').val(),
            },
            dataType: 'json',
            success: function(data, status, request){
                if(data.success === true){
                    $('#' + id + ' a.icon.display').data('from', data['from']);
                    $('#' + id + ' a.icon.display').data('to', data['to']);
                    if(data['from'].length > 0 || data['to'].length > 0){
                        $('#' + id + ' a.icon.display').removeClass('inactive');
                    }else{
                        $('#' + id + ' a.icon.display').addClass('inactive');
                    }
                    $('.' + __system['popup']['class'] + ' a.submit.cancel').trigger('click');
                }else{
                    alert(data.msg);
                }
            }
        });
        <?php } ?>
    }

    $(function(){

        tree = $("#tree").fancytree({
            debugLevel: 0,
            checkbox: false,
            titlesTabbable: true,     // Add all node titles to TAB chain
            source: {url: "<?= $this->Url->build(['controller' => 'nodes', 'action' => 'load', $structure['id']]); ?>"},
            extensions: ["glyph", "dnd", "table", "gridnav"],
            icon: false,
            glyph: {
                map: {
                    doc: "fa fa-file-text-o",
                    docOpen: "fa fa-file-text-o",
                    error: "fa fa-exclamation-triangle",
                    expanderClosed: "fa fa-caret-right",
                    expanderLazy: "fa fa-caret-right",
                    expanderOpen: "fa fa-caret-down",
                    folder: "fa fa-folder-o",
                    folderOpen: "fa fa-folder-open-o",
                    loading: "fa fa-cog fa-spin",
                    dragHelper: "",
                    dropMarker: "fa fa-long-arrow-right"
                }
            },
            dnd: {
                preventVoidMoves: true,
                preventRecursiveMoves: true,
                autoExpandMS: 400,
                dragStart: function(node, data) {
                    return true;
                },
                dragEnter: function(node, data) {
                    return true;
                },
                dragDrop: function(node, data) {
                    data.otherNode.moveTo(node, data.hitMode);

                    // sort nodes
                    sortnodes();
                }
            },
            table: {
                indentation: 20,
                nodeColumnIdx: 0,
                checkboxColumnIdx: false
            },
            gridnav: {
                autofocusInput: false,
                handleCursorKeys: true
            },
            renderColumns: function(event, data) {

                var node = data.node,
                $tdList = $(node.tr).find(">td");

                var icons = '';
                var linkable = node['data']['linkable'];

                icons += '<div id="' + node['data']['info']['id'] + '" class="actions">';

                <?php if($cp_toggle){ ?>
                // jump
                if(linkable){
                    var jump = data['node']['data']['info']['jump'] ? 1 : 0;
                    var jump_class = data['node']['data']['info']['jump'] ? '' : ' inactive';
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Jump'); ?>" data-value="' + jump + '" data-id="' + node['data']['info']['id'] + '" class="icon jump' + jump_class + '"><i class="fa fa-hashtag"></i></a>';
                }else{
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Jump'); ?>" class="icon jump ignore inactive"><i class="fa fa-hashtag"></i></a>';
                }
                <?php } ?>

                <?php if($cp_period){ ?>
                // display period
                if(linkable){
                    var period_class = ' inactive';
                    if(data['node']['data']['info']['from'] || data['node']['data']['info']['to']){
                        period_class = '';
                    }
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Display period'); ?>" data-from="' + data['node']['data']['info']['from'] + '" data-to="' + data['node']['data']['info']['to'] + '" data-id="' + node['data']['info']['id'] + '" class="icon display' + period_class + '"><i class="fa fa-calendar"></i></a>';
                }else{
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Display period'); ?>" class="icon display ignore inactive"><i class="fa fa-calendar"></i></a>';
                }
                <?php } ?>

                <?php if($cp_toggle){ ?>
                // dislplay
                var display = data['node']['data']['info']['display'] ? 1 : 0;
                var display_class = data['node']['data']['info']['display'] ? '' : ' inactive';
                icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Show in menu'); ?>" data-value="' + display + '" data-id="' + node['data']['info']['id'] + '" class="icon show' + display_class + '"><i class="fa fa-eye"></i></i></a>';

                // active
                var active = data['node']['data']['info']['active'] ? 1 : 0;
                var active_class = data['node']['data']['info']['active'] ? '' : ' inactive';
                var active_class_1 = data['node']['data']['info']['active'] ? ' hidden' : '';
                var active_class_2 = data['node']['data']['info']['active'] ? '' : ' hidden';
                icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Unlock/Lock'); ?>" data-value="' + active + '" data-id="' + node['data']['info']['id'] + '" class="icon active' + active_class + '"><i class="unlock fa fa-lock' + active_class_1 + '"></i><i class="lock fa fa-unlock-alt ' + active_class_2 + '"></i></a>';

                // index
                if(linkable){
                    var index = data['node']['data']['info']['index'] ? 1 : 0;
                    var index_class = data['node']['data']['info']['index'] ? '' : ' inactive';
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Robots index'); ?>" data-value="' + index + '" data-id="' + node['data']['info']['id'] + '" class="icon index' + index_class + '"><i class="fa fa-tag"></i></a>';
                }else{
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Robots index'); ?>" class="icon index inactive ignore"><i class="fa fa-tag"></i></a>';
                }

                // follow
                if(linkable){
                    var follow = data['node']['data']['info']['follow'] ? 1 : 0;
                    var follow_class = data['node']['data']['info']['follow'] ? '' : ' inactive';
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Robots follow'); ?>" data-value="' + follow + '" data-id="' + node['data']['info']['id'] + '" class="icon follow' + follow_class + '"><i class="fa fa-forward"></i></a>';
                }else{
                    icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Robots follow'); ?>" class="icon follow inactive ignore"><i class="fa fa-forward"></i></a>';
                }
                <?php } ?>

                // settings
                <?php if($cp_settings){ ?>
                icons += '<a href="<?= $this->Url->build(['controller' => 'nodes', 'action' => 'settings', $structure['id']]) . DS; ?>' + node['data']['info']['id'] + '" title="<?= __d('be', 'Settings'); ?>" class="icon settings"><i class="fa fa-cogs"></i></a>';
                <?php } ?>

                icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Information'); ?>" data-route="' + node['data']['info']['route'] + '" data-id="' + node['data']['info']['id'] + '" class="icon info"><i class="fa fa-info"></i></a>';
                if(linkable){
                    icons += '<a target="_blank" href="/redirect/' + __system['translation']['short'] + '/' + node['data']['info']['route'] + '" title="<?= __d('be', 'Show page'); ?>" class="icon"><i class="fa fa-external-link"></i></a>';
                }else{
                    icons += '<a target="javascript:void(0);" title="<?= __d('be', 'Show page'); ?>" class="icon inactive ignore"><i class="fa fa-external-link"></i></a>';
                }
                <?php if($cp_delete){ ?>
                icons += '<a href="javascript:void(0);" title="<?= __d('be', 'Remove'); ?>" data-id="' + node['data']['info']['id'] + '" class="icon delete"><i class="fa fa-times"></i></a>';
                <?php } ?>
                icons += '<div class="clear"></div></div>';

                // actions
                $tdList.eq(1).html(icons);

                // type
                $(node.tr).find('.fancytree-title').append(' <span>(' + node['data']['element']['type'] + ')</span>');

            },
            createNode: function(event, data){
                if(data['node']['data']['type'] == 'create'){
                    sortnodes();
                }
            }
        });

        $("a.expand").click(function(event){
            event.preventDefault();
            $("#tree").fancytree("getRootNode").visit(function(node){
                node.setExpanded(true);
            });
        });

        $("a.collapse").click(function(event){
            event.preventDefault();
            $("#tree").fancytree("getRootNode").visit(function(node){
                node.setExpanded(false);
            });
        });

        <?php if($cp_create){ ?>
        $("a.button.node").click(function(event){
            event.preventDefault();
            $('<div class="' + __system['popup']['class'] + '">').bPopup({
                opacity: __system['popup']['opacity'],
                modalClose: __system['popup']['modalClose'],
                content: 'ajax',
                closeClass: 'actions .cancel',
                loadUrl: $(this).attr('href'),
                onClose: function(){
                    destroyPopup();
                }
            });
        });
        <?php } ?>

        $(document).on("click", "div.actions a.icon", function(event) {
            if($(this).hasClass('ignore') === false){
                if($(this).hasClass('active')){
                    changeNode($(this).data('id'), 'active', $(this).data('value'));
                }else if($(this).hasClass('jump')){
                    changeNode($(this).data('id'), 'jump', $(this).data('value'));
                }else if($(this).hasClass('index')){
                    changeNode($(this).data('id'), 'index', $(this).data('value'));
                }else if($(this).hasClass('follow')){
                    changeNode($(this).data('id'), 'follow', $(this).data('value'));
                }else if($(this).hasClass('display')){
                    periodNode($(this).data('id'));
                }else if($(this).hasClass('show')){
                    changeNode($(this).data('id'), 'show', $(this).data('value'));
                }else if($(this).hasClass('info')){
                    infoNode();
                }else if($(this).hasClass('delete')){
                    deleteNode();
                }
            }
        });

    });

    $(document).ready(function(){

        // categories
        $('select#structures-dropdown').change(function(){
            window.location.href = '<?= $this->Url->build(['action' => 'tree']) . DS; ?>' + $(this).val();
        });

    });

</script>
