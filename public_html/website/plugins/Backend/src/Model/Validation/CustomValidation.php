<?php

namespace Backend\Model\Validation;

use Cake\Validation\Validation;
use Cake\Datasource\ConnectionManager;

class CustomValidation extends Validation
{
 
    public static function usersUniqueUsername($check, $context)
    {
        $id = array_key_exists('data', $context) && is_array($context['data']) && array_key_exists('id', $context['data']) ? $context['data']['id'] : '';
        $connection = ConnectionManager::get('default');
        $results = $connection->execute("SELECT `username` FROM `users` WHERE username = :username AND id != :id LIMIT 1", ['username' => $check, 'id' => $id])->fetchAll('assoc');
        if(is_array($results) && count($results) > 0){
            return __d('be', 'The username must be unique');
        }
        return true;
    }
    
    public static function usersValidGroup($check, $context)
    {
        $group = array_key_exists('data', $context) && is_array($context['data']) && array_key_exists('group_id', $context['data']) ? $context['data']['group_id'] : '';
        if(empty($group)){
            return __d('be', 'Missing group ID');
        }
        return true;
    }
 
}