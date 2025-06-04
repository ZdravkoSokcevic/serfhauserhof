<?php

namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;

class FormsController extends AppController
{

    public $paginate = [
        'limit' => 1,
        'order' => [
            'Forms.sent' => 'asc',
        ]
    ];

    public $settings;
    public $types;
    public $actions;

    public function initialize()
    {

        parent::initialize();

        // pagination
        $this->paginate['limit'] = Configure::read('pagination.limit');
        $this->loadComponent('Paginator');

        // settings/types
        $this->settings = Configure::read('elements.form');
        if(is_array($this->settings) && $this->settings['active'] == true && array_key_exists('fields', $this->settings) && is_array($this->settings['fields']) && array_key_exists('view', $this->settings['fields']) && is_array($this->settings['fields']['view']) && array_key_exists('attr', $this->settings['fields']['view']) && is_array($this->settings['fields']['view']['attr']) && array_key_exists('type', $this->settings['fields']['view']['attr']) && $this->settings['fields']['view']['attr']['type'] == 'select' && array_key_exists('options', $this->settings['fields']['view']['attr']) && is_array($this->settings['fields']['view']['attr']['options'])){
            $this->types = $this->settings['fields']['view']['attr']['options'];
        }else{
            $this->types = [];
        }
        asort($this->types);

        // actions
        $this->actions = [
            'unsubscribe' => __d('be', 'Sign off'),
            'subscribe' => __d('be', 'Sign up'),
        ];

    }

    public function index($type = false){

        // init
        $forms = [];

        if(count($this->types) > 0){

            // types
            $types = $this->types;
            $type = $type === false || !array_key_exists($type, $types) ? 'request' : $type;

            // actions
            $actions = $this->actions;

            // fetch forms
            $query = $this->Forms
            ->find('all')
            ->where(['type' => $type])
            ->order(['sent' => 'DESC'])
            ->formatResults(function ($results) use ($types, $actions) {
                return $results->map(function ($row) use ($types, $actions) {

                    // decode
                    $row->data = json_decode($row->data, true);
                    $row->request = json_decode($row->request, true);

                    // "build" info array
                    $sender = '';
                    if(array_key_exists('firstname', $row->data) && array_key_exists('lastname', $row->data)){
                        $sender = trim($row->data['firstname'] . ' ' . $row->data['lastname']);
                    }else if(array_key_exists('firstname', $row->data)){
                        $sender = trim($row->data['firstname']);
                    }else if(array_key_exists('lastname', $row->data)){
                        $sender = trim($row->data['lastname']);
                    }else if(array_key_exists('name', $row->data)){
                        $sender = trim($row->data['name']);
                    }
                    if(array_key_exists('email', $row->data) && !empty($row->data['email'])){
                        $sender = trim($sender . ' <span class="italic">' . $row->data['email'] . '</span>');
                    }

                    if(empty($sender)){
                        $sender = strtoupper(__d('be', 'Unknown'));
                    }

                    // action
                    $action = $row->action && array_key_exists($row->action, $actions) ? ' (' . $actions[$row->action] . ')' : '';

                    $infos = [
                        'id' => $row->id,
                        'sender' => $sender,
                        'type' => $types[$row->type] . $action,
                        'locale' => array_key_exists('language', $row->request) ? $row->request['language'] : false,
                        'structure' => array_key_exists('structure', $row->request) && is_array($row->request['structure']) && array_key_exists('title', $row->request['structure']) ? $row->request['structure']['title'] : false,
                        'sent' => $row->sent,
                    ];
                    $row->infos = $infos;

                    return $row;
                });
            });
            try {
                $forms = $this->paginate($query);
                $forms = $query->toArray();
            } catch (NotFoundException $e) {
                $forms = [];
            }

        }

        // set vars
        $this->set('type', $type);
        $this->set('forms', $forms);

        // menu
        $menu = [
            'left' => [
                [
                    'show' => count($types) > 0 ? true : false,
                    'type' => 'select',
                    'name' => 'types',
                    'attr' => [
                        'options' => $types,
                        'class' => 'dropdown',
                        'default' => $type,
                        'escape' => false,
                    ],
                ],
            ],
        ];

        $this->set('title', __d('be', 'Requests/Bookings'));
        $this->set('menu', $menu);

    }

