<?php 
    use Cake\Core\Configure;

    // premissions
    $cp_translate = __cp(['controller' => 'images', 'action' => 'translate'], $auth);
    $cp_exchange = __cp(['controller' => 'images', 'action' => 'exchange'], $auth);
    $cp_delete = __cp(['controller' => 'images', 'action' => 'delete'], $auth);
    $cp_crop = __cp(['controller' => 'images', 'action' => 'crop'], $auth);
    $cp_revolve = __cp(['controller' => 'images', 'action' => 'revolve'], $auth);
    $cp_auto = __cp(['controller' => 'images', 'action' => 'auto'], $auth);
    $cp_override = __cp(['controller' => 'images', 'action' => 'override'], $auth);
    if($this->request->params['action'] == 'search'){
        $cp_group = false;
    }else{
        $cp_group = $menu['right'][0]['show'] || $menu['right'][1]['show'] || $menu['right'][2]['show'] ? true : false;
    }
    
?>
<?php $this->Html->script('Backend.dropzone', ['block' => 'script']); ?>
<div class="progress exchange">
    <div class="info"><div id="filename"></div><div id="percentage"><span>0</span>%</div><div class="clear"></div></div>
    <div class="bar"></div>
</div>
<div class="progress upload">
    <div class="info"><div id="filename"></div><div id="percentage"><span>0</span>%</div><div class="clear"></div></div>
    <div class="bar"></div>
</div>
<div class="progress override">
    <div class="info"><div id="filename"></div><div id="percentage"><span>0</span>%</div><div class="clear"></div></div>
    <div class="bar"></div>
</div>
<form style="display:none;" id="htmlexchangezone" action="<?php echo $this->Url->build(['action' => 'exchange', $category['id']]); ?>" class="dropzone">
    <input id="exchange_image_id" type="hidden" name="image_id" value="" />
    <div class="fallback">
        <input name="Filedata" type="file" />
    </div>
</form>
<div class="<?= strtolower($this->name); ?> list">
    <?php if($imagick_check === false){ ?>
    <div class="message info"><?= __d('be', 'Missing PHP extension "Imagick"!') ?></div>
    <?php } ?>
    <?php if(count($images) < 1){ ?>
    <div class="message info"><?= __d('be', 'No images available') ?></div>
    <?php } ?>
    <?php if(!isset($search) || $search !== true){ ?>
        <?php if(!empty($category['parent_id'])){ ?>
            <a class="item folder" href="<?= $this->Url->build([$category['parent_id']]); ?>">
                <i class="fa fa-level-up fa-flip-horizontal"></i>
                <span><?= __d('be', 'Up'); ?></span>
            </a>
        <?php } ?>
        <?php foreach($folders as $folder){ ?>
            <a class="item folder" href="<?= $this->Url->build([$folder['id']]); ?>">
                <i class="fa fa-folder-open-o"></i>
                <span><?= $folder['internal']; ?></span>
            </a>
        <?php } ?>
    <?php } ?>
    <?php foreach($images as $image){ ?>
        <?= $this->element('Backend.image', ['info' => $info, 'image' => $image, 'translations' => Configure::read('translations'), 'purposes' => Configure::read('images.sizes.purposes'), 'premissions' => ['translate' => $cp_translate, 'exchange' => $cp_exchange, 'delete' => $cp_delete, 'crop' => $cp_crop, 'revolve' => $cp_revolve, 'auto' => $cp_auto, 'group' => $cp_group]]); ?>
    <?php } ?>
    <div class="clear"></div>
