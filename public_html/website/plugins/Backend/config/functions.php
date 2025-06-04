<?php

use Cake\Core\Configure;

function decode_session($data){
    $last = null;
    $key = '';
    $res = [];
    if(preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $data, $match, PREG_OFFSET_CAPTURE)){
        foreach($match[2] as $value){
            $offset = $value[1];
            if(!is_null($last)){
                $text = substr($data, $last, $offset - $last);
                $res[$key] = unserialize($text);
            }
            $key = $value[0];
            $last = $offset + strlen($key) + 1;
        }
        
        $text = substr($data, $last);
        $res[$key] = unserialize($text);
    }
    return $res;
}

function __cp($url, $auth){
    
    if(is_array($url) && array_key_exists('controller', $url) && array_key_exists('action', $url)){
        
        // super user?
        if(empty($auth['User']['group_id'])){
            return true;
        }
        
        // check premissions
        $allow = [];
        $allowed = Configure::read('allowed');
        if(array_key_exists($url['controller'], $allowed)){
            $allow = $allowed[$url['controller']];
        }else{
            $url = array_map('strtolower', $url);
            $cfp = ROOT . DS . 'plugins' . DS . 'Backend' . DS . 'src' . DS . 'Controller' . DS . ucfirst($url['controller']) . 'Controller.php';
            if(file_exists($cfp)){
                $controller = file($cfp);
                foreach($controller as $line){
                    if(($pos = strpos($line, '$allow')) !== false){
                        $line = trim(substr($line, $pos));
                        eval($line);
                        break;
                    }
                    if(strpos($line, ' function ') !== false){
                        break;
                    }
                }
                unset($controller);
            }
            $allowed[$url['controller']] = $allow;
        }
        Configure::write('allowed', $allowed);
        
        if(in_array($url['action'], $allow)){
            return true;
        }else if(is_array($auth) && array_key_exists('User', $auth) && array_key_exists('Group', $auth)){
            $rights = $auth['Group']['settings']; 
            if(array_key_exists($url['controller'], $rights)){
                switch($url['controller']){
                    case "config":
                        $config = false;
                        if(!array_key_exists(0, $url)){
                            if(array_key_exists('Group', $auth) && is_array($auth['Group']) && array_key_exists('settings', $auth['Group']) && is_array($auth['Group']['settings']) && array_key_exists($url['controller'], $auth['Group']['settings']) && is_array($auth['Group']['settings'][$url['controller']])){
                                foreach($auth['Group']['settings'][$url['controller']] as $k => $v){
                                    if($config === false && $v == 1){
                                        $config = $k;
                                    }
                                    
                                }
                            }
                        }else{
                            $config = $url[0];
                        }
                        if($url['action'] == 'index' && array_key_exists($config, $rights[$url['controller']]) && $rights[$url['controller']][$config] == true){
                            return true;
                        }
                        break;
                    case "images":
                        if(array_key_exists($url['action'], $rights[$url['controller']])){
                            if(is_array($rights[$url['controller']][$url['action']])){
                                if(array_key_exists(0, $url) && array_key_exists($url[0], $rights[$url['controller']][$url['action']]) && $rights[$url['controller']][$url['action']][$url[0]] == true){
                                    return true; // group actions
                                }
                            }else if($rights[$url['controller']][$url['action']] == true){
                                return true;
                            }
                        }
                        break;
                    case "elements":
                        if(array_key_exists(0, $url) && array_key_exists($url[0], $rights[$url['controller']]) && array_key_exists($url['action'], $rights[$url['controller']][$url[0]])){
                            if(is_array($rights[$url['controller']][$url[0]][$url['action']])){
                                if(array_key_exists(1, $url) && array_key_exists($url[1], $rights[$url['controller']][$url[0]][$url['action']]) && $rights[$url['controller']][$url[0]][$url['action']][$url[1]] == true){
                                    return true; // group actions
                                }
                            }else if($rights[$url['controller']][$url[0]][$url['action']] == true){
                                return true;
                            }
                        }
                        break;
                    default:
                        if(array_key_exists($url['action'], $rights[$url['controller']])){
                            if(is_array($rights[$url['controller']][$url['action']])){
                            
                            }else if($rights[$url['controller']][$url['action']] == true){
                                return true;
                            }
                        }
                        break;
                }
            }else{
                switch($url['controller']){
                    case "nodes":
                        if(array_key_exists('structures', $rights) && array_key_exists($url['controller'], $rights['structures']) && array_key_exists($url['action'], $rights['structures'][$url['controller']])){
                            if(is_array($rights['structures'][$url['controller']][$url['action']])){
                            
                            }else if($rights['structures'][$url['controller']][$url['action']] == true){
                                return true;
                            }
                        }
                        break;
                    default:
                        if(array_key_exists(0, $url)){
                            switch($url[0]){
                                case "elements":
                                    if(array_key_exists(1, $url) && array_key_exists($url[0], $rights) && is_array($rights[$url[0]]) && array_key_exists($url[1], $rights[$url[0]]) && is_array($rights[$url[0]][$url[1]]) && array_key_exists($url['controller'], $rights[$url[0]][$url[1]])){
                                        if(is_array($rights[$url[0]][$url[1]][$url['controller']]) && array_key_exists($url['action'], $rights[$url[0]][$url[1]][$url['controller']]) && $rights[$url[0]][$url[1]][$url['controller']][$url['action']] == true){
                                            return true;
                                        }else if(!is_array($rights[$url[0]][$url[1]][$url['controller']]) && $rights[$url[0]][$url[1]][$url['controller']] == true){
                                            return true;
                                        }
                                    }
                                    break;
                                case "images":
                                    if(array_key_exists($url[0], $rights) && is_array($rights[$url[0]]) && array_key_exists($url['controller'], $rights[$url[0]]) && is_array($rights[$url[0]][$url['controller']]) && array_key_exists($url['action'], $rights[$url[0]][$url['controller']]) && $rights[$url[0]][$url['controller']][$url['action']] == true){
                                        return true;
                                    }
                                    break;
                            }
                        }
                        break;
                }
            }
        }
    }
    return false;
}

