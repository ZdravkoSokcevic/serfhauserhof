<?php

namespace Frontend\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

class ElementsTable extends Table
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
            if(is_array($this->settings) && array_key_exists('fields', $this->settings)){
                foreach($this->settings['fields'] as $name => $info){
                    if(array_key_exists('translate', $info) && $info['translate']){
                        $fields[] = $name;
                    }
                }
            }
            if(count($fields) > 0){
                $this->addBehavior('Translate', ['fields' => $fields, 'defaultLocale' => false]);
                $this->locale($request->params['language']);
            }
            
            $this->setup = $code . ':' . $request->params['language'];
        }
    }

    public function afterFind($row){
        
        // json
        $fields = json_decode($row->fields, true);
        foreach($fields as $k => $v){
            $row->{$k} = $v;
        }
        
        // media
        if(isset($row->media) && !is_array($row->media) && !empty($row->media)){
            $media = json_decode($row->media, true);
            if(is_array($media)){
                foreach($media as $theme => $positions){
                    $check = array_filter($positions);
                    if(count($check) == 0){
                        unset($media[$theme]);
                    }
                }
            }else{
                $media = array();
            }
        }else{
            $media = array();
        }
        $row->media = $media;
        
        // callbacks
        foreach($this->settings['fields'] as $_name => $_info){
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