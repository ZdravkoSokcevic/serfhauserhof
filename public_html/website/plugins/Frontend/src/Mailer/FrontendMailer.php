<?php

namespace Frontend\Mailer;

use Cake\Mailer\Mailer;
use Cake\Core\Configure;
use Cake\Routing\Router;

class FrontendMailer extends Mailer
{
    public function contact($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {
        
        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];

        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);
            
    }
    
    public function job($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {
      
        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];

        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);
      
    }

    public function callback($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {

        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];

        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);

    }

    public function brochure($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {

        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];

        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);

    }

    public function coupon($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {

        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];

        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);

    }

    public function table($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {
        
        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];

        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);

    }

    public function request($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {

        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];

        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);

    }

    public function lastminute($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {

        // recipient
        $recipient = $context['recipient'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject'],
            'headline' => !empty_html($context['email_headline']) ? $context['email_headline'] : '',
            'content' => !empty_html($context['email_content']) ? $context['email_content'] : '',
            'infos' => infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]),
        ];
        
        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->set($vars);

    }

    public function autoreply($request, $context, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {
        
        // recipient
        $recipient = $request->data['email'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // greetings/gender
        $greetings = $genders = [];
        foreach(Configure::read('salutations') as $k => $v){
            $greetings[$k] = $v['long'];
            $genders[$k] = $v['gender'];
        }
        
        // replace
        $replace = [];
        foreach($request['data'] as $k => $v){
            if(!is_array(($v))){
                $replace[$k] = $v;
            }
        }
        
        if(array_key_exists('salutation', $replace)){
            if(array_key_exists($replace['salutation'], $salutations) && array_key_exists($replace['salutation'], $greetings)){
                $replace['greeting'] = $greetings[$replace['salutation']];
                $replace['salutation'] = $salutations[$replace['salutation']];
            }else{
                $replace['greeting'] = '';
                $replace['salutation'] = '';
            }
        }

        $replace['table'] = infoTable($request->data, $context, ['salutations' => $salutations, 'countries' => $countries, 'interests' => $interests, 'rooms' => $rooms, 'packages' => $packages, 'map' => $map]);
        $replace['teaser'] = replyTeaser($request->data, $context, ['fullBaseUrl' => Configure::read('App.fullBaseUrl') . DS, 'request' => $request]);

        // vars
        $vars = [
            'subject' => $context['reply_subject'],
            'headline' => !empty_html($context['reply_headline']) ? $context['reply_headline'] : '',
            'content' => !empty_html($context['reply_content']) ? $this->replaceVars($context['reply_content'], $replace) : '',
        ];
        
        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['reply_subject'])
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/reply')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/' . $request->params['structure']['theme'])
            ->set($vars);
            
    }

    public function doubleoptin($request, $context, $init, $countries, $salutations, $interests, $rooms, $packages, $sender, $config, $map)
    {

        // recipient
        $recipient = $request->data['email'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // link
        $href = Router::url(['node' => Configure::read('config.default.newsletter.0.org'), 'language' => $request->params['language']], true) . '?activate=' . $init;
        $link = '<a href="' . $href . '" target="_blank">' . $href . '</a>';
        
        // vars
        $vars = [
            'subject' => $context['doi_subject'],
            'headline' => !empty_html($context['doi_headline']) ? $context['doi_headline'] : '',
            'content' => !empty_html($context['doi_content']) ? $this->replaceVars($context['doi_content'], ['link' => $link, 'sender' => $sender['email']]) : '',
        ];
        
        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['doi_subject'])
            ->template('Frontend.plain')
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/' . $request->params['structure']['theme'])
            ->set($vars);

    }

    public function register($request, $context, $salutations, $sender, $config, $map)
    {

        // recipient
        $recipient = $request->data['email'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject_signup'],
            'headline' => !empty_html($context['email_headline_signup']) ? $context['email_headline_signup'] : '',
            'content' => !empty_html($context['email_content_signup']) ? $this->replaceVars($context['email_content_signup'], ['username' => $request->data['username'], 'password' => $request->data['password']]) : '',
        ];
        
        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject_signup'])
            ->template('Frontend.plain')
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/' . $request->params['structure']['theme'])
            ->set($vars);

    }

    public function forgot($request, $context, $access, $salutations, $sender, $config, $map)
    {

        // recipient
        $recipient = $request->data['email'];
        if($config['mode'] && !empty($config['email'])){
            $ips = array_filter(explode(";", $config['ip']));
            if(empty($config['ip']) || in_array($_SERVER['REMOTE_ADDR'],$ips)){
                $recipient = $config['email'];
            }
        }
        
        // vars
        $vars = [
            'subject' => $context['email_subject_forgot'],
            'headline' => !empty_html($context['email_headline_forgot']) ? $context['email_headline_forgot'] : '',
            'content' => !empty_html($context['email_content_forgot']) ? $this->replaceVars($context['email_content_forgot'], ['username' => $access['username'], 'password' => $access['password']]) : '',
        ];
        
        // send        
        $this
            ->emailFormat('html')
            ->to($recipient)
            ->from([$sender['email'] => $sender['name']])
            ->subject($context['email_subject_forgot'])
            ->template('Frontend.plain')
            ->template('Frontend.' . ucfirst($request->params['structure']['theme']) . '/plain')
            ->layout('Frontend.' . ucfirst($request->params['structure']['theme']) . '/' . $request->params['structure']['theme'])
            ->set($vars);

    }

    private function replaceVars($content, $vars){
        
        if(is_array($vars)){
            foreach($vars as $k => $v){
                $content = str_replace('{$var:' . $k . '}', $v, $content);
            }
        }
        
        return $content;
    }

}