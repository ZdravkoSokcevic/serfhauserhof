<?php if(isset($slideshow) && is_array($slideshow) && count($slideshow) > 0){ ?>
<?php

    // prepare
    $purpose = 1;
    $first = false;
    $albums = [];
    foreach($slideshow as $album){

        // images        
        $images = [];
        foreach($album['images'] as $image){
            $images[$image['id']] = "background-position: " . $image['details']['focus'][$purpose]['css'] . "; background-image: url('" . $image['details']['seo'][$purpose] . "');";
        }
        
        // store
        $albums[$album['id']] = [
            'id' => $album['id'],
            'title' => $album['title'],
            'images' => $images
        ];
        
        // first album
        $first = $first === false ? $album['id'] : $first;
        
    }

?>
<script type="text/javascript">

    var __changes = 0;
    var __slideshow;
    var __albums = <?= json_encode($albums); ?>;

    function slideshow(album){
        
        // loading
        if(__changes > 0){
            $('section#slideshow div.loading').show();
        }
        //$('section#slideshow div.navigation').removeClass('open');
        
        // album
        album = album != false ? album : '<?= $first; ?>';
        
        // new images
        var slides = '';
        $.each(__albums[album]['images'], function(k,v){
            slides += '<li><div class="bxslide" style="' + v + '"></div></li>';
        });
        
        // destroy
        if(__slideshow){
            __slideshow.destroySlider();
        }
        
        // update
        $('section#slideshow h2').text(__albums[album]['title']);
        $('section#slideshow .bxslider').html(slides);
        
        // show
        $('body').addClass('slideshow');
        $('#slideshow').show();
        
        // init
        __slideshow = $('section#slideshow .bxslider').bxSlider({
            auto: true,
            pager: true,
            controls: true,
            speed: 800,
            pause: 8000,
            nextText: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
            prevText: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
            onSliderLoad: function(currentIndex){
                if(__changes > 0){
                    $('section#slideshow div.loading').fadeOut(500);
                }
                __changes++;
                
                $('section#slideshow .bx-controls .bx-controls-direction').appendTo($('section#slideshow .bx-controls .bx-pager'));
            }
        });
        
    }

    $(document).ready(function(){
        $('a.open-slideshow').click(function(event){
            event.preventDefault();
            slideshow(false);
        });
        
        $('section#slideshow div.navigation a.button.close').click(function(event){
            event.preventDefault();
            
            // hide
            $('body').removeClass('slideshow');
            $('#slideshow').hide();
            
            // destroy
            if(__slideshow){
                __slideshow.destroySlider();
            }
            
            // reload header
            if(__header.length > 0){
                __header.reloadSlider();
            }
            
        });
        
        $('section#slideshow div.navigation a.button.menu').click(function(event){
            event.preventDefault();
            $('section#slideshow div.navigation').toggleClass('open');
        });
        
    });
    
</script>
<section id="slideshow" style="display:none;">
    <ul class="viewport bxslider"></ul>
    <h2></h2>
    <div class="navigation open">
        <a href="#" class="button close"><i class="fa fa-times" aria-hidden="true"></i></a>
        <?php if(count($albums) > 1){ ?>
            <a href="#" class="button menu"><i class="fa fa-bars" aria-hidden="true"></i></a>
        <?php } ?>
        <div class="clear"></div>
        <?php if(count($albums) > 1){ ?>
            <ul class="albums">
            <?php foreach($albums as $album){ ?>
                <li><a href="javascript:slideshow('<?= $album['id']; ?>');"><?= $album['title']; ?></a></li>
            <?php } ?>
            </ul>
        <?php } ?>
    </div>
    <div class="loading"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div>
</section>
<?php } ?>