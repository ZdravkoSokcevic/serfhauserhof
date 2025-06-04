<?php
    use Cake\Core\Configure;
    $api_key = 'AIzaSyBW8pUCeF1XqxffKNeDbO1KXJna2qgrM4Q'; //you could enter one here
	$api_key = $api_key !== false ? '&key=' . $api_key : '';
    echo $this->Html->script('https://maps.googleapis.com/maps/api/js?language=' . $this->request->params['language'] . $api_key);
?>


<section class="main routeplanner">
	<div class="inner">
	    <div class="map">
	        <div id="route-error" class="route-error"><?php echo __d('fe', 'Route could not be calculated! Please check your entry and try again!'); ?></div>
	        <div id="map-canvas"></div>
	        <div id="control">
	            <form onsubmit="calcRoute(); return false;" id="routeplanner-form">
	                <input type="text" id="start" value="" placeholder="<?php echo __d('fe', 'Start'); ?>" />
	                <button type="submit" id="calc" class="button submit" onclick="calcRoute();"><i class="fa fa-search" aria-hidden="true"></i></button>
	                <div class="clear"></div>
	            </form>
	        </div>
	        <div id="directions-panel"></div>
	    </div>
    </div>
</section>
<script>

    var directionDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;
    var hotel;
    var stepDisplay;
    var markerArray = [];
    var info;
    var position;

    // info
    position = {'lat': <?= $special_element_content['details']['latitude']; ?>, 'lang':<?= $special_element_content['details']['longitude']; ?>};

    info = '<div>';
    info += '<div class="address"><?= Configure::read('config.default.street'); ?>& middot;<?= Configure::read('config.default.zip'); ?><?= Configure::read('config.default.city'); ?></div>';
    info += '<div><a href="tel:<?= Configure::read('config.default.phone-plain'); ?>"><i class="fa fa-phone" aria-hidden="true"></i> <?= Configure::read('config.default.phone'); ?></a></div>';
    info += '<div><a href="mailto:<?= Configure::read('config.default.email'); ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?= Configure::read('config.default.email'); ?></a></div>';info += '<div>';

    function initialize() {
        try{
            directionsDisplay = new google.maps.DirectionsRenderer();
            hotel = new google.maps.LatLng(position.lat,position.lang);
            var mapOptions = {
                zoom: <?= $special_element_content['details']['zoom']; ?>,
                mapTypeId: google.maps.MapTypeId.<?= $special_element_content['details']['map']; ?>,
                center: hotel,
                gestureHandling: 'cooperative',
                scrollwheel: false,
                mapTypeControl: false,
                draggable: true,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                },
                scaleControl: false,
                streetViewControl: false
            }
            $('#map-canvas').css('background-image', 'none');
            map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById('directions-panel'));

            // control
            var control = document.getElementById('control');
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(control);

            // Instantiate an info window to hold step text.
            stepDisplay = new google.maps.InfoWindow();

            // Set marker
            var marker = new google.maps.Marker({
                position : hotel,
                map : map,
                zIndex : google.maps.Marker.MAX_ZINDEX + 1
            });
            markerArray[0] = marker;

            google.maps.event.addListener(marker, 'click', function() {
                stepDisplay.setContent(info);
                stepDisplay.open(map, marker);
            });

        }catch(e) { };
    }

    function calcRoute() {
        $('#directions-panel').html('');
        $('#route-error').slideUp('fast');
        var start = document.getElementById('start').value;
        var request = {
            origin : start,
            destination : hotel,
            unitSystem : google.maps.DirectionsUnitSystem.METRIC,
            provideRouteAlternatives : true,
            travelMode : google.maps.DirectionsTravelMode.DRIVING,
        };
        directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {

                // replace special chars in text for start/end address
                response.routes[0].legs[0].start_address = response.routes[0].legs[0].start_address.replace(/ß/g, 'ss');
                response.routes[0].legs[0].end_address = response.routes[0].legs[0].end_address.replace(/ß/g, 'ss');

                directionsDisplay.setDirections(response);
                $('html,body').animate({
                    scrollTop : $("#directions-panel").offset().top
                }, 'fast');
            } else {
                $('#route-error').slideDown('fast');
            }
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
