<?php
    echo $this->Html->script('Backend.jquery.jcrop.min.js', ['block' => true]);
    echo $this->Html->css('Backend.jquery.jcrop.min.css', ['block' => true]);
?>
<div class="<?= strtolower($this->name); ?> crop">
<?= $this->Form->create() ?>
    <?= $this->Form->button(__d('be', 'Save'), ['class' => 'wait space-bottom hidden submit']); ?>
    <div class="clear"></div>
    <fieldset class="wait infos hidden">
        <legend><?= __d('be', 'Information'); ?></legend>
        <div><span><?= __d('be', 'Title'); ?>:</span> <?= $image['title']; ?></div>
        <div><span><?= __d('be', 'Original title'); ?>:</span> <?= $image['original']; ?></div>
        <div><span><?= __d('be', 'Mime type'); ?>:</span> <?= $image['mime']; ?></div>
    </fieldset>
    <fieldset class="wait purposes hidden">
        <legend><?= __d('be', 'Purposes') ?></legend>
        <?= $this->Form->input('image', array('id' => 'image', 'name' => 'image', 'type'=>'hidden', 'value' => $image['id'], 'autocomplete' => 'off')); ?>
        <?php foreach($purposes as $idx => $_purpose){ ?>
        <?php
            
            // init
            if(is_array($_purpose['crop'])){
                $x = array_key_exists('x',$_purpose['crop']) ? $_purpose['crop']['x'] : '';
                $y = array_key_exists('y',$_purpose['crop']) ? $_purpose['crop']['y'] : '';
                $sw = array_key_exists('sw',$_purpose['crop']) ? $_purpose['crop']['sw'] : '';
                $sh = array_key_exists('sh',$_purpose['crop']) ? $_purpose['crop']['sh'] : '';
                $deg = array_key_exists('deg',$_purpose['crop']) ? $_purpose['crop']['deg'] : 0;
                $focus = array_key_exists('focus',$_purpose['crop']) ? $_purpose['crop']['focus'] : 0;
                $fx = array_key_exists('fx',$_purpose['crop']) ? $_purpose['crop']['fx'] : '';
                $fy = array_key_exists('fy',$_purpose['crop']) ? $_purpose['crop']['fy'] : '';
            }else{
                $x = $y = $sw = $sh = '';
                $deg = 0;
                $focus = 0;
                $fx = '';
                $fy = '';
            }
            
            $exact = $_purpose['exact'] ? 1 : 0;
            $class = $_purpose['cropped'] ? 'cropped' : '';
            $disabled = $_purpose['cropped'] ? 'data-dummy' : 'disabled';
            
            echo $this->Form->input(
                'crop',
                array(
                    'templates' => ['inputContainer' => '<div class="checkbox ' . $disabled . '">{{content}}' . '<a href="javascript:changePurpose(' . $idx . ')" rel="crop-link-' . $idx . '"><i class="fa fa-crop"></i></a><label for="crop' . $idx . '" class="' . $class . '">' . $_purpose['name'] . '</label><div class="clear"></div></div>'],
                    'label' => false,
                    $disabled => $disabled,
                    'id' => 'crop'.$idx,
                    'name' => 'crop['.$idx.'][crop]',
                    'type' => 'checkbox',
                    'value' => 1,
                    'checked' => false,
                    'autocomplete' => 'off'
                    )
                );
            echo $this->Form->input('purpose', array('id' => 'purpose'.$idx, 'name' => 'crop['.$idx.'][purpose]', 'type'=>'hidden', 'value' => $idx, 'autocomplete' => 'off'));
            echo $this->Form->input('x', array('id' => 'x'.$idx, 'name' => 'crop['.$idx.'][x]', 'type'=>'hidden', 'value' => $x, 'autocomplete' => 'off'));
            echo $this->Form->input('y', array('id' => 'y'.$idx, 'name' => 'crop['.$idx.'][y]', 'type'=>'hidden', 'value' => $y, 'autocomplete' => 'off'));
            echo $this->Form->input('sw', array('id' => 'sw'.$idx, 'name' => 'crop['.$idx.'][sw]', 'type'=>'hidden', 'value' => $sw, 'autocomplete' => 'off'));
            echo $this->Form->input('sh', array('id' => 'sh'.$idx, 'name' => 'crop['.$idx.'][sh]', 'type'=>'hidden', 'value' => $sh, 'autocomplete' => 'off'));
            echo $this->Form->input('ratio', array('id' => 'ratio'.$idx, 'name' => 'crop['.$idx.'][ratio]', 'type'=>'hidden', 'value' => $_purpose['ratio'], 'autocomplete' => 'off'));
            echo $this->Form->input('deg', array('id' => 'deg'.$idx, 'name' => 'crop['.$idx.'][deg]', 'type'=>'hidden', 'value' => $deg, 'autocomplete' => 'off'));
            echo $this->Form->input('focus', array('id' => 'focus'.$idx, 'name' => 'crop['.$idx.'][focus]', 'type'=>'hidden', 'value' => $focus, 'autocomplete' => 'off'));
            echo $this->Form->input('fx', array('id' => 'fx'.$idx, 'name' => 'crop['.$idx.'][fx]', 'type'=>'hidden', 'value' => $fx, 'autocomplete' => 'off'));
            echo $this->Form->input('fy', array('id' => 'fy'.$idx, 'name' => 'crop['.$idx.'][fy]', 'type'=>'hidden', 'value' => $fy, 'autocomplete' => 'off'));
            echo $this->Form->input('nw', array('id' => 'nw'.$idx, 'name' => 'crop['.$idx.'][nw]', 'type'=>'hidden', 'value' => $_purpose['width'], 'autocomplete' => 'off'));
            echo $this->Form->input('nh', array('id' => 'nh'.$idx, 'name' => 'crop['.$idx.'][nh]', 'type'=>'hidden', 'value' => $_purpose['height'], 'autocomplete' => 'off'));
            echo $this->Form->input('exact', array('id' => 'exact'.$idx, 'name' => 'crop['.$idx.'][exact]', 'type'=>'hidden', 'value' => $exact, 'autocomplete' => 'off'));
                    
        ?>
        <?php } ?>
   </fieldset>
   
    <fieldset class="wait crop hidden">
        <legend><?= __d('be', 'Crop') ?></legend>
        <div class="image2crop" style="width:<?php echo $fit[0]; ?>px; height:<?php echo $fit[1]; ?>px;">
            <div class="rotate" style="width:<?php echo $fit[0]; ?>px; height:<?php echo $fit[1]; ?>px;"><?php echo $this->Html->image($original['url'].'?'.time(), array('id'=>'crop-rotate-'.$image['id'], 'width' => $fit[0], 'height' => $fit[1])) ?></div>
            <div class="dummy" style="width:<?php echo $fit[0]; ?>px; height:<?php echo $fit[1]; ?>px;"><?php echo $this->Html->image('/backend/img/blank.gif', array('id'=>'crop-dummy-'.$image['id'], 'width' => $fit[0], 'height' => $fit[1])) ?></div>
            <div class="modal mt"></div>
            <div class="modal ml"></div>
            <div class="modal mb"></div>
            <div class="modal mr"></div>
        </div>
   </fieldset>
   
    <fieldset id="crop-preview-wrapper" class="preview hidden">
        <legend><?= __d('be', 'Preview') ?></legend>
        <div id="crop-preview-viewport" style="position:relative; margin: 0 auto; overflow:hidden;">
            <?php echo $this->Html->image($original['url'].'?'.time(), array('id'=>'crop-preview')) ?>
            <div id="focus" class="<?php echo $focusPoint ? 'enabled' : 'disabled'; ?>"></div>
        </div> 
   </fieldset>
   
    <fieldset class="loading">
        <legend><?= __d('be', 'Please wait') ?></legend>
        <i class="fa fa-cog fa-spin"></i>
   </fieldset>
    <?= $this->Form->button(__d('be', 'Save'), ['class' => 'wait hidden submit']); ?>
