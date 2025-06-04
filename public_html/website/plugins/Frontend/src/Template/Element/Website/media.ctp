<?php

    // init
    $check = false;
    if(is_array($media) && isset($position) && array_key_exists($position, $media)){
        $media = $media[$position];
    }else{
        $media = [];
    }
    
    $wrapper = isset($wrapper) ? $wrapper : '';
    $inner = isset($inner) && $inner == true ? true : false;
    $prev = false;
    $count = 1;
    $contain = 0;
	$prev_type_count = 0;
    $group = true;
    $open = false;
    
    // check
    foreach($media as $m){
        if(array_key_exists('type', $m)){
            if(is_array($m) && array_key_exists('details', $m) && is_array($m['details']) && count($m['details']) > 0){
                $check = true;
                $contain++;
            }
        }
    }

?>
<?php if($check){ ?>
<section class="media <?= $wrapper; ?> contain-<?= $contain; ?>">
    <?php if($inner){ ?><div class="<?= $wrapper.'-inner'; ?>"><?php } ?>
    <?php
    	$media_string = '';
        foreach($media as $m){
            if(array_key_exists('type', $m)){
                if(is_array($m) && array_key_exists('details', $m) && is_array($m['details']) && count($m['details']) > 0){
                    switch($m['type']){
                        case "image":
                            if(array_key_exists('details', $m) && is_array($m['details'])){
                            	$prev_type_count = $count;
                                $count = $prev == $m['type'] ? $count + 1 : 1;
                                if($wrapper == 'downloads'){
	                                // media groups
	                                if($group === true){
	                                    if($open === true && $prev !== false && 'download' !== $prev){
	                                        $media_string .= '</div></div>';
											$media_string = str_replace('##MEDIA-CONTAINS##', $prev_type_count, $media_string);
                                            $open = false;
	                                    }                            
	                                    if($count == 1){
	                                        $media_string .= '<div class="media-group ' . 'download' . '-group mg-contains-##MEDIA-CONTAINS##"><div class="media-group-inner">';
	                                        $open = true;
	                                    }
	                                }
                            		$media_string .= $this->element('Frontend.Website/' . 'download', ['element_content' => $m, 'prev' => $prev, 'count' => $count]);
									$prev = 'download';
                            	} else{
	                                // media groups
	                                if($group === true){
	                                    if($open === true && $prev !== false && $m['type'] !== $prev){
	                                        $media_string .= '</div></div>';
											$media_string = str_replace('##MEDIA-CONTAINS##', $prev_type_count, $media_string);
                                            $open = false;
	                                    }                            
	                                    if($count == 1){
	                                        $media_string .=  '<div class="media-group ' . $m['type'] . '-group mg-contains-##MEDIA-CONTAINS##"><div class="media-group-inner">';
	                                        $open = true;
	                                    }
	                                }
	                                
	                                $media_string .= '<section class="image" style="background-image: url(\'' . $m['details']['seo'][1] . '\'); background-repeat: ' . $m['details']['focus'][1]['css'] . '"></section>';
	                                $prev = $m['type'];
								}
                            }
                            break;
                        case "element":
                        	$prev_type_count = $count;
                            $count = $prev == $m['details']['code'] ? $count + 1 : 1;

                            // media groups
                            if($group === true){
                                if($open === true && $prev !== false && $m['details']['code'] !== $prev){
                                    $media_string .= '</div></div>';
									$media_string = str_replace('##MEDIA-CONTAINS##', $prev_type_count, $media_string);
                                    $open = false;
                                }                            
                                if($count == 1){
                                    $media_string .= '<div class="media-group ' . $m['details']['code'] . '-group mg-contains-##MEDIA-CONTAINS##"><div class="media-group-inner">';
                                    $open = true;
                                }
                            }
                                                        
                            $media_string .= $this->element('Frontend.Website/' . $m['details']['code'], ['element_content' => $m, 'prev' => $prev, 'count' => $count]);
                            
                            $prev = $m['details']['code'];
                            break;
                        case "node":
                            if(array_key_exists('details', $m) && is_array($m['details']) && array_key_exists('element', $m['details']) && is_array($m['details']['element'])){
                                $prev_type_count = $count;
                                $count = $prev == $m['type'] ? $count + 1 : 1;
                                
                                // media groups
                                if($group === true){
                                    if($open === true && $prev !== false && $m['type'] !== $prev){
                                        $media_string .= '</div></div>';
										$media_string = str_replace('##MEDIA-CONTAINS##', $prev_type_count, $media_string);
                                        $open = false;
                                    }                            
                                    if($count == 1){
                                        $media_string .= '<div class="media-group ' . 'type-' . $m['type'] . '-group mg-contains-##MEDIA-CONTAINS##"><div class="media-group-inner">';
                                        $open = true;
                                    }
                                }
                                
                                $media_string .= $this->element('Frontend.Website/' . $m['type'], ['element_content' => $m, 'prev' => $prev, 'count' => $count]);
                                $prev = $m['type'];                                
                            }
                            break;
                        case "category":
                            if(array_key_exists('details', $m) && is_array($m['details']) && array_key_exists('content', $m['details']) && is_array($m['details']['content'])){
                                $prev_type_count = $count;
                                $count = $prev == 'category-' . $m['details']['category']['code'] ? $count + 1 : 1;
                                
                                // media groups
                                if($group === true){
                                    if($open === true && $prev !== false && 'category-' . $m['details']['category']['code'] !== $prev){
                                        $media_string .= '</div></div>';
										$media_string = str_replace('##MEDIA-CONTAINS##', $prev_type_count, $media_string);
                                        $open = false;
                                    }                            
                                    if($count == 1){
                                        $media_string .= '<div class="media-group ' . 'category-' . $m['details']['category']['code'] . '-group mg-contains-##MEDIA-CONTAINS##"><div class="media-group-inner">';
                                        $open = true;
                                    }
                                }
                                
                                $media_string .= $this->element('Frontend.Website/category-' . $m['details']['category']['code'], ['category_content' => $m, 'prev' => $prev, 'count' => $count]);
                                $prev = 'category-' . $m['details']['category']['code'];                                
                            }
                            break;
                        default:
                            //TODO: delete this!
                            if($_SERVER['REMOTE_ADDR'] == '83.175.88.51'){
                                $media_string .= $m['type'];
                                $media_string .= "<pre>" . print_r($m,1) . "</pre>";
                            }
                            break;
                    }
                }
            }
        }
        
        // close
        if($group === true){
            if($open === true && $prev !== false){
                $media_string .= '</div></div>';
            }                            
        }
        
		//output
		$media_string = str_replace('##MEDIA-CONTAINS##', $count, $media_string);
		echo $media_string;
    ?>
    <?php if($inner){ ?></div><?php } ?>
</section>
<?php } ?>