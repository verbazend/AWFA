<?php

/**
 * WC_StarTrack class.
 *
 * @extends WC_Shipping_Method
 */

class WC_StarTrack extends WC_Shipping_Method {

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */

		public function __construct() {
				
			$this->id = 'star_track';
			$this->method_title = __('StarTrack Express', 'woocommerce');
			$this->title = "StarTrack Express";
			$this->enabled = true;
			$this->init();
        }

	    /**
	     * init function.
	     *
	     * @access public
	     * @return void
	     */

        private function init() {
            $this->init_form_fields();
            $this->init_settings();

            $this->enabled      = $this->settings['enabled'];
            $this->availability = $this->settings['availability'];
            $this->startrackapiusername    = $this->settings['startrackapiusername'];
            $this->startrackapipassword    = $this->settings['startrackapipassword'];
            $this->startrackapikey    = $this->settings['startrackapikey'];
            $this->startrackapiaccnum    = $this->settings['startrackapiaccnum'];
            $this->origincity    = $this->settings['origincity'];
            $this->originstate    = $this->settings['originstate'];
            $this->originpostcode    = $this->settings['originpostcode'];
            $this->riskwarranty    = $this->settings['riskwarranty'];
            $this->fuelsurcharge    = $this->settings['fuelsurcharge'];
            $this->securitysurcharge    = $this->settings['securitysurcharge'];
            $this->handlingchargetype    = $this->settings['handlingchargetype'];
            $this->handlingcharge    = $this->settings['handlingcharge'];
            $this->handlegstlocally    = $this->settings['handlegstlocally'];

            add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_options'));
        }