function action_width($icons, $flags = false){
    
    // init
    $width = 0;
    $margin = 5;
    $icon_width = 26;
    $flag_width = 26;
    $nof = 0; // number of flags
    $noi = is_int($icons) ? $icons : 0;
    
    // flags?
    if($flags){
        foreach(Configure::read('translations') as $k => $v){
            if($v['active'] === true){
                $nof++;
            }
        }
    }
    
    // calc
    $width = ($noi*($icon_width + $margin)) + ($nof*($flag_width + $margin));
    
    return $width;
}

function count_true($array) {
    $cnt = 0;
    if (is_array($array)) {
        foreach ($array as $k => $v) {
            if ($v == true) {
                $cnt++;
            }
        }
    }
    return $cnt;
}

function flag_callback_width($flags){
    
    // init
    $width = 0;
    $margin = 5;
    $icon_width = 26;
    $flag_width = 26;
    $nof = $flags;
    $noi = 1;
    
    // calc
    $width = ($nof*($flag_width + $margin)) - ($noi*($icon_width + $margin));
    
    return $width;
}

function get_locale(){
    $locale = false;
    if(isset($_SERVER) && array_key_exists('REQUEST_URI', $_SERVER)){
        $parts = array_filter(explode("/", $_SERVER['REQUEST_URI']));
        if(array_key_exists(2,$parts) && strlen($parts[2]) == 2){
            $locale = strtolower($parts[2]);
        }
    }
    return $locale;
}

function deeper($sql, $params, $change, $connection){
    
    // init
    $infos = [];
    
    $fetch = $connection->execute($sql, $params)->fetchAll('assoc');
    if(is_array($fetch)){
        foreach($fetch as $f){
            $infos[] = $f;
            foreach($change as $k => $v){
                $params[$k] = $f[$v];
            }
            $infos = array_merge($infos, deeper($sql, $params, $change, $connection));
        }
    }
    
    // return
    return $infos;
}

function sampling($chars, $size, $combinations = array()) {

    # if it's the first iteration, the first set 
    # of combinations is the same as the set of characters
    if (empty($combinations)) {
        $combinations = $chars;
    }

    # we're done if we're at size 1
    if ($size == 1) {
        return $combinations;
    }

    # initialise array to put new values in
    $new_combinations = array();

    # loop through existing combinations and character set to create strings
    foreach ($combinations as $combination) {
        foreach ($chars as $char) {
            $new_combinations[] = $combination . $char;
        }
    }

    # call same function again for the next iteration
    return sampling($chars, $size - 1, $new_combinations);

}
