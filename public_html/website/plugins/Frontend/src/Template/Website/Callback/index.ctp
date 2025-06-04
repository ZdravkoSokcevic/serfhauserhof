<main class="page">
    <a class="anchor" name="content"></a>
    <h1><?= $content['headline']; ?></h1>
    <div class="content">
        <?= $content['content']; ?>
        <div class="clear"></div>
    </div>
</main>
<div class="form-wrapper">
	<div class="inner">
		<?php if($messages['show']){ ?>
	        <?= $this->element('Frontend.Website/form-message', ['messages' => $messages]) ?>
	    <?php }else{ ?>
	        <!--// Form Start //-->
	        <?= $this->CustomForm->create($form); ?>
			<div class="form-cols">
				<div class="col left">
					<?= $this->CustomForm->input('name', ['label' => __d('fe', 'Name')]); ?>
				</div>
				<div class="col right">
					<?= $this->CustomForm->input('phone', ['label' => __d('fe', 'Phone')]); ?>    
				</div>
	        </div>
			<div class="form-cols">
				<div class="col left">
					<?= $this->CustomForm->input('date', ['label' => __d('fe', 'Date'), 'class' => 'date']); ?>
				</div>
				<div class="col right">
					<?= $this->CustomForm->input('time', ['label' => __d('fe', 'Time'), 'empty' => __d('fe', '-- Please select --')]); ?>    
				</div>
	        </div>
	        <?= $this->CustomForm->input('message', ['label' => __d('fe', 'Message')]); ?>
            <?= $this->element('Frontend.Website/privacy') ?>
	        <?= $this->element('Frontend.Website/captcha') ?>
	        <?= $this->CustomForm->end(); ?>
	        <!--// Form End //-->
		<?php } ?>
	</div>
</div>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>