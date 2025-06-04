<?php

    function empty_html($html){
        $html = trim(strip_tags($html));
        if(!empty($html)){
            return false;
        }
        return true;
    }
    
    function is_valid($times){
        $valid = false;
        if(!empty($times)){
            $times = array_filter(explode("|", $times));
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
        return $valid;
    }
