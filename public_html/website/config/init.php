<?php

use Cake\Core\Configure;

$_init = [

    // default language (cms)
    'language' => 'de',

    // available languages (cms)
    'languages' => [
        'de' => ['title' => 'Deutsch', 'active' => true],
        'en' => ['title' => 'English', 'active' => false],
        // 'it' => ['title' => 'Italiano', 'active' => false],
        // 'fr' => ['title' => 'Française', 'active' => false],
    ],

    // default language (website)
    'translation' => 'de', // change also ini_set('intl.default_locale', 'de') in /config/bootstrap.php

    // translations (website)
    'translations' => [
        'de' => ['title' => 'Deutsch', 'active' => true, 'released' => true],
        'en' => ['title' => 'English', 'active' => true, 'released' => true],
        // 'it' => ['title' => 'Italiano', 'active' => true, 'released' => true],
        // 'fr' => ['title' => 'Française', 'active' => true, 'released' => true],
    ],

    // redirect settings (website)
    'redirects' => [
        'de' => ['default' => 'nr5'],
        'en' => ['default' => 'nr5'],
        // 'it' => ['default' => 'nr5'],
        // 'fr' => ['default' => 'nr5'],
    ],

    // upload settings
    'upload' => [
        'images' => [
            'extensions' => ['.jpg', '.jpeg', '.png'],
            'mime' => ['image/png', 'image/jpeg'],
            'dir' => 'img' . DS,
        ],
        'elements' => [
            'dir' => 'files' . DS,
        ]
    ],

    // pretty url
    'pretty-url' => [
        'use-route-code' => true,
    ],

];

return $_init;
