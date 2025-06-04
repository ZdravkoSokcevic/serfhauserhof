<?php

$_setup = [
    
    // domains
    'plugin_i18n' => [
        'Backend' => ['name' => __d('be', 'Backend'), 'domain' => 'be', 'list' => 'languages', 'fetch' => true],
        'Frontend' => ['name' => __d('be', 'Website'), 'domain' => 'fe', 'list' => 'translations', 'fetch' => true],
        'Countries' => ['name' => __d('be', 'Countries'), 'domain' => 'country', 'list' => 'translations', 'fetch' => true],
        'Salutations' => ['name' => __d('be', 'Salutations'), 'domain' => 'salutation', 'list' => 'translations', 'fetch' => true],
    ],
    
    // technical contacts
    'contact' => [
        'admin' => [
            'name' => 'Medienjaeger Programmierung',
            'email' => 'coders@medienjaeger.at',
        ],
        'support' => [
            'name' => 'Ludwig Jaeger',
            'email' => 'support@medienjaeger.at',
        ],
    ],
    
    // currencies
    'currencies' => ['EUR' => '&euro;'],
    
    // image settings
    'images' => [
        'use_categories' => true, // true, false or element
        'focus' => true,
        'sizes' => [
            'auto' => [
                'thumbs' => ['width' => 300, 'height' => 200],
                'popup' => ['width' => 800, 'height' => 600],
            ],
            'ecard' => [
                'view' => ['width' => 1000, 'height' => 625],
                'gallery' => ['width' => 750, 'height' => 468],
                'thumbs' => ['width' => 235, 'height' => 146],
            ],
            'purposes' => [
                1 => ['name' => __d('be', 'Header'), 'width' => 1920, 'height' => 1080, 'editor' => false, 'thumbs' => [ // header
                    ['width' => 1000, 'height' => 1000, 'folder' => 'gallery'],
                    ['width' => 600, 'height' => 1000, 'folder' => 'small'],
                    ['width' => 112, 'height' => 1000, 'folder' => 'thumbs'],
                ]],
                3 => ['name' => __d('be', 'Teaser'), 'width' => 700, 'height' => 345, 'thumbs' => false], // teaser
                4 => ['name' => __d('be', 'Content/Teaser (square)'), 'width' => 800, 'height' => 800, 'thumbs' => false], // content/teaser (square)
//                5 => ['name' => __d('be', 'Content'), 'width' => 500, 'height' => false, 'thumbs' => [ // content
//                    ['width' => 230, 'height' => 1000, 'folder' => 'small'],
//                ]],
            ],
        ],
        'quality' => [
            'png' => 0,
            'jpg' => 80,
        ]
    ],

    // seo
    'seo' => [
        'meta' => [
            'title' => ['min' => 0, 'max' => 55],
            'desc' => ['min' => 80, 'max' => 156]
        ],
        'images' => [
            'folder' => 'seo',
        ],
        'canonical' => []
    ],
        
    // editor links classes
    'editor' => [
        'links' => [
            'nolink' => __d('be', 'No link'),
            'phone' => __d('be', 'Phone'),
            'email' => __d('be', 'E-Mail'),
            'panorama' => __d('be', '360° Tour'),
        ]
    ],
    
    // themes
    'themes' => [
        'website' => __d('be', 'Website'),
    ],
    
    // pagination
    'pagination' => [
        'limit' => 25
    ],
    
    // categories
    'categories' => [
        'code' => 'none', // change also in categories table!
    ],
    
    // allowed
    'allowed' => [],
    
];

return $_setup;