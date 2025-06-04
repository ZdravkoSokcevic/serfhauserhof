<div class="list translations">
    <?php if(count($translations) > 0){ ?>
    <?php
        foreach($translations as $translation){
            $cls = $translation->translated ? '' : ' missing';
            echo '<div class="row' . $cls . '" data-locale="' . $translation->locale . '" data-domain="' . $translation->domain . '" data-id="' . $translation->id . '">';
                echo '<div class="col fallback">' . htmlspecialchars($translation->fallback) . '</div>';
                echo '<div class="col translation">' . htmlspecialchars($translation->translation) . '&nbsp;</div>';
                echo '<div class="clear"></div>';
            echo '</div>';
        }
    ?>
    <?php }else{ ?>
        <div class="message info"><?= __d('be', 'No translations available!'); ?></div>
    <?php } ?>
</div>
<?php if(count($translations) > 0){ ?>
<div class="form translations">
    <div>
        <form class="translation" name="translation">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="locale" id="locale" value="" />
            <input type="hidden" name="domain" id="domain" value="" />
            <label for="fallback"><?= __d('be', 'Source') ?></label>
            <textarea id="fallback" readonly="readonly" name="fallback" class="fallback"></textarea>
            <label for="translation"><?= __d('be', 'Translation') ?></label>
            <textarea id="translation" name="translation" class="translation"></textarea>
            <?= $this->Form->button(__d('be', 'Save'), ['type' => 'button', 'class' => 'submit']); ?>
            <div class="clear"></div>
        </form>
    </div>
</div>
<?php } ?>
<script>
    
    $(window).ready(function(){
        
        $('.form button').click(function(){

            $.ajax({
                url: "<?= $this->Url->build(['action' => 'update']) ?>",
                method: 'POST',
                data: {
                    locale: $('.form.translations #locale').val(),
                    domain: $('.form.translations #domain').val(),
                    id: $('.form.translations #id').val(),
                    fallback: $('.form.translations #fallback').val(),
                    translation: $('.form.translations #translation').val(),
                    translated: 1
                },
                dataType: 'json',
                success: function(data, status, request){
                    if(data.success === true){
                        var row = $('.list .row[data-id=' + data['data']['id'] + '][data-locale=' + data['data']['locale'] + '][data-domain=' + data['data']['domain'] + ']');
                        $(row).removeClass('missing');
                        $(row).addClass('translated');
                        $(row).find('div.translation').html(data['translation']);
                    }else{
                        alert(data.msg);
                    }
                }
            });

        });
        
        $('.list .row').click(function(){
            
            var row = $('.list .row[data-id=' + $(this).data('id') + '][data-locale=' + $(this).data('locale') + '][data-domain=' + $(this).data('domain') + ']');
            $('.list .row').removeClass('selected');
            $(row).addClass('selected');
            
            $.ajax({
                url: "<?= $this->Url->build(['action' => 'load']) ?>",
                method: 'POST',
                data: {
                    locale: $(this).data('locale'),
                    domain: $(this).data('domain'),
                    id: $(this).data('id')
                },
                dataType: 'json',
                success: function(data, status, request){
                    if(data.success === true){
                        $('.form.translations #id').val(data['data']['id']);
                        $('.form.translations #locale').val(data['data']['locale']);
                        $('.form.translations #domain').val(data['data']['domain']);
                        $('.form.translations #fallback').val(data['data']['fallback']);
                        $('.form.translations #translation').val(data['data']['translation']);
                    }else{
                        alert(data.msg);
                    }
                }
            });
        });
        
        $('.list .row:first').trigger('click');
        
        $('nav .left select').change(function(){
            var val = $(this).val();
            val = val.split(':');
            window.location.href = '<?php echo $this->Url->build(['action' => 'index']) . DS; ?>' + val[0] + '<?php echo DS; ?>' + val[1] + '<?php echo DS; ?>';
        });
        
        $('nav .right select.init').change(function(){
            var val = $(this).val();
            var href = '<?= $this->Url->build(['action' => 'init']) . DS; ?>';
            if(val){
                href +=  val + '<?= DS; ?>';
            }
            $('nav .right a.init').attr('href', href);
        });
        
        // import
        $('.button.import i').click(function(event){
            event.preventDefault();
            $(this).parents('.button.import').addClass('show');
        });
        
        $('.button.import a.cancel').click(function(event){
            event.preventDefault();
            $(this).parents('.button.import').removeClass('show');
        });
        
        $('.button.import a.send').click(function(event){
            event.preventDefault();
            var fd = new FormData();
            var files = $('#import-file')[0].files; 
            if(files.length == 1){
                fd.append('import', files[0]);
                
                fd.append('domain', $("#import-domain option:selected").val());

                $.ajax({
                    url: $(this).parents('.button.import').data('url'),
                    method: 'POST',
                    data: fd,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data, status, request){
                        if(data.success === true){
                            if(Object.keys(data.failed).length > 0){
                                var failed = '';
                                $(data.failed).each(function(k,v){
                                    failed += '\n' + v.f + ': ' + v.t;
                                });
                                alert(__translations.import.failed + ':\n' + failed);
                            }else{
                                alert(__translations.import.success);
                            }
                            location.reload(true);
                        }else{
                            alert(data.msg);
                        }
                    }
                });
                $(this).parents('.button.import').removeClass('show');

            }else{
                alert(__translations.upload.select);
            }
        });
        
        // export
        $('.button.export i').click(function(event){
            event.preventDefault();
            $(this).parents('.button.export').addClass('show');
        });
        
        $('.button.export a.cancel').click(function(event){
            event.preventDefault();
            $(this).parents('.button.export').removeClass('show');
        });
        
        $('.button.export a.send').click(function(event){
            event.preventDefault();
            $.ajax({
                url: $(this).parents('.button.export').data('url'),
                method: 'POST',
                data: {
                    'domain': $("#export-domain option:selected").val(),
                },
                dataType: 'json',
                success: function(data, status, request){
                    if(data.success === true){
                        window.location = data.url;
                    }else{
                        alert(data.msg);
                    }
                }
            });
            $(this).parents('.button.export').removeClass('show');
        });
        
    });
    
</script>