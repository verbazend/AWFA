<?php

class esmtp  {
	
	
	public static function sendemail_smtp($to,$subject,$message,$emailcc = false){
		require_once 'mailer/PHPMailerAutoload.php';
		
		$rand = md5($tot.$subject.$message.$emailcc.strtotime("now"));
		
	    $mail[$rand] = new PHPMailer;
	    
	    $mail[$rand]->isSMTP();                                      // Set mailer to use SMTP
	    $mail[$rand]->Host = 'email-smtp.us-east-1.amazonaws.com';  // Specify main and backup server
	    $mail[$rand]->SMTPAuth = true;                               // Enable SMTP authentication
	    $mail[$rand]->Username = 'AKIAJJK5T5D7OHSJ2GXA';                            // SMTP username
	    $mail[$rand]->Password = 'Ah2O3wObw80nOOTNKg5uzzuH0SuIMmGwNACgNsNJoob/';                           // SMTP password
	    $mail[$rand]->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
	    
	    $mail[$rand]->From = 'noreply@australiawidefirstaid.com.au';
	    $mail[$rand]->FromName = 'Australia Wide First Aid';
	    $mail[$rand]->addAddress($to, '');  // Add a recipient
	    
	    $mail[$rand]->addReplyTo('bookings@australiawidefirstaid.com.au', 'Australia Wide First Aid');
	    if($emailcc){
		    	$mail[$rand]->addBCC($emailcc);
			}
	    $mail[$rand]->addBCC('andrew@vbz.com.au');
	    
	    $mail[$rand]->WordWrap = 50;                                 // Set word wrap to 50 characters
	    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	    $mail[$rand]->isHTML(true);                                  // Set email format to HTML
	    
	    $mail[$rand]->Subject = $subject;
	    $mail[$rand]->Body    = $message;
	    $mail[$rand]->AltBody = $message;
	    
	    if(!$mail[$rand]->send()) {
	       //echo 'Message could not be sent.';
	       //echo 'Mailer Error: ' . $mail[$rand]->ErrorInfo;
	       //var_dump($mail[$rand]);
	       exit;
	    }
	    unset($mail[$rand]);
	    //echo 'Message has been sent';
	
	}
	
	

}