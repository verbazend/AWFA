<?php

function axcelerate_booking_forms($step){
	if($step=="enrolment"){
		axcelerate_process_enrolment();
	} else {
     	// print $step;
     	$cssPath = drupal_get_path('module','axcelerate').'/css/axcelerate.css';
     	drupal_add_css($cssPath);
  
     	return theme('booking_page_theme',array('step' => $step,'form' => drupal_get_form('axcelerate_booking_form_step_'.$step)));
     	//return 'sadfasd';
	}
    
}

function axcelerate_booking_form_step_1(){
$form = array();
//print_r($_SESSION['axcelerate']);
$form['fname'] = array(
  '#type' => 'textfield', 
  '#title' => t('First Name'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['fname'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
    
    
    $form['lname'] = array(
  '#type' => 'textfield', 
  '#title' => t('Last Name'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['lname'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
    
        $form['mobile'] = array(
  '#type' => 'textfield', 
  '#title' => t('Mobile No'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['mobile'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
        
    $form['email'] = array(
  '#type' => 'textfield', 
  '#title' => t('Email'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['email'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
    
          $form['workplace'] = array(
  '#type' => 'textfield', 
  '#title' => t('Workplace'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['workplace'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
  
  
          $form['address'] = array(
  '#type' => 'textfield', 
  '#title' => t('Address'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['address'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);

        $form['suburb'] = array(
  '#type' => 'textfield', 
  '#title' => t('Suburb'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['suburb'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);  

        $form['postcode'] = array(
  '#type' => 'textfield', 
  '#title' => t('Postcode'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['postcode'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);

$form['agree'] = array(
  '#type' =>'checkbox',
  '#default_value' => (isset($_SESSION['axcelerate']['agree']) ? $_SESSION['axcelerate']['agree'] : ''), 
'#required' => TRUE,
  '#title' => t('I agree to the Terms & Conditions'),

);

$form['submit'] = array('#type' => 'submit', '#value' => t('Submit'));

 //   $form['redirect'] = 'axcelerate/book/2';
    
  return $form;  
}



    function axcelerate_booking_form_step_1_validate($form, &$form_state){
$vals = $form_state['values'];
   // print_r($vals);
    if(!validate_email($vals['email'])){
 form_set_error('email','The Email Address is not valid') ;
    }
    
     
  //  exit;
    }

function validate_email($e){
    return (bool)preg_match("`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim($e));
}
function axcelerate_booking_form_step_1_submit($form, &$form_state){
    
    
    
    
    $vals = $form_state['values'];
    unset($vals['submit']);
     unset($vals['form_build_id']);
      unset($vals['form_token']);
       unset($vals['form_id']);
        unset($vals['op']);
        if(!isset($_SESSION['axcelerate'])){
         $_SESSION['axcelerate'] = array();   
        }
      $_SESSION['axcelerate'] = array_merge($_SESSION['axcelerate'],$vals);  
  //  print_r($vals);
   // exit;
    drupal_goto('axcelerate/book/3');
    
}


function axcelerate_booking_form_step_2(){
    
    $form = array();

$form['fname'] = array(
  '#type' => 'textfield', 
  '#title' => t('First Name'), 
  '#default_value' => (isset($_SESSION['axcelerate']['fname']) ? $_SESSION['axcelerate']['fname'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
    
    
    $form['lname'] = array(
  '#type' => 'textfield', 
  '#title' => t('Last Name'), 
  '#default_value' => (isset($_SESSION['axcelerate']['lname']) ? $_SESSION['axcelerate']['lname'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
    
        $form['mobile'] = array(
  '#type' => 'textfield', 
  '#title' => t('Mobile No'), 
  '#default_value' => (isset($_SESSION['axcelerate']['mobile']) ? $_SESSION['axcelerate']['mobile'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
        
    $form['email'] = array(
  '#type' => 'textfield', 
  '#title' => t('Email'), 
  '#default_value' => (isset($_SESSION['axcelerate']['email']) ? $_SESSION['axcelerate']['email'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
    
          $form['address'] = array(
  '#type' => 'textfield', 
  '#title' => t('Address'), 
  '#default_value' => (isset($_SESSION['axcelerate']['address']) ? $_SESSION['axcelerate']['address'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);

        $form['suburb'] = array(
  '#type' => 'textfield', 
  '#title' => t('Suburb'), 
  '#default_value' => (isset($_SESSION['axcelerate']['suburb']) ? $_SESSION['axcelerate']['suburb'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);  

        $form['postcode'] = array(
  '#type' => 'textfield', 
  '#title' => t('Postcode'), 
  '#default_value' => (isset($_SESSION['axcelerate']['postcode']) ? $_SESSION['axcelerate']['postcode'] : ''), 
  '#size' => 60, 
  '#maxlength' => 128, 
  '#required' => TRUE,
);
    
$form['back'] = array('#type' => 'submit', '#value' => t('Back'));
$form['submit'] = array('#type' => 'submit', '#value' => t('Confirm'));
    
   return $form; 
    
    
    
}


function axcelerate_booking_form_step_2_submit($form, &$form_state){
   // print_r($form_state);
   // exit;
    $vals = $form_state['values'];
    unset($vals['submit']);
     unset($vals['form_build_id']);
      unset($vals['form_token']);
       unset($vals['form_id']);
        unset($vals['op']);
        if(!isset($_SESSION['axcelerate'])){
         $_SESSION['axcelerate'] = array();   
        }
      $_SESSION['axcelerate'] = array_merge($_SESSION['axcelerate'],$vals);  
        $contactVars = array(
                      'givenName'=> $_SESSION['axcelerate']['fname'],
                      'surname'=>$_SESSION['axcelerate']['lname'],
                      'title'=>'Mr',
                      'emailAddress'=> $_SESSION['axcelerate']['email'],
                      );
    
//$rData = axcelerate_save_contact($contactVars); 
  //  print_r($rData);
  //  exit;
  switch($form_state['clicked_button']['#value']){
    case 'Back':
        drupal_goto('axcelerate/book/1');
        break;
    case 'Confirm':
        drupal_goto('axcelerate/book/3');  
        break;
    
    
  }
}


function axcelerate_booking_form_step_3(){
    $form = array();
    
    $method = 0;
   $method = (isset($_SESSION['axcelerate']['payment']) ? $_SESSION['axcelerate']['payment'] : 0);
    
    
           if($method == 1){
        $display = "block";
       }else{
        $display = "none";
       }
    
       $form['payment'] = array(
       '#type' => 'select',
       '#title' => t('Payment Method'),
       '#options' => array(
         0 => t('Please Select'),
         1 => t('Credit Card'),
         2 => t('Invoice'),
       ),
       '#required' => TRUE,
       '#default_value' => $method,
       
   );
       
    $form['cc-details'] = array(
  '#type' => 'fieldset', 
  '#title' => t('Credit Card Details'),  
  '#collapsible' => FALSE, 
  '#collapsed' => FALSE,
    '#attributes' => array('style' => 'display:'.$display) 
);
    
    $form['cc-details']['name'] = array(
  '#type' => 'textfield', 
  '#title' => t('Name on Card.'), 
  '#default_value' => '', 
  '#size' => 60, 
  '#maxlength' => 128, 
);
    
        $form['cc-details']['cc'] = array(
  '#type' => 'textfield', 
  '#title' => t('credit card number.'), 
  '#default_value' => '', 
  '#size' => 60, 
  '#maxlength' => 128, 
);
       
         $expiryMMonths = array(
    '01' => '01 - January',
    '02' => '02 - February',
    '03' => '03 - March',
    '04' => '04 - April',
    '05' => '05 - May',
    '06' => '06 - June',
    '07' => '07 - July',
    '08' => '08 - August',
    '09' => '09 - September',
    '10' => '10 - October',
    '11' => '11 - November',
    '12' => '12 - December',
  );

  //print_r($expiryMMonths);
  
  
  $form['cc-details']['expiryM'] = array(
       '#type' => 'select',
       '#title' => t('Expiry Month'),
       '#options' => $expiryMMonths,
       '#default_value' => '',
   );
  
  for($i=date('y');$i<=date('y')+8;$i++){
  $expiryYArray[$i] = ($i + 2000);
  }
  
    $form['cc-details']['expiryY'] = array(
       '#type' => 'select',
       '#title' => t('Expiry Year'),
       '#options' => $expiryYArray,
       '#default_value' => '',
   );
       
     
             $form['cc-details']['cvv'] = array(
  '#type' => 'textfield', 
  '#title' => t('CVV.'), 
  '#default_value' => '', 
  '#size' => 60, 
  '#maxlength' => 128, 
);
             
             
          if($method == 2){
        $display = "block";
       }else{
        $display = "none";
       }
   $form['invoice-details'] = array(
  '#type' => 'fieldset', 
  '#title' => t('Invoice'),  
  '#collapsible' => FALSE, 
  '#collapsed' => FALSE,
 '#attributes' => array('style' => 'display:'.$display) 
);
   

   
   
   $form['invoice-details']['contact_information'] = array(
'#markup' => t('Please note invoice can take 24-48 hours to arrive.<br>

Note: Certificates will not be issued untill full payment has been received.'),

);
             
             
       
       $form['submit'] = array('#type' => 'submit', '#value' => t('Confirm'));
       
    return $form;

}


function axcelerate_booking_form_step_5(){
     $form = array();
     $form['invoice-details']['contact_information'] = array(
'#markup' => t('<h2>Thank you for your Booking</h2>


An email has been sent to you with your confirmed booking details. Contact us on 1300 336 613 if you require any changes to your booking or personal information..'),

);  
  
  return $form;  
}

function axcelerate_booking_form_step_3_validate($form, &$form_state){
    $vals = $form_state['values'];
    $_SESSION['axcelerate']['payment'] = $vals['payment'];
    //print_r($vals);
   // exit;
    switch($vals['payment']){
        case 0:
            // They didnt select anything, Naughty Naughy
            form_set_error('payment','You must select a payment method.');  
            break;
        case 1:
            $errors  = 0;
            // They are paying with credit card
           // print_r($vals);
            if($vals['name'] == ''){
              form_set_error('name','You Must provide the name on the card.');
              $errors++;
            }
            
                        if($vals['cc'] == ''){
              form_set_error('cc','You Must provide the credit card number.');
              $errors++;
            }
            
                        if($vals['cvv'] == ''){
              form_set_error('cvv','You Must provide the CVV number.');
              $errors++;
            }
            if($errors == 0){
               
           // print_r($trans);
            
            }
            
            
            break;
        case 2:
            // They would like in invoice, Ta.
            break;
        
    }
    
    
   // print_r($vals);
   // exit;
    //drupal_goto('axcelerate/book/3');  
}


function axcelerate_booking_form_step_3_submit($form, &$form_state){
        $vals = $form_state['values'];
    unset($vals['submit']);
     unset($vals['form_build_id']);
      unset($vals['form_token']);
       unset($vals['form_id']);
        unset($vals['op']);
        if(!isset($_SESSION['axcelerate'])){
         $_SESSION['axcelerate'] = array();   
        }
      $_SESSION['axcelerate'] = array_merge($_SESSION['axcelerate'],$vals); 
    
    drupal_goto('axcelerate/book/4');  
}
function axcelerate_booking_form_step_4(){
    $form = array();
 $form['submit'] = array('#type' => 'submit', '#value' => t('Confirm'));
 return $form;
    
}


function axcelerate_booking_form_step_4_validate($form, &$form_state){
    
    
    if($_SESSION['axcelerate']['payment'] == '1'){
$node = node_load($_SESSION['axcelerate']['courseid']);
 $amount = number_format($node->field_cost['und'][0]['value'] ,2);
                
    $data = array(
    'txnType' => '0',
    'txnSource' => 23,
    'amount' => (int)($amount * 100),
    'currency' => 'AUD',
    'purchaseOrderNo' => time(),
    'CreditCardInfo' => array(
      'cardNumber' => $_SESSION['axcelerate']['cc'],
      'expiryDate' => $_SESSION['axcelerate']['expiryM'].'/'.$_SESSION['axcelerate']['expiryY'],
    ),
  );
                
                $order_id = time();
            $trans = uc_nab_transact_charge($order_id, $amount, $data);
            if($trans['success'] == '1'){
               
            }else{
                $message = ' - <a href="https://www.australiawidefirstaid.com.au/booking-new/axcelerate/book/3">Click Here</a> to correct.';
              form_set_error('form',$trans['message'].$message);   
            }
    }

}


function axcelerate_booking_form_step_4_submit($form, &$form_state){
    
    // Lets Create an account for them in accellerate
                        $contactVars = array(
                      'givenName'=>$_SESSION['axcelerate']['fname'],
                      'surname'=>$_SESSION['axcelerate']['lname'],
                      'title'=>'mr',
                      'emailAddress'=>$_SESSION['axcelerate']['email'],
                      'mobilephone' => $_SESSION['axcelerate']['mobile'],
                      'organisation' => $_SESSION['axcelerate']['workplace'],
                      'address1' =>$_SESSION['axcelerate']['address'],
                      'city' =>$_SESSION['axcelerate']['suburb'],
                      'postcode'=>$_SESSION['axcelerate']['postcode'],
                      );
    
$enroll = axcelerate_save_contact($contactVars);
print_r($enroll);
$contactID = $enroll->CONTACTID;



print $contactID;
                    $enrollVars = array(
                    'contactID' => $contactID,
                    'instanceID' => $_SESSION['axcelerate']['course_instance'],
                    'type' => 'w',
                    );
    
            switch($_SESSION['axcelerate']['payment']){
                case '1':
                    // User has paid with credit Card
               // $enrollVars['SuppressEmail'] = 'TRUE';
                print_r($enrollVars);
                $enrollNow =  axcelerate_enroll($enrollVars);
                   print_r($enrollNow); 
                    
                    $transVars = array(
                                       'amount' => $enrollNow->AMOUNT,
                                       'ContactID' => $contactID,
                                       'invoiceID' => $enrollNow->INVOICEID,
                                       );
                    print_r($transVars);
                   $trans =  axcelerate_transact($transVars);
                    print_r($trans);
                    
//exit; 
                    break;
                    case 2:
                    $enrollNow =  axcelerate_enroll($enrollVars);
                 //   print_r($enrollNow);
                    break;
                
            }
      drupal_goto('axcelerate/book/5'); 
   
}


function check_email_address($email) {
  // First, we check that there's one @ symbol, 
  // and that the lengths are right.
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters 
    // in one section or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
    if
(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
?'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
$local_array[$i])) {
      return false;
    }
  }
  // Check if domain is IP. If not, 
  // it should be valid domain name
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if
(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
?([A-Za-z0-9]+))$",
$domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}



?>