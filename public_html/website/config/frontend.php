<?php

use Cake\Core\Configure;

$_global = [

    // member?
    'member' => false,

    // email sender
    'sender' => [
        'name' => 'Serfauser Hof',
        'email' => 'info@serfauserhof.at',
    ],

    // newsletter
    'newsletter' => [
        'type' => $_SERVER['REMOTE_ADDR'] == '83.175.88.51' ? 'maileon' : 'internal', // false / internal / tm5 / tm6 (add new if needed)
        'settings' => [ // settings f.e. for TM6 API
            'db' => [ //for tm5
                'datasource' => 'Database/Mysql',
                'persistent' => false,
                'host' => 'host',
                'login' => 'login',
                'password' => 'pass',
                'database' => 'database',
                'prefix' => '',
            ],
            'api' => [ //for tm6
                'client' => 'client', //tm6 client slug
                'username' => 'username',
                'password' => 'password',
                'uri' => 'https://api.tourismail.net',
            ],
            'maileon' => [
                'BASE_URI' => 'https://api.maileon.com/1.0',
                //'BASE_URI' => 'api-test.maileon.com/1.0',
                'API_KEY' => 'ab4bded5-be0c-4914-86d8-17d51a95657a',
                'THROW_EXCEPTION' => false,
                'TIMEOUT' => 60,
                'DEBUG' => false, // NEVER enable on production
                'DATABASE' => 'Serfauserhof',
                'DOI_CODE' => 'lHBKqPHX'
            ]
        ],
        'interests' => [
            'summer' => __d('fe', 'Summer'),
            'winter' => __d('fe', 'Winter'),
            'beauty' => __d('fe', 'Wellness & Beauty'),
        ],
    ],

    // brochure
    'brochure' => [
        'interests' => [
            'summer' => [
                'title' => __d('fe', 'Summer'),
                'rel' => false,
            ],
            'winter' => [
                'title' => __d('fe', 'Winter'),
                'rel' => false,
            ],
            'beauty' => [
                'title' => __d('fe', 'Wellness & Beauty'),
                'rel' => false,
            ],
        ]
    ],

    // captcha
    'captcha' => [
        'type' => 'math',
        'width' => 70,
        'height' => 40,
        'size' => 11,
        'angle' => 0,
        'font' => 'open-sans.regular.ttf',
        'background' => [80, 79, 84],
        'color' => [255,255,255]
    ],

    // styles
    'styles' => [
        [
            'title' => 'Headline',
            'block' => 'h2',
            'classes' => 'editor'
        ],
        [
            'title' => 'Klein',
            'inline' => 'span',
            'classes' => 'small'
        ],
        [
            'title' => 'Farbe',
            'inline' => 'span',
            'classes' => 'color'
        ],
        [
            'title' => '360°',
            'block' => 'div',
            'classes' => 'pano-tours-editor'
        ],
        // [
            // 'title' => 'Inline Style',
            // 'inline' => 'span',
            // 'styles' => [
                // 'color' => 'red',
                // 'text-transform' => 'uppercase',
                // 'text-decoration' => 'underline',
            // ],
        // ]
    ],

    // node settings
    'node-settings' => [],

    // config
    'config' => [

        // default config
        'default' => [
            'name' => __d('be', 'Default'),
            'fields' => [

                // address
                'hotel' => [
                    'fieldset' => __d('be', 'Footer'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'The hotel name is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Hotel name'),
                        'placeholder' => __d('be', 'Hotel name'),
                    ]
                ],
                'family' => [
                    'fieldset' => __d('be', 'Footer'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'The family name is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Family'),
                        'placeholder' => __d('be', 'Family'),
                    ]
                ],
                'street' => [
                    'fieldset' => __d('be', 'Footer'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A street is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Street'),
                        'placeholder' => __d('be', 'Street'),
                    ]
                ],
                'zip' => [
                    'fieldset' => __d('be', 'Footer'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A zip code is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Zip code'),
                        'placeholder' => __d('be', 'Zip code'),
                    ]
                ],
                'city' => [
                    'fieldset' => __d('be', 'Footer'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A city is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'City'),
                        'placeholder' => __d('be', 'City'),
                    ]
                ],

                'state' => [
                    'fieldset' => __d('be', 'Footer'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A state is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'State'),
                        'placeholder' => __d('be', 'State'),
                    ]
                ],
                'country' => [
                    'fieldset' => __d('be', 'Footer'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A country is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Country'),
                        'placeholder' => __d('be', 'Country'),
                    ]
                ],
                'email' => [
                    'fieldset' => __d('be', 'Footer'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'An email address is required'),
                            'url' => [
                                'rule' => 'email',
                                'message' => __d('be', 'Invalid email address'),
                                'last' => true,
                            ],
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'E-Mail address'),
                        'placeholder' => __d('be', 'E-Mail address'),
                    ]
                ],
                'phone' => [
                    'fieldset' => __d('be', 'Footer'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A phone number is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Phone number (formatted)'),
                        'placeholder' => __d('be', 'Phone number (formatted)'),
                    ]
                ],
                'phone-plain' => [
                    'fieldset' => __d('be', 'Footer'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A phone number is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Phone number (plain)'),
                        'placeholder' => __d('be', 'Phone number (plain)'),
                    ]
                ],
                'fax' => [
                    'fieldset' => __d('be', 'Footer'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A fax number is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Fax number (formatted)'),
                        'placeholder' => __d('be', 'Fax number (formatted)'),
                    ]
                ],
                'domain' => [
                    'fieldset' => __d('be', 'Footer'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'The URL is required'),
                            'url' => [
                                'rule' => 'url',
                                'message' => __d('be', 'Invalid URL'),
                                'last' => true,
                            ],
                            'protocol' => [
                                'rule' => ['custom', '/^(http\:\/\/|https\:\/\/)/i'],
                                'message' => __d('be', 'Link without protocol'),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'URL'),
                        'placeholder' => __d('be', 'URL'),
                    ]
                ],

				//geo
                'geo-region' => [
                    'fieldset' => __d('be', 'Geo'),
                    'multi' => false,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A region is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Region'),
                        'placeholder' => 'AT-5',
                    ]
                ],
                'geo-latitude' => [ //Breitengrad
                    'fieldset' => __d('be', 'Geo'),
                    'multi' => false,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A latitude is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Latitude'),
                        'placeholder' => '47.278091',
                    ]
                ],
                'geo-longitude' => [ //Längengrad
                    'fieldset' => __d('be', 'Geo'),
                    'multi' => false,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A longitude is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Longitude'),
                        'placeholder' => '11.433128',
                    ]
                ],

                // seasons
               'summer-start' => [
                   'fieldset' => __d('be', 'Seasons'),
                   'required' => [
                       'rules' => [
                           'notempty' => __d('be', 'A date is required'),
                       ]
                   ],
                   'attr' => [
                       'type' => 'text',
                       'label' => __d('be', 'Summer start date'),
                       'placeholder' => __d('be', 'Summer start date'),
                       'class' => 'date',
                       'data-date-daypicker' => 'true',
                   ]
               ],
               'winter-start' => [
                   'fieldset' => __d('be', 'Seasons'),
                   'required' => [
                       'rules' => [
                           'notempty' => __d('be', 'A date is required'),
                       ]
                   ],
                   'attr' => [
                       'type' => 'text',
                       'label' => __d('be', 'Winter start date'),
                       'placeholder' => __d('be', 'Winter start date'),
                       'class' => 'date',
                       'data-date-daypicker' => 'true',
                   ]
               ],

                // media
                'slideshow' => [
                    'fieldset' => __d('be', 'Media'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A slideshow is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Slideshow'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-category' => 'elements:slideshow',
                        'data-selector-text' => __d('be', 'Select slideshow'),
                    ]
                ],
               'default-panorama' => [
                   'fieldset' => __d('be', 'Media'),
                   'attr' => [
                       'type' => 'text',
                       'label' => __d('be', 'Default panorama ID'),
                       'placeholder' => __d('be', 'f.e.: 1234'),
                   ]
                ],

                // fixed pages
                'home' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Home'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => true,
                ],
                'request' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Booking request'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'book' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Online booking'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'services' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Included services'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'children' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Children prices'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'offers' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Top offers'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
