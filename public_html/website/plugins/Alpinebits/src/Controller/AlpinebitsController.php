<?php
//THE URL FOR REACHING THIS IS:
// https://www.domain.com/alpinebits/de/v1/

namespace Alpinebits\Controller;

use Cake\Core\Configure;
use Alpinebits\Controller\AppController;

class AlpinebitsController extends AppController
{
    
    public function v1_getVersion(){
    	$this->auth();
		$content = 'OK:' . $this->getAlpineBitsVersion();
		
		header('Content-type: application/xml');
		echo $content;
		exit;
    }
	
    public function v1_getCapabilities(){
    	$this->auth();
		$content = 'OK:';
		$glue = '';
		foreach(Configure::read('alpinebits.capabilities') as $capability){
			$content .= $glue . $capability;
			$glue = ',';
		}
		
		header('Content-type: application/xml');
		echo $content;
		exit;
    }
	
    public function v1_OTA_Read_GuestRequests(){
    	$this->auth();
		$content = '<?xml version="1.0" encoding="UTF-8"?>';
		$content .= '<OTA_ResRetrieveRS xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opentravel.org/OTA/2003/05" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_ResRetrieveRS.xsd" Version="7.000">' . "\n";
		
		$resStatus = 'Requested'; //INFO: should be set to "Reserved" for the booking form (could also be "Modify" or "Cancelled", but we won't need that)
		$xml = @simplexml_load_string($_POST['request']);
		
		if(is_object($xml)){
		
			$where = $where_nl = "";
			$dategiven = false;
			
			if(isset($xml->ReadRequests->HotelReadRequest->SelectionCriteria) && isset($xml->ReadRequests->HotelReadRequest->SelectionCriteria->attributes()->Start)){
				$dategiven = strtotime((string) $xml->ReadRequests->HotelReadRequest->SelectionCriteria->attributes()->Start);
				$where .= " WHERE `type`='request'";
				$where_nl .= " WHERE `status` IN('subscribed','unsubscribed')";
			} else{
				//Info: AlpineBits wants only new, if no date is given
				$where .= " WHERE `retrieved`='0' AND `type`='request'";
				$where_nl .= " WHERE `retrieved`='0' AND `status` IN('subscribed','unsubscribed')";
			}
			
			//get newsletter subscriptions
			$sql = "SELECT * FROM `newsletter`".$where_nl;
			$result_nl = $this->connection->execute($sql)->fetchAll('assoc');
			foreach($result_nl as $r_nl_key => $r_nl){
				$result_nl[$r_nl_key]['data'] = json_decode(utf8_encode($result_nl[$r_nl_key]['data']), true);
				if($result_nl[$r_nl_key]['data'] !== false){
					$result_nl[$r_nl_key]['type'] = 'newsletter';
					$result_nl[$r_nl_key]['created'] = strtotime($result_nl[$r_nl_key]['created']);
					$result_nl[$r_nl_key]['modified'] = strtotime($result_nl[$r_nl_key]['modified']);
				} else{
					unset($result_nl[$r_nl_key]);
				}
			}
			
			//get requests
			$sql = "SELECT * FROM `emails`".$where;
			$result = $this->connection->execute($sql)->fetchAll('assoc');
			
			if (is_array($result) && is_array($result_nl)) {
				
				$result = array_merge($result, $result_nl);
						
				$content .= '<Success/>
				<ReservationsList>';
				
				foreach($result as $record) {
					//save infos for later
					$id = $record['id'];
					$type = $record['type'];
					
					//encode
					foreach($record as $k => $v){
						if(is_string($v)){
							$record[$k] = utf8_encode($v);
						}
					}
					
					//check and reformat
					$record = $this->checkAndRefomat($record);
					
					
					//check for date
					if($record !== false && $dategiven !== false && $dategiven > $record['created']){
						$record = false;
					}
					
					if($record === false){
						//something is not right -> don't send it
						if($type == 'newsletter'){
							$sql = "UPDATE `newsletter` SET `retrieved`='1' WHERE `id`='".$id."'";
						} else{
							$sql = "UPDATE `emails` SET `retrieved`='1' WHERE `id`='".$id."'";
						}
						$this->connection->execute($sql);
						continue;
					}
					
					$country_found = false;
					if(isset($record['country'])){
						foreach(Configure::read('alpinebits.countries') as $clang => $clangcountries){
							if($country_found===true) break;
							foreach($clangcountries as $ck => $cv){
								if($country_found===true) break;
								if(str_replace(' ', '', strtolower($cv)) == str_replace(' ', '', strtolower($record['country']))){
									$record['country'] = strtolower($ck);
									$country_found = true;
								}
							}
						}
					} else{
						$record['country'] = '';
					}
					
					$content .= '<HotelReservation CreateDateTime="'.$record['created'].'" ResStatus="'.$resStatus.'">';
						$content .= '<UniqueID Type="14" ID="'.$record['id'].'"/>'; //for "Requested", "Reserved" and "Modify" -> Type=14, for "Cancelled," -> Type=15
						
						//ROOMS
						if(in_array($record['type'], array('request'/*, 'booking'*/))){
							$content .= '<RoomStays>';
								if(in_array($record['type'], array('request'/*, 'booking'*/))){
									foreach($record['rooms'] as $room){
										if(isset($room['code']) && !empty($room['code'])){
											$content .= '<RoomStay>';
													$content .= '<RoomTypes>';
														$content .= '<RoomType RoomTypeCode="'.$room['code'].'" RoomClassificationCode="42"/>'; //42 follows the OTA list “Guest Room Info” (GRI) and stands for "Room" (There would also be appartement, Suite...) 
													$content .= '</RoomTypes>';
													
													if(array_key_exists('package', $room) && is_array($room['package']) && array_key_exists('code', $room['package']) && $room['package']['code'] !== false && !empty($room['package']['code'])){
														$content .= '<RatePlans>';
															$content .= '<RatePlan RatePlanCode="' . $room['package']['code'] . '">';
																//$content .= '<MealsIncluded MealPlanIndicator="true" MealPlanCodes="'.$record['service'].'"/>'; // MealPlanCodes -> 1=all inclusive / 3=bed and breakfast / 10=full board / 12=half board / 14=room only
															$content .= '</RatePlan>';
														$content .= '</RatePlans>';
													}
													
													$content .= '<TimeSpan Start="'.date('Y-m-d', $record['date-from']).'" End="'.date('Y-m-d', $record['date-to']).'"/>';
													
													$content .= '<GuestCounts>'; //Info: AgeQualifyingCode 10=adult, 8=child
														$room['adults'] = isset($room['adults']) ? $room['adults'] : '0';
														$content .= '<GuestCount Count="'.$room['adults'].'" AgeQualifyingCode="10"/>'; //INFO a Guestcount with no age-attribute means adults
														if(isset($room['children']) && is_array($room['children']) && count($room['children'])>0){
															foreach($room['children'] as $_child_age => $_child_count){
																$content .= '<GuestCount Count="'.$_child_count.'" Age="'.str_replace(array('<', '>'), array('', ''), $_child_age).'" AgeQualifyingCode="8"/>'; //INFO we could have multiple childcounts with different age-atributes
															}
														}
													$content .= '</GuestCounts>';
													
													//$content .= '<Total AmountAfterTax="299" CurrencyCode="EUR"/>'; //INFO: (would be requred in Reservations) AmountAfterTax has to be the price which was shown to the user
											$content .= '</RoomStay>';
										}
									}
								} else{
									//if this doesn't come from a reservation or request form, we put a fake-Room here, because "RoomStays", "RoomStay", "RoomTypes" and "RoomType" are mandatory
									$content .= '<RoomTypes>';
										$content .= '<RoomType RoomTypeCode="FAKE" RoomClassificationCode="42"/>'; 
									$content .= '</RoomTypes>';
								}
							$content .= '</RoomStays>';
						}
						
						//GUESTS
						$content .= '<ResGuests>';
							$content .= '<ResGuest>';
								$content .= '<Profiles>';
									$content .= '<ProfileInfo>';
										$content .= '<Profile>';
											$salutation = '';
       										Configure::load('salutations');
											$salutation_cfg = Configure::read('salutations');
											$salutation_cfg[1]['gender'] = 'Male';
											$salutation_cfg[1]['by-lang'] = [
												'de' => 'Herr',
												'en' => 'Mr.',
												'it' => 'Sig.',
											];
											$salutation_cfg[2]['gender'] = 'Female';
											$salutation_cfg[2]['by-lang'] = [
												'de' => 'Frau',
												'en' => 'Mrs.',
												'it' => 'Sig.ra.',
											];
											$record['gender'] = '';
											if(isset($record['language']) && !empty($record['language']) && isset($record['salutation']) && array_key_exists($record['salutation'], $salutation_cfg)){
												if(!empty($record['language']) && !empty($record['salutation']) && $record['salutation']!='Unknown'){
													$salutation = $salutation_cfg[$record['salutation']]['by-lang'][$record['language']];
													$record['gender'] = $salutation_cfg[$record['salutation']]['gender'];
												}
											}
											$birthday = '';
											if(isset($record['birthday']) && !empty($record['birthday']) && $record['birthday'] !== false){
												$birthday = ' BirthDate="'.date('Y-m-d', strtotime($record['birthday'])).'"'; //INFO:we don't get that now
											}
											$content .= '<Customer Gender="'.$record['gender'].'"'.$birthday.' Language="'.$record['language'].'">';
												$content .= '<PersonName>';
													if(!empty($salutation)){
														$content .= '<NamePrefix>';
															$content .= $this->cData($salutation);
														$content .= '</NamePrefix>';
													}
													$content .= '<GivenName>';
														$content .= $this->cData(ucfirst(strtolower($record['firstname'])));
													$content .= '</GivenName>';
													$content .= '<Surname>';
														$content .= $this->cData(ucfirst(strtolower($record['lastname'])));
													$content .= '</Surname>';
													$content .= '<NameTitle>'; //INFO: we don't get that yet
														$content .= $this->cData(ucfirst(strtolower($record['title'])));
													$content .= '</NameTitle>';
												$content .= '</PersonName>';
												$content .= '<Telephone PhoneTechType="1" PhoneNumber="'.$record['phone'].'"/>'; //(could additionally give more numbers with Techtype 3=Fax or 5=Mobile)
												
												$newsletter = '';
												if(isset($record['newsletter']) && $record['newsletter'] !== false){
													if($record['type'] == 'newsletter'){
														if($record['newsletter'] == '1'){
															$newsletter = ' Remark="newsletter:yes"';
														} else if($record['newsletter'] == '0'){
															$newsletter = ' Remark="newsletter:no"';
														}
													} else{
														if($record['newsletter'] == '1'){
															$newsletter = ' Remark="newsletter:yes"';
														}
													}
												}
												
												$content .= '<Email'.$newsletter.'>'.$record['email'].'</Email>';
												$content .= '<Address>'; //INFO: there would be an attribute -> Remark="catalog:yes"
													$content .= '<AddressLine>';
														$content .= $this->cData(ucfirst(strtolower($record['street'])));
													$content .= '</AddressLine>';
													$content .= '<CityName>';
														$content .= $this->cData(ucfirst(strtolower($record['city'])));
													$content .= '</CityName>';
													$content .= '<PostalCode>';
														$content .= $this->cData($record['zip']);
													$content .= '</PostalCode>';
													$content .= '<CountryName Code="'.strtoupper($record['country']).'"/>';
												$content .= '</Address>';
											$content .= '</Customer>';
										$content .= '</Profile>';
									$content .= '</ProfileInfo>';
								$content .= '</Profiles>';
							$content .= '</ResGuest>';
						$content .= '</ResGuests>';
						
						//INFOS
						$content .= '<ResGlobalInfo>';
						
							if((isset($record['note']) && !empty($record['note']))/* || (isset($record['brochures']) && !empty($record['brochures']))*/ || (isset($record['type']) && $record['type']=='request')){
								$content .= '<Comments>';
									//brochure comment
									//TODO:maybe do brochures in the future
									/*if(isset($record['brochures']) && !empty($record['brochures'])){
										$brochures = explode(',',$record['brochures']);
										
										if(is_array($brochures) && count($brochures)>0){
											$content .= '<Comment Name="Gewünschte Prospekte">';
												$brochurecount = 1;
												foreach($brochures as $brochure_key => $brochure){
													$content .= '<ListItem ListItem="'.$brochure_key.'" Language="'.$record['language'].'">';
														$content .= $this->cData($brochure);
														if($brochurecount >= count($brochures)) $content .= "\n";
													$content .= '</ListItem>';
													$brochurecount++;
												}
											$content .= '</Comment>';
										}
									}*/
									//guests note comment
									if(isset($record['note']) && !empty($record['note'])){
										$content .= '<Comment Name="NACHRICHT DES GASTES:">';
											$content .= '<Text>';
												$content .= $this->cData("\t".$record['note']);
												$content .= "\n";
											$content .= '</Text>';
										$content .= '</Comment>';
									}
									//booking info comment
									if(isset($record['type']) && !empty($record['type']) && in_array($record['type'], array('request'/*, 'booking' */))){
										$content .= '<Comment Name="INFOS ZUR BUCHUNG:">';
											$content .= '<Text>';
												$content .= $this->cData("\t".'Anreise: ' . date('d.m.Y', $record['date-from'])) . "\n";
												$content .= $this->cData("\t".'Abreise: ' . date('d.m.Y', $record['date-to'])) . "\n";
												$content .= $this->cData("\t".'Nächte: ' . round((strtotime(date('d-m-Y 00:00:00',$record['date-to'])) - strtotime(date('d-m-Y 00:00:00',$record['date-from']))) / (60 * 60 * 24), 0, PHP_ROUND_HALF_UP) . "\n");
												$content .= $this->cData("\t".'Sprache: ' . $record['language']);
												if(isset($record['rooms']) && is_array($record['rooms']) && count($record['rooms'])>0){
													$content .= "\n";
													$roomcount = 1;
													$content .= "\nZIMMER:\n";
													foreach($record['rooms'] as $room){
														$content .= "------\n";
														$content .= $this->cData('Zimmer: ' . $room['code'] . "\n");
														if(isset($room['adults']) && !empty($room['adults']) && $room['adults'] > 0){
															$content .= $this->cData("\t".'Erwachsene: ' . $room['adults'] . "\n");
														}
														if(isset($room['children']) && is_array($room['children']) && count($room['children'])>0){
															foreach($room['children'] as $_child_age => $_child_count){
																$content .= $this->cData("\t".'Kind ('.$_child_age.' Jahre): ' . $_child_count . "\n");
															}
														}
														if(isset($room['package']) && is_array($room['package'])){
															$_package_text = '';
															if(array_key_exists('title', $room['package']) && !empty($room['package']['title'])){
																$_package_text .= $room['package']['title'];
															}
															if(array_key_exists('code', $room['package']) && !empty($room['package']['code'])){
																if(empty($_package_text)){
																	$_package_text .= $room['package']['code'];
																} else{
																	$_package_text .= ' (' . $room['package']['code'] . ')';
																}
															}
															if(!empty($_package_text)){
																$content .= $this->cData("\t".'Pauschale: ' . $_package_text . "\n");
															}
														}
														if($roomcount == count($record['rooms'])){
															$content .= "------\n";
														}
														$roomcount++;
													}
												}
												$content .= "\n";
											$content .= '</Text>';
										$content .= '</Comment>';
									}
								$content .= '</Comments>';
							}
							
							$content .= '<BasicPropertyInfo/>'; //this is needed for OTA-2015A compatibility
							
						$content .= '</ResGlobalInfo>';
						
					$content .= '</HotelReservation>';
				}
				$content .= '</ReservationsList>';			
			}
		}else{
			$content .= '<Errors>';	
				$content .= '<Error Type="13" Code="448">';
					$content .= $this->cData('OTA_Read:GuestRequests - XML seems Invalid at '.date('Y-m-d H:i:s', time()));	
				$content .= '</Error>';	
			$content .= '</Errors>';	
			//log Error
			$this->writeLog("### ".date('Y-m-d H:i:s', time())." ###".PHP_EOL."\tERROR - OTA_Read:GuestRequests-XML:".PHP_EOL."\tXML: ".$_POST["request"].PHP_EOL."-------------------------".PHP_EOL);
		}
		
		$content .= '</OTA_ResRetrieveRS>';
		
		header('Content-type: application/xml');
		echo $content;
		exit;
    }
	
