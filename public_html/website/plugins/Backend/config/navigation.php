<?php

use Cake\Core\Configure;

$_navigation = [

    'navigation' => [

        // dashboard
        [
            'name' => __d('be', 'Overview'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Dashboard'),
                    'url' => [
                        'controller' => 'dashboard',
                        'action' => 'index'
                    ],
                    'active' => [],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Configuration'),
                    'url' => [
                        'controller' => 'config',
                        'action' => 'index'
                    ],
                    'active' => [],
                    'show' => count(Configure::read('config')) > 0 ? true : false,
                ],
                [
                    'name' => __d('be', 'Requests/Bookings'),
                    'url' => [
                        'controller' => 'forms',
                        'action' => 'index'
                    ],
                    'active' => [],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Newsletter registrations'),
                    'url' => [
                        'controller' => 'newsletter',
                        'action' => 'index'
                    ],
                    'active' => [],
                    'show' => true,//Configure::read('newsletter.type') == 'internal' ? true : false,
                ]
            ]
        ],

        // structure
        [
            'name' => __d('be', 'Structure'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Structure'),
                    'url' => [
                        'controller' => 'structures',
                        'action' => 'tree'
                    ],
                    'active' => [
                        [
                            'controller' => 'structures',
                            'action' => 'update',
                        ],
                        [
                            'controller' => 'nodes',
                            'action' => 'settings',
                        ],
                    ],
                    'show' => true,
                ]
            ]
        ],

        // pages
        [
            'name' => __d('be', 'Pages'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Menu groups'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'menugroup'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'menugroup',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'menugroup',
                        ],
                    ],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Standard pages'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'page'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'page',
                        ],
                        [
                            'controller' => 'elements',
                            'action' => 'media',
                            'page',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'page',
                        ],
                    ],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Forms'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'form'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'form',
                        ],
                        [
                            'controller' => 'elements',
                            'action' => 'media',
                            'form',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'form',
                        ],
                    ],
                    'show' => true,
                ],
            ],
        ],

        // rooms
        [
            'name' => __d('be', 'Rooms'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Rooms'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'room'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'room',
                        ],
                        [
                            'controller' => 'elements',
                            'action' => 'media',
                            'room',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'room',
                        ],
                        [
                            'controller' => 'prices',
                            'action' => 'update',
                            'elements',
                            'room',
                        ],
                        [
                            'controller' => 'seasons',
                            'action' => 'update',
                            'elements',
                            'room',
                        ],
                        [
                            'controller' => 'drafts',
                            'action' => 'update',
                            'elements',
                            'room',
                        ],
                    ],
                    'show' => true,
                ]
            ]
        ],

        // packages
        [
            'name' => __d('be', 'Packages'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Packages'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'package'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'package',
                        ],
                        [
                            'controller' => 'elements',
                            'action' => 'media',
                            'package',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'package',
                        ],
                        [
                            'controller' => 'prices',
                            'action' => 'update',
                            'elements',
                            'package',
                        ],
                        [
                            'controller' => 'seasons',
                            'action' => 'update',
                            'elements',
                            'package',
                        ],
                        [
                            'controller' => 'drafts',
                            'action' => 'update',
                            'elements',
                            'package',
                        ],
                    ],
                    'show' => true,
                ]
            ]
        ],

        // last-minute
        [
            'name' => __d('be', 'Last-minute offers'),
            'show' => false,
            'elements' => [
                [
                    'name' => __d('be', 'Last-minute offers'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'lastminute'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'lastminute',
                        ],
                        [
                            'controller' => 'elements',
                            'action' => 'media',
                            'lastminute',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'lastminute',
                        ],
                    ],
                    'show' => true,
                ]
            ]
        ],

        // beauty
        [
            'name' => __d('be', 'Wellness & Beauty'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Treatments'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'treatment'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'treatment',
                        ],
                        [
                            'controller' => 'elements',
                            'action' => 'media',
                            'treatment',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'treatment',
                        ],
                        [
                            'controller' => 'prices',
                            'action' => 'update',
                            'elements',
                            'treatment',
                        ],
                        [
                            'controller' => 'seasons',
                            'action' => 'update',
                            'elements',
                            'treatment',
                        ],
                        [
                            'controller' => 'drafts',
                            'action' => 'update',
                            'elements',
                            'treatment',
                        ],
                    ],
                    'show' => true,
                ]
            ]
        ],

        // media
        [
            'name' => __d('be', 'Media'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Images'),
                    'url' => [
                        'controller' => 'images',
                        'action' => 'index'
                    ],
                    'active' => [
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'images',
                        ],
                        [
                            'controller' => 'images',
                            'action' => 'crop',
                        ],
                        [
                            'controller' => 'images',
                            'action' => 'search',
                        ],
                        [
                            'controller' => 'images',
                            'action' => 'override',
                        ]
                    ],
                    'show' => true,
                ],
                [
                    'elements' => true
                ],
            ],
        ],

        // teasers
        [
            'name' => __d('be', 'Teasers'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Header teasers'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'header-teaser'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'header-teaser',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'header-teaser',
                        ],
                    ],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Tiny teasers'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'tiny-teaser'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'tiny-teaser',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'tiny-teaser',
                        ],
                    ],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Small teasers'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'small-teaser'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'small-teaser',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'small-teaser',
                        ],
                    ],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Full width teasers'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'fw-teaser'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'fw-teaser',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'fw-teaser',
                        ],
                    ],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Impressions teasers'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'impressions-teaser'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'impressions-teaser',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'impressions-teaser',
                        ],
                    ],
                    'show' => true,
                ],

            ],
        ],

        // jobs
        [
            'name' => __d('be', 'Jobs'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Jobs'),
                    'url' => [
                        'controller' => 'elements',
                        'action' => 'index',
                        'job'
                    ],
                    'active' => [
                        [
                            'controller' => 'elements',
                            'action' => 'update',
                            'job',
                        ],
                        [
                            'controller' => 'elements',
                            'action' => 'media',
                            'job',
                        ],
                        [
                            'controller' => 'categories',
                            'action' => 'update',
                            'elements',
                            'job',
                        ],
                    ],
                    'show' => true,
                ]
            ]
        ],

        // settings
        [
            'name' => __d('be', 'Settings'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Translations'),
                    'url' => [
                        'controller' => 'translations',
                        'action' => 'index'
                    ],
                    'active' => [],
                    'show' => true,
                ],
            ]
        ],

        // user management
        [
            'name' => __d('be', 'User management'),
            'show' => true,
            'elements' => [
                [
                    'name' => __d('be', 'Users'),
                    'url' => [
                        'controller' => 'users',
                        'action' => 'index'
                    ],
                    'active' => [
                        [
                            'controller' => 'users',
                            'action' => 'update'
                        ]
                    ],
                    'show' => true,
                ],
                [
                    'name' => __d('be', 'Groups'),
                    'url' => [
                        'controller' => 'groups',
                        'action' => 'index'
                    ],
                    'active' => [
                        [
                            'controller' => 'groups',
                            'action' => 'update'
                        ],
                        [
                            'controller' => 'groups',
                            'action' => 'settings'
                        ]
                    ],
                    'show' => true,
                ]
            ]
        ]

    ]

];

return $_navigation;
