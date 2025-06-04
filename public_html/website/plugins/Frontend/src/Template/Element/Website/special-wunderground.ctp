<?php if(isset($special_element_content['details']['_details']) && is_array($special_element_content['details']['_details']) && count($special_element_content['details']['_details']) == 4){ ?>
	<section class="weather-container wunderground hidden-print">
		<div class="inner">
			<div class="cols">
				<?php $total = 3; $count = 1; ?>
			    <?php foreach($special_element_content['details']['_details'] as $nr => $day){ ?>
			    	<?php if($count > $total) break; ?>
			        <div class="col col-4 weather-col weather-col-<?php echo $nr; ?>">
			        	<div class="weather-col-inner">
				            <div class="weather-icon-wrap">
					            <div class="weather-icon"><?php echo $day['font_icon']; ?></div>
					            <div class="weather-conditions"><?php echo $day['conditions']; ?></div>
				           	</div>
				           	<div class="weather-text-wrap">
					            <div class="weather-date"><?php echo '<strong>' . $day['date']['day-name'] . '</strong>, ' . date("d.m.",$day['date']['uxt']); ?></div>
					            <div class="weather-temp">
						            <div class="weather-temp-min"><?php echo '<span class="title">' . __d('fe','Min') . '</span><span class="value">' .  $day['min'] . '°C</span>'; ?></div>
						            <div class="weather-temp-max"><?php echo '<span class="title">' . __d('fe','Max') . '</span><span class="value">' . $day['max'] . '°C</span>'; ?></div>
					            </div>
				            </div>
			            </div>
			        </div>
			        <?php $count++; ?>
			    <?php } ?>
		    </div>
		    <div class="weather-copy"><?php echo __d('fe', 'Weather from') . ' ' . $this->Html->link('www.wunderground.com','http://www.wunderground.com/',array('target' => '_blank', 'rel' => 'nofollow', 'escape' => false)); ?></div>
	    </div>
	</section>
<?php } ?>









