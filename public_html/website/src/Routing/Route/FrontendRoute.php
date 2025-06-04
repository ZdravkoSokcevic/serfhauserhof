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
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\Network\Exception\NotFoundException;

class FrontendRoute extends \Cake\Routing\Route\Route
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

        // init
        $connection = ConnectionManager::get('default');
        $node = $structure = false;
        $poss = Configure::read('redirects');

        // page
        if(array_key_exists('match', $params) && is_string($params['match']) && $params['match'] == 'pages'){
            if(count($params['pass']) == 0){ // f.e. www.test.at
                $params['match'] = 'root';
            }else if(count($params['pass']) == 1){
                if(strlen($params['pass'][0]) == 2 && ctype_alpha($params['pass'][0]) && array_key_exists($params['pass'][0], Configure::read('translations'))){ // f.e. www.test.at/en
                    $params['language'] = $params['pass'][0];
                    $params['match'] = 'language';
                }else{ // f.e. www.test.at/structure
                    $params['structure'] = $params['pass'][0];
                    $params['match'] = 'structure';
                }
            }else if(count($params['pass']) == 2){
                if(!array_key_exists($params['pass'][0], Configure::read('translations')) && strlen($params['pass'][1]) == 2 && ctype_alpha($params['pass'][1]) && array_key_exists($params['pass'][1], Configure::read('translations'))){ // f.e. www.test.at/structure/en
                    $params['structure'] = $params['pass'][0];
                    $params['language'] = $params['pass'][1];
                    $params['match'] = 'structure-language';
                }else{
                    $params['match'] = 'fallback';
                }
            }else if(count($params['pass']) > 2){
                if(array_key_exists(0, $params['pass']) && array_key_exists(1, $params['pass']) && strlen($params['pass'][0]) == 2 && ctype_alpha($params['pass'][0]) && array_key_exists($params['pass'][0], Configure::read('translations')) && strlen($params['pass'][1]) == 3 && !preg_match('/[^a-z0-9]/', $params['pass'][1])){ // f.e. www.test.at/en/xyz
                    $params['language'] = $params['pass'][0];
                    $params['route'] = $params['pass'][1];
                    $params['match'] = 'language-route';
                }else if(array_key_exists(1, $params['pass']) && array_key_exists(2, $params['pass']) && strlen($params['pass'][1]) == 2 && ctype_alpha($params['pass'][1]) && array_key_exists($params['pass'][1], Configure::read('translations')) && strlen($params['pass'][2]) == 3 && !preg_match('/[^a-z0-9]/', $params['pass'][2])){ // f.e. www.test.at/structure/en/xyz
                    $params['structure'] = $params['pass'][0];
                    $params['language'] = $params['pass'][1];
                    $params['route'] = $params['pass'][2];
                    $params['match'] = 'structure-language-route';
                }else{
                    $params['match'] = 'fallback';
                }
            }
        }

        // params
        $params['error'] = false;
        $params['url'] = array_merge(['url' => $url], array_filter(explode('/', str_replace('.html', '', join("/", $params['pass'])))));
        $params['pass'] = !array_key_exists('keep', $params) ? [] : $params['pass'];

        // specials
        if(array_key_exists('match', $params) && is_string($params['match']) && in_array($params['match'], ['captcha','recaptcha','redirect','image','sitemap'])){

            // pass
            switch($params['match']){
                case "captcha":
                    $params['pass'][] = $params['type'];
                    break;
                case "image":
                    if(array_key_exists('purpose', $params) && array_key_exists('id', $params)){
                        $image = $connection->execute("SELECT `mime`, `extension` FROM `images` WHERE `id` = :id", ['id' => $params['id']])->fetch('assoc');
                        if(is_array($image) && count($image) > 0){

                            // folder
                            if(strpos($params['purpose'], "-") !== false){
                                list($purpose, $thumb) = explode("-", $params['purpose'], 2);
                                $folder = $purpose . DS . $thumb;
                            }else{
                                $folder = $params['purpose'];
                            }

                            $path = ROOT . DS . Configure::read('App.webroot') . DS . Configure::read('upload.images.dir') . $folder . DS . $params['id'] . '.' . $image['extension'];
                            if(file_exists($path)){
                                header('Content-Type:' . $image['mime']);
                                header('Content-Length: ' . filesize($path));
                                readfile($path);
                                exit;
                            }
                        }
                    }

                    // 404
                    header('HTTP/1.0 404 Not Found', true);
                    exit;
                    break;
                case "redirect":
                    if(array_key_exists('route', $params) && array_key_exists('language', $params)){
                        $node = $connection->execute("SELECT `id`, `foreign_id` FROM `nodes` WHERE `route` = :route", ['route' => $params['route']])->fetch('assoc');
                        if(is_array($node) && count($node) > 0){
							$lc = $connection->execute("SELECT `content` FROM `i18n` WHERE `locale`='".$params['language']."' AND `foreign_key`='".$node['foreign_id']."'")->fetch('assoc');
							if(!is_array($lc) || !array_key_exists('content', $lc)){
	                            header('Location: /', true, 301);
                                exit;
							} else{
	                            $add = count($_GET) > 0 ? '?' . http_build_query($_GET) : '';
	                            $url = $this->match(['node' => 'node:'.$node['id'], 'language' => $params['language']]);
                                header('Location: ' . $url . $add, true, 301);
                                exit;
							}
                        }else{
                            $params['error'] = true;
                        }
                    }else{
                        $params['error'] = true;
                    }
                    break;
                case "sitemap":
                    $params = $this->structure($connection, $params);
                    break;
            }

            if($params['error'] !== true){
                return $params;
            }

        }

        // structure
        $params = $this->structure($connection, $params);

        // language
        $params['redirect'] = $this->language();
        $params['client'] = $this->language(false);
        if(array_key_exists('language', $params)){
            if (!array_key_exists($params['language'], Configure::read('translations')) || !Configure::read('translations.' . $params['language'] . '.active') || !Configure::read('translations.' . $params['language'] . '.released')) {
                $params['error'] = true;
            }
        }else{
            if ($params['redirect'] != false && array_key_exists($params['redirect'], $poss) && is_array($poss[$params['redirect']]) && Configure::read('translation') != $params['redirect']){
                $route = false;
                if (array_key_exists($params['structure']['theme'], $poss[$params['redirect']])) {
                    $route = $poss[$params['redirect']][$params['structure']['theme']];
                }

                // keep $_GET!
                $add = count($_GET) > 0 ? '?' . http_build_query($_GET) : '';

                if($route){
                    header('Location: /redirect/' . $params['redirect'] . '/' . $route . '/' . $add);
                }else{
					header('Location: /redirect/' . $params['redirect'] . '/' . $poss[$params['redirect']]['default'] . '/' . $add);
                }
                exit;
            }

            // set language
            $params['language'] = $params['redirect'];

        }

        if($params['error'] !== true){

            // route
            if($params['match'] == 'fallback'){
                // nothing
            }else if(array_key_exists('route', $params)){
                $params['node'] = $connection->execute("SELECT `id`, `foreign_id`, `route`, `jump`, `popup`, `show_from`, `show_to`, `active`, `robots_follow`, `robots_index` FROM nodes WHERE active = 1 AND (show_from = '' OR show_from <= CURDATE()) AND (show_to = '' OR show_to > CURDATE()) AND structure_id = :structure AND route = :route ORDER BY position", ['structure' => $params['structure']['id'], 'route' => $params['route']])->fetch('assoc');
            }else{
                $params['node'] = $connection->execute("SELECT `id`, `foreign_id`, `route`, `jump`, `popup`, `show_from`, `show_to`, `active`, `robots_follow`, `robots_index` FROM nodes WHERE active = 1 AND (show_from = '' OR show_from <= CURDATE() OR show_from IS NULL) AND (show_to = '' OR show_to > CURDATE() OR show_to IS NULL) AND structure_id = :structure AND parent_id = :parent ORDER BY position", ['structure' => $params['structure']['id'], 'parent' => ''])->fetch('assoc');
            }
          if($_SERVER['REMOTE_ADDR'] ==  '31.223.221.124') {
            // var_dump($params);die();
          }

            // fallback
            if(!array_key_exists('node', $params) || !is_array($params['node']) || count($params['node']) == 0){
                $params['error'] = true;
            }else{
                $params['route'] = $params['node']['route'];
                $params['element'] = $connection->execute("SELECT `id`, `code`, `category_id`, `show_from`, `show_to`, `active`, `valid_times`, `valid_fallback` FROM elements WHERE id = :id AND active = 1 AND (show_from = '' OR show_from <= CURDATE() OR show_from IS NULL) AND (show_to = '' OR show_to > CURDATE() OR show_to IS NULL)", ['id' => $params['node']['foreign_id']])->fetch('assoc');
                if(is_array($params['element']) && count($params['element']) > 0){

                    // valid?
                    $valid = false;
                    if(!empty($params['element']['valid_times'])){
                        $times = array_filter(explode("|", $params['element']['valid_times']));
                        if(is_array($times) && count($times) > 0){
                            foreach($times as $time){
                                if(strpos($time, ":") !== false){
                                    list($from,$to) = explode(":", $time, 2);
                                    $to = strtotime($to);
                                    if($to > time()){
                                        $valid = true;
                                    }
                                }
                            }
                        }
                    }else{
                        $valid = true;
                    }

                    if($valid){
                        $params['controller'] = Inflector::pluralize(ucfirst($params['element']['code']));
                        $params['pass'][] = $params['element']['id'];
                    }else{
                        $url = $this->match(['node' => $params['element']['valid_fallback'], 'language' => $params['language']]);
                        if(strlen($url) > 1){
                            header('Location: ' . $url, true, 301);
                            exit;
                        }else{
                            $params['error'] = true;
                        }
                    }
                }else{
                    $params['error'] = true;
                }
            }
        }

        if($params['error']){

            // get 404 page from configuration
            $error = false;
            $config = $connection->execute("SELECT `settings` FROM config WHERE label = :label", ['label' => 'default'])->fetch('assoc');
            if(is_array($config) && array_key_exists('settings', $config)){
                $config = json_decode($config['settings']);
                if(array_key_exists('error', $config)){
                    $error = $config->error;
                }
            }

            // throw error
            if($error){
                if(strpos($error, ":") !== false){
                    list($code,$id) = explode(":", $error, 2);
                    $params['node'] = $connection->execute("SELECT `id`, `foreign_id`, `route`, `show_from`, `show_to`, `active`, `robots_follow`, `robots_index` FROM nodes WHERE id = :id", ['id' => $id])->fetch('assoc');
                    if(is_array($params['node']) && count($params['node']) > 0){
                        $params['element'] = $connection->execute("SELECT * FROM elements WHERE id = :id", ['id' => $params['node']['foreign_id']])->fetch('assoc');
                        if(is_array($params['element']) && count($params['element']) > 0){
                            $params['controller'] = Inflector::pluralize(ucfirst($params['element']['code']));
                            $params['pass'][] = $params['node']['foreign_id'];
                        }else{
                            header('Location: /', true, 301);
                        }
                    }else{
                        header('Location: /', true, 301);
                    }
                }else{
                    header('Location: /', true, 301);
                }
            }else{
                die("No page in structure!");
            }
        }
                  if($_SERVER['REMOTE_ADDR'] ==  '31.223.221.124') {
            //var_dump($params);die();
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

        // init
        $res = '#';

        if(array_key_exists('node', $url) && array_key_exists('language', $url)){

            if(strpos($url['node'], ":") !== false){

                // db
                $connection = ConnectionManager::get('default');

                // check node
                list($type, $id) = explode(':', $url['node']);

                if($type == 'node' && strlen($id) == 36){

                    // get infos
                    $node = $connection->execute("SELECT `n`.`route`, `t`.`content` FROM `nodes` as `n` LEFT JOIN `i18n` as `t` ON (`n`.`foreign_id` = `t`.`foreign_key`) WHERE `n`.`id` = :id AND `t`.`locale` = :locale AND `t`.`field` = :field LIMIT 1", ['id' => $id, 'locale' => $url['language'], 'field' => 'title'])->fetch('assoc');

                    if(is_array($node)){

                        // extend
                        $extend = array_key_exists('extend', $url) && is_array($url['extend']) ? array_filter($url['extend']) : [];
                        $extend = count($extend) > 0 ? '/' . join("/", $extend) : '';

                        // params
                        $params = array_key_exists('?', $url) && is_array($url['?']) ? array_filter($url['?']) : [];
                        $params = count($params) > 0 ? '?' .  http_build_query($params) : '';

                        // name
                        $s = ['ä','ü','ö','Ä','Ü','Ö','ß'];
                        $r = ['ae','ue','oe','Ae','Ue','Oe','ss'];
                        $name = strtolower(Text::slug(str_replace($s,$r,html_entity_decode(strip_tags($node['content'])))));

                        // build url
                        $res = '/' . $url['language'] . '/' . $node['route'] . '/' . $name . $extend . '.html' . $params;

                    }
                }
            }
        }

        return $res;
    }

    private function language($check = true) {
        if (array_key_exists('HTTP_USER_AGENT',$_SERVER) && strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot")) { // google bot
            return Configure::read('translation');
        } else {
            $available = Configure::read('translations');
            $redirects = Configure::read('redirects');
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach ($langs as $value) {
                    $choice = substr($value, 0, 2);
                    if ($check) {
                        if (array_key_exists($choice, $available) && $available[$choice]['active'] === true && $available[$choice]['released'] === true && array_key_exists($choice, $redirects)) {
                            return $choice;
                        }
                    } else {
                        return $choice;
                    }
                }
            }
        }
        return Configure::read('translation');
    }

    private function structure($connection, $params){
        $part = isset($params['structure']) ? $params['structure'] : '';
        $domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $params['structure'] = $connection->execute("SELECT * FROM structures WHERE filter = :domain OR filter = :part", ['domain' => $domain, 'part' => $part])->fetch('assoc');
        if(!is_array($params['structure']) || count($params['structure']) == 0){
            $params['error'] = true;
            $params['structure'] = $connection->execute("SELECT * FROM structures WHERE filter = ''")->fetch('assoc');
            if(!is_array($params['structure']) || count($params['structure']) == 0){
                die("Structure not found!");
            }
        }

        return $params;
    }

}
