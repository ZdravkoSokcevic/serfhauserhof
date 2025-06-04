<main class="page">
	<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'left', 'wrapper' => 'left']) ?>
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
            <?php if($messages['success'] === true){ ?>
                <div class="message success"><?= $messages['status']; ?></div>
            <?php }else if($messages['success'] === false){ ?>
                <div class="message error"><?= $messages['status']; ?></div>
            <?php } ?>
	    <?php }else{ ?>
	        <!--// Form Start //-->
	        <?= $this->CustomForm->create($form); ?>
			<div class="form-cols">
				<div class="col left">
					<?= $this->CustomForm->input('salutation', ['label' => __d('fe', 'Salutation'), 'empty' => __d('fe', '-- Please select --')]); ?>
					<?= $this->CustomForm->input('firstname', ['label' => __d('fe', 'Firstname')]); ?>
				</div>
				<div class="col right">
					<?= $this->CustomForm->input('lastname', ['label' => __d('fe', 'Lastname')]); ?>
					<?= $this->CustomForm->input('email', ['label' => __d('fe', 'E-Mail')]); ?>
				</div>
	        </div>
            <?= $this->element('Frontend.Website/privacy') ?>
	        <?= $this->element('Frontend.Website/captcha', ['text' => __d('fe', 'Subscribe'), 'options' => [['text' => __d('fe', 'Unsubscribe'), 'class' => 'option', 'type' => 'subscribe', 'url' => $this->Url->build(['node' => 'node:' . $this->request->params['node']['id'], 'language' => $this->request->params['language'], 'extend' => [__d('fe', 'unsubscribe')]])]]]) ?>
	        <?= $this->CustomForm->end(); ?>
	        <!--// Form End //-->
		<?php } ?>
	</div>
</div>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>
