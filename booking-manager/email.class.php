<?php
class email  {


			
	public function __construct()
    {
        //var_dump(date("Y"));
		//die();
		
		//DB Configuration
		
		
		
    }
    
	function sendBookingConfirmation_noinvoice($bookingID,$instanceID,$enrolVar,$ccEmail=false){
	
			$enrolVar['state'] = getStateFromPostcode($enrolVar['postcode']);
		
			//$instanceID = "161835";
			
			$courseDetailObj = getEventDetails($instanceID);
			
			$courseDetails['name'] 		= $courseDetailObj['websiteName'];
			$courseDetails['date']		= date("l",strtotime($courseDetailObj['courseDate']))."<br>".date("j",strtotime($courseDetailObj['courseDate']))."<sup>".date("S",strtotime($courseDetailObj['courseDate']))."</sup> ".date("F Y",strtotime($courseDetailObj['courseDate']));
			$courseDetails['date_nb']		= date("l",strtotime($courseDetailObj['courseDate']))." ".date("j",strtotime($courseDetailObj['courseDate']))."<sup>".date("S",strtotime($courseDetailObj['courseDate']))."</sup> ".date("F Y",strtotime($courseDetailObj['courseDate']));
			$courseDetails['time']		= date("g:i a",strtotime($courseDetailObj['startDateTime']))." to ".date("g:i a",strtotime($courseDetailObj['endDateTime']));
			$courseDetails['location']	= str_replace(", ","<br>",str_replace("\n","<br>",$courseDetailObj['StreetAddress']));
			$courseDetails['maplink']	= "https://www.google.com.au/maps?q=".str_replace(" ","+",str_replace("\n"," ",$courseDetailObj['StreetAddress']));
			
			
			//var_dump($courseDetails);
			//die();
			
			$emailKey = sha1(date("r",strtotime("now")).generateRandomString(5).$bookingID.$instanceID);
			
			//$bookingID = "123456789-123456789";
			$viewonlinelink = "https://www.australiawidefirstaid.com.au/email/?k=".$emailKey;
			
			$livedata = $enrolVar['fname']." ".$enrolVar['lname']."|".$enrolVar['email']."|".$bookingID;
			
			$livechatURL = "https://www.australiawidefirstaid.com.au/?livechat=true&data=".base64_encode($livedata);
					
	
				$html = getEmailTemplate("template_CouponEmailConfirmation_generic.htm");
				
				$html = varReplace("bookingID",$bookingID,$html);
				$html = varReplace("emailviewlink",$viewonlinelink,$html);
				$html = varReplace("livechatURL",$livechatURL,$html);
				
				$html = varReplace("couponCompany",$enrolVar['couponCompany'],$html);
				
				$html = varReplace("user.firstname",$enrolVar['fname'],$html);
				$html = varReplace("user.lastname",$enrolVar['lname'],$html);
				$html = varReplace("user.address",$enrolVar['address'],$html);
				$html = varReplace("user.suburb",$enrolVar['suburb'],$html);
				$html = varReplace("user.state",$enrolVar['state'],$html);
				$html = varReplace("user.postcode",$enrolVar['postcode'],$html);
				$html = varReplace("user.phone",$enrolVar['mobile'],$html);
				$html = varReplace("user.email",$enrolVar['email'],$html);
				$html = varReplace("user.orginisation",$enrolVar['workplace'],$html);
				
				$html = varReplace("course.name",$courseDetails['name'],$html);
				$html = varReplace("course.date",$courseDetails['date'],$html);
				$html = varReplace("course.date_nb",$courseDetails['date_nb'],$html);
				$html = varReplace("course.time",$courseDetails['time'],$html);
				$html = varReplace("course.location",$courseDetails['location'],$html);
				$html = varReplace("course.maplink",$courseDetails['maplink'],$html);
				
				

				
				$message = $html;
				
				$semail = "bookings@australiawidefirstaid.com.au";
	            $sname = "Australia Wide First Aid";
				
	            $rname = "";
	            $priority = "high";
	            $type = "text/html";
	            $replysemail = $semail;
				$fullmessage = "";
				
				
				//$remail = "accounts@australiawidefirstaid.com.au";
				$remail = $enrolVar['email'];
				$subject = "First Aid Course Booking Confirmation - ".$enrolVar['fname']." ".$enrolVar['lname']." - ".$enrolVar['workplace'];
	            
				email::logEmail($remail,"",$ccEmail,$subjet,$message,$bookingID,$instanceID,$emailKey);
				
				esmtp::sendemail_smtp($remail,$subject,$message,$ccEmail);
	
	}

