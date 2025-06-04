<?php

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Aura\Intl\Package;
use Cake\Datasource\ConnectionManager;
use Backend\Error\AppError;
use Cake\Cache\Cache;

// error handling
$errorHandler = new AppError();
$errorHandler->register();

// global functions ;)
include_once(CONFIG . "functions.php");
include_once("functions.php");

// translations
Cache::disable();
foreach(['be','country','salutation'] as $d){
    I18n::config($d, function ($domain, $locale) {
    
        // init
        $connection = ConnectionManager::get('default');
        $locale = Locale::parseLocale($locale);
        $results = $connection->execute("SELECT `id`, `fallback`, `translation`, `rel`, `translated` FROM translations WHERE domain = :domain AND locale = :locale ORDER BY rel ASC", ['domain' => $domain, 'locale' => $locale['language']])->fetchAll('assoc');
        
        $package = new Package(
            'sprintf',
            null
        );
        
        if(is_array($results) && count($results) > 0){
            
            // prepare
            $i18n = $remember = [];
            foreach($results as $k => $v){
                
                $_translation = $v['translated'] ? $v['translation'] : $v['fallback'];
                
                if(!empty($v['rel']) && array_key_exists($v['rel'], $remember)){
                    $i18n[$v['fallback']] = [
                        $remember[$v['rel']], $_translation
                    ];
                }else{
                    $i18n[$v['fallback']] = $_translation;
                }
                $remember[$v['id']] = $_translation;
            }
            
            // set
            $package->setMessages($i18n);
        }
        
        return $package;
        
    });
}
Cache::enable();

// read setup
try {
    Configure::load('init');
} catch (\Exception $e) {
    die($e->getMessage() . "\n");
}
