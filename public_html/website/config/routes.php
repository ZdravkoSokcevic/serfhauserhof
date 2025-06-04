<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use App\Routing\Route\AlpinebitsRoute;
use App\Routing\Route\BackendRoute;
use App\Routing\Route\FrontendRoute;
use App\Routing\Route\ShortRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */

// FILTER
Router::addUrlFilter(function ($params, $request) {
    if(array_key_exists('plugin', $request->params) && $request->params['plugin'] == 'Frontend'){
        $params['controller'] = 'Pages';
    }
    return $params;
});

// ALPINEBITS
Router::scope('/alpinebits/', ['plugin' => 'Alpinebits'], function ($routes) {

    // route to correct controller/action
    $routes->connect('/:language/:version/*', ['alpinebits' => true], ['version' => 'error|v1', 'routeClass' => 'AlpinebitsRoute']);

    $routes->redirect('/*', '/alpinebits/error/', ['status' => 302]);

});

// BACKEND
Router::scope('/admin/', ['plugin' => 'Backend'], function ($routes) {

    // languages
    $validate = array();
    foreach(Configure::read('languages') as $key => $value){
        if($value['active'] == true){
            $validate[] = $key;
        }
    }

    // route to correct controller/action
    $routes->connect('/:language/:controller/:action/*', ['admin' => true], ['language' => join("|", $validate), 'routeClass' => 'BackendRoute']);
    $routes->connect('/:language/', ['admin' => true, 'controller' => 'Dashboard', 'action' => 'index'], ['language' => join("|", $validate), 'routeClass' => 'BackendRoute']);

    // redirect unknown admin url
    $routes->redirect('/*', '/admin/' . Configure::read('language') . '/', ['status' => 302]);

});

// FRONTEND
if(Configure::read('pretty-url.use-route-code') === false){ // URL's without route code
    Router::defaultRouteClass(ShortRoute::class);
}else{ // URL's with route code
    Router::defaultRouteClass(FrontendRoute::class);
}

Router::scope('/', ['plugin' => 'Frontend'], function ($routes) {

    // sitemap
    $routes->connect(
        '/sitemap.xml',
        array('controller' => 'Pages', 'action' => 'sitemap', 'match' => 'sitemap', 'layout' => 'xml', 'special' => true, '_ext' => 'xml')
    );

    $routes->connect(
        '/:structure/sitemap.xml',
        array('controller' => 'Pages', 'action' => 'sitemap', 'match' => 'sitemap', 'layout' => 'xml', 'special' => true, '_ext' => 'xml')
    );

    // provide download
    $routes->connect(
        '/provide/:type/:language/:id',
        array('controller' => 'Pages', 'action' => 'provide', 'match' => 'download', 'keep' => true),
        array(
            'pass' => array('type','language','id'),
            'language' => '[a-z]{2}',
        )
    );

    // captcha
    $routes->connect(
        '/captchas/:type/:route/:language/*',
        array('controller' => 'Forms', 'action' => 'captcha', 'match' => 'captcha', 'special' => true),
        array(
            'language' => '[a-z]{2}',
            'route' => '[a-z0-9]{3}',

            'persist' => array('route','language'),
        )
    );

    // recaptcha
    $routes->connect(
        '/recaptcha',
        array('controller' => 'Forms', 'action' => 'verifyReCaptcha', 'match' => 'recaptcha', 'special' => false),
        array(
            'persist' => array(),
        )
    );

    // redirect
    $routes->connect(
        '/redirect/:language/:route/*',
        array('action' => 'index', 'match' => 'redirect'),
        array(
            'language' => '[a-z]{2}',
            'route' => '[a-z0-9]{3}',

            'persist' => array('route'),
        )
    );

    // images
    $routes->connect(
        '/seo/:purpose/:id/*',
        array('action' => 'index', 'match' => 'image')
    );

    // pages
    $routes->connect(
        '/*',
        array('controller' => 'Pages', 'action' => 'index', 'match' => 'pages')
    );

});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