		/**
		 * init_form_fields function.
		 *
		 * @access public
		 * @return void
		 */
		public function init_form_fields() {

            global $woocommerce;

            $this->form_fields = array(
                'enabled'      => array(
                    'title'            => __('Enable/Disable', 'woocommerce'),
                    'type'             => 'checkbox',
                    'label'            => __('Enabled', 'woocommerce'),
                    'default'          => 'yes'
                ),
                'availability' => array(
                    'title'            => __('Method availability', 'woocommerce'),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'availability',
                    'options'          => array(
                        'all'          => __('All allowed countries', 'woocommerce'),
                        'specific'     => __('Specific Countries', 'woocommerce')
                    )
                ),
                'countries'    => array(
                    'title'            => __('Specific Countries', 'woocommerce'),
                    'type'             => 'multiselect',
                    'class'            => 'chosen_select',
                    'css'              => 'width: 450px;',
                    'default'          => '',
                    'options'          => $woocommerce->countries->countries
                ),
                'startrackapiusername'    => array(
                    'title'            => __('StarTrack API Username', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'apifield',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),
                'startrackapipassword'    => array(
                    'title'            => __('StarTrack API Password', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'apifield',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),
                'startrackapikey'    => array(
                    'title'            => __('StarTrack API Key', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'apifield',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),
                'startrackapiaccnum'    => array(
                    'title'            => __('StarTrack Account Number', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'apifield',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),

                'origincity'    => array(
                    'title'            => __('Origin City', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'originfield',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),

                'originstate'    => array(
                    'title'            => __('Origin State (e.g. NSW, VIC)', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'originfield',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),
                'originpostcode'    => array(
                    'title'            => __('Origin Postcode', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'originfield',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),
                'riskwarranty' => array(
                    'title'            => __('Risk Warranty', 'woocommerce'),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'options',
                    'options'          => array(
                        '0'          => __('No', 'woocommerce'),
                        '1'     => __('Yes', 'woocommerce')
                    )
                ),
                'fuelsurcharge' => array(
                    'title'            => __('Fuel Surcharge', 'woocommerce'),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'options',
                    'options'          => array(
                        '0'          => __('No', 'woocommerce'),
                        '1'     => __('Yes', 'woocommerce')
                    )
                ),
                'securitysurcharge' => array(
                    'title'            => __('Security Surcharge', 'woocommerce'),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'options',
                    'options'          => array(
                        '0'          => __('No', 'woocommerce'),
                        '1'     => __('Yes', 'woocommerce')
                    )
                ),
                'handlingchargetype' => array(
                    'title'            => __('Handling Charge Type', 'woocommerce'),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'options',
                    'options'          => array(
                        '0'          => __('Flat Fee', 'woocommerce'),
                        '1'     => __('Percentage of StarTrack Shipping Cost', 'woocommerce')
                    )
                ),
                'handlingcharge'    => array(
                    'title'            => __('Handling Charge', 'woocommerce'),
                    'type'             => 'text',
                    'class'            => 'options',
                    'css'              => 'width: 200px;',
                    'default'          => ''
                ),
                'handlegstlocally' => array(
                    'title'            => __('Handle GST Locally', 'woocommerce'),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'options',
                    'options'          => array(
                        '0'          => __('No', 'woocommerce'),
                        '1'     => __('Yes', 'woocommerce')
                    )
                ),
                

            );
        }
	
   
       /**
     * calculate_shipping function.
     *
     * @access public
     * @param mixed $package
     * @return void
     */
     
    public function calculate_shipping($package = array()) {
            	
			global $woocommerce;
			
			include ('controller.php');
			
			$this->rates = array();
			
			$address['suburb'] = $package['destination']['city'];
			$address['state'] = $package['destination']['state'];
			$address['postcode'] = $package['destination']['postcode'];
			$address['country'] = $package['destination']['country'];
			                     
			$weight = 0;
			$width = 0;
			$height = 0;
			$length = 0;
			$items = 0;
			$vol = 0;

			$i = 0;

			foreach ( $woocommerce->cart->get_cart() as $item_id => $values ) {
            	$_product = $values['data'];
            	$vol = ($_product->width/1000) * ($_product->height/1000) * ($_product->length/1000);
				$message = $message . '\n\n' . get_the_title($_product->id) . '\nIndividual: Weight ' . $_product->weight . ' | L' . $_product->length . ' | H' . $_product->height . ' | W' . $_product->width . ' | V' . $vol . '\n';
				$message = $message . 'Quantity: ' . $values['quantity'] .'\nCombined: Weight ' . $values['quantity'] * $_product->weight . ' | L' . $values['quantity'] * $_product->length . ' | H' .  $_product->height . ' | W' .  $_product->width . ' | V' . (($_product->width/1000) * ($_product->height/1000) * (($values['quantity'] * $_product->length)/1000)) . '\n';
				$length = ($values['quantity'] * $_product->length);
				$height =  $_product->height;
				$width =  $_product->width;				
				$vol += ($width/1000) * ($height/1000) * ($length/1000);				
				$weight += ($values['quantity'] * $_product->weight);
				$i++;
				
            }

	    	$items = $woocommerce->cart->cart_contents_count;
	    	$weight = $woocommerce->cart->cart_contents_weight;
	    		    	
	    	$testdata = 'Weight: ' . $weight . 'kg \nWidth: ' . $width . 'mm \nHeight: ' . $height . 'mm \nLength: ' . $length . 'mm\nVolume: ' . $vol . 'm3';
	    	
	    	?>
	   
			<script>
			
			//alert(<?php echo $testdata;?>);
			
			</script>
	    	
	    	<?php


            $thequote = returnquote($address,$weight, $vol,$this->settings,$items); 
                       
			if($thequote!='') {
			
				$i = 1;
			
				foreach ($thequote as $quote) {
								
					$rate = array(
						'id' => 'startrack_' . $i,
						'label' => $quote['title'],
						'cost' => $quote['cost'],
						'calc_tax' => 'per_item'
		            );
		            
		            $i++;
		            
		            $this->add_rate($rate);						
					
				}

			}

        }

        function is_available($package) {
            global $woocommerce;
           
            if ($this->enabled == "no") {
                return false;
            }
            return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', true);
        }
        
}