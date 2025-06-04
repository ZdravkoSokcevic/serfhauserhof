<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;

class ElementsTable extends Table
{
    
    public $path = false;
    public $url = false;

    public function initialize(array $config)
    {
        
        // init
        $this->url = DS . Configure::read('upload.elements.dir');
        $this->path = ROOT . DS . Configure::read('App.webroot') . DS . Configure::read('upload.elements.dir');
        
        // behaviors
        $this->addBehavior('Timestamp');
        
        $this->displayField('internal');
    }

    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('internal', __d('be', 'A internal title is required'))
            ->notEmpty('code', __d('be', 'A code is required'));
    }
    
    public function beforeSave($event, $entity, $options){
            
        // init
        $fields = Configure::read('elements.' . $entity->code . '.fields');
        $info = [];
        
        // callbacks
        foreach($fields as $_name => $_info){
            if(isset($entity[$_name]) && array_key_exists('callbacks', $_info) && is_array($_info['callbacks']) && array_key_exists('beforesave', $_info['callbacks']) && is_string($_info['callbacks']['beforesave']) && !empty($_info['callbacks']['beforesave'])){
                $handler = '_handle' . ucfirst($_info['callbacks']['beforesave']);
                if(method_exists($this, $handler)){
                    if($result = $this->{$handler}($_name, $_info, $event, $entity, $options)){
                        if($result !== true){
                            $entity->{$_name} = $result;
                        } 
                    }else{
                        return false;
                    }
                }
            }
        }
        
        // json
        foreach($fields as $_name => $_info){
            if((!array_key_exists('translate', $_info) || !$_info['translate']) && isset($entity[$_name])){
                $info[$_name] = $entity[$_name];
            }
        }
        
        $entity->fields = json_encode($info);
        
        // media
        if(isset($entity->media) && is_array($entity->media)){
            $entity->media = json_encode($entity->media);
        }
        
    }

    public function afterSave($event, $entity, $options){
        
        // init
        $settings = Configure::read('elements.' . $entity->code);
        
        // settings (delete settings if they are no longer in selection/subselection)
        if(array_key_exists('settings', $settings) && is_array($settings['settings'])){

            $selections = array_filter(explode(";", $entity->{$settings['settings']['selection']}));
            $subselections = array_filter(explode(";", $entity->{$settings['settings']['subselection']}));

            $connection = !isset($connection) ? ConnectionManager::get('default') : $connection;
            $infos = $connection->execute("SELECT * FROM `settings` WHERE `foreign_id` = :id AND (`selection` NOT IN ('" . join("','", $selections) . "') OR `subselection` NOT IN ('" . join("','", $subselections) . "'))", ['id' => $entity->id])->fetchAll('assoc');
            
            if(is_array($infos) && count($infos) > 0){
                foreach($infos as $k => $v){
                    $connection->execute("DELETE FROM `settings` WHERE `id` = :id", ['id' => $v['id']]);
                    $connection->execute("DELETE FROM `i18n` WHERE `foreign_key` = :key", ['key' => $v['id']]);
                }
            }
        }
        
    }

    public function afterFind($row, $settings){
        
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
            $row->media = $media;
        }
        
        // callbacks
        foreach($settings['fields'] as $_name => $_info){
            if(isset($row[$_name]) && array_key_exists('callbacks', $_info) && is_array($_info['callbacks']) && array_key_exists('afterfind', $_info['callbacks']) && is_string($_info['callbacks']['afterfind']) && !empty($_info['callbacks']['afterfind'])){
                $handler = '_handle' . ucfirst($_info['callbacks']['afterfind']);
                if(method_exists($this, $handler)){
                    $row[$_name] = $this->{$handler}($_name, $_info, $row);
                }
            }
        }

        return $row;
    }

    public function beforeDelete($event, $entity, $options){

        // init
        $settings = Configure::read('elements.' . $entity->code);
    
        // check
        if(array_key_exists('structure', $settings) && $settings['structure']){
            $connection = ConnectionManager::get('default');
            $check = $connection->execute("SELECT `id` FROM `nodes` WHERE `foreign_id` = :id", ['id' => $entity->id])->fetchAll('assoc');
            if(is_array($check) && count($check) > 0){
                $entity['_in_structure'] = true;
                return false;
            }
        }
        
        // prepare
        $entity = $this->afterFind($entity, $settings);
        
        // callbacks
        foreach($settings['fields'] as $_name => $_info){
            if(isset($entity[$_name]) && array_key_exists('callbacks', $_info) && is_array($_info['callbacks']) && array_key_exists('beforedelete', $_info['callbacks']) && is_string($_info['callbacks']['beforedelete']) && !empty($_info['callbacks']['beforedelete'])){
                $handler = '_handle' . ucfirst($_info['callbacks']['beforedelete']);
                if(method_exists($this, $handler)){
                    $this->{$handler}($_name, $_info, $event, $entity, $options);
                }
            }
        }
        
    }
    
    public function afterDelete($event, $entity, $options){
        
        // init
        $all_settings = Configure::read('elements');
        $settings = $all_settings[$entity->code];
        $connection = ConnectionManager::get('default');
        
        // prices
        if(array_key_exists('prices', $settings) && is_array($settings['prices'])){
            $connection->execute("DELETE FROM `prices` WHERE `foreign_id` = :id", ['id' => $entity->id]);
        }
        
        // settings
        if(array_key_exists('settings', $settings) && is_array($settings['settings'])){
            $infos = $connection->execute("SELECT `id` FROM `settings` WHERE `foreign_id` = :id", ['id' => $entity->id])->fetchAll('assoc');
            if(is_array($infos) && count($infos) > 0){
                $connection->execute("DELETE FROM `settings` WHERE `foreign_id` = :id", ['id' => $entity->id]);
                foreach($infos as $k => $v){
                    $connection->execute("DELETE FROM `i18n` WHERE `foreign_key` = :key", ['key' => $v['id']]);
                }
            }
        }
        
        // images with this element as category
        $use_categories = Configure::read('images.use_categories');
        if(is_string($use_categories) && array_key_exists($use_categories, Configure::read('elements')) && Configure::read('elements.' . $use_categories . '.active')){
                            
            // images
            $images = $connection->execute("SELECT `id` FROM `images` WHERE `category_id` = :id", ['id' => $entity->id])->fetchAll('assoc');
            
            if(is_array($images) && count($images) > 0){
                    
                // init images model
                $imagesTable = TableRegistry::get('Backend.Images');
            
                // delte
                foreach($images as $image){
                    try {
                        $entity = $imagesTable->get($image['id']);
                        $imagesTable->delete($entity);
                    } catch (RecordNotFoundException $e) { }
                }
            }
        }
        
        // elements with this element as category
        foreach($all_settings as $c => $s){
            if(array_key_exists('use_categories', $s) && is_string($s['use_categories']) && $s['active']){
                
                // elements
                $elements = $connection->execute("SELECT `id` FROM `elements` WHERE `category_id` = :id", ['id' => $entity->id])->fetchAll('assoc');
                
                if(is_array($elements) && count($elements) > 0){
                
                    // validation + translations
                    $this->setup($s);
                
                    // delte
                    foreach($elements as $element){
                        try {
                            $entity = $this->get($element['id']);
                            $this->delete($entity);
                        } catch (RecordNotFoundException $e) { }
                    }
                }
            }
        }
        
    }
    
    public function infos($infos){
        
        $result = [];
        
        if(!empty($infos)){
            
            // init
            $infos = array_filter(explode(";", $infos));
            $imagesTable = TableRegistry::get('Backend.Images');
            $categoriesTable = TableRegistry::get('Backend.Categories');
            $nodesTable = TableRegistry::get('Backend.Nodes');
            
            foreach($infos as $info){
                $i = false;
                list($type, $id) = explode(":", $info, 2);

                try {
                    if($type == 'image'){
                        $i = $imagesTable->get($id);
                        $i = [
                            'type' => 'image',
                            'id' => $i->id,
                            'filename' => $i->id . '.' . $i->extension,
                            'title' => $i->title,
                            'original' => $i->original,
                        ];
                    }else if($type == 'node'){
                        $i = $nodesTable->get($id);
                        $e = $elementsTable->get($i->foreign_id);
                        $i = [
                            'type' => 'node',
                            'id' => $i->id,
                            'icon' => 'sitemap',
                            'title' => $e->internal,
                            'desc' => Configure::read('elements.' . $e->code . '.translations.type'),
                        ];
                    }else if($type == 'category'){
                        $i = $categoriesTable->get($id);
                        $i = [
                            'type' => 'category',
                            'id' => $i->id,
                            'title' => $i->internal,
                            'desc' => __d('be', 'Category'),
                        ];
                    }else{
                        $i = $this->get($id); 
                        if(Configure::read('elements.' . $i->code)){
                            $i = [
                                'type' => $i->code,
                                'id' => $i->id,
                                'icon' => Configure::read('elements.' . $i->code . '.icon'),
                                'title' => $i->internal,
                                'desc' => Configure::read('elements.' . $i->code . '.translations.type'),
                            ];
                        }else{
                            $i = false;
                        }
                    }
                } catch (RecordNotFoundException $e) {
                    $i = false;
                }

                if($i){
                    $result[] = [
                        'type' => $type,
                        'infos' => $i,
                    ];
                };
                
            }
        }
        
        return $result;
    }
    
    public function setup($settings, $id = false){
        
        // validation + translations
        $fields = [];
        foreach($settings['fields'] as $name => $info){
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
        $this->addBehavior('Translate', ['fields' => $fields, 'defaultLocale' => false]);

    }

    public function _category($category, $code){
        
        // settings
        if(array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active') ){
            $settings = Configure::read('elements.' . $code);
        }else{
            return false;
        }

        // validation + translations
        $this->setup($settings, false);
        
        $success = true;
        $elements = $this->find('list')->where(['category_id' => $category])->toArray();
        foreach($elements as $id => $title){
            $entity = $this->get($id);
            if(!$this->delete($entity)){
                $success = false;
            }
        }
        return $success;
    }
    
    private function _handleSavefile($name, $info, $event, $entity, $options){
        if(isset($entity[$name]) && is_array($entity[$name]) && count($entity[$name]) > 0 && $entity[$name]['error'] != UPLOAD_ERR_NO_FILE){
            if($entity[$name]['error'] ==  UPLOAD_ERR_OK){
                $filename = Text::uuid() . '.' . strtolower(pathinfo($entity[$name]['name'], PATHINFO_EXTENSION));
                if(rename($entity[$name]['tmp_name'], $this->path . $filename)){
                    
                    // set premissions
                    chmod($this->path . $filename, 0777);
                    
                    // delete old one
                    $original = $entity->getOriginal($name);
                    if(is_array($original) && array_key_exists('name', $original) && file_exists($this->path . $original['name'])){
                        @unlink($this->path . $original['name']);
                    }
                    
                    // result
                    $result = [
                        'name' => $filename,
                        'title' => $entity[$name]['name'],
                        'type' => $entity[$name]['type'],
                    ];

                    // return                    
                    return array_key_exists('translate', $info) && $info['translate'] == true ? json_encode($result) : $result;
                }else{
                    $entity->errors($name, __d('be', 'Unable to save file!'));
                    return false;
                }
            }else{
                switch($entity[$name]['error']){
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $entity->errors($name, __d('be', 'Uploaded file exceeds the maximum filesize!'));
                        break;
                    case UPLOAD_ERR_PARTIAL:
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                    case UPLOAD_ERR_EXTENSION:
                    default:
                        $entity->errors($name, __d('be', 'File upload failed!'));
                        break;
                }
                return false;
            }
        }else{
            $entity->{$name} = $entity->getOriginal($name);
        }
        return true;
    }
    
    public function _handleFindfile($name, $info, $row){
        if(isset($row[$name]) && is_array($info) && array_key_exists('translate', $info) && $info['translate'] === true){
            return json_decode($row[$name], true);
        }
        return isset($row[$name]) ? $row[$name] : false;
    }
    
    private function _handleDeletefile($name, $info, $event, $entity, $options){
        
        // init
        $files = [];
        
        // get files
        if(isset($entity[$name]) && isset($entity['id']) && is_array($info)){
            if(array_key_exists('translate', $info) && $info['translate'] === true){ // translated (delete all files!)
                $element = $this->get($entity['id'], ['finder' => 'translations'])->toArray();
                foreach($element['_translations'] as $t){
                    $file = json_decode($t[$name], true);
                    if(is_array($file) && array_key_exists('name', $file)){
                        $files[] = $this->path . $file['name'];
                    }
                }
            }else if(is_array($entity[$name]) && array_key_exists('name', $entity[$name])){ // not translated
                $files[] = $this->path . $entity[$name]['name'];
            }
        }
        
        // delete
        foreach($files as $file){
            @unlink($file);
        }
    }

}