	function sendBookingConfirmation($bookingID,$instanceID,$enrolVar,$ccEmail=false){
	
			$enrolVar['state'] = getStateFromPostcode($enrolVar['postcode']);
		
			//$instanceID = "161835";
			
			$courseDetailObj = getEventDetails($instanceID);
			
			$courseDetails['name'] 		= $courseDetailObj['websiteName'];
			$courseDetails['date']		= date("l",strtotime($courseDetailObj['courseDate']))."<br>".date("j",strtotime($courseDetailObj['courseDate']))."<sup>".date("S",strtotime($courseDetailObj['courseDate']))."</sup> ".date("F Y",strtotime($courseDetailObj['courseDate']));
			$courseDetails['date_nb']		= date("l",strtotime($courseDetailObj['courseDate']))." ".date("j",strtotime($courseDetailObj['courseDate']))."<sup>".date("S",strtotime($courseDetailObj['courseDate']))."</sup> ".date("F Y",strtotime($courseDetailObj['courseDate']));
			$courseDetails['time']		= date("g:i a",strtotime($courseDetailObj['startDateTime']))." to ".date("g:i a",strtotime($courseDetailObj['endDateTime']));
			$courseDetails['location']	= str_replace(", ","<br>",str_replace("\n","<br>",$courseDetailObj['StreetAddress']));
			$courseDetails['maplink']	= "https://www.google.com.au/maps?q=".str_replace(" ","+",str_replace("\n"," ",$courseDetailObj['StreetAddress']));
			
			
			//var_dump($courseDetails);
			//die();
			
			$emailKey = sha1(date("r",strtotime("now")).generateRandomString(5).$bookingID.$instanceID);
			
			//$bookingID = "123456789-123456789";
			$viewonlinelink = "https://www.australiawidefirstaid.com.au/email/?k=".$emailKey;
			
			$livedata = $enrolVar['fname']." ".$enrolVar['lname']."|".$enrolVar['email']."|".$bookingID;
			
			$livechatURL = "https://www.australiawidefirstaid.com.au/?livechat=true&data=".base64_encode($livedata);
					
	
				$html = getEmailTemplate("template_CouponEmailConfirmation_generic.htm");
				
				$html = varReplace("bookingID",$bookingID,$html);
				$html = varReplace("emailviewlink",$viewonlinelink,$html);
				$html = varReplace("livechatURL",$livechatURL,$html);
				
				
				$html = varReplace("user.firstname",$enrolVar['fname'],$html);
				$html = varReplace("user.lastname",$enrolVar['lname'],$html);
				$html = varReplace("user.address",$enrolVar['address'],$html);
				$html = varReplace("user.suburb",$enrolVar['suburb'],$html);
				$html = varReplace("user.state",$enrolVar['state'],$html);
				$html = varReplace("user.postcode",$enrolVar['postcode'],$html);
				$html = varReplace("user.phone",$enrolVar['mobile'],$html);
				$html = varReplace("user.email",$enrolVar['email'],$html);
				$html = varReplace("user.orginisation",$enrolVar['workplace'],$html);
				
				$html = varReplace("course.name",$courseDetails['name'],$html);
				$html = varReplace("course.date",$courseDetails['date'],$html);
				$html = varReplace("course.date_nb",$courseDetails['date_nb'],$html);
				$html = varReplace("course.time",$courseDetails['time'],$html);
				$html = varReplace("course.location",$courseDetails['location'],$html);
				$html = varReplace("course.maplink",$courseDetails['maplink'],$html);
				
				

				
				$message = $html;
				
				$semail = "bookings@australiawidefirstaid.com.au";
	            $sname = "Australia Wide First Aid";
				
	            $rname = "";
	            $priority = "high";
	            $type = "text/html";
	            $replysemail = $semail;
				$fullmessage = "";
				
				
				//$remail = "accounts@australiawidefirstaid.com.au";
				$remail = $enrolVar['email'];
				$subject = "First Aid Course Booking Confirmation - ".$enrolVar['fname']." ".$enrolVar['lname']." - ".$enrolVar['workplace'];
	            
				email::logEmail($remail,"",$ccEmail,$subjet,$message,$bookingID,$instanceID,$emailKey);
				
				esmtp::sendemail_smtp($remail,$subject,$message,$ccEmail);
	
	}
    
    function logEmail($toAddress,$ccAddress,$bccAddress,$subjet,$message,$bookingID,$instanceID,$emailKey=false){
    	$returnValue = false;
		
    	if($emailKey=="" || $emailKey==false){
			$emailKey = sha1(date("r",strtotime("now")).generateRandomString(20));
			$returnValue = true;
		}
		
		$toAddress  = db::esc($toAddress);
		$ccAddress  = db::esc($ccAddress);
		$bccAddress = db::esc($bccAddress);
		$subjet     = db::esc($subjet);
		$message    = db::esc($message);
		$bookingID  = db::esc($bookingID);
		$instanceID = db::esc($instanceID);
		$emailKey   = db::esc($emailKey);
		
		db::insertQuery("insert into emails (toAddress,ccAddress,bccAddress,subject,body,bookingID,instanceID,emailKey) values('$toAddress','$ccAddress','$bccAddress','$subjet','$message','$bookingID','$instanceID','$emailKey')");
		
		if($returnValue){
			return $emailKey;
		} else {
			return true;
		}
    }
	
	function getEmailByHashID($hashid,$emailConfirmation){
		$hashid = db::esc($hashid);
		$emailConfirmation = db::esc($emailConfirmation);
		
		$email = db::runQuery("select * from emails where toAddress = '$emailConfirmation' and emailKey = '$hashid'");
		if($email){
			$email = $email[0];
			return $email;
		} else {
			return false;
		}
		
	}
	
	
}
?>