<?php if(isset($element_content['details']['mp4']['name']) && !empty($element_content['details']['mp4']['name'])){ ?>
	<section class="video-container main hidden-print">
		<div class="inner">
		    <video id="video-<?php echo $element_content['id']; ?>" class="video-js vjs-default-skin" controls="controls" preload="auto" width="1000px" height="563px">
		        <source src="/files/<?php echo $element_content['details']['mp4']['name']; ?>" type='<?php echo $element_content['details']['mp4']['type']; ?>' />
		        <source src="/files/<?php echo $element_content['details']['webm']['name']; ?>" type='<?php echo $element_content['details']['webm']['type']; ?>' />
		        <source src="/files/<?php echo $element_content['details']['ogv']['name']; ?>" type='<?php echo $element_content['details']['ogv']['type']; ?>' /> 
		    </video>
	    </div>
	</section>
<?php } ?>