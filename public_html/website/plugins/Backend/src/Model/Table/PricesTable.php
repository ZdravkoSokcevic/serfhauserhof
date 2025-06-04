<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class PricesTable extends Table
{
    
    public function interlace($results)
    {
        
        $interlaced = [];

        // item > option > element > draft > value

        foreach($results as $result){
            
            // item
            if(!array_key_exists($result->foreign_id, $interlaced)){
                $interlaced[$result->foreign_id] = [];
            }
            
            // option
            $option = $result->option ? $result->option : 'false';
            if(!array_key_exists($option, $interlaced[$result->foreign_id])){
                $interlaced[$result->foreign_id][$option] = [];
            }
            
            // element
            $element = $result->element ? $result->element : 'false';
            if(!array_key_exists($element, $interlaced[$result->foreign_id][$option])){
                $interlaced[$result->foreign_id][$option][$element] = [];
            }
            
            // draft
            $interlaced[$result->foreign_id][$option][$element][$result->price_draft_id][$result->flag] = [
                'value' => $result->value,
            ];
            
        }
        
        return $interlaced;
    }

}