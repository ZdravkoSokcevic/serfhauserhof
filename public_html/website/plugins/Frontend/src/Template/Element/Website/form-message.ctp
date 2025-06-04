<div class="content">
<?php if($messages['success'] === true){ ?>
    <div class="message success"><?= __d('fe', 'Your request has been successfully sent.'); ?></div>
<?php }else if($messages['success'] === false){ ?>
    <div class="message error"><?= __d('fe', 'When sending your request an error occurred! Please try again.'); ?></div>
<?php } ?>
<?php if($messages['newsletter'] === true){ ?>
    <div class="message space-top success"><?= __d('fe', 'You will receive a confirmation email to sign up for the newsletter.'); ?></div>
<?php }else if($messages['newsletter'] === 'exists'){ ?>
    <div class="message space-top error"><?= __d('fe', 'This e-mail address is already registered for the newsletter.'); ?></div>
<?php }else if($messages['newsletter'] === false){ ?>
    <div class="message space-top error"><?= __d('fe', 'When registering for the newsletter an error occurred!'); ?></div>
<?php } ?>
</div>