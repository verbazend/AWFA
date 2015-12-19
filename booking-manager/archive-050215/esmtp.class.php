<?php

class esmtp  {
	
	
	public static function sendemail_smtp($to,$subject,$message){
		require 'mailer/PHPMailerAutoload.php';
		
	    $mail = new PHPMailer;
	    
	    $mail->isSMTP();                                      // Set mailer to use SMTP
	    $mail->Host = '54.206.5.241';  // Specify main and backup server
	    $mail->SMTPAuth = false;                               // Enable SMTP authentication
	    //$mail->Username = '';                            // SMTP username
	    //$mail->Password = '';                           // SMTP password
	    //$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
	    
	    $mail->From = 'noreply@australiawidefirstaid.com.au';
	    $mail->FromName = 'AWFA Booking Manager';
	    $mail->addAddress($to, '');  // Add a recipient
	    
	    $mail->addReplyTo('accounts@australiawidefirstaid.com.au', 'Australia Wide First Aid');
	    //$mail->addCC('cc@example.com');
	    $mail->addBCC('andrew@vbz.com.au');
	    
	    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	    $mail->isHTML(true);                                  // Set email format to HTML
	    
	    $mail->Subject = $subject;
	    $mail->Body    = $message;
	    $mail->AltBody = $message;
	    
	    if(!$mail->send()) {
	       //echo 'Message could not be sent.';
	       //echo 'Mailer Error: ' . $mail->ErrorInfo;
	       //exit;
	    }
	    
	    //echo 'Message has been sent';
	
	}
	
	

}