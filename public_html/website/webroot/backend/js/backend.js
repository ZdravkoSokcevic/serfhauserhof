// cms js functions
$.ajaxSetup({ cache: false });

function destroyPopup(){
    $('body > .popup-container').remove();
}

function selectoropen(rel, images, elements, nodes, categories, max, editor){
    
    var act = false;
    if(editor == false && max !== false){
        act = 0;
        var __act = $('.' + rel + ' input').val();
        $.each(__act.split(";"), function(k,v){
            act++;
        });
    }
    
    $('<div class="' + __system['popup']['class'] + '">').bPopup({
        opacity: __system['popup']['opacity'],
        modalClose: __system['popup']['modalClose'],
        content: 'ajax',
        closeClass: 'actions .cancel',
        loadUrl: '/admin/' + __system['locale'] + '/selector/button/' + rel + '/' + images + '/' + elements + '/' + nodes + '/' + categories + '/' + max + '/' + act + '/' + editor + '/?locale=' + __system['locale'],
        onClose: function(){
            destroyPopup();
        }
    });
}

function selectorshow(rel){
    if($('.' + rel).hasClass('wait')){
        alert(__translations['selector']['wait']);
    }else if($('.' + rel + ' input').val() != ''){
        $('.' + rel).toggleClass('preview');
    }
}

function selectorremove(rel, id){
    if(confirm(__translations['selector']['confirm'])){
        var item = $('.' + rel + ' > div.preview div.item[data-id="' + id + '"]');
        var val = '';
        var glue = '';
        var act = $('.' + rel + ' input').val();
        var images = 0;
        var elements = 0;
        
        $.each(act.split(";"), function(k,v){
            if($(item).data('type') + ':' + id != v){
                val += glue + v;
                glue = ';';
                if($(item).data('type') == 'image'){
                    images++;
                }else{
                    elements++;
                }
            }
        });

        $('.' + rel + ' input').val(val);
        if(images > 0){
            $('.' + rel + ' a.button.image > span').text(' (' + images + ')');
        }else{
            $('.' + rel + ' a.button.image > span').text('');
        }
        if(elements > 0){
            $('.' + rel + ' a.button.element > span').text(' (' + elements + ')');
        }else{
            $('.' + rel + ' a.button.element > span').text('');
        }
        
        if(elements < 1 && images < 1){
            $('.' + rel).removeClass('preview');
            $('.' + rel + ' a.preview.button').addClass('inactive');
        }
        
        $(item).remove();
    }
}

function selectorpreview(rel){

    $('.' + rel).addClass('wait');
    $('.' + rel).removeClass('preview');
    $('.' + rel + ' > div.preview').text('');
    $('.' + rel + ' a.preview.button').addClass('inactive');
    $('.' + rel + ' a.button > span').text('');

    if($('.' + rel + ' input').val() != ''){

        $.ajax({
            url: '/admin/' + __system['locale'] + '/selector/preview',
            method: 'POST',
            data: {
                'rel': rel,
                'elements': $('.' + rel + ' input').val()
            },
            dataType: 'json',
            success: function(data, status, request){
                if(data.success === true){
                    var val = '';
                    var glue = '';
                    var images = 0;
                    var elements = 0;
                    $.each(data.elements, function(k,v){
                        val += glue + v.type + ':' + v.id;
                        glue = ';';
                        if(v.type == 'image'){
                            images++;
                            $('.' + rel + ' > div.preview').append('<div class="item ' + v.type + '" data-id="' + v.id + '" data-type="' + v.type + '"><div class="img" style="background-image: url(\'/img/thumbs/' + v.info['filename'] + '\')"></div><div class="info"><span class="title">' + v.info['title'] + '<a class="fa fa-trash" href="javascript:selectorremove(\'' + rel + '\', \'' + v.id + '\')" title="' + __translations['selector']['remove'] + '"></a><div class="clear"></div></span>' + v.info['original'] + '</div><div class="clear"></div></div>');
                        }else{
                            elements++;
                            $('.' + rel + ' > div.preview').append('<div class="item ' + v.type + '" data-id="' + v.id + '" data-type="' + v.type + '"><i class="fa fa-' + v.settings['icon'] + '"></i><div class="info"><span class="title">' + v.info['internal'] + '<a class="fa fa-trash" href="javascript:selectorremove(\'' + rel + '\', \'' + v.id + '\')" title="' + __translations['selector']['remove'] + '"></a><div class="clear"></div></span>' + v.settings['name'] + '</div><div class="clear"></div></div>');
                        }
                    });
                    $('.' + rel + ' input').val(val);
                    if(val.length > 0){
                        var count = images + elements;
                        if(count > 0){
                            $('.' + rel + ' a.button > span').text(' (' + count + ')');
                        }
                        $('.' + rel + ' a.preview.button').removeClass('inactive');
                    }
                }else{
                    alert(data.msg);
                }
                $('.' + rel).removeClass('wait');
            }
        });
        
    }else{
        $('.' + rel).removeClass('wait');
    }

}

