<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;

class SeasonsController extends AppController {

    public function update($model, $code, $category, $id = false, $related = false) {

        // settings
        if(Configure::read($model) && array_key_exists($code, Configure::read($model)) && Configure::read($model . '.' . $code . '.active') ){
            $settings = Configure::read($model . '.' . $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }
        
        // check seasons
        if(!array_key_exists('prices', $settings) || !is_array($settings['prices']) || !array_key_exists('seasons', $settings['prices']) || !is_array($settings['prices']['seasons']) || !array_key_exists('active', $settings['prices']['seasons']) || $settings['prices']['seasons']['active'] !== true){
            
            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'prices', 'action' => 'update', $model, $code, $category]);
                
        }
        
        // related seasons?
        if(array_key_exists('rel', $settings['prices']['seasons']) && is_string($settings['prices']['seasons']['rel'])){
            
            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'prices', 'action' => 'update', $model, $code, $category]);
            
        }
        
        // check id
        $id = strlen($id) == 36 ? $id : false;
        
        // containers
        $containers = Configure::read('season-containers');

        // link
        $link = false;
        if(array_key_exists('link', $settings['prices']['seasons']) && is_array($settings['prices']['seasons']['link']) && array_key_exists('code', $settings['prices']['seasons']['link']) && !empty($settings['prices']['seasons']['link']['code'])){
            $link = [
                'code' => $settings['prices']['seasons']['link']['code'],
                'required' => array_key_exists('required', $settings['prices']['seasons']['link']) ? $settings['prices']['seasons']['link']['required'] : true,
            ];
        }
        
        // set locale
        $locale = array_key_exists('locale', $this->request->query) && array_key_exists($this->request->query['locale'], Configure::read('translations')) && Configure::read('translations.' . $this->request->query['locale'] . '.active') ? $this->request->query['locale'] : Configure::read('translation');
        $this->Seasons->locale($locale);

        // save
        if($id){
            $season = $this->Seasons
            ->find('translations')
            ->where(['Seasons.id' => $id])
            ->formatResults(function ($results) {
                return $results->map(function ($row) {
                    return $this->Seasons->afterFind($row);
                });
            })
            ->first();
        }else{
            $season = $this->Seasons->newEntity();
        }
        if ($this->request->is(['post', 'put'])) {
            
            // add code
            $this->request->data['code'] = $code;
            
            $this->Seasons->patchEntity($season, $this->request->data, ['validate' => is_array($link) && array_key_exists('required', $link) && $link['required'] ? 'link' : 'default']);
            if ($result = $this->Seasons->save($season)) {
                $this->Flash->success($id ? __d('be', 'The season has been updated.') : __d('be', 'The season has been created.'));
                if($this->request->params['redirect']){
                    return $this->redirect(['controller' => 'prices', 'action' => 'update', $model, $code, $category, $result->id, $related]);
                }else{
                    return $this->redirect(['action' => 'update', $model, $code, $category, $result->id, $related, '?' => ['locale' => $locale]]);
                }
            }else{
                $this->Flash->error(__d('be', 'Unable to save season!'));
            }
        }
        $this->set('link', $link);
        $this->set('containers', $containers);
        $this->set('season', $season);
        $this->set('model', $model);
        $this->set('code', $code);
        $this->set('settings', $settings);
        $this->set('id', $id);
        
        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'translations',
                    'show' => $id === false || !array_key_exists('seasons', $settings['prices']) || !array_key_exists('fields', $settings['prices']['seasons']) || !in_array(true, $settings['prices']['seasons']['fields'], true) ? false : true,
                    'active' => $locale,
                ]
            ],
            'right' => [
                [
                    'type' => 'icon',
                    'show' => $id === false ? false : true,
                    'text' => __d('be', 'Create new season'),
                    'url' => ['controller' => 'seasons', 'action' => 'update', $model, $code, $category, 'false', $related],
                    'icon' => 'plus',
                ],
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back'),
                    'url' => ['controller' => 'prices', 'action' => 'update', $model, $code, $category, $id ? $id : 'false', $related],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', $id ? __d('be', 'Update season') : __d('be', 'Create season'));
        $this->set('menu', $menu);
    }

    public function delete($model, $code, $category, $id, $related = false) {
    
        // settings
        if(Configure::read($model) && array_key_exists($code, Configure::read($model)) && Configure::read($model . '.' . $code . '.active') ){
            $settings = Configure::read($model . '.' . $code);
        }else{

            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }
        
        // check seasons
        if(!array_key_exists('prices', $settings) || !is_array($settings['prices']) || !array_key_exists('seasons', $settings['prices']) || !is_array($settings['prices']['seasons']) || !array_key_exists('active', $settings['prices']['seasons']) || $settings['prices']['seasons']['active'] !== true){
            
            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
                
        }
        
        // related seasons?
        if(array_key_exists('rel', $settings['prices']['seasons']) && is_string($settings['prices']['seasons']['rel'])){
            
            // error
            $this->Flash->error(__d('be', 'Invalid request!'));
            
            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
            
        }
        
        // init
        $success = false;

        // delte
        try {
            $entity = $this->Seasons->get($id);
            if($this->Seasons->delete($entity)){
                $success = true;
            }
        } catch (RecordNotFoundException $e) { }

        // message
        if($success){
            $this->Flash->success(__d('be', 'The season has been successfully removed!'));
        }else{
            $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
        }
            
        // redirect
        return $this->redirect(['controller' => 'prices', 'action' => 'update', $model, $code, $category, 'false', $related]);
        
    }

}
