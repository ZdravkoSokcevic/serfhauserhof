<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class ConfigTable extends Table
{
    
    public $label = false;

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        
        if($this->label !== false && is_array(Configure::read('config.' . $this->label))){

            // validation
            foreach(Configure::read('config.' . $this->label . '.fields') as $name => $info){
                if(array_key_exists('required', $info) && is_array($info['required'])){
                    if(array_key_exists('rules', $info['required']) && is_array($info['required']['rules'])){
                        $fieldnames = [];
                        if(array_key_exists('multi', $info) && $info['multi']){
                            foreach(Configure::read('translations') as $k => $v){
                                if($v['active']){
                                    $fieldnames[] = $name.'-'.$k;
                                }
                            }
                        }else{
                            $fieldnames[] = $name;
                        }
                        foreach($fieldnames as $fieldname){
                            foreach($info['required']['rules'] as $type => $rule){
                                switch(strtolower($type)){
                                    case "requirepresence":
                                        $validator->requirePresence($fieldname);
                                        break;
                                    case "notempty":
                                        $validator->notEmpty($fieldname, $rule);
                                        break;
                                    case "allowempty":
                                        $validator->allowEmpty($fieldname);
                                        break;
                                    default:
                                        $validator->add($fieldname, [$type => $rule]);
                                        break;
                                }
                            }
                        }
                    }
                }
            }
            return $validator;
        }
        return false;
    }

    public function beforeSave($event, $entity, $options){
        if(Configure::read('config.' . $entity->label . '.fields')){
            
            // init
            $info = [];
            
            // json
            foreach(Configure::read('config.' . $entity->label . '.fields') as $_name => $_info){
                if(array_key_exists('multi', $_info) && $_info['multi']){
                    foreach(Configure::read('translations') as $k => $v){
                        if($v['active']){
                            $info[$_name . '-' . $k] = $entity[$_name . '-' . $k];
                        }
                    }
                }else{
                    $info[$_name] = $entity[$_name];
                }
            }
            $entity->settings = json_encode($info);
        
        }else{
            return false;
        }
    }

    public function afterFind($row){
        
        // json
        $settings = json_decode($row->settings, true);
        foreach($settings as $k => $v){
            $row->{$k} = $v;
        }
        
        return $row;
    }

}