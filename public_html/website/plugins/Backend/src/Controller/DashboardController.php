<?php

namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;

class DashboardController extends AppController {

    public $allow = ['index'];

    public function index() {

        // init
        $auth = $this->request->session()->read('Auth');
        $elements = Configure::read('elements');

        // shortcuts
        $shortcuts = [
            [
                'title' => __d('be', 'Structure'),
                'icon' => 'sitemap',
                'url' => [
                    'controller' => 'structures',
                    'action' => 'tree',
                ],
                'display' => true,
            ],
            [
                'title' => __d('be', 'Pages'),
                'icon' => 'file-text-o',
                'url' => [
                    'controller' => 'elements',
                    'action' => 'index',
                    'page'
                ],
                'display' => true,
            ],
            [
                'title' => __d('be', 'Forms'),
                'icon' => 'envelope-o',
                'url' => [
                    'controller' => 'elements',
                    'action' => 'index',
                    'form'
                ],
                'display' => true,
            ],
            [
                'title' => __d('be', 'Rooms'),
                'icon' => 'bed',
                'url' => [
                    'controller' => 'elements',
                    'action' => 'index',
                    'room'
                ],
                'display' => true,
            ],
            [
                'title' => __d('be', 'Packages'),
                'icon' => 'gift',
                'url' => [
                    'controller' => 'elements',
                    'action' => 'index',
                    'package'
                ],
                'display' => true,
            ],
            [
                'title' => __d('be', 'Images'),
                'icon' => 'picture-o',
                'url' => [
                    'controller' => 'images',
                    'action' => 'index',
                ],
                'display' => true,
            ],
            [
                'title' => __d('be', 'Teasers'),
                'icon' => 'newspaper-o',
                'url' => [
                    'controller' => 'elements',
                    'action' => 'index',
                    'teaser'
                ],
                'display' => true,
            ],
        ];
        
        // check
        foreach($shortcuts as $k => $v){
            if(array_key_exists('url', $v) && is_array($v['url']) && array_key_exists('controller', $v['url']) && $v['url']['controller'] == 'elements' && array_key_exists(0, $v['url'])){
                if(!array_key_exists($v['url'][0], $elements) || $elements[$v['url'][0]]['active'] == false || __cp($v['url'], $auth) === false){
                    $shortcuts[$k]['display'] = false;
                }
            }else{
                if(!array_key_exists('url', $v) || __cp($v['url'], $auth) === false){
                    unset($shortcuts[$k]);
                }
            }
        }
        
        $this->set('shortcuts', $shortcuts);
        $this->set('title', __d('be', 'Dashboard'));

    }

}
