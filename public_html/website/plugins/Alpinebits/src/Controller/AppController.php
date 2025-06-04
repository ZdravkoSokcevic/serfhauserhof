<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Alpinebits\Controller;

use Cake\Event\Event;
use App\Controller\AppController as BaseController;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\Router;
use Cake\I18n\I18n;
use Cake\Error\FatalErrorException;

class AppController extends BaseController
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
     
    public $connection;
     
    public function initialize()
    {
    	$this->autoRender = false;

        // language
        if(array_key_exists('language', $this->request->params)){
            I18n::locale($this->request->params['language']);
            Configure::write('language', $this->request->params['language']);
            Configure::write('App.defaultLocale', $this->request->params['language']);
        }

        // parent
        parent::initialize();
        
        // init
        try {
        	Configure::load('alpinebits');
            // Configure::load('backend');
            // Configure::load('frontend');
            // Configure::load('elements');
        } catch (\Exception $e) {
            die($e->getMessage() . "\n");
        }
        
        // init
        $this->connection = ConnectionManager::get('default');

        // components
        $this->loadComponent('RequestHandler');
        
    }
    
}
