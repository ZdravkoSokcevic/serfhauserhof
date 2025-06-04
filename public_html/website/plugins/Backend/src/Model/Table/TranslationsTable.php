<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class TranslationsTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    public function validationLoad(Validator $validator){
        $validator
            ->requirePresence('id')
            ->notEmpty('id')
            ->requirePresence('locale')
            ->notEmpty('locale')
            ->requirePresence('domain')
            ->notEmpty('domain');

        return $validator;
    }

    public function validationUpdate(Validator $validator){
        
        // load validaor
        $validator = $this->validationLoad($validator);
        
        // add stuff
        $validator
            ->requirePresence('fallback')
            ->notEmpty('fallback')
            ->requirePresence('translation')
            ->allowEmpty('translation');

        return $validator;
    }
    
}