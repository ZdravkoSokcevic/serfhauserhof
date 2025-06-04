<?php
namespace Frontend\View\Helper;
 
use Cake\View\Helper\FormHelper;
 
class CustomFormHelper extends FormHelper {
	
 	protected function _parseOptions($fieldName, $options)
    {
        $options = parent::_parseOptions($fieldName, $options);
		
		if(in_array($options['type'], ['text', 'email', 'tel', 'number', 'textarea', 'password']) && !array_key_exists('placeholder', $options) && array_key_exists('label', $options)){
			$options['placeholder'] = $options['label'];
			if(array_key_exists('class', $options)){
				$options['class'] .= ' mobile-placeholder'; 
			} else{
				$options['class'] = 'mobile-placeholder'; 
			}
		}
		
        return $options;
    }
	
}
?>