<?php

use Cake\Core\Configure;

if(count(Configure::read('translations')) > 1){
    foreach(Configure::read('translations') as $k => $v){
        if($v['active'] === true){
            $cls = $active == $k ? ' active' : '';
            echo $this->Html->link($v['title'], array_merge($this->request->params['pass'], ['?' => ['locale' => $k]]), ['class' => 'button' . $cls, 'confirm' => __d('be','Unsaved changes will be lost if you change the language!')]);
        }
    }
}