function timesopen(rel){
    var from_field = '<input type="text" value="" id="period-from" data-date-range="season-period" class="first date date-range date-from" placeholder="' + __translations['times']['placeholder']['from'] + '" name="period-from">';
    var to_field = '<input type="text" value="" id="period-to" data-date-range="season-period" class="date date-range date-to" placeholder="' + __translations['times']['placeholder']['to'] + '" name="period-to">';
    
    $('<div class="' + __system['popup']['class'] + ' popup-wrapper inline period"><h2>' + __translations['times']['headline'] + '</h2>' + from_field + to_field + '<div class="actions"><a href="javascript:void(0);" class="submit cancel multi">' + __translations['times']['buttons']['cancel'] + '</a><a href="javascript:timesadd(\'' + rel + '\');" class="submit save">' + __translations['times']['buttons']['add'] + '</a><div class="clear"></div></div></div>').bPopup({
        opacity: __system['popup']['opacity'],
        modalClose: __system['popup']['modalClose'],
        closeClass: 'actions .cancel',
        onClose: function(){
            destroyPopup();
        }
    }, function(){
        initdatepicker();
    });
}

function timespreview(rel){
    
    var __act = $('.' + rel + ' input').val();
    var __new = [];
    var __pos = 0;
    
    // cleanup
    $('.' + rel + ' div.preview').text('');
    
    // create preview
    if(__act){
        var __parts = __act.split("|");
        $(__parts).each(function(k,v){
            if(v.length == 21 && v.indexOf(":") == 10){
                var __dates = v.split(":");
                if(__dates.length == 2){
                    $('.' + rel + ' div.preview').append('<div class="time" data-time-from="' + __dates[0] + '" data-time-to="' + __dates[1] + '"><div class="from">' + formateDate(__dates[0]) + '</div><div class="to">' + formateDate(__dates[1]) + '</div><a class="icon" href="javascript:timesremove(\'' + rel + '\', ' + __pos + ');" title="' + __translations['times']['remove'] + '"><i class="fa fa-trash"></i></a><div class="clear"></div></div>');
                    __new[__new.length] = v;
                    __pos++;
                }
            }
        });
        $('.' + rel + ' input').val(__new.join("|"));
    }
    
    // sort
    $('.' + rel + ' .preview').find('.time').sort(function(a, b) {
        var __date_a = new Date($(a).data('time-from'));
        var __date_b = new Date($(b).data('time-from'));
        return __date_a.getTime() - __date_b.getTime();
    })
    .appendTo($('.' + rel + ' .preview'));
    
}

function timesadd(rel){
    var __from = $('input[name="period-from"]').val();
    var __to = $('input[name="period-to"]').val();
    var __act = $('.' + rel + ' input').val();
    if(__from && __to){
        
        // add times
        if(__act){
            var __parts = __act.split("|");
        }else{
            var __parts = [];
        }
        __parts[__parts.length] = __from + ':' + __to;
        var __new = __parts.join("|");
        $('.' + rel + ' input').val(__new);
        
        // preview
        timespreview(rel);
        
        // close popup
        $('.' + __system['popup']['class'] + ' a.submit.cancel').trigger('click');
    }else{
        alert(__translations['times']['empty']);
    }
}

function timesremove(rel, pos){
    if(confirm(__translations['times']['confirm'])){
        var __act = $('.' + rel + ' input').val();
        var __new = [];
        var __pos = 0;
        if(__act){
            var __parts = __act.split("|");
            $(__parts).each(function(k,v){
                if(v.length == 21 && v.indexOf(":") == 10){
                    var __dates = v.split(":");
                    if(__dates.length == 2){
                        if(pos != __pos){
                            __new[__new.length] = v;
                        }
                        __pos++;
                    }
                }
            });
            $('.' + rel + ' input').val(__new.join("|"));
        }
        timespreview(rel);
    }
}

