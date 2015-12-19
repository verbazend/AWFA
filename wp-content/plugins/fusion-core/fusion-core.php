<?php
/*
Plugin Name: Fusion Core
Plugin URI: http://www.theme-fusion.com
Description: ThemeFusion Core Plugin for ThemeFusion Themes
Version: 1.2.1
Author: ThemeFusion
Author URI: http://www.theme-fusion.com
*/

class FusionShortcodes {

    public static $_dir;
    public static $_url;

    function __construct()
    {
        // Windows-proof constants: replace backward by forward slashes. Thanks to: @peterbouwmeester
        self::$_dir     = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
        $wp_content_dir = trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) );
        $relative_url   = str_replace( $wp_content_dir, '', self::$_dir );
        $wp_content_url = ( is_ssl() ? str_replace( 'http://', 'https://', WP_CONTENT_URL ) : WP_CONTENT_URL );
        self::$_url     = trailingslashit( $wp_content_url ) . $relative_url;

    	require_once( self::$_dir .'shortcodes.php' );
    	define('FUSION_TINYMCE_URI', self::$_url . 'tinymce');
		define('FUSION_TINYMCE_DIR', self::$_dir .'tinymce');

        add_action('init', array(&$this, 'init'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('wp_ajax_fusion_shortcodes_popup', array(&$this, 'popup'));
	}

	/**
	 * Registers TinyMCE rich editor buttons
	 *
	 * @return	void
	 */
	function init()
	{
		/*if( ! is_admin() )
		{
			wp_enqueue_style( 'fusion-shortcodes', plugin_dir_url( __FILE__ ) . 'shortcodes.css' );
			wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'fusion-shortcodes-lib', plugin_dir_url( __FILE__ ) . 'js/fusion-shortcodes-lib.js', array('jquery', 'jquery-ui-accordion', 'jquery-ui-tabs') );
		}*/

		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;

		if ( get_user_option('rich_editing') == 'true' )
		{
			add_filter( 'mce_external_plugins', array(&$this, 'add_rich_plugins') );
			add_filter( 'mce_buttons', array(&$this, 'register_rich_buttons') );
		}

	}

	// --------------------------------------------------------------------------

	/**
	 * Defins TinyMCE rich editor js plugin
	 *
	 * @return	void
	 */
	function add_rich_plugins( $plugin_array )
	{
		if( is_admin() ) {
			$plugin_array['fusionShortcodes'] = FUSION_TINYMCE_URI . '/plugin.js';
		}

		return $plugin_array;
	}

	// --------------------------------------------------------------------------

	/**
	 * Adds TinyMCE rich editor buttons
	 *
	 * @return	void
	 */
	function register_rich_buttons( $buttons )
	{
		array_push( $buttons, "|", 'fusion_button' );
		return $buttons;
	}

	/**
	 * Enqueue Scripts and Styles
	 *
	 * @return	void
	 */
	function admin_init()
	{
		// css
		wp_enqueue_style( 'fusion-popup', FUSION_TINYMCE_URI . '/css/popup.css', false, '1.0', 'all' );
		wp_enqueue_style( 'jquery.chosen', FUSION_TINYMCE_URI . '/css/chosen.css', false, '1.0', 'all' );
		wp_enqueue_style( 'font-awesome', FUSION_TINYMCE_URI . '/css/font-awesome.css', false, '3.2.1', 'all' );
		wp_enqueue_style( 'wp-color-picker' );

		// js
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-livequery', FUSION_TINYMCE_URI . '/js/jquery.livequery.js', false, '1.1.1', false );
		wp_enqueue_script( 'jquery-appendo', FUSION_TINYMCE_URI . '/js/jquery.appendo.js', false, '1.0', false );
		wp_enqueue_script( 'base64', FUSION_TINYMCE_URI . '/js/base64.js', false, '1.0', false );
		wp_enqueue_script( 'jquery.chosen', FUSION_TINYMCE_URI . '/js/chosen.jquery.min.js', false, '1.0', false );
    	wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script( 'fusion-popup', FUSION_TINYMCE_URI . '/js/popup.js', false, '1.0', false );

		// Developer mode
		$dev_mode = current_theme_supports( 'fusion_shortcodes_embed' );
		if( $dev_mode ) {
			$dev_mode = 'true';
		} else {
			$dev_mode = 'false';
		}

		wp_localize_script( 'jquery', 'FusionShortcodes', array('plugin_folder' => plugins_url( '', __FILE__ ), 'dev' => $dev_mode) );
	}

	/**
	 * Popup function which will show shortcode options in thickbox.
	 *
	 * @return void
	 */
	function popup() {

		require_once( FUSION_TINYMCE_DIR . '/fusion-sc.php' );

		die();

	}

}
$fusion_shortcodes_obj = new FusionShortcodes();
?>