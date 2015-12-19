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
	
	function dlog($message, $vtype = false){
		//return true;
		if(!$vtype=="sync"){
			return true;
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
		
		$ins = db::insertQuery("insert into log (type, message, sessionData, postData, getData) values('$vtype','$message','$sessionData','$postData','$getData')");
		
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
		$campaign = db::runQuery("select * from events left join courses on courses.courseID = events.courseID left join locations on events.locationID = locations.ID where events.instanceID = '$courseID'");
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
	
	    $response = curl_exec( $ch );
	    curl_close( $ch );
	  //}
	
	  // Include the XML parser for PHP 4 compatibility.
	  //module_load_include('php', 'uc_store', 'includes/simplexml');
	
	  // Create the XML object and parse the response string.
	  $xml = simplexml_load_string( $response );
	  //print_r($xml);
	  // Check to make sure the response parses and payment passed properly.
	  if ( isset( $xml->Status->statusCode ) && $xml->Status->statusCode != '000' ) {
	    $approval = 'No';
	    $responsecode = $xml->Status->statusCode;
	    $responsetext = $xml->Status->statusDescription;
	  }
	  elseif ( isset( $xml->Payment->TxnList->Txn->approved ) ) {
	    $approval = $xml->Payment->TxnList->Txn->approved;
	    $responsecode = $xml->Payment->TxnList->Txn->responseCode;
	    $responsetext = $xml->Payment->TxnList->Txn->responseText;
	    $charged = $xml->Payment->TxnList->Txn->amount / 100;
	    $txnid = $xml->Payment->TxnList->Txn->txnID;
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
	  }
	
	  if ( $approval != 'Yes' ) {
	    $message = t( 'Credit card declined: !amount', array( '!amount' => $amount ) );
	
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
	
	    $result = array(
	      'success' => TRUE,
	      'comment' => t( 'NAB Transact Txn ID: @txnid<br/>Approval code: @code', array( '@txnid' => $txnid, '@code' => $responsecode ) ),
	      'message' => t( 'NAB Transact Txn ID: @txnid<br/>Approval code: @code', array( '@txnid' => $txnid, '@code' => $responsecode ) ),
	      'data' => array( 'TxnID' => $txnid ),
	      'uid' => $user->uid,
	    );
	  }
	
	  $message .= '<br />'. t( 'Response code: @code - @text', array( '@code' => $responsecode, '@text' => $responsetext ) );
	
	
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
	
	
?>