function initdatepicker(){
    $('input.date').each(function(k,v){
        if($(this).hasClass('picker__input') === false){
        
            // init
            var __min = $(this).data('date-min') ? new Date($(this).data('date-min')) : undefined;
            var __max = $(this).data('date-max') ? new Date($(this).data('date-max')) : undefined;
            var __range = $(this).data('date-range');
            var __years = $(this).data('date-years') == false ? false : true;
            var __months = $(this).data('date-months') == false ? false : true;
            var __format = $(this).data('date-format') != undefined ? $(this).data('date-format') : 'dd.mm.yyyy';
            var __hidden = $(this).data('date-hidden') != undefined ? $(this).data('date-hidden') : 'yyyy-mm-dd';
            
            // classes
            var __class_year = 'picker__year';
            var __class_today = 'picker__button--today';
            
            if($(this).data('date-daypicker') != undefined && $(this).data('date-daypicker') == true){
                __format = 'dd.mm.';
                __hidden = 'yyyy-mm-dd';
                __years = false;
                __months = true;
                __min = new Date('2000-01-01');
                __max = new Date('2000-12-31');
                __class_year += ' hidden';
                __class_today += ' hidden';
            }
            
            $(this).data('value', $(this).val());
            var $input = $(this).pickadate({
                selectYears: __years,
                selectMonths: __months,
                container: '#datepicker-container',
                format: __format,
                formatSubmit: __hidden,
                hiddenName: true,
                closeOnSelect: true,
                closeOnClear: false,
                max: __max,
                min: __min,
                klass: {
                    buttonToday: __class_today,
                    year: __class_year
                }
            });
            
            if(__range){
                var __clicked = $(this).hasClass('date-from') ? 'from' : 'to';
                var __opposite = __clicked == 'from' ? 'to' : 'from';
                $input.pickadate('picker').on('set', function(event) {
                    if ( event.select ) {
                        var __select = $('input.date.date-' + __clicked + '[data-date-range="' + __range + '"]').pickadate('picker').get('select');
                        var __related = $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').get('select');
                        if(__opposite == 'to'){
                            __select.obj.setDate(__select.obj.getDate() + 1);
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('min', __select);
                            if(__related && __related.pick <= __select.pick){
                                $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('select', __select);
                            }
                        }else{
                            __select.obj.setDate(__select.obj.getDate() - 1);
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('max', __select);
                            if(__related && __related.pick >= __select.pick){
                                $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('select', __select);
                            }
                        }
                    }else if('clear' in event){
                        if(__opposite == 'from'){
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('max', false);
                        }else{
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('min', false);
                        }
                    }
                });
            }
            
        }
    });
}

function setcounter(e, mode){

    // init
    var __val = $(e).val();
    var __min = $(e).data('counter-min') ? parseInt($(e).data('counter-min')) : false;
    var __max = $(e).data('counter-max') ? parseInt($(e).data('counter-max')) : false;

    if(mode == 'init'){

        var __markup = false;
        
        if(__min && __min > 0 && __max && __max > 0){
            __markup = '<span class="act">' + __val.length + '</span>/<span class="max">' + __max + '</span> (min. <span class="min">' + __min + '</span>)'; // X/Y (min Z)
        }else if(__min && __min > 0){
            __markup = '<span class="act">' + __val.length + '</span> (min. <span class="min">' + __min + '</span>)'; // X (min Y)
        }else if(__max && __max > 0){
            __markup = '<span class="act">' + __val.length + '</span>/<span class="max">' + __max + '</span>'; // X/Y
        }
        
        if(__markup !== false){
            $(e).after('<div class="counter">' + __markup + '</div><div class="clear"></div>');
            
            $(e).on("change", function(){
                setcounter(this, 'update');
            }).keyup(function(){
                setcounter(this, 'update');
            });
        }
    }else{
        
        $(e).next('div.counter').find('span.act').text(__val.length);
        $(e).next('div.counter').find('span.min').text(__min);
        $(e).next('div.counter').find('span.max').text(__max);

    }
}

