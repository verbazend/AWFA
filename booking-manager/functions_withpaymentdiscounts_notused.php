<?php
	function axcelerate_authentication_headers() {
		
	  $_WSTOKEN = '75FD969E-89F4-497D-9EA18299CE0361C9';
	  $_APITOKEN = '7C511127-809C-40D7-A9847FD02DFAE93D';
	  $headers = array( 'wstoken' => $_WSTOKEN, 'apitoken' => $_APITOKEN );
	
	  return $headers;
	
	}
	
	function axcelerate_get_url() { 
	  return 'https://admin.axcelerate.com.au';
	}
	
	function golog($inp){
		echo "".$inp."\n";
	}
	
	function checkIfCourseClosed($instanceID){
		$instanceID = db::esc($instanceID);
		$courseinfo = db::runQuery("select count(*) as total from eventsClosed where instanceID = '$instanceID'");
		
		if($courseinfo[0]['total']){
			//Course is Closed
			return true;
		} else {
			//Course is Open
			return false;
		}
	}
	
	function courseCustomPriceLookup($discountType,$instanceID,$defaultPrice,$campaignCode){
		
		$instance = getEventDetails($instanceID);
		if($instance){
			
			$courseID = $instance['CourseCID'];
			$locationID = $instance['locationID'];
			$campaignDetails = getCampaignDetails($campaignCode);
			
			if($campaignDetails){
				$campaignID = $campaignDetails['ID'];
			} else {
				return $defaultPrice;
			}
			
			$campaignID = db::esc($campaignID);
			$locationID = db::esc($locationID);
			$courseID = db::esc($courseID);
			
			if($discountType==2){
				
				$ret = db::runQuery("select * from coupon_locationRestriction where locationID = '$locationID' and couponID = '$campaignID'");
				if($ret){
					if($ret[0]['priceOverride']==1){
						return $ret[0]['newPrice'];
					} else {
						return $defaultPrice;
					}
				} else {
					return $defaultPrice;
				}
				
			} else if($discountType==3){
				$ret = db::runQuery("select * from coupon_courserestriction where courseID = '$courseID'  and couponID = '$campaignID'");
				if($ret){
					
					if($ret[0]['priceOverride']==1){
						return $ret[0]['newPrice'];
					} else {
						return $defaultPrice;
					}
				} else {
					return $defaultPrice;
				}
				
			} else {
				return $defaultPrice;
			}
			
		} else {
			return $defaultPrice;
		}
		
	}
	
	function getEmailTemplate($templateName){
		
		$templatefile = file_get_contents($templateName);
		
		return $templatefile;
	}
	
	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
	
	function varReplace($lookup,$value,$resource){
		
		$lookup = "{".$lookup."}";
		
		return str_replace($lookup,$value,$resource);
		
	}
	
	function varReplaceBR($lookup,$value,$resource){
		
		$lookup = "{".$lookup."}";
		
		if($value!=""){
			$value = $value."<br>";
		}
		
		return str_replace($lookup,$value,$resource);
		
	}
	
	function varReplaceSP($lookup,$value,$resource){
		
		$lookup = "{".$lookup."}";
		
		if($value!=""){
			$value = $value." ";
		}
		
		return str_replace($lookup,$value,$resource);
		
	}
	
	function varReplaceDF($lookup,$value,$resource, $defaultValue){
		
		$lookup = "{".$lookup."}";
		
		if($value==""){
			$value = $defaultValue;
		}
		
		return str_replace($lookup,$value,$resource);
		
	}
	
	function getStatusIDtoStatusName($statusID){
		
		switch($statusID){
			
			case '0':
				$res = "Queued";
				break;
			case '1':
				$res = "Creating Contacts";
				break;
			case '2':
				$res = "Contacts Created";
				break;
			case '3':
				$res = "Creating Enrolment";
				break;
			case '4':
				$res = "Enrolment Created";
				break;
			case '5':
				$res = "Processing Payment";
				break;
			case '6':
				$res = "Payment Complete";
				break;
			case '7':
				$res = "Finalising Order";
				break;
			case '8':
				$res = "Order Complete";
				break;
			case '99':
				$res = "Error";
				break;
			default:
				$res = "Unknown";
							
		}
		
		return $res;
	}
	
	function goAutomaticProcessing(){
		
		/*
		0/98 = Details saved, no processing started
		1 = started processing contacts
		2 = contact creation completed
		3 = Started processing enrolments
		4 = Enroments Completed
		5 = started processing payment
		6 = payment is completed
		7 = finalising order
		8 = order completed and paid
		99 = order error
		*/ 
		
		//Moved new transactions from Queue to Processing
		loadNewtransactionstoQueue();
		
		checkEnrolmentsComplete();
		
		//create contacts for any trainess awaiting processing
		goCreateContacts();
		
		//process enrolments for all contacts created
		goEnrolments();
		
		//Process payment for the order
		goProcessPayment();
		
		
		//Payment finished, send confirmation emails and invoices etc.
		finalisePayments();
		
		//
		finaliseOrderChecks();
		
		
	}
	
	function finaliseOrderChecks(){
		
		$progroup = db::runQuery("select * from process_group where process_status = '7'");
		
		if($progroup){
			foreach($progroup as $payment){
				
				$groupUnID = $payment['ID'];
				
				db::insertQuery("update process_group set process_status = '8' where ID = '$groupUnID'");
			}
		}
	}
	
	function finalisePayments(){
		
		$progroup = db::runQuery("select * from process_group where process_status = '6'");
		
		if($progroup){
			foreach($progroup as $payment){
				
				$groupUnID = $payment['ID'];
				
				db::insertQuery("update process_group set process_status = '7' where ID = '$groupUnID'");
			}
		}
	}
	
	function goProcessPayment(){
		
		$progroup = db::runQuery("select * from process_group where process_status = '4'");
		
		if($progroup){
			foreach($progroup as $payment){
				
				$groupID = db::esc($payment['ID']);
				$ListgroupID = db::esc($payment['processgroupID']);
				
				$dontInvoiceUser = db::esc($payment['dontInvoiceUser']);
				$addressToInvoice = db::esc($payment['addressToInvoice']);
				
				db::insertQuery("update process_group set process_status = '5' where ID = '$groupID'");
				
				if($payment['payment_type']=="offline"){
					
					db::insertQuery("update process_group set process_status = '6', payment_status = 'complete' where ID = '$groupID'");
				
				} else if($payment['payment_type']=="payment-employer"){
					
					db::insertQuery("update process_group set process_status = '6', payment_status = 'employer' where ID = '$groupID'");
						
				} else {
					
					
					db::insertQuery("update process_group set payment_status = 'preparing' where ID = '$groupID'");
					
					
					$courseCostTotal = $payment['totalcost'];
					
					$courseInstanceID = $payment['eventID'];
					
					$addressToInvoice = $payment['addressToInvoice'];
					
					$cc_num = $payment['cc_num'];
					$cc_month = $payment['cc_month'];
					$cc_year = $payment['cc_year'];
					
					$primContactID = $payment['primaryContactID'];
					
					$groupInvoiceID = $payment['GroupInvoiceID'];
					
					$amount = number_format($courseCostTotal ,2);
					//var_dump($courseCostTotal." -- ".$amount);
	                
				    $data   = array(
							    'txnType' => '0',
							    'txnSource' => 23,
							    'amount' => (int)($amount * 100),
							    'currency' => 'AUD',
							    'purchaseOrderNo' => time(),
							    'CreditCardInfo' => array(
							      'cardNumber' => $cc_num,
							      'expiryDate' => $cc_month.'/'.$cc_year,
							    ),
							  );
					
					
					$order_id = time();
					dlog("Start NAB Trans "); 
					$trans    = uc_nab_transact_charge_Test($order_id, $amount, $data);
					dlog("Finish NAB Trans "); 
					$txnID    = $trans['data']['TxnID'][0];
					ob_start();
					var_dump($trans);
					$NABTrans_dump = ob_get_clean();
					//var_dump($trans);
			
			        if($trans['success'] == '1' || true == true){
					    $transVars = array(
				                           'amount' => $courseCostTotal,
				                           'ContactID' => $primContactID,
				                           'invoiceID' => $groupInvoiceID,
				                           'reference' => $txnID,
				                           'description' => "TXN: ".$txnID
				                     );
								 
				    	$extrans =  axcelerate_transact($transVars);
						
						ob_start();
						var_dump($extrans);
						echo("---------");
						var_dump($enrollNow);
						$extrans_dump = ob_get_clean();
						
						$enrolmentReturn = array(
												'success' => true,
												'error_message' => '',
												'txnid' => "".$txnID."",
												'invoice' => $groupInvoiceID,
						                   );
										   
						$dontInvoiceUser = true;
						if($dontInvoiceUser){
							
							
							$trainees = db::runquery("select * from process_trainee where processgroupID = '$ListgroupID'");
							
							if($trainees){
								foreach($trainees as $trainee){
									
									
									$learnerID = $trainee['leanerID'];
									$contactID = $trainee['contactID'];
									$invoiceID = $trainee['invoiceID'];
									$learnerID = $trainee[''];
									
									$bookingID = $enrollNow->LEARNERID."-".$contactID;
								
								
									$enrolVar = array(
												'fname' => $trainee['firstname'],
												'lname' => $trainee['lastname'],
												'email' => $trainee['email'],
												'address' => $trainee['address'],
												'suburb' => $trainee['suburb'],
												'state' => getStateFromPostcode($trainee['postcode']),
												'postcode' => $trainee['postcode'],
												'mobile' => $trainee['mobile'],
												'workplace' => $trainee['workplace'],
												);
											
									email::sendBookingConfirmation($bookingID,$courseInstanceID,$enrolVar,$addressToInvoice);
									
								}
							} else {
								
							}
						}
						
						db::insertQuery("update process_group set process_status = '6', payment_status = 'completed' where ID = '$groupID'");
						
					} else {
						$errorMessage = db::esc($trans['message']);
						db::insertQuery("update process_group set process_status = '99', errorMessage = 'Payment Failed: $errorMessage', payment_status = 'failed - $NABTrans_dump' where ID = '$groupID'");
					}
					
				}
				
				
				
				//Clear credit card details out
				db::insertQuery("update process_group set cc_num = '#### #### #### ####', cc_ccv = '###', cc_month = '##', cc_year = '##' where ID = '$groupID'");
				
			}
		}
		
	}
	
	function goEnrolments(){
		
		$progroup = db::runQuery("select * from process_group where process_status = '2'");
		if($progroup){
				foreach($progroup as $pgr){
					
					$supressInvoiceEmail = "0";
					$generateInvoice = "true";
					$archiveINvoice = "true";
					
					
					
					if($pgr['campaign_code']==""){
						$campaignCode = false;
						$campaignDetails = false;
						$campaignID = 0;
						$campaignName = false;
						
						$allowOfflinePayments = false;
						$forceOfflinePayments = false;
						$dontInvoiceUser = false;
						$addressToInvoice = false;
						$contactIDtoInvoice = false;
					} else {
						$campaignCode = $pgr['campaign_code'];
						$campaignDetails = getCampaignDetails($campaignCode);
						$campaignID = $campaignDetails['ID'];
						$campaignName = $campaignDetails['campaignName'];
						
						$allowOfflinePayments = $campaignDetails['allowOfflinePayments'];
						$forceOfflinePayments = $campaignDetails['forceOfflinePayments'];
						$dontInvoiceUser = $campaignDetails['dontInvoiceUser'];
						$addressToInvoice = $campaignDetails['addressToInvoice'];
						$contactIDtoInvoice = $campaignDetails['contactIDtoInvoice'];
						
						if($forceOfflinePayments){
							$forceOfflinePayments = true;
						} else {
							$forceOfflinePayments = false;
						}
						
						if($contactIDtoInvoice==""){
							$contactIDtoInvoice = false;
						}
						
					}
					
					
					
					$courseID                  = $pgr['eventID'];
					$courseDetails             = getEventDetails($courseID);
					//var_dump($courseDetails);
					if($campaignDetails){
						
						//Course Details
						$courseCostTotal       = intval($courseDetails['cost']);
						
						$totalAttend = $pgr['total_trainee'];
						$courseCostTotal = $courseCostTotal * $totalAttend;
						
						if($campaignDetails['discountType']==1){
								//Percent Discount
							$courseCostTotal = $courseCostTotal-($courseCostTotal*("0.".$campaignDetails['discountAmount']));
							$manualInvoiceEmailPrice = $courseCostTotal;
							$customPriceOption = false;
							$sendManualInvoice = false;
							
						} else if($campaignDetails['discountType']==2){
								//Custom Price Override - Location
								$customPriceOption = 2;
								$courseCostTotal = courseCustomPriceLookup($customPriceOption,$courseID,$courseCostTotal,$campaignCode);
								$manualInvoiceEmailPrice = $courseCostTotal;
								$dontInvoiceUser = true;
								$sendManualInvoice = true;
								
						} else if($campaignDetails['discountType']==3){	
								//Custom Price Override - Course
								$customPriceOption = 3;
								$courseCostTotal = courseCustomPriceLookup($customPriceOption,$courseID,$courseCostTotal,$campaignCode);
								//return errorBackResponse($courseCostTotal." - ".$courseID);
								$manualInvoiceEmailPrice = $courseCostTotal;
								$dontInvoiceUser = true;
								$sendManualInvoice = true;
								
						} else {
							//Dollor Amount Discount
							$courseCostTotal = $courseCostTotal-$campaignDetails['discountAmount'];
							$manualInvoiceEmailPrice = $courseCostTotal;
							$customPriceOption = false;
							$sendManualInvoice = false;
						}
						
						//return errorBackResponse("Disc Type: ".$campaignDetails['discountType']);
						
					} else {
						//Course Details
						$courseCostTotal           = intval($courseDetails['cost']);
						
						$totalAttend = $pgr['total_trainee'];
						$courseCostTotal = $courseCostTotal * $totalAttend;
						
						$manualInvoiceEmailPrice = $courseCostTotal;
					}
					
					
					
					
					
					$processgroup = db::esc($pgr['processgroupID']);
					
					$contactIDtoInvoice = db::esc($contactIDtoInvoice);
					$allowOfflinePayments = db::esc($allowOfflinePayments);
					$forceOfflinePayments = db::esc($forceOfflinePayments);
					$dontInvoiceUser = db::esc($dontInvoiceUser);
					$addressToInvoice = db::esc($addressToInvoice);
					
					db::insertQuery("update process_group set process_status = '3', primaryContactID = '$contactIDtoInvoice', allowOfflinePayments = '$allowOfflinePayments', forceOfflinePayments = '$forceOfflinePayments', dontInvoiceUser = '$dontInvoiceUser', addressToInvoice = '$addressToInvoice', contactIDtoInvoice = '$contactIDtoInvoice' where processgroupID = '$processgroup'");
					
					 //Create Multi Enrolment
						 
						 $trainees = db::runQuery("select * from process_trainee where processgroupID = '$processgroup'");
						 $primaryContact = "";
						 $courseInstanceID = "";
						 $processgroup = "";
						 $costperperson = $courseDetails['cost'];
						 if($trainees){
						 		$contactList = "";
						 		foreach($trainees as $trn){
						 			
						 				if($contactList==""){
						 						$contactList = $contactList.$trn['contactID'];
						 						$primaryContact = $trn['contactID'];
						 						$courseInstanceID = $trn['eventID'];
						 						$processgroup = $trn['processgroupID'];
												$processgroup = $trn['processgroupID'];
												$costperperson = $trn['cost'];
						 				} else {
						 					$contactList = $contactList.",".$trn['contactID'];
						 				}
						 			
						 		}	
						 		
								if($contactIDtoInvoice!=false){
									$primaryContact = $contactIDtoInvoice;
								}
								
						 		//contact IDS in list   55555,111211,515456
						 $supressInvoiceEmail = true;
						 //$generateInvoice = true;
						 $archiveINvoice = false;
						 
						 //$contactInvoiceID = '236951';
						 
							  $enrollVars  = array(
								      					'contactID'   => $contactList,
								      					'instanceID'  => $courseInstanceID,
								      					'invoiceID'   => $contactInvoiceID,
								      					'payerID'			=> $primaryContact,
								      					'type'        => 'w',
								      					//'generateInvoice'=>$generateInvoice,
								      					//'suppressEmail'=> $supressInvoiceEmail,
								      					//'archiveInvoice'=>$archiveINvoice,
								      					
								          		 );
								          		 
								 $enrollNow =  axcelerate_multienroll($enrollVars,$processgroup);
								 
								 
								 $contactListarr = explode(",",$contactList);
								 $totalCost = 0;
								 
								 foreach($contactListarr as $contact){
									 $enrollVarsupdate  = array(
									      					'contactID'   => $contact,
									      					'instanceID'  => $courseInstanceID,
									      					'payerID'			=> $primaryContact,
									      					'type'        => 'w',
									      					'cost' => $costperperson,
									      					
									      					
									          		 );
									 $enrollNowupdate =  axcelerate_multienrollupdate($enrollVarsupdate,$processgroup);
									 
									 var_dump($enrollNowupdate);
									 
									 $totalCost = $totalCost + $costperperson;
								 }

								 if(isset($enrollNow->error)){
											$enrollIsError = true;
											$trans['message'] = $enrollNow->MESSAGES;
											dlog("Enrol-Enrolment: Axcelerate returned error: $processgroup - ".$trans['message']);
											
											markGroupError($processgroup,"");
											die();
								 }
								
								$groupInvoiceID = "";
								$totalCost = 0;
								foreach($enrollNow as $student){
										$InvoiceID = db::esc($student->INVOICEID);
										$ContactID = db::esc($student->CONTACTID);
										$LearnerID = db::esc($student->LEARNERID);
										$Amount    = db::esc($student->AMOUNT);
										
										$totalCost = $totalCost + $student->AMOUNT;
										
										$groupInvoiceID = $InvoiceID;
										
										db::insertQuery("update process_trainee set learnerID = '$LearnerID', invoiceID = '$InvoiceID' where contactID = '$ContactID' and processgroupID = '$processgroup'");
								}
								
								
								
								if($forceOfflinePayments==true){
									db::insertQuery("update process_group set GroupInvoiceID = '$groupInvoiceID', process_status = '4', totalcost = '$courseCostTotal', payment_type = 'offline', primaryContactID = '$primaryContact' where processgroupID = '$processgroup'");
								} else {
									db::insertQuery("update process_group set GroupInvoiceID = '$groupInvoiceID', process_status = '4', totalcost = '$courseCostTotal', primaryContactID = '$primaryContact' where processgroupID = '$processgroup'");	
								}
								
								
						 		
						 }
						 
					
					
					
				}
		}
		
		
	}
	
	function loadNewtransactionstoQueue(){
		// get list of items to start processing contact creation
		$progroup = db::runQuery("select * from process_group where process_status = '98'");
		if($progroup){
				foreach($progroup as $pgrp){
						$id = db::esc($pgrp['ID']);
						$processgroup = $pgrp['processgroupID'];
						
						db::insertQuery("update process_group set process_status = '1' where ID = '$id'");
						
				}
		}
	}
	
	function checkEnrolmentsComplete(){
		$progroup = db::runQuery("select * from process_group where process_status = '0'");
		if($progroup){
				foreach($progroup as $pgrp){
						$id = db::esc($pgrp['ID']);
						$processgroup = db::esc($pgrp['processgroupID']);
						
						$subgr = db::runQuery("select count(*) as total from process_trainee where processgroupID = '$processgroup' and processed = '0'");
						if($subgr[0]['total']==0){
							db::insertQuery("update process_group set process_status = '2' where ID = '$id'");
							
						}
						
						
				}
		}
		return true;
	}
	
	function goCreateContacts(){
		
		$trainees = db::runQuery("select * from process_trainee where processed = '0'");
		if($trainees){
			foreach($trainees as $trains){
				
				$traineeBatchID = db::esc($trains['ID']);
				
				//check to make sure this item has been procesed in another thread.
				$dupcheck = db::runQuery("select * from process_trainee where processed = '0' and ID = '$traineeBatchID'");
				if(!$dupcheck){
					//do nothing for now, later date we will add some extra processing steps here when volumes increas heavily.
				} else {
					$contactVars = array(
			                      'givenName'    => $trains['firstname'],
			                      'surname'      => $trains['lastname'],
			                      'title'        => '',
			                      'emailAddress' => $trains['email'],
			                      'mobilephone'  => $trains['mobile'],
			                      'organisation' => $trains['workplace'],
			                      'address1'     => $trains['address'],
			                      'city'         => $trains['suburb'],
			                      'postcode'     => $trains['postcode'],
			                      'USI'		       => $trains['usi'],
			                      
			                      'customField_additionalinfo' => $trains['additionalinfo'],
			                      'customField_processingGroupID' => $trains['processgroupID']
			                      
			                      
			                   );
			                   
			                   
	        $enroll      = axcelerate_save_contact($contactVars);
	        $contactID   = db::esc($enroll->CONTACTID);
					
					db::insertQuery("update process_trainee set processed = '1', contactID = '$contactID' where ID = '$traineeBatchID'");
					
					checkEnrolmentsComplete();
				}
			}
		}
		
		return true;
	}
	
	
	function markGroupError($processgroup,$errormessage = false){
			
			$processgroup = db::esc($processgroup);
			$errormessage = db::esc($errormessage);
			if($errormessage){
				db::insertQuery("update process_group set process_status = '99' and errorMessage = '$errormessage' where processgroupID = '$processgroup'");
			} else {
				db::insertQuery("update process_group set process_status = '99' and errorMessage = 'An Error has occurred. Please contact us on 1300 336 613' where processgroupID = '$processgroup'");
			}
			
			dlog($processgroup."-".$errormessage, "autoprocessing");
			
			return true;
	}
	
	function getStateFromPostcode($postcode){
		$postcode = db::esc($postcode);
		$res = db::runQuery("select * from postcode_db where postcode = '$postcode'",0,1);
		if($res){
			$res = $res[0];
			return $res['state'];
		} else {
			return "";
		}
	}
	
	function dlog($message, $vtype = false){
		//return true;
		if(!$vtype=="sync"){
			//return true;
		}
		$message = db::esc($message);
		
		if(!$vtype){
			$vtype = "";
		} else {
			$vtype = db::esc($vtype);
		}
		
		
		//Session Data
		ob_start();
		var_dump($_SESSION);
		$sessionData = ob_get_clean();
		$sessionData = db::esc($sessionData);
		
		//Post Data
		$pdata = $_POST;
		if(isset($pdata['cc'])){
			$pdata['cc'] = "NO RECORD";
		}
		if(isset($pdata['expiryM'])){
			$pdata['expiryM'] = "NO RECORD";
		}
		if(isset($pdata['expiryY'])){
			$pdata['expiryY'] = "NO RECORD";
		}
		if(isset($pdata['cvv'])){
			$pdata['cvv'] = "NO RECORD";
		}
		
		ob_start();
		var_dump($pdata);
		$postData = ob_get_clean();
		$postData = db::esc($postData);
		
		//Get Data
		ob_start();
		var_dump($_GET);
		$getData = ob_get_clean();
		$getData = db::esc($getData);
		
		$sessionID = session_id();
		$URL = $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
		
		$ins = db::insertQuery("insert into log (type, message, sessionData, postData, getData, sessionID, URL) values('$vtype','$message','$sessionData','$postData','$getData','$sessionID','$URL')");
		
		return $ins;
	}
	
	function postAXData($url,$postData){
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'wstoken: 75FD969E-89F4-497D-9EA18299CE0361C9',
	    'apitoken: 7C511127-809C-40D7-A9847FD02DFAE93D'
	    ));
	    $output = curl_exec($ch);       
	    curl_close($ch);
		//var_dump($output);
	    return json_decode($output);
    }
	function putAXData($url,$postData){
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'wstoken: 75FD969E-89F4-497D-9EA18299CE0361C9',
	    'apitoken: 7C511127-809C-40D7-A9847FD02DFAE93D'
	    ));
	    $output = curl_exec($ch);       
	    curl_close($ch);
		//var_dump($output);
	    return json_decode($output);
    }
	function getAXData($url){
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_POST, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'wstoken: 75FD969E-89F4-497D-9EA18299CE0361C9',
	    'apitoken: 7C511127-809C-40D7-A9847FD02DFAE93D'
	    ));
	    $output = curl_exec($ch);       
	    curl_close($ch);
		//var_dump($output);
	    return json_decode($output);
    }
	
	function getLocationIDfromDataName($locationDataName){
		if(isset($_SESSION["".$locationDataName.""])){
			golog("Cached location ".$locationDataName);
			return $_SESSION["".$locationDataName.""];
		} else {
			golog("Query location ".$locationDataName);
			$locationDataName = db::esc($locationDataName);
			$location = db::runQuery("select * from locations where data_location = '$locationDataName'");
			if($location){
				$_SESSION["".$locationDataName.""] = $location[0]['ID'];
				return $location[0]['ID'];
			} else {
				db::insertQuery("insert into locations (LocationName, data_location) values('$locationDataName','$locationDataName')");
				$locationID = db::runQuery("SELECT LAST_INSERT_ID() as ID");
				$_SESSION["".$locationDataName.""] = $locationID[0]['ID'];
				return $locationID[0]['ID'];
			}
		}
	}

	function getCampaignDetails($campaigncode){
		$campaigncode = db::esc($campaigncode);
		$campaign = db::runQuery("select * from coupons where couponCode = '$campaigncode'");
		if($campaign){
			return $campaign[0];
		} else {
			return false;
		}
	}
	
	function getEventDetails($courseID){
		$courseID = db::esc($courseID);
		$campaign = db::runQuery("select *, events.courseID as CourseCID from events left join courses on courses.courseID = events.courseID left join locations on events.locationID = locations.ID where events.instanceID = '$courseID'");
		if($campaign){
			return $campaign[0];
		} else {
			return false;
		}
	}
	

	
	
	function errorBackResponse($errorMessage){
		$enrolmentReturn = array(
								'success' => false,
								'error_message' => $errorMessage,
		                   );
	    echo json_encode($enrolmentReturn);
		die();
		return false;
	}
	
	function getNABMessageID(){
		
		$messageID = db::runQuery("select * from NAB_messageID");
		return $messageID[0];
	}
	
	function setNABMessageID($messageID){
		$messageID = db::esc($messageID);
		db::insertQuery("update NAB_messageID set ID = '$messageID'");
		return true;
	}
	
	function uc_nab_transact_charge( $order_id, $amount, $data ) {
	  global $user;
	
	
	  // Get the next message ID.
	   $message_id = getNABMessageID();
	   $message_id = $message_id['ID'];
	   $message_id = $message_id + 1;
  	   setNABMessageID( $message_id );
	
	  // Build the post XML from the data array.
	  $post_data = uc_nab_transact_xml( 'Payment', $data, $message_id );
	  //print $post_data;
	  // Build the URL where we'll send the request.
	  //$url = 'https://transact.nab.com.au/'. variable_get('uc_nab_xml_mode', 'test') .'/xmlapi/payment';
	  if($GLOBALS['nabSandbox']){
	  	$url = "https://transact.nab.com.au/test/xmlapi/payment";
	  } else {
	  	$url = "https://transact.nab.com.au/xmlapi/payment";
	  }
	  
	  dlog("NAB Using:  ".$url); 
	
	  // Get the response of our payment request.
	  //if ( extension_loaded( 'opensslx' ) ) {
	  //  $response = drupal_http_request( $url, array( 'Content-Type' => 'text/xml' ), 'POST', $post_data );
	  //  $response = $response->data;
	 // }
	  // if openssl extension is not loaded we use CURL
	  //elseif ( extension_loaded( 'curl' ) ) {
	    $ch = curl_init( $url );
	
	    curl_setopt( $ch, CURLOPT_POST, 1 );
	    curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 180); //timeout in seconds
	
	
			dlog("NAB Sending Request");
			
			try {
	    		$response = curl_exec( $ch );
		  } catch (Exception $e) {
		  		dlog("NAB - ##EXCEPTION## - ".$e->getMessage());
		  		
		  		$result = array(
			      'success' => FALSE,
			      'comment' => t( 'Credit card payment failed: @text', array( '@text' => 'Bank Returned Error at this time' ) ),
			      'message' => t( 'Credit card payment failed: @text', array( '@text' => 'Bank Returned Error at this time' ) ),
			      'uid' => $user->uid,
			    );
			    
			    return $result;
		  }
	    
	    dlog("Request Completed");
	    curl_close( $ch );
	  //}
	
	  // Include the XML parser for PHP 4 compatibility.
	  //module_load_include('php', 'uc_store', 'includes/simplexml');
	
		dlog("NAB Creating XML OBject");
		dlog("NAB Response: ".$response);
	  // Create the XML object and parse the response string.
	  $xml = simplexml_load_string( $response );
	  
	  dlog("NAB Response XML: ".$xml);
	  //print_r($xml);
	  // Check to make sure the response parses and payment passed properly.
	  if ( isset( $xml->Status->statusCode ) && $xml->Status->statusCode != '000' ) {
	    $approval = 'No';
	    $responsecode = $xml->Status->statusCode;
	    $responsetext = $xml->Status->statusDescription;
	    dlog("NAB - NO APPROVAL");
	  }
	  elseif ( isset( $xml->Payment->TxnList->Txn->approved ) ) {
	    $approval = $xml->Payment->TxnList->Txn->approved;
	    $responsecode = $xml->Payment->TxnList->Txn->responseCode;
	    $responsetext = $xml->Payment->TxnList->Txn->responseText;
	    $charged = $xml->Payment->TxnList->Txn->amount / 100;
	    $txnid = $xml->Payment->TxnList->Txn->txnID;
	    dlog("NAB - CHECKING");
	  }
	  else {
	    // Otherwise supply some default values.
	    $approval = 'No';
	    $responsecode = 'x';
	
	    // If $response is set, we made a request, error was in the response
	    if ( isset( $response ) ) {
	      $responsetext = t( 'Failed to parse the XML API request or response.' );
	      // Log the trouble string to the watchdog.
	      watchdog( 'uc_nab_transact', 'Failed XML parse response:<br/>@xml', array( '@xml' => $response ), WATCHDOG_ERROR );
	    }
	    else {
	      $responsetext = t( 'Failed to make the request.' );
	      // Log the trouble string to the watchdog.
	      watchdog( 'uc_nab_transact', 'Failed to make the request: "openssl" or "curl" PHP extensions are needed.', array(), WATCHDOG_ERROR );
	    }
	    
	    dlog("NAB - Failed: ".$responsetext);
	  }
	
	  if ( $approval != 'Yes' ) {
	    $message = t( 'Credit card declined: !amount', array( '!amount' => $amount ) );
			
			dlog("NAB - Declined: ".$message);
			
	    $result = array(
	      'success' => FALSE,
	      'comment' => t( 'Credit card payment declined: @text', array( '@text' => $responsetext ) ),
	      'message' => t( 'Credit card payment declined: @text', array( '@text' => $responsetext ) ),
	      'uid' => $user->uid,
	    );
	  }
	  else {
	  	
	    $message = t( 'Credit card charged: !amount', array( '!amount' => $charged ) )
	      .'<br />'. t( 'NAB Transact Txn ID: @txnid', array( '@txnid' => $txnid ) );
			
			dlog("NAB - Approved: ".$message);
			
	    $result = array(
	      'success' => TRUE,
	      'comment' => t( 'NAB Transact Txn ID: @txnid<br/>Approval code: @code', array( '@txnid' => $txnid, '@code' => $responsecode ) ),
	      'message' => t( 'NAB Transact Txn ID: @txnid<br/>Approval code: @code', array( '@txnid' => $txnid, '@code' => $responsecode ) ),
	      'data' => array( 'TxnID' => $txnid ),
	      'uid' => $user->uid,
	    );
	  }
	
	  $message .= '<br />'. t( 'Response code: @code - @text', array( '@code' => $responsecode, '@text' => $responsetext ) );
	
		dlog("NAB - Result: ".$message);
	  return $result;
	}

