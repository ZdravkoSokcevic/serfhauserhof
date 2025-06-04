<?php

    /*
     * functions needed for frontend AND backend
     */

    function infoTable($data, $context, $infos){

        // init
        $line = 0;
        $table = '';
        $skip = ['id','captcha','g-recaptcha-response','c-info','h-info'];
        $map = array_key_exists('map', $infos) ? $infos['map'] : [];

        // special behavioir for coupon
        if($context['view'] == 'coupon' && array_key_exists('coupon_type', $data)){
            if($data['coupon_type'] == 'vacation'){
                $skip = array_merge($skip, ['value']);
            }else if($data['coupon_type'] == 'value'){
                $skip = array_merge($skip, ['arrival','departure','adults','children','comment']);
            }
        }

        if(is_array($data)){
            $table .= '<table cellpadding="5" cellspacing="0" width="100%">';
            foreach($data as $k => $v){
                if(!in_array($k, $skip)){

                    // key
                    $value = '';
                    $key = array_key_exists('desc', $map) && is_array($map['desc']) && array_key_exists($k, $map['desc']) ? $map['desc'][$k] : ucfirst($k);

                    // value
                    switch($k){
                        case "salutation":
                        case "salutation_recipient":
                            $value = array_key_exists('salutations', $infos) && is_array($infos['salutations']) && array_key_exists($v, $infos['salutations']) ? $infos['salutations'][$v] : trim($v);
                            break;
                        case "email":
                            $value = trim($v);
                            if(!empty($value)){
                                $value = '<a href="mailto:' . $value . '">' . $value . '</a>';
                            }
                            break;
                        case "country":
                            $value = array_key_exists('countries', $infos) && is_array($infos['countries']) && array_key_exists($v, $infos['countries']) ? $infos['countries'][$v] : trim($v);
                            break;
                        case "privacy":
                        case "newsletter":
                            if($v == 1){
                                $value = array_key_exists('misc', $map) && is_array($map['misc']) && array_key_exists('yes', $map['misc']) ? $map['misc']['yes'] : $v;
                            }else{
                                $value = array_key_exists('misc', $map) && is_array($map['misc']) && array_key_exists('no', $map['misc']) ? $map['misc']['no'] : $v;
                            }
                            break;
                        case "coupon_type":
                            $value = array_key_exists('options', $map) && is_array($map['options']) && array_key_exists($v, $map['options']) ? $map['options'][$v] : ucfirst($v);
                            break;
                        case "message":
                        case "comment":
                            $value = nl2br($v);
                            break;
                        case "arrival":
                        case "departure":
                            if(!empty($v)){
                                $value = date("d.m.Y", strtotime($v));
                            }else{
                                $value = '';
                            }
                            break;
                        case "ages":
                            $ages = $glue = '';
                            if(is_array($v) && count($v) > 0){
                                foreach($v as $option){
                                    if(array_key_exists('age', $option)){
                                        $ages .= $glue . $option['age'];
                                        $glue = ', ';
                                    }
                                }
                            }
                            if(strlen($glue) > 0){
                                $value = $ages;
                            }
                            break;
                        case "rooms":
                            $rooms = $glue = '';
                            if(is_array($v) && count($v) > 0){
                                foreach($v as $option){
									$rooms .= $glue;
									if(count($option) > 1) $rooms .= '&bull; ';
                            		$person_room_glue = $persons_and = '';
									if(array_key_exists('adults', $option) && !empty($option['adults'])){
										$rooms .= sprintf(__dn('fe', '%s adult', '%s adults', $option['adults']), $option['adults']);
										$persons_and = ' ' . __d('fe', 'and') . ' ';
										$person_room_glue = ' ' . __d('fe', 'in a room') . ' ';
									}
									if(array_key_exists('children', $option) && !empty($option['children'])){
										$rooms .= $persons_and . sprintf(__dn('fe', '%s child', '%s children', $option['children']), $option['children']);
										$person_room_glue = ' ' . __d('fe', 'in a room') . ' ';
										if(array_key_exists('ages', $option) && is_array($option['ages']) && count($option['ages']) > 0){
											$agestring = $age_glue = '';
											$agecnt = 1;
											foreach($option['ages'] as $_age){
												$agestring .= $age_glue . $_age['age'];
												$agecnt++;
												if($agecnt == count($option['ages'])){
													$age_glue = ' ' . __d('fe', 'and') . ' ';
												} else{
													$age_glue = ', ';
												}
											}
											if(!empty($agestring)){
												if(count($option['ages']) == 1){
													$rooms .= ' (' . sprintf(__d('fe', 'at the age of %s'), $agestring) . ')';
												} else{
													$rooms .= ' (' . sprintf(__d('fe', 'at the ages of %s'), $agestring) . ')';
												}
											}
										}
									}
                                    if(array_key_exists('room', $option) && !empty($option['room'])){
                                        if(array_key_exists($option['room'],$infos['rooms'])){
                                            $rooms .= $person_room_glue . '"' . $infos['rooms'][$option['room']] . '"';
                                        }else{
                                            $rooms .= $person_room_glue . '"' . $map['misc']['missing_room'] . ' (' . $option['room'] . ')"';
                                        }
                                        $glue = '</br>';
                                    }
									if(array_key_exists('package', $option) && !empty($option['package'])){
										if(array_key_exists($option['package'],$infos['packages'])){
											$rooms .= ' ' . sprintf(__d('fe', 'with the package "%s"'), $infos['packages'][$option['package']]);
                                        }else{
                                            $rooms .= $person_room_glue . '"' . $map['misc']['missing_package'] . ' (' . $option['package'] . ')"';
                                        }
									}
                                }
                            }
                            if(strlen($glue) > 0){
                                $value = $rooms;
                            }
                            break;
                        case "room":
                            if(array_key_exists($v,$infos['rooms'])){
                                $value = $infos['rooms'][$v];
                            }else{
                                $value = $map['misc']['missing_room'] . ' (' . $v . ')';
                            }
                            break;
                        case "packages":
                            $packages = $glue = '';
                            if(is_array($v) && count($v) > 0){
                                foreach($v as $option){
                                    if(array_key_exists('package', $option) && !empty($option['package'])){
                                        if(array_key_exists($option['package'],$infos['packages'])){
                                            $packages .= $glue . $infos['packages'][$option['package']];
                                        }else{
                                            $packages .= $glue . $map['misc']['missing_package'] . ' (' . $option['package'] . ')';
                                        }
                                        $glue = '</br>';
                                    }
                                }
                            }
                            if(strlen($glue) > 0){
                                $value = $packages;
                            }
                            break;
                        case "interests":
                            $options = $glue = '';
                            if(is_array($v) && count($v) > 0){
                                foreach($v as $option){
                                    if(array_key_exists($option, $infos['interests']) && is_array($infos['interests'][$option]) && array_key_exists('title', $infos['interests'][$option])){
                                        $options .= $glue . $infos['interests'][$option]['title'];
                                    }else{
                                        $options .= $glue . $map['misc']['missing_interest'] . ' (' . $option . ')';
                                    }
                                    $glue = '</br>';
                                }
                            }
                            if(strlen($glue) > 0){
                                $value = $options;
                            }
                            break;
                        default:
                            $value = trim($v);
                            break;
                    }

                    if(!empty($value)){
                        $cls = $line%2 ? 'even' : 'odd';
                        $table .= '<tr class="' . $cls. '"><td class="left" valign="top"><strong>' . $key . ':</strong></td><td class="right" valign="top">' . $value . '</td></tr>';
                        $line++;
                    }
                }
            }
            $table .= '</table>';
        }

        return $table;
    }

    function replyTeaser($data, $context, $infos){

        // init
        $teaser = '';

        if(array_key_exists('reply_teaser', $context) && is_array($context['reply_teaser']) && array_key_exists(0, $context['reply_teaser']) && is_array($context['reply_teaser'][0])){

            try{
                $img = $infos['fullBaseUrl'] . $context['reply_teaser'][0]['details']['image'][0]['details']['seo'][3];
                if($context['reply_teaser'][0]['details']['link'][0]['type'] == 'node'){
                    $url = $infos['fullBaseUrl'] . 'redirect' . DS . $infos['request']->params['language'] . DS . $context['reply_teaser'][0]['details']['link'][0]['details']['node']['route'];
                }else if($context['reply_teaser'][0]['details']['link'][0]['type'] == 'element' && $context['reply_teaser'][0]['details']['link'][0]['details']['code'] == 'link'){
                    $url = $context['reply_teaser'][0]['details']['link'][0]['details']['link'];
                }
            }catch(Exeption $e){
                $img = false;
                $url = false;
            }

            if($img && $url){
                $teaser .= '<table class="hidden-mobile" cellpadding="0" cellspacing="0" width="100%">';
                    $teaser .= '<tr class="hidden-mobile">';
                        $teaser .= '<td class="hidden-mobile">&nbsp;</td>';
                    $teaser .= '</tr>';
                    $teaser .= '<tr class="hidden-mobile">';
                        $teaser .= '<td class="hidden-mobile">';

                            $teaser .= '<table class="hidden-mobile" cellpadding="0" cellspacing="0" width="100%">';
                                $teaser .= '<tr class="hidden-mobile">';
                                    $teaser .= '<td class="hidden-mobile" style="width:270px; vertical-align:top;">';
                                        $teaser .= '<img style="width:100%;" src="' . $img . '" />';
                                    $teaser .= '</td>';
                                    $teaser .= '<td class="hidden-mobile" style="width:20px; line-height:0; font-size:0;">';
                                        $teaser .= '&nbsp;';
                                    $teaser .= '</td>';
                                    $teaser .= '<td class="hidden-mobile" style="width:270px; vertical-align:top;">';
                                        $teaser .= '<table class="hidden-mobile" cellpadding="0" cellspacing="0" width="100%">';
                                            $teaser .= '<tr class="hidden-mobile">';
                                                $teaser .= '<td class="hidden-mobile" style="vertical-align:top;">';
                                                    $teaser .= '<h2>' . $context['reply_teaser'][0]['details']['headline'] . '</h2>';
                                                    $teaser .= $context['reply_teaser'][0]['details']['content'];
                                                $teaser .= '</td>';
                                            $teaser .= '</tr>';
                                            $teaser .= '<tr class="hidden-mobile">';
                                                $teaser .= '<td class="hidden-mobile">&nbsp;</td>';
                                            $teaser .= '</tr>';
                                            $teaser .= '<tr class="hidden-mobile">';
                                                $teaser .= '<td class="hidden-mobile" style="height:40px; background-color:#8C7265; vertical-align:middle; text-align:center;">';
                                                    $teaser .= '<a style="color:#FFFFFF; text-decoration:none; vertical-align: middle; text-align:center; font-weight: bold;" href="' . $url . '">' . $context['reply_teaser'][0]['details']['linktext'] . '</a>';
                                                $teaser .= '</td>';
                                            $teaser .= '</tr>';
                                        $teaser .= '</table>';
                                    $teaser .= '</td>';
                                $teaser .= '</tr>';
                            $teaser .= '</table>';

                        $teaser .= '</td>';
                    $teaser .= '</tr>';
                    $teaser .= '<tr class="hidden-mobile">';
                        $teaser .= '<td class="hidden-mobile">&nbsp;</td>';
                    $teaser .= '</tr>';
                $teaser .= '</table>';
            }
        }
        return $teaser;
    }

    function custom_number_format($number, $decimals = 0, $dec_point = ".", $thousands_sep = ","){
        $res = number_format($number, $decimals, $dec_point, $thousands_sep);
        list(,$adp) = explode($dec_point, $res, 2);
        if($adp == '00'){
            $res = number_format($number, 0, $dec_point, $thousands_sep) . $dec_point . '-';
        }
        return $res;
    }

    function custom_array_filter($array) {
        if(is_array($array)){
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    $array[$k] = custom_array_filter($v);
                }
            }
            $array = array_filter($array, function($var){
                if(is_array($var)){
                    return count($var) > 0 ? true : false;
                }else{
                    return strlen($var) ? true : false;
                }
            });
        }
        return $array;
    }

    function vd(...$data)
    {
      $allowed_ips = [
        '185.71.89.39', // Milos
        '178.237.222.191', // Zdravko
        '178.237.223.58',
      ];
      if(in_array($_SERVER['REMOTE_ADDR'], $allowed_ips))
      {
        var_dump($data);
        die();
      }
    }

