<?php

namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Text;

class ElementsController extends AppController {

    public $paginate = [
        'limit' => 1,
        'order' => [
            'Elements.internal' => 'asc',
        ]
    ];
    
    public $skip = [
        '_method',
        'category_id',
        'code',
        'show_from',
        'show_to',
        'valid_times',
        'active',
        'internal',
        'sort',
        
    ];

    public function initialize()
    {
        
        parent::initialize();
        
        // pagination
        $this->paginate['limit'] = Configure::read('pagination.limit');
        $this->loadComponent('Paginator');

    }

    public function index($code, $category = false)
    {
        
        // settings
        if(array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active') ){
            $settings = Configure::read('elements.' . $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }
        
        // init
        $editable_categories = false;
        
        // sortable
        $sortable = array_key_exists('sortable', $settings) && $settings['sortable'] === true ? true : false;
        $global_sorting = $sortable === true && array_key_exists('global_sorting', $settings) && $settings['global_sorting'] === true ? true : false;
        $this->set('load_tablesorter', $sortable);

        // categories
        $categories = [];
        $categories_order = false;
        if(!array_key_exists('use_categories', $settings) || $settings['use_categories'] === true){
            $categories = $this->getCategories('elements', $code);
            if(count($categories) < 1){
                
                // error
                $this->Flash->error(__d('be', 'You need to create a category first!'));
                
                // redirect
                return $this->redirect(['controller' => 'categories', 'action' => 'update', 'elements', $code]);
                
            }else{

                // category
                $categoriesTable = TableRegistry::get('Backend.Categories');
                $category = $category === false || !array_key_exists($category, $categories) ? key($categories) : $category;
                
                // category infos
                $category = $categoriesTable->get($category);
                
                // editable
                $editable_categories = true;

            }
            $categories_order = count($categories) > 1 && array_key_exists('sort_categories', $settings) && $settings['sort_categories'] === true ? $this->getCategoriesOrder('elements', $code) : $categories_order;
        }else if(array_key_exists('use_categories', $settings) && $settings['use_categories'] === false){
            $category = ['id' => Configure::read('categories.code')];
        }else if(array_key_exists('use_categories', $settings) && is_string($settings['use_categories']) && array_key_exists($settings['use_categories'], Configure::read('elements')) && Configure::read('elements.' . $settings['use_categories'] . '.active')){
            
            // TODO: maybe make optgroups here ...
            $elements = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `code` = :code ORDER BY `sort`, `internal`", ['code' => $settings['use_categories']])->fetchAll('assoc');

            if(is_array($elements)){
                foreach($elements as $k => $v){
                    $categories[$v['id']] = $v['internal'];
                }
            }

            if(count($categories) < 1){
                
                // error
                $this->Flash->error(__d('be', 'You need to create a related element first!'));
                
                // redirect
                return $this->redirect(['controller' => 'elements', 'action' => 'index', $settings['use_categories']]);
                
            }else{

                // category infos
                $category = ['id' => $category === false || !array_key_exists($category, $categories) ? key($categories) : $category];

            }

        }else {

            // error
            $this->Flash->error(__d('be', 'Invalid categories setting for element!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }
        $this->set('category', $category);
        $this->set('categories', $categories);
        $this->set('categories_order', $categories_order);

        // global sorting
        $elements_order = $all_elements = [];
        if($global_sorting){
            $ae = $this->connection->execute("SELECT `id`, `internal`, `sort` FROM `elements` WHERE `code` = :code ORDER BY `sort`, `internal`", ['code' => $code])->fetchAll('assoc');
            foreach($ae as $e){
                $elements_order[$e['id']] = $e['sort'];
                $all_elements[$e['id']] = $e['internal'];
            }
        }
        $this->set('elements_order', $elements_order);
        $this->set('all_elements', $all_elements);
        
        // validation + translations
        $this->Elements->setup($settings, false);
        
        // fetch elements
        $query = $this->Elements
        ->find('translations')
        ->where(['code' => $code, 'category_id' => $category['id']])
        ->order(['sort' => 'ASC', 'internal' => 'ASC'])
        ->formatResults(function ($results) use($settings) {
            return $results->map(function ($row) use($settings) {
                return $this->Elements->afterFind($row, $settings);
            });
        });
        if($sortable === false){
            try {
                $elements = $this->paginate($query);
                $elements = $query->toArray();
            } catch (NotFoundException $e) {
                $elements = [];
            }
        }else{
            $elements = $query->toArray();
        }
        $this->set('elements', $elements);
        
        // media?
        $media = array_key_exists('media', $settings) && is_array($settings['media']) ? true : false;
        $this->set('media', $media);
        
        // element settings?
        $es = array_key_exists('settings', $settings) && is_array($settings['settings']) ? true : false;
        $this->set('es', $es);
        
        // menu
        $gmove = $editable_categories && count($elements) > 0 && count($categories) > 1 && __cp(['controller' => 'elements', 'action' => 'group', $code, 'move'], $this->request->session()->read('Auth')) ? true : false;
        $gdel = count($elements) > 0 && __cp(['controller' => 'elements', 'action' => 'group', $code, 'delete'], $this->request->session()->read('Auth')) ? true : false;
        $menu = [
            'left' => [
                [
                    'show' => count($categories) > 0 ? true : false,
                    'type' => 'select',
                    'name' => 'element_category',
                    'attr' => [
                        'options' => $categories,
                        'class' => 'dropdown',
                        'default' => $category['id'],
                        'escape' => false,
                    ],
                ],
                [
                    'show' => $editable_categories && count($categories) > 0 && __cp(['controller' => 'categories', 'action' => 'update', 'elements', $code, $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Edit category'),
                    'url' => ['controller' => 'categories', 'action' => 'update', 'elements', $code, $category['id']],
                    'icon' => 'pencil',
                ],
                [
                    'show' => $editable_categories && count($categories) > 0 && __cp(['controller' => 'categories', 'action' => 'delete', 'elements', $code, $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Delete category'),
                    'url' => ['controller' => 'categories', 'action' => 'delete', 'elements', $code, $category['id']],
                    'icon' => 'trash',
                    'confirm' => __d('be', 'Do you realy want to delete this category with all containing items?'),
                ],
                [
                    'show' => $editable_categories && count($categories) > 0 && __cp(['controller' => 'categories', 'action' => 'update', 'elements', $code], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Create new category'),
                    'url' => ['controller' => 'categories', 'action' => 'update', 'elements', $code],
                    'icon' => 'plus',
                ],
                [
                    'show' => is_array($categories_order) && count($categories) > 1 && __cp(['controller' => 'categories', 'action' => 'order', 'elements', $code, $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Sort categories'),
                    'url' => ['controller' => 'categories', 'action' => 'order', 'elements', $code, $category['id']],
                    'icon' => 'sort',
                    'class' => 'sort',
                ],
            ],
            'right' => [
                [
                    'show' => $gmove,
                    'type' => 'icon',
                    'text' => __d('be', 'Move selected elements'),
                    'url' => ['controller' => 'elements', 'action' => 'group', $code, 'move'],
                    'icon' => 'folder-open-o',
                    'class' => 'move group hidden',
                    'action' => 'select',
                    'select' => [
                        'name' => 'move_category',
                        'attr' => [
                            'options' => $categories,
                            'class' => 'dropdown',
                            'escape' => false,
                        ]
                    ]
                ],
                [
                    'show' => $gdel,
                    'type' => 'icon',
                    'text' => __d('be', 'Delete selected elements'),
                    'url' => ['controller' => 'elements', 'action' => 'group', $code, 'delete'],
                    'icon' => 'trash',
                    'class' => 'delete group hidden',
                ],
                [
                    'show' => $gmove || $gdel ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Reverse selection'),
                    'url' => ['controller' => 'elements', 'action' => 'index', $category['id']],
                    'icon' => 'check',
                    'class' => 'reverse-selection',
                ],
                [
                    'show' => count($elements) > 0 && array_key_exists('prices', $settings) && is_array($settings['prices']) && (!array_key_exists('per_element', $settings['prices']) || $settings['prices']['per_element'] === false) && __cp(['controller' => 'prices', 'action' => 'update', 'elements', $code], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Manage prices'),
                    'url' => ['controller' => 'prices', 'action' => 'update', 'elements', $code, $category['id']],
                    'icon' => 'euro',
                ],
                [
                    'show' => $sortable && $global_sorting && __cp(['controller' => 'categories', 'action' => 'order', 'elements', $code, $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'icon',
                    'text' => __d('be', 'Sort'),
                    'url' => ['controller' => 'elements', 'action' => 'order', $code],
                    'icon' => 'sort',
                    'class' => 'sort',
                ],
                [
                    'show' => __cp(['controller' => 'elements', 'action' => 'update', $code, $category['id']], $this->request->session()->read('Auth')) ? true : false,
                    'type' => 'link',
                    'text' => $settings['translations']['buttons']['add'],
                    'url' => ['controller' => 'elements', 'action' => 'update', $code, $category['id']],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', $settings['translations']['menu']);
        $this->set('menu', $menu);
        $this->set('code', $code);
        $this->set('settings', $settings);
        $this->set('sortable', $sortable);
        $this->set('global_sorting', $global_sorting);

    }

    function update($code, $category, $id = false){
        
        // settings
        if(array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active') ){
            $settings = Configure::read('elements.' . $code);
            $this->set('code', $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }
        
        // validation + translations
        $this->Elements->setup($settings, $id);

        // set locale
        $locale = array_key_exists('locale', $this->request->query) && array_key_exists($this->request->query['locale'], Configure::read('translations')) && Configure::read('translations.' . $this->request->query['locale'] . '.active') ? $this->request->query['locale'] : Configure::read('translation');
        $this->Elements->locale($locale);

        // fieldsets
        $fieldsets = [];
        foreach($settings['fields'] as $field){
            if(array_key_exists('fieldset', $field) && $field['fieldset'] && !in_array($field['fieldset'], $fieldsets)){
                $fieldsets[] = $field['fieldset'];
            }
        }
        $this->set('fieldsets', $fieldsets);
        
        // save
        if($id){
            $element = $this->Elements
            ->find('translations')
            ->where(['Elements.id' => $id])
            ->formatResults(function ($results) use($settings) {
                return $results->map(function ($row) use($settings) {
                    return $this->Elements->afterFind($row, $settings);
                });
            })
            ->first();
        }else{
            $element = $this->Elements->newEntity();
        }
        if ($this->request->is(['post', 'put'])) {
            
            // remove unneeded values
            if(array_key_exists('dynamic', $settings) && is_array($settings['dynamic']) && array_key_exists('depends', $settings['dynamic']) && array_key_exists('fields', $settings['dynamic'])){
                $depends = array_key_exists($settings['dynamic']['depends'], $this->request->data) ? $this->request->data[$settings['dynamic']['depends']] : false;
                foreach($this->request->data as $k => $v){
                    if(!in_array($k, array_merge($this->skip,[$settings['dynamic']['depends']]))){
                        if(!array_key_exists($depends, $settings['dynamic']['fields']) || !in_array($k, $settings['dynamic']['fields'][$depends])){
                            unset($this->request->data[$k]);
                        }
                    }
                }
            }
            
            $this->Elements->patchEntity($element, $this->request->data);
            if ($result = $this->Elements->save($element)) {
                $this->Flash->success($id ? __d('be', 'The record has been updated.') : __d('be', 'The record has been created.'));
                if($this->request->params['redirect']){
                    return $this->redirect(['action' => 'index', $code, $category]);
                }else{
                    return $this->redirect(['action' => 'update', $code, $category, $result->id, '?' => ['locale' => $locale]]);
                }
            }else{
                $this->Flash->error(__d('be', 'Unable to save record!'));
            }
        }
        $this->set('element', $element);
        $this->set('id', $id);
        
        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'translations',
                    'show' => $id === false || !isset($element['_translations']) || count($element['_translations']) == 0 ? false : true,
                    'active' => $locale,
                ]
            ],
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back to overview'),
                    'url' => ['action' => 'index', $code, $category],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', $id ? $settings['translations']['title']['edit'] : $settings['translations']['title']['new']);
        $this->set('settings', $settings);
        $this->set('skip', $this->skip);
        $this->set('category', $category);
        $this->set('url', $this->Elements->url);
        $this->set('menu', $menu);
        
    }

    function settings($code, $category, $element_id){
        
        // settings
        if(array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active') && array_key_exists('settings', Configure::read('elements.' . $code)) && is_array(Configure::read('elements.' . $code . '.settings')) ){
            $settings = Configure::read('elements.' . $code);
            $this->set('code', $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }
        
        // validation + translations
        $this->Elements->setup($settings, $element_id);

        // set locale
        $locale = array_key_exists('locale', $this->request->query) && array_key_exists($this->request->query['locale'], Configure::read('translations')) && Configure::read('translations.' . $this->request->query['locale'] . '.active') ? $this->request->query['locale'] : Configure::read('translation');
        $this->Elements->locale($locale);

        // fieldsets
        $fieldsets = [];
        foreach($settings['settings']['fields'] as $field){
            if(array_key_exists('fieldset', $field) && $field['fieldset'] && !in_array($field['fieldset'], $fieldsets)){
                $fieldsets[] = $field['fieldset'];
            }
        }
        $this->set('fieldsets', $fieldsets);

        // element
        $element = $this->Elements
        ->find('translations')
        ->where(['Elements.id' => $element_id])
        ->formatResults(function ($results) use($settings) {
            return $results->map(function ($row) use($settings) {
                return $this->Elements->afterFind($row, $settings);
            });
        })
        ->first();
        
        // selection
        $selections = [];
        if(isset($element->{$settings['settings']['selection']}) && !empty($element->{$settings['settings']['selection']})){
            $_selections = $this->Elements->infos($element->{$settings['settings']['selection']});
            foreach($_selections as $k => $v){
                $selections[$v['type'].':'.$v['infos']['id']] = $v['infos']['title'];
            }
        }
        $selection = count($selections) > 0 ? key($selections) : false;
        $selection = array_key_exists('s1', $this->request->query) && array_key_exists($this->request->query['s1'], $selections) ? $this->request->query['s1'] : $selection;
        
        // subselection
        $subselections = [];
        if(isset($element->{$settings['settings']['subselection']}) && !empty($element->{$settings['settings']['subselection']})){
            $_subselections = $this->Elements->infos($element->{$settings['settings']['subselection']});
            foreach($_subselections as $k => $v){
                $subselections[$v['type'].':'.$v['infos']['id']] = $v['infos']['title'];
            }
        }
        $subselection = count($subselections) > 0 ? key($subselections) : false;
        $subselection = array_key_exists('s2', $this->request->query) && array_key_exists($this->request->query['s2'], $subselections) ? $this->request->query['s2'] : $subselection;
        
        // save
        $id = false;
        $check = $this->connection->execute("SELECT `id` FROM `settings` WHERE `foreign_id` = :id AND `selection` = :selection AND `subselection` = :subselection", ['id' => $element_id, 'selection' => $selection, 'subselection' => $subselection])->fetch('assoc');
        if(is_array($check) && count($check) > 0){
            $id = $check['id'];
        }

        $settingsTable = TableRegistry::get('Backend.Settings');
        $settingsTable->setup($settings, $id, ['selection' => $selection, 'subselection' => $subselection]);
        
        if($id){
            $element_settings = $settingsTable
            ->find('translations')
            ->where(['Settings.id' => $id])
            ->formatResults(function ($results) use($settings, $settingsTable) {
                return $results->map(function ($row) use($settings, $settingsTable) {
                    return $settingsTable->afterFind($row, $settings);
                });
            })
            ->first();
        }else{
            $element_settings = $settingsTable->newEntity();
        }
        
        if ($this->request->is(['post', 'put'])) {
            $settingsTable->patchEntity($element_settings, $this->request->data);
            if ($result = $settingsTable->save($element_settings)) {
                $this->Flash->success(__d('be', 'The settings have been saved.'));
                return $this->redirect(['action' => 'settings', $code, $category, $element_id, '?' => ['s1' => $selection, 's2' => $subselection]]);
            }else{
                $this->Flash->error(__d('be', 'Unable to save record!'));
            }
        }

        $this->set('element', $element);
        $this->set('element_id', $element_id);
        
        $this->set('element_settings', $element_settings);
        $this->set('id', $id);

        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'select',
                    'name' => 'selection',
                    'attr' => [
                        'options' => $selections,
                        'class' => 'dropdown',
                        'default' => $selection,
                        'escape' => false,
                    ],
                ],
                [
                    'type' => 'select',
                    'name' => 'subselection',
                    'attr' => [
                        'options' => $subselections,
                        'class' => 'dropdown',
                        'default' => $subselection,
                        'escape' => false,
                    ],
                ],
            ],
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back to overview'),
                    'url' => ['action' => 'index', $code, $category],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];

        $this->set('title', __d('be', 'Settings') . ' <span>' . $element->internal . '</span>');
        $this->set('settings', $settings);
        $this->set('selection', $selection);
        $this->set('subselection', $subselection);
        $this->set('category', $category);
        $this->set('code', $code);
        $this->set('menu', $menu);
        $this->set('load_editor', true);
        $this->set('load_datepicker', true);
        
    }

    public function copy($code, $category, $id)
    {
        
        // settings
        if(array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active')){
            $settings = Configure::read('elements.' . $code);
        }else{
            
            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
        }
        
        // validation + translations
        $this->Elements->setup($settings, $id);

        // init
        $uuid = Text::uuid();
        $success = true;
        
        // copy
        try {
            
            foreach(Configure::read('translations') as $locale => $info){
                $this->Elements->locale($locale);
                $entity = $this->Elements->get($id);
                $entity->id = $uuid;
                $entity->internal .= ' (' . __d('be', 'Copy') . ')';
                
                // fields
                $fields = json_decode($entity['fields'], true);
                if(is_array($fields)){
                    foreach($fields as $field => $value){
                        $entity[$field] = $value;
                    }
                }
                
                $element = $this->Elements->newEntity($entity->toArray());
                if (!$this->Elements->save($element)) {
                    $success = false;
                }
            }
            
            // copy prices
            if($success === true && array_key_exists('prices', $settings) && is_array($settings['prices'])){
                $prices = $this->connection->execute("SELECT * FROM `prices` WHERE `foreign_id` = :id", ['id' => $id])->fetchAll('assoc');
                if(is_array($prices) && count($prices) > 0){
                    foreach($prices as $price){
                        $this->connection->execute("INSERT INTO `prices` (`id`, `foreign_id`, `foreign_model`, `foreign_code`, `season_id`, `price_draft_id`, `option`, `element`, `value`, `flag`) VALUES (:id, :foreign_id, :foreign_model, :foreign_code, :season, :draft, :option, :element, :value, :flag)", [
                            'id' => Text::uuid(),
                            'foreign_id' => $uuid,
                            'foreign_model' => $price['foreign_model'],
                            'foreign_code' => $price['foreign_code'],
                            'season' => $price['season_id'],
                            'draft' => $price['price_draft_id'],
                            'option' => $price['option'],
                            'element' => $price['element'],
                            'value' => $price['value'],
                            'flag' => $price['flag'],
                        ]);
                    }
                }
            }

            // copy settings
            if($success === true && array_key_exists('settings', $settings) && is_array($settings['settings'])){
                $element_settings = $this->connection->execute("SELECT * FROM `settings` WHERE `foreign_id` = :id", ['id' => $id])->fetchAll('assoc');
                if(is_array($element_settings) && count($element_settings) > 0){
                    foreach($element_settings as $element_setting){
                        
                        // uuid
                        $suuid = Text::uuid();
                        
                        // translations
                        $setting_translations = $this->connection->execute("SELECT * FROM `i18n` WHERE `foreign_key` = :id", ['id' => $element_setting['id']])->fetchAll('assoc');
                        if(is_array($setting_translations) && count($setting_translations) > 0){
                            foreach($setting_translations as $setting_translation){
                                $this->connection->execute("INSERT INTO `i18n` (`locale`, `model`, `foreign_key`, `field`, `content`) VALUES (:locale, :model, :foreign_key, :field, :content)", [
                                    'locale' => $setting_translation['locale'],
                                    'model' => $setting_translation['model'],
                                    'foreign_key' => $suuid,
                                    'field' => $setting_translation['field'],
                                    'content' => $setting_translation['content'],
                                ]);
                            }
                        }
                        
                        // settings
                        $this->connection->execute("INSERT INTO `settings` (`id`, `foreign_id`, `selection`, `subselection`, `settings`) VALUES (:id, :foreign_id, :selection, :subselection, :settings)", [
                            'id' => $suuid,
                            'foreign_id' => $uuid,
                            'selection' => $element_setting['selection'],
                            'subselection' => $element_setting['subselection'],
                            'settings' => $element_setting['settings'],
                        ]);
                        
                    }
                }
            }
            
        } catch (RecordNotFoundException $e) {
            $success = false;
        }

        // message
        if($success){
            $this->Flash->success($settings['translations']['flash']['copy']['success']);
        }
            
        // redirect
        return $this->redirect(['action' => 'index', $code, $category]);

    }
    
    public function order($code)
    {
        
        // init
        $this->autoRender = false;
        $ret = ['success' => false, 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        if ($this->request->is(['post', 'put']) && array_key_exists('order', $this->request->data)) {
            foreach($this->request->data['order'] as $pos => $id){
                $this->connection->execute("UPDATE `elements` SET `sort` = :pos WHERE `id` = :id", ['pos' => $pos, 'id' => $id]);
            }
            $ret['success'] = true;
        }
        
        echo json_encode($ret);
        exit;
    }
    
    public function delete($code, $category, $id, $ajax = false){
    
        // settings
        if(array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active')){
            $settings = Configure::read('elements.' . $code);
        }else{

            if($ajax === false){

                // error
                $this->Flash->error(__d('be', 'Invalid request!'));
                
                // redirect
                return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
            
            }else{
                return false;
            }

        }
        
        // check
        if($used = $this->isUsed($id)){
            if($ajax === false){
                $success = false;
                $this->Flash->error(__d('be', 'The element could not be deleted because it is in use!') . "<br /><ul><li>" . join("</li><li>", $used) . "</li></ul>");
            }else{
                return false;
            }
        }else{

            // validation + translations
            $this->Elements->setup($settings, $id);
        
            // init
            $success = false;
            if($ajax){
                $this->autoRender = false;
            }
            
            // delte
            try {
                $entity = $this->Elements->get($id);
                if($this->Elements->delete($entity)){
                    $success = true;
                }
            } catch (RecordNotFoundException $e) { }
    
            // message
            if($ajax === false){
                if($success){
                    $this->Flash->success($settings['translations']['flash']['delete']['success']);
                }else{
                    if(isset($entity['_in_structure']) && $entity['_in_structure']){
                        $this->Flash->error(__d('be', 'Element can not be deleted as long as it is used in a structure!'));
                    }else{
                        $this->Flash->error($settings['translations']['flash']['delete']['error']);
                    }
                }
            }
        
        }
            
        // redirect
        return $ajax ? $success : $this->redirect(['action' => 'index', $code, $category]);
        
    }

    public function group($code, $action){
        
        // init
        $success = true;
        $this->autoRender = false;
        $ret = ['success' => false, 'action' => $action, 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        // save
        if ($this->request->is('ajax') && $this->request->is('post') && array_key_exists('code', $this->request->data) && array_key_exists('elements', $this->request->data)) {
            if($action == 'move' && array_key_exists('category', $this->request->data)){
                
                // move
                $query = $this->Elements->query();
                $success = $query->update()
                    ->set(['category_id' => $this->request->data['category']])
                    ->where(function ($exp, $q) {
                        return $exp->in('id', $this->request->data['elements']);
                    })
                    ->execute();
                if($success == true){
                    $this->Flash->success(__d('be', 'The elements have been successfully moved!'));
                    $ret = ['success' => true, 'action' => $action];
                }
                
            }else if($action == 'delete'){

                // check
                $used = [];
                $_used = $this->isUsed($this->request->data['elements']);
                if(is_array($_used) && count($_used) > 0){
                    foreach($_used as $k => $v){
                        list($c, $i) = explode(":", $k , 2);
                        $used[$i] = true;
                    }
                }
                $ret['used'] = $used;
                
                foreach($this->request->data['elements'] as $element){
                    if(!is_array($ret['used']) || !array_key_exists($element, $ret['used'])){
                        if(!$this->delete($this->request->data['code'], false, $element, true)){
                            $success = false;
                        }
                    }
                }
                
                if($success == true){
                    if($ret['used']){
                        $this->Flash->error(__d('be', 'Some elements could not be deleted because they are in use!'));
                    }else{
                        $this->Flash->success(__d('be', 'The elements have been successfully removed!'));
                    }
                    $ret = ['success' => true, 'action' => $action];
                }
            }
        }
        
        echo json_encode($ret);
        exit;
    }

    function media($code, $category, $id, $theme = false){

        // settings
        if(array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active') ){
            $settings = Configure::read('elements.' . $code);
            $this->set('code', $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }
        
        // media?
        if(!array_key_exists('media', $settings) || !is_array($settings['media'])){

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['action' => 'index', $code, $category]);

        }
        
        // validation + translations
        $this->Elements->setup($settings, $id);
        
        // check id
        $element = $this->Elements
        ->find('all')
        ->where(['Elements.id' => $id])
        ->formatResults(function ($results) use($settings) {
            return $results->map(function ($row) use($settings) {
                return $this->Elements->afterFind($row, $settings);
            });
        })
        ->first();
        if(!is_object($element) || count($element) == 0){

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['action' => 'index', $code, $category]);

        }

        // save
        if ($this->request->is(['post', 'put'])) {
            
            // media
            $media = !is_array($element['media']) ? [] : $element['media'];
            foreach($this->request->data['media'] as $theme => $blocks){
                $media[$theme] = $blocks;
            }
            
            $this->Elements->patchEntity($element, ['media' => json_encode($media)]);
            if ($result = $this->Elements->save($element)) {
                $this->Flash->success(__d('be', 'The media blocks have been saved.'));
                if($this->request->params['redirect']){
                    return $this->redirect(['action' => 'index', $code, $category]);
                }else{
                    return $this->redirect(['action' => 'media', $code, $category, $id, $theme]);
                }
            }else{
                $this->Flash->error(__d('be', 'Unable to save record!'));
            }
        }
        $this->set('element', $element);
        $this->set('id', $id);
        
        // themes
        $themes = Configure::read('themes');
        if(is_array($themes) && count($themes) > 0){
            $theme = $theme && array_key_exists($theme, $themes) ? $theme : key($themes);
        }
        $this->set('themes', $themes);
        $this->set('theme', $theme);
        
        // infos
        $infos = [];
        if(is_array($element->media)){
            foreach($element->media as $_theme => $_blocks){
                $infos[$_theme] = [];
                foreach($_blocks as $_block => $_media){
                    $infos[$_theme][$_block] = $this->Elements->infos($_media);
                }
            }
        }
        $this->set('infos', $infos);
        
        // menu
        $menu = [
            'left' => [
                [
                    'show' => count($themes) > 1 ? true : false,
                    'type' => 'select',
                    'name' => 'theme_dropdown',
                    'attr' => [
                        'options' => $themes,
                        'class' => 'dropdown',
                        'default' => $theme,
                        'escape' => false,
                        'value' => $theme
                    ],
                ],
            ],
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back to overview'),
                    'url' => ['action' => 'index', $code, $category],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', $element['internal'] . ' <span>' . __d('be', 'Media') . '</span>');
        $this->set('settings', $settings);
        $this->set('code', $code);
        $this->set('category', $category);
        $this->set('menu', $menu);
    }

}