    public function delete($type, $id){

        // init
        $success = false;

        // delte
        try {
            $entity = $this->Forms->get($id);
            if($this->Forms->delete($entity)){
                $success = true;
            }
        } catch (RecordNotFoundException $e) { }

        // message
        if($success){
            $this->Flash->success(__d('be', 'The entry has been successfully removed!'));
        }else{
            $this->Flash->error(__d('be', 'An error has occurred, please try again!'));
        }

        // redirect
        return $this->redirect(['action' => 'index', $type]);

    }

    public function details($type, $id, $mode = 'details'){

        // init
        $dataTable = $detailsTable = '';
        $data = [];

        // fetch form
        $query = $this->Forms
        ->find('all')
        ->where(['type' => $type, 'id' => $id])
        ->order(['sent' => 'DESC'])
        ->formatResults(function ($results) {
            return $results->map(function ($row) {

                // decode
                $row->data = json_decode($row->data, true);
                $row->request = json_decode($row->request, true);

                return $row;
            });
        });
        try {
            $forms = $query->toArray();
        } catch (NotFoundException $e) {
            $forms = [];
        }

        if(count($forms) > 0){
            $data = $forms[0];
        }

        // context
        $context = $this->connection->execute("SELECT * FROM `elements` WHERE `id` = :id LIMIT 1", ['id' => $data['request']['element']['id']])->fetch('assoc');
        if(is_array($context) && count($context) > 0){

            // fields
            $fields = json_decode($context['fields'], true);
            foreach($fields as $k => $v){
                $context[$k] = $v;
            }

            // salutations
            Configure::load('salutations');
            $salutations = [];
            foreach(Configure::read('salutations') as $k => $v){
                $salutations[$k] = $v['short'];
            }

            // countries
            Configure::load('countries');
            $countries = [];
            foreach(Configure::read('countries') as $k => $v){
                $countries[$k] = $v['name'];
            }

            // interests/rooms/packages
            $interests = $rooms = $packages = [];

            // newsletter special behaviour
            if($data['type'] == 'newsletter'){
                $interests = [];
                $_interests = Configure::read('newsletter.interests');
                if(is_array($_interests)){
                    foreach($_interests as $key => $value){
                        $interests[$key] = [
                            'title' => $value,
                            'rel' => false,
                        ];
                    }
                }
            }else if($data['type'] == 'brochure'){
                $interests = Configure::read('brochure.interests');
            }else if($data['type'] == 'request'){
                $rooms = $this->getRooms();
                $packages = $this->getPackages();
            }else if($data['type'] == 'lastminute'){
                $rooms = $this->getRooms();
            }

            // action
            $action = $data['action'] && array_key_exists($data['action'], $this->actions) ? ' (' . $this->actions[$data['action']] . ')' : '';

            // date table
            $dataTable = infoTable($data['data'], $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $this->Forms->map('data')]);

            // details table
            $details = [
                'type' => $this->types[$data['type']] . $action,
                'date' => date("d.m.Y H:i:s", $data['sent']),
                'page' => $context['internal'],
                'url' => array_key_exists('url', $data['request']) && is_array($data['request']['url']) && array_key_exists('url', $data['request']['url']) ? $data['request']['url']['url'] : false,
                'locale' => array_key_exists('language', $data['request']) ? $data['request']['language'] : false,
                'structure' => array_key_exists('structure', $data['request']) && is_array($data['request']['structure']) && array_key_exists('title', $data['request']['structure']) ? $data['request']['structure']['title'] : false,
            ];
            $detailsTable = infoTable($details, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $this->Forms->map('details')]);

        }

        // set vars
        $this->set('title', $this->types[$data['type']] . $action);
        $this->set('data', $dataTable);
        $this->set('details', $detailsTable);
        $this->set('context', $context);

        // render
        $this->render($mode);

    }

}
