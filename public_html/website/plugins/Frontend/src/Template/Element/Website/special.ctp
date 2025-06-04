<?php
    if(array_key_exists('type', $element_content['details'])){
        echo $this->element('Frontend.Website/' . $element_content['details']['code'] . '-' . $element_content['details']['type'], ['special_element_content' => $element_content]);
    }
?>