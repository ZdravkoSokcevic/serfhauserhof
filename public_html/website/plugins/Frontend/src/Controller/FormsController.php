<?php
namespace Frontend\Controller;

use Frontend\Controller\AppController;
use Frontend\Form\FrontendForm;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Mailer\MailerAwareTrait;

class FormsController extends AppController {

    use MailerAwareTrait;

    public function index($id = false) {

        // init
        $interests = [];
        $tracking = false;
        $action = false;
        $rooms = [];
        $packages = [];
        $last_minute_offers = [];
        $room_details = [];
        $job = false;
        $jobs = [];
        $arrival_val = isset($this->request->query['arrival']) && !empty($this->request->query['arrival']) ? $this->request->query['arrival'] : '';
        $departure_val = isset($this->request->query['departure']) && !empty($this->request->query['departure']) ? $this->request->query['departure'] : '';

        // info
        $messages = ['show' => false, 'success' => null, 'newsletter' => null];
        if(count($this->request->params['url']) > 4){
            $url = $this->request->params['url'];
            $last = array_pop($url);

            // global
            if($last == __d('fe', 'sent')){
                $messages['show'] = true;
                $messages['success'] = true;
                $tracking = true;
            }else if($last == __d('fe', 'sent-and-registered')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['newsletter'] = true;
                $tracking = true;
            }else if($last == __d('fe', 'sent-and-existing')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['newsletter'] = 'exists';
                $tracking = true;
            }else if($last == __d('fe', 'sent-and-not-registered')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['newsletter'] = false;
                $tracking = true;
            }else if($last == __d('fe', 'failed')){
                $messages['show'] = true;
                $messages['success'] = false;
            }

            // members
            if($last == __d('fe', 'register-success')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['status'] = __d('fe', 'You will soon receive an e-mail to confirm your registration.');
                $tracking = true;
            }else if($last == __d('fe', 'register-error')){
                $messages['show'] = true;
                $messages['success'] = false;
                $messages['status'] = __d('fe', 'There was an error saving your registration. Please try it again.');
            }else if($last == __d('fe', 'forgot-password-success')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['status'] = __d('fe', 'You will soon receive an e-mail to confirm your access data change.');
                $tracking = true;
            }else if($last == __d('fe', 'forgot-password-error')){
                $messages['show'] = true;
                $messages['success'] = false;
                $messages['status'] = __d('fe', 'There was an error resetting your password. Please try it again.');
            }else if($last == __d('fe', 'logout')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['status'] = __d('fe', 'You successfully logged out.');
            }

            // newsletter
            if($last == __d('fe', 'subscribe-init')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['status'] = __d('fe', 'You will soon receive an e-mail to confirm your registration.');
                $tracking = true;
            }else if($last == __d('fe', 'subscribe-error')){
                $messages['show'] = true;
                $messages['success'] = false;
                $messages['status'] = __d('fe', 'There was an error saving your registration. Please try it again.');
            }else if($last == __d('fe', 'unsubscribe-error')){
                $messages['show'] = true;
                $messages['success'] = false;
                $messages['status'] = __d('fe', 'There was an error removing your registration. Please try it again.');
            }else if($last == __d('fe', 'unsubscribed')){
                $messages['show'] = true;
                $messages['success'] = true;
                $messages['status'] = __d('fe', 'Your registration has been successfully removed!');
            }

        }

        // salutations
        Configure::load('salutations');
        $salutations = [];
        foreach(Configure::read('salutations') as $k => $v){
            $salutations[$k] = $v['short'];
        }
        $this->set('salutations', $salutations);

        // countries
        Configure::load('countries');
        $nc = $ic = [];
        foreach(Configure::read('countries') as $k => $v){
            if(array_key_exists('important', $v) && $v['important']){
                $ic[$k] = $v['name'];
            }else{
                $nc[$k] = $v['name'];
            }
        }
        asort($ic);
        asort($nc);
        $countries = array_merge($ic, $nc);
        $this->set('countries', $countries);

        // get content
        $content = $this->getContent($id, $this->getCode($id));

        // type
        $type = $content['view'];

        // newsletter special behaviour
        if($type == 'newsletter'){
            $url = $this->request->params['url'];
            $last = array_pop($url);
            $us = [
                __d('fe', 'unsubscribe'),
                __d('fe', 'unsubscribed'),
                __d('fe', 'unsubscribe-init'),
                __d('fe', 'unsubscribe-error'),
            ];

            if(in_array($last, $us)){
                $action = 'unsubscribe';
            }else{
                $action = 'subscribe';
            }

            // interests
            $interests = $this->Forms->interests();
        }else if($type == 'job'){
            if(count($this->request->params['url']) == 4){ // show list
                $action = 'list';
                $this->request->data = [];
                $jobs = $this->getJobs($content['jobs']);
            }else{ // show form
                $action = 'form';
                $job = $this->mediaElementDetails($this->request->params['url'][3], false);
            }
        }else if($type == 'brochure'){
            $interests = Configure::read('brochure.interests');
        }else if($type == 'request'){
            $rooms = $this->getRooms();
            $packages = $this->getPackages();
        }else if($type == 'member'){
            $url = $this->request->params['url'];
            $last = array_pop($url);
            $su = [
                __d('fe', 'register'),
                __d('fe', 'register-error'),
                __d('fe', 'register-success'),
            ];
            $fo = [
                __d('fe', 'forgot-password'),
                __d('fe', 'forgot-password-error'),
                __d('fe', 'forgot-password-success'),
            ];
            $lo = [
                __d('fe', 'logout'),
            ];

            if(in_array($last, $su)){
                $action = 'subscribe';
            }else if(in_array($last, $fo)){
                $action = 'forgot';
            }else if(in_array($last, $lo)){
                $action = 'logout';
            }else{
                $action = Configure::read('member') ? 'protected' : 'login';
            }
        }else if($type == 'lastminute'){
            $rooms = $this->getRooms();
            if(array_key_exists('last_minute_offers', $content) && is_array($content['last_minute_offers']) && array_key_exists(0, $content['last_minute_offers']) && is_array($content['last_minute_offers'][0]) && array_key_exists('id', $content['last_minute_offers'][0])){
                $_rooms = [];
                foreach($content['last_minute_offers'][0]['details']['contain'] as $lmo){
                    $tmp = $this->mediaElementDetails($lmo, ['image']);
                    if((!array_key_exists('quota', $tmp) || $tmp['quota'] > 0) && array_key_exists('room', $tmp) && !empty($tmp['room'])){

                        $valid_ranges = [];
                        $skip = false;

                        // ranges
                        if(array_key_exists('ranges', $tmp) && !empty($tmp['ranges'])){
                            $ranges = array_filter(explode("|", $tmp['ranges']));
                            foreach($ranges as $range){
                                list($_f, $_t) = explode(":", $range, 2);
                                if(strtotime($_t) > time()){
                                    $valid_ranges[] = [
                                        'from' => strtotime($_f),
                                        'to' => strtotime($_t),
                                    ];
                                }
                            }

                            if(count($valid_ranges) < 1){
                                $skip = true;
                            }else{
                                usort($valid_ranges, function($a, $b){
                                    if($a['from'] == $b['from']) return 0;
                                    return ($a['from'] < $b['from']) ? -1 : 1;
                                });
                                $tmp['ranges'] = $valid_ranges;
                            }

                        }else{
                            $tmp['ranges'] = [];
                        }

                        if($skip === false){
                            list($_code, $_id) = explode(":", $tmp['room'], 2);
                            if(!in_array($_id, $_rooms)) $_rooms[] = $_id;
                            $tmp['room'] = $_id;
                            $last_minute_offers[] = $tmp;
                        }
                    }
                }

                foreach($_rooms as $v){
                    $room_details[$v] = [
                        'id' => $v,
                        'details' => [
                            'code' => 'room',
                        ],
                    ];
                    $room_details[$v]['_details'] = $this->getFurtherDetails('element', $room_details[$v], ['prices' => false]);
                }
            }
        }

        // process form
        if(is_array($content) && array_key_exists('view', $content) && !empty($content['view'])){

            if(in_array($type, ['newsletter','member'])){
                $form = new FrontendForm($content['view'].ucfirst($action), $this->request->session()->read('Captcha.' . $this->request->params['route']));
            }else{
                $form = new FrontendForm($content['view'], $this->request->session()->read('Captcha.' . $this->request->params['route']));
            }
            if ($this->request->is('post')) {
                if(array_key_exists('quickform', $this->request->data) === false){
                  if(!isset($this->request->data['c-info']) || !isset($this->request->data['h-info']) || $this->request->data['c-info'] != 'all-clear' || !empty($this->request->data['h-info'])){
                    // the form was send with js deactivated or with the honeypot filled out.
                    // we will not send it, but let the bot belive so.
                    $extend = __d('fe', 'sent');
                    return $this->redirect(['node' => 'node:' . $this->request->params['node']['id'], 'language' => $this->request->params['language'], 'extend' => [$extend]]);
                  } else{
                    if ($form->execute($this->request->data)) {

                        // save?
                        $save = true;
                        if($type == 'member' && $action == 'login'){
                            $save = false;
                        }

                        // save in db
                        if($save){
                            $entity = $this->Forms->newEntity([
                                'id' => Text::uuid(),
                                'type' => $content['view'],
                                'action' => $action,
                                'theme' => $this->request->params['structure']['id'],
                                'data' => json_encode($this->request->data),
                                'request' => json_encode($this->request->params),
                                'sent' => time(),
                            ]);

                            if ($this->Forms->save($entity)) {
                                $saved = true;
                            }else{
                                $saved = false;
                            }
                        }else{
                            $saved = true;
                        }

                        if ($saved) {
                            if($type == 'newsletter'){
                                if($action == 'subscribe'){
                                    $init = Text::uuid();
                                    // if($form->newsletterInit($init, $interests, $this->request->data, $this->request) && $this->getMailer('Frontend.Frontend')->send('doubleoptin', [$this->request, $content, $init, $countries, $salutations, $interests, $rooms, $packages, Configure::read('sender'), Configure::read('config.dev'), $this->Forms->map()])){
                                    if($form->newsletterInit($init, $interests, $this->request->data, $this->request)){
                                        $extend = __d('fe', 'subscribe-init');
                                    }else{
                                        $extend = __d('fe', 'subscribe-error');
                                    }
                                }else{
                                    $extend = $form->newsletterUnsubscribe($this->request->data, $this->request) ? __d('fe', 'unsubscribed') : __d('fe', 'unsubscribe-error');
                                }
                            }else if($type == 'member'){
                                if($action == 'subscribe'){
                                    $init = Text::uuid();
                                    if($form->memberRegister($init, $this->request->data, $this->request) && $this->getMailer('Frontend.Frontend')->send('register', [$this->request, $content, $salutations, Configure::read('sender'), Configure::read('config.dev'), $this->Forms->map()])){
                                        $extend = __d('fe', 'register-success');
                                    }else{
                                        $extend = __d('fe', 'register-error');
                                    }

                                }else if($action == 'forgot'){
                                    if($this->getMailer('Frontend.Frontend')->send('forgot', [$this->request, $content, $form->memberForgot($this->request->data), $salutations, Configure::read('sender'), Configure::read('config.dev'), $this->Forms->map()])){
                                        $extend = __d('fe', 'forgot-password-success');
                                    }else{
                                        $extend = __d('fe', 'forgot-password-error');
                                    }
                                }else{

                                    $extend = false;
                                    $action = 'protected';

                                    // member!
                                    Configure::write('member', true);
                                    $this->request->session()->write('Member', ['infos' => $this->getMemberInfos($this->request->data), 'timestamp' => time()]);

                                }
                            }else{

                                // last-minute special behaviour
                                if($type == 'lastminute'){
                                    $this->Forms->reduceLastMinuteQuota($this->request->data['id']);
                                }

                                // extend
                                $extend = __d('fe', 'sent');

                                // send email
                                $this->getMailer('Frontend.Frontend')->send($content['view'], [$this->request, $content, $countries, $salutations, $interests, $rooms, $packages, Configure::read('sender'), Configure::read('config.dev'), $this->Forms->map()]);

                                // send auto reply
                                if(array_key_exists('autoreply', $content) && $content['autoreply'] == 1){
                                    $this->getMailer('Frontend.Frontend')->send('autoreply', [$this->request, $content, $countries, $salutations, $interests, $rooms, $packages, Configure::read('sender'), Configure::read('config.dev'), $this->Forms->map()]);
                                }

                                // newsletter
                                if(array_key_exists('newsletter', $this->request->data) && $this->request->data['newsletter'] == 1){
                                    if($form->newsletterNonExistent($this->request['data']['email'], $this->request)){

                                        $init = Text::uuid();
                                        if($form->newsletterInit($init, $interests, $this->request->data, $this->request)){
                                            $extend = __d('fe', 'sent-and-registered');
                                        }else{
                                            $extend = __d('fe', 'sent-and-not-registered');
                                        }
                                    }else{
                                        $extend = __d('fe', 'sent-and-existing');
                                    }
                                }
                            }
                        }else{
                            $extend = __d('fe', 'failed');
                        }

                        // redirect
                        if(isset($extend) && $extend !== false){
                            return $this->redirect(['node' => 'node:' . $this->request->params['node']['id'], 'language' => $this->request->params['language'], 'extend' => [$extend]]);
                        }

                    } else {
                        $this->set('errors', $form->errors());
                    }
                  }
                }
            }else if($type == 'newsletter'){
                if(array_key_exists('activate', $_GET)){
                    if($form->newsletterSubscribe($_GET['activate'], $this->request)){
                        $messages['show'] = true;
                        $messages['success'] = true;
                        $messages['status'] = __d('fe', 'Your registration has been successfully activated!');
                    }else{
                        $messages['show'] = true;
                        $messages['success'] = false;
                        $messages['status'] = __d('fe', 'There was an error activating your registration. Please try it again.');
                    }
                }else if(array_key_exists('e', $_GET)){
                    $this->request->data['email'] = strip_tags($_GET['e']);
                }
            }else if($type == 'request'){
                if(count($this->request->data) == 0){
                    if(array_key_exists('room', $_GET) && array_key_exists($_GET['room'], $rooms)){
                        $this->request->data['rooms'][] = [
                            'room' => $_GET['room'],
                            'adults' => false,
                            'package' => false,
                            'children' => false,
                        ];
                    }
                    if(array_key_exists('package', $_GET) && array_key_exists($_GET['package'], $packages)){
                        $this->request->data['packages'][] = [
                            'room' => false,
                            'adults' => false,
                            'package' => $_GET['package'],
                            'children' => false,
                        ];
                    }
                }
            }else if($type == 'member' && $action == 'logout'){

                // no member!
                Configure::write('member', false);
                $this->request->session()->write('Member', false);

                $action = 'login';
            }
        }

        if ($this->request->is('post') && array_key_exists('quickform', $this->request->data)) {

            // children ages
            if(array_key_exists('children', $this->request->data) && !array_key_exists('ages', $this->request->data)){
                for($a = 0; $a < (int) $this->request->data['children']; $a++){
                    if(!array_key_exists('ages', $this->request->data)){
                        $this->request->data['ages'] = [];
                    }
                    $this->request->data['ages'][$a]['age'] = '';
                }
            }

        }

        $this->set('form', $form);
        $this->set('messages', $messages);

        // interests
        foreach($interests as $k => $v){
            if(is_array($v) && array_key_exists('title', $v)){
                $interests[$k] = $v['title'];
            }
        }
        $this->set('interests', $interests);

        // set vars
        $this->set('title', $content['html']);
        $this->set('content', $content);
        $this->set('action', $action);
        $this->set('tracking', $tracking);
        $this->set('rooms', $rooms);
        $this->set('packages', $packages);
        $this->set('last_minute_offers', $last_minute_offers);
        $this->set('job', $job);
        $this->set('jobs', $jobs);
        $this->set('room_details', $room_details);
        $this->set('arrival_val', $arrival_val);
        $this->set('departure_val', $departure_val);

        // render
        if(in_array($type, ['newsletter','member']) && $action !== false){
            $this->render(DS . ucfirst($this->request->params['structure']['theme']) . DS . ucfirst($content['view']) . DS . $action . DS);
        }else{
            $this->render(DS . ucfirst($this->request->params['structure']['theme']) . DS . ucfirst($content['view']) . DS . 'index' . DS);
        }

    }

