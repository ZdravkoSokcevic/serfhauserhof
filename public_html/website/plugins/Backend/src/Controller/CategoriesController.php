<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;

class CategoriesController extends AppController {

    public $settings = [];

    public function initialize()
    {
        
        parent::initialize();
        
        // initial settings
        $this->settings = [
            'images' => [
                'title' => __d('be', 'Image'),
                'infos' => [
                    'rel' => false,
                ],
                'translations' => [
                    'title' => false,
                    'content' => false,
                    'seo' => true,
                ],
                'fields' => [
                    'special' => false,
                ],
                'validate' => 'image',
                'code' => false,
            ],
            'elements' => [
                'title' => __d('be', 'Elements'),
                'infos' => [
                    'rel' => false,
                ],
                'translations' => [ // overwrite this settings for each element in elements.php
                    'title' => false,
                    'content' => false,
                    'seo' => false,
                ],
                'fields' => [
                    'special' => false,
                ],
                'code' => true,
            ],
        ];

    }

    public function update($model = false, $code = false, $id = false) {
        
        // check model
        if(!array_key_exists($model, $this->settings)){
            
            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
            
        }
        
        // set locale
        $locale = array_key_exists('locale', $this->request->query) && array_key_exists($this->request->query['locale'], Configure::read('translations')) && Configure::read('translations.' . $this->request->query['locale'] . '.active') ? $this->request->query['locale'] : Configure::read('translation');
        $this->Categories->locale($locale);

        // get other categories
        $categories = $this->getCategories($model, $code, $id);
        $this->set('categories', $categories);

        // settings
        $settings = $this->settings[$model];
        if($model == 'elements' && $code){
            $overrule = Configure::read('elements.' . $code . '.categories');
            if(is_array($overrule)){
                foreach($overrule as $k => $v){
                    if(array_key_exists($k, $settings['translations'])){
                        $settings['translations'][$k] = $v;
                    }else if(array_key_exists($k, $settings['fields'])){
                        $settings['fields'][$k] = $v;
                    }else if(array_key_exists($k, $settings['infos'])){
                        $settings['infos'][$k] = $v;
                    }
                }
            }
        }

        // save
        $category = $id ? $this->Categories->get($id) : $this->Categories->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $this->Categories->patchEntity($category, $this->request->data, ['validate' => array_key_exists('validate', $this->settings[$model]) ? $this->settings[$model]['validate'] : 'default']);
            if ($result = $this->Categories->save($category)) {
                $this->Flash->success($id ? __d('be', 'The category has been updated.') : __d('be', 'The category has been created.'));
                if($this->request->params['redirect']){
                    return $this->redirect(['controller' => $model, 'action' => 'index', $this->settings[$model]['code'] ? $code : false, $result->id]);
                }else{
                    return $this->redirect(['action' => 'update', $model, $code, $result->id, '?' => ['locale' => $locale]]);
                }
            }else{
                $this->Flash->error(__d('be', 'Unable to save category!'));
            }
        }
        
        $this->set('category', $category);
        $this->set('model', $model);
        $this->set('code', $code ? $code : Configure::read('categories.code'));
        $this->set('settings', $settings);
        $this->set('id', $id);
        
        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'translations',
                    'show' => $id === false || !in_array(true, $settings['translations'], true) ? false : true,
                    'active' => $locale,
                ]
            ],
            'right' => [
                [
                    'type' => 'icon',
                    'show' => $id === false ? false : true,
                    'text' => __d('be', 'Create new category'),
                    'url' => ['controller' => 'categories', 'action' => 'update', $model, $code],
                    'icon' => 'plus',
                ],
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back'),
                    'url' => ['controller' => $model, 'action' => 'index', $settings['code'] ? $code : false, $id],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', $id ? __d('be', 'Update category') : __d('be', 'Create category'));
        $this->set('menu', $menu);
        
    }

    public function delete($model, $code, $id, $nested = false) {

        // init       
        $success = false; 
        $this->autoRender = $nested;
        
        try{
            $category = $this->Categories->get($id);
        }catch(RecordNotFoundException $e){
            $category = false;
        }
        
        if($category){
 
            // check
            if($used = $this->isUsed($id, $model)){
                if($nested){
                    $success = false;
                }else{
                    $this->Flash->error(__d('be', 'Category could not be deleted because it contains elements that are in use!'));
                }
            }else{
            
                // init
                $linkedTable = TableRegistry::get('Backend.' . ucfirst($model));
                
                if($this->Categories->delete($category)){
                    
                    // delete stuff in linked model
                    $linkedTable->_category($id, $code);
                    
                    try{
                        $categories = $this->Categories->find('list', ['keyField' => 'id', 'valueField' => 'internal'])->where(['parent_id' => $id])->all()->toArray();
                    }catch(RecordNotFoundException $e){
                        $categories = [];
                    }
                    
                    if(count($categories) > 0){
                        foreach($categories as $cid => $info){
                            $this->delete($model, $code, $cid, true);
                        }
                    }
                    
                    if($nested){
                        $success = true;
                    }else{
                        $success = true;
                        $this->Flash->success(__d('be', 'The category has been successfully removed!'));
                    }
                
                }else{
                    if($nested){
                        $success = false;
                    }else{
                        $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
                    }
                }
            
            }
        }else{
            if($nested){
                $success = false;
            }else{
                $this->Flash->error(__d('be', 'Invalid request!'));
            }
        }
        
        $id = $success === true ? $category['parent_id'] : $id;
        return $nested ? $success : $this->redirect(['controller' => $model, 'action' => 'index', $this->settings[$model]['code'] ? $code : false, $id]);
        
    }
    
    public function order($model, $code, $category) {
        
        // init
        $this->autoRender = false;
        $ret = ['success' => false, 'order' => [], 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        // save
        if ($this->request->is('ajax') && $this->request->is('post')) {
            if(isset($this->request->data) && array_key_exists('order', $this->request->data) && count($this->request->data['order']) > 0){
                foreach($this->request->data['order'] as $id => $sort){
                    $this->connection->execute("UPDATE `categories` SET `sort` = :sort WHERE `id` = :id", ['sort' => $sort, 'id' => $id]);
                }
                $ret['success'] = true;
                $ret['order'] = $this->request->data['order'];
            }
        }
        
        echo json_encode($ret);
        exit;
    }

}