    public function v1_OTA_NotifReport_GuestRequests(){
    	$this->auth();
		$content .= '<OTA_ResRetrieveRS xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opentravel.org/OTA/2003/05" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_ResRetrieveRS.xsd" Version="7.000">' . "\n";
		
		$xml = @simplexml_load_string($_POST['request']);
		
		if(is_object($xml)){
			if(isset($xml->NotifDetails->HotelNotifReport->HotelReservations->HotelReservation) && is_object($xml->NotifDetails->HotelNotifReport->HotelReservations->HotelReservation)){
				$errors = false;
				$success = false;
				foreach($xml->NotifDetails->HotelNotifReport->HotelReservations->HotelReservation as $hotelReservation){
					if(isset($hotelReservation->UniqueID) && isset($hotelReservation->UniqueID->attributes()->ID)){
						$id = (string) $hotelReservation->UniqueID->attributes()->ID;
						
						
						$sql = "SELECT * FROM `emails` WHERE `id`='" . $id . "'";
						$check = $this->connection->execute($sql)->fetchAll('assoc');
						if(is_array($check) && count($check) > 0){
							$sql = "UPDATE `emails` SET `retrieved`='1' WHERE `id`='".$id."'";
						} else{
							$sql = "UPDATE `newsletter` SET `retrieved`='1' WHERE `id`='".$id."'";
						}
						if ($this->connection->execute($sql)) {
							//no errors
						} else{
							$errors = "The Server couldn't UPDATE already sent requests with id '".$id."' at ".date('Y-m-d H:i:s', time())."\n";	
						}
						$success = true; //just to confirm ASA, that we got their request
					} else{
						$errors = "The Server didn't get any IDs at ".date('Y-m-d H:i:s', time()) . "\n";
						//log Error
						$this->writeLog("### ".date('Y-m-d H:i:s', time())." ###".PHP_EOL."\tERROR - The Server didn\'t get any IDs:".PHP_EOL."\tXML: ".$_POST["request"].PHP_EOL."-------------------------".PHP_EOL);
					}	
				}
				$errors_string = 'Errors';
				$error_string = 'Error';
				$error_type = '13';
				$error_code = ' Code="448"';
				if($success){
					$content .= '<Success/>';	
					$errors_string = 'Warnings';
					$error_string = 'Warning';
					$error_type = '11';
					$error_code = '';
				} 
				if($errors !== false){
					$content .= '<'.$errors_string.'>';	
						$content .= '<'.$error_string.' Type="'.$error_type.'"'.$error_code.'>';
							$content .= $this->cData($errors);	
						$content .= '</'.$error_string.'>';	
					$content .= '</'.$errors_string.'>';	
				}
			}

		} else{
			$content .= '<Errors>';	
				$content .= '<Error Type="13" Code="448">';
					$content .= $this->cData('OTA_NotifReport:GuestRequests - XML seems Invalid at '.date('Y-m-d H:i:s', time()));	
				$content .= '</Error>';	
			$content .= '</Errors>';	
			//log Error
			$this->writeLog("### ".date('Y-m-d H:i:s', time())." ###".PHP_EOL."\tERROR - OTA_NotifReport:GuestRequests-XML:".PHP_EOL."\tXML: ".$_POST["request"].PHP_EOL."-------------------------".PHP_EOL);
		}
		
		$content .= '</OTA_ResRetrieveRS>';
		
		header('Content-type: application/xml');
		echo $content;
		exit;
    }
	
	
	private function auth(){
		if($_SERVER['REMOTE_ADDR'] != '83.175.88.51'){ //for easy debugging at our place
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
			    header('WWW-Authenticate: Basic realm="AlpineBits demo server"');
			    header('HTTP/1.0 401 Authorization Required)');
			    echo 'ERROR:no user/pass';
			    exit;
			}
			
			if ( ! ( isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == Configure::read('alpinebits.auth.user')) ||
			     ! ( isset($_SERVER['PHP_AUTH_PW'])   && $_SERVER['PHP_AUTH_PW']   == Configure::read('alpinebits.auth.pw'))
			   ) {
			    header('HTTP/1.0 401 Authorization Required');
			    echo 'ERROR:wrong user/pass';
			    exit;
			}
		}
	}
	
	private function apacheRequestHeaders() { 
		if (!function_exists('apache_request_headers')) { 
	        foreach($_SERVER as $key=>$value) { 
	            if (substr($key,0,5)=="HTTP_") { 
	            	$key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
	                $out[$key]=$value; 
	            }else{ 
	                $out[$key]=$value; 
	    		} 
	        } 
	        return $out; 
	    } else{
	    	return apache_request_headers();
	    }
	} 
	
	private function getAlpineBitsVersion(){
		$ver = "legacy";
		$headers = $this->apacheRequestHeaders();
		foreach ($headers as $key => $val) {
		    if (strtolower($key) == strtolower("X-AlpineBits-ClientProtocolVersion")) {
		        $ver = $val;
		    }
		}	
		return $ver;
	}
	private function getAlpineBitsClientID(){
		$id = '';
		
		$headers = $this->apacheRequestHeaders();
		foreach ($headers as $key => $val) {
		    if (strtolower($key) == strtolower("X-AlpineBits-ClientID")) {
		        $id = $val;
		    }
		}	
		return $id;
	}
	
	private function cData($text){
		return Configure::read('alpinebits.cdata.start').$text.Configure::read('alpinebits.cdata.end');
	}
	
	private function writeLog($text){
		$this->log("Alpinebits-Log: " . $text);
	}
	
	//this function might look unnecessary, but this way i could use the existing code better
	private function checkAndRefomat($record){
		//init
		$return = false;
		
		if(is_array($record) && array_key_exists('data', $record) && array_key_exists('type', $record) && (array_key_exists('request', $record) || $record['type'] == 'newsletter')){
			if($record['type'] != 'newsletter'){
				$record['data'] = json_decode($record['data'], true);
				$record['request'] = json_decode($record['request'], true);
			}
			if(is_array($record['data']) && ((isset($record['request']) && is_array($record['request'])) || $record['type'] == 'newsletter')){
				if(array_key_exists('id', $record) && ((isset($record['request']) && array_key_exists('language', $record['request']) && array_key_exists('arrival', $record['data']) && array_key_exists('departure', $record['data'])) || $record['type'] == 'newsletter')){
					if(($record['type'] == 'newsletter' && array_key_exists('created', $record)) || (array_key_exists('sent', $record))){
						$return['id'] = $record['id'];
						$return['date-from'] = isset($record['data']['arrival']) ? strtotime($record['data']['arrival']) : '';
						$return['date-to'] = isset($record['data']['departure']) ? strtotime($record['data']['departure']) : '';
						$return['country'] = isset($record['data']['country']) ? $record['data']['country'] : '';
						if(isset($record['sent'])){
							$return['created'] = $record['sent'];
						} else if(isset($record['created'])){
							$return['created'] = $record['created'];
						} else{
							$return['created'] = '';
						}
						$return['type'] = $record['type'];
						$return['language'] = isset($record['request']['language']) ? $record['request']['language'] : '';
						$return['salutation'] = isset($record['data']['salutation']) ? $record['data']['salutation'] : '';
						$return['birthday'] = isset($record['data']['birthday']) ? strtotime($record['data']['birthday']) : '';
						$return['firstname'] = isset($record['data']['firstname']) ? $record['data']['firstname'] : '';
						$return['lastname'] = isset($record['data']['lastname']) ? $record['data']['lastname'] : '';
						$return['title'] = isset($record['data']['title']) ? $record['data']['title'] : '';
						$return['phone'] = isset($record['data']['phone']) ? $record['data']['phone'] : '';
						if(isset($record['data']['email'])){
							$return['email'] = $record['data']['email'];
						} else if(isset($record['email'])){
							$return['email'] = $record['email'];
						} else{
							$return['email'] = '';
						}
						if($record['type'] == 'newsletter'){
							$return['newsletter'] = isset($record['status']) && $record['status'] == 'subscribed' ? 1 : 0;
						} else{
							$record['newsletter'] = false;
						}
						$return['zip'] = isset($record['data']['zip']) ? $record['data']['zip'] : '';
						$return['city'] = isset($record['data']['city']) ? $record['data']['city'] : '';
						$return['street'] = isset($record['data']['address']) ? $record['data']['address'] : '';
						$return['note'] = isset($record['data']['message']) ? $record['data']['message'] : '';
						$return['adults'] = isset($record['data']['adults']) ? $record['data']['adults'] : '';
						$return['children'] = isset($record['data']['children']) ? $record['data']['children'] : '';
						$return['rooms'] = [];
						if(isset($record['data']['rooms']) && is_array($record['data']['rooms']) && count($record['data']['rooms']) > 0){
							foreach($record['data']['rooms'] as $_room){
								if(is_array($_room) && array_key_exists('room', $_room) && !empty($_room['room'])){
									//get room code
									$room_code = false;
									$sql = "SELECT * FROM `elements` WHERE `id`='" . $_room['room'] . "' AND `code`='room'";
									$room = $this->connection->execute($sql)->fetch('assoc');
									if(is_array($room) && array_key_exists('fields', $room)){
										$room['fields'] = json_decode($room['fields'], true);
										if(is_array($room['fields']) && array_key_exists('hs_code', $room['fields']) && !empty($room['fields']['hs_code'])){
											$room_code = $room['fields']['hs_code'];
										}
									}
									if($room_code !== false){
										$room_key = count($return['rooms']);
										$return['rooms'][$room_key]['code'] = $room_code;
										//get package
										$return['rooms'][$room_key]['package'] = array('code' => false,'title' => false);
										if(array_key_exists('package', $_room) && !empty($_room['package'])){
											$sql = "SELECT * FROM `elements` WHERE `id`='" . $_room['package'] . "' AND `code`='package'";
											$package = $this->connection->execute($sql)->fetch('assoc');
											if(is_array($package) && array_key_exists('fields', $package)){
												$package['fields'] = json_decode($package['fields'], true);
												if(is_array($package['fields']) && array_key_exists('hs_code', $package['fields']) && !empty($package['fields']['hs_code'])){
													$return['rooms'][$room_key]['package']['code'] = $package['fields']['hs_code'];
												}
												if(is_array($package) && array_key_exists('internal', $package) && !empty($package['internal'])){
													$return['rooms'][$room_key]['package']['title'] = $package['internal'];
												}
											}
										}
										//get adults
										if(array_key_exists('adults', $_room) && !empty($_room['adults'])){
											$return['rooms'][$room_key]['adults'] = $_room['adults'];
										}
										//get children
										if(array_key_exists('children', $_room) && !empty($_room['children']) && array_key_exists('ages', $_room) && is_array($_room['ages']) && count($_room['ages']) == $_room['children']){
											$return['rooms'][$room_key]['children'] = [];
											foreach($_room['ages'] as $_age){
												if(is_array($_age) && array_key_exists('age', $_age)){
													if(!array_key_exists($_age['age'], $return['rooms'][$room_key]['children'])){
														$return['rooms'][$room_key]['children'][$_age['age']] = 1;
													} else{
														$return['rooms'][$room_key]['children'][$_age['age']]++;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		return $return;
	}
}

