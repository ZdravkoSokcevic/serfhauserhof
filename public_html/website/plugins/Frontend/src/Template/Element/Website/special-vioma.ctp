<?php

    // init
    $id = false;
    $parameter = '';
    
    $data = count($_GET) > 0 ? $_GET : [];
    if(count($data) > 0){
        $from = array_key_exists('arrival', $data) && !empty($data['arrival']) ? $data['arrival'] : false;
        $to = array_key_exists('departure', $data) && !empty($data['departure']) ? $data['departure'] : false;
        $adults = array_key_exists('adults', $data) && $data['adults'] > 0 ? (int) $data['adults'] : false;
        $room = array_key_exists('room', $data) ? (int) $data['room'] : false;
        $package = array_key_exists('package', $data) ? (int) $data['package'] : false;
        
        if($adults){
            $parameter .= ", persons_adults: '" . $adults . "'";
        }
        
        if($from){
            if(strpos($from,".") !== false || strpos($from,"/") !== false){
                $sep = strpos($from,".") !== false ? '.' : '/';
                list ($sday, $smon, $syear) = explode($sep, $from); // beim Punkt aufteilen 
                $_sdate = mktime(0,0,0,$smon,$sday,$syear);
            }else{
                $_sdate = strtotime($from);
            }
            $parameter .= ", calendar_date_from: '" . date('Y-m-d',$_sdate) . "'";
            
            if($to){
                if(strpos($to,".") !== false || strpos($to,"/") !== false){
                    $sep = strpos($to,".") !== false ? '.' : '/';
                    list ($eday, $emon, $eyear) = explode($sep, $to); // beim Punkt aufteilen 
                    $_edate = mktime(0,0,0,$emon,$eday,$eyear);
                }else{
                    $_edate = strtotime($to);
                }
                $parameter .= ", calendar_date_to: '" . date('Y-m-d',$_edate) . "'";
            }
            
        }
        
        if($room){
            $parameter .= ",  'items[]':'hrt:" . $room . "'";
        }
        
        if($package){
            $parameter .= ", package: '" . $package . "'";
        }
    }

?>
<div class="inner">
	<div id="vri-container-<?php echo $id; ?>"></div>
	<script type="text/javascript" charset="UTF-8">
		( function ( v, i, o, m, a ){
			if ( !( o in v ) ) {
				v.vioma_vri=o;
				v[o] || ( v[o] = function ( ){ ( v[o].q = v[o].q || [] ).push ( arguments ); } );
				m = i.createElement( 'script' ), a = i.scripts[0];
				m.src = 'https://cst-client-hotel-schalber.viomassl.com/js/vri/vri.js';
				a.parentNode.insertBefore ( m, a );
			}
		} )( window, document, 'vcst' );
		vcst( {load: 'init', url: 'https://cst-client-hotel-schalber.viomassl.com/', set_language: '<?= $this->request->params['language']; ?>'} );
		vcst( {id: <?php echo $id . $parameter; ?>} );
	</script>
</div>