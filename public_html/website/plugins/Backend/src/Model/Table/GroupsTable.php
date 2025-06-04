<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class GroupsTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator->provider('custom', 'Backend\Model\Validation\CustomValidation');
        
        return $validator
            ->notEmpty('name', __d('be', 'A name is required'));
    }
    
    public function afterFind($row){
        
        // json
        $row->settings = json_decode($row->settings, true);

        return $row;
    }
    
}