<main>
    <a class="anchor" name="content"></a>
    <h1><?= $content['headline']; ?></h1>
    <div class="content">
        <?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'right', 'wrapper' => 'right']) ?>
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
						<?= $this->CustomForm->input('arrival', ['label' => __d('fe', 'Arrival'), 'value' => $arrival_val, 'class' => 'date date-from', 'data-date-range' => 'request', 'data-date-min' => date("Y-m-d")]); ?>    
				</div>
				<div class="col right">
						<?= $this->CustomForm->input('departure', ['label' => __d('fe', 'Departure'), 'value' => $departure_val, 'class' => 'date date-to', 'data-date-range' => 'request', 'data-date-min' => date("Y-m-d")]); ?>     
				</div>
			</div>

			<!-- rooms -->
			<div class="rooms-label label"><?= __d('fe', 'Rooms') ?></div>
			<div class="rooms-wrap">
				<?php if(array_key_exists('rooms', $this->request->data) && is_array($this->request->data['rooms']) && count($this->request->data['rooms']) > 0){ ?>
					<?php foreach($this->request->data['rooms'] as $k => $v){ ?>
						<?= $this->element('Frontend.Website/request-form-room', ['rooms' => $rooms, 'packages' => $packages, 'key' => $k, 'value' => $v]) ?>
					<?php } ?>
				<?php }else{ ?>
					<?= $this->element('Frontend.Website/request-form-room', ['rooms' => $rooms, 'packages' => $packages, 'key' => 0, 'value' => false]) ?>
				<?php } ?>
				<a href="javascript:void(0)" class="button room-add" onclick="selecotraction(this)"><i class="fa fa-plus"></i><?= __d('fe', 'add room') ?></a>
			</div>
			<div class="clear"></div>

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