//                'lastminute' => [
//                    'fieldset' => __d('be', 'Pages'),
//                    'required' => [
//                        'rules' => [
//                            'notempty' => __d('be', 'A page is required'),
//                        ]
//                    ],
//                    'attr' => [
//                        'type' => 'text',
//                        'label' => __d('be', 'Last-Minute'),
//                        'class' => 'selector',
//                        'data-selector-max' => 1,
//                        'data-selector-node' => 'true',
//                        'data-selector-text' => __d('be', 'Select page'),
//                    ],
//                    'details' => false,
//                ],
                'tours' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', '360° Tour'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'sitemap' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Sitemap'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'downloads' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Press & Downloads'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'map' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Routeplanner'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'search' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Search'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'cookie' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Cookie information'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'newsletter' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Newsletter'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => true,
                ],
                'contact' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Contact'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'brochures' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Brochures'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'videos' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Videos'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
//                'lastminute' => [
//                    'fieldset' => __d('be', 'Pages'),
//                    'required' => [
//                        'rules' => [
//                            'notempty' => __d('be', 'A page is required'),
//                        ]
//                    ],
//                    'attr' => [
//                        'type' => 'text',
//                        'label' => __d('be', 'Last-Minute'),
//                        'class' => 'selector',
//                        'data-selector-max' => 1,
//                        'data-selector-node' => 'true',
//                        'data-selector-text' => __d('be', 'Select page'),
//                    ],
//                    'details' => false,
//                ],
                'weather' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Weather'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'jobs' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Jobs'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'arrival' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Arrival'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'privacy' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Privacy policy'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'terms' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Terms and conditions'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'imprint' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Imprint'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],
                'error' => [
                    'fieldset' => __d('be', 'Pages'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A page is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', '404 errors'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select page'),
                    ],
                    'details' => false,
                ],

            ]
        ],

        // fixed links
        'links' => [
            'name' => __d('be', 'External'),
            'fields' => [
                'tripadvisor' => [
                    'fieldset' => __d('be', 'Partner'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A link is required'),
                            'url' => [
                                'rule' => 'url',
                                'message' => __d('be', 'Invalid URL'),
                                'last' => true,
                            ],
                            'protocol' => [
                                'rule' => ['custom', '/^(http\:\/\/|https\:\/\/)/i'],
                                'message' => __d('be', 'Link without protocol'),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Tripadvisor'),
                        'placeholder' => __d('be', 'Tripadvisor'),
                    ]
                ],
                'holidaycheck' => [
                    'fieldset' => __d('be', 'Partner'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A link is required'),
                            'url' => [
                                'rule' => 'url',
                                'message' => __d('be', 'Invalid URL'),
                                'last' => true,
                            ],
                            'protocol' => [
                                'rule' => ['custom', '/^(http\:\/\/|https\:\/\/)/i'],
                                'message' => __d('be', 'Link without protocol'),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Holidaycheck'),
                        'placeholder' => __d('be', 'Holidaycheck'),
                    ]
                ],
            ]
        ],

        // news
        'news' => [
            'name' => __d('be', 'News'),
            'fields' => [
                'headline' => [
                    'fieldset' => __d('be', 'News'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A headline is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'image' => [
                    'fieldset' => __d('be', 'News'),
                    'required' => [
                        'on' => ['insert','update'],
                        'rules' => [
                            'notempty' => __d('be', 'An image is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Image'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-image' => '4',
                        'data-selector-text' => __d('be', 'Select image'),
                    ],
                ],
                'content' => [
                    'fieldset' => __d('be', 'News'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A content is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Content'),
                        'placeholder' => __d('be', 'Content'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],
                'link' => [
                    'fieldset' => __d('be', 'News'),
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link'),
                        'class' => 'selector',
                        'data-selector-max' => 1,
                        'data-selector-node' => 'true',
                        'data-selector-text' => __d('be', 'Select link'),
                    ],
                    'details' => false,
                ],
                'linktext' => [
                    'fieldset' => __d('be', 'News'),
                    'multi' => true,
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Link text'),
                        'placeholder' => __d('be', 'Link text'),
                    ]
                ],
                'open' => [
                    'fieldset' => __d('be', 'News'),
                    'attr' => [
                        'type' => 'checkbox',
                        'label' => __d('be', 'open'),
                    ]
                ],
                'active' => [
                    'fieldset' => __d('be', 'News'),
                    'attr' => [
                        'type' => 'checkbox',
                        'label' => __d('be', 'active'),
                    ]
                ],
            ]
        ],

        // opening hours
        'opening-hours' => [
            'name' => __d('be', 'Opening hours'),
            'fields' => [
                'hours-winter-headline' => [
                    'fieldset' => __d('be', 'Winter'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'hours-winter' => [
                    'fieldset' => __d('be', 'Winter'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'Opening hours are required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Opening hours'),
                        'placeholder' => __d('be', 'Opening hours'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],


                'hours-summer-headline' => [
                    'fieldset' => __d('be', 'Summer'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'hours-summer' => [
                    'fieldset' => __d('be', 'Summer'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'Opening hours are required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Opening hours'),
                        'placeholder' => __d('be', 'Opening hours'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],

                'hours-restaurant-headline' => [
                    'fieldset' => __d('be', 'Restaurant'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'A text is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Headline'),
                        'placeholder' => __d('be', 'Headline'),
                    ]
                ],
                'hours-restaurant' => [
                    'fieldset' => __d('be', 'Restaurant'),
                    'multi' => true,
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'Opening hours are required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Opening hours'),
                        'placeholder' => __d('be', 'Opening hours'),
                        'class' => 'wysiwyg',
                        'data-config' => 'teaser',
                    ]
                ],
            ]
        ],

        // tracking config
        'tracking' => [
            'name' => __d('be', 'Tracking & APIs'),
            'fields' => [
                'recapcha_site_key' => [
                    'fieldset' => __d('be', 'Recaptcha'),
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Site key'),
                        'placeholder' => '',
                        'templateVars' => ['help' => '<div class="help-message">' . sprintf(__d('be', 'Get this from %s'), ' <a href="https://www.google.com/recaptcha/admin#site">' . __d('be', 'Google') . '</a>') . '</div>'],
                    ]
                ],
                'recapcha_secret_key' => [
                    'fieldset' => __d('be', 'Recaptcha'),
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Secret key'),
                        'placeholder' => '',
                        'templateVars' => ['help' => '<div class="help-message">' . sprintf(__d('be', 'Get this from %s'), ' <a href="https://www.google.com/recaptcha/admin#site">' . __d('be', 'Google') . '</a>') . '</div>'],
                    ]
                ],
                // google analytics tracking id
                'ga-tracking-id' => [
                    'fieldset' => __d('be', 'Google'),
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'Google Analytics - Tracking ID'),
                        'placeholder' => 'UA-XXXXXXXX-X',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Get this from Google Analytics / Manage / Property / Tracking-Information') . '</div>'],
                    ]
                ],
                'tagmanager-head' => [
                    'fieldset' => __d('be', 'Google'),
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Google Tagmanager - Head Code'),
                        'placeholder' => '<!-- Google Tag Manager --> ...',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Get this from Google Tagmanager / Manage / install Google Tagmanager') . '</div>'],
                    ]
                ],
                'tagmanager-body' => [
                    'fieldset' => __d('be', 'Google'),
                    'attr' => [
                        'type' => 'textarea',
                        'label' => __d('be', 'Google Tagmanager - Body Code'),
                        'placeholder' => '<!-- Google Tag Manager (noscript) --> ...',
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Get this from Google Tagmanager / Manage / install Google Tagmanager') . '</div>'],
                    ]
                ],

            ]
        ],

        // development config
        // INFO: Do not change, just expand!
        'dev' => [
            'name' => __d('be', 'Development'),
            'fields' => [

                // debug
                'mode' => [
                    'fieldset' => __d('be', 'Debug'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'An option is required'),
                        ]
                    ],
                    'attr' => [
                        'type' => 'select',
                        'label' => __d('be', 'Status'),
                        'options' => [
                            1 => __d('be', 'On'),
                            0 => __d('be', 'Off'),
                        ]
                    ]
                ],
                'ip' => [
                    'fieldset' => __d('be', 'Debug'),
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'IP Address(es)'),
                        'placeholder' => __d('be', 'IP Address(es)'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Debug IP Addresses, seperat with ";"') . '</div>'],
                    ]
                ],
                'email' => [
                    'fieldset' => __d('be', 'Debug'),
                    'required' => [
                        'rules' => [
                            'notempty' => __d('be', 'An email address is required'),
                            'email' => [
                                'rule' => 'email',
                                'message' => __d('be', 'Invalid email address'),
                            ]
                        ]
                    ],
                    'attr' => [
                        'type' => 'text',
                        'label' => __d('be', 'E-Mail address'),
                        'placeholder' => __d('be', 'E-Mail address'),
                        'templateVars' => ['help' => '<div class="help-message">' . __d('be', 'Recipient for ALL emails when debug mode is ON!') . '</div>'],
                    ]
                ],

            ]
        ],

    ],

    // season "containers"
    'season-containers' => [
        //'spring' => __d('be', 'Spring'),
        'summer' => __d('be', 'Summer'),
        //'autumn' => __d('be', 'Autumn'),
        'winter' => __d('be', 'Winter'),
    ],

    // ssl
    'ssl' => false,

];

return $_global;
