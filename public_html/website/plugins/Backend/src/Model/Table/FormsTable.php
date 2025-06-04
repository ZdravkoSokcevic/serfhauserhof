<?php

namespace Backend\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;

class FormsTable extends Table
{

    public function initialize(array $config)
    {
        $this->table('emails');
    }
    
    public function map($type){
        
        // init
        $map = [];
        
        if($type == 'data'){
            $map = [
                'desc' => [
                    'room' => __d('be', 'Room'),
                    'offer' => __d('be', 'Offer'),
                    'period' => __d('be', 'Period'),
                    'salutation' => __d('be', 'Salutation'),
                    'title' => __d('be', 'Title'),
                    'firstname' => __d('be', 'Firstname'),
                    'lastname' => __d('be', 'Lastname'),
                    'email' => __d('be', 'E-Mail'),    
                    'address' => __d('be', 'Address'),
                    'zip' => __d('be', 'ZIP'),
                    'city' => __d('be', 'City'),
                    'country' => __d('be', 'Country'),
                    'phone' => __d('be', 'Phone'),
                    'message' => __d('be', 'Message'),
                    'birthday' => __d('be', 'Birthday'),
                    'citizenship' => __d('be', 'Citizenship'),
                    'salutation_recipient' => __d('be', 'Salutation recipient'),
                    'title_recipient' => __d('be', 'Title recipient'),
                    'firstname_recipient' => __d('be', 'Firstname recipient'),
                    'lastname_recipient' => __d('be', 'Lastname recipient'),
                    'coupon_type' => __d('be', 'Coupon type'),
                    'arrival' => __d('be', 'Arrival'),
                    'departure' => __d('be', 'Departure'),
                    'adults' => __d('be', 'Adults'),
                    'children' => __d('be', 'Children'), 
                    'ages' => __d('be', 'Age of children'),
                    'rooms' => __d('be', 'Rooms'),
                    'packages' => __d('be', 'Packages'),
                    'comment' => __d('be', 'Comment'),
                    'value' => __d('be', 'Value'), 
                    'interests' => __d('be', 'Interests'),
                    'newsletter' => __d('be', 'Newsletter'),
                    'education' => __d('be', 'Education'),
                    'references' => __d('be', 'References'),
                    'languages' => __d('be', 'Languages'),
                ],
                'options' => [
                    'vacation' => __d('be', 'Option 1: Holiday'),
                    'value' => __d('be', 'Option 2: Voucher'),
                ],
                'misc' => [
                    'yes' => __d('be', 'Yes'),
                    'no' => __d('be', 'No'),
                    'missing_room' => __d('be', 'Missing room'),
                    'missing_package' => __d('be', 'Missing package'),
                    'missing_interest' => __d('be', 'Missing interest'),
                ]
            ];
        }else if($type == 'details'){
            $map = [
                'desc' => [
                    'type' => __d('be', 'Type'),
                    'date' => __d('be', 'Date/Time'),
                    'page' => __d('be', 'Page'),
                    'locale' => __d('be', 'Language'),
                    'structure' => __d('be', 'Structure'),
                    'url' => __d('be', 'URL'),
                ]
            ];
        }
        
        return $map;
        
    }

}