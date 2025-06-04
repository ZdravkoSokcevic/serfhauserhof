<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class CategoriesTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
        $this->addBehavior('Translate', ['fields' => ['title','content','seo'], 'defaultLocale' => false]);
    }

    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('internal', __d('be', 'A internal title is required'))
            ->notEmpty('model', __d('be', 'A model is required'))
            ->notEmpty('code', __d('be', 'A code is required'));
    }
    
    public function validationImage(Validator $validator)
    {
        
        // load validaor
        $validator = $this->validationDefault($validator);
        
        // add stuff
        $validator
            ->notEmpty('seo', __d('be', 'A SEO title is required'));

        return $validator;
    }
    
}