<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;

// interfaces
require_once(ROOT . DS .  'vendor' . DS  . 'tm5' . DS . 'interface.php');
require_once(ROOT . DS .  'vendor' . DS  . 'tm6' . DS . 'interface.php');
require_once(ROOT . DS .  'vendor' . DS  . 'maileon' . DS . 'interface.php');

class ConfigController extends AppController {

    public $fieldset = '';
    public $interface;

    public function initialize()
    {

        parent::initialize();

        $this->fieldset = __d('be', 'Settings');

        // init interface
        switch(Configure::read('newsletter.type')){
            case "tm5":
            case "tm6":
                $cn = '\\' . strtoupper(Configure::read('newsletter.type')) . 'Interface';
                if(class_exists(strtoupper(Configure::read('newsletter.type')) . 'Interface')){
                    $this->interface = new $cn(Configure::read());
                }else{
                    echo __METHOD__ . ": interface class not found!"; exit;
                }
                break;
        }


    }

    public function index($label = false) {

        // init
        $interface = false;

        // labels
        $labels = [];
        if(is_array(Configure::read('config'))){
            foreach(Configure::read('config') as $k => $v){
                if(__cp(['controller' => 'config', 'action' => 'index', $k], $this->request->session()->read('Auth')) && array_key_exists('name', $v) && array_key_exists('fields', $v) && count($v['fields'])){
                    $labels[$k] = $v['name'];
                }
            }
        }

        if(Configure::read('newsletter.type') !== false && Configure::read('newsletter.type') != 'internal' && __cp(['controller' => 'config', 'action' => 'index', Configure::read('newsletter.type')], $this->request->session()->read('Auth'))){
            $interface = Configure::read('newsletter.type');
            $labels[Configure::read('newsletter.type')] = __d('be', 'Newsletter');
            Configure::write('config.' . Configure::read('newsletter.type'), $this->__init());
        }

        // settings
        if(count($labels) < 1){

            // error
            $this->Flash->error(__d('be', 'No configuration settings available!'));

            // redirect
            return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);

        }

        // label
        $label = $label && array_key_exists($label, $labels) ? $label : key($labels);
        $this->Config->label = $label;

        // settings
        $settings = Configure::read('config.' . $label . '.fields');

        // fieldsets
        $def = false;
        $fieldsets = [$this->fieldset];
        foreach($settings as $k => $v){
            if(array_key_exists('fieldset', $v) && $v['fieldset']){
                if(!in_array($v['fieldset'], $fieldsets)){
                    $fieldsets[] = $v['fieldset'];
                }
            }else{
                $def = true;
            }
        }
        if($def === false){
            unset($fieldsets[0]);
        }
        $this->set('dummy', $this->fieldset);
        $this->set('fieldsets', $fieldsets);

        // get first config entry
        $config = $this->Config
        ->find()
        ->where(['label' => $label])
        ->formatResults(function ($results) use($settings) {
            return $results->map(function ($row) {
                return $this->Config->afterFind($row);
            });
        })
        ->first();
        if($config == false){
            $config = $this->Config->newEntity();
        }

        // languages
        $languages = [];
        foreach(Configure::read('translations') as $k => $v){
            if($v['active']){
                $languages[$k] = $v;
            }
        }

        // save
        if ($this->request->is(['post', 'put'])) {
            $this->Config->patchEntity($config, $this->request->data);
            if ($result = $this->Config->save($config)) {
                $this->Flash->success(__d('be', 'The configurations have been saved.'));
                return $this->redirect(['action' => 'index', $label]);
            }else{
                $this->Flash->error(__d('be', 'Unable to save configurations!'));
            }
        }
        $this->set('config', $config);

        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'select',
                    'name' => 'config_label',
                    'attr' => [
                        'options' => $labels,
                        'class' => 'dropdown',
                        'default' => $label,
                    ],
                    'show' => count($labels) > 1
                ],
            ],
            'right' => [],
        ];

        $this->set('title', __d('be', 'Configuration'));
        $this->set('label', $label);
        $this->set('settings', $settings);
        $this->set('languages', $languages);
        $this->set('menu', $menu);
        $this->set('load_editor', true);
        $this->set('load_datepicker', true);

    }

    private function __init(){
        if($this->interface){

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

            // retrun
            return [
                'name' => __d('be', 'Newsletter'),
                'fields' => $this->interface->config($salutations, $countries)
            ];

        }else{
            return [];
        }
    }

}
