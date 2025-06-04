<?php
    use Cake\Core\Configure;

    // init
    $dp =  !empty(Configure::read('config.default.default-panorama')) ? ',file:serfauserhof_' . Configure::read('config.default.default-panorama') : '';

    echo $this->Html->script('../tour/_/js/vendor/switch/switch_integrated.js');
    $file = array_key_exists('id', $_GET) ? ',file:serfauserhof_' .  $_GET['id'] : $dp;
?>
<section class="main panorama">
	<div class="inner">
        <div id="tour" style="background-position: center center; background-size: cover; background-color: #FFFFFF;"></div>
    </div>
</section>
<script type="text/javascript">
    new Tour ('/tour/_/index.php?options=projectPath:../at_00091,language:<?= $this->request->params['language']; ?><?= $file; ?>,hideMenu:false,disableSound:true', 'tour', '/frontend/img/panorama.jpg');
</script>