<?= $this->Form->end() ?>
</div>
<script>
    
    var purpose = false;
    var purposes = <?php echo json_encode($purposes); ?>;

    function updateCropReport(coords){
        
        fakeModal(coords);
        
        var image = $('#crop-dummy-<?php echo $image['id'] ?>');
        var form = image.siblings('form');
        
        var nx = Math.round(coords.x/(p*100)*100);
        var ny = Math.round(coords.y/(p*100)*100);
        var nw = Math.round(coords.w/(p*100)*100);
        var nh = $('#ImageRatio').val() ? Math.round(nw/parseFloat($('#ImageRatio').val())) : Math.round(coords.h/(p*100)*100);
        
        $('#x' + purpose).val(nx);
        $('#y' + purpose).val(ny);
        $('#sw' + purpose).val(nw);
        $('#sh' + purpose).val(nh);

        if(purposes[purpose]['height'] === false || purposes[purpose]['width'] === false){
            if(purposes[purpose]['height'] === false){
                var rx = ry = ow/nw;
                var ch = rx * nh;
                $("#crop-preview-viewport").css({
                    height: ch + "px"
                });
            }else if(purposes[purpose]['width'] === false){
                var rx = ry = oh/nh;
                var cw = rx * nw;
                $("#crop-preview-viewport").css({
                    width: cw + "px"
                });
            }else{
                var rx = ow/nw; // fallback!
            }
            
            var pw = (rx * purposes[purpose]['original'][0]);
            var ph = (ry * purposes[purpose]['original'][1]);
            var px = ((rx * nx)) * -1;
            var py = ((ry * ny)) * -1;
            
            $("#crop-preview").css({
                width: pw + "px",
                height: ph + "px",
                marginLeft: px + "px",
                marginTop: py + "px"
            });
            
        }else{
            
            var rx = ow/nw;
            var ry = oh/nh;
            var pw = (rx * purposes[purpose]['original'][0]);
            var ph = (ry * purposes[purpose]['original'][1]);
            var px = ((rx * nx)) * -1;
            var py = ((ry * ny)) * -1;
            
            $("#crop-preview").css({
                width: pw + "px",
                height: ph + "px",
                marginLeft: px + "px",
                marginTop: py + "px"
            });
            
        }
        
        // update fh/fw
        if(purposes[purpose]['height'] === false || purposes[purpose]['width'] === false){
            purposes[purpose]['crop']['fw'] = $('#crop-preview-viewport').width();
            purposes[purpose]['crop']['fh'] = $('#crop-preview-viewport').height();
            initFocus(true);
        }
        
        // show preview and crop button
        $('#crop-preview-wrapper').removeClass('hidden');
    }
    
    function initCropReport(api){
        if(purposes[purpose]['crop'] !== false){
            var x1 = purposes[purpose]['crop']['x']*p;
            var y1 = purposes[purpose]['crop']['y']*p;
            var x2 = x1 + purposes[purpose]['crop']['sw']*p;
            var y2 = y1 + purposes[purpose]['crop']['sh']*p;
            api.setSelect([x1,y1,x2,y2]);
            if(purposes[purpose]['crop']['deg'] > 0 && purposes[purpose]['crop']['deg'] < 360){
                processRotation(purposes[purpose]['crop']['deg']);
            }else{
                processRotation(360);
            }
        }else{
            processRotation(360);
        }
    }
    
    function clearCropReport(){
        
        fakeModal(false);
        
        $('#x' + purpose).val('');
        $('#y' + purpose).val('');
        $('#sw' + purpose).val('');
        $('#sh' + purpose).val('');
        $('#crop-preview-wrapper').addClass('hidden');
    }
    
    function processRotation(deg){
        $('#deg' + purpose).val(deg);
        rotation = "rotate(" + deg + "deg)";
        $('#crop-rotate-<?php echo $image['id'] ?>, #crop-preview-viewport img').css({
            'transform': rotation,
            '-webkit-transform': rotation,
            '-ms-transform': rotation,
            'msTransform': rotation,
            'top': 0,
            'left': 0
        });
        
        if(deg >= 0 && deg <= 360){
            $('#rotationSlider').slider('value',deg);
        }
        
        $('#rotationAct').val(mapDeg(deg));
    }
    
    function typedDegrees(){
        var org = $('#rotationAct').val();
        var deg = parseInt(org);
        var regex = /^[0-9\b]+$/;
        if(!regex.test(org) || isNaN(deg) || deg < 0 || deg > 360){
            processRotation(360);
        }else{
            deg = mapDeg(parseInt(deg));
            processRotation(deg);
        }
    }
    
    function typedDegreesFocus(){
        $('#rotationAct').select();
    }
    
    function mapDeg(deg){
        var dummy = 360 - deg;
        return dummy;
    }
    
    function createRotationSlider() {

        var rotationContainerSlider = $("<div />").attr('id',
                'rotationContainer').mouseover(function () {
                $(this).css('opacity', 1);
            }).mouseout(function () {
                $(this).css('opacity', 0.6);
            });

        var rotMin = $('<div />').attr('id', 'rotationMin')
            .html("0");
        var rotMax = $('<div />').attr('id', 'rotationMax')
            .html("360");
        var actRot = $('<div />').attr('class', 'rotationAct')
            .html('<input type="text" id="rotationAct" onclick="typedDegreesFocus()" onkeyup="typedDegrees()" onchange="typedDegrees()" value="' + purposes[purpose]['crop']['deg'] + '" />');

        var $slider = $("<div />").attr('id', 'rotationSlider');

        // Apply slider
        var orientation = 'horizontal';

        $slider.slider({
            orientation: orientation,
            value: 360,
            range: "max",
            min: 0,
            max: 360,
            step: 1,
            slide: function (event, ui) {
                processRotation(Math.abs(ui.value));
            }
        });

        rotationContainerSlider.append(rotMin);
        rotationContainerSlider.append($slider);
        rotationContainerSlider.append(rotMax);
        rotationContainerSlider.append(actRot);

        $slider.addClass('vertical');
        rotationContainerSlider.addClass('vertical');
        rotMin.addClass('vertical');
        rotMax.addClass('vertical');
        rotationContainerSlider.css({
            'position': 'absolute',
            'top': 5,
            'left': 5,
            'opacity': 0.6
        });
        
        $('.jcrop-holder').append(rotationContainerSlider);
    }
    
    function fakeModal(coords){
        
        var w = <?php echo $fit[0]; ?>;
        var h = <?php echo $fit[1]; ?>;
        
        if(coords){
            $('.image2crop .modal.mt').css({
               top: 0,
               left: 0,
               width: w,
               height: coords.y 
            });
            $('.image2crop .modal.ml').css({
               top: coords.y,
               left: coords.x + coords.w,
               width: w - coords.x - coords.w,
               height: coords.y2 - coords.y
            });
            $('.image2crop .modal.mb').css({
               top: coords.y + coords.h,
               left: 0,
               width: w,
               height: h + coords.y - coords.h
            });
            $('.image2crop .modal.mr').css({
               top: coords.y,
               left: 0,
               width: coords.x,
               height: coords.y2 - coords.y
            });
            $('.image2crop .modal').show();
        }else{
            $('.image2crop .modal').hide();
        }
        
    }
    
    function changePurpose(u){
        purpose = u;
        $('.main nav h1 span.purpose').text(purposes[purpose]['name'] + ':');
        if(purposes[purpose]['width'] && purposes[purpose]['height']){
            $('.main nav h1 span.size').text(purposes[purpose]['width'] + 'x' + purposes[purpose]['height']);
        }else if(purposes[purpose]['width']){
            $('.main nav h1 span.size').text(purposes[purpose]['width'] + 'x');
        }else if(purposes[purpose]['height']){
            $('.main nav h1 span.size').text('x' + purposes[purpose]['height']);
        }else{
            $('.main nav h1 span.size').text('');
        }
        initCrop();
    }
    
    function selectPurpose(){
        $('#crop' + purpose).removeAttr('disabled');
        $('#crop' + purpose).attr('checked','checked');
    }
    
    function calcFocusPos(x,y,offset){
        
        // init
        var pw = $('#crop-preview-viewport').width();
        if(purposes[purpose]['crop'] !== false){
            var m = purposes[purpose]['crop']['fw']/pw;
            var w = purposes[purpose]['crop']['fw'];
            var h = purposes[purpose]['crop']['fh'];
        }else{
            var m = $('#crop-preview-viewport').width()/pw;
            var w = $('#crop-preview-viewport').width();
            var h = $('#crop-preview-viewport').height();            
        }
        var wc = Math.round(w/2);
        var hc = Math.round(h/2);
        
        x = (x*m) + offset;
        y = (y*m) + offset;
        
        if(x < wc){
            x = (wc - x) * -1;
        }else{
            x = x - wc;
        }
        
        // calc y
        if(y < hc){
            y = hc - y;
        }else{
            y = (y - hc) * -1;
        }
        
        // round
        x = Math.round(x);
        y = Math.round(y);
        
        // form
        $('#focus' + purpose).val(1);
        $('#fx' + purpose).val(x);
        $('#fy' + purpose).val(y);
        
        // output
        $("#focusX").text(x);
        $("#focusY").text(y);
    }
    
    function reverseFocusPos(pos,section,offset,type){
        pos = parseInt(pos);
        if(type == 'y'){
            pos = pos*-1;
        }
        pos = pos - offset;
        var c = type == 'x' ? Math.round(purposes[purpose]['crop']['fw']/2) : Math.round(purposes[purpose]['crop']['fh']/2);
        var m = type == 'x' ? section/purposes[purpose]['crop']['fw'] : section/purposes[purpose]['crop']['fh'];
        pos = Math.round(((c + pos)*m));
        return pos;
    }
    
    function initFocus(center){
        
        var offset = 39;
        
        if(center === false && purposes[purpose]['crop'] !== false && purposes[purpose]['crop']['focus'] == '1'){
            var _fx = reverseFocusPos(purposes[purpose]['crop']['fx'],$('#crop-preview-viewport').width(),offset, 'x');
            var _fy = reverseFocusPos(purposes[purpose]['crop']['fy'],$('#crop-preview-viewport').height(),offset, 'y');
        }else{
            var _fx = Math.round(($('#crop-preview-viewport').width()/2) - offset);
            var _fy = Math.round(($('#crop-preview-viewport').height()/2) - offset);
        }
        
        $("#focus").css({
           left: _fx,
           top: _fy 
        });
        
        calcFocusPos(_fx,_fy,offset);
        
        $("#focus").draggable({
            containment: "#crop-preview-viewport",
            scroll: false,
            stop: function(event, ui) {
                var pos = $(this).position();
                var _fx = pos.left;
                var _fy = pos.top;
                calcFocusPos(_fx,_fy,offset);
            }
             
        });
        
    }
    
    function initCrop(){
        
        // show
        $('#crop-preview-wrapper').removeClass('hidden');
        
        // init
        var image = $('#crop-dummy-<?php echo $image['id'] ?>');
        var ratio = purposes[purpose]['js']['ratio'];
        var mw = $('#crop-preview-wrapper').width();
        
        ow = purposes[purpose]['js']['width'];
        oh = purposes[purpose]['js']['height'];
        
        aw = $(image).width();
        ah = $(image).height();
        p = (aw/purposes[purpose]['original'][0]);
        var sw = ow*p;
        var sh = oh*p;

        if(ow > mw){
            var mr = mw/ow;
            ow = mw;
            oh = oh*mr;
        }
        
        $('#crop-preview-viewport, #crop-preview-viewport img').css({
            width: ow + "px",
            height: oh + "px"
        });
        
        // destroy
        var jca = $('#crop-dummy-<?php echo $image['id'] ?>').data('Jcrop');
        if(typeof(jca) == 'object'){
            jca.destroy();
            $('.image2crop > div.modal').css({
               top: 0,
               left: 0,
               width: 0,
               heigth: 0 
            });
        }
        
        // check
        if(purpose && purposes[purpose]['valid'] === false){
            $('#crop-preview-wrapper').addClass('hidden');
            alert('<?php echo __d('be', 'The selected image is to small for this purpose!'); ?>');
            return false;
        }
        
        // select ;-)
        selectPurpose();
        
        // init
        if(purposes[purpose]['exact'] === false){
            $('#crop-dummy-<?php echo $image['id'] ?>').Jcrop({
                onSelect : updateCropReport,
                onChange : updateCropReport,
                onRelease : clearCropReport,
                allowResize: true,
                allowMove: true,
                setSelect: [ 0, 0, sw, sh],
                minSize: [sw, sh],
                aspectRatio: ratio,
                bgOpacity: .3
            },function(){
                createRotationSlider();
                initCropReport(this);
            });
        }

        // focus ;-)
        initFocus(false);

        if(purposes[purpose]['exact'] === true){
            alert('<?php echo __d('be', 'The selected image has exactly the correct dimensions and will be saved for this purpose!'); ?>');
        }

    }

    var aw;
    var ah; 
    var ow;
    var oh;
    var p;
    
    $('#crop-rotate-<?php echo $image['id'] ?>').load(function(){
        $('fieldset.loading').addClass('hidden');
        $('fieldset.wait, button.wait').removeClass('hidden');
        //initCrop();
    });

</script>