<?php

set_time_limit(0);

include("application.php");
include("db.class.php");
include("functions.php");
include("esmtp.class.php");
include("email.class.php");

dlog("Load "); 




$blockedCourses 		    = array('1052','517','1690','1780','1053','518');
$blockedCoursesByInstanceID = array('154569','155759','155762','132187','133296','132096','132063','153925','143974','152656','133049','153930','153925','132160','133273','132162','133276','132263','132807','137230','137241','132940','133005','141628','141785','152608','137054','137151','137054','137151');
	
	$app = $_GET['rq'];
	
	if($app=="checkVRS"){
		
		echo(md5("SESSION-34817184"));
	}

	if($app=="getCourseInfo"){
		
			$inputData = $_GET['rd'];
			$inputDataArr = explode("_",$inputData);
			$IDACount = count($inputDataArr);
			
			if(isset($_GET['campaign'])){
				$campaignCode = $_GET['campaign'];
				$campaignDetails = getCampaignDetails($campaignCode);
			} else {
				$campaignCode = false;
				$campaignDetails = false;
			}
			////var_dump($IDACount);
			if($IDACount==1){
					//State Only
					$state = db::esc($inputDataArr[0]);
					if($campaignDetails){
						if($campaignDetails['restrictCourses'] || $campaignDetails['restrictCourses']=="1"){
							//var_dump($campaignDetails);
							$couponID = $campaignDetails['ID'];
							$locations = db::runQuery("select locations.* from locations inner join coupon_locationRestriction on coupon_locationRestriction.locationID = locations.ID  where locationState = '$state' and mergeWithID = '0' and coupon_locationRestriction.couponID = '$couponID' order by LocationName asc");
								//var_dump($locations);
							//$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
							
						} else {
							$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
						}
					} else {
						$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
					}
					if($locations){
						$locCount = count($locations);
						$lastID = "";
						foreach($locations as $location){
							//if(!$lastID==$location['ID']){
								echo '<option value="'.$location['ID'].'" data-loc="'.$locCount.'">'.$location['LocationName'].'</option>';
							//}
							$lastID = $location['ID'];
						}
					} else {
						echo '<option>-- No Locations Available --</option>';
						die();
					}
			} elseif($IDACount==2){
					//State + Location
					$locationID = db::esc($inputDataArr[1]);
					if($campaignDetails){
						if($campaignDetails['restrictCourses']){
							$locations = db::runQuery("select distinct(events.courseID) as CID, courses.websiteName, courses.CourseID from events left join courses on courses.courseID = events.courseID inner join coupon_courserestriction on coupon_courserestriction.courseID = courses.courseID  where events.locationID  = '$locationID' group by events.courseID order by websiteName asc");
								
							//$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
						} else {
							$locations = db::runQuery("select distinct(events.courseID) as CID, courses.websiteName, courses.CourseID from events left join courses on courses.courseID = events.courseID where locationID  = '$locationID' group by events.courseID order by websiteName asc");
						}
					} else {
						$locations = db::runQuery("select distinct(events.courseID) as CID, courses.websiteName, courses.CourseID from events left join courses on courses.courseID = events.courseID where locationID  = '$locationID' group by events.courseID order by websiteName asc");
					}
					if($locations){
						foreach($locations as $location){
							echo '<option value="'.$location['CourseID'].'">'.$location['websiteName'].'</option>';
						}
					} else {
						die("Error.");
					}
			} elseif($IDACount==3){	
					//State + Location + Course
					$locationID = db::esc($inputDataArr[1]);
					$courseID = db::esc($inputDataArr[2]);
					
					$events = db::runQuery("select * from events where courseID = '$courseID' and courseDate >= '".date( 'Y-m-d', strtotime( 'now' ) )."' and locationID = '$locationID' and enrolmentOpen = '1' and active = '1' order by courseDate desc");
					////var_dump($events);
					if($events){
						$eventArray = array();
						//foreach($events as $event){
						//	$eventArray = array_merge(array(array($event['instanceID'] => date( 'd-m-Y', strtotime( $event['courseDate'] ) ))),$eventArray);
						//}
						

						foreach($events as $event){
							//echo $event['courseDate']."<br>\n";
							if(!in_array($event['ID'],$blockedCourses) && !in_array($event['instanceID'],$blockedCoursesByInstanceID) && !checkIfCourseClosed($instances[0]['instanceID'])){
								$eventArray = array_merge(array(array("instanceID" => $event['instanceID'], "instanceDate" => date( 'Y-m-d', strtotime( $event['courseDate'] ) ), date( 'd-m-Y', strtotime( $event['courseDate'] ) ) => true)),$eventArray);	
							}
							
							//$eventArray = array_merge(array( date('Y-m-d',strtotime($event['courseDate'])) => $event['instanceID'] ),$eventArray);
							//$eventArray = array_merge(array(array( "A".$event['instanceID'] => date('Y-m-d',strtotime($event['courseDate'])))    ) ,$eventArray);
						}
						$data = $eventArray; //array("dates" => $eventArray);
					} else {
						$data = array("dates" => false);
					}
					echo json_encode($data);
						die();
			} else {
				
			}
		
	}
	if($app=="getCourseInfo_v2"){
		
			$inputData = $_GET['rd'];
			$inputDataArr = explode("_",$inputData);
			$IDACount = count($inputDataArr);
			
			if(isset($_GET['campaign'])){
				$campaignCode = $_GET['campaign'];
				$campaignDetails = getCampaignDetails($campaignCode);
			} else {
				$campaignCode = false;
				$campaignDetails = false;
			}
			////var_dump($IDACount);
			if($IDACount==1){
					//State Only
					$state = db::esc($inputDataArr[0]);
					if($campaignDetails){
						if($campaignDetails['restrictCourses'] || $campaignDetails['restrictCourses']=="1"){
							//var_dump($campaignDetails);
							$couponID = $campaignDetails['ID'];
							$locations = db::runQuery("select locations.* from locations inner join coupon_locationRestriction on coupon_locationRestriction.locationID = locations.ID  where locationState = '$state' and mergeWithID = '0' and coupon_locationRestriction.couponID = '$couponID' order by LocationName asc");
								//var_dump($locations);
							//$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
							
						} else {
							$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
						}
					} else {
						$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
					}
					if($locations){
						$locCount = count($locations);
						$lastID = "";
						foreach($locations as $location){
							//if(!$lastID==$location['ID']){
								echo '<option value="'.$location['ID'].'" data-loc="'.$locCount.'">'.$location['LocationName'].'</option>';
							//}
							$lastID = $location['ID'];
						}
					} else {
						echo '<option>-- No Locations Available --</option>';
						die();
					}
			} elseif($IDACount==2){
					//State + Location
					$locationID = db::esc($inputDataArr[1]);
					if($campaignDetails){
						if($campaignDetails['restrictCourses']){
							$couponID = $campaignDetails['ID'];
							$locations = db::runQuery("select distinct(events.courseID) as CID, courses.websiteName, courses.CourseID from events left join courses on courses.courseID = events.courseID inner join coupon_courserestriction on coupon_courserestriction.courseID = courses.courseID  where events.locationID  = '$locationID' and courses.deleted = '0' and coupon_courserestriction.couponID = '$couponID' group by events.courseID order by websiteName asc");
							if(!$locations){
								echo '<option value="false">-- No Courses at Location --</option>';
								die();
							}
								//echo "select distinct(events.courseID) as CID, courses.websiteName, courses.CourseID from events left join courses on courses.courseID = events.courseID inner join coupon_courserestriction on coupon_courserestriction.courseID = courses.courseID  where events.locationID  = '$locationID' and courses.deleted = '0' and coupon_courserestriction.couponID = '$couponID' group by events.courseID order by websiteName asc";
							//$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
						} else {
							$locations = db::runQuery("select distinct(events.courseID) as CID, courses.websiteName, courses.CourseID from events left join courses on courses.courseID = events.courseID where locationID  = '$locationID'  and courses.deleted = '0' group by events.courseID order by websiteName asc");
						}
					} else {
						$locations = db::runQuery("select distinct(events.courseID) as CID, courses.websiteName, courses.CourseID from events left join courses on courses.courseID = events.courseID where locationID  = '$locationID'  and courses.deleted = '0' group by events.courseID order by websiteName asc");
					}
					if($locations){
						foreach($locations as $location){
							if($location['deleted']!="1"){
							echo '<option value="'.$location['CourseID'].'">'.$location['websiteName'].'</option>';
							}
						}
					} else {
						die("Error.");
					}
			} elseif($IDACount==3){	
					//State + Location + Course
					$locationID = db::esc($inputDataArr[1]);
					$courseID = db::esc($inputDataArr[2]);
					
					$events = db::runQuery("select * from events where courseID = '$courseID' and courseDate >= '".date( 'Y-m-d', strtotime( 'now' ) )."' and locationID = '$locationID' and enrolmentOpen = '1' and active = '1' order by courseDate desc");
					////var_dump($events);
					if($events){
						$eventArray = array();
						//foreach($events as $event){
						//	$eventArray = array_merge(array(array($event['instanceID'] => date( 'd-m-Y', strtotime( $event['courseDate'] ) ))),$eventArray);
						//}
						

						foreach($events as $event){
							//echo $event['courseDate']."<br>\n";
							//var_dump($event);
							if(!in_array($event['ID'],$blockedCourses) && !in_array($event['instanceID'],$blockedCoursesByInstanceID) && !checkIfCourseClosed($instances[0]['instanceID'])){
								
								$time = ''.date('h:i a',strtotime($event['startDateTime'])).' - '. date('h:i a',strtotime($event['endDateTime']));
								
								//$cost = "$".$event['cost'];
								$courseCostTotal = $event['cost'];
								
								if($campaignDetails){
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
								} else {
									  
										$manualInvoiceEmailPrice = $courseCostTotal;
										$customPriceOption = false;
										$sendManualInvoice = false;
								}
								$cost = "$".$courseCostTotal;
								
								
								
								
								$totalvacant = $event['totalParticipantsVacancy'];
								if($totalvacant>=3){
									$remseats = "3+";
								} else {
									$remseats = $totalvacant;
								}
								
								
								$eventArray = array_merge(array(array("instanceID" => $event['instanceID'], "cost" => $cost, "remseats" => $remseats, "instanceDate" => date( 'Y-m-d', strtotime( $event['courseDate'] ) ), "time" => $time, "date" => date( 'l jS F', strtotime( $event['courseDate'] ) ), "jsDate" => date( 'D M d Y H:i:s O', strtotime( $event['courseDate'] ))  )),$eventArray);	
							}
							
							//$eventArray = array_merge(array( date('Y-m-d',strtotime($event['courseDate'])) => $event['instanceID'] ),$eventArray);
							//$eventArray = array_merge(array(array( "A".$event['instanceID'] => date('Y-m-d',strtotime($event['courseDate'])))    ) ,$eventArray);
						}
						$data = $eventArray; //array("dates" => $eventArray);
					} else {
						$data = array("dates" => false);
					}
					echo json_encode($data);
						die();
			} else {
				
			}
		
	}
	if($app=="checkInstance"){
		$instanceID = db::esc($_GET['instanceID']);
		$couponCode = db::esc($_GET['campaign']);
		$promoname  = "";
		$allowOfflinePayments = "0";
		$forceOfflinePayments = "0";
		$dontInvoiceUser = "0";
		$forceEmployer = "0";
		
		$instances = db::runQuery("select * from events left join courses on courses.courseID = events.courseID left join locations on locations.ID = events.locationID where instanceID = '$instanceID' and enrolmentOpen = '1' and active = '1'");
		if($instances){
					
			$courseCost = $instances[0]['cost'];
			$promocodes = db::runQuery("select * from coupons where couponCode = '$couponCode'");	
			if($promocodes){
				$promocodes = $promocodes[0];
				
				$promoname = $promocodes['campaignName'];
				
				$allowOfflinePayments = $promocodes['allowOfflinePayments'];
				$forceOfflinePayments = $promocodes['forceOfflinePayments'];
				$dontInvoiceUser = $promocodes['dontInvoiceUser'];
				$forceEmployer = $promocodes['employerName'];
				
				
				if($promocodes['discountType']==1){
					//Percent Discount
					$courseCost = $courseCost-($courseCost*("0.".$promocodes['discountAmount']));
					$customPriceOption = false;
				} else if($promocodes['discountType']==2){
						//Custom Price Override - Location
						$customPriceOption = 2;
						$courseCost = courseCustomPriceLookup($customPriceOption,$instanceID,$courseCost,$couponCode);
						
				} else if($promocodes['discountType']==3){	
						//Custom Price Override - Course
						$customPriceOption = 3;
						$courseCost = courseCustomPriceLookup($customPriceOption,$instanceID,$courseCost,$couponCode);
				} else {
					//Dollor Amount Discount
					$courseCost = $courseCost-$promocodes['discountAmount'];
					$customPriceOption = false;
				}
						
			}
			$data = array(
						'websiteName'      => $instances[0]['websiteName'],
						'courseDate'      => date('d M Y',strtotime($instances[0]['courseDate'])),
						'courseTimings'   => ''.date('h:i a',strtotime($instances[0]['startDateTime'])).' - '. date('h:i a',strtotime($instances[0]['endDateTime'])),
						'courseLocation'  => $instances[0]['StreetAddress'],
						'courseTotalCost' => $courseCost,
						'promotion'		  => $promoname,
						'allowOfflinePayments' => $allowOfflinePayments,
						'forceOfflinePayments' => $forceOfflinePayments,
						'dontInvoiceUser' => $dontInvoiceUser,
						'frcEmp'		  => $forceEmployer
											
					);
			if(!in_array($instances[0]['ID'],$blockedCourses) && !in_array($instances[0]['instanceID'],$blockedCoursesByInstanceID) && !checkIfCourseClosed($instances[0]['instanceID'])){
				echo json_encode($data);
			} else {
				echo false;
			}
		} else {
			echo false;
		}
	}

	if($app=="submitEnrolment"){
		
		dlog("Start Enrolment "); 
		
		if(isset($_POST['campaign'])){
			$campaignCode = $_POST['campaign'];
			$campaignDetails = getCampaignDetails($campaignCode);
			$campaignID = $campaignDetails['ID'];
			$campaignName = $campaignDetails['campaignName'];
			
			$allowOfflinePayments = $campaignDetails['allowOfflinePayments'];
			$forceOfflinePayments = $campaignDetails['forceOfflinePayments'];
			$dontInvoiceUser = $campaignDetails['dontInvoiceUser'];
			$addressToInvoice =$campaignDetails['addressToInvoice'];
			
			if($forceOfflinePayments){
				$forceOfflinePayments = true;
			} else {
				$forceOfflinePayments = false;
			}
			
		} else {
			$campaignCode = false;
			$campaignDetails = false;
			$campaignID = 0;
			$campaignName = false;
			
			$allowOfflinePayments = false;
			$forceOfflinePayments = false;
			$dontInvoiceUser = false;
			$addressToInvoice = false;
		}
		
		//if(isset($_SESSION['EnrolLocked'])){
			if($_SESSION['EnrolLocked']==true){
				//echo "false";	
				//die();
			} else {
				$_SESSION['EnrolLocked'] = true;
			}
		//} else {
			$_SESSION['EnrolLocked'] = true;
		//}
		//
		
		$postVar = $_POST;
		
		//var_dump($postVar);
	
		//Course ID from the form
		$courseID                  = $postVar['courseid'];
		$courseDetails             = getEventDetails($courseID);
		//var_dump($courseDetails);
		if($campaignDetails){
			
			//Course Details
			$courseCostTotal       = intval($courseDetails['cost']);
			
			
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
			$manualInvoiceEmailPrice = $courseCostTotal;
		}
		
		//return errorBackResponse("Campaign Details: ".$campaignDetails." -- Course Total: ".$courseCostTotal);
	
		//Contact Details
		$enrolVar['fname']         = $postVar['fname'];    if($enrolVar['fname']==""){ return errorBackResponse("First name cannot be blank"); }
		$enrolVar['lname']         = $postVar['lname'];	   if($enrolVar['lname']==""){ return errorBackResponse("Last name cannot be blank"); }
		$enrolVar['mobile']        = $postVar['mobile'];   if($enrolVar['mobile']==""){ return errorBackResponse("Mobile cannot be blank"); }
		$enrolVar['email']         = $postVar['email'];    if($enrolVar['email']==""){ return errorBackResponse("Email cannot be blank"); }
		$enrolVar['address']       = $postVar['address'];  if($enrolVar['address']==""){ return errorBackResponse("Address cannot be blank"); }
		$enrolVar['suburb']        = $postVar['suburb'];   if($enrolVar['suburb']==""){ return errorBackResponse("Suburb cannot be blank"); }
		$enrolVar['postcode']      = $postVar['postcode']; if($enrolVar['postcode']==""){ return errorBackResponse("postcode cannot be blank"); }
		 
		//Extra Details
		$enrolVar['usi']     	   = $postVar['usi'];
		$enrolVar['workplace']     = $postVar['workplace'];
		$enrolVar['source']        = $postVar['source'];
		$enrolVar['special_needs'] = $postVar['special_needs'];
		
		//Payment Method
		$enrolVar['payment']       = $postVar['payment'];  if($enrolVar['payment']==""){ return errorBackResponse("Payment type cannot be blank"); }
		$enrolVarp['otherPtype']   = $postVar['otherpaymentselection']; 
		
		//Fields for Credit Card
		$enrolVar['cc']            = $postVar['cc'];
		$enrolVar['expiryM']       = $postVar['expiryM'];
		$enrolVar['expiryY']       = substr($postVar['expiryY'],-2,2);
		$enrolVar['cvv']           = $postVar['cvv'];
		
		//Terms & Conditions / mailing opt in
		$enrolVar['opt_in']        = $postVar['opt_in'];
		$enrolVar['terms']         = $postVar['terms'];
		
		$enrolVar['campaign']      = $postVar['campaign'];
		
		//$_SESSION['enrol_ArrayHash'] =  md5(date('r',strtotime('now')));
		
		dlog("Field Pickups "); 
		
		//Create contact in Excelerate for this entry
		if($dontInvoiceUser){
			$contactVars = array(
		                      'givenName'    => $enrolVar['fname'],
		                      'surname'      => $enrolVar['lname'],
		                      'title'        => '',
		                      'emailAddress' => $addressToInvoice,
		                      'mobilephone'  => $enrolVar['mobile'],
		                      'organisation' => $enrolVar['workplace'],
		                      'address1'     => $enrolVar['address'],
		                      'city'         => $enrolVar['suburb'],
		                      'postcode'     => $enrolVar['postcode'],
		                      'USI'		     => $enrolVar['usi'],
		                      
		                   );
			$dontsuppressInvoiceEmail = true;
			dlog("Set: Dont Invoice User "); 
		} else {
			$contactVars = array(
		                      'givenName'    => $enrolVar['fname'],
		                      'surname'      => $enrolVar['lname'],
		                      'title'        => '',
		                      'emailAddress' => $enrolVar['email'],
		                      'mobilephone'  => $enrolVar['mobile'],
		                      'organisation' => $enrolVar['workplace'],
		                      'address1'     => $enrolVar['address'],
		                      'city'         => $enrolVar['suburb'],
		                      'postcode'     => $enrolVar['postcode'],
		                      'USI'		     => $enrolVar['usi'],
		                      
		                   );
			$dontsuppressInvoiceEmail = false;
			dlog("Set: Invoice User "); 
		}		  
		
		$arrayHash = md5(serialize($contactVars));
		
		//couponAddressToInvoice
					  
	    if(!$_SESSION['enrol_Contact'] || !$arrayHash==$_SESSION['enrol_ArrayHash']){
			$enroll      = axcelerate_save_contact($contactVars);
			$_SESSION['enrol_Contact'] = $enroll;
			$_SESSION['enrol_ArrayHash'] = $arrayHash;
			$contactID   = $enroll->CONTACTID;
			$courseInstanceID = $courseID;
			dlog("Enrol-Contact: Created Contact ID ".$contactID);
		} else {
			$enroll      = $_SESSION['enrol_Contact'];
			$contactID   = $enroll->CONTACTID;
			$courseInstanceID = $courseID;
			dlog("Enrol-Contact: Used Session Contact ID ".$contactID);
		}
		
		if($campaignCode){
			if(!$dontsuppressInvoiceEmail){
				$supressInvoiceEmail = "1";
				$generateInvoice = "false";
				$archiveINvoice = "false";
			} else {
				$supressInvoiceEmail = "0";
				$generateInvoice = "true";
				$archiveINvoice = "true";
			}
			if($sendManualInvoice){
				$supressInvoiceEmail = "1";
				$generateInvoice = "false";
				$archiveINvoice = "false";
			}
			$enrollVars  = array(
	      					'contactID'   => $contactID,
	      					'instanceID'  => $courseInstanceID,
	      					'type'        => 'w',
	      					'generateInvoice'=>$generateInvoice,
	      					'suppressEmail'=> $supressInvoiceEmail,
	      					'archiveInvoice'=>$archiveINvoice,
	      					
	          		   );
		} else {
			if($sendManualInvoice){
				$supressInvoiceEmail = "1";
				$generateInvoice = "false";
				$archiveINvoice = "false";
			} else {
				$supressInvoiceEmail = "0";
				$generateInvoice = "true";
				$archiveINvoice = "true";
			}
			$enrollVars  = array(
	      					'contactID'   => $contactID,
	      					'instanceID'  => $courseInstanceID,
	      					'type'        => 'w',
	      					'generateInvoice'=>$generateInvoice,
	      					'suppressEmail'=> $supressInvoiceEmail,
	      					'archiveInvoice'=>$archiveINvoice,
	      					
	          		   );
		}
	
		if($forceOfflinePayments==true && $enrolVar['payment']=="payment-card"){
			//Cancel Booking, Coupon is set to Force Offline Payments
			$enrolmentReturn = array(
									'success' => false,
									'error_message' => "You must use Offline Payments to use this Campaign/Coupon.",
			                   );
							   
			echo json_encode($enrolmentReturn);
			die();
		}

					   
		if($enrolVar['payment']=="payment-card"){
			
			//Credit Card Payments. Process and book.
			if($enrolVar['campaign']=="lesmills"){
				//$courseCostTotal = $courseCostTotal-5;
			}
			
	 		$amount = number_format($courseCostTotal ,2);
			//var_dump($courseCostTotal." -- ".$amount);
	                
		    $data   = array(
					    'txnType' => '0',
					    'txnSource' => 23,
					    'amount' => (int)($amount * 100),
					    'currency' => 'AUD',
					    'purchaseOrderNo' => time(),
					    'CreditCardInfo' => array(
					      'cardNumber' => $enrolVar['cc'],
					      'expiryDate' => $enrolVar['expiryM'].'/'.$enrolVar['expiryY'],
					    ),
					  );
	        //var_dump($data);
	        //$order_id = time();
			//$trans    = uc_nab_transact_charge($order_id, $amount, $data);
			//ob_start();
			//var_dump($trans);
			//$NABTrans_dump = ob_get_clean();
			//var_dump($trans);
	
	        //if($trans['success'] == '1'){
	        	
	        if(!$_SESSION['enrol_Enrolment']){			
		        $enrollNow =  axcelerate_enroll($enrollVars);
		        $_SESSION['enrol_Enrolment'] = $enrollNow;
				dlog("Enrol-Enrolment: Created Enrolment");
			} else {
				$enrollNow =  $_SESSION['enrol_Enrolment'];
				dlog("Enrol-Enrolment: Session Enrolment");
			}
			
			
			$enrollIsError = false;
			if(isset($enrollNow->error)){
				$enrollIsError = true;
				$trans['message'] = $enrollNow->MESSAGES;
				$_SESSION['enrol_Enrolment'] = false;
				dlog("Enrol-Enrolment: Axcelerate returned error: ".$trans['message']);
			}
	        if(!$enrollIsError){
	           	//Payment Successfull.
	           	
	           	
	           	
	           	//$enrollNow =  axcelerate_enroll($enrollVars);
				//var_dump($enrollNow);
				$paymentamount = $courseCostTotal;

				$order_id = time();
				dlog("Start NAB Trans "); 
				$trans    = uc_nab_transact_charge($order_id, $amount, $data);
				dlog("Finish NAB Trans "); 
				$txnID    = $trans['data']['TxnID'][0];
				ob_start();
				var_dump($trans);
				$NABTrans_dump = ob_get_clean();
				//var_dump($trans);
		
		        if($trans['success'] == '1'){
				    $transVars = array(
			                           'amount' => $paymentamount,
			                           'ContactID' => $contactID,
			                           'invoiceID' => $enrollNow->INVOICEID,
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
											'invoice' => $enrollNow->CONTACTID,
					                   );
									   
					$bookingdetails = array (
											'courseID'      => $courseID,
											'instanceID'    => $courseInstanceID,
											'learnerID'     => $enrollNow->LEARNERID,
											'invoiceID'     => $enrollNow->INVOICEID,
											'USI'			=> $enrolVar['usi'],
											'orderID'		=> $order_id,
											'Txn'           => $txnID,
											'contactID'     => $contactID,
											'paymentMethod' => $enrolVar['payment'],
											'campaignID'    => $campaignID,
											'failed'		=> false,
											'dollorAmount'	=> $courseCostTotal,
											'AxTransData'	=> $extrans_dump,
											'NABTransData'	=> $NABTrans_dump,
											'userSource'	=> $enrolVar['source'],
											'specialNeeds'	=> $enrolVar['special_needs'],
											'optInReBook'	=> $enrolVar['opt_in']			
									  );
					logBookingDetails($bookingdetails);	
					
					if($dontInvoiceUser){
							$bookingID = $enrollNow->LEARNERID."-".$contactID;
							email::sendBookingConfirmation($bookingID,$courseInstanceID,$enrolVar,$addressToInvoice);
							if($sendManualInvoice){
								$message = "
									Hello,<br>
									<br>
									Please note the following student has enroled online with the ".$campaignName.".<br>
									As of this they require a <strong>Manual</strong> invoice to be created with below Discount applied.<br>
									<br>
									<h1>Student & Booking Details</h1>
									
									<strong>Invoice ID:</strong> ".$enrollNow->INVOICEID."  <br>
									<strong>Contact ID:</strong>  ".$enrollNow->CONTACTID." <br>
									<strong>Leaner ID:</strong>  ".$enrollNow->LEARNERID." <br>
									<br>
									<strong>Student Name:</strong>  ".$enrolVar['fname']." ".$enrolVar['lname']."   <br>
									<strong>Course Name:</strong> ".$courseDetails['websiteName']."  <br>
									<strong>Course Date:</strong>  ".date('d-m-Y',strtotime($courseDetails['courseDate']))."  <br>
									<strong>Course Time:</strong>  ".date('h:i a',strtotime($courseDetails['startDateTime']))." - ". date('h:i a',strtotime($courseDetails['endDateTime']))."  <br>
									<strong>Course Location:</strong>  ".$courseDetails['LocationName']."  <br><br>
									<strong>Amount Due:</strong>  $".$courseCostTotal." &nbsp;&nbsp;&nbsp;&nbsp;   (<strong>Amount Normally Due:</strong> $".$enrollNow->AMOUNT.")<br><br>
									##########################################<br>
									INVOICE TO BE SENT TO BELOW EMAILS ONLY!<br>
									<br>
									".str_replace(",","<br>",$addressToInvoice)."
									<br>
									<br>
									---------------------------------------------------<br>
									<br>
									";
						
									
									$semail = "noreply@australiawidefirstaid.com.au";
						            $sname = "AWFA - Online Enrolments from Campaign";
									
						            $rname = "";
						            $priority = "high";
						            $type = "text/html";
						            $replysemail = $semail;
									$fullmessage = "";
									
									$rname = "AWFA";
									//$remail = "accounts@australiawidefirstaid.com.au";
									$remail = "accounts@australiawidefirstaid.com.au";
									$remail = "andrew@vbz.com.au";
									$subject = "Campaign Booking - Action Required for Invoicing - ".$campaignName;
						            
						           
									
									esmtp::sendemail_smtp($remail,$subject,$message);
							}
					}

					$_SESSION['enrol_Enrolment'] = false;
					$_SESSION['enrol_Contact'] = false;
				} else {
		            ////// RETURN ERROR ABOUT TRANSACTION FAILING ---------------------------------------------------------------------
		            ////var_dump("----- PAYMENT FAILED -----");
					
					$enrolmentReturn = array(
											'success' => false,
											'error_message' => $trans['message'],
					                   );
									   
					$bookingdetails = array (
										'courseID'      => $courseID,
										'instanceID'    => $courseInstanceID,
										'learnerID'     => '',
										'orderID'		=> $order_id,
										'invoiceID'     => '',
										'USI'			=> $enrolVar['usi'],
										'Txn'           => '',
										'contactID'     => $contactID,
										'paymentMethod' => $enrolVar['payment'],
										'campaignID'    => $campaignID,
										'failed'		=> true,
										'dollorAmount'	=> $courseCostTotal,
										'AxTransData'	=> 'Fail Message to User: '.$trans['message'],
										'NABTransData'	=> $NABTrans_dump,
										'userSource'	=> $enrolVar['source'],
										'specialNeeds'	=> $enrolVar['special_needs'],
										'optInReBook'	=> $enrolVar['opt_in']	
								  );
					logBookingDetails($bookingdetails);
					dlog("Payment Failed:  ".$trans['message']);
					
		        }
				
				
				
											   
				
	        } else {
	            ////// RETURN ERROR ABOUT TRANSACTION FAILING ---------------------------------------------------------------------
	            ////var_dump("----- PAYMENT FAILED -----");
				
				$enrolmentReturn = array(
										'success' => false,
										'error_message' => $trans['message'],
				                   );
								   
				$bookingdetails = array (
									'courseID'      => $courseID,
									'instanceID'    => $courseInstanceID,
									'learnerID'     => '',
									'orderID'		=> $order_id,
									'invoiceID'     => '',
									'USI'			=> $enrolVar['usi'],
									'Txn'           => '',
									'contactID'     => $contactID,
									'paymentMethod' => $enrolVar['payment'],
									'campaignID'    => $campaignID,
									'failed'		=> true,
									'dollorAmount'	=> $courseCostTotal,
									'AxTransData'	=> '',
									'NABTransData'	=> '',
									'userSource'	=> $enrolVar['source'],
									'specialNeeds'	=> $enrolVar['special_needs'],
									'optInReBook'	=> $enrolVar['opt_in']	
							  );
				logBookingDetails($bookingdetails);
				
	        }
			
		} else {
			
			if(!$_SESSION['enrol_Enrolment']){			
		        $enrollNow =  axcelerate_enroll($enrollVars);
		        $_SESSION['enrol_Enrolment'] = $enrollNow;
				dlog("Enrol-Enrolment: Created Enrolment");
			} else {
				$enrollNow =  $_SESSION['enrol_Enrolment'];
				dlog("Enrol-Enrolment: Session Enrolment");
			}
			ob_start();
			var_dump($enrollNow);
			$extrans_dump = ob_get_clean();
			////var_dump($enrollNow);
			$enrolmentReturn = array(
										'success' => true,
										'error_message' => '',
										'txnid' => '',
										'invoice' => $enrollNow->CONTACTID,
				                   );
			
			if($enrolVar['payment']=="payment-other"){
				$paymentMethod = $enrolVarp['otherPtype'];
			} else {
				$paymentMethod = $enrolVar['payment'];
			}
				
			$bookingdetails = array (
									'courseID'      => $courseID,
									'instanceID'    => $courseInstanceID,
									'learnerID'     => '',
									'orderID'		=> '',
									'invoiceID'     => '',
									'USI'			=> $enrolVar['usi'],
									'Txn'           => '',
									'contactID'     => $contactID,
									'paymentMethod' => $paymentMethod,
									'campaignID'    => $campaignID,
									'failed'		=> false,
									'dollorAmount'	=> $courseCostTotal,
									'AxTransData'	=> $extrans_dump,
									'NABTransData'	=> '',
									'userSource'	=> $enrolVar['source'],
									'specialNeeds'	=> $enrolVar['special_needs'],
									'optInReBook'	=> $enrolVar['opt_in']	
							  );
			logBookingDetails($bookingdetails);	
			
			if($dontInvoiceUser){
					$bookingID = $contactID;
					email::sendBookingConfirmation($bookingID,$courseInstanceID,$enrolVar,$addressToInvoice);
					
					//if($sendManualInvoice){
						$message = "
							<div style='font-family: arial, helvetica, sans-serif;'>
							Hello,<br>
							<br>
							Please note the following student has enroled online with the ".$campaignName.".<br>
							As of this they require a <strong>Manual</strong> invoice to be created with below Discount applied.<br>
							<br>
							This is an Employee/Member of ".$campaignName." enrolling, and we need to invoice ".$campaignName." for the below booking.<br>
							The Trainee does not pay for this course online.<br><br>
							<br>
							<h1>Student & Booking Details</h1>
							
							<strong>Learner ID:</strong>  ".$contactID." <br>
							<br>
							<strong>Student Name:</strong>  ".$enrolVar['fname']." ".$enrolVar['lname']."   <br>
							<strong>Course Name:</strong> ".$courseDetails['websiteName']." ( Workshop ID: ".$courseInstanceID." ) <br>
							<strong>Course Date:</strong>  ".date('d-m-Y',strtotime($courseDetails['courseDate']))."  <br>
							<strong>Course Time:</strong>  ".date('h:i a',strtotime($courseDetails['startDateTime']))." - ". date('h:i a',strtotime($courseDetails['endDateTime']))."  <br>
							<strong>Course Location:</strong>  ".$courseDetails['LocationName']."  <br><br>
							<strong>Amount Due:</strong>  $".$manualInvoiceEmailPrice." &nbsp;&nbsp;&nbsp;&nbsp;   (<strong>Amount Normally Due:</strong> $".$enrollNow->AMOUNT.")<br><br>
							##########################################<br>
							INVOICE TO BE SENT TO BELOW EMAILS ONLY!<br>
							<br>
							".str_replace(",","<br>",$addressToInvoice)."
							<br>
							##########################################<br><br>
							<br>
							</div>
							";
				
							
							$semail = "noreply@australiawidefirstaid.com.au";
				            $sname = "AWFA - Online Enrolments from Campaign";
							
				            $rname = "";
				            $priority = "high";
				            $type = "text/html";
				            $replysemail = $semail;
							$fullmessage = "";
							
							$rname = "AWFA";
							//$remail = "accounts@australiawidefirstaid.com.au";
							$remail = "accounts@australiawidefirstaid.com.au";
							
							$subject = "Campaign Booking - Action Required for Invoicing - ".$campaignName;
				            
				           
							
							esmtp::sendemail_smtp($remail,$subject,$message);
					//}
			}
			
			
			$_SESSION['enrol_Enrolment'] = false;
			$_SESSION['enrol_Contact'] = false;
			////var_dump($_POST);					   
			
		}
		if($enrolVar['payment']=="payment-paypal"){
			//PayPal Payments - PENDING
		}
		if($enrolVar['payment']=="payment-ontheday"){
			//On The Day payment. Process and Book. (makr as not paid)
		}
		if($enrolVar['payment']=="payment-other"){
			//Other Payment options -> load from otherPtype variable.
			
			if($enrolVarp['otherPtype']=="Direct Debit"){
				
			}
			if($enrolVarp['otherPtype']=="Send me an Invoice"){
				
			}
			if($enrolVarp['otherPtype']=="Corporate Invoice"){
				
			}
			if($enrolVarp['otherPtype']=="Money Order/Cheque"){
				
			}
		}
		
		echo json_encode($enrolmentReturn);
		
		if($enrolmentReturn->success){
		if($campaignCode){
		$message = "
				Hello,<br>
				<br>
				Please note the following student has enroled online with the ".$campaignName.".<br>
				As of this they require a <strong>Manual</strong> invoice to be created with below Discount applied.<br>
				<br>
				<h1>Student & Booking Details</h1>
				
				<strong>Invoice ID:</strong> ".$enrollNow->INVOICEID."  <br>
				<strong>Contact ID:</strong>  ".$enrollNow->CONTACTID." <br>
				<strong>Leaner ID:</strong>  ".$enrollNow->LEARNERID." <br>
				<br>
				<strong>Student Name:</strong>  ".$enrolVar['fname']." ".$enrolVar['lname']."   <br>
				<strong>Course Name:</strong> ".$courseDetails['websiteName']."  <br>
				<strong>Course Date:</strong>  ".date('d-m-Y',strtotime($courseDetails['courseDate']))."  <br>
				<strong>Course Time:</strong>  ".date('h:i a',strtotime($courseDetails['startDateTime']))." - ". date('h:i a',strtotime($courseDetails['endDateTime']))."  <br>
				<strong>Course Location:</strong>  ".$courseDetails['LocationName']."  <br><br>
				<strong>Amount Due:</strong>  $".$courseCostTotal." &nbsp;&nbsp;&nbsp;&nbsp;   (<strong>Amount Normally Due:</strong> $".$enrollNow->AMOUNT.")
				<br>
				---------------------------------------------------<br>
				<br>
				";
	
				
				$semail = "noreply@australiawidefirstaid.com.au";
	            $sname = "AWFA - Online Enrolments";
				
	            $rname = "";
	            $priority = "high";
	            $type = "text/html";
	            $replysemail = $semail;
				$fullmessage = "";
				
				$rname = "AWFA";
				//$remail = "accounts@australiawidefirstaid.com.au";
				$remail = "accounts@australiawidefirstaid.com.au";
				$subject = "Campaign Booking - Action Required for Invoicing - ".$campaignName;
	            
	           
				
				esmtp::sendemail_smtp($remail,$subject,$message);
				//esmtp::sendemail_smtp("andrew@vbz.com.au",$subject,$message);
				
				//mail($rname."<".$remail.">", $subject, $fullmessage, $header);
		}  
		
		
		
		if(!$enrolVar['special_needs']==""){
		$message = "
				Hello,<br>
				<br>
				Please note the following student has enroled online with <b>Special Needs Instructions</b>.<br>
				<br>
				---------------------------------------------------<br>
				<b>STUDENT SPECIAL NEEDS INSTRUCTIONS</b><br>
				<br>
				".$enrolVar['special_needs']."
				<br>
				---------------------------------------------------<br>
				<br><br>
				<h1>Student & Booking Details</h1>
				
				<strong>Invoice ID:</strong> ".$enrollNow->INVOICEID."  <br>
				<strong>Contact ID:</strong>  ".$enrollNow->CONTACTID." <br>
				<strong>Learner ID:</strong>  ".$enrollNow->LEARNERID." <br>
				<br>
				<strong>Student Name:</strong>  ".$enrolVar['fname']." ".$enrolVar['lname']."   <br>
				<strong>Course Name:</strong> ".$courseDetails['websiteName']."  <br>
				<strong>Course Date:</strong>  ".date('d-m-Y',strtotime($courseDetails['courseDate']))."  <br>
				<strong>Course Time:</strong>  ".date('h:i a',strtotime($courseDetails['startDateTime']))." - ". date('h:i a',strtotime($courseDetails['endDateTime']))."  <br>
				<strong>Course Location:</strong>  ".$courseDetails['LocationName']."  <br><br>
				<strong>Amount Due:</strong>  $".$courseCostTotal." &nbsp;&nbsp;&nbsp;&nbsp;   (<strong>Amount Normally Due:</strong> $".$enrollNow->AMOUNT.")	<br>
				---------------------------------------------------<br>
				<br>
				";
	
				
				$semail = "noreply@australiawidefirstaid.com.au";
	            $sname = "AWFA - Online Enrolments";
				
	            $rname = "";
	            $priority = "high";
	            $type = "text/html";
	            $replysemail = $semail;
				$fullmessage = "";
				
				$rname = "AWFA";
				//$remail = "accounts@australiawidefirstaid.com.au";
				$remail = "accounts@australiawidefirstaid.com.au";
				$subject = "Enrolment Special Needs - REF: #".$enrollNow->LEARNERID;
	            
	           
				
				esmtp::sendemail_smtp($remail,$subject,$message);
				//esmtp::sendemail_smtp("andrew@vbz.com.au",$subject,$message);
				
				//mail($rname."<".$remail.">", $subject, $fullmessage, $header);
		}   
		}
		
				
		//esmtp::sendemail_smtp($remail,$subject,$message);
		//$extrans_dump = "TEST EMAIL FROM WEBSITE - PLEASE IGNORE";
		//esmtp::sendemail_smtp("accounts@australiawidefirstaid.com.au","SYSTEM TEST",$extrans_dump);
	}
	
	$_SESSION['EnrolLocked'] = false;
	/*
	ob_start();
	var_dump($_POST);
	$extrans_dump = ob_get_clean();
	ob_start();
	var_dump($_GET);
	$extrans_dump = $extrans_dump."\n\n". ob_get_clean();
	esmtp::sendemail_smtp("andrew@vbz.com.au","test",$extrans_dump);
	*/
	
	if($app=="vbzaxman_checkInstance"){
		if(isset($_SESSION['vbz_auth_isadmin'])) {
			if($_SESSION['vbz_auth_isadmin']){
				$instanceID = db::esc($_GET['instanceID']);
				$couponCode = db::esc($_GET['campaign']);
				$promoname  = "";
				
				$instances = db::runQuery("select * from events left join courses on courses.courseID = events.courseID left join locations on locations.ID = events.locationID where instanceID = '$instanceID'");
				if($instances){
							
					$courseCost = $instances[0]['cost'];
					$promocodes = db::runQuery("select * from coupons where couponCode = '$couponCode'");	
					if($promocodes){
						$promocodes = $promocodes[0];
						
						$promoname = $promocodes['campaignName'];
						if($promocodes['discountType']==1){
							//Percent Discount
							//Currently Not an Option
							$useCustomPrice = false;
							$customPriceOption = false;
						} else if($promocodes['discountType']==2){
								//Custom Price Override - Location
								$useCustomPrice = true;
								$customPriceOption = 2;
						} else if($promocodes['discountType']==3){	
								//Custom Price Override - Course
								$useCustomPrice = true;
								$customPriceOption = 3;
						} else {
							//Dollor Amount Discount
							$courseCost = $courseCost-$promocodes['discountAmount'];
							$useCustomPrice = false;
							$customPriceOption = false;
						}
					}
					
					$coursePermClosed = db::runQuery("select count(*) as total from eventsClosed where instanceID = '$instanceID'");
					$coursePermClosedCount = $coursePermClosed[0]['total'];
					
					if($coursePermClosedCount){
						$coursePermClosedDetails = db::runQuery("select * from eventsClosed where instanceID = '$instanceID'");
						
						$courseClosedOn = $coursePermClosedDetails[0]['created'];
						$courseClosedBy = $coursePermClosedDetails[0]['user'];
					} else {
						$courseClosedOn = false;
						$courseClosedBy = false;
					}
					
					$data = array(
								'websiteName'      => $instances[0]['websiteName'],
								'courseDate'      => date('d M Y',strtotime($instances[0]['courseDate'])),
								'courseTimings'   => ''.date('h:i a',strtotime($instances[0]['startDateTime'])).' - '. date('h:i a',strtotime($instances[0]['endDateTime'])),
								'courseLocation'  => $instances[0]['StreetAddress'],
								'courseTotalCost' => $courseCost,
								'promotion'		  => $promoname,
								'enrolmentOpen'	  => $instances[0]['enrolmentOpen'],
								'manualClose'	  => $coursePermClosedCount,
								'courseClosedOn'  => $courseClosedOn,
								'courseClosedBy'  => $courseClosedBy
													
							);
					
					echo json_encode($data);
					
				} else {
					echo json_encode(false);
				}
			}
		} else {
			die("Access Denied!");
		}

	}

	if($app=="vbzaxman_closeInstance"){
			if(isset($_SESSION['vbz_auth_isadmin'])) {
				if($_SESSION['vbz_auth_isadmin']){
					
					$instanceID = db::esc($_GET['instanceID']);
					$userName   = db::esc($_SESSION['vbz_auth_username']);
					
					db::insertQuery("insert into eventsClosed (instanceID,user) values('$instanceID','$userName')");
					
					dlog("Course instance closed ($instanceID) by $userName","courseQuickClose");
					
					echo json_encode(true);
				} else {
					echo json_encode(false);
				}
			} else {
				echo json_encode(false);
			}
	}
	
	if($app=="vbzaxman_openInstance"){
			if(isset($_SESSION['vbz_auth_isadmin'])) {
				if($_SESSION['vbz_auth_isadmin']){
					
					$instanceID = db::esc($_GET['instanceID']);
					$userName   = db::esc($_SESSION['vbz_auth_username']);
					
					db::insertQuery("delete from eventsClosed where instanceID = '$instanceID' ");
					
					dlog("Course instance opened ($instanceID) by $userName","courseQuickClose");
					
					echo json_encode(true);
				} else {
					echo json_encode(false);
				}
			} else {
				echo json_encode(false);
			}
	}
	
	if($app=="vbzaxman_getCoupons"){
			if(isset($_SESSION['vbz_auth_isadmin'])) {
				if($_SESSION['vbz_auth_isadmin']){
					
					
					
					$coupons = db::runQuery("Select * from coupons");
					
					$coparr = array();
					$totalRecords = 0;
					
					if($coupons){
								
						foreach($coupons as $coupon){
								
								$couponType = $coupon['discountType'];
								if($couponType==0){
									$couponTypeStr = "$".$coupon['discountAmount']." Discount";
								}else if($couponType==1){
									$couponTypeStr = "".$coupon['discountAmount']."% Discount";
								}else if($couponType==2){
									$couponTypeStr = "Custom Per Location";
								} else {
									$couponTypeStr = "Unknown";
								}
								
								$darr = array(
											'ID'=>$coupon['ID'],
											'Name'=>$coupon['campaignName'],
											'Code'=>$coupon['couponCode'],
											'Type'=>$couponTypeStr,
											'Amount'=>$coupon['discountAmount'],
											'EmployerName'=>$coupon['employerName'],
											'edit'=>'<a href="#" onclick="editCoupon('.$coupon['ID'].');" class="btn btn-block btn-primary">Edit</a>'
										);
										
								$coparr = array_merge($coparr,array($darr));
								$totalRecords++;
						}
						
					}

					$tableData = array(
									'records'=>$coparr,
									'queryRecordCount'=>$totalRecords,
									'totalRecordCount'=>$totalRecords
								 );
					
					//dlog("Course instance closed ($instanceID) by $userName","courseQuickClose");
					header('Content-Type: application/json');
					echo removeBlockQuotes(json_encode($coparr));
				} else {
					echo json_encode(false);
				}
			} else {
				echo json_encode(false);
			}
	}


	if($app=="vbzaxman_getActiveprocessing"){
			if(isset($_SESSION['vbz_auth_isadmin'])) {
				if($_SESSION['vbz_auth_isadmin']){
					
					
					
					$coupons = db::runQuery("Select * from process_group where process_status <> '8' or process_status <> '99'");
					
					$coparr = array();
					$totalRecords = 0;
					
					if($coupons){
								
						foreach($coupons as $coupon){
								
								
								$currentStatus = getStatusIDtoStatusName($coupon['process_status']);
								
								$darr = array(
											'ID'=>$coupon['ID'],
											'TotalTrainees'=>$coupon['total_trainee'],
											'SingleInvoice'=>$coupon['single_invoice'],
											'CampaignCode'=>$coupon['campaign_code'],
											'TotalCost'=>$coupon['totalcost'],
											'PaymentType'=>$coupon['payment_type'],
											'CurrentStatus'=>$currentStatus,
											'view'=>'<a href="#" onclick="viewEnrolment('.$coupon['ID'].');" class="btn btn-block btn-primary">View</a>'
										);
										
								$coparr = array_merge($coparr,array($darr));
								$totalRecords++;
						}
						
					}

					$tableData = array(
									'records'=>$coparr,
									'queryRecordCount'=>$totalRecords,
									'totalRecordCount'=>$totalRecords
								 );
					
					//dlog("Course instance closed ($instanceID) by $userName","courseQuickClose");
					header('Content-Type: application/json');
					echo removeBlockQuotes(json_encode($coparr));
				} else {
					echo json_encode(false);
				}
			} else {
				echo json_encode(false);
			}
	}


	if($app=="getCampaignDetailsPUB"){

					$couponcode = db::esc($_GET['campaign']);
		
					$coupons = db::runQuery("Select * from coupons where couponcode = '$couponcode'",0,1);
					
					$coparr = array();
					$totalRecords = 0;
					
					if($coupons){
								
						foreach($coupons as $coupon){
								
								$couponType = $coupon['discountType'];
								if($couponType==0){
									$couponTypeStr = "$".$coupon['discountAmount']." Discount";
								}else if($couponType==1){
									$couponTypeStr = "".$coupon['discountAmount']."% Discount";
								}else if($couponType==2){
									$couponTypeStr = "Custom Per Location";
								}else if($couponType==3){
									$couponTypeStr = "Custom Per Course";
								} else {
									$couponTypeStr = "Unknown";
								}
								
								$darr = array(
											'ID'=>$coupon['ID'],
											'Name'=>$coupon['campaignName'],
											'Code'=>$coupon['couponCode'],
											'Type'=>$couponTypeStr,
											'Amount'=>$coupon['discountAmount'],
											'EmployerName'=>$coupon['employerName'],
											'defaultLocation'=>$coupon['defaultLocationID'],
										);
										
								$coparr = array_merge($coparr,array($darr));
								$totalRecords++;
						}
						
					}

					
					
					//dlog("Course instance closed ($instanceID) by $userName","courseQuickClose");
					header('Content-Type: application/json');
					echo removeBlockQuotes(json_encode($coparr));
	}

	if($app=="getCourseLocations"){
		
			//$inputDataArr = explode("_",$inputData);
			//$IDACount = count($inputDataArr);
			
			if(isset($_GET['defaultLocation'])){
				$defaultLocation = $_GET['defaultLocation'];
			} else {
				$defaultLocation = false;
			}
			
			if(isset($_GET['campaign'])){
				$campaignCode = $_GET['campaign'];
				$campaignDetails = getCampaignDetails($campaignCode);
			} else {
				$campaignCode = false;
				$campaignDetails = false;
			}

			//State Only
			//$state = db::esc($inputDataArr[0]);
			if($campaignDetails){
				if($campaignDetails['restrictCourses'] || $campaignDetails['restrictCourses']=="1"){
					//var_dump($campaignDetails);
					$couponID = $campaignDetails['ID'];
					$locations = db::runQuery("select locations.* from locations inner join coupon_locationRestriction on coupon_locationRestriction.locationID = locations.ID  where locationState <> '' and mergeWithID = '0' and coupon_locationRestriction.couponID = '$couponID' order by locationState asc, LocationName asc");
						//var_dump($locations);
					//$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
					
				} else {
					$locations = db::runQuery("select * from locations where locationState <> '' and mergeWithID = '0' order by locationState asc, LocationName asc");
				}
			} else {
				$locations = db::runQuery("select * from locations where locationState <> '' and mergeWithID = '0' order by locationState asc, LocationName asc");
			}
			if($locations){
				$locCount = count($locations);
				$lastID = "";
				$lastState = "";
				foreach($locations as $location){
					//if(!$lastID==$location['ID']){
						//echo $location['locationState']."-".$lastState."\n\n";
						if($lastState!=$location['locationState']){
							if($lastState!=""){
								echo "</optgroup>"."\n";
							}
							echo '<optgroup label="'.$location['locationState'].'">'."\n";
  						}
  						if($defaultLocation==$location['ID']){
  							echo '	<option value="'.$location['ID'].'" data-loc="'.$locCount.'" selected>'.$location['LocationName'].'</option>'."\n";
						} else {
							echo '	<option value="'.$location['ID'].'" data-loc="'.$locCount.'">'.$location['LocationName'].'</option>'."\n";	
						}
						
					//}
					$lastID = $location['ID'];
					$lastState = $location['locationState'];
				}
				echo "</optgroup>";
			} else {
				echo '<option>-- No Locations Available --</option>';
				die();
			}
	}
	
	if($app=="vbz_t"){
		
			if(isset($_SESSION['vbz_auth_isadmin'])) {
				if($_SESSION['vbz_auth_isadmin']){
					echo("OK");
					
					//$url = axcelerate_get_url()."/api/users";
	  			//var_dump(postAXData($url,false));
	  			
	  			//var_dump(axcelerate_getinvoice("180939"));
					
				}
				
			}
		
	}
	
	
	/*########################################  MULTI USER SECTION #############################*/
	if($app=="mtest"){
			
	} 
	
	if($app=="checkenrolmentstatus"){
			
			$sessionKey = db::esc($_GET['enrolmentkey']);
			
			$res = db::runQuery("select process_status, created, errorMessage from process_group where processgroupID = '$sessionKey'");
			
			if($res){
				$vres = $res[0];
				
				
				
				if($vres['process_status']==99){
					$result = array(
										'result' => true,
										'sessionKey' => $sessionKey,
										'statuscode' => $vres['process_status'],
										'created'    => $vres['created'],
										'error_message' => $vres['errorMessage']
									);
				} else if($vres['process_status']==8){
					$extDet = db::runQuery("select * from process_group where processgroupID = '$sessionKey'");
					$extDet = $extDet[0];
					$result = array(
										'result' => true,
										'sessionKey' => $sessionKey,
										'statuscode' => $vres['process_status'],
										'created'    => $vres['created'],
										
										'en_courseid' => $extDet['eventID'],
										'txnid' => $extDet['GroupInvoiceID']."-".$extDet['primaryContactID'],
										'invoice' => $extDet['GroupInvoiceID']."-".$extDet['primaryContactID'],
										'en_campaign' => $extDet['campaign_code'],
									);
				} else {
					$result = array(
										'result' => true,
										'sessionKey' => $sessionKey,
										'statuscode' => $vres['process_status'],
										'created'    => $vres['created']
									);
				}
				
		  		echo json_encode($result);
				
			} else {
				$result = array(
									'result' => true,
									'sessionKey' => $groupID,
									'statuscode' => 98
								);
								
		  	echo json_encode($result);
			}
			
			
	} 
	
	if($app=="multienrol"){
		
			  $testdata['usertotal'] = "2";
			
			  $testdata['fname1'] = "Adam 1";
			  $testdata['lname1'] = "Test 1";
			  $testdata['mobile1'] = "0400000000";
			  $testdata['email1'] = "test@example.com";
			  $testdata['address1'] = "123 fake st";
			  $testdata['suburb1'] = "faketown";
			  $testdata['postcode1'] = "5555";
			  $testdata['usi1'] = "1325456";
			  $testdata['workplace1'] = "Smiths";
			  $testdata['special_needs1'] = "";
			  
			  $testdata['fname2'] = "Adam 2";
			  $testdata['lname2'] = "Test 2";
			  $testdata['mobile2'] = "0499999999";
			  $testdata['email2'] = "test2@example.com";
			  $testdata['address2'] = "555 fake st";
			  $testdata['suburb2'] = "Faketownship";
			  $testdata['postcode2'] = "9999";
			  $testdata['usi2'] = "45654654";
			  $testdata['workplace2'] = "Smiths";
			  $testdata['special_needs2'] = "";
			  
			  $testdata['courseid'] = "9999999";
			  
			  $testdata['campaign'] = "vbztest";
			  $testdata['cc'] = "1234123412341234";
			  $testdata['cvv'] = "123";
			  $testdata['expiryM'] = "04";
			  $testdata['expiryY'] = "2019";
			  $testdata['payment'] = "credit-card";
			  $testdata['source'] = "Google";
			  $testdata['opt_in'] = "1";
			  $testdata['terms'] = "1";
			  

			
			$postVar = $_POST;
			//var_dump($postVar);
			//$postVar = $testdata;
			
			$enrolVar = "";
			$eventID = $postVar['courseid'];
			
			if(isset($_SESSION['groupID'])){
				$groupID = db::esc($_SESSION['groupID']);
				
				//$rescheck = db::insertQuery("delete from |process_trainee| where processgroupID = '$groupID'");
			} else {
				$groupID = hash("sha512",strtotime("now").session_id(), false);
				$_SESSION['groupID'] = $groupID;
			}
			
			$currentuser = 1;
			$totalUsers = $postVar['usertotal'];
			$currentCost = $postVar['crq'];
			
			$s_costperuser = $currentCost / $totalUsers;
			
			while($totalUsers >= $currentuser){
			// Get data from user, add to DB and return statusKey to check against.
				$enrolVar["fname".$currentuser]         = db::esc($postVar["fname".$currentuser]); 
				$enrolVar["lname".$currentuser]         = db::esc($postVar["lname".$currentuser]);
				$enrolVar["mobile".$currentuser]        = db::esc($postVar["mobile".$currentuser]);
				$enrolVar["email".$currentuser]         = db::esc($postVar["email".$currentuser]);
				$enrolVar["address".$currentuser]       = db::esc($postVar["address".$currentuser]);
				$enrolVar["suburb".$currentuser]        = db::esc($postVar["suburb".$currentuser]);
				$enrolVar["postcode".$currentuser]      = db::esc($postVar["postcode".$currentuser]);
				$enrolVar["usi".$currentuser]     	    = db::esc($postVar["usi".$currentuser]);
				$enrolVar["workplace".$currentuser]     = db::esc($postVar["workplace".$currentuser]);
				$enrolVar["special_needs".$currentuser] = db::esc($postVar["special_needs".$currentuser]);
				
				
				$userData = array(
											'processgroupID' => $groupID,
											'eventID'				 => $eventID,
											'firstname'			 => $enrolVar["fname".$currentuser],
											'lastname'			 => $enrolVar["lname".$currentuser],
											'mobile'				 => $enrolVar["mobile".$currentuser],
											'email'					 => $enrolVar["email".$currentuser],
											'address' 			 => $enrolVar["address".$currentuser],
											'suburb' 			 	 => $enrolVar["suburb".$currentuser],
											'postcode'			 => $enrolVar["postcode".$currentuser],
											'usi'						 => $enrolVar["usi".$currentuser],
											'workplace'			 => $enrolVar["workplace".$currentuser],
											'additionalinfo' => $enrolVar["special_needs".$currentuser],
											'cost'           => $s_costperuser
										);
				$lookupData = array(
											'processgroupID' => $groupID,
											'eventID'				 => $eventID,
											'firstname'			 => $enrolVar["fname".$currentuser],
											'lastname'			 => $enrolVar["lname".$currentuser]
										);
				
				$insertData = arrtosqltable($userData);
				$selectData = arrtosqltable_select($lookupData);
				$updateData = arrtosqltable_update($userData);
				
				$contactcheck = db::runQuery("select * from |process_trainee| $selectData");
				if($contactcheck){
					$fname = db::esc($enrolVar["fname".$currentuser]);
					$lname = db::esc($enrolVar["lname".$currentuser]);
					db::insertQuery("update |process_trainee| $updateData where processgroupID = '$groupID' and firstname = '$fname' and lastname = '$lname'");
				} else {
					db::insertQuery("insert into |process_trainee| $insertData");
				}
				
				
				
				
				$currentuser++;
			}
			
			$enrolVar['source']        = $postVar['source'];
			
			//Payment Method
			$enrolVar['payment']       = $postVar['payment'];
			$enrolVarp['otherPtype']   = $postVar['otherpaymentselection']; 
			
			//Fields for Credit Card
			$enrolVar['cc']            = $postVar['cc'];
			$enrolVar['expiryM']       = $postVar['expiryM'];
			$enrolVar['expiryY']       = substr($postVar['expiryY'],-2,2);
			$enrolVar['cvv']           = $postVar['cvv'];
			
			//Terms & Conditions / mailing opt in
			$enrolVar['opt_in']        = $postVar['opt_in'];
			$enrolVar['terms']         = $postVar['terms'];
			
			$enrolVar['campaign']      = $postVar['campaign'];
			
			//$enrolVar['singleinvoice'] = $postVar['singleinvoice'];
			
			
			$groupdata = array(
												'processgroupID' 	=> $groupID,
												'eventID'				 	=> $eventID,
												'total_trainee' 	=> $totalUsers,
												'single_invoice' 	=> 0,
												'campaign_code' 	=> $enrolVar['campaign'],
												'cc_num' 					=> $enrolVar['cc'],
												'cc_ccv' 					=> $enrolVar['cvv'],
												'cc_month'				=> $enrolVar['expiryM'],
												'cc_year' 				=> $enrolVar['expiryY'],
												'payment_type'		=> $enrolVar['payment'],
												'userSource' 			=> $enrolVar['source'],
												'optinRebook' 		=> $enrolVar['opt_in'],
												'terms_agree'			=> $enrolVar['terms'],
												'process_status'  => 0
									 );
			
			$hasGroup = db::runQuery("select count(*) as total from process_group where processgroupID = '$groupID'");
			if($hasGroup[0]['total']==0){
				$insertData = arrtosqltable($groupdata);
				db::insertQuery("insert into |process_group| $insertData");
			} else {
				$insertData = arrtosqltable_update($groupdata);
				db::insertQuery("update |process_group| ".$insertData." where processgroupID = '".$groupID."'");
			}
				
			$result = array(
									'result' => true,
									'sessionKey' => $groupID
								);
								
		  echo json_encode($result);
		  
		  
		
			//Create contacts for each user
			
			//Submit to /course/enrolMultiple
				//with contact ID's in array
		
	}
	
	
?>