<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class UsersTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        
        // load validaor
        $validator = $this->validationUpdate($validator);
        
        return $validator
            ->notEmpty('password', __d('be', 'A password is required'));
    }
    
    public function validationAdmindefault(Validator $validator)
    {
        
        // load validaor
        $validator = $this->validationAdminupdate($validator);
        
        return $validator
            ->notEmpty('password', 'A password is required');
    }
    
    public function validationUpdate(Validator $validator)
    {
        $validator->provider('custom', 'Backend\Model\Validation\CustomValidation');
        
        return $validator
            ->notEmpty('firstname', __d('be', 'A firstname is required'))
            ->notEmpty('lastname', __d('be', 'A lastname is required'))
            ->notEmpty('username', __d('be', 'A username is required'))
            ->add('username', 'unique', [
                'rule' => ['usersUniqueUsername'], 'provider' => 'custom'
            ])
            ->add('group_id', 'valid', [
                'rule' => ['usersValidGroup'], 'provider' => 'custom'
            ]);
    }
    
    public function validationAdminupdate(Validator $validator)
    {
        $validator->provider('custom', 'Backend\Model\Validation\CustomValidation');
        
        return $validator
            ->notEmpty('firstname', __d('be', 'A firstname is required'))
            ->notEmpty('lastname', __d('be', 'A lastname is required'))
            ->notEmpty('username', __d('be', 'A username is required'))
            ->add('username', 'unique', [
                'rule' => ['usersUniqueUsername'], 'provider' => 'custom'
            ]);
    }
    
}