<?php

namespace Frontend\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

class FormsTable extends Table
{
    
    public $connection;
    
    public function initialize(array $config)
    {
        // init
        $this->connection = ConnectionManager::get('default');
        
        // set table
        $this->table('emails');
    }
    
    public function interests(){
        
        // init
        $interests = [];
        
        $_interests = Configure::read('newsletter.interests');
        if(is_array($_interests)){
            $config = Configure::read('config.' . Configure::read('newsletter.type'));
            if(!is_array($config)){
                $config = [];
            }
            foreach($_interests as $key => $value){
                $interests[$key] = [
                    'title' => $value,
                    'rel' => array_key_exists('interests-' . $key, $config) ? $config['interests-' . $key] : false,
                ];
            }
            
        }
        
        return $interests;
    }
    
    public function map(){
        
        return $map = [
            'desc' => [
                'room' => __d('fe', 'Room'),
                'offer' => __d('fe', 'Offer'),
                'period' => __d('fe', 'Period'),
                'salutation' => __d('fe', 'Salutation'),
                'title' => __d('fe', 'Title'),
                'firstname' => __d('fe', 'Firstname'),
                'lastname' => __d('fe', 'Lastname'),
                'email' => __d('fe', 'E-Mail'),    
                'address' => __d('fe', 'Address'),
                'zip' => __d('fe', 'ZIP'),
                'city' => __d('fe', 'City'),
                'country' => __d('fe', 'Country'),
                'phone' => __d('fe', 'Phone'),
                'message' => __d('fe', 'Message'),
                'birthday' => __d('fe', 'Birthday'),
                'citizenship' => __d('fe', 'Citizenship'),
                'salutation_recipient' => __d('fe', 'Salutation recipient'),
                'title_recipient' => __d('fe', 'Title recipient'),
                'firstname_recipient' => __d('fe', 'Firstname recipient'),
                'lastname_recipient' => __d('fe', 'Lastname recipient'),
                'coupon_type' => __d('fe', 'Coupon type'),
                'arrival' => __d('fe', 'Arrival'),
                'departure' => __d('fe', 'Departure'),
                'adults' => __d('fe', 'Adults'),
                'children' => __d('fe', 'Children'), 
                'ages' => __d('fe', 'Age of children'),
                'rooms' => __d('fe', 'Rooms'),
                'packages' => __d('fe', 'Packages'),
                'comment' => __d('fe', 'Comment'),
                'value' => __d('fe', 'Value'), 
                'interests' => __d('fe', 'Interests'),
                'newsletter' => __d('fe', 'Newsletter'),
                'privacy' => __d('fe', 'Data protection'),
                'education' => __d('fe', 'Education'),
                'references' => __d('fe', 'References'),
                'languages' => __d('fe', 'Languages'),
            ],
            'options' => [
                'vacation' => __d('fe', 'Option 1: Holiday'),
                'value' => __d('fe', 'Option 2: Voucher'),
            ],
            'misc' => [
                'yes' => __d('fe', 'Yes'),
                'no' => __d('fe', 'No'),
                'missing_room' => __d('fe', 'Missing room'),
                'missing_package' => __d('fe', 'Missing package'),
                'missing_interest' => __d('fe', 'Missing interest'),
            ]
        ];
        
    }

    public function reduceLastMinuteQuota($id){
        $offer = $this->connection->execute("SELECT `id`, `fields` FROM `elements` WHERE `id` = :id", ['id' => $id])->fetch('assoc');
        if(is_array($offer) && array_key_exists('fields', $offer)){
            $fields = @json_decode($offer['fields'], true);
            if(is_array($fields) && array_key_exists('quota', $fields)){
                
                // reduce
                $fields['quota'] -= 1;
                
                // save
                $this->connection->execute("UPDATE `elements` SET `fields` = :fields WHERE `id` = :id", ['fields' => json_encode($fields), 'id' => $id]);
            }
        }
        return true;
    }
    
}