$(document).ready(function(){
    
    $(document).ajaxStart(function(){
        $('.loading:first').fadeIn(300);
    });
    
    $(document).ajaxComplete(function(){
        $('.loading:first').fadeOut(300);
    });
    
    $(document).ajaxError(function(e, r){
        if(r['readyState'] == 4){
            alert(__translations['ajax-error']);
        }
    });
    
    // messages
    $('div.message a.close').click(function(k,v){
        $(this).parent('div.message').addClass('hidden');
    });
    
    // counter
    $('input.counter').each(function(k,v){
        setcounter(v, 'init');
    });
    
    // times
    $('input.times').each(function(k,v){

        // set
        $(this).parent('div.input').removeClass('text');
        $(this).parent('div.input').addClass('times times-' + k + ' wait ');
        $(this).attr('type', 'hidden');
        
        // change dom
        $(this).after('<div class="clear"></div><div class="preview"></div><div class="clear"></div>');
        $(this).after('<a class="image button" href="javascript:timesopen(\'times-' + k + '\');">' + __translations['times']['button'] + '<span></span></a>');
        
        // preview
        timespreview('times-' + k);

    });
    
    // selectors
    $('input.selector').each(function(k,v){
        
        // init
        var __check = false;
        var __empty = $(this).val() == '' ? true : false;
        var __max = $(this).data('selector-max') ? $(this).data('selector-max') : false;
        var __image = $(this).data('selector-image') ? $(this).data('selector-image') : false;
        var __element = $(this).data('selector-element') ? $(this).data('selector-element') : false;
        var __node = $(this).data('selector-node') ? $(this).data('selector-node') : false;
        var __category = $(this).data('selector-category') ? $(this).data('selector-category') : false;
        var __text = $(this).data('selector-text') ? $(this).data('selector-text') : false;
        if(!__text){
            __text = __max != false ? __translations['selector']['single'] : __translations['selector']['multiple'];
        }
        
        // check
        if(__image || __element || __node || __category){
            __check = true;
        }
        
        if(__check){
        
            // set
            $(this).parent('div.input').removeClass('text');
            $(this).parent('div.input').addClass('selector selector-' + k + ' wait ');
            $(this).attr('type', 'hidden');
            
            // change dom
            $(this).after('<a href="javascript:selectorshow(\'selector-' + k + '\')" class="preview button" title="' + __translations['selector']['preview'] + '"><i class="fa fa-cog fa-spin loading"></i><i class="fa fa-eye show"></i></a><div class="clear"></div><div class="preview"></div><div class="clear"></div>');
            $(this).after('<a class="element button" href="javascript:selectoropen(\'selector-' + k + '\', \'' + __image + '\', \'' + __element + '\', \'' + __node + '\', \'' + __category + '\', ' + __max + ', false);">' + __text + '<span></span></a>');
            
            // preview
            selectorpreview('selector-' + k);
            
        }else{
            alert(__translations['selector']['error']);
        }
        
    });
        
    $('div.input.selector > div.preview').sortable({
        axis: "y",
        opacity: 0.8,
        update: function( event, ui ) {
            var val = '';
            var glue = '';
            $(this).find('div.item').each(function(k,v){
                val += glue + $(v).data('type') + ':' + $(v).data('id');
                glue = ';';
            });
            $(this).prevAll("input.selector").val(val);
        }
    }).disableSelection();

    // sortable tables
    if(__system['sortable']){
        $("table.list").rowSorter({
            handler: "span.sort i",
            onDrop: function(tbody, row, index, oldIndex) {
                var __nr = 0;
                var __order = {};
                $(tbody).find('tr.element').each(function(k,v){

                    // store order
                    __order[__nr] = $(this).data('id');
                    
                    // alternate row colors
                    if(__nr%2){
                        $(this).addClass('alternate');
                    }else{
                        $(this).removeClass('alternate');
                    }
                    
                    __nr++;
                });
                
                // apply
                if(__nr > 1){
                    var code = typeof(__system['request']['pass'][0]) !== undefined ? __system['request']['pass'][0] : '';
                    $.ajax({
                        url: '/admin/' + __system['locale'] + '/elements/order/' + code,
                        method: 'POST',
                        data: {
                            'order': __order,
                        },
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
                
            }
        });
    }

    // datepicker
    initdatepicker();
    
    // editor
    var editor_config = {
        'default': {
            'plugins': "paste, code, link, table, visualchars, fullscreen",
            'toolbar': [
                'undo redo | cut copy pastetext | styleselect | removeformat | bullist numlist | outdent indent | visualchars | code',
                'bold italic | subscript superscript | alignleft aligncenter alignright alignjustify | mjlink unlink | mjimage | table | fullscreen | mjauto'
            ],
            'height': 300,
            'formats': __system['editor']['styles']
        },
        'small': {
            'plugins': "paste, code, fullscreen",
            'toolbar': [
                'undo redo | cut copy pastetext | styleselect | removeformat | bold italic | subscript superscript | alignleft aligncenter alignright alignjustify | code'
            ],
            'height': 200,
            'formats': __system['editor']['styles']
        },
        'teaser': {
            'plugins': "paste, code, fullscreen",
            'toolbar': [
                'undo redo | cut copy pastetext | removeformat | bullist numlist | bold italic | subscript superscript | alignleft aligncenter alignright alignjustify | code'
            ],
            'height': 200,
            'formats': __system['editor']['styles']
        },
        'headline': {
            'plugins': "paste, code, fullscreen",
            'toolbar': [
                'undo redo | cut copy pastetext | removeformat | bold italic | code'
            ],
            'height': 200,
            'formats': __system['editor']['styles']
        },
        'email': {
            'plugins': "paste, link, code, fullscreen",
            'toolbar': [
                'undo redo | cut copy pastetext | link | styleselect | removeformat | bold italic | subscript superscript | alignleft aligncenter alignright alignjustify | code'
            ],
            'height': 200,
            'formats': [{"title":"Farbe","inline":"span","classes":"color"}]
        }
    };
    
    $('textarea.wysiwyg').each(function(k,v){
        
        // config
        var _config;
        if($(this).data('config') && editor_config[$(this).data('config')]){
            _config = editor_config[$(this).data('config')];
        }else{
            _config = editor_config['default'];
        }
        
        $(this).tinymce({
            theme: 'modern',
            height: _config['height'],
            language: __system['locale'],
            inline: false,
            resize: false,
            statusbar: false,
            menubar: false,
            plugins: _config['plugins'],
            paste_as_text: true,
            toolbar: _config['toolbar'],
            style_formats: _config['formats'],
            content_css : '/frontend/css/reset.css,/frontend/css/styles.css',
            setup: function (editor) {
                
                // images
                editor.addButton('mjimage', {
                    icon: 'image',
                    tooltip: __translations['editor']['image'],
                    onclick: function () {
                        mjimage(editor);
                    }
                });
                
                // links
                editor.addButton('mjlink', {
                    icon: 'link',
                    tooltip: __translations['editor']['link'],
                    stateSelector : "a[href]",
                    onclick: function () {
                        mjlink(editor);
                    }
                });
                
                // autolinks
                var __values = [];
                $.each(__system['infos']['structures'], function(k,v){
                    __values[__values.length] = {
                        text: v,
                        onclick: function() {
                            mjauto(editor, k);
                        }
                    };
                });
                if(__values.length > 1){
                    editor.addButton('mjauto', {
                        type: 'splitbutton',
                        icon: 'magic',
                        tooltip: __translations['editor']['auto'],
                        onclick: function(){
                            this.showMenu();
                        },
                        menu: __values
                    });
                }else if(__values.length == 1){
                    editor.addButton('mjauto', {
                        icon: 'magic',
                        tooltip: __translations['editor']['auto'],
                        onclick: function () {
                            $.each(__system['infos']['structures'], function(k,v){
                                mjauto(editor, k);
                            });
                        }
                    });
                }
            },
            object_resizing: false,
            relative_urls : false,
            forced_root_block: false
        });
    });

});

// tinymce

function mjlink(editor){
    selectoropen(false, false, 'link|download', true, false, 1, 'links');
}

function mjimage(editor){
    selectoropen(false, true, false, false, false, 1, 'images');
}

function mjauto(editor, structure){
    
    $.ajax({
        url: '/admin/' + __system['locale'] + '/nodes/autolink/' + structure + '/?locale=' + __system['locale'],
        method: 'POST',
        data: {
            'content': editor.getContent()
        },
        dataType: 'json',
        success: function(data, status, request){
            if(data['success'] === true){
                if(data['replace'] === true){
                    editor.setContent(data['content']);
                }
            }else{
                alert(data.msg);
            }
        }
    });
    
}

// global functions

function openInHiddenIFrame(url) {
    var frame = $('#hif');
    if($(frame).size() > 0){
        $(frame).attr("src", url);
    }else{
        $("<iframe>").attr('id', 'hif').hide().attr("src", url).appendTo("body");
    }
}

function in_array(needle, haystack, argStrict) {
    var key = '',
        strict = !! argStrict;

    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }
    return false;
}

function formateDate(d){
    var __date = new Date(d);
    var __day = __date.getDate();
    if(__day < 10){
        __day = '0' + __day;
    }
    var __month = __date.getMonth() + 1;
    if(__month < 10){
        __month = '0' + __month;
    }
    var __year = __date.getFullYear();
    return __day + '.' + __month + '.' + __year;
}