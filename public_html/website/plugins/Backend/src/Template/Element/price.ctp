<?php
    // settings
    $settings = isset($parms) && is_array($parms) && array_key_exists('settings', $parms) ? $parms['settings'] : [];
    
    // values
    $item = isset($parms) && is_array($parms) && array_key_exists('item', $parms) ? $parms['item'] : '%item';
    $option = isset($parms) && is_array($parms) && array_key_exists('option', $parms) ? $parms['option'] : '%option';
    $element = isset($parms) && is_array($parms) && array_key_exists('element', $parms) ? $parms['element'] : '%element';
    $draft = isset($parms) && is_array($parms) && array_key_exists('draft', $parms) ? $parms['draft'] : '%draft';
    $value = isset($parms) && is_array($parms) && array_key_exists('value', $parms) ? $parms['value'] : '';
    $flag = isset($parms) && is_array($parms) && array_key_exists('flag', $parms) ? $parms['flag'] : '%flag';
    $text = isset($parms) && is_array($parms) && array_key_exists('text', $parms) ? $parms['text'] : '%text';
    
    if(!empty($settings['prices']['flags'][$flag]) && $settings['prices']['flags'][$flag] !== false){
        $flag_label = count($settings['prices']['flags']) > 1 ? $settings['prices']['flags'][$flag] : '';
    }else{
        $flag_label = '%flag_label';
    }
    
    // flags
    $flags = '';
    $flags .= '<span class="flag-label">'.$flag_label.'</span>';
	
?>
<div data-draft="<?= $draft; ?>" data-flag="<?= $flag; ?>" class="prices"><div class="draft"><?= $text; ?></div><input type="text" value="<?= str_replace('.',',',$value); ?>" name="prices[<?= $item; ?>][<?= $option; ?>][<?= $element; ?>][<?= $draft; ?>][<?= $flag ?>][value]" placeholder="<?= __d('be', 'Price'); ?>"><?= $flags; ?><a class="icon" href="javascript:removePrice('<?= $item; ?>', '<?= $option; ?>', '<?= $element; ?>', '<?= $draft; ?>', '<?= $flag ?>');" title="<?= __d('be', 'Delete price'); ?>"><i class="fa fa-trash"></i></a><div class="clear"></div></div>