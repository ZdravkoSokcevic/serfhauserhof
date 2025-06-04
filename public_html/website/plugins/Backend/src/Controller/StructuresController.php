<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;
use Cake\Core\Configure;

class StructuresController extends AppController {

    public function tree($id = false) {

        // structures
        $structures = $this->Structures->find('list')->order(['title' => 'ASC'])->toArray();
        if(count($structures) < 1){

            // error
            $this->Flash->error(__d('be', 'You need to create a structure first!'));

            // redirect
            return $this->redirect(['action' => 'update']);

        }

        // init
        $structure = $id === false || !array_key_exists($id, $structures) ? key($structures) : $id;

        // category infos
        $structure = $this->Structures->get($structure);
        $this->set('structure', $structure);

        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'select',
                    'name' => 'structures_dropdown',
                    'attr' => [
                        'options' => $structures,
                        'class' => 'dropdown',
                        'default' => $structure['id'],
                        'escape' => false,
                    ],
                ],
                [
                    'show' => __cp(['controller' => 'structures', 'action' => 'update'], $this->request->session()->read('Auth')),
                    'type' => 'icon',
                    'text' => __d('be', 'Edit structure'),
                    'url' => ['action' => 'update', $structure['id']],
                    'icon' => 'pencil',
                ],
                [
                    'show' => __cp(['controller' => 'structures', 'action' => 'delete'], $this->request->session()->read('Auth')),
                    'type' => 'icon',
                    'text' => __d('be', 'Delete structure'),
                    'url' => ['action' => 'delete', $structure['id']],
                    'icon' => 'trash',
                    'confirm' => __d('be', 'Do you realy want to delete this structure?'),
                ],
                [
                    'show' => __cp(['controller' => 'structures', 'action' => 'update'], $this->request->session()->read('Auth')),
                    'type' => 'icon',
                    'text' => __d('be', 'Create new structure'),
                    'url' => ['action' => 'update'],
                    'icon' => 'plus',
                ],
            ],
            'right' => [
                [
                    'type' => 'icon',
                    'text' => __d('be', 'Expand all'),
                    'url' => [],
                    'icon' => 'folder-open-o',
                    'class' => 'expand'
                ],
                [
                    'type' => 'icon',
                    'text' => __d('be', 'Collapse all'),
                    'url' => [],
                    'icon' => 'folder-o',
                    'class' => 'collapse'
                ],
                [
                    'show' => __cp(['controller' => 'nodes', 'action' => 'create'], $this->request->session()->read('Auth')),
                    'type' => 'link',
                    'text' => __d('be', 'Add page'),
                    'url' => ['controller' => 'selector', 'action' => 'node', $structure['id']],
                    'attr' => [
                        'class' => 'button node',
                    ],
                ],
            ],
        ];

        $this->set('title', __d('be', 'Structure'));
        $this->set('menu', $menu);

        // load datepicker!
        $this->set('load_datepicker', true);

    }

    public function update($id = false) {

        // save
        $structure = $id ? $this->Structures->get($id) : $this->Structures->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $this->Structures->patchEntity($structure, $this->request->data);
            if ($result = $this->Structures->save($structure)) {
                $this->Flash->success($id ? __d('be', 'The structure has been updated.') : __d('be', 'The structure has been created.'));
                if($this->request->params['redirect']){
                    return $this->redirect(['action' => 'tree', $result->id]);
                }else{
                    return $this->redirect(['action' => 'update', $result->id]);
                }
            }else{
                $this->Flash->error(__d('be', 'Unable to save structure!'));
            }
        }
        $this->set('structure', $structure);
        $this->set('id', $id);

        // themes
        $this->set('themes', Configure::read('themes'));

        // menu
        $menu = [
            'left' => [
            ],
            'right' => [
                [
                    'type' => 'icon',
                    'show' => $id === false ? false : true,
                    'text' => __d('be', 'Create new structure'),
                    'url' => ['action' => 'update'],
                    'icon' => 'plus',
                ],
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back'),
                    'url' => ['action' => 'tree', $id],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];

        $this->set('title', $id ? __d('be', 'Update structure') : __d('be', 'Create structure'));
        $this->set('menu', $menu);

    }

    public function delete($id) {

        try{
            $structure = $this->Structures->get($id);
        }catch(RecordNotFoundException $e){
            $structure = false;
        }

        if($structure){
            if($this->Structures->delete($structure)){
                $this->Flash->success(__d('be', 'The structure has been successfully removed!'));
            }else{
                $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
            }
        }else{
            $this->Flash->error(__d('be', 'Invalid request!'));
        }

        return $this->redirect(['action' => 'tree']);

    }

}