</div>
<script>
    
    var waiting = 0;
    var selected = 0;
    
    function auto(url, group){
        
        // wait for it ...
        waiting++;
        
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(data, status, request){
                
                waiting--;
                
                if(data.success === true){
                    
                    $('div#' + data.id).removeClass('used');
                    $('div#' + data.id).removeClass('recrop');
                    if(data.info.used){
                        $('div#' + data.id).addClass('used');
                    }
                    if(data.info.recrop){
                        $('div#' + data.id).addClass('recrop');
                    }
                    $('div#' + data.id + ' div.information ul li').each(function(k,v){
                        var cls = '';
                        var recrop = false;
                        var cropped = false;
                        $(v).attr('class', '');
                        if(data['info']['blanks'][$(v).attr('rel')] != undefined){
                            cropped = data['info']['blanks'][$(v).attr('rel')]['modified'];
                            cls = 'cropped';
                            if(data['info']['blanks'][$(v).attr('rel')]['recrop']){
                                recrop = true;
                                cls = 'recrop';
                            }
                        }
                        $(v).attr('class', cls);
                        if(recrop){
                            $(v).find('i').attr('class', 'fa fa-exclamation-triangle');
                        }else if(cropped){
                            $(v).find('i').attr('class', 'fa fa-check-circle');
                        }else{
                            $(v).find('i').attr('class', 'fa fa-times-circle');
                        }
                        if(cropped){
                            $(v).find('span.time').remove();
                            $(v).find('span.purpose').after('<span class="time">' + cropped + '</span>');
                        }
                    });
                }
                if(waiting == 0){
                    alert(data.msg);
                }
            }
        });
    }
    
    $(document).ready(function(){

        // rotate
        $('.item.image .actions a.rotate-icon').click(function(event){
            event.preventDefault();
            $(this).parents('.item.image').find('.rotate').addClass('show');
        });
        
        $('.item.image .rotate a.cancel').click(function(event){
            event.preventDefault();
            $(this).parents('.item.image').find('.rotate').removeClass('show');
        });
        
        // translations
        $('.item.image .languages a').click(function(event){
            event.preventDefault();
            $(this).parents('.item.image').find('.translations').addClass('show');
        });
        
        $('.item.image .translations a.save').click(function(event){
            event.preventDefault();
            
            // check
            var check = false;
            try{
                if($(this).parent('form').find('input#translation-' + __system['translation']['short']).val() != ''){
                    check = true;
                }
            }catch(e){ }
            
            // save
            if(check){
                
                $(this).parents('.item.image').find('.translations').removeClass('show');
                
                $.ajax({
                    url: $(this).parent('form').attr('action'),
                    method: 'POST',
                    data: $(this).parent('form').serialize(),
                    dataType: 'json',
                    success: function(data, status, request){
                        if(data.success === true){
                            
                            // set default values
                            $('div#' + data.id + ' form input').each(function(k,v){
                                $(this).data('value', $(this).val());
                            });
                            
                            // flags
                            $('div#' + data.id + ' .languages a.flag').addClass('inactive');
                            $.each(data['result']['_translations'], function(k,v){
                                $('div#' + data.id + ' .languages a.flag.' + k).removeClass('inactive');
                                
                                // set title
                                if(k == __system['translation']['short']){
                                    $('div#' + data.id + ' label').text(v['title']);
                                }
                            });
                            
                        }else{
                            
                            // reset form
                            $('div#' + data.id + ' form input').each(function(k,v){
                                $(this).val($(this).data('value'));
                            });
                            
                        }
                        alert(data.msg);
                    }
                });
            }else{
                alert('<?= __d('be','There translation for "%s" is required!'); ?>'.replace('%s', __system['translation']['title']));
            }

        });
        
        $('.item.image .translations a.cancel').click(function(event){
            event.preventDefault();
            $(this).parents('.item.image').find('.translations').removeClass('show');
            $(this).parents('form').find('input').each(function(k,v){
                $(this).val($(this).data('value'));
            });
        });
        
        // exchange
        $(document).on("click", ".list.images .actions .exchange", function(event) {
            event.preventDefault();
            $('#exchange_image_id').val($(this).parents('.item.image').attr('id'));
            $('#htmlexchangezone').trigger('click');
        });
        
        // search
        $('.button.search i').click(function(event){
            event.preventDefault();
            $(this).parents('.button.search').addClass('show');
        });
        
        $('.button.search a.cancel').click(function(event){
            event.preventDefault();
            $(this).parents('.button.search').removeClass('show');
        });
        
        $('.button.search a.send').click(function(event){
            event.preventDefault();
            document.location.href = $(this).parents('.button.search').data('url') + '?term=' + encodeURIComponent($('#search-term').val());
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
                alert('<?= __d('be', 'The images are already in this category!'); ?>');
            }else if(confirm('<?= __d('be', 'Do you realy want to move the selected images to "%s"?'); ?>'.replace('%s', $("#move-category option:selected").text()))){
                images = [];
                $.each($('input:checked[name="image[]"]'), function(k,v){
                    images[k] = $(v).val();
                });
                $.ajax({
                    url: $(this).parents('.button.group.move').data('url'),
                    method: 'POST',
                    data: {
                        'category': $("#move-category option:selected").val(),
                        'images': images
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
            if(confirm('<?= __d('be', 'Do you realy want to delete the selected images?'); ?>')){
                images = [];
                $.each($('input:checked[name="image[]"]'), function(k,v){
                    images[k] = $(v).val();
                });
                $.ajax({
                    url: $(this).attr('href'),
                    method: 'POST',
                    data: {
                        'images': images
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
        
        $('.icon.group.auto').click(function(event){
            event.preventDefault();
            var __url = $(this).attr('href');
            $.each($('input:checked[name="image[]"]'), function(k,v){
                auto(__url + '/' + $(v).val(), true);
            });
        }); 


        $('.icon.reverse-selection').click(function(event){
            event.preventDefault();
            $('.item.image input[type="checkbox"]').each(function(k,v){
                $(this).trigger('click');
            });
        });
        
        $('.item.image input[type="checkbox"]').click(function(){
            if($(this).is(':checked')){
                selected++;
                $(this).parents('.item.image').addClass('selected');
            }else{
                selected--;
                $(this).parents('.item.image').removeClass('selected');
            }
            
            if(selected > 0){
                $('nav .icon.group, nav .button.group').removeClass('hidden');
            }else{
                $('nav .icon.group, nav .button.group').addClass('hidden');
            }
            
        });
        
        $('.item.image .actions .auto').click(function(event){
            event.preventDefault();
            auto($(this).attr('href'), false);
        });
        
        // information
        $('.item.image .actions .info').hover(
            function() {
                $(this).parents('.item.image').find('.information').addClass('show');
            }, function() {
                $(this).parents('.item.image').find('.information').removeClass('show');
            }
        );
        
        // categories
        $('select#image-category').change(function(){
            window.location.href = '<?= $this->Url->build(['action' => 'index']) . DS; ?>' + $(this).val();
        });
        
        // dropzone
        var dz_upload_errors = [];
        var dz_exchange_errors = [];
        var dz_override_errors = [];
        
        Dropzone.options.htmluploadzone = {
            paramName: "Filedata",
            parallelUploads: 1,
            uploadMultiple: true,
            createImageThumbnails: false,
            acceptedFiles: '<?php echo join(",",Configure::read('upload.images.mime')); ?>',
            uploadprogress: function(file, progress){
                $('#htmluploadzone').css('display', 'none');
                $('.progress.upload').css('display', 'block');
                $('.progress.upload .bar').css('width', Math.ceil(progress) + '%');
                $('.progress.upload #filename').text(file.name);
                $('.progress.upload #percentage > span').text(Math.ceil(progress));
            },
            successmultiple: function(file, response){
                var response = jQuery.parseJSON(response);
                if(response.success == false){
                    dz_upload_errors[dz_upload_errors.length] = response.file.name;
                }
            },
            queuecomplete: function(){
                $('#htmluploadzone').css('display', 'block');
                $('.progress.upload').css('display', 'none');
                if(dz_upload_errors.length > 0){
                    alert("<?= __d('be', 'Some images could not be uploaded:'); ?>\n" + dz_upload_errors.join(", "));
                }
                location.reload();
            }
        };
        
        Dropzone.options.htmlexchangezone = {
            paramName: "Filedata",
            parallelUploads: 1,
            uploadMultiple: false,
            createImageThumbnails: false,
            acceptedFiles: '<?php echo join(",",Configure::read('upload.images.mime')); ?>',
            uploadprogress: function(file, progress){
                $('#htmluploadzone').css('display', 'none');
                $('.progress.exchange').css('display', 'block');
                $('.progress.exchange .bar').css('width', Math.ceil(progress) + '%');
                $('.progress.exchange #filename').text(file.name);
                $('.progress.exchange #percentage > span').text(Math.ceil(progress));
            },
            success: function(file, response){
                var response = jQuery.parseJSON(response);
                if(response.success == true){
                    var bg = $('div#' + response.id + ' .preview').css('backgroundImage');
                    $('div#' + response.id + ' .preview').css('backgroundImage', bg.replace('")','?' + Math.floor(Date.now() / 1000) + '")'));
                    $('div#' + response.id + ' span.original').text(response['result']['original']);
                }else{
                    dz_exchange_errors[dz_exchange_errors.length] = response.file.name;
                }
            },
            queuecomplete: function(){
                $('#exchange_image_id').val('');
                $('#htmluploadzone').css('display', 'block');
                $('.progress.exchange').css('display', 'none');
                if(dz_exchange_errors.length > 0){
                    alert("<?= __d('be', 'The image could not be replaced!'); ?>");
                }
            }
        };
        
        <?php if($cp_override){ ?>
        Dropzone.options.htmloverridezone = {
            paramName: "Filedata",
            parallelUploads: 1,
            uploadMultiple: true,
            createImageThumbnails: false,
            acceptedFiles: '<?php echo join(",",Configure::read('upload.images.mime')); ?>',
            uploadprogress: function(file, progress){
                $('#htmloverridezone').css('display', 'none');
                $('.progress.override').css('display', 'block');
                $('.progress.override .bar').css('width', Math.ceil(progress) + '%');
                $('.progress.override #filename').text(file.name);
                $('.progress.override #percentage > span').text(Math.ceil(progress));
            },
            successmultiple: function(file, response){
                var response = jQuery.parseJSON(response);
                if(response.success == false){
                    dz_override_errors[dz_override_errors.length] = response.file.name;
                }
            },
            queuecomplete: function(){
                $('#htmloverridezone').css('display', 'block');
                $('.progress.override').css('display', 'none');
                if(dz_override_errors.length > 0){
                    alert("<?= __d('be', 'Some images could not be uploaded:'); ?>\n" + dz_override_errors.join(", "));
                }
                
                document.location.href = '<?php echo $this->Url->build(['action' => 'override', 'process']); ?>';

            }
        };
        <?php } ?>
        
    });
    
</script>