    function captcha($type){

        // init
        $settings = Configure::read('captcha');
        $infos = $text = false;
        $font = WWW_ROOT . 'frontend' . DS . 'fonts' . DS . $settings['font'];

        // get text
        if($type == 'text'){
            $length = 5;
            $infos = array(
                'type' => 'text',
                'text' => substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length),
            );
            $text = $infos['text'];
        } else {
            $operations = array('+');
            $infos = array(
                'type' => 'math',
                'value1' => rand(1,9),
                'value2' => rand(1,9),
                'operation' => $operations[array_rand($operations)],
            );
            $text = $infos['value1'] . ' ' . $infos['operation'] . ' ' . $infos['value2'] . ' =';
        }

        // save values in session
        if(is_array($infos) && $text){
            $this->request->session()->write('Captcha.' . $this->request->params['route'], $infos);
        }

        // create captcha
        $captcha = imagecreatetruecolor($settings['width'], $settings['height']);
        imagesavealpha($captcha, true);
        imageantialias($captcha, true);
        $background = imagecolorallocate($captcha, $settings['background'][0], $settings['background'][1], $settings['background'][2]);
        $color = imagecolorallocate($captcha, $settings['color'][0], $settings['color'][1], $settings['color'][2]);
        imagefilledrectangle($captcha, 0, 0, $settings['width'], $settings['height'], $background);

