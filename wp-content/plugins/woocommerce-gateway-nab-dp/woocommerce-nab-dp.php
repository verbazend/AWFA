<?php
/*
Plugin Name: WooCommerce NAB Transact (Direct Post) Gateway
Plugin URI: http://woothemes.com/woocommerce
Description: Use NAB Transact (National Australia Bank) Direct Post as a credit card processor for WooCommerce. Now supports both V1 and V2 of the API, Subscriptions, and UnionPay Online Payments and Risk Management.
Version: 1.5
Author: Tyson Armstrong
Author URI: http://work.tysonarmstrong.com/

Copyright: © 2012-2013 Tyson Armstrong

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '3b6b4e5212943f49037eb2193b6d7952', '18684' );

add_action('plugins_loaded', 'woocommerce_nab_dp_init', 0);

function woocommerce_nab_dp_init() {

	if (!class_exists('WC_Payment_Gateway'))  return;

	class WC_Gateway_NAB_Direct_Post extends WC_Payment_Gateway {

		public function __construct() {
			global $woocommerce;

		    $this->id 					= 'nab_dp';
		    $this->method_title 		= __('NAB Transact (direct post)', 'woothemes');
			$this->method_description 	= __('NAB Transact handles all the steps in the secure transaction while remaining virtually transparent. Payment data is passed from the checkout form to NAB Transact for processing thus removing the complexity of PCI compliance.', 'woothemes');
			$this->icon 				= WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/images/nab_small.jpg';
			$this->supports 			= array('products','subscriptions','subscription_cancellation','subscription_suspension','subscription_reactivation','subscription_amount_changes','subscription_date_changes','subscription_payment_method_change','subscription_payment_method_change_customer','subscription_payment_method_change_admin','multiple_subscriptions');

		    // Load the form fields.
		    $this->init_form_fields();

		    // Load the settings.
		    $this->init_settings();

		 	if ($this->settings['testmode'] == 'yes') {
		 		if (isset($this->settings['api_version']) && $this->settings['api_version'] == 'V2') {
		 			$this->payurl = 'https://transact.nab.com.au/test/directpostv2/authorise';
				} else {
					$this->fingerprinturl = 'https://transact.nab.com.au/test/directpost/genfingerprint';
					$this->payurl = 'https://transact.nab.com.au/test/directpost/authorise';
				}
				$this->crnfingerprinturl = 'https://transact.nab.com.au/test/directpost/crnfingerprint';
				$this->crnurl = 'https://transact.nab.com.au/test/directpost/crnmanage';
				$this->xmlapiurl = 'https://transact.nab.com.au/xmlapidemo/periodic';
		    } else {
		    	if (isset($this->settings['api_version']) && $this->settings['api_version'] == 'V2') {
		    		$this->payurl = 'https://transact.nab.com.au/live/directpostv2/authorise';
				} else {
					$this->fingerprinturl = 'https://transact.nab.com.au/live/directpost/genfingerprint';
					$this->payurl = 'https://transact.nab.com.au/live/directpost/authorise';
				}
				$this->crnfingerprinturl = 'https://transact.nab.com.au/live/directpost/crnfingerprint';
				$this->crnurl = 'https://transact.nab.com.au/live/directpost/crnmanage';
				$this->xmlapiurl = 'https://transact.nab.com.au/xmlapi/periodic';
		    }

		    // Define user set variables
		    $this->title = $this->settings['title'];
		    $this->paymentmethods = 'Visa, Mastercard';
		    if ($this->settings['accept_amex'] == 'yes') $this->paymentmethods .= ', American Express';
		    if ($this->settings['accept_diners'] == 'yes') $this->paymentmethods .= ', Diners Club';
		    if ($this->settings['accept_jcb'] == 'yes') $this->paymentmethods .= ', JCB';
		    if (isset($this->settings['api_version']) && $this->settings['api_version'] == 'V2' && $this->settings['accept_upop'] == 'yes') $this->paymentmethods .= ', UnionPay';
		    $this->description = 'Credit cards accepted: '.$this->paymentmethods;
		    


   		 	// Hooks
			add_action( 'woocommerce_receipt_nab_dp', array(&$this, 'receipt_page') );
			add_action( 'woocommerce_api_wc_gateway_nab_direct_post', array(&$this, 'relay_response'));

			// Save admin options
			add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) ); // 1.6.6
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) ); // 2.0.0

			// Additional tasks if Subscriptions is installed
			if (class_exists('WC_Subscriptions_Order')) {

				add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );
				add_filter( 'wcs_resubscribe_order_created', array( $this, 'delete_resubscribe_meta' ), 10 );
				add_action( 'woocommerce_subscription_failing_payment_method_updated_'.$this->id, array( $this, 'update_failing_payment_method' ), 10, 2 );

				// Allow store managers to manually set Simplify as the payment method on a subscription
				add_filter( 'woocommerce_subscription_payment_meta', array( $this, 'add_subscription_payment_meta' ), 10, 2 );
				add_filter( 'woocommerce_subscription_validate_payment_meta', array( $this, 'validate_subscription_payment_meta' ), 10, 2 );

				// Filter acceptable order statuses to allow changing of payment gateway with DP-style gateways
				add_filter('woocommerce_valid_order_statuses_for_payment', array( $this, 'allow_payment_method_change'), 10, 2);

			}

		}


		/**
	     * Initialise Gateway Settings Form Fields
		 *
		 * @since 1.0.0
	     */
		function init_form_fields() {
			$this->form_fields = array(
			    'enabled' => array(
			        'title' => __( 'Enable/Disable', 'woothemes' ),
			        'type' => 'checkbox',
			        'label' => __( 'Enable this payment method', 'woothemes' ),
			        'default' => 'yes'
			    ),
			    'title' => array(
			        'title' => __( 'Title', 'woothemes' ),
			        'type' => 'text',
			        'description' => __( 'This controls the title which the user sees during checkout.', 'woothemes' ),
			        'default' => __( 'NAB Transact', 'woothemes' )
			    ),
			    'api_version' => array(
			        'title' => __( 'API Version', 'woothemes' ),
			        'type' => 'select',
			        'options' => array('V1'=>'V1','V2'=>'V2'),
			        'description' => __( 'V1 supports fewer features.', 'woothemes' ),
			        'default' => __( 'V1', 'woothemes' )
			    ),			    
				'testmode' => array(
					'title' => __( 'Test mode', 'woothemes' ),
					'label' => __( 'Enable Test mode', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Process transactions in Test mode. No transactions will actually take place.', 'woothemes' ),
					'default' => 'yes'
				),
				'client_id' => array(
					'title' => __( 'NAB Client ID', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'The Client ID will be of the format "ABC0010", where ABC is your unique three-letter account code.', 'woothemes' ),
					'default' => ''
				),
				'nab_password' => array(
					'title' => __( 'NAB Password', 'woothemes' ),
					'type' => 'password',
					'description' => __( 'Your merchant password is for payment authentication only.', 'woothemes' ),
					'default' => ''
				),
				'risk_management' => array(
					'title' => __( 'Risk management', 'woothemes' ),
					'label' => __( 'Enable risk management feature', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( '<strong>V2 only.</strong> This feature must be enabled by NAB Transact.', 'woothemes' ),
					'default' => 'yes'
				),
				/*'3d_secure' => array(
					'title' => __( '3D Secure', 'woothemes' ),
					'label' => __( 'Enable 3D Secure feature', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( '<strong>V2 only.</strong> This feature must be enabled by NAB Transact.', 'woothemes' ),
					'default' => 'yes'
				),
				'3d_secure_number' => array(
					'title' => __( 'Your NAB EB number', 'woothemes' ),
					'type' => 'text',
					'description' => __( '<strong>V2 only.</strong> Used only when you have 3D Secure enabled, this is your online merchant number specified by your bank which has been registered for Verified by VISA or SecureCode (or both). This will be your NAB EB number, e.g. “22123456”.', 'woothemes' ),
					'default' => ''
				), */
				'accept_upop' => array(
					'title' => __( 'Accept UnionPay Online Payments', 'woothemes' ),
					'label' => __( 'Accept UnionPay', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( '<strong>V2 only.</strong> Contact NAB to activate UnionPay Online Payments on on your account.', 'woothemes' ),
					'default' => 'no'
				),
				'accept_amex' => array(
					'title' => __( 'Accept American Express', 'woothemes' ),
					'label' => __( 'Accept American Express cards', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Call 1300 363 614 to activate American Express on your account.', 'woothemes' ),
					'default' => 'no'
				),
				'accept_diners' => array(
					'title' => __( 'Accept Diners Club', 'woothemes' ),
					'label' => __( 'Accept Diners Club cards', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Call 1300 360 500 to activate Diners Club on your account.', 'woothemes' ),
					'default' => 'no'
				),
				'accept_jcb' => array(
					'title' => __( 'Accept JCB', 'woothemes' ),
					'label' => __( 'Accept JCB cards', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Call 1300 363 614 to activate JCB on your account.', 'woothemes' ),
					'default' => 'no'
				)
			);
		} // End init_form_fields()

		/**
		 * Admin Panel Options
		 *
		 * @since 1.0.0
		 */
		public function admin_options() {

	    	?>
	    	<h3><?php _e('NAB Transact Credit Card Payment', 'wc-nab'); ?></h3>
	    	<p><?php _e('Using the NAB Transact Direct Post payment gateway.', 'wc-nab'); ?></p>
	    	<table class="form-table">
	    	<?php
	    		// Generate the HTML For the settings form.
	    		$this->generate_settings_html();
	    	?>
			</table><!--/.form-table-->
	    	<?php
	    } // End admin_options()




		/**
		 * Process the payment and return the result
		 * - redirects the customer to the pay page
		 *
		 * @since 1.0.0
		 */
		function process_payment( $order_id ) {

			$order = new WC_Order( $order_id );

			$redirect = $order->get_checkout_payment_url( true );
			// Check if this is a payment change, and if so, add a query arg for later
			if (class_exists('WC_Subscriptions_Change_Payment_Gateway')) {
				$is_payment_change = WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment;
				if ($is_payment_change) $redirect = add_query_arg('is_payment_change',1,$redirect);
			}

			return array(
				'result' 	=> 'success',
				'redirect'	=> $redirect
			);
		}

		/**
		 * Send post data to a https url
		 * Used to get the fingerprint
		 *
		 * @since 1.0.0
		 */
	  	function send($packet, $url) {
	  		if (is_array($packet)) {
	  	  		$packet = http_build_query($packet);
	  	  	}

		  	$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 45,
				'body' => $packet
			    )
			);

			if( ! is_wp_error( $response ) ) {
			   return $response['body'];
			}
		}

		/**
		 * Collect the credit card details on the pay page and post
		 * to NAB Transact
		 * - includes fingerprint generation
		 *
		 * @since 1.0.0
		 */
		function receipt_page($order_id) {
			global $woocommerce;
			// Get the order
			$order = new WC_Order( $order_id );

			// Payment form
			if ($this->settings['testmode']=='yes') : ?><p><?php _e('TEST MODE ENABLED', 'woothemes'); ?></p><?php endif;

			$timestamp = gmdate('YmdHis');
			$reference_id = str_replace(array('-','_','#'),'',substr(urlencode(time().$order_id.$order->order_key),0,32));
			$amount = number_format($order->get_total(),2,'.','');
			
			$is_payment_change = (isset($_GET['is_payment_change']) && $_GET['is_payment_change'] == '1');
			if ($is_payment_change) {
				$amount = 0;
				update_post_meta($order_id,'_is_mid_change_method',true);
			}

			$is_crn = 0;

			if ($this->order_has_subscription($order_id)) {
				// Get the total initial payment if using older v1.5 of Subscriptions
				if ($this->order_has_subscription($order_id) == 1) {
					$amount = WC_Subscriptions_Order::get_total_initial_payment( $order );
				}
				if ($is_payment_change) $amount = 0;
				$is_crn = 1;
				$eps_crn = 'WOO'.$order_id.'-'.time();
				$fingerprint = $this->generate_fingerprint('CRN',array('timestamp'=>$timestamp,'eps_crn'=>$eps_crn));
			} elseif ($this->settings['api_version'] == 'V2') {

				// Determine the transaction type to pass based on functionality
				if (isset($this->settings['risk_management']) && $this->settings['risk_management'] == 'yes' && isset($this->settings['3d_secure']) && $this->settings['3d_secure'] == 'yes') {
					$txntype = 6;
				} elseif (isset($this->settings['risk_management']) && $this->settings['risk_management'] == 'yes' && (!isset($this->settings['3d_secure']) || $this->settings['3d_secure'] == 'no')) {
					$txntype = 2;
				} elseif ((!isset($this->settings['risk_management']) || $this->settings['risk_management'] == 'no') && isset($this->settings['3d_secure']) && $this->settings['3d_secure'] == 'yes') {
					$txntype = 4;
				} else {
					$txntype = 0;
				}

				$fingerprint = $this->generate_fingerprint('V2',array('amount'=>$amount,'timestamp'=>$timestamp,'txntype'=>$txntype,'reference_id'=>$reference_id));
				$upop_fingerprint = $this->generate_fingerprint('UPOP',array('amount'=>$amount,'timestamp'=>$timestamp,'reference_id'=>$reference_id,'txntype'=>$txntype));

			} else {
				$fingerprint = $this->generate_fingerprint('V1',array('timestamp'=>$timestamp,'amount'=>$amount,'reference_id'=>$reference_id));
			}

			$this->result_url = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Nab_Direct_Post', home_url('/') ) );
			$this->result_url = add_query_arg('order',$order_id,$this->result_url);
			$this->result_url = add_query_arg('key',$order->order_key,$this->result_url);
			$this->result_url = add_query_arg('is_crn',$is_crn,$this->result_url);

			if ($is_payment_change) $this->result_url = add_query_arg('is_payment_change',1,$this->result_url);

			if (get_option('woocommerce_force_ssl_checkout')=='yes' || is_ssl()) $this->result_url = str_replace('http:', 'https:', $this->result_url);
			if ($this->description) : ?><p><?php echo $this->description;
			//if (!$is_crn && $this->settings['api_version'] == 'V2' && $this->settings['accept_upop'] == 'yes') echo ', UnionPay Online Payments'; ?></p><?php endif; ?>
			<p class="woocommerce-error" id="nab_error_message" style="display:none;"></p>
			<form method="POST" action="<?php echo ($is_crn) ? $this->crnurl : $this->payurl; ?>" class="nab_payment_form">
			<?php // Fields always required ?>
			<input type="hidden" name="EPS_MERCHANT" value="<?php echo $this->settings['client_id']; ?>" />
			<input type="hidden" name="EPS_TIMESTAMP" value="<?php echo $timestamp; ?>" />
			<input type="hidden" name="EPS_RESULTURL" value="<?php echo $this->result_url; ?>" />
			<input type="hidden" name="EPS_FINGERPRINT" value="<?php echo urlencode($fingerprint); ?>" data-fingerprint="<?php echo urlencode($fingerprint); ?>" <?php if (isset($upop_fingerprint)) echo 'data-upop-fingerprint="'.$upop_fingerprint.'"'; ?> />
			<?php // Although EPS_REDIRECT isn't in the NAB documentation,
			// it is required to maintain the query variables in EPS_RESULTURL.
			// It seems like a bug in NAB's system ?>
			<input type="hidden" name="EPS_REDIRECT" value="true" />

			<input type="hidden" name="EPS_CURRENCY" value="<?php echo get_woocommerce_currency(); ?>" />

			<?php // IF A CRN CALL 
			if ($is_crn) { ?>
				<input type="hidden" name="EPS_TYPE" value="CRN" />
				<input type="hidden" name="EPS_ACTION" value="ADDCRN" />
				<input type="hidden" name="EPS_CRN" value="<?php echo $eps_crn; ?>" />
			<?php }// IF A V2 PAYMENT
			if ($this->settings['api_version'] == 'V2' && !$is_crn) { 
				echo '<input type="hidden" name="EPS_TXNTYPE" value="'.$txntype.'" />';
				if (isset($this->settings['risk_management']) && $this->settings['risk_management'] == 'yes') {
					echo '<input type="hidden" name="EPS_FIRSTNAME" value="'.$order->billing_first_name.'" />';
					echo '<input type="hidden" name="EPS_LASTNAME" value="'.$order->billing_last_name.'" />';
					echo '<input type="hidden" name="EPS_IP" value="'.$_SERVER['REMOTE_ADDR'].'" />';
					echo '<input type="hidden" name="EPS_ZIPCODE" value="'.$order->billing_postcode.'" />';
					echo '<input type="hidden" name="EPS_BILLINGCOUNTRY" value="'.$order->billing_country.'" />';
					if ($order->shipping_country) {
						echo '<input type="hidden" name="EPS_DELIVERYCOUNTRY" value="'.$order->shipping_country.'" />';
					}
					echo '<input type="hidden" name="EPS_EMAILADDRESS" value="'.$order->billing_email.'" />';
				} 
				if (isset($this->settings['3d_secure']) && $this->settings['3d_secure'] == 'yes') {
					echo '<input type="hidden" name="3D_XID" value="'.str_pad($timestamp,20,'0').'" />';
					echo '<input type="hidden" name="EPS_MERCHANTNUM" value="'.$this->settings['3d_secure_number'].'" />';
				}
			}

			if (!$is_crn) { ?>
				<input type="hidden" name="EPS_AMOUNT" value="<?php echo $amount; ?>" />
				<input type="hidden" name="EPS_REFERENCEID" value="<?php echo $reference_id; ?>" />
			<?php } ?>	

			<input type="hidden" name="EPS_CARDTYPE" value="unknown" id="jsCardType" />
			<?php if ($this->settings['accept_upop'] == 'yes' && $this->settings['api_version'] == 'V2' && !$is_crn) { ?>
				<div>
					<input type="radio" name="EPS_PAYMENTCHOICE" id="nab_cardtype_vmc" class="input-radio cardtype_checking" checked="checked" value="" /> <label for="nab_cardtype_vmc"><?php echo sprintf(__("Pay with %s", 'wc-nab'),str_replace(', UnionPay','',$this->paymentmethods)); ?></label><br />
					<input type="radio" name="EPS_PAYMENTCHOICE" id="nab_cardtype_upop" class="input-radio cardtype_checking" value="UPOP" /> <label for="nab_cardtype_upop"><?php _e("Pay with UnionPay", 'wc-nab'); ?></label>
				</div><br />
			<?php } ?>
				<fieldset id="nab_card_details">
					<p class="form-row form-row-first">
						<label for="nab_card_number"><?php _e("Credit card number", 'wc-nab') ?> <span class="required">*</span></label>
						<input type="text" class="input-text" name="EPS_CARDNUMBER" id="nab_card_number" /><span id="jsCardType"></span>
					</p>
					<div class="clear"></div>
					<p class="form-row form-row-first">
						<label for="cc-expire-month"><?php _e("Expiration date", 'wc-nab') ?> <span class="required">*</span></label>
						<select name="EPS_EXPIRYMONTH" id="cc-expire-month">
							<option value=""><?php _e('Month', 'wc-nab') ?></option>
							<?php
								$months = array();
								for ($i = 1; $i <= 12; $i++) {
								    $timestamp = mktime(0, 0, 0, $i, 1);
								    $months[date('m', $timestamp)] = date('F', $timestamp);
								}
								foreach ($months as $num => $name) {
						            printf('<option value="%s">%s - %s</option>', $num,$num, $name);
						        }

							?>
						</select>
						<select name="EPS_EXPIRYYEAR" id="cc-expire-year">
							<option value=""><?php _e('Year', 'wc-nab') ?></option>
							<?php
								$years = array();
								for ($i = date('Y'); $i <= date('Y') + 15; $i++) {
									if ($is_crn) {
								    	printf('<option value="%u">%u</option>', substr($i,2), $i);
								    } else {
								    	printf('<option value="%u">%u</option>', $i, $i);
								    }
								}
							?>
						</select>
					</p>
					<p class="form-row form-row-last">
						<label for="nab_card_ccv"><?php _e("Card security code", 'wc-nab') ?> <span class="required">*</span></label>
						<input type="text" class="input-text" id="nab_card_ccv" name="EPS_CCV" maxlength="4" style="width:45px" />
						<span class="help nab_card_ccv_description"><?php _e('3 or 4 digits usually found on the signature strip.', 'wc-nab') ?></span>
					</p>
					<div class="clear"></div>
				</fieldset>
				<div class="upop_note" style="display: none;"><p><?php _e("You will be able to enter your UnionPay details on the next page.",'wc-nab'); ?></p></div>
				<input type="submit" id="jsPayButton" class="submit buy button" value="<?php _e('Confirm and pay','wc-nab'); ?>" />
				</form>
				<script type="text/javascript">
				jQuery(function(){

					jQuery('input#jsPayButton').on('click',function(e) {
						var number = jQuery('input#nab_card_number').val();
						number = number.replace(/[^0-9]/g, '');
						jQuery('input#nab_card_number').val(number);
						if (!validateFields(true)) {
							e.preventDefault();
							return false;
						} else {
							return true;
						}
					});

					jQuery('form.nab_payment_form').submit(function() {
						jQuery(this).find('input[type=submit]').attr('disabled','disabled');
					});

					jQuery('input.cardtype_checking').on('change',function() {
				        if (jQuery('input.cardtype_checking#nab_cardtype_vmc').is(':checked')) {
				            jQuery('fieldset#nab_card_details').show();
				            jQuery('div.upop_note').hide();
				            jQuery('input[name="EPS_TXNTYPE"]').val(jQuery('input[name="EPS_TXNTYPE"]').data('vmc_value'));
				            jQuery('input[name="EPS_FINGERPRINT"]').val(jQuery('input[name="EPS_FINGERPRINT"]').attr('data-fingerprint'));
				            validateFields(false);
				        } else if (jQuery('input.cardtype_checking#nab_cardtype_upop').is(':checked')) {
				            jQuery('fieldset#nab_card_details').hide();
				            jQuery('div.upop_note').show();
				            jQuery('input[name="EPS_TXNTYPE"]').data('vmc_value',jQuery('input[name="EPS_TXNTYPE"]').val()).val('0');
				            jQuery('input[name="EPS_FINGERPRINT"]').val(jQuery('input[name="EPS_FINGERPRINT"]').attr('data-upop-fingerprint'));
				        }
				    });

					jQuery('input#nab_card_number').on('keyup',function() {
						var number = jQuery(this).val();
						number = number.replace(/[^0-9]/g, '');
							var re = new RegExp("^4[0-9]{12}(?:[0-9]{3})?$");
           					if (number.match(re) != null) {
           					jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/visa.png', __FILE__ ) ?>" alt="Visa detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('visa');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
           					}
							re = new RegExp("^5[1-5][0-9]{14}$");
				            if (number.match(re) != null) {
							jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/mastercard.png', __FILE__ ) ?>" alt="Mastercard detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('mastercard');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
				            re = new RegExp("^3[47][0-9]{13}$");
				            if (number.match(re) != null) {
				            jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/amex.png', __FILE__ ) ?>" alt="American Express detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('amex');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
				            re = new RegExp("^3(?:0[0-5]|[68][0-9])[0-9]{11}$");
				            if (number.match(re) != null) {
				            jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/diners.png', __FILE__ ) ?>" alt="Diners Club card detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('dinersclub');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
				            re = new RegExp("^(?:3[0-9]{15}|(2131|1800)[0-9]{11})$");
				            if (number.match(re) != null) {
				            jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/jcb.png', __FILE__ ) ?>" alt="JCB card detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('jcb');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
							jQuery('span#jsCardType').html('');
							jQuery('input#jsCardType').val('');
							jQuery('input#nab_card_number').data('validated',false);
					});
	
					jQuery('select#cc-expire-month,select#cc-expire-year').on('change',function() {
						validateFields(false);
					});
					jQuery('input#nab_card_ccv').on('keyup',function() {
						validateFields(false);	
					});

					function validateFields(showError) {
						jQuery('input#jsPayButton').removeAttr('disabled');
						// Skip validation is UPOP is selected
						if (jQuery('input#nab_cardtype_upop').is(':checked')) {
							return true;
						}
						var error = new Array();
						// Card number
						if (jQuery('input#nab_card_number').data('validated') != true) {
							error.push("<?php _e('Please enter a valid credit card number.','wc-nab'); ?>");
						}
						// Expiry date
						if (jQuery('select#cc-expire-month').val() == '' || jQuery('select#cc-expire-year').val() == '') {
							error.push("<?php _e('Please enter a valid expiry date.','wc-nab'); ?>");
						}
						var tdate = new Date();
						var year = tdate.getFullYear().toString().substring(2);
						var month = tdate.getMonth() + 1;
						if (month.length == 1) month = '0' + month;
						if (jQuery('select#cc-expire-month').val() < month && jQuery('select#cc-expire-year').val() == year) {
							error.push("<?php _e('Please enter an expiry date in the future.','wc-nab'); ?>");
						}
						// CCV
						if (jQuery('input#nab_card_ccv').val().length < 3 || jQuery('input#nab_card_ccv').val().length > 4) {
							error.push("<?php _e('Please enter a valid card security code.','wc-nab'); ?>");
						}
						if (error.length == 0) {
							jQuery('#nab_error_message').hide().html('');
							return true;
						} else if (showError == true) {
							var error_string = error.join("<br />");
							jQuery('#nab_error_message').show().html(error_string);
							return false;
						} else {
							return false;
						}
					}
					jQuery('input#jsPayButton').removeAttr('disabled');
				});
				</script>
		<?php
		}


		/**
		 * Generate fingerprint (3 versions)
		 *
		 * @since 1.3.0
		 */
		function generate_fingerprint($ver='V1',$vars) {
			$fingerprint;
			switch ($ver) {
				case "UPOP" :
					// Generate the Payment Fingerprint, V2 for UPOP payment style
					$fingerprint = sha1($this->settings['client_id'].'|'.$this->settings['nab_password'].'|0|'.$vars['reference_id'].'|'.$vars['amount'].'|'.$vars['timestamp']);
					break;

				case "V2" :
					// Generate the Payment Fingerprint, V2 style
					$fingerprint = sha1($this->settings['client_id'].'|'.$this->settings['nab_password'].'|'.$vars['txntype'].'|'.$vars['reference_id'].'|'.$vars['amount'].'|'.$vars['timestamp']);
					break;

				case "V1" :
					// Get the Payment Fingerprint
					$data = array(
						'EPS_MERCHANT'=>$this->settings['client_id'],
						'EPS_PASSWORD'=>$this->settings['nab_password'],
						'EPS_TIMESTAMP'=>$vars['timestamp'],
						'EPS_REFERENCEID'=>$vars['reference_id'],
						'EPS_AMOUNT'=>$vars['amount']
						);
					$fingerprint = urldecode($this->send($data,$this->fingerprinturl));
					if (strpos($fingerprint,' ') !== false || $fingerprint == '') {
						$errormsg = str_replace('error=','',$fingerprint);
						echo '<div class="woocommerce-error">';
						echo sprintf(__('There was a problem generating your NAB payment fingerprint: %s', 'wc-nab'),$errormsg);
						if ($errormsg == "Invalid merchant") {
							echo '<br />';
							_e('Your NAB credentials are incorrect. Please confirm your merchant details with NAB.', 'wc-nab');
						}
						echo '</div>';
					}
					break;

				case "CRN" :
					$data = array(
						'EPS_MERCHANT'=>$this->settings['client_id'],
						'EPS_PASSWORD'=>$this->settings['nab_password'],
						'EPS_TYPE'=>'CRN',
						'EPS_ACTION'=>'ADDCRN',
						'EPS_CRN'=>$vars['eps_crn'],
						'EPS_TIMESTAMP'=>$vars['timestamp']
						);
					$fingerprint = $this->send($data,$this->crnfingerprinturl);
					if (strpos($fingerprint,' ') !== false || $fingerprint == '') {
						$errormsg = str_replace('error=','',$fingerprint);
						echo '<div class="woocommerce-error">';
						echo sprintf(__('There was a problem generating your NAB customer fingerprint: %s', 'wc-nab'),$errormsg);
						if ($errormsg == "Invalid merchant") {
							echo '<br />';
							_e('Your NAB credentials are incorrect. Please confirm your merchant details with NAB.', 'wc-nab');
						}
						echo '</div>';
					}
					break;
			}
			return $fingerprint;
		}


		/**
		 * Generates the XML message to send via API
		 *
		 * $data = array('crn','amountcents','reference','currency');
		 * @since 1.2.0 
		 **/
		function generatePaymentXMLMessage($data) {
			$tz_in_secs = date('Z');
			$tz_in_mins = round($tz_in_secs/60);
			if ($tz_in_mins >= 0) $tz_in_mins = '+'.$tz_in_mins;
			$timestamp = date('YdmHis000000').$tz_in_mins;

			$messageID = substr(time().'-'.$data['reference'],0,30);

			$xml = new DOMDocument();
			$root = $xml->appendChild($xml->createElement("NABTransactMessage"));
			
			// Create MessageInfo
			$MessageInfo = $root->appendChild($xml->createElement("MessageInfo"));
			$MessageInfo->appendChild($xml->createElement("messageID",$messageID));
			$MessageInfo->appendChild($xml->createElement("messageTimestamp",$timestamp));
			$MessageInfo->appendChild($xml->createElement("timeoutValue",'60'));
			$MessageInfo->appendChild($xml->createElement("apiVersion",'spxml-4.2'));

			// Create MerchantInfo
			$MerchantInfo = $root->appendChild($xml->createElement("MerchantInfo"));
			$MerchantInfo->appendChild($xml->createElement("merchantID",$this->settings['client_id']));
			$MerchantInfo->appendChild($xml->createElement("password",$this->settings['nab_password']));
			
			// Create RequestType
			$RequestType = $root->appendChild($xml->createElement("RequestType","Periodic"));

			// Create Periodic
			$Periodic = $root->appendChild($xml->createElement("Periodic"));
			$PeriodicList = $Periodic->appendChild($xml->createElement("PeriodicList"));
			$PeriodicList->appendChild($xml->createAttribute("count"))->appendChild($xml->createTextNode("1"));
			$PeriodicItem = $PeriodicList->appendChild($xml->createElement("PeriodicItem"));
			$PeriodicItem->appendChild($xml->createAttribute("ID"))->appendChild($xml->createTextNode("1"));
			$PeriodicItem->appendChild($xml->createElement("actionType","trigger"));
			$PeriodicItem->appendChild($xml->createElement("periodicType","8"));
			$PeriodicItem->appendChild($xml->createElement("crn",$data['crn']));
			$PeriodicItem->appendChild($xml->createElement("transactionReference",$data['reference']));
			$PeriodicItem->appendChild($xml->createElement("amount",$data['amountcents']));
			$PeriodicItem->appendChild($xml->createElement("currency",$data['currency']));
			$CreditCardInfo = $PeriodicItem->appendChild($xml->createElement("CreditCardInfo"));
			$CreditCardInfo->appendChild($xml->createElement("recurringFlag","no"));

			return $xml->saveHTML();
		}


		/**
		 * Relay response - handles response from NAB Transact
		 *
		 * @since 1.0.0
		 */
		function relay_response() {
			// Use alternate handler if this is a CRN response
			if (isset($_GET['is_crn']) && $_GET['is_crn'] == true) {
				$this->relay_response_crn();
				exit();
			}

			global $woocommerce;

			// Process response
			$response = new stdClass;
			foreach ($_GET as $key => $value) {
				$response->$key = $value;
			}
		    foreach ($_POST as $key => $value) {
	            $response->$key = $value;
	        }
	        
	        if ( ! empty( $response->order ) ) {

		        $order = new WC_Order( (int) $response->order );

				if ($response->rescode == '00' || $response->rescode == '08' || $response->rescode == '11') { // Approved
					if ($order->key_is_valid( $response->key )) {

						// Payment complete
						$order->add_order_note(
						'NAB Transaction id: '.$response->txnid
						."\r\nNAB Auth id: ".$response->authid
						."\r\nNAB Settlement date: ".$response->settdate);

						$order->payment_complete($response->txnid);

						if (isset($response->afrescode) && $response->afrescode != '000') {
							$order->add_order_note(sprintf(__('FraudGuard warning triggered: %s','wc-nab'), $response->afrestext));
							$order->update_status( 'on-hold' );
						}

						// Remove cart
						$woocommerce->cart->empty_cart();

					} else { // payment received but order key didn't match!

						// Key did not match order id
						$order->add_order_note( sprintf(__('Payment received, but order ID did not match key: code %s - %s.', 'wc-nab'), $response->response_code, $response->response_reason_text ) );

						// Put on hold if pending
						if ($order->status == 'pending' || $order->status == 'failed') {
							$order->update_status( 'on-hold' );
						}
					}
				} else { // Transaction failed
					$order->update_status( 'failed' );
					$order->add_order_note( sprintf(__("NAB payment failure: code %s - %s. \r\nTransaction ID: %s\r\n", 'wc-nab'), $response->rescode, $response->restext,$response->txnid ) );
				}

				wp_redirect( $this->get_return_url( $order ) );
				exit;

			}

			wp_redirect( $this->get_return_url() );
			exit;
		}

		/**
		 * Relay response - handles response from NAB Transact CRN
		 * At this stage we've just added a CRN record, and now we must process the payment to this CRN
		 * 
		 * @since 1.2.0
		 */
		function relay_response_crn() {
			global $woocommerce;

			// Process response
			$response = new stdClass;
			foreach ($_GET as $key => $value) {
				$response->$key = $value;
			}
		    foreach ($_POST as $key => $value) {
	            $response->$key = $value;
	        }

			if ( ! empty( $response->order ) ) {
		    
		        $order = new WC_Order( (int) $response->order );

				if ((!isset($response->afrescode) || $response->afrescode=='400' || $response->afrescode=='000') && ($response->rescode == '00' || $response->rescode == '08' || $response->rescode == '11')) { // Approved
					if ($order->key_is_valid( $response->key ) && $order->status != 'completed' && $order->status != 'processing') {

						// Save CRN to order meta also
						$this->save_subscription_meta( (int)$response->order , $response->CRN);

						// Add Order Note
						$order->add_order_note(__('Credit card details stored.','wc-nab'));

						if (!function_exists('wcs_get_subscriptions_for_order')) {
							$amount = WC_Subscriptions_Order::get_total_initial_payment($order);
						} else {
							$amount = $order->get_total();
						}

						if (isset($response->is_payment_change) && $response->is_payment_change == '1' && get_post_meta($order->id,'_is_mid_change_method',true)) {
							$amount = 0;
							delete_post_meta($order->id,'_is_mid_change_method',true);
						}

						if ($amount == 0) {
							$order->payment_complete();
						} else {
							// Now to process payment, but only if it hasn't already been done
							// (this url is used by result AND return)
							if ($order->status != 'completed') {
								$data = array(
									'crn'=>$response->CRN,
									'amountcents'=>($amount*100),
									'reference'=>substr(urlencode($response->order.'-'.$order->order_key),0,32),
									'currency'=>get_woocommerce_currency());
								$payment_xml = $this->generatePaymentXMLMessage($data);
								$payment_result = $this->send($payment_xml,$this->xmlapiurl,true);

								$result_object = simplexml_load_string($payment_result); 


								if ($result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '00' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '08' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '11') {
									// Payment success!
									$order->add_order_note(sprintf(__("NAB Transaction id: %s\r\nNAB Settlement date: %s",'wc-nab'),$result_object->Periodic->PeriodicList->PeriodicItem->txnID,$result_object->Periodic->PeriodicList->PeriodicItem->settlementDate));

									$order->payment_complete((string)$result_object->Periodic->PeriodicList->PeriodicItem->txnID);
									// Remove cart
									$woocommerce->cart->empty_cart();

								} else {
									if ($order->status != 'completed' && $order->status != 'processing') {
										$order->update_status( 'failed', sprintf(__("NAB error whilst processing payment via XML API using CRN: code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription) );
									}
								}
							}
						}

					} else { // payment received but order key didn't match!

						// Key did not match order id
						$order->add_order_note( sprintf(__('Payment received, but order ID did not match key: code %s - %s.', 'wc-nab'), $response->response_code, $response->response_reason_text ) );

						// Put on hold if pending
						if ($order->status == 'pending' || $order->status == 'failed') {
							$order->update_status( 'on-hold' );
						}
					}
				} else { // Transaction failed
					if ($order->status != 'completed' && $order->status != 'processing') {
						$order->update_status( 'failed', sprintf(__("NAB error whilst adding CRN: code %s - %s. PAYMENT NOT PROCESSED!", 'wc-nab'), $response->rescode, $response->restext) );
					}
				}
				
				// It's possible we just processed a change of payment method, not a proper order
		        // so we might want to just go back to the My Account page
		        if (isset($_GET['is_payment_change']) && $_GET['is_payment_change'] == '1') {
		        	wp_redirect(get_permalink( woocommerce_get_page_id( 'myaccount' ) ));
		        } else {
		        	wp_redirect( $this->get_return_url( $order ) );
		        }
				exit;

			}
			wp_redirect( $this->get_return_url() );
			exit;
		}


		/**
		 * scheduled_subscription_payment function.
		 * 
		 * @param $amount_to_charge float The amount to charge.
		 * @param WC_Order $renewal_order A WC_Order object created to record the renewal payment.
		 * @access public
		 * @return void
		 */
		function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

			$result = $this->process_subscription_payment( $renewal_order, $amount_to_charge );
			
			if ( is_wp_error( $result ) ) {	
				$renewal_order->update_status( 'failed', sprintf( __( 'NAB transaction failed (%s)', 'wc-nab' ), $result->get_error_message() ) );
			}
			
		}

		/**
		 * process_subscription_payment function.
		 * 
		 * @access public
		 * @param mixed $order
		 * @param int $amount (default: 0)
		 * @return void
		 */
		function process_subscription_payment( $order = '', $amount = 0 ) {
			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();
				return true;
			}

			$crn = get_post_meta($order->id,'_nab_crn',true);
			if (!$crn) 
				return new WP_Error( 'nab_error', __( 'CRN not found.', 'wc-nab' ) );

			$subscription_name = sprintf( __( '%s - Order %s', 'wc-merchant-warrior' ), substr(esc_html( get_bloginfo( 'name', 'display' ) ) , 0, 15), $order->get_order_number() );

			$data = array(
				'crn'=>$crn,
				'amountcents'=>$amount*100,
				'reference'=>substr(urlencode($subscription_name),0,32),
				'currency'=>get_woocommerce_currency());
			$payment_xml = $this->generatePaymentXMLMessage($data);
			$payment_result = $this->send($payment_xml,$this->xmlapiurl,true);

			$result_object = simplexml_load_string($payment_result); 

			if ($result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '00' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '08' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '11') {
				// Payment success!
				$order->add_order_note(
					'Subscription payment processed. NAB Transaction id: '.$result_object->Periodic->PeriodicList->PeriodicItem->txnID
					."\r\nNAB Settlement date: ".$result_object->Periodic->PeriodicList->PeriodicItem->settlementDate);
				$order->payment_complete((string)$result_object->Periodic->PeriodicList->PeriodicItem->txnID);
				return true;
			} else {
				$order->add_order_note(sprintf(__("NAB error whilst processing payment via XML API using CRN: code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription) );
				return new WP_Error('nab_error',sprintf(__("NAB error whilst processing payment via XML API using CRN: code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription));
			}
		}

		/**
		 * Store the customer and card IDs on the order and subscriptions in the order
		 *
		 * @param int $order_id
		 * @param string $customer_id
		 */
		protected function save_subscription_meta( $order_id, $customer_id ) {
			$customer_id = wc_clean( $customer_id );
			update_post_meta( $order_id, '_nab_crn', $customer_id );

			// Also store it on the subscriptions being purchased in the order
			if (function_exists('wcs_get_subscriptions_for_order')) {
				foreach( wcs_get_subscriptions_for_order( $order_id ) as $subscription ) {
					update_post_meta( $subscription->id, '_nab_crn', $customer_id );
				}
			}
		}

		/**
		 * Check if the order has a subscription (either according to Subscriptions 1.5 or 2.0)
		 *
		 * @param string $order_id The ID of the order to check
		 * @return mixed Either 1 (Subscriptions 1.5), 2 (Subscriptions 2) or false (no order)
		 */
		function order_has_subscription($order_id) {
			// Subscriptions not loaded
			if (!class_exists('WC_Subscriptions_Order')) return false;

			// Subscriptions v2.0
			if (function_exists('wcs_order_contains_subscription')) {
				if (wcs_order_contains_subscription($order_id) || wcs_order_contains_renewal( $order_id ) || ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order_id ))) {
					return 2;
				} else {
					return false;
				}
			}
			
			// Subscriptions v1.5
			if (WC_Subscriptions_Order::order_contains_subscription($order_id)) {
				return 1;
			}

			return false;
		}

		/**
		 * Filter acceptable payment statuses to allow active orders to reach the receipt/CC form page
		 * when changing payment methods
		 *
		 * @param array $statuses Acceptable order statuses
		 * @param WC_Order $order The order which is being checked
		 * @return array of statuses
		 */
		function allow_payment_method_change($statuses, $order = null) {
			if (isset($_GET['is_payment_change']) && $_GET['is_payment_change'] == '1') {
				$statuses[] = 'processing';
				$statuses[] = 'completed';
				$statuses[] = 'on-hold';
				$statuses[] = 'active';
			}
			return $statuses;
		}


		/**
		 * Update the customer token IDs for a subscription after a customer used the gateway to successfully complete the payment
		 * for an automatic renewal payment which had previously failed.
		 *
		 * @param WC_Order $original_order The original order in which the subscription was purchased.
		 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
		 * @return void
		 */
		function update_failing_payment_method( $subscription, $new_renewal_order ) {
			update_post_meta( $subscription->id, '_nab_crn', get_post_meta( $new_renewal_order->id, '_nab_crn', true ) );
		}

		/**
		 * Include the payment meta data required to process automatic recurring payments so that store managers can
		 * manually set up automatic recurring payments for a customer via the Edit Subscription screen in Subscriptions v2.0+.
		 *
		 * @param array $payment_meta associative array of meta data required for automatic payments
		 * @param WC_Subscription $subscription An instance of a subscription object
		 * @return array
		 */
		public function add_subscription_payment_meta( $payment_meta, $subscription ) {
			$payment_meta[ $this->id ] = array(
				'post_meta' => array(
					'_nab_crn' => array(
						'value' => get_post_meta( $subscription->id, '_nab_crn', true ),
						'label' => 'NAB Customer Reference Number (CRN)',
					),
				),
			);
			return $payment_meta;
		}

		/**
		 * Validate the payment meta data required to process automatic recurring payments so that store managers can
		 * manually set up automatic recurring payments for a customer via the Edit Subscription screen in Subscriptions 2.0+.
		 *
		 * @param string $payment_method_id The ID of the payment method to validate
		 * @param array $payment_meta associative array of meta data required for automatic payments
		 * @return array
		 */
		public function validate_subscription_payment_meta( $payment_method_id, $payment_meta ) {
			if ( $this->id === $payment_method_id ) {
				if ( ! isset( $payment_meta['post_meta']['_nab_crn']['value'] ) || empty( $payment_meta['post_meta']['_nab_crn']['value'] ) ) {
					throw new Exception( 'A "_nab_crn" value is required.' );
				}
			}
		}
		

	}


	/**
	 * Add the NAB Transact DP gateway to WooCommerce
	 *
	 * @since 1.0.0
	 **/
	function add_nab_dp_gateway( $methods ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) && !function_exists( 'wcs_create_renewal_order' ) ) {
			include_once( 'woocommerce-nab-dp-subscriptions-deprecated.php' );
			$methods[] = 'WC_Gateway_NAB_Direct_Post_Subscriptions_Deprecated';
		} else {
			$methods[] = 'WC_Gateway_NAB_Direct_Post';
		}
		return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'add_nab_dp_gateway' );
}