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
		
		$progroup = db::runQuery("select * from process_group where process_status = '4'",0,1);
		
		if($progroup){
			foreach($progroup as $payment){
				
				dlog("Charging: ".$payment['cc_num'], false, $ListgroupID); 
				
				$groupID = db::esc($payment['ID']);
				$ListgroupID = db::esc($payment['processgroupID']);
				
				$dontInvoiceUser = db::esc($payment['dontInvoiceUser']);
				$addressToInvoice = db::esc($payment['addressToInvoice']);
				
				$campaignSetcode = $payment['campaign_code'];
				
				db::insertQuery("update process_group set process_status = '5' where ID = '$groupID'");
				
				
				if(!$campaignSetcode==""){
					$campaignCode = campaignSetcode;
					$campaignDetails = getCampaignDetails($campaignCode);
					$sendManualInvoice = true;
				} else {
					$campaignCode = false;
					$campaignDetails = false;
					$sendManualInvoice = false;
				}

				$cost = "$".$payment['totalcost'];
				$courseCostTotal = $payment['totalcost'];
				
				if($payment['payment_type']=="offline"){
					
					db::insertQuery("update process_group set process_status = '6', payment_status = 'complete' where ID = '$groupID'");
				
				} else if($payment['payment_type']=="payment-employer"){
					
					db::insertQuery("update process_group set process_status = '6', payment_status = 'employer' where ID = '$groupID'");
					
					$trainees = db::runquery("select * from process_trainee where processgroupID = '$ListgroupID'");
					if($trainees){
						foreach($trainees as $trainee){
							
							
							$learnerID = $trainee['leanerID'];
							$contactID = $trainee['contactID'];
							$invoiceID = $trainee['invoiceID'];
							
							
							$bookingID = $learnerID."-".$contactID;
						
						
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
										'couponCompany' => $trainee['workplace']
										);
									
							email::sendBookingConfirmation_noinvoice($bookingID,$courseInstanceID,$enrolVar,$addressToInvoice);
							
						}
					} else {
						
					}
						
				} else {
					
					
					db::insertQuery("update process_group set payment_status = 'preparing' where ID = '$groupID'");
					
					//$courseCostTotal
					//$courseCostTotal = $payment['totalcost'];
					
					$courseInstanceID = $payment['eventID'];
					
					$addressToInvoice = $payment['addressToInvoice'];
					//dlog("Charging: ".$payment['cc_num'], false, $ListgroupID); 
					$cc_num = $payment['cc_num'];
					$cc_month = $payment['cc_month'];
					$cc_year = $payment['cc_year'];
					
					$primContactID = $payment['primaryContactID'];
					
					$groupInvoiceID = $payment['GroupInvoiceID'];
					
					$amount = number_format($courseCostTotal ,2);
					//var_dump($courseCostTotal." -- ".$amount);
	          //dlog("Charging: ".$cc_num, false, $ListgroupID); 
	                
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
					dlog("Start NAB Trans ", false, $ListgroupID); 
					$trans    = uc_nab_transact_charge($order_id, $amount, $data);
					
					$txnID    = $trans['data']['TxnID'][0];
					ob_start();
					var_dump($trans);
					$NABTrans_dump = ob_get_clean();
					dlog($NABTrans_dump, false, $ListgroupID); 
					//var_dump($trans);
					dlog("Finish NAB Trans ", false, $ListgroupID); 
			
			    if($trans['success'] == '1' || $cc_num == "1234123443214321"){
					    $transVars = array(
				                           'amount' => $courseCostTotal,
				                           'ContactID' => $primContactID,
				                           'invoiceID' => $groupInvoiceID,
				                           'reference' => $txnID,
				                           'description' => "TXN: ".$txnID
				                     );
								 
				    	$extrans =  axcelerate_transact($transVars);
						
						ob_start();
						echo("Axcelerate Transact\n");
						var_dump($extrans);
						echo("\n\nvars:\n\n");
						var_dump($transVars);
						
						$extrans_dump = ob_get_clean();
						//dlog($extrans_dump, false, $ListgroupID); 
						
						$enrolmentReturn = array(
												'success' => true,
												'error_message' => '',
												'txnid' => "".$txnID."",
												'invoice' => $groupInvoiceID,
						                   );
										   
						$dontInvoiceUser = false;
						if($sendManualInvoice){
							
							
							$trainees = db::runquery("select * from process_trainee where processgroupID = '$ListgroupID'");
							
							if($trainees){
								foreach($trainees as $trainee){
									
									
									$learnerID = $trainee['leanerID'];
									$contactID = $trainee['contactID'];
									$invoiceID = $trainee['invoiceID'];
									$learnerID = $trainee[''];
									
									$bookingID = "CA".$enrollNow->LEARNERID."-".$contactID;
								
									
								
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
											
									email::sendBookingConfirmation($bookingID,$courseInstanceID,$enrolVar,$trainee['email']);
									
								}
							} else {
								
							}
						}
						
						db::insertQuery("update process_group set process_status = '6', payment_status = 'completed', cc_num = '#### #### #### ####', cc_ccv = '###', cc_month = '##', cc_year = '##' where ID = '$groupID'");
						
					} else {
						$errorMessage = db::esc($trans['message']);
						if($cc_num == "1234123443214321"){
							db::insertQuery("update process_group set process_status = '6', payment_status = 'completed' where ID = '$groupID'");
						
						} else {
							
							//dlog("Failed:".$cc_num, false, $ListgroupID); 
							db::insertQuery("update process_group set process_status = '99', errorMessage = 'Payment Failed: $errorMessage', payment_status = 'failed - $NABTrans_dump', cc_num = '#### #### #### ####', cc_ccv = '###', cc_month = '##', cc_year = '##' where ID = '$groupID'");
						}
					}
					
				}
				
				
				
				//Clear credit card details out
				//db::insertQuery("update process_group set cc_num = '#### #### #### ####', cc_ccv = '###', cc_month = '##', cc_year = '##' where ID = '$groupID'");
				
			}
		}
		
	}
	
	function getDiscountPrice($campaignCode,$courseCostTotal){
				if(!$campaignCode==""){
					//$campaignCode = campaignSetcode;
					$campaignDetails = getCampaignDetails($campaignCode);
				} else {
					$campaignCode = false;
					$campaignDetails = false;
				}
				
				if($campaignDetails){
					if($campaignDetails['discountType']==1){
							//Percent Discount
						$courseCostTotal = $courseCostTotal-($courseCostTotal*("0.".$campaignDetails['discountAmount']));
						$manualInvoiceEmailPrice = $courseCostTotal;
						$customPriceOption = true;
						$sendManualInvoice = true;
						
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
						$customPriceOption = true;
						$sendManualInvoice = true;
					}
				} else {
					  
						$manualInvoiceEmailPrice = $courseCostTotal;
						$customPriceOption = false;
						$sendManualInvoice = false;
				}
		

				return $courseCostTotal;
	}
	
	function goEnrolments(){
		
		$progroup = db::runQuery("select * from process_group where process_status = '2'",0,1);
		if($progroup){
				foreach($progroup as $pgr){
					
					$supressInvoiceEmail = "0";
					$generateInvoice = "true";
					$archiveINvoice = "true";
					$Continue = true;
					//Check if we already have an invoice (Usually due to a failed payment needing to be come back through).
					$enrCheck = db::runQuery("select * from |process_group| where processgroupID = '$processgroup'");
					if($enrCheck){
						$enrCheckcur = $enrCheck[0];
						if(!$enrCheckcur['GroupInvoiceID']==""){
							db::insertQuery("update |process_group| set process_status = '4' where processgroupID = '$processgroup'");
							continue;
							
						}
					}
					
					
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
						
						$sendAWFANotice = false;
						$autoLockandCloseInvoice = true;
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
						
						$sendAWFANotice = $campaignDetails['awfaSendInvoiceNotification'];
						$autoLockandCloseInvoice = $campaignDetails['awfaAutoLockandCloseInvoice'];
						if($autoLockandCloseInvoice==0){
							$autoLockandCloseInvoice = false;
						}
						if($autoLockandCloseInvoice==1){
							$autoLockandCloseInvoice = true;
						}
						
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
					

					$processgroup = db::esc($pgr['processgroupID']);
					
					$contactIDtoInvoice = db::esc($contactIDtoInvoice);
					$allowOfflinePayments = db::esc($allowOfflinePayments);
					$forceOfflinePayments = db::esc($forceOfflinePayments);
					$dontInvoiceUser = db::esc($dontInvoiceUser);
					$addressToInvoice = db::esc($addressToInvoice);
					
					
					 //Create Multi Enrolment
						 
						 $trainees = db::runQuery("select * from process_trainee where processgroupID = '$processgroup'");
						 $primaryContact = "";
						 $courseInstanceID = "";
						 $processgroup = "";
						 $costperperson = $courseDetails['cost'];
						 
						 $subt = getDiscountPrice($campaignCode,$costperperson);
						 
						 $costperperson = $subt;
						 
							if(!$costperperson==$subt){
							 $hasdiscount = true;
							} else {
								$hasdiscount = false;
							}
						 
						 echo $costperperson." ---<br>";
						 
						 $totalCostCal = 0;
						 if($trainees){
						 		$contactList = "";
						 		foreach($trainees as $trn){
						 			
						 				if($contactList==""){
						 						$contactList = $contactList.$trn['contactID'];
						 						$primaryContact = $trn['contactID'];
						 						$courseInstanceID = $trn['eventID'];
						 						$processgroup = $trn['processgroupID'];
												$processgroup = $trn['processgroupID'];
												$costperperson = $costperperson;
						 				} else {
						 					$contactList = $contactList.",".$trn['contactID'];
						 				}
						 				$totalCostCal = $totalCostCal + $costperperson;
						 				
						 		}	
						 		
								if($contactIDtoInvoice!=false){
									$primaryContact = $contactIDtoInvoice;
								}
								
								$totalCostCal = db::esc($totalCostCal);
						 	
						 		$primaryContact =  db::esc($primaryContact);
						 		
							 	db::insertQuery("update process_group set totalcost = '$totalCostCal', process_status = '3', primaryContactID = '$primaryContact', allowOfflinePayments = '$allowOfflinePayments', forceOfflinePayments = '$forceOfflinePayments', dontInvoiceUser = '$dontInvoiceUser', addressToInvoice = '$addressToInvoice', contactIDtoInvoice = '$contactIDtoInvoice' where processgroupID = '$processgroup'");
								
						 	
						      
						 		
								//$supressInvoiceEmail = "false";
								
								
								if($autoLockandCloseInvoice){
									$lockInvoiceItems = "false";
									$supressInvoiceEmail = "true";
									$generateInvoice = "true";
									$archiveINvoice= "false";
								} else {
									$lockInvoiceItems = "true";
									$supressInvoiceEmail = "false";
									$generateInvoice = "true";
									$archiveINvoice= "true";
								}
								
								if($hasdiscount){
					      	$supressInvoiceEmail = "true";
					      	
					      	//20151211 - AJ - Fix for invoices not auto archiving when they should
					      	$lockInvoiceItems = "false";
									$supressInvoiceEmail = "true";
									$generateInvoice = "true";
									$archiveINvoice= "false";
					      } else {
					      	$supressInvoiceEmail = "false";
					      	
					      	//20151211 - AJ _ Fix for invoices not auto archiving when the should
					      	$lockInvoiceItems = "true";
									$supressInvoiceEmail = "false";
									$generateInvoice = "true";
									$archiveINvoice= "true";
					      }
								
							 	$enrollVars  = array(
								      					'contactID'   => $contactList,
								      					'instanceID'  => $courseInstanceID,
								      					//'invoiceID'   => $contactInvoiceID,
								      					'payerID'			=> $primaryContact,
								      					'type'        => 'w',
								      					'generateInvoice'=>$generateInvoice,
								      					'suppressEmail'=> $supressInvoiceEmail,
								      					'archiveInvoice'=>$archiveINvoice,
								      					'lockInvoiceItems'=> $lockInvoiceItems,
								          		 );
								          		 
									$enrollNow =  axcelerate_multienroll($enrollVars,$processgroup);
									 
									
									ob_start();
									echo("Axcelerate Multi Enrol\n");
									var_dump($enrollNow);
									echo("\n\nvars:\n\n");
									var_dump($enrollVars);
							
									$extrans_dump = ob_get_clean();
									dlog($extrans_dump, false, $processgroup);
									
									
									 
	
									 if(isset($enrollNow->error)){
												$enrollIsError = true;
												$trans['message'] = $enrollNow->MESSAGES;
												dlog("Enrol-Enrolment: Axcelerate returned error: $processgroup - ".$trans['message'], false, $processgroup);
												
												markGroupError($processgroup,$trans['message']);
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
											
											if($ContactID==""){
												markGroupError($processgroup,"");	
											}
												
											$groupInvoiceID = $InvoiceID;
											
											if(!$sendAWFANotice==""){
												//Send a manual invoice flag
												$sendManualInvoice = "1";
												$sendAWFANotice = db::esc($sendAWFANotice);
											} else {
												$sendManualInvoice = "0";
											}
																					
											db::insertQuery("update process_trainee set learnerID = '$LearnerID', invoiceID = '$InvoiceID', sendManualInvoice = '$sendManualInvoice', manualInvoiceTo = '$sendAWFANotice' where contactID = '$ContactID' and processgroupID = '$processgroup'");
									}
									
									
									
									if($forceOfflinePayments==true){
										db::insertQuery("update process_group set GroupInvoiceID = '$groupInvoiceID', process_status = '4', payment_type = 'offline', primaryContactID = '$primaryContact' where processgroupID = '$processgroup'");
									} else {
										db::insertQuery("update process_group set GroupInvoiceID = '$groupInvoiceID', process_status = '4', primaryContactID = '$primaryContact' where processgroupID = '$processgroup'");	
									}
									
									if($sendManualInvoice=="1"){
							 			sendManualInvoice($processgroup);
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
		
		$progroup = db::runQuery("select * from process_group where process_status = '0'");
		if($progroup){
				foreach($progroup as $pgrp){
						$id = db::esc($pgrp['ID']);
						$processgroup = $pgrp['processgroupID'];
						
						db::insertQuery("update process_group set process_status = '1' where ID = '$id'");
						
				}
		}
	}
	
	function checkEnrolmentsComplete(){
		$progroup = db::runQuery("select * from process_group where process_status = '1'");
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
		
		$trainees = db::runQuery("select * from process_trainee where processed = '0'  and inerror = '0'");
		if($trainees){
			foreach($trainees as $trains){
				
				$traineeBatchID = db::esc($trains['ID']);
				$processgroup = db::esc($trains['processgroupID']);
				
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
			                   
			        db::insertQuery("update process_trainee set processed = '2' where ID = '$traineeBatchID'");
					           
			        $enroll      = axcelerate_save_contact($contactVars);
					
					
					ob_start();
					echo("Axcelerate Create Contact\n");
					var_dump($enroll);
					echo("\n\nvars:\n\n");
					var_dump($contactVars);
			
					$extrans_dump = ob_get_clean();
					dlog($extrans_dump, false, $processgroup);
					
					dlog("is Error: ".$enroll->ERROR, false, $processgroup);
					dlog("Error Message: : ".$enroll->DETAILS, false, $processgroup);
					
					
					if($enroll->ERROR==1 || $enroll->ERROR=="1" || $enroll->ERROR==true){
						$errorMessage = $enroll->DETAILS;
						
						db::insertQuery("update process_trainee set processed = '0', inerror = '1' where ID = '$traineeBatchID'");
						
						$errorMessage = db::esc($errorMessage);
						db::insertQuery("update process_group set process_status = '99' and errorMessage = '$errormessage' where processgroupID = '$processgroup'");
						
						markGroupError($processgroup,$errorMessage);	
						
					} else {
			

 
 
		        		$contactID   = db::esc($enroll->CONTACTID);
						
						db::insertQuery("update process_trainee set processed = '1', contactID = '$contactID' where ID = '$traineeBatchID'");
						
						checkEnrolmentsComplete();
					}
				}
			}
		}
		
		return true;
	}
	
	
	function markGroupError($processgroup,$errormessage = false){
		
			if($errormessage==""){
				$errormessage = false;
			}
			
			//$prechangedetails = db::runQuery("select * from process_group where processgroupID = '$processgroup'");
			
			$processgroup = db::esc($processgroup);
			$errormessage = db::esc($errormessage);
			if($errormessage){
				db::insertQuery("update process_group set process_status = '99', errorMessage = '$errormessage' where processgroupID = '$processgroup'");
				
				dlog("Made status 99 - custom message", "autoprocessing-error");
			} else {
				db::insertQuery("update process_group set process_status = '99', errorMessage = 'An Error has occurred. Please contact us on 1300 336 613' where processgroupID = '$processgroup'");
				dlog("Made status 99 - generic message", "autoprocessing-error");
			}
			
			//Clear credit card details out
			//db::insertQuery("update process_group set cc_num = '#### #### #### ####', cc_ccv = '###', cc_month = '##', cc_year = '##' where processgroupID = '$processgroup'");
			
			//check if currently enrolled, if so we need to set that enrolment to cancelled.
			
			
			dlog($processgroup."-".$errormessage, "autoprocessing-error",$processgroup);
			
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
	
	function dlog($message, $vtype = false, $groupID = false){
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
		
		if($groupID){
			$sessionID = $groupID;
		} else {
			$sessionID = session_id();
		}
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
	
	function arrtosqltable_insert($array){
		return arrtosqltable($array);
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
	
	function arrtosqltable_select($array){
			
			$table = $array;
			/*$table = array(
									'column1' => "My Value 1' \'  '\  sdfsdf ",
									'column2' => "My Value 2"
								);	
			*/
			
			$outputString = " where ";
			$ic = 0;
			foreach($table as $tableKey => $tableValue){
				
					//echo "Column: ".$tableKey."<br>";
					//echo "---------- Value: ".$tableValue."<br><br>";
					if($ic==0){
						$outputString = $outputString."".db::esc($tableKey)." = '".db::esc($tableValue)."'";
					} else {
						$outputString = $outputString." and ".db::esc($tableKey)." = '".db::esc($tableValue)."'";	
					}
					
					$ic++;
					
			}
			$outputString = $outputString." ";
			
			return $outputString;
	}
	
	function sendManualInvoice($processgroupID){
		
		$emailKey = $processgroupID;
		$html = getEmailTemplate("template_invoicenotification.htm");
		
		$processgroupID = db::esc($processgroupID);
		$mainData = db::runQuery("select * from process_group left join events on events.instanceID = process_group.eventID left join courses on courses.courseID = events.courseID left join locations on locations.ID = events.locationID left join coupons on coupons.couponCode = process_group.campaign_code where processgroupID = '$processgroupID'");
		
		if(!$mainData){
			return false;
		} else {
			$mData = $mainData[0];
			
			$courseName = $mData['CourseName'];
			$couponName = $mData['campaignName'];
			$normalCost = $mData['cost'];
			$totalTrainees = $mData['total_trainee'];
			$discountcost = $mData['totalcost'];
			
			$costNormalTotal = $normalCost * $totalTrainees;
			$discountcostPerperson = $discountcost / $totalTrainees;
			
			$amountDiscountedTot = $costNormalTotal - $discountcost;
			
			$invoiceItems = "";
			for($tr = 0; $tr < $totalTrainees; $tr++){
				$subHTML = "";
				$subHTML = $subHTML."		<tr style='font-size:13px;'>";
        $subHTML = $subHTML."			<td>".$courseName." - ".$couponName."</td>";
        $subHTML = $subHTML."			<td>$".number_format($normalCost,2)."</td>";
        $subHTML = $subHTML."			<td>$".number_format($discountcostPerperson,2)."</td>";
        $subHTML = $subHTML."		</tr>";
        
        $invoiceItems = $invoiceItems.$subHTML;
			}
			
			$html = varReplace("invoiceItemsRow",$invoiceItems,$html);

			$html = varReplace("order.totalNormal",number_format($costNormalTotal,2),$html);
			$html = varReplace("order.Total",number_format($discountcost,2),$html);
			
			
			$html = varReplace("order.amountDiscounted",number_format($amountDiscountedTot,2),$html);
			$html = varReplace("order.subTotal",number_format($discountcost,2),$html);
			
			

			if($mData['payment_status']=="completed"){
				$html = varReplace("order.amountPaid",number_format($discountcost,2),$html);			
				$html = varReplace("order.balanceDue","0.00",$html);
			} else {
				$html = varReplace("order.amountPaid","0.00",$html);			
				$html = varReplace("order.balanceDue",number_format($discountcost,2),$html);
			}
			
			
			$html = varReplace("amountToInvoice",number_format($discountcost,2),$html);
		
			$html = varReplace("eventID",$mData['eventID'],$html);
			$html = varReplace("courseName",$mData['CourseName'],$html);
			$html = varReplace("courseDateTime",$mData['startDateTime'],$html);
			$html = varReplace("courseLocation",$mData['LocationState']." ".$mData['LocationName']." - ".$mData['StreetAddress'],$html);
			
			$html = varReplace("totalTrainees",$mData['total_trainee'],$html);
			$html = varReplace("courseNormalCost","$".number_format($costNormalTotal,2),$html);
			
			$html = varReplace("couponCode",$mData['campaign_code'],$html);
			$html = varReplace("couponName",$couponName,$html);
			
			if($mData['discountType']=="0"){
				$html = varReplace("couponDiscount","$".$mData['discountAmount']." off total",$html);
			}
			if($mData['discountType']=="1"){
				$html = varReplace("couponDiscount",$mData['discountAmount']."% off total",$html);
			}
			if($mData['discountType']=="2"){
				$html = varReplace("couponDiscount","Custom price for Location",$html);
			}
			if($mData['discountType']=="3"){
				$html = varReplace("couponDiscount","Custom price for Course",$html);
			}
			
			$trainees = db::runQuery("select * from process_trainee where processgroupID = '$processgroupID'");
			if(!$trainees){
				return false;
			} else {
				
				$currentTrainee = 1;
				$traineehtml = "";
				foreach($trainees as $trainee){
					$trhtml = "";
					$trhtml = $trhtml.'<b>Trainee '.$currentTrainee.'</b>';
	        $trhtml = $trhtml.'	<table style="width:100%; margin-left:70px;">';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Contact ID</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;"><a href="https://admin.axcelerate.com.au/management/management2/Contact_View.cfm?ContactID='.$trainee['contactID'].'">'.$trainee['contactID'].'</a></th>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Learner ID</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['leanerID'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Invoice ID</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['invoiceID'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Amount to Invoice</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">$'.number_format($trainee['cost'],2).'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">&nbsp</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;"></td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Name</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['firstname'].' '.$trainee['lastname'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">USI</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['usi'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Email Address</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['email'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Mobile/Phone</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['mobile'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Workplace</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['workplace'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'		<tr style="font-size:13px;">';
	        $trhtml = $trhtml.'			<th width="30%" style="text-align:left;">Address</th>';
	        $trhtml = $trhtml.'			<td style="text-align:left;">'.$trainee['address'].'<br>'.$trainee['suburb'].' '.$trainee['postcode'].'</td>';
	        $trhtml = $trhtml.'		</tr>';
	        $trhtml = $trhtml.'	</table><br><br>';
	        $traineehtml = $traineehtml.$trhtml;
	        $currentTrainee++;
				}
				
				$html = varReplace("traineeDetails",$traineehtml,$html);
				
				
				
				$remail = $mData['awfaSendInvoiceNotification'];
				//$remail = "andrew@vbz.com.au";
				$subject = "MANUAL INVOICE: Please generate a manual invoice for this Discounted Enrolment";
				$message = $html;
				$bookingID = "";
				$instanceID = "";
				$ccEmail = ""; //$ccEmail = "accounts@australiawidefirstaid.com.au";
				
				
				email::logEmail($remail,$ccEmail,"",$subject,$message,$bookingID,$instanceID,$emailKey);
				
				esmtp::sendemail_smtp($remail,$subject,$message,$ccEmail);
				
				echo $html;
			}
		}
		
		
	}
?>