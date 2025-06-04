<?php use Cake\Core\Configure; ?>
<?php if(isset($hint) && $hint === true){ ?>
<section id="cookie-hint" class="cookie hidden-print">
    <div class="inner">
    	<span>
    		<?php echo sprintf(__d('fe','This website uses Cookies. <br />Click <a href="%s">here</a> for more information.'), $this->Url->build(['node' => Configure::read('config.default.cookie.0.org'), 'language' => $this->request->params['language']])); ?>
		</span>
		<a class="button cookie-hint-button" href="#">
			<i class="fa fa-times-circle" aria-hidden="true"></i>
			<span><?= __d('fe','Roger that') . '!' ?></span>
		</a>
    </div>
</section>
<?php } ?>