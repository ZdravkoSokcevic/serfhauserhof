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

namespace Frontend\Controller;

use Cake\Event\Event;
use App\Controller\AppController as BaseController;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
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
        } catch (\Exception $e) {
            die($e->getMessage() . "\n");
        }

        // domain
        $this->set('domain', Router::url('/', true));

        // member?
        if($this->request->session()->read('Member') && $this->request->session()->read('Member') !== false){
            Configure::write('member', true);
            $this->request->session()->write('Member.timestamp', time());
        }

        // init
        $this->connection = ConnectionManager::get('default');

        // components
        $this->loadComponent('RequestHandler');

        // load images model
        if(!array_key_exists('special', $this->request->params)){
            $this->loadModel('Frontend.Images');
            $this->Images->locale($this->request->params['language']);
        }

        // load element model
        if(!array_key_exists('special', $this->request->params)){
            $this->loadModel('Frontend.Elements');
        }

        // custom configurations
        $config = [];
        $_c = $this->connection->execute("SELECT * FROM config ORDER BY label ASC")->fetchAll('assoc');
        if(is_array($_c)){
            foreach($_c as $__c){
                if(!empty($__c['settings'])){
                    $config[$__c['label']] = json_decode($__c['settings'], true);
                }
            }
        }

        // actual season
        $config['default']['season'] = 'wi';
       foreach($config as $l => $c){
           if(array_key_exists('summer-start', $c) && array_key_exists('winter-start', $c)){
               $ss = strtotime(date("Y") . substr($c['summer-start'], 4));
               $ws = strtotime(date("Y") . substr($c['winter-start'], 4));
               $now = time();
               if($ss && $ws){
                   $config[$l]['season'] = $now < $ss || $now >= $ws ? 'wi' : 'su';
               }else{ // fallback
                   $config[$l]['season'] = $now < strtotime(date("Y") . "-04-01") || $now >= strtotime(date("Y") . "-10-01") ? 'wi' : 'su';
               }
           }
       }

        // details
        foreach($config as $k => $v){
            foreach($v as $_k => $_v){
                if($this->hasDetails($_v) && !array_key_exists('special', $this->request->params)){
                    $settings = Configure::read('config.' . $k . '.fields.' . $_k);
                    if(is_array($settings)){
                        $config[$k][$_k] = $this->getDetails($_v, !array_key_exists('details', $settings) ? true : $settings['details']);
                    }
                }
            }
        }
        Configure::write('config', $config);

        // dialog?
        $dialog = false;
        if(array_key_exists('?', $this->request->params) && array_key_exists('dialog', $this->request->params['?'])){
            $dialog = $this->request->params['?']['dialog'] == 'true' ? true : false;
        }
        $this->set('dialog', $dialog);

        // get menu
        if(!array_key_exists('special', $this->request->params)){
            $this->set('menu', $this->getMenu());
        }

        // get breadcrumbs
        if(!array_key_exists('special', $this->request->params)){
            $this->set('breadcrumbs', $this->getBreadcrumbs());
        }

        // seo
        if(!array_key_exists('special', $this->request->params)){
            $this->set('seo', $this->getSeoStuff());
        }

        // cookie
        if(!array_key_exists('special', $this->request->params)){

            $hint = array_key_exists('hint', $_COOKIE) && $_COOKIE['hint'] == "false" ? false : true;
            $this->set('hint', $hint);

            // update
            if($hint === false){
               setcookie("hint", "false", time()+60*60*24*365, "/");
            }
        }

        // slideshow
        if(!array_key_exists('special', $this->request->params)){
            $this->set('slideshow', $this->getSlideshow());
        }
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

        // set layout
        if(array_key_exists('layout', $this->request->params)){
            $this->viewBuilder()->layout($this->request->params['layout']);
        }else{
            $this->viewBuilder()->layout($this->request->params['structure']['theme']);
        }

        // error?
        if($this->request->params['error']){
            $this->response->statusCode(404);
        }

    }

    public function getMenu($structure = false, $locale = false, $id = false){

        // init
        if(!$structure || strlen($structure) != 36){
            $structure = $this->request->params['structure']['id'];
        }
        if(!$locale){
            $locale = $this->request->params['language'];
        }
        if(!$id){
            $id = array_key_exists('node', $this->request->params) && is_array($this->request->params['node']) && array_key_exists('id', $this->request->params['node']) ? $this->request->params['node']['id'] : false;
        }

        // active/highlight flag
        $flags = [];
        if($id){
            $loops = 0;
            do{
                $node = $this->connection->execute("SELECT `id`, `parent_id` FROM `nodes` WHERE `id` = :id", ['id' => $id])->fetch('assoc');
                if(is_array($node) && count($node) > 0){
                    $flags[$node['id']] = $loops == 0 ? 'active' : 'highlight';
                    $id = !empty($node['parent_id']) ? $node['parent_id'] : false;
                    $loops++;
                }else{
                    $id = false;
                }
            }while($id !== false);
        }

        // get nodes
        $nodes = $this->__crawl('', $structure, $locale, true, true, $flags);

        return $nodes;
    }

    public function getBreadcrumbs($id = false, $locale = false){

        // init
        $bradcrumbs = [];
        if(!$locale){
            $locale = $this->request->params['language'];
        }
        if(!$id){
            $id = array_key_exists('node', $this->request->params) && is_array($this->request->params['node']) && array_key_exists('id', $this->request->params['node']) ? $this->request->params['node']['id'] : false;
        }

        // settings
        $settings = Configure::read('elements');

        if($id){
            do{
                $node = $this->connection->execute("SELECT `n`.`id`, `n`.`foreign_id`, `t`.`content`, `n`.`parent_id` FROM `nodes` as `n` LEFT JOIN `i18n` as `t` ON (`n`.`foreign_id` = `t`.`foreign_key`) WHERE `n`.`id` = :id AND `t`.`locale` = :locale AND `t`.`field` = 'title' AND `n`.`active` = 1 AND (`n`.`show_from` = '' OR `n`.`show_from` <= CURDATE()) AND (`n`.`show_to` = '' OR `n`.`show_to` > CURDATE())", ['id' => $id, 'locale' => $locale])->fetch('assoc');
                if(is_array($node) && count($node) > 0){
                    $_element = $this->connection->execute("SELECT `id`, `internal`, `code`, `valid_times` FROM `elements` WHERE `id` = :id AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE()) LIMIT 1", ['id' => $node['foreign_id']])->fetch('assoc');
                    if(is_array($_element) && array_key_exists($_element['code'], $settings) && array_key_exists('valid_times', $_element) && is_valid($_element['valid_times'])){
                        $bradcrumbs[] = array_merge($node, ['linkable' => array_key_exists('linkable', $settings[$_element['code']]) ? $settings[$_element['code']]['linkable'] : true]);
                        $id = !empty($node['parent_id']) ? $node['parent_id'] : false;
                    }
                }else{
                    $id = false;
                }
            }while($id !== false);
        }

        krsort($bradcrumbs);

        return $bradcrumbs;
    }

    public function hasDetails($media, $poss = false){
        $poss = !is_array($poss) ? ['image','element','node','category'] : $poss;
        if(is_string($media) && strpos($media,":") !== false){
            $check = json_decode($media);
            if(is_null($check)){
                $details = array_filter(explode(";", $media));
                foreach($details as $detail){
                    if(strpos($detail,":") !== false){
                        list($type, $id) = explode(":", $detail, 2);
                        if(in_array($type, $poss) && strlen($id) == 36){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function getSeoStuff(){
        // init
        $seo = [];

        if(array_key_exists('node', $this->request->params) && is_array($this->request->params['node']) && count($this->request->params['node']) > 0){

            // robot
            $index = array_key_exists('robots_index', $this->request->params['node']) && $this->request->params['node']['robots_index'] == 1 ? 'index' : 'noindex';
            $follow = array_key_exists('robots_follow', $this->request->params['node']) && $this->request->params['node']['robots_follow'] == 1 ? 'follow' : 'nofollow';
            $seo['robots'] = join(", ", [$index, $follow]);

            // canonical
            $correct = Router::url(['node' => 'node:' . $this->request->params['node']['id'], 'language' => $this->request->params['language']]);
            if($index == 'index' && $correct !== $_SERVER['REQUEST_URI']){
                $seo['canonical'] = $correct;
            }

        }

        return $seo;
    }

    public function getDetails($elements, $details = true){

        // init
        $res = [];
        $elements = array_filter(explode(";", $elements));

        foreach($elements as $element){

            // infos
            list($type, $id) = explode(":", $element);

            // details
            $res[] = [
                'org' => $element,
                'type' => $type,
                'id' => $id,
                'details' => $details ? $this->{'media' . ucfirst($type) . 'Details'}($id, $details) : false,
            ];

        }

        return $res;
    }

    public function getMediaDetails(array $media, $theme = false){

        // init
        $details = [];

        // theme
        try {
            $theme = $theme === false ? $this->request->params['structure']['theme'] : $theme;
        } catch (Exception $e){
            $theme = false;
        }

        // get details
        if(is_array($media) && array_key_exists($theme, $media)){
            foreach($media[$theme] as $position => $elements){

                // init
                $details[$position] = [];

                // elements
                $elements = array_filter(explode(";", $elements));

                // get infos
                foreach($elements as $pos => $element){

                    // infos
                    list($type, $id) = explode(":", $element);

                    // details
                    $details[$position][$pos] = [
                        'type' => $type,
                        'id' => $id,
                        'details' => $this->{'media' . ucfirst($type) . 'Details'}($id, true),
                    ];
                }
            }
        }

        return $details;
    }

    public function mediaImageDetails($id, $details = false){

        $query = $this->Images
        ->find()
        ->where(['Images.id' => $id])
        ->limit(1)
        ->formatResults(function ($results) {
            return $results->map(function ($row) {
                return $this->Images->afterFind($row);
            });
        });

        if($query->count()){
            $image = $query->first()->toArray();
        }else{
            $image = false;
        }

        return $image;
    }

    public function mediaElementDetails($id, $details = true){

        // get type
        $_code = $this->connection->execute("SELECT `code` FROM `elements` WHERE `id` = :id LIMIT 1", ['id' => $id])->fetch('assoc');
        $this->Elements->setup($_code['code'], $this->request);

        // fetch element
        $query = $this->Elements
        ->find()
        ->where(['Elements.id' => $id])
        ->limit(1)
        ->formatResults(function ($results) {
            return $results->map(function ($row) {
                return $this->Elements->afterFind($row);
            });
        });

        if($query->count()){
            $element = $query->first()->toArray();

            // media
            if($details === true || is_array($details)){
                if($details === true || (is_array($details) && in_array('media', $details))){
                    $element['media'] = $this->getMediaDetails($element['media']);
                }
                foreach($element as $k => $v){
                    if($details === true || (is_array($details) && in_array($k, $details))){
                        if($this->hasDetails($v)){
                            $element[$k] = $this->getDetails($v);
                        }
                    }
                }
            }

            // further infos
            if($element['code'] == 'special'){
                switch ($element['type']) {
                    case 'sitemap':
                    case 'search':
                        $element['_details'] = $this->getFurtherDetails($element['type'], $element);
                        break;
                    case 'lwd-bozen':
                        $element['_details'] = $this->getWeatherDetailsLWDBozen($element['type'], $element);
                        break;
                    case 'wunderground':
                        $element['_details'] = $this->getWeatherDetailsWunderground($element['type'], $element);
                        break;
                    case 'zamg':
                        $element['_details'] = $this->getWeatherDetailsZamg($element['type'], $element);
                        break;
                    default:
                        break;
                }
            }else if(in_array($element['code'], ['overview','pool'])){
                $element['_details'] = $this->getFurtherDetails($element['code'], $element);
            }

            // parse
            foreach(Configure::read('elements.' . $element['code'] . '.fields') as $field => $info){
                if(array_key_exists($field, $element) && array_key_exists('attr', $info) && is_array($info['attr']) && array_key_exists('type', $info['attr']) && $info['attr']['type'] == 'textarea'){
                    $element[$field] = $this->parseContent($element[$field]);
                }else if(array_key_exists($field, $element) && array_key_exists('attr', $info) && is_array($info['attr']) && array_key_exists('type', $info['attr']) && $info['attr']['type'] == 'file'){
                    $element[$field] = !is_array($element[$field]) ? json_decode($element[$field], true) : $element[$field];
                }
            }

        }else{
            $element = false;
        }

        return $element;
    }

    public function mediaNodeDetails($id, $details = true){
        $_node = $this->connection->execute("SELECT `foreign_id` as `id`, `route` FROM `nodes` WHERE `id` = :id LIMIT 1", ['id' => $id])->fetch('assoc');
        if(is_array($_node) && array_key_exists('id', $_node)){
            if($details === true || is_array($details)){
                $_element = $this->mediaElementDetails($_node['id'], false);
                return ['node' => $_node, 'element' => $_element];
            }else{
                return ['node' => $_node];
            }
        }
        return false;
    }

    public function mediaCategoryDetails($id, $details = false){
        $_category = $this->connection->execute("SELECT `id`, `parent_id`, `model`, `code`, `internal`, `special` FROM `categories` WHERE `id` = :id LIMIT 1", ['id' => $id])->fetch('assoc');
        if(is_array($_category) && count($_category) > 0){
            if($_category['model'] == 'elements'){
                $_settings = Configure::read('elements.' . $_category['code']);
                $_translations = [];
                if(is_array($_settings) && array_key_exists('categories', $_settings) && is_array($_settings['categories']) && in_array(true, $_settings['categories'], true)){
                    $__translations = $this->connection->execute("SELECT `field`, `content` FROM `i18n` WHERE `foreign_key` = :id AND `locale` = :locale", ['id' => $id, 'locale' => $this->request->params['language']])->fetchAll('assoc');
                    foreach($__translations as $k => $v){
                        if(array_key_exists($v['field'], $_settings['categories']) && $_settings['categories'][$v['field']]){
                            $_translations[$v['field']] = $this->parseContent($v['content']);
                        }
                    }
                }
                $_ids = [];
                $__ids = $this->connection->execute("SELECT `id` FROM `elements` WHERE `category_id` = :id AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE()) ORDER BY `sort`", ['id' => $id])->fetchAll('assoc');
                foreach($__ids as $i){
                    $_ids[] = $i['id'];
                }
            }else{
                $_translations = [];
                $__translations = $this->connection->execute("SELECT `field`, `content` FROM `i18n` WHERE `field` = :field AND `foreign_key` = :id AND `locale` = :locale", ['field' => 'seo', 'id' => $id, 'locale' => $this->request->params['language']])->fetchAll('assoc');
                foreach($__translations as $k => $v){
                    $_translations[$v['field']] = $this->parseContent($v['content']);
                }
                $_ids = [];
                $__ids = $this->connection->execute("SELECT `id` FROM `images` WHERE `category_id` = :id", ['id' => $id])->fetchAll('assoc');
                foreach($__ids as $i){
                    $_ids[] = $i['id'];
                }
            }

            // details
            if($details && $_category['model'] == 'elements' && in_array($_category['code'], ['xxx'])){
                $details = $this->getCategoryContentDetails($_ids, $_category);
            }

            return ['category' => $_category, 'contain' => $_ids, 'translations' => $_translations, 'content' => $details];
        }
        return [];
    }

    public function getCategoryContentDetails($ids, $category){
        $content = [];
        foreach($ids as $id){
            $content[$id] = $this->mediaElementDetails($id, true);
        }
        return $content;
    }

    public function getFurtherDetails($type, $element, $options = []){

        // init
        $details = false;

        // further details ;)
        switch($type){
            case "sitemap":
                if(array_key_exists('structure', $this->request->params) && is_array($this->request->params['structure']) && array_key_exists('id', $this->request->params['structure'])){
                    $details = $this->__crawl('', $this->request->params['structure']['id'], $this->request->params['language']);
                }
                break;
            case "search":
                if(array_key_exists('s', $this->request->query) && !empty($this->request->query['s'])){
                    $details = [
                        'term' => $this->request->query['s'],
                        'matches' => $this->__search($this->request->query['s'], $this->request->params['structure']['id'], $this->request->params['language'])
                    ];
                }
                break;
            case "pool":
            case "overview":
            case "element":

                // init
                $offers = [];
                $limit = false;
                $callbacks = [];
                $fetch = false;
                $code = false;
                $link = true;

                if($type == 'pool'){
                    $code = 'package';
                    $fetch = ['images'];
                    $callbacks = ['valid_times' => ['func' => 'times', 'params' => ['check' => true, 'format' => 'd.m.Y']]];
                    switch($element['type']){
                        case "custom":
                            if(array_key_exists('packages', $element) && is_array($element['packages']) && count($element['packages']) > 0){
                                foreach($element['packages'] as $p){
                                    $offers[] = $p['id'];
                                }
                            }
                            break;
                        case "category":
                            if(array_key_exists('category', $element) && is_array($element['category']) && array_key_exists(0, $element['category']) && is_array($element['category'][0]) && array_key_exists('details', $element['category'][0]) && is_array($element['category'][0]['details'])){
                                $offers = array_key_exists('contain', $element['category'][0]['details']) && is_array($element['category'][0]['details']['contain']) ? $element['category'][0]['details']['contain'] : $offers;
                            }
                            break;
                    }
                }else if($type == 'overview'){
                    switch($element['type']){
                        case "room":
                            $code = 'room';
                            $fetch = ['sketch'];
                            $category = array_key_exists('rooms', $element) && is_array($element['rooms']) && array_key_exists(0, $element['rooms']) ? $element['rooms'][0] : $category;
                            break;
                        case "room-total":
                            $code = 'room';
                            $rooms = $this->connection->execute("SELECT `id` FROM `elements` WHERE `code` = :code AND `active` = 1 ORDER BY `sort`", ['code' => $code])->fetchAll('assoc');
                            foreach($rooms as $room){
                                $offers[] = $room['id'];
                            }
                            break;
                        case "package":
                            $code = 'package';
                            $fetch = ['images'];
                            $callbacks = ['valid_times' => ['func' => 'times', 'params' => ['check' => true, 'format' => 'd.m.Y']]];
                            $category = array_key_exists('packages', $element) && is_array($element['packages']) && array_key_exists(0, $element['packages']) ? $element['packages'][0] : $category;
                            break;
                        case "treatment":
                            $link = false;
                            $code = 'treatment';
                            $category = array_key_exists('treatments', $element) && is_array($element['treatments']) && array_key_exists(0, $element['treatments']) ? $element['treatments'][0] : $category;
                            break;
                        default:
                            $category = false;
                            break;
                    }
                    $offers = is_array($category) && array_key_exists('details', $category) && is_array($category['details']) ? $category['details']['contain'] : $offers;
                }else{
                    $code = $element['details']['code'];
                    $offers = [$element['id']];
                    if($code == 'room'){
                        $fetch = ['images'];
                    }
                }

                if($code && is_array($offers) && count($offers) > 0){

                    // init
                    $ids = [];

                    // nodes
                    $nodes = [];
                    $keys = array_flip($offers);
                    if($link){
                        $_nodes = $this->connection->execute("SELECT `id`, `foreign_id` FROM `nodes` WHERE `foreign_id` IN ('" . join("','", $offers) . "') AND `structure_id` = :structure AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE())", ['structure' => $this->request->params['structure']['id']])->fetchAll('assoc');
                        if(is_array($_nodes)){
                            foreach($_nodes as $k => $v){
                                if(!array_key_exists($v['foreign_id'], $nodes)){
                                    $nodes[$v['foreign_id']] = $v['id'];
                                }
                                if(!in_array($v['foreign_id'], $ids)){
                                    $ids[$keys[$v['foreign_id']]] = $v['foreign_id'];
                                }
                            }
                        }
                        ksort($ids);
                    }else{
                        $ids = $offers;
                    }

                    // infos
                    $infos = [];
                    foreach($ids as $i => $e){
                        if($limit === false || count($infos) < $limit){

                            // element
                            $_element = $this->mediaElementDetails($e, $fetch);

                            if(is_valid($_element['valid_times'])){
                                $infos[$e] = $this->__processCallbacks($_element, $callbacks);
                            }
                        }
                        if(!array_key_exists($e, $infos)){
                            unset($ids[$i]);
                        }
                    }

                    // prices
                    if(!is_array($options) || !array_key_exists('prices', $options) || $options['prices'] == true){
                        $prices = $this->getPrices($ids, $code);
                    }else{
                        $prices = false;
                    }

                    // sort
                    $sort = $type !== 'pool' || $element['type'] != 'custom' ? true : false;
                    if($sort){
                        uasort($infos, function($a, $b) use ($type, $element) {
                            if(($type == 'overview' && $element['type'] == 'package') || $type == 'pool'){
                                $aS = $bS = 0;
                                foreach(['a', 'b'] as $letter){
                                    if(array_key_exists('valid_times', ${$letter}) && is_array(${$letter}['valid_times'])){
                                        foreach(${$letter}['valid_times'] as $valid_time){
                                            if(is_array($valid_time) && array_key_exists('from', $valid_time) && array_key_exists('to', $valid_time) && strtotime($valid_time['to']) > time()){
                                                if(${$letter.'S'} == 0 || strtotime($valid_time['from']) < ${$letter.'S'}){
                                                    ${$letter.'S'} = strtotime($valid_time['from']);
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($aS == $bS) {
                                    return strcmp($a['title'], $b['title']);
                                }
                                return ($aS < $bS) ? -1 : 1;
                            }else{
                                if ($a['sort'] == $b['sort']) {
                                    return strcmp($a['title'], $b['title']);
                                }
                                return ($a['sort'] < $b['sort']) ? -1 : 1;
                            }

                            return 0;
                        });
                    }

                    // overview navigation
                    $nav = false;
                    if($type == 'overview'){
                        $_nav = $this->connection->execute("SELECT `c`.`id`, `i`.`content` as `title`, `c`.`rel` FROM `categories` as `c` LEFT JOIN `i18n` as `i` ON (`c`.`id` = `i`.`foreign_key`) WHERE `c`.`model` = :model AND `c`.`code` = :code AND `i`.`field` = :field AND `i`.`locale` = :locale ORDER BY `c`.`sort`, `i`.`content`", ['model' => 'elements', 'code' => $element['type'], 'field' => 'title', 'locale' => $this->request->params['language']])->fetchAll('assoc');
                        $nav = [];
                        foreach($_nav as $n){
                            $nav[$n['id']] = $n;
                        }
                    }

                    // details
                    $details = ['infos' => $infos, 'nodes' => $nodes, 'prices' => $prices , 'nav' => $nav];
                }
                break;
            default:
                break;
        }

        return $details;
    }

    public function __crawl($parent, $structure, $locale, $nested = true, $visible = false, $flags = [], $details = true){

        // init
        $nodes = [];

        // settings
        $settings = Configure::read('elements');

        $visible = $visible === false ? '' : '`n`.`display` = 1 AND ';
        $_nodes = $this->connection->execute("SELECT `n`.`id`, `n`.`route`, `n`.`foreign_id`, `t`.`content`, `n`.`parent_id`, `n`.`settings` FROM `nodes` as `n` LEFT JOIN `i18n` as `t` ON (`n`.`foreign_id` = `t`.`foreign_key`) WHERE " . $visible . "`n`.`structure_id` = :id AND `t`.`locale` = :locale AND `t`.`field` = 'title' AND `n`.`active` = 1 AND (`n`.`show_from` = '' OR `n`.`show_from` <= CURDATE()) AND (`n`.`show_to` = '' OR `n`.`show_to` > CURDATE()) AND `n`.`parent_id` = :parent ORDER BY `n`.`position`", ['id' => $structure, 'locale' => $locale, 'parent' => $parent])->fetchAll('assoc');
        foreach($_nodes as $node){

            // active/valid element?
            $_element = $this->connection->execute("SELECT `id`, `internal`, `code`, `valid_times` FROM `elements` WHERE `id` = :id AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE()) LIMIT 1", ['id' => $node['foreign_id']])->fetch('assoc');
            if(is_array($_element) && array_key_exists($_element['code'], $settings) && array_key_exists('valid_times', $_element) && is_valid($_element['valid_times'])){

                // node details
                $node['settings'] = !empty($node['settings']) ? array_filter(json_decode($node['settings'], true)) : [];
                if(count($node['settings']) > 0){
                    foreach($node['settings'] as $k => $v){
                        if($this->hasDetails($v)){
                            $node['settings'][$k] = $this->getDetails($v, $details);
                        }
                    }
                }

                // element details
                if($details){
                    switch($_element['code']){
                        case "link":
                            $_details = $this->mediaElementDetails($_element['id'], true);
                            break;
                        default:
                            $_details = false;
                            break;
                    }
                }else{
                    $_details = false;
                }

                if($nested){
                    $node['type'] = $_element['code'];
                    $node['linkable'] = array_key_exists('linkable', $settings[$_element['code']]) ? $settings[$_element['code']]['linkable'] : true;
                    $node['active'] = $node['highlight'] = false;
                    if(is_array($flags)){
                        if(array_key_exists($node['id'], $flags)){
                            $node[$flags[$node['id']]] = true;
                        }
                    }
                    $node['element'] = $_element;
                    $node['details'] = $_details;
                    $node['children'] = $this->__crawl($node['id'], $structure, $locale, $nested, $visible, $flags, $details);
                    $nodes[] = $node;
                }else{
                    $node['type'] = $_element['code'];
                    $node['linkable'] = array_key_exists('linkable', $settings[$_element['code']]) ? $settings[$_element['code']]['linkable'] : true;
                    $node['details'] = $_details;
                    $nodes[] = $node;
                    $children = $this->__crawl($node['id'], $structure, $locale, $nested, $visible, $flags, $details);
                    if(count($children) > 0){
                        foreach($children as $child){
                            $nodes[] = $child;
                        }
                    }
                }
            }
        }
        return $nodes;
    }

    private function __search($term, $structure, $locale){

        // init
        $ids = $matches = $nodes = [];
        $setttings = Configure::read('elements');
        $structure = $this->connection->execute("SELECT `id`, `theme` FROM `structures` WHERE `id` = :id", ['id' => $structure])->fetch('assoc');

        if(count($structure) > 0){

            // get nodes
            $_nodes = $this->connection->execute("SELECT `id`, `foreign_id` FROM `nodes` WHERE `structure_id` = :id AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE())", ['id' => $structure['id']])->fetchAll('assoc');
            foreach($_nodes as $n){
                if(!array_key_exists($n['foreign_id'], $nodes)){
                    $nodes[$n['id']] = $n['foreign_id'];
                }
            }

            // prepare term
            $terms = [
                'placeholder' => [],
                'values' => [],
                'skip' => ['*', ';'],
            ];
            $_parts = array_filter(explode(" ", $term));
            foreach($_parts as $k => $v){
                if(!in_array($v, $terms['skip'])){
                    $key = 'placeholder_' . count($terms['placeholder']);
                    $terms['placeholder'][$key] = ':' . $key;
                    $terms['values'][$key] = '%' . $v . '%';
                }
            }

            // get ids
            foreach($nodes as $node => $element){
                $this->__searchingIds($ids, $terms, $element, false, $nodes, $setttings, $structure, $locale);
            }

            // matches
            foreach($nodes as $node => $element){
                if(array_key_exists($element, $ids)){
                    $matches[] = [
                        'node' => $node,
                        'matches' => $ids[$element],
                        'details' => $this->mediaElementDetails($element, false),
                    ];
                }
            }

            // sort
            usort($matches, function($a, $b){
                return strcmp($a['details']['headline'], $b['details']['headline']);
            });

        }

        return $matches;
    }

    private function __searchingIds(&$ids, $terms, $element, $root, $nodes, $setttings, $structure, $locale, $level = 0, $maxDepth = 1){

        // init
        $root = $root === false ? $element : $root;

        if($level <= $maxDepth){
            $_element = $this->connection->execute("SELECT `id`, `code`, `fields`, `media`, `valid_times` FROM `elements` WHERE `id` = :id AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE())", ['id' => $element])->fetch('assoc');
            if(is_array($_element) && count($_element) > 0 && is_valid($_element['valid_times'])){
                if(array_key_exists('active', $setttings[$_element['code']]) && $setttings[$_element['code']]['active'] == true && array_key_exists('searchable', $setttings[$_element['code']])){
                    if(is_array($setttings[$_element['code']]['searchable']) && !array_key_exists('func', $setttings[$_element['code']]['searchable']) && count($setttings[$_element['code']]['searchable']) > 0){
                        $_i18n = $this->connection->execute("SELECT `id` FROM `i18n` WHERE `foreign_key` = :id AND `field` IN ('" . join("','", $setttings[$_element['code']]['searchable']) . "') AND `locale` = :locale AND (`content` LIKE " . join(" OR `content` LIKE ", $terms['placeholder']) . ")", array_merge(['id' => $_element['id'], 'locale' => $locale], $terms['values']))->fetchAll('assoc');
                        if(is_array($_i18n) && count($_i18n) > 0){
                            if(!in_array($root, $ids)){
                                if(!array_key_exists($root, $ids)){
                                    $ids[$root] = [];
                                }
                                $ids[$root][] = $element;
                            }
                        }else{

                            // event
                            $fields = !empty($_element['fields']) ? json_decode($_element['fields'], true) : [];
                            $media = !empty($_element['media']) ? json_decode($_element['media'], true) : [];
                            $media = array_key_exists($structure['theme'], $media) ? $media[$structure['theme']] : [];

                            // check
                            $all = [];
                            foreach(['fields', 'media'] as $v){
                                foreach(${$v} as $c){
                                    $p = array_filter(explode(";", $c));
                                    foreach($p as $i){
                                        if(strpos($i, ":") !== false){
                                            list($code, $id) = explode(":", $i, 2);
                                            if(in_array($code, ['element']) && !in_array($id, $all)){
                                                $all[] = $id;
                                            }
                                        }
                                    }
                                }
                            }

                            // search ids
                            foreach($all as $e){
                                $this->__searchingIds($ids, $terms, $e, $root, $nodes, $setttings, $structure, $locale, $level + 1, $maxDepth);
                            }
                        }
                    }else if(is_array($setttings[$_element['code']]['searchable']) && array_key_exists('func', $setttings[$_element['code']]['searchable']) && array_key_exists('settings', $setttings[$_element['code']]['searchable']) && method_exists($this, '__search' . ucfirst($setttings[$_element['code']]['searchable']['func']))){
                        $this->{'__search' . ucfirst($setttings[$_element['code']]['searchable']['func'])}($ids, $terms, $_element, $root, $nodes, $setttings, $structure, $locale, $level, $maxDepth);
                    }
                }
            }
        }

    }

	public function getCode($id){
		$element = $this->connection->execute("SELECT `code` FROM `elements` WHERE `id` = :id", ['id' => $id])->fetch('assoc');
		if(is_array($element) && count($element) > 0){
			return $element['code'];
		}
		return false;
	}

    public function getContent($id, $code, $callbacks = [], $settingOptions = []){

        // element setup
        $this->Elements->setup($code, $this->request);

        // fetch page
        $query = $this->Elements
        ->find()
        ->where(['Elements.id' => $id])
        ->limit(1)
        ->formatResults(function ($results) {
            return $results->map(function ($row) {
                return $this->Elements->afterFind($row);
            });
        });
        $content = $query->first()->toArray();

        // media
        $content['media'] = $this->getMediaDetails($content['media']);
        foreach($content as $k => $v){
            if($this->hasDetails($v)){
                $content[$k] = $this->getDetails($v);
            }
        }

        // parse
        foreach(Configure::read('elements.' . $code . '.fields') as $field => $info){
            if(array_key_exists($field, $content) && array_key_exists('attr', $info) && is_array($info['attr']) && array_key_exists('type', $info['attr']) && $info['attr']['type'] == 'textarea'){
                $content[$field] = $this->parseContent($content[$field]);
            }
        }

        // settings
        $content['_settings'] = false;
        if(is_array($settingOptions) && array_key_exists('selection', $settingOptions) && array_key_exists('subselection', $settingOptions) && array_key_exists($code, Configure::read('elements')) && Configure::read('elements.' . $code . '.active') && array_key_exists('settings', Configure::read('elements.' . $code)) && is_array(Configure::read('elements.' . $code . '.settings')) ){
            $settings = Configure::read('elements.' . $code);

            $settingsTable = TableRegistry::get('Frontend.Settings');
            $settingsTable->setup($code, $this->request);

            // fetch settings
            $query = $settingsTable
            ->find('translations')
            ->where(['Settings.selection' => $settingOptions['selection'], 'Settings.subselection' => $settingOptions['subselection']])
            ->limit(1)
            ->formatResults(function ($results) use($settings, $settingsTable) {
                return $results->map(function ($row) use($settings, $settingsTable) {
                    return $settingsTable->afterFind($row, $settings);
                });
            });

            if($query->count() == 1){

                $settings = $query->first()->toArray();

                // media
                foreach($settings as $k => $v){
                    if($this->hasDetails($v)){
                        $settings[$k] = $this->getDetails($v);
                    }
                }

                // parse
                foreach(Configure::read('elements.' . $code . '.settings.fields') as $field => $info){
                    if(array_key_exists($field, $settings) && array_key_exists('attr', $info) && is_array($info['attr']) && array_key_exists('type', $info['attr']) && $info['attr']['type'] == 'textarea'){
                        $settings[$field] = $this->parseContent($settings[$field]);
                    }
                }

                $content['_settings'] = $settings;

            }
        }

        // callbacks
        if(!array_key_exists('valid_times', $callbacks)){
            $callbacks['valid_times'] = ['func' => 'times', 'params' => ['check' => true, 'format' => 'd.m.Y']];
        }
        $content = $this->__processCallbacks($content, $callbacks);

        return $content;
    }

    /*
     * return array
     *     values -> prices grouped by season, element, draft, option
     *     drafts -> all used drafts
     *     connections -> the elments the prices belongs to (f.e. packages for packages prices)
     *     options -> all price options
     *     elements -> all elements the prices referes to (f.e. package prices for packages refer to rooms)
     *     seasons -> all seasons
     *
     */

    public function getPrices($id, $code){

        // init
        $_id = $id;
        $id = [];
        if(is_array($_id)){
            foreach($_id as $k => $v){ $id[] = $v; }
        }else{
            $id = [$_id];
        }
        $settings = Configure::read('elements.' . $code . '.prices');
        $check_for_season_links = false;
        $infos = [
            'global' => '_global'
        ];
        $prices = $seasons = $season_links = $containers = $elements = $season_ids = $drafts = $draft_ids = $connections = $ranges = [];
        $used = [
            'connection' => [],
            'element' => [],
            'draft' => []
        ];

        // seasons
        if(is_array($settings) && array_key_exists('seasons', $settings) && array_key_exists('active', $settings['seasons']) && $settings['seasons']['active'] === true){

            // season links
            $check_for_season_links = array_key_exists('seasons', $settings) && is_array($settings['seasons']) && array_key_exists('link', $settings['seasons']) && is_array($settings['seasons']['link']) && array_key_exists('code', $settings['seasons']['link']) && !empty($settings['seasons']['link']['code']) ? $settings['seasons']['link']['code'] : false;

            // draft per season
            $infos['drafts-per-season'] = [];

            $related_seasons = array_key_exists('rel', $settings['seasons']) && is_string($settings['seasons']['rel']) ? $settings['seasons']['rel'] : false;
            $_seasons = $this->connection->execute("SELECT `s`.`id`, `s`.`internal`, `s`.`container`, `s`.`link`, `t`.`valid_from`, `t`.`valid_to` FROM `season_times` as `t` LEFT JOIN `seasons` as `s` ON (`s`.`id` = `t`.`season_id`) WHERE `s`.`code` = :code AND `t`.`valid_to` >= :to ORDER BY `t`.`valid_from` ASC", ['code' => $related_seasons ? $related_seasons : $code, 'to' => date("Y-m-d")])->fetchAll('assoc');

            foreach($_seasons as $season){

                // init
                if($season['id'] && !array_key_exists($season['id'], $seasons)){

                    // init
                    $link = !empty($season['link']) ? substr($season['link'], 8) : false;

                    // add container
                    if(!in_array($season['container'], $containers)){
                        $containers[] = $season['container'];
                    }

                    // draft per season
                    if(!array_key_exists($season['id'], $infos['drafts-per-season'])){
                        $infos['drafts-per-season'][$season['id']] = [];
                    }

                    // title/content
                    $translations = [];
                    if(array_key_exists('fields', $settings['seasons']) && is_array($settings['seasons']['fields'])){
                        $_i18n = $this->connection->execute("SELECT `field`, `content` FROM `i18n` WHERE `foreign_key` = :id AND `locale` = :locale", ['id' => $season['id'], 'locale' => $this->request->params['language']])->fetchAll('assoc');
                        foreach($_i18n as $t){
                            if(array_key_exists($t['field'], $settings['seasons']['fields']) && $settings['seasons']['fields'][$t['field']]){
                                $translations[$t['field']] = $this->parseContent($t['content']);
                            }
                        }
                    }

                    $seasons[$season['id']] = [
                        'id' => $season['id'],
                        'internal' => $season['internal'],
                        'container' => $season['container'],
                        'translations' => $translations,
                        'link' => $link,
                        'times' => []
                    ];

                    if($check_for_season_links && $link){
                        $season_links[$season['id']] = $link;
                    }

                    $season_ids[] = $season['id'];
                }

                // add time
                $seasons[$season['id']]['times'][] = [
                    'from' => strtotime($season['valid_from']),
                    'to' => strtotime($season['valid_to']),
                ];

            }
        }else{
            $seasons = false;
        }

        // get linked element infos
        if($check_for_season_links && count($season_links) > 0){
            $nodes = [];
            foreach($this->connection->execute("SELECT `id`, `foreign_id` FROM `nodes` WHERE `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE()) AND `structure_id` = :structure", ['structure' => $this->request->params['structure']['id']])->fetchAll('assoc') as $k => $v){
                if(!array_key_exists($v['foreign_id'], $nodes)){
                    $nodes[$v['foreign_id']] = $v['id'];
                }
            }
            foreach($season_links as $k => $v){
                $season_links[$k] = ['infos' => $this->mediaElementDetails($v, false), 'node' => array_key_exists($v, $nodes) ? $nodes[$v] : false];
            }
        }

        // prices
        if($seasons === false){
            $_prices = $this->connection->execute("SELECT `foreign_id`, `foreign_model`, `foreign_code`, `season_id`, `price_draft_id`, `option`, `element`, `value`, `flag` FROM `prices` WHERE `foreign_code` = :code AND `foreign_id` IN ('" . join("','", $id) . "')", ['code' => $code])->fetchAll('assoc');
        }else{
            $_prices = $this->connection->execute("SELECT `foreign_id`, `foreign_model`, `foreign_code`, `season_id`, `price_draft_id`, `option`, `element`, `value`, `flag` FROM `prices` WHERE `foreign_code` = :code AND `foreign_id` IN ('" . join("','", $id) . "') AND `season_id` IN ('" . join("','", $season_ids) . "')", ['code' => $code])->fetchAll('assoc');
        }

        foreach($_prices as $price){

            // init
            $keys = [
                'foreign' => $price['foreign_id'],
                'season' => $seasons === false ? false : $price['season_id'],
                'element' => $price['element'] != 'false' ? $price['element'] : false,
                'draft' => $price['price_draft_id'],
                'option' => $price['option'] != 'false' ? $price['option'] : false,
            ];

            // draft per season
            if($keys['season'] && array_key_exists($keys['season'], $infos['drafts-per-season']) && !in_array($price['price_draft_id'], $infos['drafts-per-season'][$keys['season']])){
                if($keys['option'] !== false){
                    if(!array_key_exists($keys['option'], $infos['drafts-per-season'][$keys['season']])){
                        $infos['drafts-per-season'][$keys['season']][$keys['option']] = [];
                    }
                    if(!in_array($price['price_draft_id'], $infos['drafts-per-season'][$keys['season']][$keys['option']])){
                        $infos['drafts-per-season'][$keys['season']][$keys['option']][] = $price['price_draft_id'];
                    }
                }else{
                    if(!in_array($price['price_draft_id'], $infos['drafts-per-season'][$keys['season']])){
                        $infos['drafts-per-season'][$keys['season']][] = $price['price_draft_id'];
                    }
                }
            }

            // values
            $deep = '';
            if($seasons === false || array_key_exists($keys['season'], $seasons)){
                foreach($keys as $key){
                    if($key !== false){
                        eval('if(!array_key_exists("' . $key . '", $prices' . $deep . ')){ $prices' . $deep . '["' . $key . '"] = []; }');
                        $deep .= '["' . $key . '"]';
                    }
                }
            }
            eval('$prices' . $deep . '[\''.$price['flag'].'\'] = [\'value\' => ' . $price['value'] . '];');

            // price ranges
            if(!array_key_exists($price['foreign_id'], $ranges)){
                $ranges[$price['foreign_id']] = [];
                $ranges[$price['foreign_id']][$infos['global']] = [
                    'min' => [
                        'value' => false,
                        'season' => false,
                        'draft' => false,
                        'option' => false
                    ],
                    'max' => [
                        'value' => false,
                        'season' => false,
                        'draft' => false,
                        'option' => false
                    ],
                ];
                if($seasons !== false){
                    foreach($containers as $container){
                        $ranges[$price['foreign_id']][$container] = [
                            'min' => [
                                'value' => false,
                                'season' => false,
                                'draft' => false,
                                'option' => false
                            ],
                            'max' => [
                                'value' => false,
                                'season' => false,
                                'draft' => false,
                                'option' => false
                            ],
                        ];
                    }
                }
            }

            // used per connection
            if(!array_key_exists($price['foreign_id'], $used['connection'])){
                $used['connection'][$price['foreign_id']] = [
                    'drafts' => [],
                    'options' => [],
                    'elements' => [],
                ];
            }

            $skey = $seasons === false ? $infos['global'] : $seasons[$price['season_id']]['container'];
            if(!array_key_exists($infos['global'], $used['connection'][$price['foreign_id']]['drafts'])){
                $used['connection'][$price['foreign_id']]['drafts'][$infos['global']] = [];
                $used['connection'][$price['foreign_id']]['options'][$infos['global']] = [];
                $used['connection'][$price['foreign_id']]['elements'][$infos['global']] = [];
            }
            if(!array_key_exists($skey, $used['connection'][$price['foreign_id']]['drafts'])){
                $used['connection'][$price['foreign_id']]['drafts'][$skey] = [];
                $used['connection'][$price['foreign_id']]['options'][$skey] = [];
                $used['connection'][$price['foreign_id']]['elements'][$skey] = [];
            }

            if(!in_array($price['price_draft_id'], $used['connection'][$price['foreign_id']]['drafts'][$infos['global']])){
                $used['connection'][$price['foreign_id']]['drafts'][$infos['global']][] = $price['price_draft_id'];
            }
            if(!in_array($price['price_draft_id'], $used['connection'][$price['foreign_id']]['drafts'][$skey])){
                $used['connection'][$price['foreign_id']]['drafts'][$skey][] = $price['price_draft_id'];
            }

            if($price['option'] != 'false' && !in_array($price['option'], $used['connection'][$price['foreign_id']]['options'][$infos['global']])){
                $used['connection'][$price['foreign_id']]['options'][$infos['global']][] = $price['option'];
            }
            if($price['option'] != 'false' && !in_array($price['option'], $used['connection'][$price['foreign_id']]['options'][$skey])){
                $used['connection'][$price['foreign_id']]['options'][$skey][] = $price['option'];
            }

            // used per draft
            if($price['option'] != 'false'){

                if(!array_key_exists($price['price_draft_id'], $used['draft'])){
                    $used['draft'][$price['price_draft_id']] = [
                        'options' => [],
                    ];
                }

                if(!array_key_exists($infos['global'], $used['draft'][$price['price_draft_id']]['options'])){
                    $used['draft'][$price['price_draft_id']]['options'][$infos['global']] = [];
                }
                if(!array_key_exists($skey, $used['draft'][$price['price_draft_id']]['options'])){
                    $used['draft'][$price['price_draft_id']]['options'][$skey] = [];
                }

                if(!in_array($price['option'], $used['draft'][$price['price_draft_id']]['options'][$infos['global']])){
                    $used['draft'][$price['price_draft_id']]['options'][$infos['global']][] = $price['option'];
                }
                if(!in_array($price['option'], $used['draft'][$price['price_draft_id']]['options'][$skey])){
                    $used['draft'][$price['price_draft_id']]['options'][$skey][] = $price['option'];
                }

            }

            if($price['element'] != 'false'){

                // used per element
                if(!array_key_exists($price['element'], $used['element'])){
                    $used['element'][$price['element']] = [
                        'drafts' => [],
                        'options' => [],
                    ];
                }

                if(!array_key_exists($infos['global'], $used['element'][$price['element']]['drafts'])){
                    $used['element'][$price['element']]['drafts'][$infos['global']] = [];
                    $used['element'][$price['element']]['options'][$infos['global']] = [];
                }
                if(!array_key_exists($skey, $used['element'][$price['element']]['drafts'])){
                    $used['element'][$price['element']]['drafts'][$skey] = [];
                    $used['element'][$price['element']]['options'][$skey] = [];
                }

                if(!in_array($price['price_draft_id'], $used['element'][$price['element']]['drafts'][$infos['global']])){
                    $used['element'][$price['element']]['drafts'][$infos['global']][] = $price['price_draft_id'];
                }
                if(!in_array($price['price_draft_id'], $used['element'][$price['element']]['drafts'][$skey])){
                    $used['element'][$price['element']]['drafts'][$skey][] = $price['price_draft_id'];
                }

                if($price['option'] != 'false' && !in_array($price['option'], $used['element'][$price['element']]['options'][$infos['global']])){
                    $used['element'][$price['element']]['options'][$infos['global']][] = $price['option'];
                }
                if($price['option'] != 'false' && !in_array($price['option'], $used['element'][$price['element']]['options'][$skey])){
                    $used['element'][$price['element']]['options'][$skey][] = $price['option'];
                }

                // used element per connection
                if(!in_array($price['element'], $used['connection'][$price['foreign_id']]['elements'][$infos['global']])){
                    $used['connection'][$price['foreign_id']]['elements'][$infos['global']][] = $price['element'];
                }
                if(!in_array($price['element'], $used['connection'][$price['foreign_id']]['elements'][$skey])){
                    $used['connection'][$price['foreign_id']]['elements'][$skey][] = $price['element'];
                }

                // "count" elements
                if(!in_array($price['element'], $elements)){
                    $element = $this->connection->execute("SELECT `i`.`content`, `e`.`sort`, `e`.`fields` FROM `elements` as `e` LEFT JOIN `i18n` as `i` ON `e`.`id` = `i`.`foreign_key` WHERE `e`.`active` = 1 AND (`e`.`show_from` = '' OR `e`.`show_from` <= CURDATE()) AND (`e`.`show_to` = '' OR `e`.`show_to` > CURDATE()) AND `e`.`id` = :id AND `i`.`locale` = :locale AND `i`.`field` = 'title' LIMIT 1", ['id' => $price['element'], 'locale' => $this->request->params['language']])->fetch('assoc');
                    $node = $this->connection->execute("SELECT `id` FROM `nodes` WHERE `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE()) AND `foreign_id` = :id AND `structure_id` = :structure LIMIT 1", ['id' => $price['element'], 'structure' => $this->request->params['structure']['id']])->fetch('assoc');
                    if(is_array($element) && count($element) > 0){
                        $elements[$price['element']] = [
                            'title' => array_key_exists('content', $element) ? $element['content'] : '',
                            'fields' => !empty($element['fields']) ? @json_decode($element['fields'], true) : [],
                            'node' => is_array($node) && array_key_exists('id', $node) ? $node['id'] : false,
                            'sort' => array_key_exists('sort', $element) ? $element['sort'] : 0,
                            'used' => [
                                'drafts' => [],
                                'options' => []
                            ]
                        ];
                    }
                }
            }

            // max/min
            if($ranges[$price['foreign_id']][$infos['global']]['min']['value'] == false || $price['value'] < $ranges[$price['foreign_id']][$infos['global']]['min']['value']){
                $ranges[$price['foreign_id']][$infos['global']]['min']['value'] = $price['value'];
                $ranges[$price['foreign_id']][$infos['global']]['min']['season'] = strlen($price['season_id']) == 36 ? $price['season_id'] : false;
                $ranges[$price['foreign_id']][$infos['global']]['min']['draft'] = $price['price_draft_id'];
                $ranges[$price['foreign_id']][$infos['global']]['min']['option'] = $price['option'] != 'false' ? $price['option'] : false;
            }

            if($ranges[$price['foreign_id']][$infos['global']]['max']['value'] == false || $price['value'] > $ranges[$price['foreign_id']][$infos['global']]['max']['value']){
                $ranges[$price['foreign_id']][$infos['global']]['max']['value'] = $price['value'];
                $ranges[$price['foreign_id']][$infos['global']]['max']['season'] = strlen($price['season_id']) == 36 ? $price['season_id'] : false;
                $ranges[$price['foreign_id']][$infos['global']]['max']['draft'] = $price['price_draft_id'];
                $ranges[$price['foreign_id']][$infos['global']]['max']['option'] = $price['option'] != 'false' ? $price['option'] : false;
            }

            if($ranges[$price['foreign_id']][$skey]['min']['value'] == false || $price['value'] < $ranges[$price['foreign_id']][$skey]['min']['value']){
                $ranges[$price['foreign_id']][$skey]['min']['value'] = $price['value'];
                $ranges[$price['foreign_id']][$skey]['min']['season'] = strlen($price['season_id']) == 36 ? $price['season_id'] : false;
                $ranges[$price['foreign_id']][$skey]['min']['draft'] = $price['price_draft_id'];
                $ranges[$price['foreign_id']][$skey]['min']['option'] = $price['option'] != 'false' ? $price['option'] : false;
            }

            if($ranges[$price['foreign_id']][$skey]['max']['value'] == false || $price['value'] > $ranges[$price['foreign_id']][$skey]['max']['value']){
                $ranges[$price['foreign_id']][$skey]['max']['value'] = $price['value'];
                $ranges[$price['foreign_id']][$skey]['max']['season'] = strlen($price['season_id']) == 36 ? $price['season_id'] : false;
                $ranges[$price['foreign_id']][$skey]['max']['draft'] = $price['price_draft_id'];
                $ranges[$price['foreign_id']][$skey]['max']['option'] = $price['option'] != 'false' ? $price['option'] : false;
            }

            // draft ids
            if(!in_array($price['price_draft_id'], $draft_ids)){
                $draft_ids[] = $price['price_draft_id'];
            }

            // connections
            if(!array_key_exists($price['foreign_id'], $connections)){
                $connections[$price['foreign_id']] = [
                    'id' => $price['foreign_id'],
                    'model' => $price['foreign_model'],
                    'code' => $price['foreign_code'],
                    'ranges' => false,
                    'used' => [
                        'drafts' => [],
                        'options' => [],
                        'elements' => [],
                    ],
                ];
            }
        }

        // add range/used to connection
        foreach($connections as $k => $v){
            if(array_key_exists($k, $ranges)){
                $connections[$k]['ranges'] = $ranges[$k];
            }
            if(array_key_exists($k, $used['connection'])){
                $connections[$k]['used']['drafts'] = $used['connection'][$k]['drafts'];
                $connections[$k]['used']['options'] = $used['connection'][$k]['options'];
                $connections[$k]['used']['elements'] = $used['connection'][$k]['elements'];
            }
        }

        // add used to elements
        foreach($elements as $k => $v){
            if(array_key_exists($k, $used['element'])){
                $elements[$k]['used']['drafts'] = $used['element'][$k]['drafts'];
                $elements[$k]['used']['options'] = $used['element'][$k]['options'];
            }
        }

        // get drafts
        if(count($draft_ids) > 0){
            $_drafts = $this->connection->execute("SELECT `id`, `internal` FROM `price_drafts` WHERE `code` = :code AND `id` IN ('" . join("','", $draft_ids) . "') ORDER BY `sort`", ['code' => $code])->fetchAll('assoc');
            foreach($_drafts as $draft){

                // title/caption
                $translations = [];
                if(array_key_exists('drafts', $settings) && is_array($settings['drafts']) && array_key_exists('fields', $settings['drafts']) && is_array($settings['drafts']['fields'])){
                    $_i18n = $this->connection->execute("SELECT `field`, `content` FROM `i18n` WHERE `foreign_key` = :id AND `locale` = :locale", ['id' => $draft['id'], 'locale' => $this->request->params['language']])->fetchAll('assoc');
                    foreach($_i18n as $t){
                        if(array_key_exists($t['field'], $settings['drafts']['fields']) && $settings['drafts']['fields'][$t['field']]){
                            $translations[$t['field']] = $this->parseContent($t['content']);
                        }
                    }
                }

                $drafts[$draft['id']] = [
                    'id' => $draft['id'],
                    'internal' => $draft['internal'],
                    'translations' => $translations,
                    'used' => [
                        'options' => array_key_exists($draft['id'], $used['draft']) ? $used['draft'][$draft['id']]['options'] : []
                    ]
                ];

            }
        }

        // get options
        $options = is_array($settings) && array_key_exists('drafts', $settings) && is_array($settings['drafts']) && array_key_exists('options', $settings['drafts']) && is_array($settings['drafts']['options']) ? $settings['drafts']['options'] : [];

        // drafts per options
        if(is_array($options) && count($options) > 0){
            $infos['drafts-per-option'] = [];
            foreach($containers as $container){
                $infos['drafts-per-option'][$container] = [];
                foreach($drafts as $draft){
                    if(array_key_exists($container, $draft['used']['options'])){
                        foreach($draft['used']['options'][$container] as $o){
                            if(!array_key_exists($o, $infos['drafts-per-option'][$container])){
                                $infos['drafts-per-option'][$container][$o] = [];
                            }
                            $infos['drafts-per-option'][$container][$o][] = $draft['id'];
                        }
                    }
                }
            }
        }

        // elements per draft/season
        if(count($id) == 1 && is_array($elements) && count($elements) > 0){
            $infos['elements-per-draft-and-season'] = [];
            foreach($drafts as $draft){
                $infos['elements-per-draft-and-season'][$draft['id']] = [];
                foreach($seasons as $season){
                    $infos['elements-per-draft-and-season'][$draft['id']][$season['id']] = [];
                    foreach($elements as $eid => $element){
                        if(!in_array($draft['id'], $infos['elements-per-draft-and-season'][$draft['id']][$season['id']]) && array_key_exists($season['id'], $prices[$id[0]]) && array_key_exists($eid, $prices[$id[0]][$season['id']]) && array_key_exists($draft['id'], $prices[$id[0]][$season['id']][$eid])){
                            $infos['elements-per-draft-and-season'][$draft['id']][$season['id']][] = $draft['id'];
                        }
                    }
                }
            }
        }

        // sort elements
        if(count($elements) > 1){
            uasort($elements, function($a, $b){
                if ($a['sort'] == $b['sort']) {
                    return strcmp($a['title'], $b['title']);
                }
                return ($a['sort'] < $b['sort']) ? -1 : 1;
            });
        }

        return ['values' => $prices, 'drafts' => $drafts, 'connections' => $connections, 'options' => $options, 'elements' => $elements, 'seasons' => $seasons, 'season_links' => $season_links, 'containers' => $containers, 'infos' => $infos];
    }

    public function getSlideshow(){

        // init
        $res = false;
        $slideshow = Configure::read('config.default.slideshow');

        // get content
        if(is_array($slideshow) && array_key_exists(0, $slideshow) && is_array($slideshow[0]) && array_key_exists('details', $slideshow[0]) && is_array($slideshow[0]['details']) && array_key_exists('contain', $slideshow[0]['details'])){
            $res = [];
            foreach($slideshow[0]['details']['contain'] as $category){
                $__content = $this->mediaElementDetails($category, ['images']);
                if(is_array($__content) && array_key_exists('images', $__content) && is_array($__content['images']) && count($__content['images']) > 0){
                    $res[$category] = $__content;
                }
            }
        }

        // sort
        if(is_array($res) && count($res) > 1){
            uasort($res, function($a, $b){
                if ($a['sort'] == $b['sort']) {
                    return strcmp($a['title'], $b['title']);
                }
                return ($a['sort'] < $b['sort']) ? -1 : 1;
            });
        }

        return $res;
    }

    public function getRooms(){

        // init
        $ids = [];
        $rooms = [];

        // nodes
        $nodes = $this->connection->execute("SELECT `foreign_id` FROM `nodes` WHERE `structure_id` = :structure AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE())", ['structure' => $this->request->params['structure']['id']])->fetchAll('assoc');
        if(is_array($nodes)){
            foreach($nodes as $node){
                if(!in_array($node['foreign_id'], $ids)){
                    $ids[] = $node['foreign_id'];
                }
            }
        }

        // elements
        $elements = $this->connection->execute("SELECT `e`.`id`, `t`.`content` FROM `elements` as `e` LEFT JOIN `i18n` as `t` ON `e`.`id` = `t`.`foreign_key` WHERE `t`.`locale` = :locale AND `t`.`field` = :field AND `e`.`id` IN ('" . join("','", $ids) . "') AND `e`.`code` = :code AND `e`.`active` = 1 AND (`e`.`show_from` = '' OR `e`.`show_from` <= CURDATE()) AND (`e`.`show_to` = '' OR `e`.`show_to` > CURDATE()) ORDER BY `t`.`content`", ['locale' => $this->request->params['language'], 'field' => 'title', 'code' => 'room'])->fetchAll('assoc');
        if(is_array($elements)){
            foreach($elements as $element){
                $rooms[$element['id']] = $element['content'];
            }
        }

        return $rooms;
    }

    public function getPackages(){

        // init
        $ids = [];
        $packages = [];

        // nodes
        $nodes = $this->connection->execute("SELECT `foreign_id` FROM `nodes` WHERE `structure_id` = :structure AND `active` = 1 AND (`show_from` = '' OR `show_from` <= CURDATE()) AND (`show_to` = '' OR `show_to` > CURDATE())", ['structure' => $this->request->params['structure']['id']])->fetchAll('assoc');
        if(is_array($nodes)){
            foreach($nodes as $node){
                if(!in_array($node['foreign_id'], $ids)){
                    $ids[] = $node['foreign_id'];
                }
            }
        }

        // elements
        $elements = $this->connection->execute("SELECT `e`.`id`, `t`.`content`, `e`.`valid_times` FROM `elements` as `e` LEFT JOIN `i18n` as `t` ON `e`.`id` = `t`.`foreign_key` WHERE `t`.`locale` = :locale AND `t`.`field` = :field AND `e`.`id` IN ('" . join("','", $ids) . "') AND `e`.`code` = :code AND `e`.`active` = 1 AND (`e`.`show_from` = '' OR `e`.`show_from` <= CURDATE()) AND (`e`.`show_to` = '' OR `e`.`show_to` > CURDATE()) ORDER BY `t`.`content`", ['locale' => $this->request->params['language'], 'field' => 'title', 'code' => 'package'])->fetchAll('assoc');
        if(is_array($elements)){
            foreach($elements as $element){
                $times = $this->__handleTimes($element, 'valid_times');
                if(empty($element['valid_times']) || count($times) > 0){
                    $packages[$element['id']] = $element['content'];
                }
            }
        }

        return $packages;
    }

    public function getJobs($categories){

        // init
        $res = [];

        if(is_array($categories)){
            foreach($categories as $category){
                $jobs = [];
                if(is_array($category) && array_key_exists('details', $category) && is_array($category['details']) && array_key_exists('contain', $category['details']) && is_array($category['details']['contain'])){
                    foreach($category['details']['contain'] as $job){
                        $infos = $this->mediaElementDetails($job, false);
                        if(is_array($infos) && array_key_exists('id', $infos)){
                            $infos['url'] = $this->urlFriendlyString($infos['headline']);
                            $jobs[] = $infos;
                        }
                    }
                    if(count($job) > 0){
                        $res[] = [
                            'category' => $category['details']['translations']['title'],
                            'jobs' => $jobs
                        ];
                    }

                }
            }
        }
        return $res;
    }

    public function parseContent($content){

        // init
        $attr_name_regex = '[a-zA-Z0-9_-]+';

        if(is_string($content)){

            // special
            if(preg_match_all('|<div([^>]*)class="([^>]*)pano-tours-editor([^>]*)"([^>]*)>(.*)<\/div>|Uism', $content, $pano)){
                $start = '<div class="inline-pano-tours"><div class="icon"><span>360°</span><span class="small">' . __d('fe', 'Tour') . '</span></div><div class="links"><table><tbody><tr><td>';
                $end = '</td></tr></tbody></table></div><div class="clear"></div></div>';
                foreach($pano[5] as $k => $v){
                    $search = $pano[0][$k];
                    $replace = $start . $v . $end;
                    $content = str_replace($search, $replace, $content);

                }
            }

            // nodes/links/downloads
            if(preg_match_all('|<a([^>]+)>(.*)<\/a>|Uism', $content, $links)){
                foreach($links[1] as $k => $v){

                    // init
                    $search = $replace = $links[0][$k];
                    $attributes = [];

                    if(preg_match_all('|data-(' . $attr_name_regex . ')="(' . $attr_name_regex . ')"|Ui', $v, $data)){
                        foreach($data[1] as $_k => $_v){
                            $value = $data[2][$_k];
                            $attributes[$_v] = $value;
                        }
                    }

                    if(count($attributes) > 0){
                        if(array_key_exists('model', $attributes) && array_key_exists('code', $attributes) && array_key_exists('id', $attributes)){
                            if($attributes['model'] == 'elements'){
                                if($attributes['code'] == 'link'){
                                    $info = $this->mediaElementDetails($attributes['id'], false);
                                    $href = $info['link'];
                                    $target = $info['target'];
                                }else if($attributes['code'] == 'download'){
                                    $href = '/provide/download/' . $this->request->params['language'] . '/' . $attributes['id'] . '/';
                                    $target = '_blank';
                                }else{
                                    $href = '#' . $attributes['code'];
                                    $target = '_blank';
                                }
                            }else if($attributes['model'] == 'nodes'){
                                $href = Router::url(['node' => 'node:' . $attributes['id'], 'language' => $this->request->params['language']]);
                                if(array_key_exists('anchor', $attributes) && !empty($attributes['anchor'])){
                                    $href .= '#' . $attributes['anchor'];
                                }
                                $target = '';
                            }

                            // target/url change
                            if(preg_match_all('|(' . $attr_name_regex . ')="(.*)"|Ui', $v, $change)){
                                $map = array_flip($change[1]);
                                foreach(['href','target'] as $c){
                                    $nv = ${$c};
                                    if(array_key_exists($c,$map)){ // change
                                        $replace = str_replace($change[0][$map[$c]], $c . '="' . $nv . '"', $replace);
                                    }else{ // add
                                        $replace = str_replace('<a', '<a ' . $c . '="' . $nv . '"', $replace);
                                    }
                                }
                            }

                            // replace
                            $content = str_replace($search, $replace, $content);

                        }else{
                            // ignore?
                        }
                    }else{
                        // ignore!
                    }
                }
            }

            // images
            if(preg_match_all('|<img ([^>]+)>|Uism', $content, $images)){

                foreach($images[1] as $k => $v){

                    // init
                    $replace = '';
                    $_replace = [];
                    $search = $images[0][$k];
                    $attributes = [];

                    if(preg_match_all('|data-(' . $attr_name_regex . ')="(' . $attr_name_regex . ')"|Ui', $v, $data)){
                        foreach($data[1] as $_k => $_v){
                            $value = $data[2][$_k];
                            $attributes[$_v] = $value;
                        }
                    }

                    if(count($attributes) > 0){
                        if(array_key_exists('model', $attributes) && $attributes['model'] == 'images' && array_key_exists('code', $attributes) && $attributes['code'] == 'image' && array_key_exists('purpose', $attributes) && array_key_exists('id', $attributes)){

                            // init
                            $_replace['alt'] = '';
                            $_replace['src'] = false;

                            // image infos
                            $image = $this->mediaImageDetails($attributes['id']);

                            if(is_array($image) && count($image) > 0 && array_key_exists('paths', $image) && is_array($image['paths']) && array_key_exists($attributes['purpose'], $image['paths'])){
                                if(file_exists($image['paths'][$attributes['purpose']])){

                                    $_replace['alt'] = $image['title'];
                                    $_replace['src'] = $image['seo'][$attributes['purpose']];

                                    // src/alt/class/style change
                                    if(preg_match_all('|(' . $attr_name_regex . ')="(.*)"|Ui', $v, $change)){
                                        $map = array_flip($change[1]);
                                        foreach(['class','style'] as $c){
                                            if(array_key_exists($c,$map)){ // existing
                                                $nv = $change[2][$map[$c]];
                                            }else{ // empty
                                                $nv = '';
                                            }
                                            $_replace[$c] = $nv;
                                        }
                                    }
                                    $_replace['class'] = trim($_replace['class'] . ' editor-image');

                                    // replace
                                    if($_replace['src']){
                                        $replace .= '<img';
                                        foreach($_replace as $an => $av){
                                            $replace .= ' ' . $an . '="' . trim($av) . '"';
                                        }
                                        $replace .= ' />';
                                        $content = str_replace($search, $replace, $content);
                                    }else{
                                        // ignore?
                                    }
                                }else{
                                    // ignore?
                                }
                            }else{
                                // ignore?
                            }
                        }else{
                            // ignore?
                        }
                    }else{
                        // ignore!
                    }
                }
            }

        }
        return $content;
    }

    public function getCorrectOverview($element){
        if(is_array($element) && array_key_exists('category_id', $element)){
            $overview = $this->connection->execute("SELECT * FROM `elements` WHERE `code` = :code AND `fields` LIKE '%category:" . $element['category_id'] . "%'", ['code' => 'overview'])->fetch('assoc');
            if(is_array($overview) && count($overview) > 0){
                return [
                    'type' => 'element',
                    'id' => $overview['id'],
                    'details' => $this->mediaElementDetails($overview['id'])
                ];
            }
        }
        return false;
    }

    // search

    private function __searchCategory(&$ids, $terms, $element, $root, $nodes, $setttings, $structure, $locale, $level){
        if(is_array($element) && array_key_exists('code', $element) && array_key_exists('fields', $element) && !empty($element['fields'])){

            // init
            $fields = json_decode($element['fields'], true);

            if(array_key_exists('type', $fields) && array_key_exists($fields['type'], $setttings) && array_key_exists('searchable', $setttings[$fields['type']])){
                if(array_key_exists($fields['type'], $setttings[$element['code']]['searchable']['settings']) && array_key_exists($setttings[$element['code']]['searchable']['settings'][$fields['type']]['field'], $fields) && strpos($fields[$setttings[$element['code']]['searchable']['settings'][$fields['type']]['field']], ":") !== false){

                    // mockup
                    $setttings[$fields['type']]['searchable'] = $setttings[$element['code']]['searchable']['settings'][$fields['type']]['search'];
                    list($code, $id) = explode(":", $fields[$setttings[$element['code']]['searchable']['settings'][$fields['type']]['field']], 2);

                    // check
                    if($code == 'category'){
                        $infos = $this->mediaCategoryDetails($id);
                        if(is_array($infos) && array_key_exists('contain', $infos) && is_array($infos['contain'])){
                            foreach($infos['contain'] as $e){

                                $search = false;
                                if(array_key_exists('link', $setttings[$element['code']]['searchable']['settings'][$fields['type']]) && $setttings[$element['code']]['searchable']['settings'][$fields['type']]['link'] === true){
                                    if(in_array($e, $nodes)){
                                        $search = true;
                                    }
                                }else{
                                    $search = true;
                                }

                                if($search){
                                    $this->__searchingIds($ids, $terms, $e, $root, $nodes, $setttings, $structure, $locale, 0, 0);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function __searchSpecial(&$ids, $terms, $element, $root, $nodes, $setttings, $structure, $locale, $level){
        if(is_array($element) && array_key_exists('code', $element) && array_key_exists('fields', $element) && !empty($element['fields'])){

            // init
            $fields = json_decode($element['fields'], true);

            if(array_key_exists('code', $element) && array_key_exists($element['code'], $setttings) && array_key_exists('searchable', $setttings[$element['code']])){
                if(array_key_exists($fields['type'], $setttings[$element['code']]['searchable']['settings'])){

                    // mockup
                    $setttings[$element['code']]['searchable'] = $setttings[$element['code']]['searchable']['settings'][$fields['type']]['search'];

                    // search
                    $this->__searchingIds($ids, $terms, $element['id'], $root, $nodes, $setttings, $structure, $locale, 0, 0);
                }
            }
        }
    }

    // callbacks

    private function __processCallbacks($content, $callbacks){

        if(is_array($callbacks) && count($callbacks) > 0){
            foreach($callbacks as $field => $settings){
                if(array_key_exists($field, $content)){
                    if(is_array($settings) && array_key_exists('func', $settings)){
                        if(method_exists($this, '__handle' . ucfirst($settings['func']))){
                            $content[$field] = $this->{'__handle' . ucfirst($settings['func'])}($content, $field, array_key_exists('params', $settings) ? $settings['params'] : []);
                        }else{
                            throw new FatalErrorException(__old__('Method "' . '__handle' . ucfirst($settings['func']) . '()" not found'));
                        }
                    }
                }else{
                    throw new FatalErrorException(__old__('Field "' . $field . '" not found'));
                }
            }
        }

        return $content;
    }


    // NOTE: set f.e. in PackagesController.php as callback for getContent() to recive more details for a certain field!
    private function __handleFurtherDetails($content, $field, $params = []){

        // init
        $res = [];

        if(is_array($content[$field])){
            foreach($content[$field] as $element){
                if(is_array($element) && array_key_exists('type', $element) && $element['type'] == 'element'){
                    $element['_details'] = $this->getFurtherDetails($element['type'], $element);
                    $res[] = $element;
                }
            }
        }

        return $res;
    }

    private function __handleTimes($content, $field, $params = []){

        // init
        $res = [];
        $times = array_filter(explode("|", $content[$field]));

        // handle
        if(is_array($times) && count($times) > 0){
            foreach($times as $time){
                if(strpos($time, ":") !== false){
                    list($from,$to) = explode(":", $time, 2);
                    $from = $uxt_from = strtotime($from);
                    $to = $uxt_to = strtotime($to);

                    if((!array_key_exists('check', $params) || $params['check'] === false) || (array_key_exists('check', $params) && $params['check'] === true && $to > time())){

                        if(array_key_exists('format', $params)){
                            $from = date($params['format'], $from);
                            $to = date($params['format'], $to);
                        }
                        $res[$uxt_from . '-' . $uxt_to] = [
                            'from' => $from,
                            'to' => $to
                        ];

                    }
                }
            }
        }

        // sort
        ksort($res);

        return $res;
    }

    private function getWeatherDetailsLWDBozen($type, $element, $cache = true){

        $storage = $_SERVER['DOCUMENT_ROOT'] . DS . 'tmp' . DS . 'cache' . DS . 'weather' . DS . 'forecast-' . $element['region'] . '-'.$this->request->params['language'].'.txt';

        $lifetime = strtotime("-6 hours");
        $filetime = @filemtime($storage);
        $weather = array();

        // check
        if (!file_exists($storage) || $filetime < $lifetime || date('z') != date('z', $filetime)) {
            $cache = false;
        }

        // load from storage
        if ($cache === true) {
            $json = file_get_contents($storage);
            if ($json == false || empty($json) || trim($json) == '') {
                $cache = false;
            }
        }

        // load from api
        if ($cache == false) {

            $file = "https://wetter.ws.siag.it/Weather_V1.svc/web/getLastProvBulletin?lang=" . $this->request->params['language'];

            $weather_data = false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $file);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, (utf8_decode('medien-jäger').":".utf8_decode('medien-jäger')));
            $result = curl_exec($ch);
            if(!curl_errno($ch)){
                $info = curl_getinfo($ch);
                if($info['http_code'] == 200){
                    $weather_data = $result;
                }
            }
            curl_close($ch);

            if($weather_data){

                // header("Content-type: text/xml; charsete=utf-8;");
                // echo $weather_data; exit;

                $xml = simplexml_load_string($weather_data);
                $weather = array('date' => 0, 'forecast' => array(), 'text' => array(), 'copy' => false);
                if(is_object($xml)){

                    // date
                    $weather['date'] = strtotime($xml->date);

                    // forecast
                    $key = (int) $element['region'];
                    $weather['forecast'][] = array(
                        'date' => strtotime($xml->today->date),
                        'icon' => (string) $xml->today->stationData[$key]->symbol->imageURL,
                        'max' => (string) $xml->today->stationData[$key]->temperature->max,
                        'min' => (string) $xml->today->stationData[$key]->temperature->min,
                        'desc' => (string) $xml->today->stationData[$key]->symbol->description,
                        'code' =>  (string) $xml->today->stationData[$key]->symbol->code
                    );
                    if(isset($xml->tomorrow->date)){
                        $weather['forecast'][] = array('date' => strtotime($xml->tomorrow->date), 'icon' => (string) $xml->tomorrow->stationData[$key]->symbol->imageURL, 'max' => (string) $xml->tomorrow->stationData[$key]->temperature->max, 'min' => (string) $xml->tomorrow->stationData[$key]->temperature->min, 'desc' => (string) $xml->tomorrow->stationData[$key]->symbol->description, 'code' =>  (string) $xml->tomorrow->stationData[$key]->symbol->code);
                        $weather['forecast'][] = array('date' => strtotime($xml->dayForecast[0]->date), 'icon' => (string) $xml->dayForecast[0]->symbol->imageURL, 'max' => (string) $xml->dayForecast[0]->tempMax->max, 'min' => (string) $xml->dayForecast[0]->tempMin->max, 'desc' => (string) $xml->dayForecast[0]->symbol->description, 'code' =>  (string) $xml->dayForecast[0]->symbol->code);
                        $weather['forecast'][] = array('date' => strtotime($xml->dayForecast[1]->date), 'icon' => (string) $xml->dayForecast[1]->symbol->imageURL, 'max' => (string) $xml->dayForecast[1]->tempMax->max, 'min' => (string) $xml->dayForecast[1]->tempMin->max, 'desc' => (string) $xml->dayForecast[1]->symbol->description, 'code' =>  (string) $xml->dayForecast[1]->symbol->code);
                    }else{
                        $weather['forecast'][] = array('date' => strtotime($xml->dayForecast[0]->date), 'icon' => (string) $xml->dayForecast[0]->symbol->imageURL, 'max' => (string) $xml->dayForecast[0]->tempMax->max, 'min' => (string) $xml->dayForecast[0]->tempMin->max, 'desc' => (string) $xml->dayForecast[0]->symbol->description, 'code' =>  (string) $xml->dayForecast[0]->symbol->code);
                        $weather['forecast'][] = array('date' => strtotime($xml->dayForecast[1]->date), 'icon' => (string) $xml->dayForecast[1]->symbol->imageURL, 'max' => (string) $xml->dayForecast[1]->tempMax->max, 'min' => (string) $xml->dayForecast[1]->tempMin->max, 'desc' => (string) $xml->dayForecast[1]->symbol->description, 'code' =>  (string) $xml->dayForecast[1]->symbol->code);
                        $weather['forecast'][] = array('date' => strtotime($xml->dayForecast[2]->date), 'icon' => (string) $xml->dayForecast[2]->symbol->imageURL, 'max' => (string) $xml->dayForecast[2]->tempMax->max, 'min' => (string) $xml->dayForecast[2]->tempMin->max, 'desc' => (string) $xml->dayForecast[2]->symbol->description, 'code' =>  (string) $xml->dayForecast[2]->symbol->code);
                    }

                    // text
                    $weather['text']['today'] = array(
                        'conditions' => (string) $xml->today->conditions,
                        'temperatures' => (string) $xml->today->temperatures,
                        'weather' => (string) $xml->today->weather,
                    );
                    $weather['text']['evolution'] = (string) $xml->evolution;

                    // copy
                    switch($this->request->params['language']){
                        case "de":
                            $weather['copy'] = array(
                                'text' => '&copy; Landeswetterdienst Südtirol',
                                'link' => 'http://www.provinz.bz.it/wetter',
                            );
                            break;
                        default:
                            $weather['copy'] = array(
                                'text' => '&copy; Servizio meteo provinciale Alto Adige',
                                'link' => 'http://www.provincia.bz.it/meteo',
                            );
                            break;
                    }
                }
            }

            $json = json_encode($weather);
            @file_put_contents($storage, $json);
        }

        $data = json_decode($json);

        return $data;
    }

	private function getWeatherDetailsZamg($type, $element, $cache = true){
		$weather = false;
        if(isset($element['file']) && !empty($element['file']) && ($weather = file($element['file']))){
            switch($this->request->params['language']){
                case "de":
                    $desc = 11; //7;
                    break;
                case "it":
                    $desc = 12; //9;
                    break;
                default:
                    $desc = 12; //8;
                    break;
            }
            foreach($weather as $k => $v){
                $infos = explode(":",trim(($v)));
                $uxt = strtotime($infos[1]);
                $infos = array(
                    'date' => array(
                        'uxt' => $uxt,
                        'day-name' => $this->getDayName($uxt),
                        'month-name' => $this->getMonthName($uxt),
                    ),
                    'morning' => $infos[2],
                    'noon' => $infos[4], //$infos[3],
                    'eve' => $infos[6], //$infos[4],
                    'desc' => $infos[$desc],
                    'min' => $infos[9], //$infos[5],
                    'max' => $infos[10], //$infos[6],
					//'icon' => (string)$day_data->{'icon_url'},
					'font_icon' => $infos[3],
                );
                $weather[$k] = $infos;
            }
        }

        return $weather;
	}

    function getDayName($uxt){
        $name = array('short' => false, 'long' => false);
        switch(date("N",$uxt)){
            case 1:
                $name['short'] = __d('fe', 'Mon');
                $name['long'] = __d('fe', 'Monday');
                break;
            case 2:
                $name['short'] = __d('fe', 'Tue');
                $name['long'] = __d('fe', 'Tuesday');
                break;
            case 3:
                $name['short'] = __d('fe', 'Wed');
                $name['long'] = __d('fe', 'Wednesday');
                break;
            case 4:
                $name['short'] = __d('fe', 'Thu');
                $name['long'] = __d('fe', 'Thursday');
                break;
            case 5:
                $name['short'] = __d('fe', 'Fri');
                $name['long'] = __d('fe', 'Friday');
                break;
            case 6:
                $name['short'] = __d('fe', 'Sat');
                $name['long'] = __d('fe', 'Saturday');
                break;
            case 7:
                $name['short'] = __d('fe', 'Sun');
                $name['long'] = __d('fe', 'Sunday');
                break;
        }
        return $name;
    }

    function getMonthName($uxt){
        $name = array('short' => false, 'long' => false);
        switch(date("n",$uxt)){
            case 1:
                $name['short'] = __d('fe', 'Jan');
                $name['long'] = __d('fe', 'January');
                break;
            case 2:
                $name['short'] = __d('fe', 'Feb');
                $name['long'] = __d('fe', 'February');
                break;
            case 3:
                $name['short'] = __d('fe', 'Mar');
                $name['long'] = __d('fe', 'March');
                break;
            case 4:
                $name['short'] = __d('fe', 'Apr');
                $name['long'] = __d('fe', 'April');
                break;
            case 5:
                $name['short'] = __d('fe', 'May');
                $name['long'] = __d('fe', 'May');
                break;
            case 6:
                $name['short'] = __d('fe', 'Jun');
                $name['long'] = __d('fe', 'June');
                break;
            case 7:
                $name['short'] = __d('fe', 'Jul');
                $name['long'] = __d('fe', 'July');
                break;
            case 8:
                $name['short'] = __d('fe', 'Aug');
                $name['long'] = __d('fe', 'August');
                break;
            case 9:
                $name['short'] = __d('fe', 'Sep');
                $name['long'] = __d('fe', 'September');
                break;
            case 10:
                $name['short'] = __d('fe', 'Oct');
                $name['long'] = __d('fe', 'October');
                break;
            case 11:
                $name['short'] = __d('fe', 'Nov');
                $name['long'] = __d('fe', 'November');
                break;
            case 12:
                $name['short'] = __d('fe', 'Dec');
                $name['long'] = __d('fe', 'December');
                break;
        }
        return $name;
    }

    private function getWeatherDetailsWunderground($type, $element, $cache = true){
        $iconFontsets = array(
            'meteocons-light' => array( //http://www.alessioatzeni.com/meteocons/
                'default' => ')',
                'chanceflurries' => 'U',
                'chancerain' => 'Q',
                'chancesleet' => 'X',
                'chancesnow' => 'V',
                'chancetstorms' => 'Z',
                'clear' => 'B',
                'cloudy' => 'N',
                'flurries' => 'W',
                'fog' => 'Y',
                'hazy' => 'Y',
                'mostlycloudy' => 'H',
                'mostlysunny' => 'H',
                'partlycloudy' => 'H',
                'partlysunny' => 'H',
                'rain' => 'R',
                'sleet' => 'X',
                'snow' => 'W',
                'sunny' => 'B',
                'tstorms' => '0',
            ),
            'meteocons-full' => array( //http://www.alessioatzeni.com/meteocons/
                'default' => ')',
                'chanceflurries' => '"',
                'chancerain' => '7',
                'chancesleet' => '$',
                'chancesnow' => '"',
                'chancetstorms' => '&',
                'clear' => '1',
                'cloudy' => '5',
                'flurries' => '#',
                'fog' => '%',
                'hazy' => '%',
                'mostlycloudy' => '3',
                'mostlysunny' => '3',
                'partlycloudy' => '3',
                'partlysunny' => '3',
                'rain' => '8',
                'sleet' => '$',
                'snow' => '#',
                'sunny' => '1',
                'tstorms' => '&',
            )
        );

        $storage = $_SERVER['DOCUMENT_ROOT'] . DS . 'tmp' . DS . 'cache' . DS . 'weather' . DS . 'forecast-'.$this->request->params['language'].'.txt';

        $lifetime = strtotime("-6 hours");
        $filetime = @filemtime($storage);
        $data = array();

        // check
        if (!file_exists($storage) || $filetime < $lifetime || date('z') != date('z', $filetime)) {
            $cache = false;
        }

        // load from storage
        if ($cache === true) {
            $json = file_get_contents($storage);
            if ($json == false || empty($json) || trim($json) == '') {
                $cache = false;
            }
        }

        // load from api
        if ($cache == false) {
            $apiKey = $element['key'];
            $apiZMW = $element['zmw'];
            if($this->request->params['language'] == 'de'){
                $apiLang = 'DL';
            }else if($this->request->params['language'] == 'en'){
                $apiLang = 'EN';
            }else if($this->request->params['language'] == 'fr'){
                $apiLang = 'FR';
            }else{
                $apiLang = 'EN';
            }
            $apiFeature = 'forecast';
            $apiUrl = "http://api.wunderground.com/api/".$apiKey."/".$apiFeature."/lang:".$apiLang."/q/zmw:".$apiZMW.".json";
            $json = file_get_contents($apiUrl);
            $save = file_put_contents($storage, $json);
        }

        $data = json_decode($json);

        if(!$data || $element['key'] == '') return array();

        $fontSet = $element['font'];
        $weather = array();
        foreach($data->{'forecast'}->{'simpleforecast'}->{'forecastday'} as $key => $day_data){
            $desc = (array) $data->{'forecast'}->{'txt_forecast'}->{'forecastday'};
            $desc = (array) $desc[$key];
            $infos = array(
                'date' => array(
                        'uxt' => $day_data->{'date'}->{'epoch'},
                        'day-name' => $day_data->{'date'}->{'weekday'},
                        'day-name-short' => $day_data->{'date'}->{'weekday_short'},
                        'month-name' => $day_data->{'date'}->{'monthname'},
                        'month-name-short' => $day_data->{'date'}->{'monthname_short'},
                ),
                'icon' => (string)$day_data->{'icon_url'},
                'font_icon' => isset($iconFontsets[$fontSet][(string)$day_data->{'icon'}]) ? $iconFontsets[$fontSet][(string)$day_data->{'icon'}] : $iconFontsets[$fontSet]['default'],
                'desc' => (string)$desc['fcttext_metric'],
                'conditions' => (string) $day_data->{'conditions'},
                'min' => (string)$day_data->{'low'}->{'celsius'},
                'max' => (string)$day_data->{'high'}->{'celsius'}
            );
            $weather[] = $infos;
        }

        return $weather;
    }

    public function urlFriendlyString($string){
        $s = ['ä','ü','ö','Ä','Ü','Ö','ß'];
        $r = ['ae','ue','oe','Ae','Ue','Oe','ss'];
        return strtolower(Text::slug(str_replace($s,$r,html_entity_decode(strip_tags($string)))));
    }

    public function provide($type, $language, $id){

        // init
        $file = ['type' => false, 'name' => false, 'path' => false];

        // language
        I18n::locale($language);
        Configure::write('language', $language);
        Configure::write('App.defaultLocale', $language);

        // infos
        switch($type){
            case "image":
                $details = $this->mediaImageDetails($id, true);
                if(is_array($details) && count($details) > 0){
                    $file['type'] = array_key_exists('mime', $details) ? $details['mime'] : false;
                    $file['name'] = array_key_exists('title', $details) && array_key_exists('extension', $details) ? str_replace([' '],['-'],strtolower($details['title'])) . '.' . $details['extension'] : false;
                    $file['path'] = array_key_exists('paths', $details) && is_array($details['paths']) && array_key_exists('original', $details['paths']) ? $details['paths']['original'] : false;
                }
                break;
            case "download":
                $details = $this->mediaElementDetails($id, true);
                if(is_array($details) && count($details) > 0 && array_key_exists('title', $details) && !empty($details['title']) && array_key_exists('file', $details) && !empty($details['file'])){
                    if(is_array($details['file'])){
                        $file['type'] = array_key_exists('type', $details['file']) ? $details['file']['type'] : false;
                        $file['name'] = array_key_exists('title', $details['file']) ? str_replace([' '],['-'],strtolower($details['title'])) . '.' . strtolower(pathinfo($details['file']['title'], PATHINFO_EXTENSION)) : false;
                        $file['path'] = array_key_exists('name', $details['file']) ? WWW_ROOT . 'files' . DS . $details['file']['name'] : false;
                    }
                }
                break;
            default:
                break;
        }

        // provide
        if(file_exists($file['path'])){
            header("Content-type: " . $file['type']);
            header("Cache-Control: no-store, no-cache");
            header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            readfile($file['path']);
            exit;
        }else{
            header("HTTP/1.0 404 Not Found");
            die(__d('fe', 'File not found!'));
        }
    }

}
