<?php

use Cake\Core\Configure;

$fallback = count($translations) == 0 && isset($fallback) && $fallback === true ? true : false;

if($fallback === false){
    foreach(Configure::read('translations') as $k => $v){
        if($v['active'] === true){
            $cls = array_key_exists($k,$translations) ? '' : ' inactive';
            echo $this->Html->link('<div class="hide-text">' . $v['title'] . '</div>', array_merge($url, ['?' => ['locale' => $k]]), ['title' => $v['title'], 'escape' => false, 'class' => 'hide-text flag ' . $k . $cls]);
        }
    }
}else{
    
    $flags = 0;
    foreach(Configure::read('translations') as $k => $v){
        if($v['active'] === true){
            $flags++;
        }
    }
    
    echo '<div class="flag-dummy" style="width: ' . flag_callback_width($flags) . 'px;"></div>' . $this->element('Backend.icon', ['icon' => 'pencil', 'text' => __d('be', 'Edit'), 'url' => $url]);
}

if(isset($clear) && $clear === true){
    echo '<div class="clear"></div>';
}
