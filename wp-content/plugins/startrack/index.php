<?php
/*
Plugin Name: StarTrack Express WooCommerce
Plugin URI: 
Description: This WooCommerce Shipping Plugin uses StarTrack Express eServices to calculate the shpping cost estimate.
Version: 1.3
Author: Alex Kebbell
Author URI: http://www.akebbell.com
License:
*/

if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

if ( is_woocommerce_active() ) {

	function wc_startrack_init() {
		include_once( 'classes/class-startrack.php' );
	}

	add_action( 'woocommerce_shipping_init', 'wc_startrack_init' );

	function add_star_track_method( $methods ) {
		$methods[] = 'WC_StarTrack'; return $methods;
	}
	
	add_filter('woocommerce_shipping_methods', 'add_star_track_method' );
	
}