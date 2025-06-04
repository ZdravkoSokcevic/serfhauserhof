<?php

use Cake\Core\Configure;

$_salutations = [
    
    'salutations' => [
    
        1 => [
            'short' => __d('salutation', 'Mr.'),
            'long' => __d('salutation', 'Dear Mr.'),
            'gender' => 'm',
        ],
        2 => [
            'short' => __d('salutation', 'Ms.'),
            'long' => __d('salutation', 'Dear Ms.'),
            'gender' => 'f',
        ],
    
    ]
    
];

return $_salutations;