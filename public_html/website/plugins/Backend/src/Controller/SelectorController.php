<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class SelectorController extends AppController {

    public $allow = ['node', 'button', 'media', 'error', 'preview', 'anchors'];

    public function beforeRender(Event $event)
    {
        
        parent::beforeRender($event);
        
        // set layout
        $this->viewBuilder()->layout('selector');
        
    }

    function node($structure){
        
        // init
        $settings = Configure::read('elements');
        $types = [];
        
        // get types
        foreach($settings as $k => $v){
            if(array_key_exists('structure', $v) && $v['structure'] === true){
                $types[] = $k;
            }
        }
        
        // process
        $this->media(false, false, false, 1, false, ['images' => false, 'elements' => join("|", $types), 'nodes' => false, 'categories' => false, 'editor' => false]);
        
        // set
        $this->set('mode', 'node');
        $this->set('structure', $structure);
        
        // render
        $this->render('media');
        
    }

    function button($rel, $images, $elements, $links, $categories, $max, $act, $editor = false){
        
        // init
        $images = $images != 'false' ? $images : false;
        $elements = $elements != 'false' ? $elements : false;
        $links = $links != 'false' ? $links : false;
        $categories = $categories != 'false' ? $categories : false;
        $max = $max != 'false' ? (int) $max : false;
        $act = $act != 'false' ? (int) $act : false;
        $editor = $editor == 'false' ? false : $editor;
        
        // process
        $this->media(false, false, false, $max, $act, ['images' => $images, 'elements' => $elements, 'nodes' => $links, 'categories' => $categories, 'editor' => $editor]);
        
        // set
        $this->set('mode', 'button');
        $this->set('rel', $rel);
        
        // render
        $this->render('media');
        
    }

    function media($code, $theme, $block, $max, $act, $options = false){
        
        // init
        $filter = [];
        $selected = false;
        $images = $elements = $nodes = $categories = false;
        $settings = Configure::read('elements');
        $multi = false;
        $size = false;
        $sizes = false;
        $keys = [];
        $editor = false;
        $link_classes = Configure::read('editor.links');
        
        if(is_array($settings) && (is_array($options) || (array_key_exists($code, $settings) &&  array_key_exists('media', $settings[$code]) && array_key_exists($theme, $settings[$code]['media']) && array_key_exists($block, $settings[$code]['media'][$theme])))){
            
            // init
            if(is_array($options)){
                $config = $options;
            }else{
                $config = $settings[$code]['media'][$theme][$block];
            }
            $keys = [
                'image' => __d('be', 'Images'),
                'node' => __d('be', 'Nodes'),
                'category' => __d('be', 'Categories'),
            ];
            $multi = !is_int($max) || (int) $max > 1 ? true : false;
            $max = (int) $max > 0 ? (int) $max : false;
            $act = (int) $act > 0 ? (int) $act : false;
            
            // editor
            if(array_key_exists('editor', $config) && $config['editor'] == true){
                $editor = [
                    'content' => $config['editor'],
                    'options' => [],
                    'templates' => [],
                ];
            }
            
            // tables
            $categoriesTable = TableRegistry::get('Backend.Categories');
            
            // images?
            if(array_key_exists('images', $config) && $config['images']){

                // init
                $use_categories = Configure::read('images.use_categories');
                $selected = $selected === false ? 'image' : $selected;
                $purpose = (int) $config['images'] ? (int) $config['images'] : false;
                
                // register tables
                $imagesTable = TableRegistry::get('Backend.Images');
                
                // get categories
                $filter[$keys['image']] = ['all:image' => __d('be', 'All')];
                if($use_categories === true){
                    $query = $categoriesTable->find('threaded')->select(['id', 'parent_id', 'internal'])->where(['Categories.model =' => 'images', 'Categories.code =' => Configure::read('categories.code')])->order(['internal' => 'ASC']);
                    $query->hydrate(false);
                    $this->buildOptions($filter[$keys['image']], $query->toArray(), false);
                }else if($use_categories === false){
                    // do nothing ;)
                }else if(is_string($use_categories) && array_key_exists($use_categories, Configure::read('elements')) && Configure::read('elements.' . $use_categories . '.active')){
                    
                    // TODO: maybe make optgroups here ...
                    $_elements = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `code` = :code ORDER BY `sort`, `internal`", ['code' => $use_categories])->fetchAll('assoc');
        
                    if(is_array($_elements)){
                        foreach($_elements as $k => $v){
                             $filter[$keys['image']][$v['id']] = $v['internal'];
                        }
                    }
                    
                }
                
                // fetch images
                if($purpose){
                    $images = $imagesTable->find('translations')->where('FIND_IN_SET(' . $purpose . ', Images.purpose)')->order(['Images_title_translation.content' => 'ASC'])->toArray();
                }else{
                    $images = $imagesTable->find('translations')->order(['Images_title_translation.content' => 'ASC'])->toArray();
                }

                // sizes
                if($editor && $config['images'] == 'true'){
                    $sizes = ['original' => __d('be', 'Original')];
                    foreach(Configure::read('images.sizes.purposes') as $idx => $size){
                        if(!array_key_exists('editor', $size) || $size['editor'] === true){
                            $sizes[$idx] = $size['name'] . ' ('  . $size['width'] . 'x' . $size['height'] . ')';
                        }
                    }
                }
                $size = $purpose;
            }
            
            // elements?
            if(array_key_exists('elements', $config) && $config['elements']){
                
                // init
                $_keys = [];
                $codes = array_filter(explode("|", $config['elements']));
                
                // register tables
                $elementsTable = TableRegistry::get('Backend.Elements');
                
                // get categories
                foreach($codes as $c){
                    
                    if(strpos($c, ':') !== false){
                        list($c, $type) = explode(":", $c, 2);
                    }else{
                        $type = false;
                    }

                    if(array_key_exists($c, $settings) && $settings[$c]['active']){
                        
                        if($editor && array_key_exists('editor', $settings[$c]) && is_array($settings[$c]['editor'])){
                            if(array_key_exists('template', $settings[$c]['editor']) && $settings[$c]['editor']['template']){
                                $editor['templates'][$c] = $settings[$c]['editor']['template'];
                            }
                            if(array_key_exists('options', $settings[$c]['editor']) && is_array($settings[$c]['editor']['options'])){
                                $editor['options'][$c] = $settings[$c]['editor']['options'];
                            }
                        }
                        
                        $key = $settings[$c]['translations']['menu'];
                        
                        $add_to_filter = true;
                        if(!array_key_exists('use_categories', $settings[$c]) || $settings[$c]['use_categories'] === true){
                            $query = $categoriesTable->find('threaded')->select(['id', 'parent_id', 'internal'])->where(['Categories.model =' => 'elements', 'Categories.code =' => $c])->order(['internal' => 'ASC']);
                            $query->hydrate(false);
                            $_categories = $query->toArray();
                        }else if(array_key_exists('use_categories', $settings[$c]) && $settings[$c]['use_categories'] === false){
                            $_categories = [
                                [
                                    'id' => Configure::read('categories.code'),
                                    'parent_id' => '',
                                    'internal' => '',
                                    'children' => []
                                ]
                            ];
                            $add_to_filter = false;
                        }else if(array_key_exists('use_categories', $settings[$c]) && is_string($settings[$c]['use_categories']) && array_key_exists($settings[$c]['use_categories'], Configure::read('elements')) && Configure::read('elements.' . $settings[$c]['use_categories'] . '.active')){
                            
                            // TODO: maybe make optgroups here ...
                            $_elements = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `code` = :code ORDER BY `sort`, `internal`", ['code' => $settings[$c]['use_categories']])->fetchAll('assoc');
                
                            if(is_array($_elements)){
                                foreach($_elements as $k => $v){
                                    $_categories[] = [
                                        'id' => $v['id'],
                                        'parent_id' => '',
                                        'internal' => $v['internal'],
                                        'children' => []
                                    ];
                                }
                            }
                        }
                        
                        if(count($_categories) > 0){
                            
                            $ids = [];
                            $this->Selector->nestedIds($ids, $_categories, 'id', 'children');
                            
                            if(count($ids) > 0){
                            
                                // fetch elements
                                $_elements = $elementsTable->find('all')->where(["code" => $c, "Elements.category_id IN ('" . join("','", $ids) . "')"])->order(['internal' => 'ASC'])->toArray();

                                // type check?
                                if(is_array($_elements) && $c == 'special' && $type){
                                    foreach($_elements as $k => $v){
                                        $fields = json_decode($v->fields);
                                        if(!array_key_exists('type', $fields) || $fields->type != $type){
                                            unset($_elements[$k]);
                                        }
                                    }
                                }
                                
                                if(is_array($_elements) && count($_elements) > 0){
                                    
                                    if(!is_array($elements)){
                                        $elements = [];
                                    }
                                
                                    foreach($_elements as $k => $v){
                                        $elements[] = $v;
                                    }
                                    
                                    $filter[$key] = ['all:element:'.$c => __d('be', 'All')];
                                    $selected = $selected === false ? 'element:' . $c : $selected;
                                    $_keys['element:'.$c] = $key;
                                    if($add_to_filter){
                                        $this->buildOptions($filter[$key], $_categories, false);
                                        if(count($filter[$key]) == 1){
                                            unset($filter[$key]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                // add keys
                $keys = array_merge($keys, $_keys);
                
            }

            // nodes?
            if(array_key_exists('nodes', $config) && $config['nodes']){
                
                // nodes
                $nodes = $this->connection->execute("SELECT `n`.`id`, `n`.`foreign_id`, `n`.`structure_id`, `n`.`route`, `e`.`internal`, `e`.`code` FROM `nodes` as `n` LEFT JOIN `elements` as `e` ON (`n`.`foreign_id` = `e`.`id`) ORDER BY `e`.`internal`")->fetchAll('assoc');
                
                // skip nodes
                if(is_array($nodes)){
                    foreach($nodes as $k => $v){
                        if(array_key_exists($v['code'], $settings) && array_key_exists('linkable', $settings[$v['code']]) && $settings[$v['code']]['linkable'] === false){
                            unset($nodes[$k]);
                        }
                    }
                }
                
                // structures
                $structures = $this->connection->execute("SELECT `id`, `title` FROM `structures` ORDER BY `title`")->fetchAll('assoc');
                
                // nodes
                if(is_array($nodes) && count($nodes) > 0 && is_array($structures) && count($structures) > 0){
                    foreach($structures as $structure){
                        $selected = $selected === false ? 'node:' . $structure['id'] : $selected;
                        $filter[$keys['node']][$structure['id']] = $structure['title'];
                    }
                }
            }
            
            // categories?
            if(array_key_exists('categories', $config) && $config['categories']){
                $_infos = array_filter(explode("|", $config['categories']));
                foreach($_infos as $_c){
                    list($_model, $_code) = explode(":", $_c, 2);
                    $_categories = $this->getCategories($_model, $_code);
                    if(count($_categories) > 0){
                        if(!array_key_exists($keys['category'], $filter)){
                            $filter[$keys['category']] = [];
                        }
                        $selected = $selected === false ? 'category' : $selected;
                        $option = $_model == 'images' ? __d('be', 'Images') : Configure::read($_model . '.' . $_code . '.translations.menu');
                        $filter[$keys['category']]['category:' . $_c] = $option;
                        
                        if(!is_array($categories)){
                            $categories = [];
                        }
                        
                        foreach($_categories as $_id => $_title){
                            $categories[$_id] = [
                                'id' => $_id,
                                'title' => $_title,
                                'category' => 'category:' . $_c,
                            ];
                        }

                    }
                }
            }
        }
        
        $this->set('block', $block);
        $this->set('settings', $settings);
        $this->set('filter', $filter);
        $this->set('selected', $selected);
        $this->set('images', $images);
        $this->set('elements', $elements);
        $this->set('nodes', $nodes);
        $this->set('categories', $categories);
        $this->set('multi', $multi);
        $this->set('max', $max);
        $this->set('act', $act);
        $this->set('size', $size);
        $this->set('sizes', $sizes);
        $this->set('keys', $keys);
        $this->set('editor', $editor);
        $this->set('link_classes', $link_classes);
    }

    function error($type, $param = false){
        
        switch($type){
            case "max":
                $error = __d('be', 'Max. %s elements allowed!', $param);
                break;
            default:
                $error = __d('be', 'An error occured!');
                break;
        }
        
        $this->set('error', $error);
    }

    function preview(){

        // init
        $elements = [];
        $this->autoRender = false;
        $ret = ['success' => false, 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        // fetch preview
        if ($this->request->is('post')) {
            if(isset($this->request->data) && array_key_exists('rel', $this->request->data) && array_key_exists('elements', $this->request->data)){
                        
                // items
                $items = array_filter(explode(";", $this->request->data['elements']));

                if(count($items) > 0){                
                    foreach($items as $item){
                        if(strpos($item, ":") !== false){
                            list($type, $id) = explode(":", $item, 2);
                            if(in_array($type, ['image','element','node','category'])){
                                if($type == 'image'){
                                    if(strlen($id) == 36){
                                        $result = $this->connection->execute("SELECT `i`.`id`, CONCAT(`i`.`id`, '.', `i`.`extension`) as `filename`, `t`.`content` as `title`, `i`.`original` FROM `images` as `i` LEFT JOIN `i18n` as `t` ON (`i`.`id` = `t`.`foreign_key`) WHERE `i`.`id` = :id AND `t`.`locale` = :locale AND `t`.`field` = :field LIMIT 1", ['id' => $id, 'locale' => Configure::read('translation'), 'field' => 'title'])->fetch('assoc');
                                        if(is_array($result) && count($result) > 0){
                                            $elements[] = [
                                                'id' => $id,
                                                'type' => $type,
                                                'info' => $result
                                            ];
                                        }
                                    }
                                }else if($type == 'element'){
                                    if(strlen($id) == 36){
                                        $result = $this->connection->execute("SELECT `id`, `internal`, `code` FROM `elements` WHERE `id` = :id LIMIT 1", ['id' => $id])->fetch('assoc');
                                        if(is_array($result) && count($result) > 0){
                                            $settings = Configure::read('elements.' . $result['code']);
                                            if(is_array($settings) && $settings['active'] == true){
                                                $elements[] = [
                                                    'id' => $id,
                                                    'type' => $type,
                                                    'info' => $result,
                                                    'settings' => [
                                                        'name' => $settings['translations']['type'],
                                                        'icon' => $settings['icon'],
                                                    ],
                                                ];
                                            }
                                        }
                                    }
                                }else if($type == 'node'){
                                    if(strlen($id) == 36){
                                        $result = $this->connection->execute("SELECT `n`.`id`, `e`.`internal` FROM `nodes` as `n` LEFT JOIN `elements` as `e` ON (`n`.`foreign_id` = `e`.`id`) WHERE `n`.`id` = :id LIMIT 1", ['id' => $id])->fetch('assoc');
                                        if(is_array($result) && count($result) > 0){
                                            $elements[] = [
                                                'id' => $id,
                                                'type' => $type,
                                                'info' => $result,
                                                'settings' => [
                                                    'name' => __d('be', 'Node'),
                                                    'icon' => 'file-text-o',
                                                ],
                                            ];
                                        }
                                    }
                                }else if($type == 'category'){
                                    if(strlen($id) == 36){
                                        $result = $this->connection->execute("SELECT `id`, `internal` FROM `categories` WHERE `id` = :id LIMIT 1", ['id' => $id])->fetch('assoc');
                                        if(is_array($result) && count($result) > 0){
                                            $elements[] = [
                                                'id' => $id,
                                                'type' => $type,
                                                'info' => $result,
                                                'settings' => [
                                                    'name' => __d('be', 'Category'),
                                                    'icon' => 'folder-o',
                                                ],
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $ret = ['success' => true, 'rel' => $this->request->data['rel'], 'elements' => $elements];
            }
        }
        
        echo json_encode($ret);
        exit;
    }

    function anchors(){

        // init
        $anchors = [];
        $this->autoRender = false;
        $ret = ['success' => false, 'msg' => __d('be', 'An error has occurred, please try again!')];
        $settings = Configure::read('elements');
        
        // fetch preview
        if ($this->request->is('post')) {
            if(isset($this->request->data) && array_key_exists('id', $this->request->data) && array_key_exists('structure', $this->request->data)){
                
                // get anchors
                $node = $this->connection->execute("SELECT `foreign_id` FROM `nodes` WHERE `id` = :id", ['id' => $this->request->data['id']])->fetch('assoc');
                if(is_array($node) && count($node) > 0){
                    
                    // structure
                    $structure = $this->connection->execute("SELECT `title`, `theme` FROM `structures` WHERE `id` = :id", ['id' => $this->request->data['structure']])->fetch('assoc');
                    
                    if(is_array($structure) && count($structure) > 0){
                        
                        // element
                        $element = $this->connection->execute("SELECT `code`, `media` FROM `elements` WHERE `id` = :id", ['id' => $node['foreign_id']])->fetch('assoc');
                        if(is_array($element) && count($element) > 0 && array_key_exists($element['code'], $settings) && $settings[$element['code']]['active'] == true && array_key_exists('media', $settings[$element['code']])){
                            
                            // media
                            $media_settings = $settings[$element['code']]['media'];
                            
                            if(array_key_exists($structure['theme'], $media_settings)){
                                $media = !empty($element['media']) ? json_decode($element['media'], true) : [];
                                if(array_key_exists($structure['theme'], $media)){
                                    $ids = [];
                                    foreach($media[$structure['theme']] as $pos => $elements){
                                        if(array_key_exists($pos, $media_settings[$structure['theme']]) && (!array_key_exists('anchor', $media_settings[$structure['theme']][$pos]) || $media_settings[$structure['theme']][$pos]['anchor'] === true)){
                                            $anchors[$media_settings[$structure['theme']][$pos]['label']] = [];
                                            $elements = array_filter(explode(";", $elements));
                                            foreach($elements as $element){
                                                if(strpos($element, ":") !== false){
                                                    list($type, $id) = explode(":", $element, 2);
                                                    if($type == 'element' && strlen($id) == 36){
                                                        $ids[] = $id;
                                                        $anchors[$media_settings[$structure['theme']][$pos]['label']][$id] = false;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if(count($ids) > 0){
                                        $titles = [];
                                        $_titles = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `id` IN ('" . join("','", $ids) . "')")->fetchAll('assoc');
                                        if(is_array($_titles)){
                                            foreach($_titles as $title){
                                                $titles[$title['id']] = $title['internal'];
                                            }
                                        }
                                        if(count($titles)){
                                            foreach($anchors as $group => $options){
                                                foreach($options as $value => $options){
                                                    if(array_key_exists($value, $titles)){
                                                        $anchors[$group][$value] = $titles[$value];
                                                    }else{
                                                        unset($anchors[$group][$value]);
                                                    }
                                                }
                                            }
                                            $anchors = array_filter($anchors);
                                        }
                                    }else{
                                        $anchors = [];
                                    }
                                }
                            }
                        }
                    }
                }
                $ret = ['success' => true, 'anchors' => $anchors];
            }
        }
        echo json_encode($ret);
        exit;
    }

}
