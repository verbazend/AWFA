<?php

function returnquote($address,$weight,$vol,$settings,$items) { 
     
   $quotes = getQuotes($address,$weight,$vol,$settings,$items);

   if (!isset($quotes['error'])) {
   
		 		foreach ($quotes as $quote) {
	            
	                    if ($quote['cost'] > 0) {
	                                        
	                        $postmethod = strtolower($quote['type']);
	
	                        $quote_data['startrack_' . $postmethod] = array(
	                            'code' => 'startrack.startrack_' . $postmethod,
	                            'title' => $quote['description'],
	                            'cost' => $quote['cost'],
	                            'tax_class_id' => '',
	                            'text' => ''
	                        );
	                        	                        
	                    }
	                }
	            
	} else {
	
		$error = $quotes['error'];
				
	}
      		
	return $quote_data;

}


function getQuotes($address, $weight, $vol, $settings, $items) {

        require_once('eServices.php');
        
		$pluginpath = dirname(dirname(__FILE__));

		$connection = array(	
							'username' => $settings['startrackapiusername'],
							'password' => $settings['startrackapipassword'],
							'userAccessKey' => $settings['startrackapikey'],
							'wsdlFilespec' => $pluginpath . '/startrack/eServicesProductionWSDL.xml'
		);
		
		
		if($address['country']=='AU') {

	        $parameters = array(
	            'header' => array(
	                'source' => 'TEAM',
	                'accountNo' => $settings['startrackapiaccnum'],
	                'userAccessKey' => $settings['startrackapikey']
	            ),
	            'senderLocation' => array(
	                'suburb' => $settings['origincity'],
	                'postCode' => $settings['originpostcode'],
	                'state' => $settings['originstate']
	            ),
	            'receiverLocation' => array(
	                'suburb' => $address['suburb'],
	                'postCode' => $address['postcode'],
	                'state' => strtoupper($address['state'])
	            ),            
	            'noOfItems' => $items,                                     
	            'weight' => $weight,
	            'volume' => round($vol,3),
	            'includeTransitWarranty' => 0,
	            'includeFuelSurcharge' => 0,
	            'includeSecuritySurcharge' => 0,
	        );
	        	          
		    $parameters += array('transitWarrantyValue' => 100);
			
	        $startrack_quote = array();
			
			$servicecodes[0] = 'EXP';
		
		}
		
		if($address['country']=='NZ') {

			$address['state'] = 'AUCKLAND';
			$address['postcode'] = 9901;
		
/*			if ($address['state'] == 'AK') {
				$address['state'] = 'AUCKLAND';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'NL') {
				$address['state'] = 'NORTHLAND';
				$address['postcode'] = 9901;
			}
			
			if ($address['state'] == 'WA') {
				$address['state'] = 'WAIKATO';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'BP') {
				$address['state'] = 'BAY OF PLENTY';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'TK') {
				$address['state'] = 'TARANAKI';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'HB') {
				$address['state'] = 'HAWKES BAY';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'MW') {
				$address['state'] = 'MANAWATU-WANGANUI';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'WE') {
				$address['state'] = 'WELLINGTON';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'NS') {
				$address['state'] = 'NELSON';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'MB') {
				$address['state'] = 'MARLBOROUGH';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'TM') {
				$address['state'] = 'TASMAN';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'WC') {
				$address['state'] = 'WEST COAST';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'CT') {
				$address['state'] = 'CANTERBURY';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'OT') {
				$address['state'] = 'OTAGO';
				$address['postcode'] = 9901;
			}

			if ($address['state'] == 'SL') {
				$address['state'] = 'SOUTHLAND';
				$address['postcode'] = 9901;
			}
*/
	        $parameters = array(
	            'header' => array(
	                'source' => 'TEAM',
	                'accountNo' => $settings['startrackapiaccnum'],
	                'userAccessKey' => $settings['startrackapikey']
	            ),
	            'senderLocation' => array(
	                'suburb' => $settings['origincity'],
	                'postCode' => $settings['originpostcode'],
	                'state' => $settings['originstate']
	            ),
	            'receiverLocation' => array(
	                'suburb' => $address['state'] . ' (NZ)',
	                'postCode' => $address['postcode'],
	                'state' => 'INT'
	            ),            
	            'noOfItems' => $items,                                     
	            'weight' => ceil($weight),
	            'volume' => round(($width/100) * ($height/100) * ($length/100), 3),
	            'includeTransitWarranty' => 0,
	            'includeFuelSurcharge' => 0,
	            'includeSecuritySurcharge' => 0,
	        );
	          
		    $parameters += array('transitWarrantyValue' => 100);
			
	        $startrack_quote = array();
			
			$servicecodes[0] = 'ITL';
		
		}		
		
		
		foreach ($servicecodes as $service_code) {
			$parameters['serviceCode'] = strtoupper($service_code);		
        	$request = array('parameters' => $parameters);

	        try {
	        
				$oC = new STEeService();
	            $response = $oC->invokeWebService($connection,'calculateCost', $request);            
	            
				if ($response->cost == 0.0) {
					continue;
				}
								
													
	            $totalCostExGST = $response->cost;
				if ($settings['fuelsurcharge']) $totalCostExGST += $response->fuelSurcharge;
				if ($settings['riskwarranty']) $totalCostExGST += $response->transitWarrantyCharge;
				if ($settings['securitysurcharge']) $totalCostExGST += $response->securitySurcharge;

				if ($settings['handlingcharge']) {
					
					if ($settings['handlingchargetype']==0) {
						$totalCostExGST += $settings['handlingcharge'];	
					} else {
						$totalCostExGST += $totalCostExGST * ($settings['handlingcharge']/100);							
					}
					
				} 			
				$totalCostInGST = $totalCostExGST + $response->gstCharge;

				$desctext['text_title']            = 'StarTrack Express';
				$desctext['text_exp']           	= 'StarTrack Express - Road Express';
				$desctext['text_1kn']           	= 'StarTrack Express - 1kg Nationwide';
				$desctext['text_3kn']           	= 'StarTrack Express - 3kg Nationwide';
				$desctext['text_5kn']           	= 'StarTrack Express - 5kg Nationwide';
				$desctext['text_tse']           	= 'StarTrack Express - Trade Show Express';
				$desctext['text_ret']           	= 'StarTrack Express - Road Express T\'Gate';
				$desctext['text_re2']           	= 'StarTrack Express - Road Express 2Men';
				$desctext['text_itl']           	= 'StarTrack Express - Int\'l Express Freight';
				$desctext['text_lo2']           	= 'StarTrack Express - Local Overnight 2Men';
				$desctext['text_lot']           	= 'StarTrack Express - Local Overnight Tail';
				$desctext['text_pac']           	= 'StarTrack Express - Priority Air Service';
				$desctext['text_sat']           	= 'StarTrack Express - Saturday Delivery';
				$desctext['text_sda']           	= 'StarTrack Express - Sameday StarTrack CR';
				$desctext['text_ids']           	= 'StarTrack Express - Int\'l Document Express';
				$desctext['text_error_mismatch']   = 'The Suburb and Post Code entered do not match!';
	
				if($settings['handlegstlocally']==1) {
					$service['cost'] = $totalCostExGST;
				} else {
					$service['cost'] = $totalCostInGST;
				}
	
				$service['description'] = $desctext['text_' . strtolower($service_code)];
				$service['type'] = $service_code;
	            $startrack_quote[] = $service;		        
	
	        } catch (SoapFault $e) {			
	            if (isset($e->detail->fault->fs_message)) {      	
					$startrack_quote['error1'] = $e->faultstring;
	            	$startrack_quote['error'] = $e->detail->fault->fs_message;

				} else {
	            	$startrack_quote['error1'] = $e->detail->fault->fs_message;
					$startrack_quote['error'] = $e->faultstring;
				}
				
				break;
	        }
        }

        return $startrack_quote;
}

?>