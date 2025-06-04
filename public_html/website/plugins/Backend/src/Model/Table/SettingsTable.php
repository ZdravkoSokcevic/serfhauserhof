<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;

class SettingsTable extends Table
{
    
    public function initialize(array $config)
    {
        
        // behaviors
        $this->addBehavior('Timestamp');
        
        // $this->displayField('internal');
    }

    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('internal', __d('be', 'A internal title is required'))
            ->notEmpty('code', __d('be', 'A code is required'));
    }
    
    public function beforeSave($event, $entity, $options){

        $connection = ConnectionManager::get('default');
        $element = $connection->execute("SELECT `code` FROM `elements` WHERE `id` = :id", ['id' => $entity->foreign_id])->fetch('assoc');
        
        if(is_array($element) && count($element) > 0){

            // init
            $settings = Configure::read('elements.' . $element['code']);
            
            if(array_key_exists('settings', $settings) && is_array($settings['settings']) && array_key_exists('fields', $settings['settings']) && is_array($settings['settings']['fields'])){
            
                $fields = $settings['settings']['fields'];
                $info = [];
                
                // json
                foreach($fields as $_name => $_info){
                    if((!array_key_exists('translate', $_info) || !$_info['translate']) && isset($entity[$_name])){
                        $info[$_name] = $entity[$_name];
                    }
                }
                
                $entity->settings = json_encode($info);
            
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }

    public function afterFind($row, $settings){
        
        // json
        $fields = json_decode($row->settings, true);
        foreach($fields as $k => $v){
            $row->{$k} = $v;
        }
        
        return $row;
    }

    public function beforeDelete($event, $entity, $options){

    }
    
    public function afterDelete($event, $entity, $options){
        
    }
    
    public function setup($settings, $id, $options){
        
        // validation + translations
        $fields = [];
        
        if(array_key_exists('settings', $settings) && is_array($settings['settings']) && array_key_exists('fields', $settings['settings']) && is_array($settings['settings']['fields'])){
            foreach($settings['settings']['fields'] as $name => $info){
                if(array_key_exists('required', $info) && is_array($info['required'])){
                    if(($id != false && in_array('update', $info['required']['on'])) || ($id == false && in_array('insert', $info['required']['on']))){
                        if(array_key_exists('rules', $info['required']) && is_array($info['required']['rules'])){
                            foreach($info['required']['rules'] as $type => $rule){
                                switch(strtolower($type)){
                                    case "requirepresence":
                                        $this->validator('default')->requirePresence($name);
                                        break;
                                    case "notempty":
                                        $this->validator('default')->notEmpty($name, $rule);
                                        break;
                                    case "allowempty":
                                        $this->validator('default')->allowEmpty($name);
                                        break;
                                    default:
                                        $this->validator('default')->add($name, [$type => $rule]);
                                        break;
                                }
                            }
                        }
                    }
                }
                if(array_key_exists('translate', $info) && $info['translate']){
                    $fields[] = $name;
                }
            }
        }
        
        $this->addBehavior('Translate', ['fields' => $fields, 'defaultLocale' => false]);

    }

}