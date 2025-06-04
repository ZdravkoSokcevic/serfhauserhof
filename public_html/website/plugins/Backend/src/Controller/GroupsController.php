<?php

namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;

class GroupsController extends AppController
{

    public $paginate = [
        'limit' => 1,
        'order' => [
            'Groups.name' => 'asc',
        ]
    ];
    
    public function initialize()
    {
        
        parent::initialize();
        
        // pagination
        $this->paginate['limit'] = Configure::read('pagination.limit');
        $this->loadComponent('Paginator');

    }

    public function index()
    {
        
        // fetch all groups
        $query = $this->Groups->find('all', ['fields' => ['id', 'name']]);
        try {
            $groups = $this->paginate($query);
            $groups = $query->toArray();
        } catch (NotFoundException $e) {
            $groups = [];
        }
        $this->set('groups', $groups);
        
        // menu
        $menu = [
            'left' => [],
            'right' => [
                [
                    'show' => __cp(['controller' => 'groups', 'action' => 'update'], $this->request->session()->read('Auth')),
                    'type' => 'link',
                    'text' => __d('be', 'Create new group'),
                    'url' => ['controller' => 'groups', 'action' => 'update'],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', __d('be', 'Groups overview'));
        $this->set('menu', $menu);
        
    }

    public function update($id = null)
    {
        
        // init
        $structures = false;
        $images = Configure::read('images');
        $rights = [];
        $map = [
            'dashboard' => __d('be', 'Dashboard'),
            'config' => __d('be', 'Configuration'),
            'structures' => __d('be', 'Structures'),
            'nodes' => __d('be', 'Nodes'),
            'images' => __d('be', 'Images'),
            'group' => __d('be', 'Group actions'),
            'elements' => __d('be', 'Elements'),
            'categories' => __d('be', 'Categories'),
            'users' => __d('be', 'Users'),
            'groups' => __d('be', 'Groups'),
            'translations' => __d('be', 'Translations'),
            'forms' => __d('be', 'Form history'),
            'newsletter' => __d('be', 'Newsletter'),
        ];
        
        if($id){
            $group = $this->Groups
            ->find()
            ->where(['Groups.id' => $id])
            ->formatResults(function ($results) {
                return $results->map(function ($row) {
                    return $this->Groups->afterFind($row);
                });
            })
            ->first();
        }else{
            $group = $this->Groups->newEntity();
        }
        
        // auth
        $auth = $this->request->session()->read('Auth');
        
        // values
        $values = $group->toArray();
        
        // config
        $configs = Configure::read('config');
        if(count($configs) > 0){
            $rights['config'] = [];
            foreach($configs as $key => $config){
                $rights['config'][$key] = [
                    'desc' => $config['name'],
                    'value' => array_key_exists('settings', $values) && array_key_exists('config', $values['settings']) && array_key_exists($key, $values['settings']['config']) ? $values['settings']['config'][$key] : 0,
                    'url' => [
                        'controller' => 'config',
                        'action' => 'index',
                        $key
                    ]
                ];
            }
            if(Configure::read('newsletter.type') != false && Configure::read('newsletter.type') != 'internal'){
                $rights['config'][Configure::read('newsletter.type')] = [
                    'desc' => __d('be', 'Newsletter'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('config', $values['settings']) && array_key_exists(Configure::read('newsletter.type'), $values['settings']['config']) ? $values['settings']['config'][Configure::read('newsletter.type')] : 0,
                    'url' => [
                        'controller' => 'config',
                        'action' => 'index',
                        $key
                    ]
                ];
            }
        }
        
        // structure
        $rights['structures'] = [
            'tree' => [
                'desc' => __d('be', 'Show'),
                'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('tree', $values['settings']['structures']) ? $values['settings']['structures']['tree'] : 0,
                'url' => [
                    'controller' => 'structures',
                    'action' => 'tree'
                ]
            ],
            'update' => [
                'desc' => __d('be', 'Update/create'),
                'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('update', $values['settings']['structures']) ? $values['settings']['structures']['update'] : 0,
                'url' => [
                    'controller' => 'structures',
                    'action' => 'update'
                ]
            ],
            'delete' => [
                'desc' => __d('be', 'Delete'),
                'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('delete', $values['settings']['structures']) ? $values['settings']['structures']['delete'] : 0,
                'url' => [
                    'controller' => 'structures',
                    'action' => 'delete'
                ]
            ],
            'nodes' => [
                'create' => [
                    'desc' => __d('be', 'Add'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('nodes', $values['settings']['structures']) && array_key_exists('create', $values['settings']['structures']['nodes']) ? $values['settings']['structures']['nodes']['create'] : 0,
                    'url' => [
                        'controller' => 'nodes',
                        'action' => 'create'
                    ]
                ],
                'toggle' => [
                    'desc' => __d('be', 'Toggle (jump, show, lock, robots-index, robots-follow)'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('nodes', $values['settings']['structures']) && array_key_exists('toggle', $values['settings']['structures']['nodes']) ? $values['settings']['structures']['nodes']['toggle'] : 0,
                    'url' => [
                        'controller' => 'nodes',
                        'action' => 'toggle'
                    ]
                ],
                'period' => [
                    'desc' => __d('be', 'Period'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('nodes', $values['settings']['structures']) && array_key_exists('period', $values['settings']['structures']['nodes']) ? $values['settings']['structures']['nodes']['period'] : 0,
                    'url' => [
                        'controller' => 'nodes',
                        'action' => 'period'
                    ]
                ],
                'settings' => [
                    'desc' => __d('be', 'Settings'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('nodes', $values['settings']['structures']) && array_key_exists('settings', $values['settings']['structures']['nodes']) ? $values['settings']['structures']['nodes']['settings'] : 0,
                    'url' => [
                        'controller' => 'nodes',
                        'action' => 'settings'
                    ]
                ],
                'delete' => [
                    'desc' => __d('be', 'Delete'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('structures', $values['settings']) && array_key_exists('nodes', $values['settings']['structures']) && array_key_exists('delete', $values['settings']['structures']['nodes']) ? $values['settings']['structures']['nodes']['delete'] : 0,
                    'url' => [
                        'controller' => 'nodes',
                        'action' => 'delete'
                    ]
                ],
            ]
        ];
        
        // images
        $rights['images'] = [
            'index' => [
                'desc' => __d('be', 'Show'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('index', $values['settings']['images']) ? $values['settings']['images']['index'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'index'
                ]
            ],
            'translate' => [
                'desc' => __d('be', 'Translate'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('translate', $values['settings']['images']) ? $values['settings']['images']['translate'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'translate'
                ]
            ],
            'upload' => [
                'desc' => __d('be', 'Upload'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('upload', $values['settings']['images']) ? $values['settings']['images']['upload'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'upload'
                ]
            ],
            'exchange' => [
                'desc' => __d('be', 'Exchange'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('exchange', $values['settings']['images']) ? $values['settings']['images']['exchange'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'exchange'
                ]
            ],
            'override' => [
                'desc' => __d('be', 'Override'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('override', $values['settings']['images']) ? $values['settings']['images']['override'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'override'
                ]
            ],
            'search' => [
                'desc' => __d('be', 'Search'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('search', $values['settings']['images']) ? $values['settings']['images']['search'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'search'
                ]
            ],            
            'delete' => [
                'desc' => __d('be', 'Delete'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('delete', $values['settings']['images']) ? $values['settings']['images']['delete'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'delete'
                ]
            ],
        ];
        
        if(is_array($images['sizes']['purposes']) && count($images['sizes']['purposes']) > 0){
            $rights['images']['crop'] = [
                'desc' => __d('be', 'Crop'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('crop', $values['settings']['images']) ? $values['settings']['images']['crop'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'crop'
                ]
            ];
            $rights['images']['auto'] = [
                'desc' => __d('be', 'Automatic crop'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('auto', $values['settings']['images']) ? $values['settings']['images']['auto'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'auto'
                ]
            ];
        }
        
        $rights['images']['revolve'] = [
            'desc' => __d('be', 'Rotate'),
            'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('revolve', $values['settings']['images']) ? $values['settings']['images']['revolve'] : 0,
            'url' => [
                'controller' => 'images',
                'action' => 'revolve'
            ]
        ];
        
        $rights['images']['group'] = [
            'move' => [
                'desc' => __d('be', 'Move'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('group', $values['settings']['images']) && is_array($values['settings']['images']['group']) && array_key_exists('move', $values['settings']['images']['group']) ? $values['settings']['images']['group']['move'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'group',
                    'move'
                ]
            ],
            'delete' => [
                'desc' => __d('be', 'Delete'),
                'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('group', $values['settings']['images']) && is_array($values['settings']['images']['group']) && array_key_exists('delete', $values['settings']['images']['group']) ? $values['settings']['images']['group']['delete'] : 0,
                'url' => [
                    'controller' => 'images',
                    'action' => 'group',
                    'delete'
                ]
            ],
        ];
        
        if(!array_key_exists('use_categories', $images) || $images['use_categories'] === true){
            $rights['images']['categories'] = [
                'update' => [
                    'desc' => __d('be', 'Update/create'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('categories', $values['settings']['images']) && is_array($values['settings']['images']['categories']) && array_key_exists('update', $values['settings']['images']['categories']) ? $values['settings']['images']['categories']['update'] : 0,
                    'url' => [
                        'controller' => 'categories',
                        'action' => 'update',
                        'images'
                    ]
                ],
                'delete' => [
                    'desc' => __d('be', 'Delete'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('images', $values['settings']) && array_key_exists('categories', $values['settings']['images']) && is_array($values['settings']['images']['categories']) && array_key_exists('delete', $values['settings']['images']['categories']) ? $values['settings']['images']['categories']['delete'] : 0,
                    'url' => [
                        'controller' => 'categories',
                        'action' => 'delete',
                        'images'
                    ]
                ]
            ];
        }else{
            unset($rights['images']['group']['move']);
        }
        
        // elements
        $elements = Configure::read('elements');
        if(count($elements) > 0){
            $rights['elements'] = [];
            foreach($elements as $code => $element){
                if($element['active']){

                    // structure?
                    if(array_key_exists('structure', $element) && $element['structure']){
                        $structures = true;
                    }

                    // map
                    $map[$code] = $element['translations']['menu'];
                    
                    // init
                    $rights['elements'][$code] = [
                        'index' => [
                            'desc' => __d('be', 'Show'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('index', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['index'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'index',
                                $code,
                            ]
                        ],
                        'update' => [
                            'desc' => __d('be', 'Update/create'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('update', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['update'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'update',
                                $code,
                            ]
                        ],
                        'copy' => [
                            'desc' => __d('be', 'Copy'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('copy', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['copy'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'copy',
                                $code,
                            ]
                        ],
                        'delete' => [
                            'desc' => __d('be', 'Delete'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('delete', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['delete'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'delete',
                                $code,
                            ]
                        ],
                    ];
                    
                    // sortable?
                    if(array_key_exists('sortable', $element) && $element['sortable'] === true){
                        $rights['elements'][$code]['order'] = [
                            'desc' => __d('be', 'Sort'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('order', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['order'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'order',
                                $code,
                            ]
                        ];
                    }
                    
                    // prices?
                    if(array_key_exists('prices', $element) && is_array($element['prices'])){
                        $rights['elements'][$code]['prices'] = [
                            'desc' => __d('be', 'Edit prices'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('prices', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['prices'] : 0,
                            'url' => [
                                'controller' => 'prices',
                                'action' => 'update',
                                'elements',
                                $code,
                            ]
                        ];
                        
                        $rights['elements'][$code]['drafts'] = [
                            'update' => [
                                'desc' => __d('be', 'Update/create'),
                                'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('drafts', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['drafts']) && array_key_exists('update', $values['settings']['elements'][$code]['drafts']) ? $values['settings']['elements'][$code]['drafts']['update'] : 0,
                                'url' => [
                                    'controller' => 'drafts',
                                    'action' => 'update',
                                    'elements',
                                    $code,
                                ]
                            ],
                            'delete' => [
                                'desc' => __d('be', 'Delete'),
                                'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('drafts', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['drafts']) && array_key_exists('delete', $values['settings']['elements'][$code]['drafts']) ? $values['settings']['elements'][$code]['drafts']['delete'] : 0,
                                'url' => [
                                    'controller' => 'drafts',
                                    'action' => 'delete',
                                    'elements',
                                    $code,
                                ]
                            ],
                            'order' => [
                                'desc' => __d('be', 'Sort'),
                                'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('drafts', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['drafts']) && array_key_exists('order', $values['settings']['elements'][$code]['drafts']) ? $values['settings']['elements'][$code]['drafts']['order'] : 0,
                                'url' => [
                                    'controller' => 'drafts',
                                    'action' => 'order',
                                    'elements',
                                    $code,
                                ]
                            ],
                        ];
                        
                        if(array_key_exists('seasons', $element['prices']) && is_array($element['prices']['seasons']) && array_key_exists('active', $element['prices']['seasons']) && $element['prices']['seasons']['active'] === true && (!array_key_exists('rel', $element['prices']['seasons']) || $element['prices']['seasons']['rel'] === false)){
                            $rights['elements'][$code]['seasons'] = [
                                'update' => [
                                    'desc' => __d('be', 'Update/create'),
                                    'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('seasons', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['seasons']) && array_key_exists('update', $values['settings']['elements'][$code]['seasons']) ? $values['settings']['elements'][$code]['seasons']['update'] : 0,
                                    'url' => [
                                        'controller' => 'seasons',
                                        'action' => 'update',
                                        'elements',
                                        $code,
                                    ]
                                ],
                                'delete' => [
                                    'desc' => __d('be', 'Delete'),
                                    'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('seasons', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['seasons']) && array_key_exists('delete', $values['settings']['elements'][$code]['seasons']) ? $values['settings']['elements'][$code]['seasons']['delete'] : 0,
                                    'url' => [
                                        'controller' => 'seasons',
                                        'action' => 'delete',
                                        'elements',
                                        $code,
                                    ]
                                ],
                            ];
                        }
                        
                    }
                                    
                    // settings?
                    if(array_key_exists('settings', $element) && is_array($element['settings'])){
                        $rights['elements'][$code]['settings'] = [
                            'desc' => __d('be', 'Edit settings'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('settings', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['settings'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'settings',
                                $code,
                            ]
                        ];
                    }
                    
                    // media?
                    if(array_key_exists('media', $element) && is_array($element['media'])){
                        $rights['elements'][$code]['media'] = [
                            'desc' => __d('be', 'Change media'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('media', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['media'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'media',
                                $code,
                            ]
                        ];
                    }

                    // group
                    $rights['elements'][$code]['group'] = [
                        'move' => [
                            'desc' => __d('be', 'Move'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('group', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['group']) && array_key_exists('move', $values['settings']['elements'][$code]['group']) ? $values['settings']['elements'][$code]['group']['move'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'group',
                                $code,
                                'move'
                            ]
                        ],
                        'delete' => [
                            'desc' => __d('be', 'Delete'),
                            'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('group', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['group']) && array_key_exists('delete', $values['settings']['elements'][$code]['group']) ? $values['settings']['elements'][$code]['group']['delete'] : 0,
                            'url' => [
                                'controller' => 'elements',
                                'action' => 'group',
                                $code,
                                'delete'
                            ]
                        ],
                    ];
                    
                    // categories?
                    if(!array_key_exists('use_categories', $element) || $element['use_categories'] === true){
                        $rights['elements'][$code]['categories'] = [
                            'update' => [
                                'desc' => __d('be', 'Update/create'),
                                'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('categories', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['categories']) && array_key_exists('update', $values['settings']['elements'][$code]['categories']) ? $values['settings']['elements'][$code]['categories']['update'] : 0,
                                'url' => [
                                    'controller' => 'categories',
                                    'action' => 'update',
                                    'elements',
                                    $code,
                                    'update'
                                ]
                            ],
                            'delete' => [
                                'desc' => __d('be', 'Delete'),
                                'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('categories', $values['settings']['elements'][$code]) && is_array($values['settings']['elements'][$code]['categories']) && array_key_exists('delete', $values['settings']['elements'][$code]['categories']) ? $values['settings']['elements'][$code]['categories']['delete'] : 0,
                                'url' => [
                                    'controller' => 'categories',
                                    'action' => 'update',
                                    'elements',
                                    $code,
                                    'delete'
                                ]
                            ]
                        ];
                        
                        if(array_key_exists('sort_categories', $element) && $element['sort_categories'] === true){
                            $rights['elements'][$code]['categories']['order'] = [
                                'desc' => __d('be', 'Sort'),
                                'value' => array_key_exists('settings', $values) && array_key_exists('elements', $values['settings']) && array_key_exists($code, $values['settings']['elements']) && is_array($values['settings']['elements']) && array_key_exists($code, $values['settings']['elements']) && array_key_exists('media', $values['settings']['elements'][$code]) ? $values['settings']['elements'][$code]['media'] : 0,
                                'url' => [
                                    'controller' => 'elements',
                                    'action' => 'media',
                                    $code,
                                ]
                            ];
                        }
                        
                    }else{
                        unset($rights['elements'][$code]['group']['move']);
                    }
                }
            }
        }

        if($structures === false){
            unset($rights['structures']);
        }

        // users
        $rights['users'] = [
            'index' => [
                'desc' => __d('be', 'Show'),
                'value' => array_key_exists('settings', $values) && array_key_exists('users', $values['settings']) && is_array($values['settings']['users']) && array_key_exists('index', $values['settings']['users']) ? $values['settings']['users']['index'] : 0,
                'url' => [
                    'controller' => 'users',
                    'action' => 'index',
                ]
            ],
            'update' => [
                'desc' => __d('be', 'Update/create'),
                'value' => array_key_exists('settings', $values) && array_key_exists('users', $values['settings']) && is_array($values['settings']['users']) && array_key_exists('update', $values['settings']['users']) ? $values['settings']['users']['update'] : 0,
                'url' => [
                    'controller' => 'users',
                    'action' => 'update',
                ]
            ],
            'delete' => [
                'desc' => __d('be', 'Delete'),
                'value' => array_key_exists('settings', $values) && array_key_exists('users', $values['settings']) && is_array($values['settings']['users']) && array_key_exists('delete', $values['settings']['users']) ? $values['settings']['users']['delete'] : 0,
                'url' => [
                    'controller' => 'users',
                    'action' => 'delete',
                ]
            ],
        ];
        
        // groups
        $rights['groups'] = [
            'index' => [
                'desc' => __d('be', 'Show'),
                'value' => array_key_exists('settings', $values) && array_key_exists('groups', $values['settings']) && is_array($values['settings']['groups']) && array_key_exists('index', $values['settings']['groups']) ? $values['settings']['groups']['index'] : 0,
                'url' => [
                    'controller' => 'groups',
                    'action' => 'index',
                ]
            ],
            'update' => [
                'desc' => __d('be', 'Update/create'),
                'value' => array_key_exists('settings', $values) && array_key_exists('groups', $values['settings']) && is_array($values['settings']['groups']) && array_key_exists('update', $values['settings']['groups']) ? $values['settings']['groups']['update'] : 0,
                'url' => [
                    'controller' => 'groups',
                    'action' => 'update',
                ]
            ],
            'delete' => [
                'desc' => __d('be', 'Delete'),
                'value' => array_key_exists('settings', $values) && array_key_exists('groups', $values['settings']) && is_array($values['settings']['groups']) && array_key_exists('delete', $values['settings']['groups']) ? $values['settings']['groups']['delete'] : 0,
                'url' => [
                    'controller' => 'groups',
                    'action' => 'delete',
                ]
            ],
        ];
        
        // translations
        $rights['translations'] = [
            'index' => [
                'desc' => __d('be', 'Edit'),
                'value' => array_key_exists('settings', $values) && array_key_exists('translations', $values['settings']) && is_array($values['settings']['translations']) && array_key_exists('index', $values['settings']['translations']) ? $values['settings']['translations']['index'] : 0,
                'url' => [
                    'controller' => 'translations',
                    'action' => 'index',
                ]
            ],
            'import' => [
                'desc' => __d('be', 'Import'),
                'value' => array_key_exists('settings', $values) && array_key_exists('groups', $values['settings']) && is_array($values['settings']['groups']) && array_key_exists('import', $values['settings']['groups']) ? $values['settings']['groups']['import'] : 0,
                'url' => [
                    'controller' => 'translations',
                    'action' => 'import',
                ]
            ],
            'export' => [
                'desc' => __d('be', 'Export'),
                'value' => array_key_exists('settings', $values) && array_key_exists('groups', $values['settings']) && is_array($values['settings']['groups']) && array_key_exists('export', $values['settings']['groups']) ? $values['settings']['groups']['export'] : 0,
                'url' => [
                    'controller' => 'translations',
                    'action' => 'export',
                ]
            ],
        ];
        
        // forms
        if(array_key_exists('elements', $rights) && array_key_exists('form', $rights['elements'])){
            $rights['forms'] = [
                'index' => [
                    'desc' => __d('be', 'Show'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('forms', $values['settings']) && is_array($values['settings']['forms']) && array_key_exists('index', $values['settings']['forms']) ? $values['settings']['forms']['index'] : 0,
                    'url' => [
                        'controller' => 'forms',
                        'action' => 'index',
                    ]
                ],
                'details' => [
                    'desc' => __d('be', 'Details'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('forms', $values['settings']) && is_array($values['settings']['forms']) && array_key_exists('details', $values['settings']['forms']) ? $values['settings']['forms']['details'] : 0,
                    'url' => [
                        'controller' => 'forms',
                        'action' => 'details',
                    ]
                ],
                'delete' => [
                    'desc' => __d('be', 'Delete'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('forms', $values['settings']) && is_array($values['settings']['forms']) && array_key_exists('delete', $values['settings']['forms']) ? $values['settings']['forms']['delete'] : 0,
                    'url' => [
                        'controller' => 'forms',
                        'action' => 'delete',
                    ]
                ],
            ];
        }
        
        // newsletter
        if(Configure::read('newsletter.type') == 'internal'){
            $rights['newsletter'] = [
                'index' => [
                    'desc' => __d('be', 'Export'),
                    'value' => array_key_exists('settings', $values) && array_key_exists('newsletter', $values['settings']) && is_array($values['settings']['newsletter']) && array_key_exists('index', $values['settings']['newsletter']) ? $values['settings']['newsletter']['index'] : 0,
                    'url' => [
                        'controller' => 'newsletter',
                        'action' => 'index',
                    ]
                ],
            ];
        }
        
        // filter
        foreach($rights as $k1 => $v1){
            if(array_key_exists('url', $v1) && array_key_exists('value', $v1)){
                if(!__cp($v1['url'], $auth)){
                    unset($rights[$k1]);
                }
            }else{
                foreach($v1 as $k2 => $v2){
                    if(array_key_exists('url', $v2) && array_key_exists('value', $v2)){
                        if(!__cp($v2['url'], $auth)){
                            unset($rights[$k1][$k2]);
                        }
                    }else{
                        foreach($v2 as $k3 => $v3){
                            if(array_key_exists('url', $v3) && array_key_exists('value', $v3)){
                                if(!__cp($v3['url'], $auth)){
                                    unset($rights[$k1][$k2][$k3]);
                                }
                            }else{
                                foreach($v3 as $k4 => $v4){
                                    if(array_key_exists('url', $v4) && array_key_exists('value', $v4)){
                                        if(!__cp($v4['url'], $auth)){
                                            unset($rights[$k1][$k2][$k3][$k4]);
                                        }
                                    }else{
                                        die("too deep");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $rights = custom_array_filter($rights);
        
        // save
        if ($this->request->is(['post', 'put'])) {
            
            // check settings!
            if(array_key_exists('settings', $this->request->data) && is_array($this->request->data['settings'])){
                $settings = [];
                foreach($this->request->data['settings'] as $controller => $actions){
                    if(array_key_exists($controller, $rights)){
                        $settings[$controller] = [];
                        foreach($actions as $action => $info){
                            if(array_key_exists($action, $rights[$controller])){
                                $settings[$controller][$action] = [];
                                if(is_array($info)){
                                    foreach($info as $k => $v){
                                        if(array_key_exists($k, $rights[$controller][$action])){
                                            $settings[$controller][$action][$k] = [];
                                            if(is_array($v)){
                                                foreach($v as $_k => $_v){
                                                    if(array_key_exists($_k, $rights[$controller][$action][$k])){
                                                        $settings[$controller][$action][$k][$_k] = [];
                                                        if(is_array($_v)){
                                                            die("too deep!");
                                                        }else{
                                                            if(__cp($rights[$controller][$action][$k][$_k]['url'], $auth)){
                                                                $settings[$controller][$action][$k][$_k] = $_v;
                                                            }else{
                                                                $settings[$controller][$action][$k][$_k] = 0;
                                                            }
                                                        }
                                                    }
                                                }
                                            }else{
                                                if(__cp($rights[$controller][$action][$k]['url'], $auth)){
                                                    $settings[$controller][$action][$k] = $v;
                                                }else{
                                                    $settings[$controller][$action][$k] = 0;
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    if(__cp($rights[$controller][$action]['url'], $auth)){
                                        $settings[$controller][$action] = $info;
                                    }else{
                                        $settings[$controller][$action] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
                $this->request->data['settings'] = json_encode($settings);
            }
            
            $this->Groups->patchEntity($group, $this->request->data);
            if ($result = $this->Groups->save($group)) {
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
        
        $this->set('map', $map);
        $this->set('rights', $rights);
        $this->set('group', $group);
        $this->set('id', $id);
        
        // menu
        $menu = [
            'left' => [],
            'right' => [
                [
                    'type' => 'link',
                    'text' => __d('be', 'Back to overview'),
                    'url' => ['controller' => 'groups', 'action' => 'index'],
                    'attr' => [
                        'class' => 'button',
                    ],
                ],
            ],
        ];
        
        $this->set('title', $id ? __d('be', 'Update group') : __d('be', 'Create new group'));
        $this->set('menu', $menu);
        
    }

    public function delete($id = null)
    {
        
        // init
        $this->autoRender = false;
        
        // delete
        try {
             $entity = $this->Groups->get($id);
             if($this->Groups->delete($entity)){
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