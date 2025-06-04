<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Routing\Route;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\Network\Exception\NotFoundException;

class AlpinebitsRoute extends \Cake\Routing\Route\Route
{

    /**
     * Parses a string URL into an array. If it matches, it will convert the
     * controller and plugin keys to their CamelCased form and action key to
     * camelBacked form.
     *
     * @param string $url The URL to parse
     * @return bool|array False on failure, or an array of request parameters
     */
    public function parse($url, $method = '')
    {
    	
        $params = parent::parse($url, $method);
		
        if (!$params) {
            return false;
        }
		
        $params['controller'] = 'Alpinebits';
		
		if(isset($params['version']) && !empty($params['version'])/* && isset($_POST['action']) && !empty($_POST['action'])*/){
        	$version = strtolower($params['version']);
			$action = false;
			if(isset($_POST['action']) && !empty($_POST['action'])){
				$action = str_replace([':'], ['_'], $_POST['action']);
			} else if(isset($params['pass'][0]) && !empty($params['pass'][0])){ //this can be used for debugging
				$action = str_replace([':'], ['_'], $params['pass'][0]);
			}
			if($action !== false){
				$params['action'] = $version . '_' . $action;
			} else{
				return false;
			}
		} else{
			return false;
		}
		
        return $params;
    }

    /**
     * Dasherizes the controller, action and plugin params before passing them on
     * to the parent class.
     *
     * @param array $url Array of parameters to convert to a string.
     * @param array $context An array of the current request context.
     *   Contains information such as the current host, scheme, port, and base
     *   directory.
     * @return bool|string Either false or a string URL.
     */
    public function match(array $url, array $context = [])
    {
        return false;
    }
    
}
