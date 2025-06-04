<?php

namespace Api\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

class SettingsTable extends Table
{
    
    var $settings = array();
    var $setup = false;
    
    public function initialize(array $config)
    {
        
    }
    
    public function setup($code, $request){
        
        if($this->setup != $code . ':' . $request->params['language']){
            
            // settings
            if(array_key_exists($code, Configure::read('elements'))){
                $this->settings = Configure::read('elements.' . $code);
            }
            
            // translations
            if($this->behaviors()->has('Translate')){
                $this->removeBehavior('Translate');
            }
            
            $fields = [];
            if(is_array($this->settings) && array_key_exists('settings', $this->settings) && is_array($this->settings['settings']) && array_key_exists('fields', $this->settings['settings']) && is_array($this->settings['settings']['fields'])){
                foreach($this->settings['settings']['fields'] as $name => $info){
                    if(array_key_exists('translate', $info) && $info['translate']){
                        $fields[] = $name;
                    }
                }
            }
            
            $this->addBehavior('Translate', ['fields' => $fields, 'defaultLocale' => false]);
            $this->locale($request->params['language']);
        
            $this->setup = $code . ':' . $request->params['language'];
        }
    }

    public function afterFind($row){
        
        // json
        $fields = json_decode($row->settings, true);
        foreach($fields as $k => $v){
            $row->{$k} = $v;
        }
        
        // callbacks
        foreach($this->settings['settings']['fields'] as $_name => $_info){
            if(isset($row[$_name]) && array_key_exists('callbacks', $_info) && is_array($_info['callbacks']) && array_key_exists('afterfind', $_info['callbacks']) && is_string($_info['callbacks']['afterfind']) && !empty($_info['callbacks']['afterfind'])){
                $handler = '_handle' . ucfirst($_info['callbacks']['afterfind']);
                if(method_exists($this, $handler)){
                    $row[$_name] = $this->{$handler}($_name, $_info, $row);
                }
            }
        }
        
        return $row;
    }

}