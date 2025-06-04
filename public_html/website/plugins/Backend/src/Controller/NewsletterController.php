<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;

class NewsletterController extends AppController {

    public function index() {
        
        // init
        $nl = "\n";
        $sep = '';
        $csv = [];
        $fields = [
            'salutation' => 'salutation',
            'title' => 'plain',
            'firstname' => 'plain',
            'lastname' => 'plain',
            'email' => 'plain',
            'status' => 'status',
        ];
        $this->autoRender = false;
        
        // salutations
        Configure::load('salutations');
        $salutations = [];
        foreach(Configure::read('salutations') as $k => $v){
            $salutations[$k] = $v['short'];
        }
        
        // data
        $data = $this->Newsletter
        ->find('all')
        ->where(['Newsletter.status IN' => ['subscribed', 'unsubscribed']])
        ->formatResults(function ($results){
            return $results->map(function ($row) {
                $row['data'] = json_decode($row['data'], true);
                return $row;
            });
        })
        ->toArray();
        
        // prepare
        foreach($data as $entry){
            $key = count($csv);
            $csv[$key] = [];
            foreach($fields as $field => $handle){
                $value = array_key_exists($field, $entry->data) ? $entry->data[$field] : false;
                switch($handle){
                    case "salutation":
                        $value = array_key_exists($value, $salutations) ? $salutations[$value] : '';
                        break;
                    case "status":
                        $value = $entry->status == 'unsubscribed' ? 0 : 1;
                        break;
                }
                $csv[$key][$field] = $value;
            }
        }
        
        // download
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=newsletter-' . date("Y-m-d") . '.csv');
        header('Pragma: no-cache');
        foreach($fields as $field => $handle){
            echo $sep . ucfirst($field);
            $sep = ';';   
        }
        foreach($csv as $line){
            echo $nl . join(";", $line);
        }
        exit;
    }

}
