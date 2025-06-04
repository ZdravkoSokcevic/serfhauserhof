<?php

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Aura\Intl\Package;
use Cake\Datasource\ConnectionManager;
use Alpinebits\Error\AppError;
use Cake\Cache\Cache;

// error handling
$errorHandler = new AppError();
$errorHandler->register();

// global functions ;)
// include_once(CONFIG . "functions.php");
// include_once("functions.php");

// read setup
try {
    Configure::load('init');
} catch (\Exception $e) {
    die($e->getMessage() . "\n");
}
