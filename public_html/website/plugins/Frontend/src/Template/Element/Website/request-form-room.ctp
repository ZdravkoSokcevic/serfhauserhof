<?php
if(!is_array($value)){
	 $value = array(
	 	'room' => '',
	 	'package' => '',
	 	'adults' => '',
	 	'children' => '',
	 );
}
?>
<div class="room-wrap" id="room-<?= $key ?>" data-room-key="<?= $key ?>">
	<a href="javascript:void(0)" onclick="selecotraction(this)" class="room-remove room-nodelete" title="<?= __d('fe', 'remove room') ?>"><i class="fa fa-times"></i></a>
	<?= $this->CustomForm->input('rooms.' . $key . '.room', ['class' => 'room-select', 'label' => __d('fe', 'Room category'), 'options' => $rooms, 'value' => $value['room'], 'empty' => __d('fe', '-- Please select room --')]); ?>
	<div class="room-col room-col-1">
		<?= $this->CustomForm->input('rooms.' . $key . '.adults', ['class' => 'room-adults', 'placeholder' => __d('fe', 'Adults'), 'type' => 'number', 'step' => 1, 'min' => 0, 'max' => 10, 'label' => __d('fe', 'Adults'), 'value' => $value['adults']]); ?>
        <?= $this->CustomForm->input('rooms.' . $key . '.package', ['class' => 'package-select', 'label' => __d('fe', 'Package'), 'options' => $packages, 'value' => $value['package'], 'empty' => __d('fe', '-- Please select package --')]); ?>
	</div>
	<div class="room-col room-col-2">
		<?= $this->CustomForm->input('rooms.' . $key . '.children', ['class' => 'room-children', 'placeholder' => __d('fe', 'Children'), 'type' => 'number', 'step' => 1, 'min' => 0, 'max' => 4, 'label' => __d('fe', 'Children'), 'value' => $value['children']]); ?>
        <?php if(is_array($value) && array_key_exists('ages', $value) && is_array($value['ages']) && count($value['ages']) > 0){ ?>
            <?php foreach($value['ages'] as $age_k => $age_v){ ?>
            <?= $this->CustomForm->input('rooms.' . $key . '.ages.' . $age_k . '.age', ['id' => 'room-' . $key . '-age-' . $age_k, 'class' => 'age', 'placeholder' => __d('fe', 'childage'), 'label' => __d('fe', 'childage'), 'value' => $age_v]); ?>
            <?php } ?>
        <?php } ?>
	</div>
</div>