<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_NAB_Direct_Post_Subscriptions_Deprecated class (Deprecated - for Subscriptions v1.x)
 * 
 * @extends WC_Gateway_NAB_Direct_Post
 */
class WC_Gateway_NAB_Direct_Post_Subscriptions_Deprecated extends WC_Gateway_NAB_Direct_Post {

	function __construct() { 
	
		parent::__construct();
		
		add_action( 'scheduled_subscription_payment_' . $this->id, array( $this, 'process_scheduled_subscription_payment' ), 10, 3 );
		add_action( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this, 'remove_renewal_order_meta' ), 10, 4 );
		add_action( 'woocommerce_subscriptions_changed_failing_payment_method_'.$this->id, array($this, 'update_failing_payment_method' ), 10, 3 );
	
	}

	/**
	 * scheduled_subscription_payment function.
	 * 
	 * @param $amount_to_charge float The amount to charge.
	 * @param $order WC_Order The WC_Order object of the order which the subscription was purchased in.
	 * @param $product_id int The ID of the subscription product for which this payment relates.
	 * @access public
	 * @return void
	 */
	function process_scheduled_subscription_payment( $amount_to_charge, $order, $product_id ) {
		
		$result = $this->process_subscription_payment( $order, $amount_to_charge );
		
		if ( is_wp_error( $result ) ) {
			
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
			
		} else {
			
			WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );
			
		}
	}

	/**
	 * Don't transfer Merchant Warrior customer/token meta when creating a parent renewal order.
	 * 
	 * @access public
	 * @param array $order_meta_query MySQL query for pulling the metadata
	 * @param int $original_order_id Post ID of the order being used to purchased the subscription being renewed
	 * @param int $renewal_order_id Post ID of the order created for renewing the subscription
	 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'
	 * @return void
	 */
	function remove_renewal_order_meta( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {

		if ( 'parent' == $new_order_role )
			$order_meta_query .= " AND `meta_key` NOT LIKE '_nab_crn' ";

		return $order_meta_query;
	}

	/**
	 * Update the customer token IDs for a subscription after a customer used the gateway to successfully complete the payment
	 * for an automatic renewal payment which had previously failed.
	 *
	 * @param WC_Order $original_order The original order in which the subscription was purchased.
	 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
	 * @return void
	 */
	function update_failing_payment_method( $original_order, $new_renewal_order ) {
		update_post_meta( $original_order->id, '_nab_crn', get_post_meta( $new_renewal_order->id, '_nab_crn', true ) );
	}

}