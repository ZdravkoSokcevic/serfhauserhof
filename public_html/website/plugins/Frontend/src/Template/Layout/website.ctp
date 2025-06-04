<?php use Cake\Core\Configure; ?>
<!doctype html>
<!--[if lt IE 9 ]><html version="HTML+RDFa 1.1" lang="<?php echo $this->request->params['language']; ?>" class="no-js ie"><![endif]-->
<!--[if IE 9 ]><html version="HTML+RDFa 1.1" lang="<?php echo $this->request->params['language']; ?>" class="no-js ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html version="HTML+RDFa 1.1" lang="<?php echo $this->request->params['language']; ?>" class="no-js"><!--<![endif]-->
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= isset($title) ? strip_tags($title) : $this->fetch('title') ?></title>
        <?= $this->element('Frontend.Website/meta', ['seo' => $seo, 'content' => $content]) ?>
        <?= $this->fetch('meta') ?>
        <?= $this->Html->css('Frontend.reset.css') ?>
        <?= $this->Html->css('Frontend.animate.css') ?>
        <?= $this->Html->css('Frontend.font-awesome.min.css') ?>
        <?= $this->Html->css('Frontend.video-js.css') ?>
        <?= $this->Html->css('Frontend.flag-icon.min.css') ?>
        <?= $this->Html->css('Frontend.styles.css') ?>
        <?= $this->fetch('css') ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script>var dataLayer = []</script>
    	<?= /* Tagmanager - head-code */ Configure::read('config.tracking.tagmanager-head'); ?>
    </head>
    <?php $body_class = isset($this->request->params['route']) && $this->request->params['route'] == Configure::read('config.default.home.0.details.node.route') ? 'home' : 'not-home'; ?>
    <body class="<?= $body_class . ' ' . $this->request->params['language']; ?><?php echo $dialog ? ' dialog-mode' : ''; ?>">
    	<?= /* Tagmanager - body-code */ Configure::read('config.tracking.tagmanager-body'); ?>
    	<div class="loading-overlay">
    		<div class="loading-overlay-inner">
    			<?php if(isset($this->request->params['route']) && $this->request->params['route'] == Configure::read('config.default.home.0.details.node.route')){ ?>
    				<div class="loading-logo"></div>
    			<?php } ?>
    			<div class="loading-spinner"></div>
    		</div>
    	</div>
        <div id="wrapper">
            <?= $this->element('Frontend.Website/navigation', ['menu' => $menu]) ?>
            <?= $this->element('Frontend.Website/header') ?>
            <?= $this->element('Frontend.Website/breadcrumbs', ['breadcrumbs' => $breadcrumbs]) ?>
            <?= $this->fetch('content') ?>
            <?= $this->element('Frontend.Website/quicklinks') ?>
            <?= $this->element('Frontend.Website/footer') ?>
        </div>
        <div id="datepicker-container"></div>
        <?= $this->Html->css('Frontend.pickadate/default.css') ?>
        <?= $this->Html->css('Frontend.pickadate/default.date.css') ?>
        <?= $this->element('Frontend.Website/js', ['tracking' => isset($tracking) ? $tracking : false]) ?>
        <?= $this->element('Frontend.slideshow') ?>

        <script src="https://frontend.casablanca.at/Scripts/headjs/head.js?Customer=a_6534_serfa" data-headjs-load="https://frontend.casablanca.at/widgets/main.js?Customer=a_6534_serfa"></script>

        <script>
        //scroll to content
        <?php if(isset($this->request->params['node']) && array_key_exists('jump', $this->request->params['node']) && $this->request->params['node']['jump'] == 1){ ?>
        	$(window).load(function(){
        		scrollToContent();
        	});
        <?php } ?>

        //Browser warning
		var $buoop = {vs:{i:13,f:-4,o:-4,s:8,c:-4},api:4};
		function $buo_f(){
		 var e = document.createElement("script");
		 e.src = "//browser-update.org/update.min.js";
		 document.body.appendChild(e);
		};
		try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
		catch(e){window.attachEvent("onload", $buo_f)}
		</script>

    </body>
</html>
