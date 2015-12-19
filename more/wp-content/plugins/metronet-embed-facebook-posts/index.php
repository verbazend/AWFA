<?php
/*

Plugin Name: Metronet Embed Facebook posts
Plugin URI: http://www.metronet.no/
Description: Easily embed Facebook posts into your pages
Author: Metronet / Ryan Hellyer
Version: 1.2.1
Author URI: http://www.metronet.no/

Copyright (c) 2013 Metronet


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/



/**
 * Embed Facebook Posts
 * 
 * @copyright Copyright (c), Metronet
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Metronet_Embed_Facebook_Posts {
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_shortcode( 'facebookpost', array( $this, 'facebookpost_shortcode' ) );
		
		wp_embed_register_handler(
			'facebook_photo',
			'/https:\/\/www\.facebook\.com\/photo.php(\?fbid=.*)?/i',
			array( $this, 'oembed' )
		);
		wp_embed_register_handler(
			'facebook_post',
			'/https:\/\/www\.facebook\.com\/[a-z]+\/posts\//i',
			array( $this, 'oembed' )
		);
	}
	
	/**
	 * Add oEmbed support
	 */
	public function oembed( $matches, $attr, $url, $rawattr ) {
		$string = $this->embed_code( $url );
		return apply_filters( 'embed_forbes', $string, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Javascript
	 */
	public function javascript() {
		?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=188720751303320";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script><?php
	}
	
	/**
	 * Facebook embed post shortcode
	 *
	 * @param string $atts Array containing the embed URL
	 * @return string The Facebook embedcode HTML
	 */
	public function facebookpost_shortcode( $atts ){
		$url = $atts[0];
		$string = $this->embed_code( $url );
		return $string;
	}
	
	/**
	 * Facebook embed post shortcode
	 *
	 * @param string $url The URL to be embedded
	 * @return string The Facebook embedcode HTML
	 */
	public function embed_code( $url ){
		
		// Add Javascript here, because if HTML is 
		add_action( 'wp_footer', array( $this, 'javascript'  ) );
		
		$string = '<div class="fb-post" data-href="' . esc_url( $url ) . '"></div>';
		return $string;
	}
	
}
new Metronet_Embed_Facebook_Posts;
