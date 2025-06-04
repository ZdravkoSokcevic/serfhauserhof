<section class="webcam-container hidden-print">
	<div class="inner">
        <h2><?= $special_element_content['details']['headline'] ?></h2>
        <?php if(strlen($special_element_content['details']['webcam']) == 4){ ?>
    	<div class="webcam-iframe-container">
    		<iframe src="https://webtv.feratel.com/webtv/?cam=<?= $special_element_content['details']['webcam'] ?>&t=1&design=v3&c0=0&c2=0&lg=de&s=0&c8=0&c1=0" frameborder="0" scrolling="no" allowfullscreen></iframe>
    	</div>
        <?php }else{ ?>
    	<div class="webcam-image-container">
    		<img src="<?= $special_element_content['details']['webcam'] ?>?c=<?= time(); ?>" />
    	</div>        
        <?php } ?>
    </div>
</section>