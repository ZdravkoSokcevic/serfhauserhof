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
					<?= $this->CustomForm->input('salutation', ['label' => __d('fe', 'Salutation'), 'empty' => __d('fe', '-- Please select --')]); ?>
					<?= $this->CustomForm->input('title', ['label' => __d('fe', 'Title')]); ?>
					<?= $this->CustomForm->input('firstname', ['label' => __d('fe', 'Firstname')]); ?>
					<?= $this->CustomForm->input('lastname', ['label' => __d('fe', 'Lastname')]); ?>
					<?= $this->CustomForm->input('email', ['label' => __d('fe', 'E-Mail')]); ?>    
				</div>
				<div class="col right">
					<?= $this->CustomForm->input('address', ['label' => __d('fe', 'Address')]); ?>
					<?= $this->CustomForm->input('zip', ['label' => __d('fe', 'ZIP')]); ?>
					<?= $this->CustomForm->input('city', ['label' => __d('fe', 'City')]); ?>
					<?= $this->CustomForm->input('country', ['label' => __d('fe', 'Country'), 'empty' => __d('fe', '-- Please select --')]); ?>
					<?= $this->CustomForm->input('phone', ['label' => __d('fe', 'Phone')]); ?>    
				</div>
			</div>
			<?= $this->CustomForm->input('message', ['label' => __d('fe', 'Message')]); ?>
			<?= $this->element('Frontend.Website/newsletter') ?>
            <?= $this->element('Frontend.Website/privacy') ?>
			<?= $this->element('Frontend.Website/captcha') ?>
			<?= $this->CustomForm->end(); ?>
			<!--// Form End //-->
		<?php } ?>
	</div>
</div>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>