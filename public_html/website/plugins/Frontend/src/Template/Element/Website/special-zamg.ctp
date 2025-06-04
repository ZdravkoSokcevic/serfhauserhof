<?php if(isset($special_element_content['details']['_details']) && is_array($special_element_content['details']['_details']) && count($special_element_content['details']['_details']) == 3){ ?>
	<section class="weather-container wunderground hidden-print">
		<div class="inner">
			<div class="cols">
				<?php $total = 3; $count = 1; ?>
			    <?php foreach($special_element_content['details']['_details'] as $nr => $day){ ?>
			    	<?php if($count > $total) break; ?>
			        <div class="col weather-col weather-col-<?php echo $nr; ?>">
			        	<div class="weather-col-inner">
                            <div class="weather-date"><?php echo '<strong>' . $day['date']['day-name']['long'] . '</strong>, ' . date("d.m.",$day['date']['uxt']); ?></div>                            
				            <div class="weather-icons-wrap">
                                <?php foreach(['morning' => __d('fe', "6 am"),'noon' => __d('fe', "12 noon"), 'eve' => __d('fe', "6 pm")] as $k => $v){ ?>
                                <div class="weather-icon"><img src="/frontend/img/weather/<?= strtolower($day[$k]); ?>.png" /><span><?= $v; ?></span></div>
                                <?php } ?>
				           	</div>
                            <div class="weather-temp">
                                <div class="weather-temp-min"><?php echo '<span class="title">' . __d('fe','Min') . '</span><span class="value">' .  $day['min'] . '°C</span>'; ?></div>
                                <div class="weather-temp-max"><?php echo '<span class="title">' . __d('fe','Max') . '</span><span class="value">' . $day['max'] . '°C</span>'; ?></div>
                            </div>
                            <div class="weather-conditions"><?php echo isset($day['desc']) ? $day['desc'] : ''; ?></div>
			            </div>
			        </div>
			        <?php $count++; ?>
			    <?php } ?>
		    </div>
		    <div class="weather-copy">© ZAMG</div>
	    </div>
	</section>
<?php } ?>
