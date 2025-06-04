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

namespace Backend\Controller;

use Cake\Event\Event;
use App\Controller\AppController as BaseController;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\Router;
use Cake\I18n\I18n;

class AppController extends BaseController
{
    
    public $connection;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
     
    public function initialize()
    {
        
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
            Configure::load('backend');
            Configure::load('frontend');
            Configure::load('elements');
            Configure::load('Backend.navigation');
        } catch (\Exception $e) {
            die($e->getMessage() . "\n");
        }

		//init default configs 
		foreach(Configure::read('elements') as $element_slug => $element){
			if(is_array($element) && array_key_exists('prices', $element)){
				if(is_array($element['prices'])){
					if(!array_key_exists('flags', $element['prices']) || !is_array($element['prices']['flags']) || count($element['prices']['flags']) <= 0){
						Configure::write('elements.'.$element_slug.'.prices.flags', ['standard' => __d('be', 'Default')]);
					}
				}
			}
		}
		
        // init
        $this->connection = ConnectionManager::get('default');

        // components
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authorize' => 'Controller',
            'loginAction' => [
                'controller' => 'users',
                'action' => 'login',
                'plugin' => 'Backend'
            ],
            'authError' => false,
            'loginRedirect' => [
                'controller' => 'dashboard',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'users',
                'action' => 'login',
                'plugin' => 'Backend'
            ],
            'unauthorizedRedirect' => [
                'controller' => 'dashboard',
                'action' => 'index',
                'plugin' => 'Backend'
            ],
        ]);
        $this->loadComponent('Cookie', ['path' => '/admin/']);
        
        // add request to session
        $this->request->session()->write(['Request' => $this->request->params]);
        
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        
        parent::beforeRender($event);
        
        // auth
        $this->set('auth', $this->request->session()->read('Auth'));
        
        // caution - other users!
        $this->set('caution', $this->caution());
        
        // system infos
        $this->set('system', $this->systemInfos());
        
        // set layout
        $this->viewBuilder()->layout('full');
        
    }
    
    public function isAuthorized($user)
    {
        
        // init
        $url = array_merge(['controller' => strtolower($this->request->params['controller']), 'action' => strtolower($this->request->params['action'])], $this->request->params['pass']);

        // refresh group
        $group = $this->connection->execute("SELECT * FROM `groups` WHERE `id` = :id LIMIT 1", ['id' => $this->request->session()->read('Auth.User.group_id')])->fetch('assoc');
        if (is_array($group) && count($group) > 0) {
            $group['settings'] = json_decode($group['settings'], true);
            $this->request->session()->write(['Auth.Group' => $group]);
        }

        // check
        if(__cp($url, $this->request->session()->read('Auth'))){
            return true;
        }
        if($this->request->is('ajax') == false){
            $this->Flash->error(__d('be', "You do not have the permissions to perform this action!"));
        }
        return false;
    }
    
    public function getCategories($model, $code, $id = false){
        $categories = [];
        $this->loadModel('Backend.Categories');
        $query = $this->Categories->find('threaded')->select(['id', 'parent_id', 'internal'])->where(['Categories.model =' => $model, 'Categories.code =' => $code])->order(['internal' => 'ASC']);
        $query->hydrate(false);
        $this->buildOptions($categories, $query->toArray(), $id);
        return $categories;
    }
    
    public function getCategoriesOrder($model, $code){
        $this->loadModel('Backend.Categories');
        $order = $this->Categories->find('list', ['keyField' => 'id', 'valueField' => 'sort'])->where(['Categories.model' => $model, 'Categories.code =' => $code])->order(['sort' => 'ASC', 'internal' => 'ASC'])->toArray();
        return $order;
    }

    public function getSeasons($code){
        $this->loadModel('Backend.Seasons');
        $seasons = $this->Seasons->find('list')->where(['Seasons.code =' => $code])->order(['internal' => 'ASC'])->toArray();
        return $seasons;
    }
    
    public function buildOptions(&$options, $data, $id, $prefix = ''){
        foreach($data as $k => $v){
            if($v['id'] != $id){
                $options[$v['id']] = $prefix . $v['internal'];
                if(count($v['children']) > 0){
                    $this->buildOptions($options, $v['children'], $id, $prefix . $v['internal'] . ' &raquo; ');
                }
            }
        }
    }
    
    public function systemInfos(){
        
        // structures
        $structures = [];
        $_structures = $this->connection->execute("SELECT `id`, `title` FROM `structures` ORDER BY `title`")->fetchAll('assoc');
        if(is_array($_structures)){
            foreach($_structures as $s){
                $structures[$s['id']] = $s['title'];
            }
        }
        
        return ['structures' => $structures];
    }
    
    public function caution(){
        
        // init
        $id = $this->request->session()->id();
        $caution = [];
        
        // fetch
        $sessions = $this->connection->execute("SELECT `data` FROM `sessions` WHERE `expires` >= :now AND `id` != :id ORDER BY `expires`", ['now' => time(), 'id' => $id])->fetchAll('assoc');
        if(is_array($sessions) && count($sessions) > 0){
            foreach($sessions as $session){
                $data = decode_session($session['data']);
                if(isset($_SESSION) && is_array($_SESSION) && array_key_exists('Auth', $_SESSION) && is_array($_SESSION['Auth']) && array_key_exists('User', $_SESSION['Auth']) && is_array($data) && count($data) > 0 && array_key_exists('Auth', $data) && is_array($data['Auth']) && array_key_exists('User', $data['Auth']) && is_array($data['Auth']['User']) && array_key_exists('Request', $data) && is_array($data['Request'])){
                    // same view?
                    if($this->request->params['controller'] == $data['Request']['controller'] && $this->request->params['action'] == $data['Request']['action'] && $this->request->params['pass'] == $data['Request']['pass']){
                        if($data['Auth']['User']['id'] != $_SESSION['Auth']['User']['id']){
                            $caution[] = $data;
                        }
                    }
                    
                }
            }
        }
        return $caution;
    }
    
    public function getRooms(){
        
        // init
        $rooms = [];
        
        // elements
        $elements = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `code` = :code ORDER BY `sort`", ['code' => 'room'])->fetchAll('assoc');
        if(is_array($elements)){
            foreach($elements as $element){
                $rooms[$element['id']] = $element['internal'];
            }
        }

        return $rooms;
    }
    
    public function getPackages(){
        
        // init
        $packages = [];
        
        // elements
        $elements = $this->connection->execute("SELECT `id`, `internal` FROM `elements` WHERE `code` = :code ORDER BY `sort`", ['code' => 'package'])->fetchAll('assoc');
        if(is_array($elements)){
            foreach($elements as $element){
                $packages[$element['id']] = $element['internal'];
            }
        }
        
        return $packages;
    }
    
    public function isUsed($id, $category = false, $skip = [], $link = true){
            
        // init
        $matches = [
            'nodes' => [],
            'elements' => [],
            'i18n' => [],
            'config' => [],
        ];
        $used = [];
        $ids = !is_array($id) ? array($id) : $id;
        $elements = Configure::read('elements');
        $config = Configure::read('config');

        // category?
        if($category == true){
            
            // get subcategories
            $categories = [$id];
            foreach($ids as $i){
                $c = deeper("SELECT `id` FROM `categories` WHERE `parent_id`  = :id", ['id' => $i], ['id' => 'id'], $this->connection);
                foreach($c as $cat){
                    $categories[] = $cat['id'];
                }
            }
            
            // get ids
            $ids = [];
            $contained = $this->connection->execute("SELECT `id` FROM `" . $category . "` WHERE `category_id` IN ('" . join("', '", $categories) . "')")->fetchAll('assoc');
            if(is_array($contained)){
                foreach($contained as $c){
                    $ids[] = $c['id'];
                }
            }
        }
        
        // check
        if(count($ids) > 0){
            foreach($ids as $i){
    
                // nodes
                if(!in_array('nodes', $skip)){
                    $matches['nodes'][$i] = $this->connection->execute("SELECT `id`, `structure_id` FROM `nodes` WHERE `foreign_id`  = :id", ['id' => $i])->fetchAll('assoc');
                }
                
                // elements
                if(!in_array('elements', $skip)){
                    $matches['elements'][$i] = $this->connection->execute("SELECT `id` FROM `elements` WHERE `fields` LIKE '%" . $i . "%' OR `media` LIKE '%" . $i . "%'")->fetchAll('assoc');
                }
                
                // i18n
                if(!in_array('i18n', $skip)){
                    $matches['i18n'][$i] = $this->connection->execute("SELECT `foreign_key` as `id`, `locale` FROM `i18n` WHERE `content` LIKE '%" . $i . "%'")->fetchAll('assoc');
                }
                
                // config
                if(!in_array('config', $skip)){
                    $matches['config'][$i] = $this->connection->execute("SELECT `id`, `label` FROM `config` WHERE `settings` LIKE '%" . $i . "%'")->fetchAll('assoc');
                }
            
            }
        }

        // get infos
        foreach($matches as $idx => $group){
            foreach($group as $key => $match){
                foreach($match as $m){
                    
                    // get infos
                    switch($idx){
                        case "nodes":
                            $structure = $this->connection->execute("SELECT `title` FROM `structures` WHERE `id` = :id", ['id' => $m['structure_id']])->fetch('assoc');
                            if(is_array($structure) && count($structure) > 0){
                                $used[$idx.':'.$key] = __d('be', 'Used in structure "%s"', $structure['title']);
                            }else{
                                $used[$idx.':'.$key] = __d('be', 'Used in a structure');
                            }
                            break;
                        case "elements":
                        case "i18n":
                            $locale = array_key_exists('locale', $m) ? ' (' . __d('be', 'Language') . ': ' . strtoupper($m['locale']) . ')' : '';
                            $element = $this->connection->execute("SELECT `code`, `internal`, `category_id` FROM `elements` WHERE `id` = :id", ['id' => $m['id']])->fetch('assoc');
                            if(is_array($element) && count($element) > 0){
                                if($link === true){
                                    $url = Router::url(['controller' => 'elements', 'action' => 'update', $element['code'], $element['category_id'], $m['id']]);
                                    if(array_key_exists('locale', $m)){
                                        $url .= '?locale=' . $m['locale'];
                                    }
                                    $short = ' <a href="' . $url . '" target="_blank"><i class="fa fa-external-link-square" aria-hidden="true"></i></a>';
                                }else{
                                    $short = '';
                                }
                                $type = array_key_exists($element['code'], $elements) ? $elements[$element['code']] : false;
                                if(is_array($type)){
                                    $used[$idx.':'.$key] = __d('be', 'Used in the element "%s" of the type "%s"%s%s', $element['internal'], $type['translations']['type'], $locale, $short);
                                }else{
                                    $used[$idx.':'.$key] = __d('be', 'Used in the element "%s"%s%s', $element['internal'], $locale, $short);
                                }
                            }else{
                                $used[$idx.':'.$key] = __d('be', 'Used in an element%s', $locale);
                            }
                            break;
                        case "config":
                            if(array_key_exists($m['label'], $config)){
                                $short = $link === true ? ' <a href="' . Router::url(['controller' => 'config', 'action' => 'index', $m['label']]) . '" target="_blank"><i class="fa fa-external-link-square" aria-hidden="true"></i></a>' : '';
                                $used[$idx.':'.$key] = __d('be', 'Used in configuration "%s"%s', $config[$m['label']]['name'], $short);
                            }else{
                                $used[$idx.':'.$key] = __d('be', 'Used in configuration');
                            }
                            break;
                        default:
                            $used[$idx.':'.$key] = __d('be', 'Used in unknow position');
                            break;
                    }
                }
            }
        }
        
        return count($used) > 0 ? $used : false;
        
    }

}
