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
			<div class="form-cols">
				<div class="col left">
					<?= $this->CustomForm->input('salutation_recipient', ['label' => __d('fe', 'Salutation recipient'), 'options' => $salutations, 'empty' => __d('fe', '-- Please select --')]); ?>
					<?= $this->CustomForm->input('title_recipient', ['label' => __d('fe', 'Title recipient')]); ?>
				</div>
				<div class="col right">
					<?= $this->CustomForm->input('firstname_recipient', ['label' => __d('fe', 'Firstname recipient')]); ?>
					<?= $this->CustomForm->input('lastname_recipient', ['label' => __d('fe', 'Lastname recipient')]); ?>
				</div>
	        </div>
	        <?= $this->CustomForm->input('coupon_type', ['label' => __d('fe', 'Coupon type'), 'onchange' => 'changeCouponForm(this)', 'options' => ['vacation' => __d('fe', 'Option 1: Holiday'), 'value' => __d('fe', 'Option 2: Voucher')], 'empty' => __d('fe', '-- Please select --')]); ?>
	        <div id="option-1" class="hidden option">
				<div class="form-cols">
					<div class="col left">
						<?= $this->CustomForm->input('arrival', ['label' => __d('fe', 'Arrival') . '*', 'class' => 'date date-from', 'data-date-range' => 'coupon', 'data-date-min' => date("Y-m-d")]); ?>    
					</div>
					<div class="col right">
						<?= $this->CustomForm->input('departure', ['label' => __d('fe', 'Departure') . '*', 'class' => 'date date-to', 'data-date-range' => 'coupon', 'data-date-min' => date("Y-m-d")]); ?>    
					</div>
	            </div>
				<div class="form-cols">
					<div class="col left">
						<?= $this->CustomForm->input('adults', ['label' => __d('fe', 'Adults') . '*']); ?>    
					</div>
					<div class="col right">
						<?= $this->CustomForm->input('children', ['label' => __d('fe', 'Children')]); ?>    
					</div>
	            </div>
	            <?= $this->CustomForm->input('comment', ['label' => __d('fe', 'Comment')]); ?>
	        </div>
	        <div id="option-2" class="hidden option">
	            <?= $this->CustomForm->input('value', ['label' => __d('fe', 'Value') . '*']); ?>
	        </div>
	        <?= $this->element('Frontend.Website/newsletter') ?>
            <?= $this->element('Frontend.Website/privacy') ?>
	        <?= $this->element('Frontend.Website/captcha') ?>
	        <?= $this->CustomForm->end(); ?>
	        <!--// Form End //-->
		<?php } ?>
	</div>
</div>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>
<script>
    changeCouponForm(document.querySelector("#coupon-type"));
    function changeCouponForm(e){
        if(e){
            if(e.options[e.selectedIndex].value == 'vacation'){
                var opt1 = document.querySelector("#option-1");
                opt1.classList.remove("hidden");
                var opt2 = document.querySelector("#option-2");
                opt2.classList.add("hidden");
            }else if(e.options[e.selectedIndex].value == 'value'){
                var opt1 = document.querySelector("#option-1");
                opt1.classList.add("hidden");
                var opt2 = document.querySelector("#option-2");
                opt2.classList.remove("hidden");
            }else{
                var opt1 = document.querySelector("#option-1");
                opt1.classList.add("hidden");
                var opt2 = document.querySelector("#option-2");
                opt2.classList.add("hidden");
            }
        }
    }
</script>