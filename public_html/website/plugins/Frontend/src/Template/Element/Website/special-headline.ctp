<?php if(array_key_exists('details', $special_element_content) && is_array($special_element_content['details']) && array_key_exists('headline', $special_element_content['details']) && !empty($special_element_content['details']['headline'])){ ?>
<h2 class="h1-like special-headline"><?= $special_element_content['details']['headline']; ?></h2>
<?php } ?>