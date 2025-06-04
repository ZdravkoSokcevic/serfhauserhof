<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

class StructuresTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('title', __d('be', 'A title is required'))
            ->notEmpty('theme', __d('be', 'A theme is required'));
    }
    
    public function afterDelete($event, $entity, $options){

        // delete nodes
        $connection = ConnectionManager::get('default');            
        $connection->execute("DELETE FROM `nodes` WHERE `structure_id` = :structure", ['structure' => $entity->id]);

    }
    
}