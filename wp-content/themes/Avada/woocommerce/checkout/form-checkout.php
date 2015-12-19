<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

//$woocommerce->show_messages(); ?>

<?php woocommerce_get_template('user-welcome.php'); ?>

<?php do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

if(get_option('woocommerce_enable_order_comments') != 'no' || get_option('woocommerce_calc_shipping') == 'yes') {
	$woo_shipping = true;
} elseif(get_option('woocommerce_enable_order_comments') == 'no' && get_option('woocommerce_calc_shipping') == 'no') {
	$woo_shipping = false;
}
?>

<ul class="woocommerce-side-nav woocommerce-checkout-nav">
	<li class="active">
		<a href="#billing">
			<?php _e('Billing Address' , 'Avada'); ?>
		</a>
	</li>
	<?php if($woo_shipping == true): ?>
	<li>
		<a href="#shipping">
			<?php _e('Shipping Address' , 'Avada'); ?>
		</a>
	</li>
	<?php endif; ?>
	<li>
		<a href="#payment-container">
			<?php _e('Review &amp; Payment' , 'Avada'); ?>
		</a>
	</li>
</ul>

<?php
// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', $woocommerce->cart->get_checkout_url() ); ?>

<form name="checkout" method="post" class="checkout woocommerce-content-box" action="<?php echo esc_url( $get_checkout_url ); ?>">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="" id="customer_details">

			<div class="col-1" id="billing">

				<?php do_action( 'woocommerce_checkout_billing' ); ?>

				<a href="<?php echo ($woo_shipping) ? '#shipping' : '#payment-container'; ?>" class="default button small continue-checkout"><?php _e('Continue', 'Avada'); ?></a>

			</div>

			<?php if($woo_shipping == true): ?>

			<div class="col-2" id="shipping">

				<?php do_action( 'woocommerce_checkout_shipping' ); ?>

				<a href="#payment-container" class="default button small continue-checkout"><?php _e('Continue', 'Avada'); ?></a>

			</div>

			<?php endif; ?>

		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<div id="payment-container">
		<h2 id="order_review_heading"><?php _e( 'Review &amp Payment', 'Avada' ); ?></h2>

		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>


	<script>
	  jQuery(function($) {
	    function log( message ) {
	      //$( "<div>" ).text( message ).prependTo( "#log" );
	      //$( "#log" ).scrollTop( 0 );
	    }
	 
	    jQuery( "#billing_city" ).autocomplete({
	      source: "/booking-manager/suburb.php",
	      minLength: 2,
	      select: function( event, ui ) {
	        log( ui.item ?
	          "Selected: " + ui.item.value + " aka " + ui.item.id :
	          "Nothing selected, input was " + this.value );
	          
	          var postArr = ui.item.value;
	          	  postArr = postArr.split("|");
	          	  
	          	  if(postArr.length>=2){
	          	  	//console.log("Array!");
	          	  	var suburb = postArr[0];
	          	  	var state = postArr[1];
	          	  	var postcode = postArr[2];
	          	  } else {
	          	  	//console.log("No!");
	          	  	var suburb = postArr[0];
	          	  	var state = "";
	          	  	var postcode = "";
	          	  }
	          	  $("#billing_city").val("");
	          	  $("#billing_city").val(suburb);
	          	  ui.item.value = suburb;
	          	  $("#billing_state").val(state);
	          	  //$('#billing_state[name="options"]').find('option:contains("'+state+'")').attr("selected",true);
	          	  $("#billing_postcode").val(postcode);
	      }
	    });
	  });
   </script>

