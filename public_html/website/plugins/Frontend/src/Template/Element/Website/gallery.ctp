<?php if(array_key_exists('details', $element_content) && is_array($element_content['details']) && array_key_exists('images', $element_content['details']) && is_array($element_content['details']['images']) && count($element_content['details']['images']) > 0){ ?>
<?php $purpose = '1_gallery'; $focus = 1; $nr = 0; ?>
<section class="gallery hidden-print">
	<div class="inner">
	    <ul class="bxslider">
	        <?php foreach($element_content['details']['images'] as $image){ ?>
	            <?php if(array_key_exists('details', $image) && is_array($image['details']) && count($image['details']) > 0){ ?>
	                <li>
	                	<div class="bxslide" style="background-position: <?= $image['details']['focus'][$focus]['css']; ?>; background-image: url('<?= $image['details']['seo'][$purpose]; ?>');">
	                		<img src="<?= $image['details']['seo'][$purpose]; ?>" alt="<?= $image['details']['title']; ?>">
	                	</div>
	                </li>
	                <?php $nr++; ?>
	            <?php } ?>
	        <?php } ?>
	    </ul>
    </div>
</section>
<?php } ?>