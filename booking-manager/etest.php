<?php
include("application.php");
include("db.class.php");
include("functions.php");
include("email.class.php");
include("esmtp.class.php");



	function axcelerate_testlogin( $loginVars ) {
	
	  $url = axcelerate_get_url()."/api/user/login";
	  return postAXData($url,$loginVars);
	
	}
	
	function axcelerate_createuser( $loginVars ) {
	
	  $url = axcelerate_get_url()."/api/user";
	  return postAXData($url,$loginVars);
	
	}
	
	function axcelerate_changepassword( $loginVars ) {
	
	  $url = axcelerate_get_url()."/api/user/changePassword";
	  return postAXData($url,$loginVars);
	
	}
	
	function axcelerate_testcontact( $contactVars ) {
	
	  $url = axcelerate_get_url()."/api/contact";
	  return postAXData($url,$contactVars);
	
	}
	
	function axcelerate_testenrolment( $contactVars ) {
	
	  $url = axcelerate_get_url()."/api/course/enrol";
	  return postAXData($url,$contactVars);
	
	}
	
echo "A";
	//$html = getEmailTemplate("template_invoicenotification.htm");
	//echo $html;
	
	//$totalusers = "2";
	//$totalcost = "5.6";
	
	//echo $totalcost / $totalusers;
	
	//
	
	$userDetails = array(
											'givenName' => 'Andrew',
											'surname' => 'James',
											'title' => 'Mr',
											'emailAddress' => 'andrew@vbz.com.au',
											'organisation' => 'Verbazend',
											'Password' => 'abc123'
									);
	
	//$result = axcelerate_testcontact($userDetails);
	
	$userDetails = array(
											'contactID' => '2707364',
											'username' => 'andrew.james',
											'password' => 'abc123'
									);
	//$result = axcelerate_createuser($userDetails);								
	//$result = axcelerate_testcontact($userDetails);
	
	//object(stdClass)#1 (3) { ["USERID"]=> int(95545) ["CONTACTID"]=> int(2707364) ["USERNAME"]=> string(17) "andrew@vbz.com.au" }
	/*
	*/
	$userDetails = array(
											'contactID' => '2707583',
											'instanceID' => '242498',
											'type' => 'w',
											//'emailAddress' => 'andrew@vbz.com.au',
											//'organisation' => 'Verbazend',
											//'Password' => 'abc123'
									);
	//$result = axcelerate_testenrolment($userDetails);							
	
	$userDetails = array(
										'username' => 'andrew@vbz.com.au',
										'oldPassword' => 'abc123',
										'newPassword' => '123abc',
										'verifyPassword' => '123abc',
									);
									
	//$result = axcelerate_changepassword($userDetails);							
	
	$userDetails = array(
										'username' => 'andrew@vbz.com.au',
										'password' => '123abc'
									);
	
	//$result = axcelerate_testlogin($userDetails);							
	
	//var_dump($result);

die();

$html = getEmailTemplate("template_Invoice_generic.htm");

$iD = axcelerate_getInvoice("158631");
//var_dump($iD);
//var_dump(getBookingDetailsFromInvoiceID("158626"));
//die();

$courseDetailObj = getBookingDetailsFromInvoiceID("158631");

$courseDetails['name'] 		= $courseDetailObj['CourseName'];
$courseDetails['date']		= date("l",strtotime($courseDetailObj['courseDate']))."<br>".date("j",strtotime($courseDetailObj['courseDate']))."<sup>".date("S",strtotime($courseDetailObj['courseDate']))."</sup> ".date("F Y",strtotime($courseDetailObj['courseDate']));
$courseDetails['date_nb']		= date("l",strtotime($courseDetailObj['courseDate']))." ".date("j",strtotime($courseDetailObj['courseDate']))."<sup>".date("S",strtotime($courseDetailObj['courseDate']))."</sup> ".date("F Y",strtotime($courseDetailObj['courseDate']));
$courseDetails['time']		= date("g:i a",strtotime($courseDetailObj['startDateTime']))." to ".date("g:i a",strtotime($courseDetailObj['endDateTime']));
$courseDetails['location']	= str_replace(", ","<br>",str_replace("\n","<br>",$courseDetailObj['StreetAddress']));
$courseDetails['maplink']	= "https://www.google.com.au/maps?q=".str_replace(" ","+",str_replace("\n"," ",$courseDetailObj['StreetAddress']));

