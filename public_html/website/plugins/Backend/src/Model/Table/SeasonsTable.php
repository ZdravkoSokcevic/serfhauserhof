<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

class SeasonsTable extends Table
{

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
        $this->addBehavior('Translate', ['fields' => ['title','content'], 'defaultLocale' => false]);
        
        $this->displayField('internal');
    }

    public function validationDefault(Validator $validator)
    {
        $containers = array_keys(Configure::read('season-containers'));
        return $validator
            ->notEmpty('internal', __d('be', 'A internal title is required'))
            ->notEmpty('times', __d('be', 'Please add season times'))
            ->notEmpty('container', __d('be', 'Please select a group'))
            ->add('container', 'inList', [
                'rule' => ['inList', $containers],
                'message' => __d('be', 'Please select a valid group')
            ]);
    }
    
    public function validationLink(Validator $validator)
    {
        
        // load validaor
        $validator = $this->validationDefault($validator);
        
        // add stuff
        $validator
            ->notEmpty('link', __d('be', 'A linked element is required'));

        return $validator;
    }
    
    public function afterSave($event, $entity, $options){
        if(isset($entity->id) && isset($entity->times) && !empty($entity->times)){
            
            // connection
            $connection = ConnectionManager::get('default');
            
            // cleanup
            $connection->execute("DELETE FROM `season_times` WHERE `season_id` = :id", ['id' => $entity->id]);
            
            // save
            $times = array_filter(explode("|", $entity->times));
            foreach($times as $time){
                if(strlen($time) == 21 && strpos($time, ":") == 10){
                    list($from,$to) = explode(":", $time, 2);
                    $connection->execute("INSERT INTO `season_times` (`season_id`, `valid_from`, `valid_to`) VALUES (:id, :from, :to)", ['id' => $entity->id, 'from' => $from, 'to' => $to]);
                }
            }
        }
    }
    
    public function afterDelete($event, $entity, $options){
        $connection = ConnectionManager::get('default');
        $connection->execute("DELETE FROM `prices` WHERE `season_id` = :season", ['season' => $entity->id]);
    }
    
    public function afterFind($row){
        
        // init
        $times = [];

        // connection
        $connection = ConnectionManager::get('default');
        
        // find
        $_times = $connection->execute("SELECT `valid_from`, `valid_to`, CONCAT(`valid_from`, ':', `valid_to`) as `time` FROM `season_times` WHERE `season_id` = :id ORDER BY `valid_from`, `valid_to` ASC", ['id' => $row->id])->fetchAll('assoc');
        
        // prepare
        foreach($_times as $time){
            $times[] = $time['time'];
        }
        
        // "save"
        $row->_times = $_times;
        $row->times = join("|", $times);

        return $row;
    }
    
}