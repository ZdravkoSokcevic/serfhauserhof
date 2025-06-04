<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

class NodesTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {

        return $validator
            ->notEmpty('structure_id', 'structure_id')
            ->notEmpty('foreign_id', 'foreign_id')
            ->notEmpty('route', 'route');
    }

    public function validationSettings(Validator $validator)
    {

        if(is_array(Configure::read('node-settings'))){

            // validation
            foreach(Configure::read('node-settings') as $name => $info){
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

    public function afterFind($row){

        // json
        $settings = !empty($row->settings) ? json_decode($row->settings, true) : [];

        foreach($settings as $k => $v){
            $row->{$k} = $v;
        }

        return $row;
    }

    public function beforeSave($event, $entity, $options){
        if(is_array(Configure::read('node-settings')) && $entity->get('_mode') == 'settings'){

            // init
            $info = [];

            // json
            foreach(Configure::read('node-settings') as $_name => $_info){
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

        }
    }

    public function afterDelete($event, $entity, $options){
        $this->__deleteChildren($entity->id);
    }

    private function __deleteChildren($parent_id) {
        $children = $this->findAllByParentId($parent_id);
        if (!empty($children)) {
            foreach ($children as $child) {
                $this->delete($child);
            }
        }
    }

    public function createRoute(){

        // init
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $skip = ['sex','kkk'];
        $charlslength = strlen($chars);

        // connection
        $connection = ConnectionManager::get('default');

        // create route
        do {

            $route = '';
            for ($i = 0; $i < 3; $i++) {
                $route .= $chars[rand(0, $charlslength - 1)];
            }

            // check
            $result = $connection->execute("SELECT `id` FROM `nodes` WHERE `route` = :route LIMIT 1", ['route' => $route])->fetch('assoc');
            if(is_array($result) && count($result) > 0){
                $route = '';
            }

        } while (empty($route));

        return $route;
    }

    public function buildTreeJSON(&$json, $nodes){

        // connection
        $connection = ConnectionManager::get('default');

        // settings
        $settings = Configure::read('elements');

        foreach($nodes as $k => $v){

            // element
            $element = $connection->execute("SELECT `internal`, `code` FROM `elements` WHERE `id` = :id LIMIT 1", ['id' => $v['foreign_id']])->fetch('assoc');

            if(array_key_exists($element['code'], $settings)){

                // type
                $type = $settings[$element['code']]['translations']['type'];

                $node = [
                    'title' => is_array($element) && count($element) > 0 ? $element['internal'] : 'MISSING',
                    'info' => [
                        'id' => $v['id'],
                        'foreign_id' => $v['foreign_id'],
                        'popup' => $v['popup'] ? true : false,
                        'active' => $v['active'] ? true : false,
                        'jump' => $v['jump'] ? true : false,
                        'follow' => $v['robots_follow'] ? true : false,
                        'index' => $v['robots_index'] ? true : false,
                        'from' => $v['show_from'],
                        'to' => $v['show_to'],
                        'display' => $v['display'] ? true : false,
                        'route' => $v['route'],
                        'missing' => is_array($element) && count($element) > 0 ? false : true,
                    ],
                    'element' => array_merge($element, ['type' => $type]),
                    'linkable' => array_key_exists('linkable', $settings[$element['code']]) ? $settings[$element['code']]['linkable'] : true,
                    'type' => 'load',
                    'children' => []
                ];

                if(count($v['children']) > 0){
                    $this->buildTreeJSON($node['children'], $v['children']);
                }

                $json[] = $node;
            }
        }

    }

}