function uc_nab_transact_charge_test( $order_id, $amount, $data ) {
	  global $user;
	
	
	  // Get the next message ID.
	   $message_id = getNABMessageID();
	   $message_id = $message_id['ID'];
	   $message_id = $message_id + 1;
  	   setNABMessageID( $message_id );
	
	  // Build the post XML from the data array.
	  $post_data = uc_nab_transact_xml_test( 'Payment', $data, $message_id );
	  //print $post_data;
	  // Build the URL where we'll send the request.
	  //$url = 'https://transact.nab.com.au/'. variable_get('uc_nab_xml_mode', 'test') .'/xmlapi/payment';

	  	$url = "https://transact.nab.com.au/test/xmlapi/payment";

	  
	  dlog("NAB Using:  ".$url); 
	
	  // Get the response of our payment request.
	  //if ( extension_loaded( 'opensslx' ) ) {
	  //  $response = drupal_http_request( $url, array( 'Content-Type' => 'text/xml' ), 'POST', $post_data );
	  //  $response = $response->data;
	 // }
	  // if openssl extension is not loaded we use CURL
	  //elseif ( extension_loaded( 'curl' ) ) {
	    $ch = curl_init( $url );
	
	    curl_setopt( $ch, CURLOPT_POST, 1 );
	    curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 180); //timeout in seconds
	
	
			dlog("NAB Sending Request");
			
			try {
	    		$response = curl_exec( $ch );
		  } catch (Exception $e) {
		  		dlog("NAB - ##EXCEPTION## - ".$e->getMessage());
		  		
		  		$result = array(
			      'success' => FALSE,
			      'comment' => t( 'Credit card payment failed: @text', array( '@text' => 'Bank Returned Error at this time' ) ),
			      'message' => t( 'Credit card payment failed: @text', array( '@text' => 'Bank Returned Error at this time' ) ),
			      'uid' => $user->uid,
			    );
			    
			    return $result;
		  }
	    
	    dlog("Request Completed");
	    curl_close( $ch );
	  //}
	
	  // Include the XML parser for PHP 4 compatibility.
	  //module_load_include('php', 'uc_store', 'includes/simplexml');
	
		dlog("NAB Creating XML OBject");
		dlog("NAB Response: ".$response);
	  // Create the XML object and parse the response string.
	  $xml = simplexml_load_string( $response );
	  
	  dlog("NAB Response XML: ".$xml);
	  //print_r($xml);
	  // Check to make sure the response parses and payment passed properly.
	  if ( isset( $xml->Status->statusCode ) && $xml->Status->statusCode != '000' ) {
	    $approval = 'No';
	    $responsecode = $xml->Status->statusCode;
	    $responsetext = $xml->Status->statusDescription;
	    dlog("NAB - NO APPROVAL");
	  }
	  elseif ( isset( $xml->Payment->TxnList->Txn->approved ) ) {
	    $approval = $xml->Payment->TxnList->Txn->approved;
	    $responsecode = $xml->Payment->TxnList->Txn->responseCode;
	    $responsetext = $xml->Payment->TxnList->Txn->responseText;
	    $charged = $xml->Payment->TxnList->Txn->amount / 100;
	    $txnid = $xml->Payment->TxnList->Txn->txnID;
	    dlog("NAB - CHECKING");
	  }
	  else {
	    // Otherwise supply some default values.
	    $approval = 'No';
	    $responsecode = 'x';
	
	    // If $response is set, we made a request, error was in the response
	    if ( isset( $response ) ) {
	      $responsetext = t( 'Failed to parse the XML API request or response.' );
	      // Log the trouble string to the watchdog.
	      watchdog( 'uc_nab_transact', 'Failed XML parse response:<br/>@xml', array( '@xml' => $response ), WATCHDOG_ERROR );
	    }
	    else {
	      $responsetext = t( 'Failed to make the request.' );
	      // Log the trouble string to the watchdog.
	      watchdog( 'uc_nab_transact', 'Failed to make the request: "openssl" or "curl" PHP extensions are needed.', array(), WATCHDOG_ERROR );
	    }
	    
	    dlog("NAB - Failed: ".$responsetext);
	  }
	
	  if ( $approval != 'Yes' ) {
	    $message = t( 'Credit card declined: !amount', array( '!amount' => $amount ) );
			
			dlog("NAB - Declined: ".$message);
			
	    $result = array(
	      'success' => FALSE,
	      'comment' => t( 'Credit card payment declined: @text', array( '@text' => $responsetext ) ),
	      'message' => t( 'Credit card payment declined: @text', array( '@text' => $responsetext ) ),
	      'uid' => $user->uid,
	    );
	  }
	  else {
	  	
	    $message = t( 'Credit card charged: !amount', array( '!amount' => $charged ) )
	      .'<br />'. t( 'NAB Transact Txn ID: @txnid', array( '@txnid' => $txnid ) );
			
			dlog("NAB - Approved: ".$message);
			
	    $result = array(
	      'success' => TRUE,
	      'comment' => t( 'NAB Transact Txn ID: @txnid<br/>Approval code: @code', array( '@txnid' => $txnid, '@code' => $responsecode ) ),
	      'message' => t( 'NAB Transact Txn ID: @txnid<br/>Approval code: @code', array( '@txnid' => $txnid, '@code' => $responsecode ) ),
	      'data' => array( 'TxnID' => $txnid ),
	      'uid' => $user->uid,
	    );
	  }
	
	  $message .= '<br />'. t( 'Response code: @code - @text', array( '@code' => $responsecode, '@text' => $responsetext ) );
	
		dlog("NAB - Result: ".$message);
	  return $result;
	}

	function t($inp, $valueArray){
		
		foreach($valueArray as $valueKey => $value){
			$inp = str_replace($valueKey,$value,$inp);
		}
		
		return db::esc($inp);
	}
	
	function uc_nab_transact_xml( $type, $data, $message_id ) {
	  if ( $type !== 'Payment' && $type !== 'Echo' ) {
	    return;
	  }
	
	  $xml = '<?xml version="1.0" encoding="UTF-8"?><NABTransactMessage>';
	
	  // element: MessageInfo
	  //$xml .= uc_nab_transact_message_info($message_id);
	
	  // element: MerchantInfo
	  $xml .= uc_nab_transact_merchant_info();
	
	  // element: RequestType
	  $xml .= '<RequestType>'. $type .'</RequestType>';
	
	  // element: Payment
	  if ( $type == 'Payment' ) {
	    $xml .= uc_nab_transact_payment_xml( $data );
	  }
	
	  $xml .= '</NABTransactMessage>';
	
	  return $xml;
	}
	
	function uc_nab_transact_xml_test( $type, $data, $message_id ) {
	  if ( $type !== 'Payment' && $type !== 'Echo' ) {
	    return;
	  }
	
	  $xml = '<?xml version="1.0" encoding="UTF-8"?><NABTransactMessage>';
	
	  // element: MessageInfo
	  //$xml .= uc_nab_transact_message_info($message_id);
	
	  // element: MerchantInfo
	  $xml .= uc_nab_transact_merchant_info_test();
	
	  // element: RequestType
	  $xml .= '<RequestType>'. $type .'</RequestType>';
	
	  // element: Payment
	  if ( $type == 'Payment' ) {
	    $xml .= uc_nab_transact_payment_xml( $data );
	  }
	
	  $xml .= '</NABTransactMessage>';
	
	  return $xml;
	}
	
	
	function uc_nab_transact_message_info( $message_id ) {
	  return '<MessageInfo><messageID>'. substr( md5( $message_id ), 0, 30 )
	    .'</messageID><messageTimestamp>'. uc_nab_transact_timestamp()
	    .'</messageTimestamp><timeoutValue>60</timeoutValue>'
	    .'<apiVersion>xml-4.2</apiVersion></MessageInfo>';
	}
	
	
	function uc_nab_transact_timestamp( $time = NULL ) {
	  if ( empty( $time ) ) {
	    $time = time();
	  }
	  // Return a formatted GMT timestamp.
	  return date( 'YdmHis000000+000', $time );
	}
	
	function uc_nab_transact_merchant_info() {
	
		if($GLOBALS['nabSandbox']){
			//Sanbox Details
			$merchantID   = "ABC0001";
			$merchantpass = "changeit";
		} else {
			//Live Details
			$merchantID   = "D3P0010";
			$merchantpass = "ciA5oPNZ";
		}
		
		$merchantInfo = '<MerchantInfo><merchantID>'.$merchantID.'</merchantID><password>'.$merchantpass.'</password></MerchantInfo>';
		return $merchantInfo;
	}

	function uc_nab_transact_merchant_info_test() {

			//Sanbox Details
			$merchantID   = "ABC0001";
			$merchantpass = "changeit";

		
		$merchantInfo = '<MerchantInfo><merchantID>'.$merchantID.'</merchantID><password>'.$merchantpass.'</password></MerchantInfo>';
		return $merchantInfo;
	}
	
	function uc_nab_transact_payment_xml( $data ) {
	  $xml = '<Payment><TxnList count="1"><Txn ID="1">';
	
	  // Create elements from array
	  foreach ( $data as $key => $value ) {
	    if ( is_array( $value ) ) {
	      $xml .= '<'. $key .'>';
	      // Create elements from nested array
	      foreach ( $value as $arr_key => $arr_value ) {
	        $xml .= '<'. $arr_key .'>'. $arr_value .'</'. $arr_key .'>';
	      }
	      $xml .= '</'. $key .'>';
	    }
	    else {
	      $xml .= '<'. $key .'>'. $value .'</'. $key .'>';
	    }
	  }
	
	  $xml .= '</Txn></TxnList></Payment>';
	
	  return $xml;
	}

	function axcelerate_save_contact( $contactVars ) {
	
	  $url = axcelerate_get_url()."/api/contact";
	  return postAXData($url,$contactVars);
	
	}
	
	
	function axcelerate_enroll( $contactVars ) {
	
	  $url = axcelerate_get_url()."/api/course/enrol";
	  return postAXData($url,$contactVars);
	
	}
	
	function axcelerate_multienroll( $contactVars ) {
	
	  $url = axcelerate_get_url()."/api/course/enrolMultiple";
	  return postAXData($url,$contactVars);
	
	}
	
	function axcelerate_multienrollupdate( $contactVars ) {
	
	  $url = axcelerate_get_url()."/api/course/enrolment";
	  return putAXData($url,$contactVars);
	
	}
	
	function axcelerate_getInvoice( $invoiceID ) {
		
	  $contactVars = array(
	  					'invoiceID' => $invoiceID,
	  				 );
	  $contactVars = array();
	  $url = axcelerate_get_url()."/api/accounting/invoice/".$invoiceID;
	  return getAXData($url,$contactVars);
	
	}
	
	
	function axcelerate_transact( $contactVars ) {
	
	  $url = axcelerate_get_url()."/api/accounting/transaction/";
	  return postAXData($url,$contactVars);
	
	}
	

	
	function logBookingDetails($bookingdetails){
		//var_dump($bookingdetails);
		$courseID      = db::esc($bookingdetails['courseID']);
		$instanceID    = db::esc($bookingdetails['instanceID']);
		$learnerID     = db::esc($bookingdetails['learnerID']);
		$invoiceID     = db::esc($bookingdetails['invoiceID']);
		$TXN           = db::esc($bookingdetails['Txn']);
		$USI           = db::esc($bookingdetails['USI']);
		$contactID     = db::esc($bookingdetails['contactID']);
		$paymentMethod = db::esc($bookingdetails['paymentMethod']);
		$campaignID    = db::esc($bookingdetails['campaignID']);
		$orderID       = db::esc($bookingdetails['orderID']);
		$failed        = db::esc($bookingdetails['failed']);
		$dollorAmount  = db::esc($bookingdetails['dollorAmount']);
		$NABTransData  = db::esc($bookingdetails['NABTransData']);
		$AxTransData   = db::esc($bookingdetails['AxTransData']);
		
		$userSource    = db::esc($bookingdetails['userSource']);
		$specialNeeds  = db::esc($bookingdetails['specialNeeds']);
		$optInReBook   = db::esc($bookingdetails['optInReBook']);
	
	
		if($failed){
			$failed = 1;
		} else {
			$failed = 0;
		}
		
		db::insertQuery("insert into bookingDetails (courseID, instanceID, learnerID, invoiceID, TXN, contactID, paymentMethod, couponID, orderID, failed, dollorAmount, AxcelerateTransData, NABTransData, userSource, specialNeeds, optInReBook, USI) values('$courseID','$instanceID','$learnerID','$invoiceID','$TXN','$contactID','$paymentMethod','$campaignID','$orderID','$failed','$dollorAmount','$AxTransData','$NABTransData','$userSource','$specialNeeds','$optInReBook','$USI')");
		
		$_SESSION['EnrolLocked'] = false;
		return true;
	}

	function removeBlockQuotes($inp){
		return $inp;
		$outp = str_replace("[","",$inp);
		$outp = str_replace("]","",$outp);
		
		return $outp;
		
	} 
	
	function getBookingDetailsFromInvoiceID($invoiceID){
		$invoiceID = db::esc($invoiceID);
		$data = db::runQuery("SELECT * FROM bookingDetails left join events on bookingDetails.instanceID = events.instanceID left join courses on courses.CourseID = events.courseID left join locations on events.locationID = locations.ID where bookingDetails.invoiceID = '$invoiceID'");
		if($data){
			return $data[0];
		} else {
			return false;
		}
	}
	
	function arrtosqltable($array){
			
			$table = $array;
			/*$table = array(
									'column1' => "My Value 1' \'  '\  sdfsdf ",
									'column2' => "My Value 2"
								);	
			*/
			
			$columns = array();
			$values = array();
			foreach($table as $tableKey => $tableValue){
				
					//echo "Column: ".$tableKey."<br>";
					//echo "---------- Value: ".$tableValue."<br><br>";
					
					$columns[] = $tableKey;
					$values[] = $tableValue;
					
			}
			
			$outputString = "(";
			foreach($columns as $columnname){
				
				$columnname = db::esc($columnname);
				if($outputString=="("){
					$outputString = $outputString.$columnname."";
				} else {
					$outputString = $outputString.", ".$columnname;	
				}
				
			}
			
			$outputString = $outputString.") values(";
			$ic = 0;
			foreach($values as $value){
				
				$value = db::esc($value);
				if($ic==0){
					$outputString = $outputString."'".$value."'";
				} else {
					$outputString = $outputString.", '".$value."'";	
				}
				
				$ic++;
				
			}
			$outputString = $outputString.") ";
			
			return $outputString;
	}
	
	function arrtosqltable_update($array){
			
			$table = $array;
			/*$table = array(
									'column1' => "My Value 1' \'  '\  sdfsdf ",
									'column2' => "My Value 2"
								);	
			*/
			
			$outputString = " set ";
			$ic = 0;
			foreach($table as $tableKey => $tableValue){
				
					//echo "Column: ".$tableKey."<br>";
					//echo "---------- Value: ".$tableValue."<br><br>";
					if($ic==0){
						$outputString = $outputString."".db::esc($tableKey)." = '".db::esc($tableValue)."'";
					} else {
						$outputString = $outputString.", ".db::esc($tableKey)." = '".db::esc($tableValue)."'";	
					}
					
					$ic++;
					
			}
			$outputString = $outputString." ";
			
			return $outputString;
	}
	
?>