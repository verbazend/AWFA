<?php

include("application.php");
include("db.class.php");
include("functions.php");
include("esmtp.class.php");

dlog("Load ");


$blockedCourses 		    = array('1052','517','1690','1780','1053','518');
$blockedCoursesByInstanceID = array('154569','155759','155762','132187','133296','132096','132063','153925','143974','152656','133049','153930','153925','132160','133273','132162','133276','132263','132807','137230','137241','132940','133005','141628','141785','152608','137054','137151','137054','137151');
	
	$app = $_GET['rq'];

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
							$locations = db::runQuery("select distinct(events.courseID) as CID, courses.CourseName, courses.CourseID from events left join courses on courses.courseID = events.courseID inner join coupon_courserestriction on coupon_courserestriction.courseID = courses.courseID  where events.locationID  = '$locationID' group by events.courseID order by CourseName asc");
								
							//$locations = db::runQuery("select * from locations where locationState = '$state' and mergeWithID = '0' order by LocationName asc");
						} else {
							$locations = db::runQuery("select distinct(events.courseID) as CID, courses.CourseName, courses.CourseID from events left join courses on courses.courseID = events.courseID where locationID  = '$locationID' group by events.courseID order by CourseName asc");
						}
					} else {
						$locations = db::runQuery("select distinct(events.courseID) as CID, courses.CourseName, courses.CourseID from events left join courses on courses.courseID = events.courseID where locationID  = '$locationID' group by events.courseID order by CourseName asc");
					}
					if($locations){
						foreach($locations as $location){
							echo '<option value="'.$location['CourseID'].'">'.$location['CourseName'].'</option>';
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
					//Currently Not an Option
					$courseCost = $courseCost-($courseCost*("0.".$promocodes['discountAmount']));
				} else {
					//Dollor Amount Discount
					$courseCost = $courseCost-$promocodes['discountAmount'];
				}
			}
			$data = array(
						'courseName'      => $instances[0]['CourseName'],
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
		
		if(isset($_POST['campaign'])){
			$campaignCode = $_POST['campaign'];
			$campaignDetails = getCampaignDetails($campaignCode);
			$campaignID = $campaignDetails['ID'];
		} else {
			$campaignCode = false;
			$campaignDetails = false;
			$campaignID = 0;
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
				//Currently Not an Option
				$courseCostTotal = $courseCostTotal-($courseCostTotal*("0.".$campaignDetails['discountAmount']));
			} else {
				//Dollor Amount Discount
				$courseCostTotal = $courseCostTotal-$campaignDetails['discountAmount'];
			}
		} else {
			//Course Details
			$courseCostTotal           = intval($courseDetails['cost']);
		}
	
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
		
		
		
		//Create contact in Excelerate for this entry
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
					  
		$arrayHash = md5(serialize($contactVars));
					  
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
			$supressInvoiceEmail = "1";
			$enrollVars  = array(
	      					'contactID'   => $contactID,
	      					'instanceID'  => $courseInstanceID,
	      					'type'        => 'w',
	      					'suppressEmail'=> $supressInvoiceEmail
	          		   );
		} else {
			$enrollVars  = array(
	      					'contactID'   => $contactID,
	      					'instanceID'  => $courseInstanceID,
	      					'type'        => 'w'
	          		   );
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
				$trans    = uc_nab_transact_charge($order_id, $amount, $data);
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
										'AxTransData'	=> '',
										'NABTransData'	=> '',
										'userSource'	=> $enrolVar['source'],
										'specialNeeds'	=> $enrolVar['special_needs'],
										'optInReBook'	=> $enrolVar['opt_in']	
								  );
					logBookingDetails($bookingdetails);
					
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
				Please note the following student has enroled online with the Les Mills Campaign.<br>
				As of this they require a <strong>Manual</strong> invoice to be created with a $5 Discount applied.<br>
				<br>
				<h1>Student & Booking Details</h1>
				
				<strong>Invoice ID:</strong> ".$enrollNow->INVOICEID."  <br>
				<strong>Contact ID:</strong>  ".$enrollNow->CONTACTID." <br>
				<strong>Leaner ID:</strong>  ".$enrollNow->LEARNERID." <br>
				<br>
				<strong>Student Name:</strong>  ".$enrolVar['fname']." ".$enrolVar['lname']."   <br>
				<strong>Course Name:</strong> ".$courseDetails['CourseName']."  <br>
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
				$subject = "Les Mills Booking - Action Required for Invoicing";
	            
	           
				
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
				<strong>Course Name:</strong> ".$courseDetails['CourseName']."  <br>
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
						} else {
							//Dollor Amount Discount
							$courseCost = $courseCost-$promocodes['discountAmount'];
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
								'courseName'      => $instances[0]['CourseName'],
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
	
?>