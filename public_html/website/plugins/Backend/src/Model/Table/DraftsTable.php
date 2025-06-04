<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

class DraftsTable extends Table
{

    public function initialize(array $config)
    {
        
        $this->table('price_drafts');
        $this->displayField('internal');
        
        $this->addBehavior('Timestamp');
        $this->addBehavior('Translate', ['fields' => ['title','caption'], 'defaultLocale' => false]);
    }

    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('internal', __d('be', 'A internal title is required'))
            ->notEmpty('title', __d('be', 'A title is required'));            
    }

    public function beforeSave($event, $entity, $options){
        if($entity->isNew()){
            $sort = 0;
            $connection = ConnectionManager::get('default');
            $check = $connection->execute("SELECT `sort` FROM `price_drafts` WHERE `model` = :model AND `code` = :code ORDER BY `sort` DESC LIMIT 1", ['model' => $entity->model, 'code' => $entity->code])->fetch('assoc');
            if(is_array($check) && count($check) > 0){
                $sort = (int) $check + 1;
            }
            $entity->sort = $sort;
        }
    }
    
    public function afterDelete($event, $entity, $options){
        $connection = ConnectionManager::get('default');
        $connection->execute("DELETE FROM `prices` WHERE `price_draft_id` = :draft", ['draft' => $entity->id]);
    }
    
}