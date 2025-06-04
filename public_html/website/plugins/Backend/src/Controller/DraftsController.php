<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;

class DraftsController extends AppController {

    public function update($model, $code, $category, $season = false, $id = false, $related = false) {

        // settings
        if(Configure::read($model) && array_key_exists($code, Configure::read($model)) && Configure::read($model . '.' . $code . '.active') ){
            $settings = Configure::read($model . '.' . $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }

        // check category / prices "needed"?
        $categories = $this->getCategories($model, $code);
        if(!is_array($categories) || !array_key_exists($category, $categories) || !array_key_exists('prices', $settings) || !is_array($settings['prices'])){
            
            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => $model, 'action' => 'index', $code]);
            
        }
        
        // check id
        $id = strlen($id) == 36 ? $id : false;
        
        // check seasons
        if(!array_key_exists('prices', $settings) || !is_array($settings['prices']) || !array_key_exists('seasons', $settings['prices']) || !is_array($settings['prices']['seasons']) || !array_key_exists('active', $settings['prices']['seasons']) || $settings['prices']['seasons']['active'] !== true){
            $related_seasons = false;
            $season = 'false';
        }else{
            $related_seasons = array_key_exists('rel', $settings['prices']['seasons']) && is_string($settings['prices']['seasons']['rel']) ? $settings['prices']['seasons']['rel'] : false;
            $seasons = $this->getSeasons($related_seasons ? $related_seasons : $code);
            if(count($seasons) < 1 || !array_key_exists($season, $seasons)){
                
                // error
                if($related_seasons){
                    $this->Flash->error(__d('be', 'You need to create a related season first!'));
                }else{
                    $this->Flash->error(__d('be', 'You need to create a season first!'));
                }
                
                // redirect
                return $this->redirect(['controller' => 'seasons', 'action' => 'update', $model, $related_seasons ? $related_seasons : $code, $category]);
                
            }
        }
        
        // set locale
        $locale = array_key_exists('locale', $this->request->query) && array_key_exists($this->request->query['locale'], Configure::read('translations')) && Configure::read('translations.' . $this->request->query['locale'] . '.active') ? $this->request->query['locale'] : Configure::read('translation');
        $this->Drafts->locale($locale);
        
        // save
        $draft = $id ? $this->Drafts->get($id, ['finder' => 'translations']) : $this->Drafts->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $this->Drafts->patchEntity($draft, $this->request->data);
            if ($result = $this->Drafts->save($draft)) {
                $this->Flash->success($id ? __d('be', 'The record has been updated.') : __d('be', 'The record has been created.'));
                if($this->request->params['redirect']){
                    return $this->redirect(['controller' => 'prices', 'action' => 'update', $model, $code, $category, $season, $related]);
                }else{
                    return $this->redirect(['action' => 'update', $model, $code, $category, $season, $result->id, $related, '?' => ['locale' => $locale]]);
                }
            }else{
                $this->Flash->error(__d('be', 'Unable to save record!'));
            }
        }
        $this->set('draft', $draft);
        $this->set('id', $id);
        
        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'translations',
                    'show' => $id === false || !isset($draft['_translations']) || count($draft['_translations']) == 0 ? false : true,
                    'active' => $locale,
                ]
            ],
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back'),
                    'url' => ['controller' => 'prices', 'action' => 'update', $model, $code, $category, $season ? $season : 'false', $related],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', __d('be', 'Price draft') . ' <span>' . $settings['translations']['menu'] . '</span>');
        $this->set('menu', $menu);
        $this->set('code', $code);
        $this->set('model', $model);
        $this->set('category', $category);
        $this->set('season', $season);
        $this->set('settings', $settings);

    }

    public function delete($model, $code, $category, $season = false, $id = false, $related = false) {
    
        // settings
        if(Configure::read($model) && array_key_exists($code, Configure::read($model)) && Configure::read($model . '.' . $code . '.active') ){
            $settings = Configure::read($model . '.' . $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
            
        }

        // init
        $success = false;
        $id = strlen($id) == 36 ? $id : false;

        // delte
        try {
            $entity = $this->Drafts->get($id);
            if($this->Drafts->delete($entity)){
                $success = true;
            }
        } catch (RecordNotFoundException $e) { }

        // message
        if($success){
            $this->Flash->success(__d('be', 'The price draft has been successfully removed!'));
        }else{
            $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
        }
            
        // redirect
        return $this->redirect(['controller' => 'prices', 'action' => 'update', $model, $code, $category, $season ? $season : 'false', $related]);
        
    }
    
    public function order($model, $code, $category, $season = false, $id = false) {
        
        // init
        $this->autoRender = false;
        $ret = ['success' => false, 'order' => [], 'msg' => __d('be', 'An error has occurred, please try again!')];
        
        // save
        if ($this->request->is('ajax') && $this->request->is('post')) {
            if(isset($this->request->data) && array_key_exists('order', $this->request->data) && count($this->request->data['order']) > 0){
                foreach($this->request->data['order'] as $draft => $sort){
                    $this->connection->execute("UPDATE `price_drafts` SET `sort` = :sort WHERE `id` = :id", ['sort' => $sort, 'id' => $draft]);
                }
                
                $ret['success'] = true;
                $ret['order'] = $this->request->data['order'];
                
            }
        }
        
        echo json_encode($ret);
        exit;
    }

}