$lookup="";
$value="";
$resource = $html;
$resource = varReplace("InvoiceID",$iD->INVOICEID,$resource);
$resource = varReplace("BookingID",$iD->CONTACTID." - ".$iD->CONTACTID,$resource);

$resource = varReplace("InvoiceDate",$iD->INVOICEDATE,$resource);
$resource = varReplaceDF("order.purchaseorder",$iD->ORDERNR,$resource,"- No PO Number");
$resource = varReplaceBR("user.orginisation",$iD->ORGANISATION,$resource);
$resource = varReplaceBR("user.street1",$iD->SHIPSTREET,$resource);
$resource = varReplaceBR("user.street2",$iD->SHIPADDRESS2,$resource);
$resource = varReplaceSP("user.suburb",$iD->SHIPCITY,$resource);
$resource = varReplaceSP("user.state",$iD->SHIPSTATE,$resource);
$resource = varReplaceSP("user.postcode",$iD->SHIPPOSTCODE,$resource);

$resource = varReplace("user.firstname",$iD->FIRSTNAME,$resource);
$resource = varReplace("user.lastname",$iD->LASTNAME,$resource);
$resource = varReplace("user.phone",$iD->PHONENR,$resource);
$resource = varReplace("user.email",$iD->EMAIL,$resource);

$userAddress = "{user.street1}{user.street2}{user.suburb}{user.state}{user.postcode}";
$userAddress = varReplaceBR("user.street1",$iD->SHIPADDRESS1,$userAddress);
$userAddress = varReplaceBR("user.street2",$iD->SHIPADDRESS2,$userAddress);
$userAddress = varReplaceSP("user.suburb",$iD->SHIPCITY,$userAddress);
$userAddress = varReplaceSP("user.state",$iD->SHIPSTATE,$userAddress);
$userAddress = varReplaceSP("user.postcode",$iD->SHIPPOSTCODE,$userAddress);
$resource = varReplace("user.address",$userAddress,$resource);


$resource = varReplace("course.name",$courseDetails['name'],$resource);
$resource = varReplace("course.date",$courseDetails['date'],$resource);
$resource = varReplace("course.time",$courseDetails['time'],$resource);
$resource = varReplace("course.location",$courseDetails['location'],$resource);
$resource = varReplace("course.maplink",$courseDetails['maplink'],$resource);
$resource = varReplace("course.date_nb",$courseDetails['date_nb'],$resource);

$resource = varReplace("order.referanceID",base64_encode($iD->INVOICEID),$resource);


$resource = varReplace("item.description",$courseDetails['name']."<br>[".$iD->ITEMS[0]->ITEMCODE."]",$resource);
$resource = varReplace("item.totalGST","0.00",$resource);
$resource = varReplace("item.Total",$courseDetailObj['dollorAmount'].".00",$resource);
$resource = varReplace("order.totalGST","0.00",$resource);
$resource = varReplace("order.Total",$courseDetailObj['dollorAmount'].".00",$resource);

if($iD->ISPAID){
	$resource = varReplace("order.amountPaid",$courseDetailObj['dollorAmount'].".00",$resource);
	$resource = varReplace("order.balanceDue","0.00",$resource);
} else {
	$resource = varReplace("order.amountPaid","0.00",$resource);
	$resource = varReplace("order.balanceDue",$courseDetailObj['dollorAmount'].".00",$resource);
}


$resource = varReplace("order.lastname",$iD->LASTNAME,$resource);
$resource = varReplace("order.orderID",$iD->INVOICEID,$resource);





//echo $resource;

$message = "
				Hello,<br>
				<br>
				Please note the following student has enroled online with the Les Mills Campaign.<br>
				As of this they require a <strong>Manual</strong> invoice to be created with a $5 Discount applied as they have chosen to pay Offline.<br>
				<br>
				<h1>Student & Booking Details</h1>
				

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
				$remail = "andrew@vbz.com.au";
				$subject = "Les Mills Booking - Action Required for Invoicing";
	            

				
				esmtp::sendemail_smtp($remail,$subject,$resource);
				
				?>
				