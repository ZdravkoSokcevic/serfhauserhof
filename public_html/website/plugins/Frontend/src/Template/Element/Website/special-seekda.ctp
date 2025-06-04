<?php
//preload image
$preload_img = 'https://static.seekda.com/assets/images/skd_spinner.gif';
if(isset($special_element_content['details']['preloadImage']) && !empty($special_element_content['details']['preloadImage'])){
	$preload_img = $special_element_content['details']['preloadImage'];
}

//hotel_id
$hotel_id = false;
if(isset($special_element_content['details']['hotelId']) && !empty($special_element_content['details']['hotelId'])){
	$hotel_id = $special_element_content['details']['hotelId'];
}

//api key
$api_key = false;
if(isset($special_element_content['details']['seekdaApiKey']) && !empty($special_element_content['details']['seekdaApiKey'])){
	$api_key = $special_element_content['details']['seekdaApiKey'];
}
?>

<?php if($hotel_id !== false && $api_key !== false){ ?>
	<!-- Script-Tag -->
	<script src="https://cloud.seekda.com/w/w-dynamic-shop/hotel:<?= $hotel_id ?>/<?= $api_key ?>.js"></script>
	
	<section class="seekda main">
	    <div>
	    
	    	<!-- Check Availability -->
	        <div class="skd-widget" data-skd-widget="check-availability" data-skd-language-code="<?= $this->request->params['language']; ?>" data-skd-show-language="false" data-skd-show-currency="false" data-skd-show-header="false" data-skd-is-themeable="true" data-skd-auto-search="true" data-skd-show-roombox="false" data-skd-children-min="0" data-skd-auto-scroll="false" data-skd-send-to-groups="A" data-skd-listen-to-groups="B" ><div style="width:100%; min-height:50px; text-align:center;"><img src="<?= $preload_img ?>" /><noscript>Your browser doesn't support JavaScript or it has been disabled. To use the booking engine, please make sure to get JavaScript running.</noscript></div></div>
	        
	        <!-- Offer List -->
	        <div class="skd-widget" data-skd-widget="offer-list" data-skd-packages-first="true" data-skd-language-code="<?= $this->request->params['language']; ?>" data-skd-is-themeable="true" data-skd-sort-order="price" data-skd-hide-packages="false" data-skd-hide-rates="false" data-skd-cheapest-offer-only="false" data-skd-show-services="true" data-skd-send-to-groups="B" data-skd-listen-to-groups="A"><div style="width:100%; min-height:100px; text-align:center;"><img src="<?= $preload_img ?>" /><noscript>Your browser doesn't support JavaScript or it has been disabled. To use the booking engine, please make sure to get JavaScript running.</noscript></div></div>
	   
	    </div>
	</section>
	
	<script type="text/javascript">
	
	    var _skd = window._skd || {};
	
	    _skd.callbacks = _skd.callbacks || {};
	    _skd.callbacks.dsr = _skd.callbacks.dsr || {};
	
	    _skd.callbacks.dsr.searchBtnClick = function(data) {
	        ga('send', 'pageview', 'seekda Suche ausgeführt'); // send pageview if searchBtnClicked
	    }
	
	    _skd.callbacks.dsr.viewOffers = function(data) {
	        ga('send', 'pageview', 'seekda Übersicht Angebote'); // send pageview when offers are shown
	    }
	
	    _skd.callbacks.dsr.viewRoom = function(data) {
	        ga('send', 'pageview', 'seekda Detail Zimmer'); // send pageview when user selects a room
	    }
	
	    _skd.callbacks.dsr.viewPackage = function(data) {
	        ga('send', 'pageview', 'seekda Detail Paket'); // send pageview when user selects a package
	    }
	
	    _skd.callbacks.dsr.viewOfferDetails = function(data) {
	        ga('send', 'pageview', 'seekda Detail Zimmer/Paket'); // send pageview when offer details are shown
	    }
	
	    _skd.callbacks.dsr.viewPersInfo = function(data) {
	        ga('send', 'pageview', 'seekda Persönliche Informationen'); // send pageview when personal info entry step is loaded
	    }
	
	    _skd.callbacks.dsr.viewReview = function(data) {
	        ga('send', 'pageview', 'seekda Auftragsübersicht'); // send pageview when review step is loaded
	    }
	
	    _skd.callbacks.dsr.viewConfirmation = function(data) {
	        ga('send', 'pageview', 'seekda Abschluss Buchung'); // send pageview when confirmation step is loaded
	    }
	
	</script>
<?php } ?>