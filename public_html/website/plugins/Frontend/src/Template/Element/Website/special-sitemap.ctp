<?php if(array_key_exists('details', $special_element_content) && is_array($special_element_content['details']) && array_key_exists('_details', $special_element_content['details']) && is_array($special_element_content['details']['_details']) && count($special_element_content['details']['_details']) > 0){ ?>
<section class="sitemap">
	<div class="inner">
	    <?php echo $this->element('Frontend.Website/sitemap', ['nodes' => $special_element_content['details']['_details'], 'lvl' => 1]); ?>
    </div>
</section>
<?php } ?>