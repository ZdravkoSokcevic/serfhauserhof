<?php

namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;

class UsersController extends AppController
{

    public $allow = ['edit','login','logout'];
    public $paginate = [
        'limit' => 1,
        'order' => [
            'Users.firstname' => 'asc',
            'Users.lastname' => 'asc'
        ]
    ];
    
    public function initialize()
    {
        
        parent::initialize();
        
        // pagination
        $this->paginate['limit'] = Configure::read('pagination.limit');
        $this->loadComponent('Paginator');

    }

    public function beforeRender(Event $event)
    {
        
        parent::beforeRender($event);
        
        // set layout
        if($this->request->param('action') == 'login'){
            $this->viewBuilder()->layout('minimal');
        }
        
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                if(empty($user['group_id'])){
                    $group = true;
                }else{
                    $group = $this->connection->execute("SELECT * FROM `groups` WHERE `id` = :id LIMIT 1", ['id' => $user['group_id']])->fetch('assoc');
                    if (is_array($group) && count($group) > 0) {
                        $group['settings'] = json_decode($group['settings'], true);
                    }else{
                        $group = false;
                    }
                }
                if ($group) {
                    $this->Auth->setUser($user);
                    $this->request->session()->write(['Auth.Group' => $group]);
                    $this->Flash->success(__d('be', 'You\'ve logged in successfully!'));
                    return $this->redirect($this->Auth->redirectUrl());
                }else{
                    $this->Flash->error(__d('be', 'The user is not assigned to a valid group!'), ['key' => 'auth']);
                }
            }else{
                $this->Flash->error(__d('be', 'Invalid username or password, try again!'), ['key' => 'auth']);
            }
        }
        $this->set('title', __d('be', 'Login'));
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    public function index()
    {
        
        // fetch all uses
        if(empty($this->request->session()->read('Auth.User.group_id'))){
            $query = $this->Users->find('all', ['fields' => ['id', 'firstname', 'lastname', 'username']]); // ->where(['group_id IN' => $groups]);
        }else{
            $query = $this->Users->find('all', ['fields' => ['id', 'firstname', 'lastname', 'username']])->where(["Users.group_id != ''"]);
        }
        try {
            $users = $this->paginate($query);
            $users = $query->toArray();
        } catch (NotFoundException $e) {
            $users = [];
        }
        $this->set('users', $users);
        
        // menu
        $menu = [
            'left' => [],
            'right' => [
                [
                    'show' => __cp(['controller' => 'users', 'action' => 'update'], $this->request->session()->read('Auth')),
                    'type' => 'link',
                    'text' => __d('be', 'Create new user'),
                    'url' => ['controller' => 'users', 'action' => 'update'],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', __d('be', 'Users overview'));
        $this->set('menu', $menu);
        
    }

    public function edit()
    {
        
        // admin
        $admin = empty($this->request->session()->read('Auth.User.group_id')) ? true : false;
        
        // save
        try {
            $user = $this->Users->get($this->request->session()->read('Auth.User.id'));
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            
            // password handling
            if(array_key_exists('password', $this->request->data) && empty($this->request->data['password'])){
                unset($this->request->data['password']);
            }
            
            // group_id
            if(array_key_exists('group_id', $this->request->data)){
                unset($this->request->data['group_id']);
            }
            
            $this->Users->patchEntity($user, $this->request->data, ['validate' => 'adminupdate']);
            if ($result = $this->Users->save($user)) {
                $this->Flash->success(__d('be', 'Your user has been updated.'));
                return $this->redirect(['action' => 'edit', $result->id]);
            }else{
                $this->Flash->error(__d('be', 'Unable to save record!'));
            }
        }
        $this->set('user', $user);
        
        // menu
        $menu = [
            'left' => [],
            'right' => [],
        ];
        
        $this->set('title', __d('be', 'Edit user'));
        $this->set('menu', $menu);
        
    }

    public function update($id = null)
    {

        // admin
        $admin = empty($this->request->session()->read('Auth.User.group_id')) ? true : false;
        
        // groups
        $groups = [];
        $_groups = $this->connection->execute("SELECT `id`, `name` FROM `groups` ORDER BY `name`")->fetchAll('assoc');
        
        foreach($_groups as $k => $v){
            $groups[$v['id']] = $v['name'];
        }
        
        // save
        try {
            $user = $id ? $this->Users->get($id) : $this->Users->newEntity();
        } catch (RecordNotFoundException $e) {
            $user = $this->Users->newEntity();
        }
        
        if($id && empty($user['group_id']) && $admin === false){
            $this->Flash->error(__d('be', 'You are not allowed to edit this user!'));
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
        }
        
        if ($this->request->is(['post', 'put'])) {
            
            // password handling
            if($id && array_key_exists('password', $this->request->data) && empty($this->request->data['password'])){
                unset($this->request->data['password']);
            }
            
            $pre = $admin && $id != null ? 'admin' : '';
            $this->Users->patchEntity($user, $this->request->data, ['validate' => $id ? $pre . 'update' : $pre . 'default']);
            if ($result = $this->Users->save($user)) {
                $this->Flash->success($id ? __d('be', 'The record has been updated.') : __d('be', 'The record has been created.'));
                if($this->request->params['redirect']){
                    return $this->redirect(['action' => 'index']);
                }else{
                    return $this->redirect(['action' => 'update', $result->id]);
                }
            }else{
                $this->Flash->error(__d('be', 'Unable to save record!'));
            }
        }
        
        $this->set('admin', $admin);
        $this->set('groups', $groups);
        $this->set('user', $user);
        $this->set('id', $id);
        
        // menu
        $menu = [
            'left' => [],
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back to overview'),
                    'url' => ['controller' => 'users', 'action' => 'index'],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', $id ? __d('be', 'Update existing user') : __d('be', 'Create new user'));
        $this->set('menu', $menu);
        
    }

    public function delete($id = null)
    {
        
        // check
        if($id == $this->request->session()->read('Auth.User.id')){
            $this->Flash->error(__d('be', 'You are not allowed to delete your own user!'));
            return $this->redirect(['controller' => 'users', 'action' => 'index']);
        }
        
        // init
        $this->autoRender = false;
        
        // delete
        try {
             $entity = $this->Users->get($id);
             if($this->Users->delete($entity)){
                $this->Flash->success(__d('be', 'The record has been successfully removed!'));
             }else{
                $this->Flash->error(__d('be', 'The record could not be removed!'));
             }
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
        }
        
        return $this->redirect(['action' => 'index']);
    }

}