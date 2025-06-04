<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;

class SelectorTable extends Table
{

    public function initialize(array $config)
    {
    }

    public function nestedIds(&$ids, $nested, $field, $deeper){
        foreach($nested as $k => $v){
            if(array_key_exists($field, $v) && !empty($v[$field])){
                $ids[] = $v[$field];
                if(array_key_exists($deeper, $v) && is_array($v[$deeper]) && count($v[$deeper]) > 0){
                    $this->nestedIds($ids, $v[$deeper], $field, $deeper);
                }
            }
        }
    }

}