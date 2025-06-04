<?php use Cake\Core\Configure; ?>
<?php
    $show = isset($show) ? (bool) $show : true;
    $text = isset($text) ? $text : __d('fe','Send');
    $opt = [];

    if(isset($options) && is_array($options)){
        foreach($options as $option){
            if(array_key_exists('type', $option) && array_key_exists('text', $option) && array_key_exists('class', $option) && array_key_exists('url', $option)){
                $opt[] = $option;
            }
        }
    }
?>
<div class="captcha-error"><?= __d('fe','There was an error sending the captcha. Please reload the page and try again!') ?></div>
<section class="captcha hidden-print">
    <?php if($show){ ?>
    <div class="input required captcha">
        <div class="submit">
            <?php echo '<button
                class="g-recaptcha button"
                data-sitekey="' . Configure::read('config.tracking.recapcha_site_key') . '"
                data-callback="onFormSubmit">' . $text . '</button>'; ?>
        </div>
        <div class="clear"></div>
    </div>
    <?php } ?>
    <div class="clear"></div>
</section>
<?php if(count($opt) > 0){ ?>
<section class="send-option hidden-print">
    <?php foreach($opt as $o){ ?>
        <a href="<?= $o['url']; ?>" class="<?= $o['class']; ?> <?= $o['type']; ?>"><?= $o['text']; ?></a>
        <div class="clear"></div>
    <?php } ?>
</section>
<?php } ?>
<div class="required-info"><?= str_replace('*','<span>*</span>',__d('fe', 'Fields marked with * must be entered.')); ?></div>


<?php
  //check if captcha is used
  echo $this->Form->input('c-info', array('label' => false, 'type' => 'hidden'));

  //honeypot
  echo '<div class="hidden">';
    echo $this->Form->input('h-info', array('label' => false, 'type' => 'text'));
  echo '</div>';
?>

<script>
 	function onFormSubmit(token) {
    		//verify token
    		$.post( "/recaptcha", {
    			'response': token,
    			'remoteip': '<?= $_SERVER['REMOTE_ADDR'] ?>',
    		})
           .always(function(response) {
    			var response = $.parseJSON(response);
    			if(response.success === true){
		        $('input#c-info').attr('value', response.recaptchaConfirm);
     	      $('button.g-recaptcha').parents('form').submit();
    			} else{
    				$('.captcha-error').fadeIn();
    			}
    	  });
 	}
</script>