        // calculate
        $box = imagettfbbox($settings['size'], $settings['angle'], $font, $text);

        $min_x = min( array( $box[0], $box[2], $box[4], $box[6] ) );
        $max_x = max( array( $box[0], $box[2], $box[4], $box[6] ) );
        $min_y = min( array( $box[1], $box[3], $box[5], $box[7] ) );
        $max_y = max( array( $box[1], $box[3], $box[5], $box[7] ) );

        $box = array(
            'left' => ( $min_x >= -1 ) ? -abs( $min_x + 1 ) : abs( $min_x + 2 ),
            'top' => abs( $min_y ),
            'width' => $max_x - $min_x,
            'height' => $max_y - $min_y,
            'box' => $box
        );

        $top  = ($settings['height']/2) - ($box['height']/2) + $box['top'];
        $left = ($settings['width']/2) - ($box['width']/2) + $box['left'];
        imagettftext($captcha, $settings['size'], $settings['angle'], $left, $top, $color, $font, $text);

        // add lines
        if($type == 'text'){
            $linecolor = array($background,$color);
            for($i=0;$i<4;$i++){
                imageline($captcha,0,rand(0,$settings['height']),$settings['width'],rand(0,$settings['height']),$linecolor[rand(0,1)]);
            }
        }

        // output
        header("Content-type: image/png");
        imagepng($captcha);
        exit;

    }

  	function verifyReCaptcha(){
      require(ROOT . DS .  'vendor' . DS  . 'recaptcha'.DS.'src'.DS.'autoload.php');
  		$recaptcha = new \ReCaptcha\ReCaptcha(Configure::read('config.tracking.recapcha_secret_key'));
      if(isset($_POST['response']) && isset($_POST['remoteip'])){
    		$resp = $recaptcha->verify($_POST['response'], $_POST['remoteip']);
    		if ($resp->isSuccess()) {
    			$return = array(
    				'success' => true,
                    'recaptchaConfirm' => 'all-clear',
    			);
    			echo json_encode($return);
    			exit;
    		} else {
    			$errors = $resp->getErrorCodes();
    			$return = array(
    				'success' => false,
    				'errors' => $errors,
    			);
    			echo json_encode($return);
    			exit;
    		}
      } else{
        $return = array(
          'success' => false,
          'errors' => [
            'Missing post data'
          ],
        );
        echo json_encode($return);
        exit;
      }
  	}

}
