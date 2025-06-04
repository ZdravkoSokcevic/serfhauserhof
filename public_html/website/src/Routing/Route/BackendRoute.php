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

use Cake\Core\Configure;
use Cake\Utility\Inflector;

class BackendRoute extends \Cake\Routing\Route\Route
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
        
        if (!empty($params['controller'])) {
            $params['controller'] = Inflector::camelize($params['controller']);
        }
        if (!empty($params['plugin'])) {
            $params['plugin'] = Inflector::camelize($params['plugin']);
        }
        
        if(in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT'])){
            if(array_key_exists('_submit', $_POST)){
                $params['redirect'] = $_POST['_submit'] == 'back' ? true : false;
            }
        }
        
        $params['ip'] = isset($_SERVER) && array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : false;
        
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
        $url['admin'] = true;
        $url['language'] = $context['params']['language'];
        
        if (!empty($url['controller'])) {
            $url['controller'] = strtolower($url['controller']);
        }
        if (!empty($url['plugin'])) {
            $url['action'] = strtolower($url['action']);
        }
        
        return parent::match($url, $context);
    }

}
