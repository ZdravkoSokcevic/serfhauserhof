<?php if($action == 'list'){ ?>
<main class="page">
    <a class="anchor" name="content"></a>
    <h1><?= $content['headline']; ?></h1>
    <div class="content">
        <?= $content['content']; ?>
        <div class="clear"></div>
    </div>
</main>
<?php if(count($jobs) > 0){ ?>
  <?php foreach($jobs as $category){ ?>
    <section class="jobs inner">
      <h2><?= $category['category']; ?></h2>
      <ul>
      <?php foreach($category['jobs'] as $job){ ?>
          <li>
              <h3><?= $job['headline']; ?><i class="fa fa-angle-up" aria-hidden="true"></i><i class="fa fa-angle-down" aria-hidden="true"></i><span class="clear"></span></h3>
              <div class="enumeration">
                  <?= $job['content']; ?>
                  <a href="<?= $this->Url->build(['node' => 'node:' . $this->request->params['node']['id'], 'language' => $this->request->params['language'], 'extend' => [$job['id'], $job['url']]]); ?>" class="button dark uc"><?= __d('fe', 'Apply now'); ?></a>
                  <div class="clear"></div>
              </div>
          </li>
      <?php } ?>
      </ul>
    </section>
  <?php } ?>
<?php }else{ ?>
<div class="message"><?= __d('fe', 'No jobs available'); ?></div>
<?php } ?>

<?php }else{ ?>
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
      <?= $this->CustomForm->input('position', ['label' => __d('fe', 'Position'), 'value' => is_array($job) && array_key_exists('headline', $job) ? $job['headline'] : '']); ?>
			<div class="form-cols">
				<div class="col left">
					<?= $this->CustomForm->input('salutation', ['label' => __d('fe', 'Salutation'), 'empty' => __d('fe', '-- Please select --')]); ?>
					<?= $this->CustomForm->input('title', ['label' => __d('fe', 'Title')]); ?>
					<?= $this->CustomForm->input('firstname', ['label' => __d('fe', 'Firstname')]); ?>
					<?= $this->CustomForm->input('lastname', ['label' => __d('fe', 'Lastname')]); ?>
					<?= $this->CustomForm->input('birthday', ['label' => __d('fe', 'Birthday')]); ?>
          <?= $this->CustomForm->input('citizenship', ['label' => __d('fe', 'Citizenship')]); ?>
				</div>
				<div class="col right">
          <?= $this->CustomForm->input('email', ['label' => __d('fe', 'E-Mail')]); ?>
					<?= $this->CustomForm->input('address', ['label' => __d('fe', 'Address')]); ?>
					<?= $this->CustomForm->input('zip', ['label' => __d('fe', 'ZIP')]); ?>
					<?= $this->CustomForm->input('city', ['label' => __d('fe', 'City')]); ?>
					<?= $this->CustomForm->input('country', ['label' => __d('fe', 'Country'), 'empty' => __d('fe', '-- Please select --')]); ?>
					<?= $this->CustomForm->input('phone', ['label' => __d('fe', 'Phone')]); ?>    
				</div>
			</div>
            <?= $this->CustomForm->input('education', ['label' => __d('fe', 'Education')]); ?>
            <?= $this->CustomForm->input('references', ['label' => __d('fe', 'References')]); ?>
            <?= $this->CustomForm->input('languages', ['label' => __d('fe', 'Languages')]); ?>
			<?= $this->CustomForm->input('message', ['label' => __d('fe', 'Message')]); ?>
            <?= $this->element('Frontend.Website/privacy') ?>
			<?= $this->element('Frontend.Website/captcha') ?>
			<?= $this->CustomForm->end(); ?>
			<!--// Form End //-->
		<?php } ?>
	</div>
</div>
<?php } ?>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>