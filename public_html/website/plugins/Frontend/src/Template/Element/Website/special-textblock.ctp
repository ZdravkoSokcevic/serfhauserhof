<?php if(is_array($special_element_content) && array_key_exists('details', $special_element_content)){ ?>
<section class="main textblock">
    <div class="inner enumeration"><?= $special_element_content['details']['textblock']; ?></div>
</section>
<?php } ?>