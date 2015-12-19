<?php
/*

Plugin Name: MasterPress
Plugin URI: http://masterpressplugin.com
Description: A complete CMS plugin solution for creating custom post types, taxonomies, and custom field controls to enable richly typed meta-data alongside the standard data objects in WordPress. Includes a rich object-oriented developer API to access content and data in your WordPress site, and the custom fields attached to them.
Author: Traversal
Version: 1.0.1
Author URI: http://traversal.com.au

*/

define("MASTERPRESS_VERSION", "1.0.1");

// --- Define Global Constants

define("MASTERPRESS_DEBUG", false);

define("MASTERPRESS_CHROME_FRAME", false);
define("MASTERPRESS_DOMAIN", "masterpress");

define("MASTERPRESS_MULTI", is_multisite());

if (!defined('WOOF_DIR_SEP')) {
	if (strpos(php_uname('s'), 'Win') !== false )
		define('WOOF_DIR_SEP', '\\');
	else 
		define('WOOF_DIR_SEP', '/');
}


if (!defined("WF_CONTENT_URL")) {
  if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
    define("WF_CONTENT_URL", preg_replace("/http:\/\//", "https://", WP_CONTENT_URL));
  } else {
    define("WF_CONTENT_URL", preg_replace("/https:\/\//", "http://", WP_CONTENT_URL));
  }
}



define("MASTERPRESS_DIR",  plugin_dir_path(__FILE__));
define("MASTERPRESS_IMAGES_DIR",  MASTERPRESS_DIR . WOOF_DIR_SEP . "images" . WOOF_DIR_SEP);

define("MASTERPRESS_URL",  plugins_url('/', __FILE__));
define("MASTERPRESS_IMAGES_URL",  MASTERPRESS_URL . 'images/');

$mp_content_folder = "mp";

if ( !defined("WOOF_MS_FILES") ) {
  define("WOOF_MS_FILES", get_site_option('ms_files_rewriting') );
}

$mp_upload_dir = wp_upload_dir();

if (isset($mp_upload_dir["error"]) && trim($mp_upload_dir["error"]) != "") {
  define("MASTERPRESS_NO_UPLOAD_DIR", $mp_upload_dir["error"]);
}


// first build the legacy (1.0) content directory

$mp_content_dir = "";

$mp_suffix = WOOF_DIR_SEP . $mp_content_folder . WOOF_DIR_SEP;
$mp_url_suffix = "/" . $mp_content_folder . "/";

if ( MASTERPRESS_MULTI ) {
  
  if (WOOF_MS_FILES) {
    
    $mp_content_dir = WP_CONTENT_DIR . WOOF_DIR_SEP . "blogs.dir" . WOOF_DIR_SEP . $blog_id . $mp_suffix;

	} else {

	  if (is_main_site()) {
      $mp_content_dir = WP_CONTENT_DIR . $mp_suffix; 
	  } else {
  	  $mp_content_dir = WP_CONTENT_DIR . WOOF_DIR_SEP . "uploads" . WOOF_DIR_SEP . "sites" . WOOF_DIR_SEP . $blog_id . $mp_suffix;
    }
	
	}

} else {
  $mp_content_dir = WP_CONTENT_DIR . $mp_suffix; 
}


// 1.0.1 - if this directory doesn't exist, use the new directory format, which is underneath the current configured upload dir

if (!WOOF_MS_FILES) {

	if (file_exists($mp_content_dir)) {
		if (!MASTERPRESS_MULTI) {
	  	define("MASTERPRESS_LEGACY_DIR", true);
		}
	}

	if (!defined("MASTERPRESS_LEGACY_DIR")) {
	  $mp_content_dir = $mp_upload_dir["basedir"] . $mp_suffix;
	}

}

define("MASTERPRESS_CONTENT_DIR", $mp_content_dir);

  
$mp_content_url = $mp_upload_dir["baseurl"] . $mp_url_suffix;

// check if the legacy (1.0) directory exists

if ( defined("MASTERPRESS_LEGACY_DIR") || WOOF_MS_FILES ) {

  if ( MASTERPRESS_MULTI ) {
  
    if ( WOOF_MS_FILES ) {
  
      $mp_content_url = WF_CONTENT_URL . "/blogs.dir/" . $blog_id . "/" . $mp_content_folder . '/';
			
		} else {

		  if (is_main_site()) {
        $mp_content_url = WF_CONTENT_URL . '/' . $mp_content_folder . '/'; 
		  } else {
    	  $mp_content_url = WF_CONTENT_URL . "/uploads/sites/" . $blog_id . '/' . $mp_content_folder . '/';
      }
		}

  } else {
    $mp_content_url = WF_CONTENT_URL . '/' . $mp_content_folder . '/'; 
  }

} 

define("MASTERPRESS_CONTENT_URL", $mp_content_url);

if ( !defined("MASTERPRESS_MULTISITE_SHARING") ) {
  define("MASTERPRESS_MULTISITE_SHARING", false);
}

if (!defined("MASTERPRESS_LEGACY_DIR")) {

	$mp_legacy_files_dir = MASTERPRESS_CONTENT_DIR . "uploads";

	if (file_exists($mp_legacy_files_dir)) {
		define("MASTERPRESS_LEGACY_DIR", true);
	}

}


if ( MASTERPRESS_MULTISITE_SHARING ) {
  
   $main_blog_id = 1;
   
   if (defined("BLOG_ID_CURRENT_SITE")) {
     $main_blog_id = BLOG_ID_CURRENT_SITE;
   }
    
  // masterplans are shared across all sites in the network
  
  if (defined("MASTERPRESS_LEGACY_DIR")) {
  
		// check if the main site is non-legacy 

    switch_to_blog( $main_blog_id );
    
    $main_upload_dir = wp_upload_dir();

		if (file_exists($main_upload_dir["basedir"] . WOOF_DIR_SEP . $mp_content_folder)) {
			$mp_main_not_legacy = true;
		}
				
		restore_current_blog();
		
		if (!isset($mp_main_not_legacy)) {
			define("MASTERPRESS_GLOBAL_CONTENT_URL", WF_CONTENT_URL . "/" . $mp_content_folder . "/"); 
	    define("MASTERPRESS_GLOBAL_CONTENT_DIR", WP_CONTENT_DIR . WOOF_DIR_SEP . $mp_content_folder . WOOF_DIR_SEP); 
  	}

		
  } 

	if (!defined("MASTERPRESS_LEGACY_DIR") || isset($mp_main_not_legacy)) { 
    
    switch_to_blog( $main_blog_id );
    
    $main_upload_dir = wp_upload_dir();
    
    define("MASTERPRESS_GLOBAL_CONTENT_URL", $main_upload_dir["baseurl"] . "/" . $mp_content_folder . "/" );
    define("MASTERPRESS_GLOBAL_CONTENT_DIR", $main_upload_dir["basedir"] . WOOF_DIR_SEP . $mp_content_folder . WOOF_DIR_SEP );
    
    restore_current_blog();
    
  }
  
} else {
  
  define("MASTERPRESS_GLOBAL_CONTENT_URL", MASTERPRESS_CONTENT_URL ); 
  define("MASTERPRESS_GLOBAL_CONTENT_DIR", MASTERPRESS_CONTENT_DIR ); 

}


define("MASTERPRESS_TMP_FOLDER", "tmp"); 
define("MASTERPRESS_TMP_URL", MASTERPRESS_GLOBAL_CONTENT_URL . MASTERPRESS_TMP_FOLDER . '/'); 
define("MASTERPRESS_TMP_DIR", MASTERPRESS_GLOBAL_CONTENT_DIR . MASTERPRESS_TMP_FOLDER . WOOF_DIR_SEP); 

define( "MASTERPRESS_UPDATE_API", "https://masterpressplugin.com/update/" ); 

define("MASTERPRESS_CONTENT_MENU_ICONS_FOLDER", "menu-icons"); 

define("MASTERPRESS_CONTENT_MENU_ICONS_URL", MASTERPRESS_GLOBAL_CONTENT_URL . MASTERPRESS_CONTENT_MENU_ICONS_FOLDER . '/'); 
define("MASTERPRESS_CONTENT_MENU_ICONS_DIR", MASTERPRESS_GLOBAL_CONTENT_DIR . MASTERPRESS_CONTENT_MENU_ICONS_FOLDER . WOOF_DIR_SEP); 

define("MASTERPRESS_CONTENT_IMAGE_CACHE_FOLDER", "image-cache"); 


define("MASTERPRESS_CONTENT_IMAGE_CACHE_URL", MASTERPRESS_CONTENT_URL . MASTERPRESS_CONTENT_IMAGE_CACHE_FOLDER . '/'); 
define("MASTERPRESS_CONTENT_IMAGE_CACHE_DIR", MASTERPRESS_CONTENT_DIR . MASTERPRESS_CONTENT_IMAGE_CACHE_FOLDER . WOOF_DIR_SEP); 

define("MASTERPRESS_CONTENT_IMAGE_FROM_URL_FOLDER", "image-from-url"); 
define("MASTERPRESS_CONTENT_IMAGE_FROM_URL_URL", MASTERPRESS_CONTENT_URL . MASTERPRESS_CONTENT_IMAGE_FROM_URL_FOLDER . '/'); 
define("MASTERPRESS_CONTENT_IMAGE_FROM_URL_DIR", MASTERPRESS_CONTENT_DIR . MASTERPRESS_CONTENT_IMAGE_FROM_URL_FOLDER . WOOF_DIR_SEP); 

define("MASTERPRESS_CONTENT_FILE_FROM_URL_FOLDER", "file-from-url"); 
define("MASTERPRESS_CONTENT_FILE_FROM_URL_URL", MASTERPRESS_CONTENT_URL.MASTERPRESS_CONTENT_FILE_FROM_URL_FOLDER.'/'); 
define("MASTERPRESS_CONTENT_FILE_FROM_URL_DIR", MASTERPRESS_CONTENT_DIR.MASTERPRESS_CONTENT_FILE_FROM_URL_FOLDER.WOOF_DIR_SEP); 


define("MASTERPRESS_CONTENT_MPFT_CACHE_FOLDER", "mp-cache"); 

define("MASTERPRESS_CONTENT_MPFT_CACHE_URL", MASTERPRESS_GLOBAL_CONTENT_URL.MASTERPRESS_CONTENT_MPFT_CACHE_FOLDER.'/'); 
define("MASTERPRESS_CONTENT_MPFT_CACHE_DIR", MASTERPRESS_GLOBAL_CONTENT_DIR.MASTERPRESS_CONTENT_MPFT_CACHE_FOLDER.WOOF_DIR_SEP); 

define("MASTERPRESS_CONTENT_MASTERPLANS_FOLDER", "masterplans"); 

define("MASTERPRESS_CONTENT_MASTERPLANS_URL", MASTERPRESS_GLOBAL_CONTENT_URL.MASTERPRESS_CONTENT_MASTERPLANS_FOLDER.'/'); 
define("MASTERPRESS_CONTENT_MASTERPLANS_DIR", MASTERPRESS_GLOBAL_CONTENT_DIR.MASTERPRESS_CONTENT_MASTERPLANS_FOLDER.WOOF_DIR_SEP); 

define("MASTERPRESS_EXTENSIONS_FOLDER", "extensions"); 

define("MASTERPRESS_EXTENSIONS_URL", MASTERPRESS_GLOBAL_CONTENT_URL.MASTERPRESS_EXTENSIONS_FOLDER.'/'); 
define("MASTERPRESS_EXTENSIONS_DIR", MASTERPRESS_GLOBAL_CONTENT_DIR.MASTERPRESS_EXTENSIONS_FOLDER.WOOF_DIR_SEP); 

define("MASTERPRESS_EXTENSIONS_FIELD_TYPES_FOLDER", "field-types"); 
define("MASTERPRESS_EXTENSIONS_ICONS_FOLDER", "icons"); 

define("MASTERPRESS_EXTENSIONS_FIELD_TYPES_URL", MASTERPRESS_GLOBAL_CONTENT_URL.MASTERPRESS_EXTENSIONS_FOLDER.'/'.MASTERPRESS_EXTENSIONS_FIELD_TYPES_FOLDER.'/'); 
define("MASTERPRESS_EXTENSIONS_FIELD_TYPES_DIR", MASTERPRESS_GLOBAL_CONTENT_DIR.MASTERPRESS_EXTENSIONS_FOLDER.WOOF_DIR_SEP.MASTERPRESS_EXTENSIONS_FIELD_TYPES_FOLDER.WOOF_DIR_SEP); 

define("MASTERPRESS_EXTENSIONS_ICONS_URL", MASTERPRESS_GLOBAL_CONTENT_URL.MASTERPRESS_EXTENSIONS_FOLDER.'/'.MASTERPRESS_EXTENSIONS_ICONS_FOLDER.'/'); 
define("MASTERPRESS_EXTENSIONS_ICONS_DIR", MASTERPRESS_GLOBAL_CONTENT_DIR.MASTERPRESS_EXTENSIONS_FOLDER.WOOF_DIR_SEP.MASTERPRESS_EXTENSIONS_ICONS_FOLDER.WOOF_DIR_SEP); 




if ( defined("MASTERPRESS_LEGACY_DIR") ) {
  define("MASTERPRESS_CONTENT_UPLOADS_FOLDER", "uploads"); 
} else {
  define("MASTERPRESS_CONTENT_UPLOADS_FOLDER", "files"); 
}

define("MASTERPRESS_CONTENT_UPLOADS_URL", MASTERPRESS_CONTENT_URL . MASTERPRESS_CONTENT_UPLOADS_FOLDER . '/'); 
define("MASTERPRESS_CONTENT_UPLOADS_DIR", MASTERPRESS_CONTENT_DIR . MASTERPRESS_CONTENT_UPLOADS_FOLDER . WOOF_DIR_SEP); 

// --- Create Menu Structure

include_once("core/other/mpu.php");

// --- Include MEOW Framework (which includes its dependencies WOOF and the base field types)

include_once("core/api/meow/meow.php");

// legacy patch for Inflector

class MPU_Inflector extends WOOF_Inflector {};



MPU::incl("model/mpm.php");
MPU::incl("other/mpft.php");
MPU::incl("other/mpft-file-base.php");


// --- Include Models
MPM::incl("field-set");
MPM::incl("field");
MPM::incl("post-type");
MPM::incl("role-field-set");
MPM::incl("shared-field-set");
MPM::incl("site-field-set");
MPM::incl("template-field-set");
MPM::incl("taxonomy-field-set");
MPM::incl("taxonomy");
MPM::incl("template");

MPU::incl("controller/mpc.php");

$mp_init_priority = 10;

if (is_admin()) {
  
  $mp_init_priority = 10;
  
  // --- Include Base Classes
  MPU::incl("view/mpv.php");
  MPV::incl("meta");

  add_action('wp_loaded',  array('MasterPress', 'wp_loaded'));

}

MPC::incl("post");
MPC::incl("meta");

// register_activation_hook( __FILE__, array('MasterPress', 'install') );

add_filter('contextual_help', array("MasterPress", "contextual_help"), 10, 3);

add_filter('media_upload_default_tab', array("MasterPress", "media_upload_default_tab"));

add_action('init',  array('MasterPress', 'init'), $mp_init_priority);

// add role editing in user listings
add_action('user_row_actions', array( 'MasterPress', 'user_row_actions' ), 10, 2 );

add_action('admin_bar_menu',  array('MasterPress', 'admin_bar_menu'), 999);
add_action('admin_notices',  array('MasterPress', 'admin_notices'));

add_action('admin_enqueue_scripts',  array('MasterPress', 'admin_enqueue_scripts'));

add_action("restrict_manage_posts", array("MasterPress", "restrict_manage_posts"));
add_action('wp_ajax_masterpress_dispatch',  array('MasterPress', 'dispatch_ajax'));
add_action('get_media_item_args', array('MasterPress', 'get_media_item_args'));

add_filter("posts_request", array("MasterPress", "posts_request"));

add_filter("pre_get_posts", array("MasterPress", "pre_get_posts"));

add_filter('pre_set_site_transient_update_plugins', array('MasterPress', 'pre_set_site_transient_update_plugins'));
add_filter('plugins_api', array('MasterPress', 'plugins_api'), 10, 3);

add_action("admin_print_styles", array("MasterPress", "admin_print_styles"));
  
add_filter('template_include', array('MasterPress', 'template_include'));


class MasterPress {

  public static $messages = array();
  
  public static $version = MASTERPRESS_VERSION;

  public static $ajax_action;
  
  public static $action;
  public static $parent;
  public static $gparent;
  public static $id;
  public static $from;
  
  public static $flush_rules;
    
  public static $is_masterplan;
  public static $model_class;
  public static $view_class;

  public static $view;
  public static $model;

  public static $cap_mode;
  
  public static $view_method;
  public static $controller_key;
  public static $controller;
  public static $post_types;
  public static $taxonomies;

  public static $all_taxonomies;
  public static $all_post_types;

  public static $sites;
  
  public static $mustache;
    
  public static $context = "string";
  
  public static $suffix;
  
  protected static $menu_icons = array();
  
  public static $admin_notices = array();
  public static $script;
  public static $is_user_editor;
  public static $is_user_create;
  public static $is_term_editor;
  public static $is_site_content_editor;
  public static $is_term_manage;
  public static $is_post_edit;
  public static $is_post_new;
  public static $is_post_editor;
  public static $is_post_manage;

  public static $capabilities = array(
      "manage_masterplan", 
      "manage_post_types", 
      "manage_taxonomies", 
      "manage_templates", 
      "manage_user_roles", 
      "manage_site_field_sets", 
      "manage_shared_field_sets", 
      "manage_mp_settings",
      "manage_mp_tools",
  		'export_masterplan',
  		'import_masterplan',
  		'backup_masterplan',
  		'restore_masterplan',
      "create_post_types", 
      "edit_post_types", 
      "delete_post_types", 
      "manage_post_type_field_sets", 
      "create_post_type_field_sets", 
      "edit_post_type_field_sets", 
      "delete_post_type_field_sets", 
      "create_post_type_fields", 
      "edit_post_type_fields", 
      "delete_post_type_fields", 
      "create_taxonomies", 
      "edit_taxonomies", 
      "delete_taxonomies", 
      "manage_taxonomy_field_sets", 
      "create_taxonomy_field_sets", 
      "edit_taxonomy_field_sets", 
      "delete_taxonomy_field_sets", 
      "create_taxonomy_fields", 
      "edit_taxonomy_fields", 
      "delete_taxonomy_fields", 
      "edit_templates", 
      "manage_template_field_sets", 
      "create_template_field_sets", 
      "edit_template_field_sets", 
      "delete_template_field_sets", 
      "create_template_fields", 
      "edit_template_fields", 
      "delete_template_fields", 
      "create_user_roles", 
      "edit_user_roles", 
      "delete_user_roles", 
      "manage_user_role_field_sets", 
      "create_user_role_field_sets", 
      "edit_user_role_field_sets", 
      "delete_user_role_field_sets", 
      "create_user_role_fields", 
      "edit_user_role_fields", 
      "delete_user_role_fields", 
      "create_site_field_sets", 
      "delete_site_field_sets", 
      "edit_site_field_sets", 
      "create_site_fields", 
      "delete_site_fields", 
      "edit_site_fields", 
      "create_shared_field_sets", 
      "edit_shared_field_sets",
      "delete_shared_field_sets",
      "create_shared_fields", 
      "edit_shared_fields",
      "delete_shared_fields" 
  );
  
  public static $front_page_post_types;
  public static $front_page_cpt = false;
  
  public static $masterplan;

  public static $debug_posts_request = false;

  protected static $page_menu_index = 20;
  protected static $post_menu_index = 5;

  protected static $post_submenu_index = "edit.php";
  protected static $page_submenu_index = "edit.php?post_type=page";

  protected static $post_submenu_all_index = 5;
  protected static $page_submenu_all_index = 5;
  
  public static function pre_get_posts(&$query) {
    
    $pof = get_option("page_on_front");
    $page_id = $query->query_vars["page_id"];
    
    if ($pof != 0 && $page_id != 0 && $query->query_vars["page_id"] == get_option("page_on_front")) {
      if (isset(self::$front_page_post_types) && count(self::$front_page_post_types) > 1) {
    
        $query->is_page = false;
        $query->is_single = true;

        $query->query_vars['post_type'] = self::$front_page_post_types;
        self::$front_page_cpt = true;
        
        remove_filter('pre_get_posts', array('MasterPress', 'pre_get_posts'));
      }
    }
    
  }
  
  public static function ini_get_bool( $a ) {
    // ini_get_bool() credit: nicolas dot grekas+php at gmail dot com
  	$b = ini_get($a);
  	switch (strtolower($b)) {
  		case 'on':
  		case 'yes':
  		case 'true':
  			return 'assert.active' !== $a;

  		case 'stdout':
  		case 'stderr':
  			return 'display_errors' === $a;

  		default:
  			return (bool) (int) $b;
  	}
  }
  
  public static function ini_get_setting( $a, $yes = null, $no = null ) {
    if (is_null($yes)) {
      $yes = __("enabled", MASTERPRESS_DOMAIN);
    }

    if (is_null($no)) {
      $no = __("disabled", MASTERPRESS_DOMAIN);
    }
    
    return self::ini_get_bool( $a ) ? $yes : $no;
  }
  
  public static function ini_get_off_on( $a ) {
    return self::ini_get_setting( $a, __("on", MASTERPRESS_DOMAIN ), __("off", MASTERPRESS_DOMAIN ));
  }
  
  
  public static function posts_request($sql) {
    if (self::$debug_posts_request) {
      pr($sql);
    }
  
    return $sql;
  }

  public static function has_iris() {
    return self::use_new_media();
  }
  
  public static function use_new_media() {
    return function_exists("wp_enqueue_media");
  }
  
  public static function enqueue_mce($editor_flag = true) {
      
    add_thickbox();

    if (self::use_new_media()) {
      // media in Wordpress 3.5
      wp_enqueue_media();
    } else {
      wp_enqueue_script('media-upload');
      add_action( 'admin_print_footer_scripts', array("MasterPress", 'media_buttons'), 51 );
    }
    

    wp_enqueue_script('wpdialogs');
    wp_enqueue_script('wpdialogs-popup');
    wp_enqueue_script('wplink');
    
     
    if ($editor_flag) {
      if (function_exists("wp_editor")) {
        // TODO - work out a better way to do this when WP3.3 hits release (perhaps the API might be modified by then)
        add_action( 'admin_print_footer_scripts', array("MasterPress", 'editor_patch') );
      } else {
        add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
      }
    }
    
    

  }

  public static function admin_enqueue_scripts() {
    
    global $wf;
  
    global $menu;
    global $submenu;


    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-widget');
    wp_enqueue_script('jquery-ui-mouse');
    wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('jquery-ui-selectable');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-resizable');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-effects-pulsate');

		wp_enqueue_script('thickbox'); // include thickbox for tinyMCE
		wp_enqueue_script('editor-functions'); // for tinyMCE

		wp_enqueue_script("woof-html", MASTERPRESS_URL."core/api/woof/woof-html.js", array( "jquery" ) );
    
    if (WP_DEBUG || MASTERPRESS_DEBUG) {

      wp_enqueue_script('jquery-fancybox');
      wp_enqueue_script('jquery-metadata');
      wp_enqueue_script('jquery-scroll-to');
      wp_enqueue_script('handlebars');
      wp_enqueue_script('inflection');
  		wp_enqueue_script('select2');
      wp_enqueue_script('date-js');


			wp_enqueue_script("sprintf", MASTERPRESS_URL."js/src/sprintf-0.7-beta1.js");
			wp_enqueue_script("jquery-reveal", MASTERPRESS_URL."js/src/jquery.reveal.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-affix", MASTERPRESS_URL."js/src/jquery.affix.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-linkify", MASTERPRESS_URL."js/src/jquery.linkify.js", array( "jquery" ) , MASTERPRESS_VERSION);
			wp_enqueue_script("jquery-tabs", MASTERPRESS_URL."js/src/jquery.mp-tabs.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-auto-grow-input", MASTERPRESS_URL."js/src/jquery.auto-grow-input.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-inputmask", MASTERPRESS_URL."js/src/jquery.inputmask.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-client", MASTERPRESS_URL."js/src/jquery.client.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-limit-maxlength", MASTERPRESS_URL."js/src/jquery.limit-maxlength.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-inputspinner", MASTERPRESS_URL."js/src/jquery.inputspinner.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("valums-file-uploader", MASTERPRESS_URL."js/src/fileuploader.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-mp-select2", MASTERPRESS_URL."js/src/jquery.mp-select2.js", array( "jquery", "jquery-ui-core" ), MASTERPRESS_VERSION );

			wp_enqueue_script("masterpress-mpv", MASTERPRESS_URL."js/src/mpv.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("masterpress-mp-uploader", MASTERPRESS_URL."js/src/mp-file-uploader.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("masterpress-mpft", MASTERPRESS_URL."js/src/mpft.js", array( "jquery", "jquery-ui-core", "jquery-ui-widget" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-ui-datetimepicker", MASTERPRESS_URL."js/src/jquery-ui-timepicker-addon.js", array( "jquery", "jquery-ui-datepicker" ), MASTERPRESS_VERSION );
			wp_enqueue_script("jquery-autoresize", MASTERPRESS_URL."js/src/jquery.autoresize.min.js", array( "jquery" ), MASTERPRESS_VERSION );
			wp_enqueue_script("masterpress-mp", MASTERPRESS_URL."js/src/mp.js", array( "jquery", "jquery-ui-core" ), MASTERPRESS_VERSION );
      wp_enqueue_script("masterpress-mpv-meta", MASTERPRESS_URL."js/src/mpv-meta.js", array( "jquery", "jquery-ui-core", "jquery-ui-widget"), MASTERPRESS_VERSION ); 

	  } else {
	    
			wp_enqueue_script("masterpress-plugins", MASTERPRESS_URL."js/mp.all.min.js", array( "jquery", "jquery-ui-core", "jquery-ui-widget", "jquery-ui-datepicker" ), MASTERPRESS_VERSION);		    

    }

    // Add library stylesheets

    wp_enqueue_style("jquery-fancybox");
    wp_enqueue_style("select2");
    
    // Add MasterPress UI stylesheet
		wp_enqueue_style("mpv", MASTERPRESS_URL."css/mp.css", array(), MASTERPRESS_VERSION);



    // setup menu label overrides (wordpress doesn't respect the post type labels currently)

    $na = false;
    
    if (function_exists("is_network_admin") && is_network_admin()) {
      $na = true;
    }
    
    if (!$na) {
      
      if (isset(MasterPress::$post_types["post"])) {
        
        if (isset($menu[self::$post_menu_index][0], $menu[self::$post_menu_index][1])) {
          if ($menu[self::$post_menu_index][1] == "edit_posts") {
            $label = MasterPress::$post_types["post"]->labels["menu_name"];
          
            if ($label != __("Posts")) {
              $menu[self::$post_menu_index][0] = $label;
            }
          }
      
        }

    

        if (isset($submenu[self::$post_submenu_index][self::$post_submenu_all_index][0])) {
          if (isset(MasterPress::$post_types["post"]->labels["all_items"])) {
            $label = MasterPress::$post_types["post"]->labels["all_items"];

            if ($label != "" && $label != __("All Posts")) {
              $submenu[self::$post_submenu_index][self::$post_submenu_all_index][0] = MasterPress::$post_types["post"]->labels["all_items"];
            }
          }
        }
      
      }
    
    
      if (isset(MasterPress::$post_types["page"])) {

      
        if (isset($menu[self::$page_menu_index][0], $menu[self::$page_menu_index][1])) {
          if ($menu[self::$page_menu_index][1] == "edit_pages") {
            $label = MasterPress::$post_types["page"]->labels["menu_name"];
          
            if ($label != __("Pages")) {
              $menu[self::$page_menu_index][0] = $label;
            }
          }
        }
    
        if (isset($submenu[self::$page_submenu_index][self::$page_submenu_all_index][0])) {
          if (isset(MasterPress::$post_types["page"]->labels["all_items"])) {
            $label = MasterPress::$post_types["page"]->labels["all_items"];

            if ($label != "" && $label != __("All Pages")) {
              $submenu[self::$page_submenu_index][self::$page_submenu_all_index][0] = MasterPress::$post_types["page"]->labels["all_items"];
            }
          }
        }

      }

    }


    if (self::is_post_editor()) {
      
      self::enqueue_mce(!$wf->the->type->supports("editor"));
      
      foreach (MPC_Post::assigned_field_types() as $type) {

        if ($type_class = MPFT::type_class($type)) {
          call_user_func( array($type_class, "enqueue") );
        }
      
      }

    } else if (self::is_term_editor() || self::is_term_manage()) {
      
      wp_enqueue_style("editor-buttons");

      self::enqueue_mce();
      
      MPC::incl("term");
      
      foreach (MPC_Term::assigned_field_types() as $type) {

        if ($type_class = MPFT::type_class($type)) {
          call_user_func( array($type_class, "enqueue") );
        }
      
      }
      
    } else if (self::is_user_editor()) {

      wp_enqueue_style("editor-buttons");

      self::enqueue_mce();
      
      foreach (MPC_User::assigned_field_types() as $type) {

        if ($type_class = MPFT::type_class($type)) {
          call_user_func( array($type_class, "enqueue") );
        }
      
      }
    
    } else if (self::is_site_content_editor()) {
      
      MPV::incl("site-content");
      
      wp_enqueue_style("editor-buttons");

      self::enqueue_mce();
      
      foreach (MPC_SiteContent::assigned_field_types() as $type) {

        if ($type_class = MPFT::type_class($type)) {
          call_user_func( array($type_class, "enqueue") );
        }
      
      }
      
    } 
    
  }

  public static function editor_patch() {
    ?>
    <div style="display: none;">
    <?php echo wp_editor('', 'content'); ?>
    </div>
    <?php
  }
  
  public static function user_row_actions( $actions, $user ) {
		global $wf;
		
		if ( current_user_can( 'manage_options' ) )
		  
		  $roles = $wf->user($user->ID)->roles();
		    
		  foreach ($roles as $role) {
			  $actions[] = '<a href="' . MasterPress::admin_url("roles", "edit", "id=".$role->id()) . '">' . sprintf( __( 'Edit %s Role', MASTERPRESS_DOMAIN ), $role->name() ) . '</a>';
      }
    
		  return $actions;
	}


  public static function media_buttons() {
    print '<div class="mp-media-buttons">';
    do_action( 'media_buttons', "content" );
    print '</div>'; 
  }
  
	public static function get_media_item_args( $args ) {
		$args[ 'send' ] = true;
		return $args;
	}
	
	public static function mem($label) {
		pr( "MEMORY $label - ". WOOF_File::format_filesize( memory_get_usage(), "AUTO" ) . "<br/>" );
  }
  
  
  public static function wp_loaded() {
  
    // setup menu icon overrides
    
    global $wp_post_types;
    
    foreach (MasterPress::$post_types as $post_type) {
      
      if ($post_type->_external && $post_type->menu_icon) {
        if (isset($wp_post_types[$post_type->name])) {
          //$wp_post_types[$post_type->name]->menu_icon = get_user_option("admin_color") == "classic" ? MPU::blue_menu_icon_url($post_type->menu_icon, false) : MPU::gray_menu_icon_url($post_type->menu_icon, false);
        }
      }
      
    }
      
  }
  
	public static function init() {
    
    global $meow_provider;
    
    load_plugin_textdomain( MASTERPRESS_DOMAIN, false, "masterpress/languages" );
    
    do_action("mp_pre_init");

    self::$cap_mode = get_site_option("mp_cap", "standard");
    
    global $wf;
    
    global $wpdb;
    
    $snp = explode("/", $_SERVER["SCRIPT_NAME"]);
    
    self::$script = basename($snp[count($snp) - 1], ".php");
    
    
    // Register useful libraries separately, so they may be used by themes

    if (WP_DEBUG || MASTERPRESS_DEBUG) {
    
      // legacy picturefill
      wp_register_script('mp-matchmedia',      MASTERPRESS_URL.'js/src/matchmedia.js',                 array(), MASTERPRESS_VERSION);
      wp_register_script('mp-picturefill',     MASTERPRESS_URL.'js/src/picturefill.js',                array('jquery', 'mp-matchmedia'), MASTERPRESS_VERSION);
      

      wp_register_script('jquery-imageload', MASTERPRESS_URL.'js/src/jquery.imageload.js',             array('jquery'), MASTERPRESS_VERSION);
      // new jQuery picturefill
      wp_register_script('jquery-picturefill', MASTERPRESS_URL.'js/src/jquery.picturefill.js',         array('jquery', 'jquery-imageload'), MASTERPRESS_VERSION);

      wp_register_script('jquery-lazyload',    MASTERPRESS_URL.'js/src/jquery.lazyload.js',            array('jquery'), MASTERPRESS_VERSION);
      
      wp_register_script('jquery-fancybox',    MASTERPRESS_URL.'js/src/jquery.fancybox-1.3.4.js',      array('jquery'), MASTERPRESS_VERSION);
      wp_register_script('jquery-scroll-to',   MASTERPRESS_URL.'js/src/jquery.scroll-to.js',           array('jquery'), MASTERPRESS_VERSION);
      wp_register_script('jquery-metadata',    MASTERPRESS_URL.'js/src/jquery.metadata.js',            array('jquery'), MASTERPRESS_VERSION);
      wp_register_script('select2',            MASTERPRESS_URL.'js/src/select2.js',                    array('jquery'), MASTERPRESS_VERSION);
      
      wp_register_script('codemirror',         MASTERPRESS_URL.'js/src/codemirror.js',                 array(), MASTERPRESS_VERSION);
      wp_register_script('codemirror-modes',   MASTERPRESS_URL.'js/codemirror.modes.min.js',           array(), MASTERPRESS_VERSION);
      wp_register_script('mediaelement',      MASTERPRESS_URL.'js/src/mediaelement-and-player.js',     array(), MASTERPRESS_VERSION);
      wp_register_script('handlebars',         MASTERPRESS_URL.'js/src/handlebars-1.0.0.beta.6.js',    array(), MASTERPRESS_VERSION);
      wp_register_script('inflection',         MASTERPRESS_URL.'js/src/inflection.js',                 array(), MASTERPRESS_VERSION);
      wp_register_script('date-js',            MASTERPRESS_URL.'js/src/date.js',                       array(), MASTERPRESS_VERSION);
      
      wp_register_script('mp-iris',     	   MASTERPRESS_URL.'js/src/iris.js',                	   array('jquery', "jquery-ui-core", "jquery-ui-widget"), MASTERPRESS_VERSION);
    
    } else {

      // legacy picturefill (not enqueued)
      wp_register_script('mp-matchmedia',      MASTERPRESS_URL.'js/matchmedia.min.js',                 array(), MASTERPRESS_VERSION);
      wp_register_script('mp-picturefill',     MASTERPRESS_URL.'js/picturefill.min.js',                array('jquery', 'mp-matchmedia'), MASTERPRESS_VERSION);

      wp_register_script('jquery-imageload', MASTERPRESS_URL.'js/jquery.imageload.min.js',             array('jquery'), MASTERPRESS_VERSION);

      // new jQuery picturefill
      wp_register_script('jquery-picturefill', MASTERPRESS_URL.'js/jquery.picturefill.min.js',         array('jquery', 'jquery-imageload'), MASTERPRESS_VERSION);

      wp_register_script('jquery-lazyload',    MASTERPRESS_URL.'js/jquery.lazyload.min.js',            array('jquery'), MASTERPRESS_VERSION);

      wp_register_script('jquery-fancybox',    MASTERPRESS_URL.'js/jquery.fancybox-1.3.4.min.js',      array('jquery'), MASTERPRESS_VERSION);
      wp_register_script('jquery-scroll-to',   MASTERPRESS_URL.'js/jquery.scroll-to.min.js',           array('jquery'), MASTERPRESS_VERSION);
      wp_register_script('jquery-metadata',    MASTERPRESS_URL.'js/jquery.metadata.min.js',            array('jquery'), MASTERPRESS_VERSION);
      wp_register_script('select2',            MASTERPRESS_URL.'js/select2.min.js',                    array('jquery'), MASTERPRESS_VERSION);
      wp_register_script('date-js',            MASTERPRESS_URL.'js/date.min.js',                       array(), MASTERPRESS_VERSION);

      wp_register_script('mediaelement',       MASTERPRESS_URL.'js/mediaelement-and-player.min.js',    array(), MASTERPRESS_VERSION);
      wp_register_script('codemirror',         MASTERPRESS_URL.'js/codemirror.min.js',                 array(), MASTERPRESS_VERSION);
      wp_register_script('codemirror-modes',   MASTERPRESS_URL.'js/codemirror.modes.min.js',           array(), MASTERPRESS_VERSION);
      wp_register_script('handlebars',         MASTERPRESS_URL.'js/handlebars-1.0.0.beta.6.min.js',    array(), MASTERPRESS_VERSION);
      wp_register_script('inflection',         MASTERPRESS_URL.'js/inflection.min.js',                 array(), MASTERPRESS_VERSION);

      if (!self::has_iris()) {
        wp_register_script('mp-iris',     	   MASTERPRESS_URL.'js/iris.min.js',                	   array('jquery', "jquery-ui-core", "jquery-ui-widget"), MASTERPRESS_VERSION);
      }
    }
    
    
    wp_register_style('jquery-fancybox',       MASTERPRESS_URL.'css/fancybox.css',                    array(), MASTERPRESS_VERSION);
    wp_register_style('select2',               MASTERPRESS_URL.'css/select2.css',                     array(), MASTERPRESS_VERSION);
    wp_register_style('codemirror',            MASTERPRESS_URL.'css/codemirror.css',                  array(), MASTERPRESS_VERSION);
    wp_register_style('codemirror-themes',     MASTERPRESS_URL.'css/codemirror.themes.min.css',       array(), MASTERPRESS_VERSION);
    wp_register_style('mediaelement',          MASTERPRESS_URL.'css/mediaelementplayer.min.css',      array(), MASTERPRESS_VERSION);

    
    
    if ( is_admin() ) {

      // run an upgrade (if required)

      MasterPress::upgrade();

      if (defined('MASTERPRESS_NO_UPLOAD_DIR')) {

        MPV::err( 
          sprintf(  
            __( "<b>Important: </b>When trying to determine the location of your uploads directory, WordPress reported the following error: <code>%s</code>That is, your WordPress installation <b>does not currently contain a writable uploads directory</b>, so MasterPress will not function correctly as it cannot create its own required sub-folders.<br>To avoid further problems, you must correct this before creating or editing anything with MasterPress.<br>", MASTERPRESS_DOMAIN ),
            MASTERPRESS_NO_UPLOAD_DIR
          )
        );
        
      } else {
        self::create_directories();
      }
      
      add_action('admin_menu', array('MasterPress', 'admin_menu'));

      wp_enqueue_script("jquery-lazyload");
      wp_enqueue_script("jquery-imageload");
      wp_enqueue_script("jquery-picturefill");
      

      // Add filters for specific screens
      
      if (self::$script == "options-reading") {
        add_filter( 'get_pages',  array("MasterPress", 'add_front_page_supports' ) );
      }
      
      if (self::is_user_editor()) {
        add_filter( 'mce_external_plugins', array( "MasterPress", 'mce_plugins' ) );
        MPC::incl("user");
        add_action( 'show_user_profile', array("MPC_User", "field_sets" ));
        add_action( 'edit_user_profile', array("MPC_User", "field_sets" ));
      }
      
      if (self::is_term_editor() || self::is_term_manage()) {
        MPC::incl("term");
        if (isset($_GET["taxonomy"])) {
          $taxonomy = $_GET["taxonomy"];
        }
      }
      
      if (self::is_term_editor()) {
        add_filter( 'mce_external_plugins', array( "MasterPress", 'mce_plugins' ) );
        // add field UI hook
        add_action( $taxonomy."_edit_form_fields", array("MPC_Term", "field_sets"), 10, 2 );
      }

      if (self::is_term_manage()) {
        add_filter( 'mce_external_plugins', array( "MasterPress", 'mce_plugins' ) );
        // add field UI hook
        add_action( $taxonomy."_add_form_fields", array("MPC_Term", "field_sets"), 10, 2 );
      }
      
      if (self::is_site_content_editor()) {
        MPV::incl("meta");
        MPC::incl("meta");
        add_filter( 'mce_external_plugins', array( "MasterPress", 'mce_plugins' ) );
      }
      
      if (self::is_post_editor()) {
        add_filter( 'mce_external_plugins', array( "MasterPress", 'mce_plugins' ) );
			  add_action('add_meta_boxes', array('MPC_Post', 'post_editor_meta_boxes'));
        add_action('admin_init', array('MasterPress', 'admin_init'));
		  }

      if (self::is_post_manage()) {
        add_filter("manage_posts_columns", array("MPC_Post", "define_post_columns"));
        add_filter("manage_pages_columns", array("MPC_Post", "define_post_columns"));
        add_action("manage_posts_custom_column", array("MPC_Post", "post_column_content"));
        add_action("manage_pages_custom_column", array("MPC_Post", "post_column_content"));
        add_filter('parse_query', array("MPC_Post", "filter_manage_posts"));
        add_filter('request', array("MPC_Post", "request"));
        
        $meow_provider->set_optimize_mode( "post_list" );
        
      }
      
      
      // add save hooks
      
      add_action('edited_term', array("MPC_Meta", 'save_term_meta') );
      add_action('created_term', array("MPC_Meta", 'save_term_meta') );
      add_action('save_post', array("MPC_Meta", 'save_post_meta'));
      add_action('personal_options_update', array("MPC_Meta", 'save_user_meta'));
      add_action('edit_user_profile_update', array("MPC_Meta", 'save_user_meta'));
      

      add_action('save_post', array("MasterPress", 'save_post'));

      // add inline scripts
      
      add_action("admin_head", array("MasterPress", "inline_scripts"));
      add_action("admin_head", array("MasterPress", "inline_styles"));
      add_action("admin_head", array("MasterPress", "inline_meta"));

    }

    

    // cache the list of all post types
    
    
    $args = array();
    
    if (is_admin()) {
      // only order by menu positions in the admin, as the order doesn't matter on the front-end (slightly faster)
      $args = array("orderby" => "menu_position,menu_sub_position");
    }

    $post_types = MPM_PostType::find($args);
    
    MasterPress::$post_types = array();

    foreach ($post_types as $post_type) {
      MasterPress::$post_types[$post_type->name] = $post_type;
    }

    $taxonomies = MPM_Taxonomy::find( );

    MasterPress::$all_taxonomies = array();
    
    MasterPress::$taxonomies = array();
    
    foreach ($taxonomies as $tax) {
      if (!$tax->disabled) {
        MasterPress::$taxonomies[$tax->name] = $tax;
      }

      MasterPress::$all_taxonomies[$tax->name] = $tax;
    
    }
    

    // create necessary file directories
    
    if (is_admin()) {
    
      $combine = false;
      
      // check for a querystring force
      if (isset($_GET["mpft"])) {
        $combine = true;
      }
      
      // check if debug mode had been switched on, then off (javascript "make" still needs to be run manually)

      $opt_debug = get_option("mp_debug");
      
      if (!MASTERPRESS_DEBUG && $opt_debug != MASTERPRESS_DEBUG) {
        $combine = true;
      }

      if ($opt_debug != MASTERPRESS_DEBUG) {
        update_option("mp_debug", MASTERPRESS_DEBUG);
      }

      if (!file_exists(MASTERPRESS_CONTENT_MPFT_CACHE_DIR."mpft-all.css")) {
        $combine = true;
      }

      if (!file_exists(MASTERPRESS_CONTENT_MPFT_CACHE_DIR."mpft-all.js")) {
        $combine = true;
      }
      

      if ($combine) {
        MPU::combine_type_styles();
        MPU::combine_type_scripts();
      }
      
    }
  

    if (self::is_term_manage()) {
      
      MPC::incl("term");

      add_filter('get_terms_args', array("MPC_Term", "filter_manage_terms"));

      foreach (MasterPress::$taxonomies as $tax) {
        add_filter('manage_edit-'.$tax->name.'_columns', array("MPC_Term", "define_term_columns"));
        add_action('manage_'.$tax->name.'_custom_column', array("MPC_Term", "term_column_content"), 10, 3);
      }
    
    }

    // register post types based on database entries

    do_action('mp_pre_register_post_types');

    self::register_post_types();

    do_action('mp_register_post_types');

    // register taxonomies based on database entries

    do_action('mp_pre_register_taxonomies');

    self::register_taxonomies();

    do_action('mp_pre_register_taxonomies');

    // patch template support
    
    self::register_template_support();
    
    
    do_action('mp_register');

    // look for externally defined post types, and insert models for them in MasterPress.
    
    $reg_post_types = $wf->post_types();

    $rpt_keys = array();
    $pt_keys = array();
	
    foreach ($reg_post_types as $rpt) {
      if (!is_woof_silent($rpt)) {
        $rpt_keys[$rpt->name] = $rpt;
      }
    }
    
    foreach ($post_types as $pt) {
      $pt_keys[$pt->name] = $pt;
    }
    
    $ignore_keys = array("revision", "nav_menu_item");
    
    foreach ($rpt_keys as $key => $pt) {
      
      if (!isset($pt_keys[$key]) && !in_array($key, $ignore_keys)) {

        // create an external post type database entry
        
        $ept = new MPM_PostType();
        
        // ensure that unknown properties return "null", not "WOOF_Silent"
        $pt->set_property_mode("standard");
        
        $ept->set(      
          array( 
            "name" => $pt->name,
            "plural_name" => WOOF_Inflector::pluralize($pt->name),
            "disabled" => false,
            "labels" => $pt->labels,
            "description" => "",
            "publicly_queryable" => (bool) $pt->publicly_queryable,
            "exclude_from_search" => (bool) $pt->exclude_from_search,
            "show_ui" => (bool) $pt->show_ui,
            "show_in_menu" => (bool) $pt->show_in_menu,
            "menu_position" => (int) $pt->menu_position,
            "menu_sub_position" => 0,
            "menu_icon" => isset($pt->menu_icon) ? $pt->menu_icon : "",
            "manage_sort_order" => "post_date|desc",
            "capability_type" => $pt->capability_type,
            "capabilities" => $pt->cap_array(),
            "map_meta_cap" => (bool) $pt->map_meta_cap,
            "hierarchical" => (bool) $pt->hierarchical,
            "supports" => implode(",", $pt->supports_keys()),
            "permalink_epmask" => $pt->permalink_epmask,
            "has_archive" => (bool) $pt->has_archive,
            "rewrite" => $pt->rewrite,
            "query_var" => $pt->query_var,
            "can_export" => $pt->can_export,
            "show_in_nav_menus" => $pt->show_in_nav_menus,
            "_builtin" => false,
            "_external" => true,
            "visibility" => array("sites" => "*") 
          )
        );
        
        $ept->insert();
        
        
        
      }
    }
    
    // look for externally defined taxonomies, and insert models for them in MasterPress.
    
    $reg_tax = $wf->taxonomies();

    $rt_keys = array();
    $t_keys = array();

    foreach ($reg_tax as $tax) {
      $rt_keys[$tax->name] = $tax;
    }
    
    foreach ($taxonomies as $tax) {
      $t_keys[$tax->name] = $tax;
    }
    
    $ignore_keys = array("nav_menu", "link_category", "post_format");
    
    foreach ($rt_keys as $key => $tax) {
      if (!isset($t_keys[$key]) && !in_array($key, $ignore_keys)) {

        // create an external post type database entry
        
        $et = new MPM_Taxonomy();
        
        // ensure that unknown properties return "null", not "WOOF_Silent"
        $tax->set_property_mode("standard");
        
        
        $et->set(      
          array( 
            "name" => $tax->name,
            "plural_name" => WOOF_Inflector::pluralize($tax->name),
            "labels" => $tax->labels,
            "disabled" => false,
            "show_in_nav_menus" => (bool) $tax->show_in_nav_menus,
            "show_ui" => (bool) $tax->show_ui,
            "show_tagcloud" => (bool) $tax->show_tagcloud,
            "hierarchical" => (bool) $tax->hierarchical,
            "rewrite" => $tax->rewrite,
            "update_count_callback" => $tax->update_count_callback,
            "query_var" => $tax->query_var,
            "capabilities" => $tax->cap_array(),
            "object_type" => $tax->object_type,
            "_builtin" => false,
            "_external" => true
          )
        );

        $et->insert();
        
      }
    }
    

    
    if (is_admin()) {
      self::dispatch();
    }
    
    foreach ($wf->types() as $type) {
      if ($type->supports("top-level-slugs")) {
        add_filter("woof_".$type->name."_permalink", array("MasterPress", "woof_permalink"), 100, 2);
      }
    }
    
    // if anyone rewrites URLs, add ours to the queue
    add_filter( 'rewrite_rules_array', array("MasterPress", 'add_rewrite_rules' ));

    do_action('mp_rewrite_rules');
    do_action('mp_pre_flush_rewrite_rules');

    add_rewrite_tag("%mp_rest_endpoint%", '([^&]+)');

    if (is_admin()) {
      // flush rewrite rules (only if marked as needed)
      MasterPress::flush_rewrite_rules();
    }
  
    if (is_admin() && MasterPress::current_user_can("manage_mp_tools")) {
      self::tools_actions();
    }
    
    do_action('mp_init');

  }
  
  public static function tools_actions() {

    if (isset($_GET["mp_image_cache_admin"])) {
      self::clear_image_cache_admin();
    }

    if (isset($_GET["mp_image_cache_site"])) {
      self::clear_image_cache_site();
    }

  }
  
  public static function clear_image_cache_admin() {
    MPU::rmdir_r(MASTERPRESS_CONTENT_IMAGE_CACHE_DIR . "admin", TRUE);
  }

  public static function clear_image_cache_site() {
    MPU::rmdir_r(MASTERPRESS_CONTENT_IMAGE_CACHE_DIR . "site", TRUE);
  }
  
  public static function save_post($id) {
    
    global $wf;
    
    $the = $wf->post($id);
    
    if ($the->exists()) {
      
      $type = $the->type();
      
      $model = MPM_PostType::find_by_name($type->name);
      
      if ($type->supports("top-level-slugs")) {
        MasterPress::flag_for_rewrite();
      }
      
    }
    
  }
  
  public static function flag_for_rewrite() {
    add_option("masterpress_flush_rules", "yes");
  }
  
  public static function flush_rewrite_rules() {
    global $wp_rewrite;

    // only flush the rewrite rules when needed
    if (get_option("masterpress_flush_rules") == "yes" || isset($_GET["mp_rewrite"])) {
      delete_option("masterpress_flush_rules");
      $wp_rewrite->flush_rules();
      
      if (isset($_GET["mp_rewrite"])) {
        self::admin_notification(__("Rewrite rules flushed", MASTERPRESS_DOMAIN)); 
      }
    }
  }

  public static function admin_notification($message) {
    self::$admin_notices["notification"] = $message;
  }

  public static function admin_error($message) {
    self::$admin_notices["error"] = $message;
  }

  public static function admin_success($message) {
    self::$admin_notices["success"] = $message;
  }
  
  public static function admin_notices() {
    if (isset($_GET["def_updated"])) {
      self::admin_success(__("Field Definition Updated", MASTERPRESS_DOMAIN));
    }
    
    if (count(self::$admin_notices)) {
      ?>
      <ul class="mp-messages">
      <?php foreach (self::$admin_notices as $type => $message) : ?>
        <li class="<?php echo $type ?>"><?php echo $message ?></li>
      <?php endforeach; ?>
      </ul>
      <?php
    }
  }
  
  public static function mce_plugins( $plugins ) {
    if (self::use_new_media()) {
      $plugins['mp_media'] = MPU::url("js/tinymce/media/media.js");
    } else {
      $plugins['mp_media'] = MPU::url("js/tinymce/media-legacy/media-legacy.js");
    }
  
    return $plugins;
  }

  public static function is_editor() {
    return self::is_post_editor() || self::is_site_content_editor() || self::is_term_editor() || self::is_user_editor();
  }
  
  public static function is_post_editor() {

    if (!self::$is_post_editor) {
      self::$is_post_editor = in_array(self::$script, array("post", "post-new"));
    }

    return self::$is_post_editor;
  }

  public static function is_site_content_editor() {

    if (!self::$is_site_content_editor) {
      self::$is_site_content_editor = self::$script == "admin" && self::get_var("page") == "masterpress-site-content";
    }

    return self::$is_site_content_editor;
  }
  
  public static function is_user_editor() {

    if (!self::$is_user_editor) {
      self::$is_user_editor = in_array(self::$script, array("user-edit", "profile"));
    }

    return self::$is_user_editor;
  }

  public static function is_user_create() {

    if (!self::$is_user_create) {
      self::$is_user_create = in_array(self::$script, array("user-new"));
    }

    return self::$is_user_create;
  }

  public static function get_var($key) {
    if (isset($_GET[$key])) {
      return $_GET[$key];
    }
    
    return "";
  }
  
  public static function is_term_editor() {

    if (!self::$is_term_editor) {
      self::$is_term_editor = in_array(self::$script, array("edit-tags")) && self::get_var("action") == "edit";
    }

    return self::$is_term_editor;
  }

  public static function is_term_manage() {

    if (!self::$is_term_manage) {
      self::$is_term_manage = in_array(self::$script, array("edit-tags")) && self::get_var("action") != "edit";
    }

    return self::$is_term_manage;
  }

  public static function is_post_edit() {

    if (!self::$is_post_edit) {
      self::$is_post_edit = self::$script == "post";
    }

    return self::$is_post_edit;
  }

  public static function is_post_create() {

    if (!self::$is_post_create) {
      self::$is_post_create = self::$script == "post-new";
    }

    return self::$is_post_create;
  }

  
  public static function is_post_manage() {

    if (!self::$is_post_manage) {
      self::$is_post_manage = in_array(self::$script, array("edit"));
    }

    return self::$is_post_manage;
  }

  
  public static function inline_scripts() {

    ?>
    <script type="text/javascript">
    
    jQuery("html").addClass(jQuery.client.os).addClass(jQuery.client.browser);

    var mp_wp_upload_url = '<?php echo esc_url( get_upload_iframe_src("media") ) ?>';
    var mp_wp_media_library_url = '<?php echo esc_url( str_replace("TB_iframe", "mp-media-library=1&TB_iframe", get_upload_iframe_src("media")) ) ?>';
    var mp_ajax_url = '<?php echo get_admin_url() ?>admin-ajax.php', mp_controller = '<?php echo MasterPress::$controller_key ?>', mp_action = '<?php echo MasterPress::$action ?>', mp_nonce = "<?php echo esc_js( wp_create_nonce( 'mp-ajax-nonce' ) ); ?>";
    
    jQuery.mp.lang['confirm_go'] = '<?php echo esc_js( __("There are unsaved changes which will be lost if you manage this item now. Are you sure you wish to navigate away from this page?", MASTERPRESS_DOMAIN) ) ?>';
    
    
    jQuery(document).ready( function($) {
      // update the first menu item to "Masterplan"
      $('#toplevel_page_masterpress .wp-submenu-wrap a[href$=masterpress]').html('<span class="mpv-options-menu mpv-options-menu-overview"><?php echo __("Masterplan", MASTERPRESS_DOMAIN); ?></span>');
    });
    
    </script>

    <script id="mp-field-label-tooltip-template" type="text/html">
    <div class="mptt">
    <div class="mptt-bubble">
    <div class="nub"></div>
    <div class="nub-border"></div>
    <div class="mptt-text">
    {{text}}
    </div>
    </div>
    </div>
    </script>



    <?php if (self::is_media_library()) : ?>
    
    <style type="text/css">

    /* Add styles for a simple "read-only" mode for the media library. It's very difficult to patch the ML to insert correctly into the field from all tabs */

    tr.align, 
    tr.url, 
    tr.post_content, 
    tr.post_excerpt, 
    tr.image_alt, 
    tr.image-size, 
    tr.post_title, 
    .media-item-info input.button, 
    .media-item-info img.imgedit-wait-spin, 
    .del-link, 
    li#tab-gallery, 
    #media-upload-header {
      display: none !important;
    }    
    
    .button-select {
      margin-left: 10px;
    }
    
    </style>
    
    <script>

      jQuery(function($) { 
        
        // make sure the search form retains the "MasterPress-mode"
        $('#filter').append('<input type="hidden" name="mp-media-library" value="1" />');
        
        $('.del-link').each(function() {
          var $button = $('<button type="button" class="button button-select"><?php echo __("Select", MASTERPRESS_DOMAIN) ?></a>');
          $(this).before($button);
          $(this).parent().find("input:submit").remove();  
          
          $button.click( function() {
            var $media_item = $(this).closest(".media-item");
            var id = $media_item.attr("id").replace("media-item-", "");
            
            window.parent.mp_media_library_set_field(id);
            window.parent.tb_remove();
            
            return false;                        
          });
          
        });

      });

    </script>
      
    <?php endif; 
  
    if (self::is_post_editor()) { 
      MPC_Post::inline_head();  
    }    

    if (self::is_user_editor()) { 
      MPC_User::inline_head();  
    }    

    if (self::is_term_editor() || self::is_term_manage()) { 
      MPC::incl("term");
      MPC_Term::inline_head();  
    }    

    if (self::is_site_content_editor()) { 
      MPC_SiteContent::inline_head();  
    }    


    ?>
    
    <?php
  }
  
  public static function inline_meta() {

    if (MASTERPRESS_CHROME_FRAME) { 
    ?>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <?php 
    }
    
  }

  
  public static function admin_print_styles() {
    
    echo '<style type="text/css">';
    
    $admin_color = get_user_option("admin_color");
    
    $offset = "6";
    
    if ($admin_color == "classic") {
      $offset = "-594";
    } else if ($admin_color == "bbpress") {
      $offset = "-894";
    }
    

    $offset_2x = "6";
    
    if ($admin_color == "classic") {
      $offset_2x = "-294";
    } else if ($admin_color == "bbpress") {
      $offset_2x = "-444";
    }
    
    foreach (self::$menu_icons as $menu_id => $icon_url) {
      echo '#adminmenu '.$menu_id.' .wp-menu-image { background: url('.$icon_url.') no-repeat '.$offset.'px 6px !important; } ';
      echo '#adminmenu '.$menu_id.'.wp-has-current-submenu .wp-menu-image, #adminmenu '.$menu_id.'.current .wp-menu-image, #adminmenu '.$menu_id.':hover .wp-menu-image { background-position: -294px 6px !important; } ';
      echo '#adminmenu '.$menu_id.' .wp-menu-image img { display: none; } ';
      
      MPU::mq2x_start();

      echo '#adminmenu '.$menu_id.' .wp-menu-image { background-position: '.$offset_2x.'px -26px !important; background-size: auto 56px !important; } ';
      echo '#adminmenu '.$menu_id.'.wp-has-current-submenu .wp-menu-image, #adminmenu '.$menu_id.'.current .wp-menu-image, #adminmenu '.$menu_id.':hover .wp-menu-image { background-position: -144px -26px !important; } ';
      echo '#adminmenu '.$menu_id.' .wp-menu-image img { display: none; } ';
    
      MPU::mq2x_end();
    }
    
    echo '</style>';
    
  }
  
  public static function menu_icon($id, $icon, $icon_2x = "", $is_sprite = false, $prefix = "toplevel_page_") {
    if (!$is_sprite) {
      $sprite = MPU::create_icon_sprite($icon, $icon_2x);
    } else {
      $sprite = $icon;
    }
  
    self::$menu_icons['#'.$prefix.$id] = $sprite->url();
    // return a spacer gif, since the image is handled by CSS
    return MPU::img_url("blank.gif");
  }
    
  public static function inline_styles() {
    global $wf;

    $action = "";
    
    if (isset($_REQUEST["action"])) {
      $action = $_REQUEST["action"];
    }
  
    
    $the_type = $wf->the->type();
    
    ?>
    <style type="text/css">
    <?php 

  
    // insert styles to show the menu icons adjacent to the headers, instead of the default post icon
    
    if (self::is_post_editor()) {
      
      if ($the_type->supports("mp-page-attributes")) {
        // make sure the standard meta box is hidden!
        ?>
        #pageparentdiv { display: none; }
        <?php
      }
      
      if (!$the_type->supports("editor") && self::use_new_media()) {
        // the margin below the title is too large when the post body is hidden
        ?>
        #post-body-content { 
          margin-bottom: 9px; 
        }

        .wrap div.updated, .wrap div.error {
        margin: 5px 0 8px;
        }  

        <?php
      }
      
    }
    

    foreach (MasterPress::$post_types as $post_type) {

      $menu_icon = MPU::menu_icon($post_type->menu_icon, false);
      
      if ($menu_icon && $menu_icon->exists()) {
        
        $margin = "";
        
        if ($post_type->menu_icon != "" && ! ( ($post_type->name == "post" && $post_type->menu_icon == "menu-icon-posts.png") || ($post_type->name == "page" && $post_type->menu_icon == "menu-icon-pages.png") ) ) {
          $margin = "margin-right: 0px;"; 
            
          $icon_url = MPU::sprite_menu_icon_url($post_type->menu_icon);
          
          $menu_id = "#menu-posts-".$post_type->name;
          
          if ($post_type->name == "post") {
            $menu_id = "#menu-posts";
          } else if ($post_type->name == "page") {
            $menu_id = "#menu-pages";
          }
          
          // image_get_user_option("admin_color") == "classic" ? MPU::blue_menu_icon_url($post_type->menu_icon, false) : MPU::gray_menu_icon_url($post_type->menu_icon, false),
            

          $admin_color = get_user_option("admin_color");
          
          $offset = "6";
          
          if ($admin_color == "classic") {
            $offset = "-594";
          } else if ($admin_color == "bbpress") {
            $offset = "-894";
          }
          
          // output our post type icons
          
          // child post types meta box
          echo '#poststuff #detail-post-type-' . $post_type->name . ' h3 em, #your-profile #field-set-post-type-' . $post_type->name . ' h3 em, #createuser #field-set-post-type-' . $post_type->name . ' h3 em, #edittag #field-set-post-type-' . $post_type->name . ' { padding-left: 25px; background-image: url(' . $menu_icon->url() . '); } ';
          
          echo '#poststuff #detail-post-type-' . $post_type->name . ' .inside { padding: 0; margin: 0 } '; 
          
          echo '#adminmenu '.$menu_id.' .wp-menu-image { background: url('.$icon_url.') no-repeat '.$offset.'px 6px !important; } ';
          echo '.admin-color-mp6 #adminmenu '.$menu_id.' .wp-menu-image { background: url('.$icon_url.') no-repeat '.($offset + 2).'px 8px !important; } ';
          
          echo '#adminmenu '.$menu_id.'.wp-has-current-submenu .wp-menu-image, #adminmenu '.$menu_id.':hover .wp-menu-image { background-position: -294px 6px !important; } ';

          echo '.admin-color-mp6 #adminmenu '.$menu_id.'.wp-has-current-submenu .wp-menu-image, .admin-color-mp6 #adminmenu '.$menu_id.':hover .wp-menu-image { background-position: -292px 8px !important; } ';

          echo '#adminmenu '.$menu_id.' .wp-menu-image img { display: none; } ';
          
          if (self::is_post_manage() || self::is_post_editor()) {
          
            echo "#wpbody-content .wrap .icon32-posts-".$post_type->name." { background: url(".$menu_icon->url.") no-repeat center center !important; background-size: 16px 16px !important; ".$margin." } \n";
            
            // MP6 integration
            echo "body.admin-color-mp6 #wpbody-content .wrap .icon32-posts-".$post_type->name." { display: block;  margin-left: -6px; } \n";
  
                  
            if ($menu_icon->width() <= 16) {
              echo "#wpbody-content .wrap .icon32-posts-".$post_type->name." { background-position: 8px 8px !important; width: 36px; } \n";
            }

          }
          
          
        
        }
        
        // output a general post type icon class, which can be hooked into by field type UIs :)
      
        echo ".mp-icon-post-type-".$post_type->name." { background-image: url(".$menu_icon->url().") !important; } \n";

      }
      
      
    }


    MPU::mq2x_start();


    foreach (MasterPress::$post_types as $post_type) {

      $menu_icon = MPU::menu_icon($post_type->menu_icon, false);
      
      if ($menu_icon && $menu_icon->exists()) {
        
        $margin = "";
        
        if ($post_type->menu_icon != "" && ! ( ($post_type->name == "post" && $post_type->menu_icon == "menu-icon-posts.png") || ($post_type->name == "page" && $post_type->menu_icon == "menu-icon-pages.png") ) ) {
          
          $offset = "6";
          
          if ($admin_color == "classic") {
            $offset = "-294";
          } else if ($admin_color == "bbpress") {
            $offset = "-444";
          }

          // child post types meta box
          echo '#poststuff #detail-post-type-' . $post_type->name . ' h3 em, #your-profile #field-set-post-type-' . $post_type->name . ' h3 em, #createuser #field-set-post-type-' . $post_type->name . ' h3 em, #edittag #field-set-post-type-' . $post_type->name . ' { padding-left: 25px; background-size: 16px 16px !important; background-image: url(' . $menu_icon->resize("w=32&h=32")->url() . '); } ';
          
          
          echo '#adminmenu '.$menu_id.' .wp-menu-image { background-position: '.$offset.'px -26px !important; background-size: auto 56px !important; } ';
          echo '#adminmenu '.$menu_id.'.wp-has-current-submenu .wp-menu-image, #adminmenu '.$menu_id.':hover .wp-menu-image { background-position: -144px -26px !important; } ';
          echo '#adminmenu '.$menu_id.' .wp-menu-image img { display: none; } ';

          if (self::is_post_manage() || self::is_post_editor()) {
            echo "#wpbody-content .wrap .icon32-posts-".$post_type->name." { background: url(".$menu_icon->resize("w=32&h=32")->url.") no-repeat center center !important; background-size: 16px 16px !important; ".$margin." } \n";
          }
        
        
        }
        
      }
    
    

    }

    echo ".mp-icon-post-type-".$post_type->name." { background-image: url(".$menu_icon->resize("w=32&h=32")->url.") !important; background-size: 16px 16px; } \n";

    MPU::mq2x_end();
          
    
    ?>
    
    .edit-tags-php .icon32 {
      background: url(<?php echo MPU::img_url('icon-tag.png') ?>) 7px 8px no-repeat !important;
      width: 24px;
    }
    
    
    
    <?php foreach (MPFT::type_keys() as $key) : ?>
    .mp-icon-field-type-<?php echo $key ?> { background-image: url(<?php echo MPU::type_icon_url($key) ?>); }
    <?php endforeach; ?>

    <?php MPU::mq2x_start(); ?>
    <?php foreach (MPFT::type_keys() as $key) : ?>
    <?php $url = MPU::type_image($key, "icon-color.png", "")->resize("w=32&h=32")->url; ?>
    .mp-icon-field-type-<?php echo $key ?> { background-image: url(<?php echo $url ?>); background-size: 16px 16px; }
    <?php endforeach; ?>
    <?php MPU::mq2x_end(); ?>
    

    <?php
    

    foreach (MasterPress::$all_taxonomies as $tax) {

      if ($tax->title_icon) {

        $title_icon = MPU::menu_icon($tax->title_icon);
        
        if ($title_icon && $title_icon->exists()) {
          
          if (self::is_term_manage() || self::is_term_editor()) {
          
            if (isset($_GET["taxonomy"]) && $_GET["taxonomy"] == $tax->name) {
              echo ".edit-tags-php .icon32 { background: url(".$title_icon->url().") no-repeat 7px 8px !important; width: 36px; margin-right: 0px;  }";


              // MP6 integration
              echo " body.admin-color-mp6.edit-tags-php .icon32 { display: block; margin-left: -4px; } \n";

            }
        
          }

          // output a general taxonomy icon class, which can be hooked into by field type UIs :)
          echo " .mp-icon-taxonomy-".$tax->name." { background-image: url(".$title_icon->url().") !important; }";
        
        }

        
      } else {

        // output a general taxonomy icon class, which can be hooked into by field type UIs :)
        echo " .mp-icon-taxonomy-".$tax->name." { background-image: url(".MPU::img_url('icon-tag.png').") !important; }";
        
      }
      
    }
    
        
    MPU::mq2x_start();
    
    foreach (MasterPress::$all_taxonomies as $tax) {

      if ($tax->title_icon) {

        $title_icon = MPU::menu_icon($tax->title_icon);
        
        if ($title_icon && $title_icon->exists()) {
          
          // output 2x nearest neighbor scaled icons
          echo "\n.mp-icon-taxonomy-".$tax->name." { background-image: url(".$title_icon->resize("w=32&h=32")->url().") !important; background-size: 16px 16px; }";

          if (self::is_term_manage() || self::is_term_editor()) {
          
            if (isset($_GET["taxonomy"]) && $_GET["taxonomy"] == $tax->name) {
              echo "\n.edit-tags-php .icon32 { background: url(".$title_icon->resize("w=32&h=32")->url().") no-repeat 7px 8px !important; background-size: 16px 16px !important; width: 36px; margin-right: 0px;  }";
            }
        
          }
        
        }
        
      }
      
    }
    
    MPU::mq2x_end();

    

    ?>
    
    </style>
    <!--[if lte IE 7]><link rel="stylesheet" href="<?php echo MASTERPRESS_URL."css/mpv.ie7.css" ?>" type="text/css" /><![endif]-->
    <!--[if IE 8]><link rel="stylesheet" href="<?php echo MASTERPRESS_URL."css/mpv.ie8.css" ?>" type="text/css" /><![endif]-->
    
    <?php 

  }

  
  protected static function dir_cmd($dir) {
    return sprintf('<code>chmod 777 %s</code>', $dir);
  }

  protected static function chmod_cmd($dir) {
    return sprintf('<code>chmod 777 %s</code>', $dir);
  }

  protected static function chmod_back_cmd($dir) {
    return sprintf('<code>chmod 755 %s</code>', $dir);
  }
  
  public static function create_directories() {
    
    MPU::incl("view/mpv.php");

    global $wf;
    global $blog_id;
    
    $main_exists = true;
    $global_exists = true;
		$try_content = true;
		

    self::$is_masterplan = ( isset($_GET["page"] ) && $_GET["page"] == "masterpress" );
    
    
    if (!file_exists(MASTERPRESS_CONTENT_DIR)) {
      
			if ($try_content) {

	      // if the base uploads directory doesn't exist, try to create it
	      if (!wp_mkdir_p(MASTERPRESS_CONTENT_DIR)) {
	        $main_exists = FALSE;
			
					// check that the base blogs.dir actually exists!
					$bd_base_dir = WP_CONTENT_DIR.WOOF_DIR_SEP."blogs.dir";
				
	        if (is_multisite()) {
	  				MPV::warn(sprintf(__('<strong>Note: the MasterPress content folder %s for this site does not yet exist and cannot be automatically created</strong>, which will cause problems when using MasterPress.<br /><br />Using your FTP client, server admin panel, or operating system (for local installations), please give the %s folder permission <strong>777</strong> so that MasterPress can create the necessary folders.<br /><br />Alternatively, use the following command if you have shell or terminal access: %s<br /><a href=%s>Click here</a> once this is complete, to verify this problem has been solved.', MASTERPRESS_DOMAIN), '<span class="tt">'.MASTERPRESS_CONTENT_DIR.'</span>', '<span class="tt">wp-content/blogs.dir/'.$blog_id.'/</span>', self::dir_cmd(WP_CONTENT_DIR.WOOF_DIR_SEP."blogs.dir".WOOF_DIR_SEP.$blog_id.WOOF_DIR_SEP), '"'.MPU::current_url().'"')); 
	        } else {
	          MPV::warn(sprintf(__('<strong>Note: the MasterPress content folder %s does not yet exist and cannot be automatically created</strong>, which will cause problems when using MasterPress.<br /><br />Using your FTP client, server admin panel, or operating system (for local installations), please give the %s folder the permission <strong>777</strong> so that MasterPress can create the necessary folders.<br /><br />Alternatively, use the following command if you have shell or terminal access: %s<br /><a href=%s>Click here</a> once this is complete, to verify this problem has been solved.', MASTERPRESS_DOMAIN), '<span class="tt">'.MASTERPRESS_CONTENT_DIR.'</span>', '<span class="tt">wp-content</span>', self::dir_cmd(WP_CONTENT_DIR), '"'.MPU::current_url().'"')); 
	        }
      
	        update_site_option("mp_dir_problem", true);
        
	      } else {
        
	        chmod(MASTERPRESS_CONTENT_DIR, 0755);
        
	      }

			}
		
    }
    
    
    if (!file_exists(MASTERPRESS_GLOBAL_CONTENT_DIR)) {
      // if the global content directory doesn't exist, try to create it
      if (MASTERPRESS_GLOBAL_CONTENT_DIR != MASTERPRESS_CONTENT_DIR) {
        
        if (!wp_mkdir_p(MASTERPRESS_GLOBAL_CONTENT_DIR)) {
          $global_exists = FALSE;
          MPV::warn(sprintf(__('<strong>Note: the MasterPress shared content folder %s for your multi-site network does not yet exist and cannot be automatically created</strong>, which will cause problems when using MasterPress.<br /><br />Using your FTP client, server admin panel, or operating system (for local installations), please give the %s folder the permission <strong>777</strong> so that MasterPress can create the necessary folders.<br /><br />Alternatively, use the following command if you have shell or terminal access: %s<br /><a href=%s>Click here</a> once this is complete, to verify this problem has been solved', MASTERPRESS_DOMAIN), '<span class="tt">'.MASTERPRESS_GLOBAL_CONTENT_DIR.'</span>', '<span class="tt">wp-content</span>', self::dir_cmd(WP_CONTENT_DIR), '"'.MPU::current_url().'"')); 

          update_site_option("mp_dir_problem", true);
        
        } else {
        
          chmod(MASTERPRESS_GLOBAL_CONTENT_DIR, 0755);

        }
        
      } 
      
    } 

    $warning_777 = __('Please change the permissions of your <span class="tt">wp-content</span> directory to <span class="tt">755</span>. <br />');

    $stop_trying_global = false;
    $stop_trying = false;

    
    if ($main_exists && $global_exists) {
      
      $stop_trying = false;
      $stop_trying_global = false;
      
      // test if the standard sub-directories exist
      
      $not_writable_warning = sprintf(__('<strong>Note: the MasterPress folder %s does not seem to be writable</strong>, which will cause problems when using MasterPress.<br /><br />Using your FTP client, server admin panel, or operating system (for local installations), please give this folder the permission 777.<br /><br />Alternatively, use the following command if you have shell or terminal access: %s<br /><a href=%s>Click here</a> once this is complete, to verify this problem has been solved', MASTERPRESS_DOMAIN), '<span class="tt">'.MASTERPRESS_CONTENT_DIR.'.</span>', self::chmod_cmd(MASTERPRESS_CONTENT_DIR), '"'.MPU::current_url().'"');
      
      if (is_multisite()) {
        $not_writable_global_warning = sprintf(__('<strong>Note: the MasterPress shared folder %s for your multi-site network does not seem to be writable</strong>, which will cause problems when using MasterPress.<br /><br />Using your FTP client, server admin panel, or operating system (for local installations), please give this folder the permission 777.<br /><br />Alternatively, use the following command if you have shell or terminal access: %s<br /><a href=%s>Click here</a> once this is complete, to verify this problem has been solved', MASTERPRESS_DOMAIN), '<span class="tt">'.MASTERPRESS_GLOBAL_CONTENT_DIR.'</span>', self::chmod_cmd(MASTERPRESS_GLOBAL_CONTENT_DIR), '"'.MPU::current_url().'"');
      } else {
        $not_writable_global_warning = $not_writable_warning;
      }
      
      if (!$stop_trying) {
        
        if (!file_exists(MASTERPRESS_CONTENT_IMAGE_CACHE_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_CONTENT_IMAGE_CACHE_DIR)) {
            $stop_trying = TRUE;
            MPV::warn($not_writable_warning); 
          }
        }
        
      }
      
      if (!$stop_trying) {
        
        if (!file_exists(MASTERPRESS_CONTENT_IMAGE_FROM_URL_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_CONTENT_IMAGE_FROM_URL_DIR)) {
            $stop_trying = TRUE;
          MPV::warn($not_writable_warning); 
          }
        }
        
      }

      if (!$stop_trying) {
        
        if (!file_exists(MASTERPRESS_CONTENT_FILE_FROM_URL_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_CONTENT_FILE_FROM_URL_DIR)) {
            $stop_trying = TRUE;
          MPV::warn($not_writable_warning); 
          }
        }
        
      }

      
      if (!$stop_trying) {
        
        if (!file_exists(MASTERPRESS_CONTENT_UPLOADS_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_CONTENT_UPLOADS_DIR)) {
          $stop_trying = TRUE;
          MPV::warn($not_writable_warning); 
          } 
        }
        
      }      
      
      if (!is_multisite() && $stop_trying) {
        // global and site are the same path
        $stop_trying_global = true;
      }
      

      if (!$stop_trying_global) {
            
        if (!file_exists(MASTERPRESS_CONTENT_MENU_ICONS_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_CONTENT_MENU_ICONS_DIR)) {
            $stop_trying_global = true;
            MPV::warn($not_writable_global_warning); 
          
          }
        } 
      
      }
      
      if (!$stop_trying_global) {
        
        if (!file_exists(MASTERPRESS_CONTENT_MASTERPLANS_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_CONTENT_MASTERPLANS_DIR)) {
          $stop_trying_global = TRUE;
          MPV::warn($not_writable_global_warning); 
          }
        }
        
      }

      if (!$stop_trying_global) {
        
        if (!file_exists(MASTERPRESS_CONTENT_MPFT_CACHE_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_CONTENT_MPFT_CACHE_DIR)) {
            $stop_trying_global = TRUE;
            MPV::warn($not_writable_global_warning); 
          }
        }
        
      }
      
      
      if (!$stop_trying_global) {
        
        if (!file_exists(MASTERPRESS_TMP_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_TMP_DIR)) {
            $stop_trying_global = TRUE;
            MPV::warn($not_writable_warning); 
          }
        }
        
      }


      if (!$stop_trying_global) {
        
        if (!file_exists(MASTERPRESS_EXTENSIONS_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_EXTENSIONS_DIR)) {
          $stop_trying_global = TRUE;
          MPV::warn($not_writable_global_warning); 
          }
        }
        
      }

      if (!$stop_trying_global) {
        
        if (!file_exists(MASTERPRESS_EXTENSIONS_FIELD_TYPES_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_EXTENSIONS_FIELD_TYPES_DIR)) {
          $stop_trying_global = TRUE;
          MPV::warn($not_writable_extensions_warning); 
          }
        }
        
      }

      if (!$stop_trying_global) {
        
        if (!file_exists(MASTERPRESS_EXTENSIONS_ICONS_DIR)) {
          if (!wp_mkdir_p(MASTERPRESS_EXTENSIONS_ICONS_DIR)) {
          $stop_trying_global = TRUE;
          MPV::warn($not_writable_extensions_warning); 
          }
        }
        
      }
  
      
      if (!$stop_trying) {

        // check the writable status of MasterPress directories

        $not_writables = self::check_writable( array(
          MASTERPRESS_CONTENT_MENU_ICONS_DIR, 
          MASTERPRESS_CONTENT_IMAGE_CACHE_DIR, 
          MASTERPRESS_CONTENT_IMAGE_FROM_URL_DIR, 
          MASTERPRESS_CONTENT_FILE_FROM_URL_DIR, 
          MASTERPRESS_CONTENT_MPFT_CACHE_DIR, 
          MASTERPRESS_TMP_DIR, 
          MASTERPRESS_CONTENT_MASTERPLANS_DIR, 
          MASTERPRESS_EXTENSIONS_DIR, 
          MASTERPRESS_EXTENSIONS_FIELD_TYPES_DIR, 
          MASTERPRESS_EXTENSIONS_ICONS_DIR, 
          MASTERPRESS_CONTENT_UPLOADS_DIR
        ));
        
        if (count($not_writables)) {
          $content_paths = WOOF_HTML::open("p");
          $chmods = WOOF_HTML::open("code");
          
          foreach ($not_writables as $nw) {
            $content_paths .= WOOF_HTML::tag("span", "class=tt", $nw["content_path"]."<br />");
            $chmods .= $nw["chmod"];
          }

          $content_paths .= WOOF_HTML::close("p");
          $chmods .= WOOF_HTML::close("code");

          MPV::warn(sprintf(__('<p><strong>Note:</strong> these MasterPress folders in <span class="tt">wp-content</span> are not writable which will cause problems using WordPress:%s<br /></p><p>Using your FTP client, server admin panel, or operating system (for local installations), please give these folders permission 777.<br /><br />Alternatively, use the following commands if you have shell or terminal access:<br />%s', MASTERPRESS_DOMAIN), $content_paths, $chmods)); 

        } 
      

        
      }
      
      
    }

		


    
    
  }
  
  public static function is_writable($path) {

    //will work in despite of Windows ACLs bug
    //NOTE: use a trailing slash for folders!!!
    //see http://bugs.php.net/bug.php?id=27609
    //see http://bugs.php.net/bug.php?id=30931

      if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
          return self::is_writable($path.uniqid(mt_rand()).'.tmp');
      else if (is_dir($path))
          return self::is_writable($path.'/'.uniqid(mt_rand()).'.tmp');
      // check tmp file for read/write capabilities
      $rm = file_exists($path);
      $f = @fopen($path, 'a');
      if ($f===false)
          return false;
      fclose($f);
      if (!$rm)
          unlink($path);
      return true;
  }

  public static function check_writable($dirs) {
    $not_writables = array();
    
    if (!is_array($dirs)) {
      $dirs = explode(",", $dirs);
    }
    
    foreach ($dirs as $dir) {
      if (!self::is_writable($dir)) {
        $not_writables[] = array("content_path" => str_replace(WP_CONTENT_DIR, "", $dir), "path" => $dir, "mkdir" => "mkdir -p ".$dir."<br />", "chmod" => "chmod 777 ".$dir."<br />");
      }
    }
    
    return $not_writables;
  }
  
  public static function media_upload_default_tab($tab) {
    if (self::is_media_library()) {
      return "library";
    }
    
    return $tab;
  }
  
  public static function is_media_library() {
    return isset($_GET["mp-media-library"]);
  }
  

  public static function admin_menu() {
    
    global $wf;

    // we need to hide built-in types
    
    foreach (MasterPress::$post_types as $post_type) {
      
      if ($post_type->name == "post" && ($post_type->disabled || !$post_type->in_current_site())) {
        remove_menu_page('edit.php');
      } else if ($post_type->name == "page" && ($post_type->disabled || !$post_type->in_current_site())) {
        remove_menu_page('edit.php?post_type=page');
      } else if ($post_type->name == "attachment" && ($post_type->disabled || !$post_type->in_current_site())) {
        remove_menu_page('upload.php');
      }
      
    }

    $caps = array(
      "manage_masterplan" => MasterPress::get_cap_key("manage_masterplan"),
      "manage_post_types" => MasterPress::get_cap_key("manage_post_types"),
      "manage_taxonomies" => MasterPress::get_cap_key("manage_taxonomies"),
      "manage_templates" => MasterPress::get_cap_key("manage_templates"),
      "manage_user_roles" => MasterPress::get_cap_key("manage_user_roles"),
      "manage_site_field_sets" => MasterPress::get_cap_key("manage_site_field_sets"),
      "manage_shared_field_sets" => MasterPress::get_cap_key("manage_shared_field_sets"),
      "manage_mp_settings" => MasterPress::get_cap_key("manage_mp_settings"),
      "manage_mp_tools" => MasterPress::get_cap_key("manage_mp_tools")
    );
    
    
    if ($wf->the_user->is_an("Administrator")) {
      // ensure that user roles can be accessed by admins!
      $caps["manage_user_roles"] = "manage_options";
      $caps["edit_user_roles"] = "manage_options";
      $caps["manage_mp_settings"] = "manage_options";
    }


    // main menu

    $page = add_menu_page( 
      __( 'Masterplan', "masterpress" ), 
      __( 'Masterplan', "masterpress" ), 
      $caps['manage_masterplan'], 
      'masterpress', 
      array('MasterPress', 'render'),
      self::menu_icon( "masterpress", MPU::plugin_image("menu-icon-masterpress.sprite.png"), "", true ),
      "85.5"
    );



    add_action("load-$page", array("MasterPress", "add_help_tabs"));
    
    
    // sub menus
  
    $sm = add_submenu_page( 
      'masterpress', 
      __('Post Types', MASTERPRESS_DOMAIN), 
      MPU::options_menu('post-types', __( 'Post Types', MASTERPRESS_DOMAIN )), 
      $caps['manage_post_types'],
      'masterpress-post-types', 
      array('MasterPress', 'render')
    );

    add_action("load-$sm", array("MasterPress", "add_help_tabs"));

    $sm = add_submenu_page( 
      'masterpress', 
      __('Taxonomies', MASTERPRESS_DOMAIN), 
      MPU::options_menu('taxonomies', __( 'Taxonomies', MASTERPRESS_DOMAIN )), 
      $caps['manage_taxonomies'],
      'masterpress-taxonomies', 
      array('MasterPress', 'render')
    );

    add_action("load-$sm", array("MasterPress", "add_help_tabs"));

    $sm = add_submenu_page( 
      'masterpress', 
      __('Templates', MASTERPRESS_DOMAIN), 
      MPU::options_menu('page-templates', __( 'Templates', MASTERPRESS_DOMAIN )), 
      $caps['manage_templates'],
      'masterpress-templates', 
      array('MasterPress', 'render')
    );

    add_action("load-$sm", array("MasterPress", "add_help_tabs"));
  
    $sm = add_submenu_page( 
      'masterpress', 
      __('User Roles' ), 
      MPU::options_menu('roles', __( 'User Roles', MASTERPRESS_DOMAIN )), 
      $caps['manage_user_roles'],
      'masterpress-roles', 
      array('MasterPress', 'render')
    );

    add_action("load-$sm", array("MasterPress", "add_help_tabs"));

    $sm = add_submenu_page( 
      'masterpress', 
      __('Site Field Sets', MASTERPRESS_DOMAIN), 
      MPU::options_menu('site-field-sets', __( 'Site Field Sets', MASTERPRESS_DOMAIN )), 
      $caps['manage_site_field_sets'],
      'masterpress-site-field-sets', 
      array('MasterPress', 'render')
    );

    add_action("load-$sm", array("MasterPress", "add_help_tabs"));
    
    $sm = add_submenu_page( 
      'masterpress', 
      __('Shared Field Sets', MASTERPRESS_DOMAIN), 
      MPU::options_menu('shared-field-sets', __( 'Shared Field Sets', MASTERPRESS_DOMAIN )), 
      $caps['manage_shared_field_sets'],
      'masterpress-shared-field-sets', 
      array('MasterPress', 'render')
    );

    add_action("load-$sm", array("MasterPress", "add_help_tabs"));

    // add settings page for MasterPress
    
    add_options_page( __('MasterPress', MASTERPRESS_DOMAIN), __('MasterPress', MASTERPRESS_DOMAIN), 'manage_options', 'masterpress-settings', array("MasterPress", "render"));
    
    add_action("load-$sm", array("MasterPress", "add_help_tabs"));

    $sfs = MPM_SiteFieldSet::find();
    
    if (count($sfs)) {


      $page = add_menu_page(
        __("Site Content", MASTERPRESS_DOMAIN),
        __("Site Content", MASTERPRESS_DOMAIN),
        'publish_posts',
        'masterpress-site-content',
        array('MasterPress', 'render'),
        self::menu_icon( "masterpress-site-content", MPU::plugin_image("menu-icon-site-content.png") ),
        "3"
      );
      
      add_action("load-$page", array("MasterPress", "add_help_tabs"));

    }
    
       
    
  } // create_menu
  
  
  public static function render() {

    // renders the view which has been prepared by a controller in "init"
    
    // get the current users color scheme
    $color = MPU::dasherize( get_user_option("admin_color") );

    // get the current language
    $lang = get_bloginfo("language");
    $lang_short = substr($lang, 0, 2);


    
    ?>
      
    <div id="mpv<?php echo MasterPress::$suffix ?>" class="wrap clearfix mpv-admin mpv mpv-<?php echo $color ?> { lang: '<?php echo $lang ?>', lang_short: '<?php echo $lang_short ?>' }">
    <div class="mpv<?php echo MasterPress::$suffix."-".MasterPress::$action ?> mpv-<?php echo MasterPress::$action ?>">

    <?php

    $view = MasterPress::$view;
    
    $controller = MasterPress::$controller;
    
    if ($view == "") {
      MPV::err(__("<strong>Error:</strong> No view has been prepared", MASTERPRESS_DOMAIN));
      MPV::messages();
    } else {
      
    
      if ($view->auto_form) {
        $view->form_open();
      }
  
      $view->title();
      $method = $view->method;

      if (method_exists($view, $method)) {
        call_user_func_array(array($view, $method), $view->method_args);
      }
    
    
      if ($view->auto_form) {
        $view->form_close();
      }
    
    }
    
    ?>
  </div>
  </div>
  <!-- /.mpv -->
  
  <?php
    
  }


  public static function admin_bar_menu() {
    
    global $wp_admin_bar, $wf;
    
    $caps = array(
      "manage_masterplan" => MasterPress::get_cap_key("manage_masterplan"),
      "manage_post_types" => MasterPress::get_cap_key("manage_post_types"),
      "manage_taxonomies" => MasterPress::get_cap_key("manage_taxonomies"),
      "manage_templates" => MasterPress::get_cap_key("manage_templates"),
      "manage_user_roles" => MasterPress::get_cap_key("manage_user_roles"),
      "manage_site_field_sets" => MasterPress::get_cap_key("manage_site_field_sets"),
      "manage_shared_field_sets" => MasterPress::get_cap_key("manage_shared_field_sets"),
      "manage_mp_settings" => MasterPress::get_cap_key("manage_mp_settings")
    );
    
    
    $menu_items = array(
      array(
        'id' => 'masterpress',
        'title' => 'Masterplan',
        'href' => admin_url('admin.php?page=masterpress')
      )
    );
    
    
    if (self::current_user_can('manage_mp_tools')) {
    
      $menu_items[] = array(
        'id' => 'masterpress-tools',
        'parent' => 'masterpress',
        'title' => __('Tools', MASTERPRESS_DOMAIN),
        'href' => '#',
        'meta' => array('class' => "secondary")
      );

      $menu_items[] = array(
        "id" => "masterpress-flush-rewrite-rules",
        "title" => __("Flush Rewrite Rules"),
        'parent' => 'masterpress-tools',
        "href" => add_query_arg( array("mp_rewrite" => 1), $wf->current_url() )
      );

      $menu_items[] = array(
        "id" => "masterpress-image-cache-site",
        "title" => __("Clear Image Cache (Site)"),
        'parent' => 'masterpress-tools',
        "href" => add_query_arg( array("mp_image_cache_site" => 1), $wf->current_url() )
      );
      
      $menu_items[] = array(
        "id" => "masterpress-image-cache-admin",
        "title" => __("Clear Image Cache (Admin)"),
        'parent' => 'masterpress-tools',
        "href" => add_query_arg( array("mp_image_cache_admin" => 1), $wf->current_url() ) 
      );
      
    }
    
    
    
    if (self::current_user_can('manage_post_types')) {
      
      $menu_items[] = array(
        'id' => 'masterpress-post-types',
        'parent' => 'masterpress',
        'title' => __('Post Types', MASTERPRESS_DOMAIN),
        'href' => wp_nonce_url(admin_url('admin.php?page=masterpress-post-types'), 'masterpress')
      );
      
    }

    if (self::current_user_can('edit_post_types')) {
      
      foreach (self::$post_types as $post_type) {
        if ($post_type->still_registered()) {
          $menu_items[] = array(
            'id' => 'masterpress-post-type-'.$post_type->name,
            'parent' => 'masterpress-post-types',
            'title' => '<b class="mp-icon mp-icon-post-type mp-icon-post-type-'.$post_type->name.'">'.$post_type->display_label().'</b>',
            'href' => MasterPress::admin_url("post-types", "edit", "id=".$post_type->id)
          );
        }
      }
      
    }



    if (self::current_user_can('manage_taxonomies')) {
      
      $menu_items[] = array(
        'id' => 'masterpress-taxonomies',
        'parent' => 'masterpress',
        'title' => __('Taxonomies', MASTERPRESS_DOMAIN),
        'href' => wp_nonce_url(admin_url('admin.php?page=masterpress-taxonomies'), 'masterpress')
      );
      
    }    
    
      if (self::current_user_can('edit_taxonomies')) {
      
        foreach (self::$taxonomies as $tax) {
          if ($tax->still_registered()) {

            $menu_items[] = array(
              'id' => 'masterpress-post-type-'.$tax->name,
              'parent' => 'masterpress-taxonomies',
              'title' => '<b class="mp-icon mp-icon-taxonomy mp-icon-taxonomy-'.$tax->name.'">'.$tax->display_label().'</b>',
              'href' => MasterPress::admin_url("taxonomies", "edit", "id=".$tax->id)
            );
            
          }
        }
      
      }


    if (self::current_user_can('manage_templates')) {
      
      $menu_items[] = array(
        'id' => 'masterpress-templates',
        'parent' => 'masterpress',
        'title' => __('Templates', MASTERPRESS_DOMAIN),
        'href' => wp_nonce_url(admin_url('admin.php?page=masterpress-templates'), 'masterpress')
      );

    }
  
    if (self::current_user_can('manage_shared_field_sets')) {
      
      $menu_items[] = array(
        'id' => 'masterpress-shared-field-sets',
        'parent' => 'masterpress',
        'title' => __('Shared Field Sets', MASTERPRESS_DOMAIN),
        'href' => wp_nonce_url(admin_url('admin.php?page=masterpress-shared-field-sets'), 'masterpress')
      );
      
    }
    
    if (self::current_user_can('manage_user_roles')) {
      
      $menu_items[] = array(
        'id' => 'masterpress-roles',
        'parent' => 'masterpress',
        'title' => __('User Roles', MASTERPRESS_DOMAIN),
        'href' => wp_nonce_url(admin_url('admin.php?page=masterpress-roles'), 'masterpress')
      );

    }
    

    if (self::current_user_can('manage_site_field_sets')) {
      
      $menu_items[] = array(
        'id' => 'masterpress-site-field-sets',
        'parent' => 'masterpress',
        'title' => __('Site Field Sets', MASTERPRESS_DOMAIN),
        'href' => wp_nonce_url(admin_url('admin.php?page=masterpress-site-field-sets'), 'masterpress')
      );

    }
    
    $add_menu_items = array(
      
      array(
        'id' => 'masterpress-add-new',
        'parent' => 'masterpress',
        'title' => __('Add New', MASTERPRESS_DOMAIN),
        'href' => '#',
        'meta' => array('class' => "secondary")
      )
      
    );
    
    if (self::current_user_can('create_post_types')) {
      
      $add_menu_items[] = array(
        'id' => 'masterpress-add-new-post-type',
        'parent' => 'masterpress-add-new',
        'title' => __('Post Type', MASTERPRESS_DOMAIN),
        'href' => MasterPress::admin_url("post-types", "create")
      );
      
    }

    if (self::current_user_can('create_taxonomies')) {
      
      $add_menu_items[] = array(
        'id' => 'masterpress-add-new-taxonomy',
        'parent' => 'masterpress-add-new',
        'title' => __('Taxonomy', MASTERPRESS_DOMAIN),
        'href' => MasterPress::admin_url("taxonomies", "create")
      );
      
    }

    if (self::current_user_can('create_user_roles')) {

      $add_menu_items[] = array(
        'id' => 'masterpress-add-new-user-role',
        'parent' => 'masterpress-add-new',
        'title' => __('User Role', MASTERPRESS_DOMAIN),
        'href' => MasterPress::admin_url("roles", "create")
      );
      
    }
    
    if (self::current_user_can('create_shared_field_sets')) {

      $add_menu_items[] = array(
        'id' => 'masterpress-add-new-shared-field-set',
        'parent' => 'masterpress-add-new',
        'title' => __('Shared Field Set', MASTERPRESS_DOMAIN),
        'href' => MasterPress::admin_url("shared-field-sets", "create")
      );
      
    }

    if (self::current_user_can('create_site_field_sets')) {

      $add_menu_items[] = array(
        'id' => 'masterpress-add-new-site-field-set',
        'parent' => 'masterpress-add-new',
        'title' => __('Site Field Set', MASTERPRESS_DOMAIN),
        'href' => MasterPress::admin_url("site-field-sets", "create")
      );
      
    }
    
    if (count($add_menu_items) > 1) {
      $menu_items = array_merge($menu_items, $add_menu_items);
    } 


    


    if (count($menu_items) > 1) {

      foreach ($menu_items as $menu_item) {
        $wp_admin_bar->add_menu($menu_item);
      }
        
    }
        
  }
  
  public static function controller_class($key) {
    return "MPC_".MPU::title_case($key, true);
  }

  public static function model_class($key) {
    return "MPM_".MPU::title_case(WOOF_Inflector::singularize($key), true);
  }

  public static function view_class($key) {
    return "MPV_".MPU::title_case($key, true);
  }
  
  
  public static function dispatch() {
    
    // check that we're inside MasterPress (this also prevents MasterPress ajax from the normal dispatch too)
    
    if (isset($_REQUEST["page"]) && substr($_REQUEST["page"], 0, 11) == "masterpress") {
      // dispatch the menu to the correct controller / action

      // infer the controller, falling back to masterplan if the page is blank (shouldn't happen)
    
      if ($_REQUEST["page"] == "masterpress") {
        MasterPress::$controller_key = "masterplan";
      } else {
        MasterPress::$controller_key = str_replace("masterpress-", "", $_REQUEST["page"]); 
      }
    
      // include the controller
      MPC::incl(MasterPress::$controller_key);
    
      // infer the controller class, by converting to Title Case, and prefixing with "MPC_"
      $controller_class = MasterPress::controller_class(MasterPress::$controller_key);

      MasterPress::$controller = new $controller_class();

      MasterPress::$suffix = "-".MasterPress::$controller_key;

      MasterPress::$view = new stdClass();
       
      MasterPress::$action = "";
      
      if (isset($_GET['action'])) {
        MasterPress::$action = $_GET['action'];
      }
    
      if (MasterPress::$action == "") {
        MasterPress::$action = "manage";
      }
      
      if (isset($_REQUEST["parent"])) {
        MasterPress::$parent = $_REQUEST["parent"];
      }

      if (isset($_REQUEST["gparent"])) {
        MasterPress::$gparent = $_REQUEST["gparent"];
      }
    
      if (isset($_REQUEST["id"])) {
        MasterPress::$id = $_REQUEST["id"];
      }
      
      if (isset($_REQUEST["from"])) {
        MasterPress::$from = $_REQUEST["from"];
      }
    
      if (MasterPress::$action != "manage") { // no need for nonce checks on the manage (menu) pages
        $nonce = $_REQUEST['_wpnonce'];
        
        if ( !wp_verify_nonce($nonce, MasterPress::$action) ) { 
          // if nonce verification fails, simply go back to "manage", which is a non-destructive action
          MasterPress::$action = "manage";
        } 
      }

      
      $method = str_replace("-", "_", MasterPress::$action);
      
      if (method_exists(MasterPress::$controller, $method)) {
        MasterPress::$controller->$method();
      }

      
      
      
    }
    
  }


  public static function help() {
    
    return array(
      
      MPV::overview_tab( 
        __("The Masterplan view summarises your WordPress content management system setup", MASTERPRESS_DOMAIN ) 
      )
      
    );
    
  }
  

  public static function add_help_tabs() {
    
      $screen = get_current_screen();

      $tabs = array();
      
      if (isset($screen->id) && $screen->id == "toplevel_page_masterpress") {
         
        $tabs = self::help();
        
      } else {
        
        $help_method = str_replace("-", "_", MasterPress::$action)."_help";
      
        if (method_exists(MasterPress::$controller, $help_method)) {
          $tabs = MasterPress::$controller->$help_method();
        }
      }
      
      foreach ($tabs as $tab) {
        $screen->add_help_tab( $tab );
      }

  }


  public static function contextual_help($contextual_help, $screen_id, $screen) {
    /*
    global $pagenow;
    
    $help_method = str_replace("-", "_", MasterPress::$action)."_help";
    
    if (method_exists(MasterPress::$controller, $help_method)) {
      return MasterPress::$controller->$help_method();
    }
    
	  return $contextual_help;
    */
    
  }
  

  public static function dispatch_ajax($action) {

    MasterPress::$ajax_action = $_REQUEST["method"];
    MasterPress::$controller_key = $_REQUEST["controller"];
    
    if ( wp_verify_nonce($_REQUEST["nonce"], "mp-ajax-nonce") ) {

      MPC::incl(MasterPress::$controller_key);

      $controller_class = MasterPress::controller_class(MasterPress::$controller_key);

      if (class_exists($controller_class)) {
        MasterPress::$controller = new $controller_class();
        
        $method = str_replace("-", "_", MasterPress::$ajax_action);
        $ajax_method = "ajax_".$method;
        
        if (method_exists(MasterPress::$controller, $ajax_method)) {
          MasterPress::$controller->$ajax_method();
        } else if (method_exists(MasterPress::$controller, $method)) {
          MasterPress::$controller->$method();
        }

      }

    }
    exit();
  }


  public static function action_url( $controller, $action, $qs = array(), $entities = true) {
    
    $def_qs = array();
    $q = wp_parse_args($qs, $def_qs);
    
    $suffix = "-$controller";
    
    if ($controller == "") {
      $suffix = "";
    } else if ($controller == "masterplan") {
      $suffix = ""; // default controller
    } 

    
    $return = "";
    
    if (isset($_GET["page"])) {
      $return = "&return=".$_GET["page"];
    }
    
    $url = "admin.php?page=masterpress$suffix".( $entities ? "&amp;" : "&" )."action=".$action;

    foreach ($q as $key => $value) {
      $url .= ( $entities ? "&amp;" : "&" ).$key.'='.$value;
    }
  
    return $url;
  }
  
  
  public static function admin_url( $controller, $action, $qs = array(), $entities = true) {
    
    if ($entities) {
      $wpnu = wp_nonce_url( MasterPress::action_url( $controller, $action, $qs, $entities), $action );
    } else {
      $wpnu = html_entity_decode(wp_nonce_url( MasterPress::action_url($controller, $action, $qs, $entities), $action ));
    }
  
    return admin_url( $wpnu );
  }
  
  public static function register_post_types() {
    
    global $blog_id;
    global $wp_post_types;
    
    // here comes the magic

    $standard_supports = array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats');
    
    self::$front_page_post_types = array("page");
     
    foreach (MasterPress::$post_types as $post_type) {
      
      add_filter( 'views_edit-'.$post_type->name, array("MPC_Post", "manage_posts_views") );
  
      if (in_array("front-page", explode(",", $post_type->supports))) {
        self::$front_page_post_types[] = $post_type->name;
      }

      MPU::create_icon_sprite($post_type->menu_icon, "");

      if (!$post_type->_builtin) {
        
        if (!$post_type->disabled) {
          
          
          // check for any external post types that are no longer defined here
          
    
          if ($post_type->in_current_site()) {
          
            if (!$post_type->_external) {
              
              $labels = $post_type->labels;
            
              if (!isset($labels["all_items"]) || $labels["all_items"] == "") {
                $labels["all_items"] = $labels["name"];
              }
              
              $params = array(
                "show_ui" => true,
                "show_in_menu" => (bool) $post_type->show_in_menu,
                "label" => $post_type->plural_name,
                "public" => true,
                "publicly_queryable" => (bool) $post_type->publicly_queryable,
                "menu_position" => (int) $post_type->menu_position,
                "show_in_nav_menus" => (bool) $post_type->show_in_nav_menus, 
                "exclude_from_search" => (bool) $post_type->exclude_from_search,
                "labels" => $labels,
                "map_meta_cap" => (bool) $post_type->map_meta_cap,
                "hierarchical" => (bool) $post_type->hierarchical,
                "supports" => explode(",", $post_type->supports),
                "taxonomies" => array(),
                "permalink_epmask" => $post_type->permalink_epmask(),
                "has_archive" => (bool) $post_type->has_archive,
                "rewrite" => $post_type->rewrite,
                "query_var" => (bool) $post_type->query_var,
                "can_export" => (bool) $post_type->can_export,
                "capability_type" => $post_type->capability_type,
                "description" => $post_type->description
              );
              
                          
              if ($post_type->menu_icon != "") {
                $menu_icon = MPU::menu_icon($post_type->menu_icon, false);
                
                if ($menu_icon->exists()) {
                  $params["menu_icon"] = MPU::img_url("blank.gif");
                }
              }


              if ($post_type->capabilities && $post_type->capabilities != "") {
                $params["capabilities"] = $post_type->capabilities;
              }
               
              $register = apply_filters( "mp_can_register_post_type_{$post_type->name}", true );
              $register = apply_filters( "mp_can_register_post_type", $register, $post_type->name );
              
              if ( $register ) {
              
                $params = apply_filters( "mp_register_post_type_params", $params, $post_type->name );
                $params = apply_filters( "mp_register_post_type_params_{$post_type->name}", $params );
                
                register_post_type( 
                  $post_type->name, 
                  $params
                );
              
                remove_post_type_support($post_type->name, "custom-fields");
              }
              
            } 
          
          }
        
        }
      
      } else {

        if ($post_type->disabled) {
          
          if (!$post_type->in_current_site()) {
            self::unregister_post_type($post_type->name);
          }
        
        } else {
          
          // add or remove support from the built-in post types 
        
          $supports = explode(",", $post_type->supports);

          $remove_supports = array_diff($standard_supports, $supports);
          $add_supports = array_diff($supports, $standard_supports);
          
          foreach ($add_supports as $support) {
            add_post_type_support($post_type->name, $support);
          }

          foreach ($remove_supports as $support) {
            remove_post_type_support($post_type->name, $support);
          }
        
        }
        
        // label overrides
        
        $pto = &$wp_post_types[$post_type->name];

        foreach ($post_type->labels as $key => $value) {
          $pto->labels->$key = $value;
        }


        
      }
      
      if (self::is_post_editor()) {

        $supports = explode(",", $post_type->supports);
        
        if (in_array("page-attributes", $supports) || $post_type->name == "page") {
        
          remove_post_type_support($post_type->name, "page-attributes");
          add_post_type_support($post_type->name, "mp-page-attributes");
        }
      }
    
      
    } // endforeach
      
  }


  public static function register_template_support() {
   
    global $wf;

    if (isset($_REQUEST["post"])) {
      
      $the = $wf->post($_REQUEST["post"]);

      $type = $the->type_name();
      
      $template_name = $the->template();
  
      if ($template_name && !is_woof_silent($template_name)) {

        $template = MPM_Template::find_by_id($template_name);
      
        if ($template && $template->supports != "*") {
          
          remove_post_type_support($type, 'title');
          remove_post_type_support($type, 'editor');
          remove_post_type_support($type, 'author');
          remove_post_type_support($type, 'thumbnail');
          remove_post_type_support($type, 'excerpt');
          remove_post_type_support($type, 'trackbacks');
          remove_post_type_support($type, 'custom-fields');
          remove_post_type_support($type, 'comments');
          remove_post_type_support($type, 'revisions');
          remove_post_type_support($type, 'page-attributes');
        
          foreach (explode(",", $template->supports) as $support) {
            add_post_type_support($type, $support);
          }
        }

      }
    
    }
  
  }

  public static function admin_init() {
    global $wp_taxonomies;
    
    foreach (MasterPress::$taxonomies as $tax) {
      
      if (isset($wp_taxonomies[$tax->name])) {
        
        if ($tax->hide_term_ui || apply_filters("mp_hide_term_ui_for_taxonomy_".$tax->name, $tax->hide_term_ui) === TRUE ) {
          $wp_taxonomies[$tax->name]->show_ui = 0;
        }
      
      }
    
    }
    
  }
  
  public static function add_front_page_supports( $pages ) {
    
    if (count(self::$front_page_post_types)) {
     
       $my_cpt_pages = new WP_Query( array( 'post_status' => 'any', 'posts_per_page' => -1, 'post_type' => self::$front_page_post_types ) );
     
       if ( $my_cpt_pages->post_count > 0 ) {
         $pages = $my_cpt_pages->posts;
       }
     
       return $pages;
   
    }
    
  }

  public static function template_include($template) {
    global $wf;


    // JSON API Dispatcher
    
    $api = $wf->rest_api();
    
    if ($api->exists()) {
      // the api will "exit" if the dispatch was successful, outputting data instead of a physical template 
      $api->dispatch();
    }
    
    // if self::$front_page_cpt is true, we have a custom post type as the front page.
    
    if (is_single() || self::$front_page_cpt) {
      $setting = $wf->the->template();

      if ($setting && $setting != "") {
        $file = $wf->theme_file($setting);
      
        if ($file->exists()) {
          $template = $file->path();
        }
      }
    }
    
    $template = apply_filters("mp_template_include", $template);

    return $template;
  }
     
  
  protected static function restrict_manage_posts_select_options($current_term, $terms, $depth = 0) {
    
    // recursive function to display the options for custom taxonomy filter dropdowns, and their children
    
    foreach ($terms as $term) {
      // output each select option line, check against the last $_GET to show the current option selected
      echo '<option value='. $term->slug, $current_term == $term->slug ? ' selected="selected"' : '','>' . str_repeat("&nbsp;", $depth * 3) . $term->name . '&nbsp;</option>';

      if ($term->has_children()) {
        self::restrict_manage_posts_select_options($current_term, $term->children(), $depth + 1);
      }
  
    }
    
  }
  
  public static function restrict_manage_posts() {

    // sets up filter dropdowns on manage post listings linked to custom taxonomies
    
    global $typenow, $wf;
    
    MPM::incl("taxonomy");
    MPM::incl("post-type");
    
    if ($post_type = MPM_PostType::find_by_name($typenow) ) {
    
      foreach ($post_type->taxonomies() as $taxonomy) {
    
        if ($taxonomy->show_manage_filter) {
      
          // get the taxonomy API object

          $tax_slug = $taxonomy->name;
          
          if (apply_filters("manage_posts_show_taxonomy_filter", array("post_type" => $typenow, "taxonomy" => $tax_slug)) !== FALSE) { 
            
            $tax = $wf->taxonomy($tax_slug);
      
            if ($tax) {
        
              $terms = $tax->top_level_terms();
            
              if ($terms->count()) {
                // output html for taxonomy dropdown filter
                echo '<select name="'.$tax_slug.'" id="'.$tax_slug.'" class="postform custom-taxonomy-filter { taxonomy: \''.esc_attr($taxonomy->labels["singular_name"]).'\' }">';
                echo '<option value="">'.sprintf( __( " All %s&nbsp;&nbsp;" ), $tax->labels->name).'</option>';
              
                $current_term = "";
              
                if (isset($_GET[$tax_slug])) {
                  $current_term = $_GET[$tax_slug];
                }
              
                self::restrict_manage_posts_select_options( $current_term, $terms );
            
                echo '</select>';
              }
        
            }
          
          }
          
        }
      }
    
    }
    
  }

  
  

  public static function woof_permalink($link, $post) {
    
    // extract away the rewrite slug, but keep any parent links etc

    $ret = $link;

    $type_name = $post->type_name();

    $slug = $post->type()->slug();
    
    if ($slug != "") {
      $ret = preg_replace( "/\/$slug\//", "/", $ret); 
    }

    return $ret;
    
  }

  
  public static function add_rewrite_rules($rules) {

    global $wf;
    
    $extra_rules = array();
    
    $high_rules = apply_filters("mp_high_rewrite_rules", array());
    
    // add concrete slug rules for any post types supporting top level slugs 
    
    foreach ($wf->types() as $type) {
      
      $name = $type->name;
      
      if ($type->supports("top-level-slugs")) {
        
        foreach ($type->posts() as $post) {

          $slug = $post->slug();
          $url = $post->url_in_site();
          
          $high_rules[trim($url, "/")."/?$"] = 'index.php?'.$name.'='.$slug;
        }
        
    
      }
      
    }
    
    if (is_array($high_rules)) {
      $extra_rules = $high_rules + $extra_rules;
    }
    
    $api = $wf->rest_api();
    
    if ($api->exists()) {
      $api->setup_rules($extra_rules);
    }
    
  
    
    $low_rules = apply_filters("mp_low_rewrite_rules", array());
    
    if (is_array($low_rules)) {
      $extra_rules = $extra_rules + $low_rules;
    }
      
    $rules = $extra_rules + $rules;


    return $rules;
  }
   
  public static function register_taxonomies() {
    // unregister the built-in stuff
    
    self::unregister_taxonomy_from_object_type("category", "post");
    self::unregister_taxonomy_from_object_type("post_tag", "post");

    self::unregister_taxonomy_from_object_type("category", "page");
    self::unregister_taxonomy_from_object_type("post_tag", "page");
    
    foreach (MasterPress::$taxonomies as $tax) {
      
      if (!$tax->_builtin && !$tax->_external) {

        if ($tax->in_current_site()) {

          MPU::create_icon_sprite($tax->title_icon, "");
          
          $register = apply_filters( "mp_can_register_taxonomy_{$tax->name}", true );
          $register = apply_filters( "mp_can_register_taxonomy", $register, $tax->name );
          
          if ($register) {
            
            $params = array(
              "label" => $tax->plural_name,
              "labels" => $tax->labels,
              "show_in_nav_menus" => (bool) $tax->show_in_nav_menus, 
              "show_ui" => (bool) $tax->show_ui, 
              "show_tagcloud" => (bool) $tax->show_tagcloud, 
              "hierarchical" => (bool) $tax->hierarchical, 
              "update_count_callback" => $tax->update_count_callback, 
              "rewrite" => $tax->rewrite, 
              "query_var" => $tax->query_var, 
              "capabilities" => $tax->as_array("capabilities")
            );
          

            $params = apply_filters( "mp_register_taxonomy_params", $params, $tax->name );
            $params = apply_filters( "mp_register_taxonomy_params_{$tax->name}", $params );

            $object_type = $tax->object_type;
            
            $object_type = apply_filters( "mp_register_taxonomy_object_type", $object_type, $tax->name );
            $object_type = apply_filters( "mp_register_taxonomy_object_type_{$tax->name}", $object_type );
          
            register_taxonomy(
              $tax->name,
              $object_type,
              $params            
            );
          
          }
          
      }
        
      } else {
        
        // add support for the built-in taxonomies

        if ($tax->in_current_site()) {
  
          $post_types = $tax->object_type;

          if (is_array($post_types)) {
            foreach ($post_types as $post_type) {
              register_taxonomy_for_object_type($tax->name, $post_type);
            }
          }
        
        }
        
        
      }
    
    }

  
  
    
    

  }


  


  /* -- Installation and Upgrade -- */
  
  public static function table_exists($table) {
    global $wpdb;
    $results = $wpdb->get_results("SHOW TABLES LIKE '".MPU::table($table)."'");
    return count($results);
  }
  

  public static function install() {

    global $wpdb;

    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

    if ( !empty($wpdb->charset) )
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

    if ( !empty($wpdb->collate) )
        $charset_collate .= " COLLATE $wpdb->collate";

    MPU::combine_type_styles();
    MPU::combine_type_scripts();

  
    // create database tables
    
    $table_name = MPU::table("post_types");
    
    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      name varchar(100),
      plural_name varchar(100),
      disabled tinyint(1),
      labels text,
      description text,
      publicly_queryable tinyint(1),
      exclude_from_search tinyint(1),
      show_ui tinyint(1),
      show_in_menu tinyint(1),
      menu_position tinyint,
      menu_sub_position tinyint,
      menu_icon text, 
      menu_icon_2x text, 
      manage_sort_order varchar(255) DEFAULT 'post_date|desc', 
      capability_type varchar(100), 
      capabilities text, 
      map_meta_cap tinyint(1), 
      hierarchical tinyint(1),
      supports text,
      columns text,
      permalink_epmask varchar(255),
      has_archive tinyint(1), 
      rewrite text,
      query_var varchar(100),
      can_export tinyint(1), 
      show_in_nav_menus tinyint(1),
      visibility text,
      _builtin tinyint(1),
      _external tinyint(1),
      PRIMARY KEY  (name),
      KEY id (id)
    ) $charset_collate;";
  
    dbDelta($sql);


    // create database tables
    
    $table_name = MPU::table("templates");
    
    $sql = "CREATE TABLE $table_name (
      id varchar(255),
      supports text,
      visibility text,
      PRIMARY KEY  (id)
    ) $charset_collate;";
  
    dbDelta($sql);
        
    $table_name = MPU::table("taxonomies");
    
    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      name varchar(100),
      plural_name varchar(100),
      object_type text,
      labels text, 
      disabled tinyint(1),
      show_in_nav_menus tinyint(1),
      show_manage_filter tinyint(1),
      show_ui tinyint(1),
      show_tagcloud tinyint(1),
      hide_term_ui tinyint(1),
      hierarchical tinyint(1),
      rewrite text, 
      query_var varchar(100),
      capabilities text, 
      columns text, 
      update_count_callback varchar(100),
      visibility text,
      title_icon text,
      title_icon_2x text,
      _builtin tinyint(1),
      _external tinyint(1),
      PRIMARY KEY  (name), 
      KEY id (id)
    ) $charset_collate;";
  
    dbDelta($sql);
    
    // remove "another_field" if it exists

    $row = $wpdb->get_row("SHOW COLUMNS FROM `" . $table_name . "` LIKE '_another_field'");

    if ($row) { 
      $wpdb->query( "ALTER TABLE $table_name DROP COLUMN `_another_field`" );
    } 


    $table_name = MPU::table("fields");

    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      field_set_id int(11) NOT NULL,
      name varchar(255) NOT NULL,
      labels text,
      disabled tinyint(1),
      summary_options text,
      required tinyint(1) NOT NULL DEFAULT 0,
      allow_multiple tinyint(1) NOT NULL DEFAULT 0,
      visibility text, 
      icon text, 
      type varchar(100) NOT NULL, 
      type_options text,
      position int(11),
      capabilities text NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    dbDelta($sql);

    $table_name = MPU::table("field_sets");
    
    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL, 
      singular_name varchar(255) NOT NULL,
      disabled tinyint(1),
      position smallint, 
      allow_multiple BOOLEAN NOT NULL DEFAULT 1,
      visibility text, 
      icon text, 
      labels text, 
      expanded tinyint(1),
      sidebar tinyint(1),
      type varchar(20),
      versions int(11) NOT NULL DEFAULT 10,
      capabilities text NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    dbDelta($sql);
    
    
   
    // a site-specific custom table to store metadata for taxonomy terms
 
    $table_name = MPU::table("termmeta", "mp_", false);
    
    $sql = "CREATE TABLE $table_name (
      tmeta_id int(11) NOT NULL AUTO_INCREMENT,
      term_id bigint(20) NOT NULL,
      taxonomy varchar(50) DEFAULT NULL,
      meta_key varchar(255) DEFAULT NULL,
      meta_value LONGTEXT,
      PRIMARY KEY  (tmeta_id),
      KEY term_id (term_id),
      KEY meta_key (meta_key)
    ) $charset_collate;";
  
    dbDelta($sql);

    // a site-specific custom table to store metadata for the site
 
    $table_name = MPU::table("sitemeta");
    
    $sql = "CREATE TABLE $table_name (
      smeta_id int(11) NOT NULL AUTO_INCREMENT,
      site_id int(11) NOT NULL,
      meta_key varchar(255) DEFAULT NULL,
      meta_value LONGTEXT,
      PRIMARY KEY  (smeta_id),
      KEY meta_key (meta_key)
    ) $charset_collate;";
  
    dbDelta($sql);
    
    
    $table_name = MPU::table("versions", "mp_", false);
    
    $sql = "CREATE TABLE $table_name (
      version_id int(11) NOT NULL AUTO_INCREMENT,
      date DATETIME NOT NULL,
      user_id int(11) NOT NULL,
      object_id bigint(20) NOT NULL,
      object_type varchar(50) NOT NULL,
      object_type_meta varchar(50) DEFAULT NULL,
      field_set_name varchar(255) NOT NULL,
      field_set_count int(11) NOT NULL,
      value LONGTEXT,
      PRIMARY KEY  (version_id),
      KEY field_set_name (field_set_name)
    ) $charset_collate;";
  
    dbDelta($sql);
    
    MPM_Taxonomy::insert_builtin();
    MPM_PostType::insert_builtin();
   
  }
  
  
  public static function upgrade() {
    
    global $wpdb;
    
    // run an upgrade, based on the version number

    if (get_option("masterpress_version") != MasterPress::$version || !self::table_exists("post_types") || isset($_GET["mp_install"])) {
      // Always add the latest table definitions here (when required) with dbDelta calls
      update_option("masterpress_version", MasterPress::$version);
      MasterPress::install();
    }
    
  }
  
  protected static function version_pad_main($main) {
    $parts = explode(".", trim($main, " ."));
    
    if (count($parts) == 2) {
      $parts[] = "0";
    }
    
    return implode(".", $parts); 
  }
  
  public static function version_parts($num) {
    if (preg_match("/([0-9\.]+)(b|rc)([0-9]+)/", (string) $num, $matches)) {
      return array("main" => self::version_pad_main($matches[1]), "type" => $matches[2], "sub" => $matches[3]);
    } else {
      return array("main" => self::version_pad_main((string) $num), "type" => "", "sub" => "");
    }
  }
    
  public static function version_greater($new, $current) {
    $cp = self::version_parts($current);
    $np = self::version_parts($new);
    
    if ($np["main"] > $cp["main"]) {
      return true;
    } else if ($np["main"] < $cp["main"]) {
      return false;
    } else { // main release is equal
      
      
      // check if the type of release is the same (i.e. b (beta) or rc (release candidate))
      if ($np["type"] == $cp["type"]) {
        return (int) $np["sub"] > (int) $cp["sub"];
      } else if ($cp["type"] == "b" && $np["type"] == "rc") {
        return true;
      } else if ($cp["type"] == "rc" && $np["type"] == "b") {
        return false;
      } else if ($np["type"] == "" && $cp["type"] != "") {
        return true;
      }
    } 
    
    return false;
  }
  
  public static function pre_set_site_transient_update_plugins($transient) {
    
    global $wf;
    
    if ( empty( $transient->checked ) ) {
      return $transient;      
    }
    
    $slug = plugin_basename( __FILE__ );
    
    // POST data for the update API

    $licence_key = self::licence_key();
    
    $args = array(
      "action" => "update-check",
      "key" => $licence_key,
      "plugin_name" => $slug,
      "version" => $transient->checked[$slug],
      "domain" => $_SERVER['SERVER_NAME']
    );
    
    // Send the request to check for an update

    
    $response = self::update_request( $args );
    
    // If the response is false, don't alter the transient
    
    if (!isset($response->error)) {
      if ($response !== false && self::version_greater($response->new_version, $transient->checked[$slug]) ) {
        $transient->response[$slug] = $response;
      }
    }
  
    return $transient;
    
  }
  
  public static function licence_key() {
    return trim(get_site_option("mp_licence_key", ""));
  }
  
  public static function update_request($args) {
    
    // Send the request
    
    $update_api = MASTERPRESS_UPDATE_API;
    
    if (defined("MASTERPRESS_UPDATE_API_TEST")) {
      $update_api = MASTERPRESS_UPDATE_API_TEST;
    }
    
    $request = wp_remote_post( $update_api, array( 'body' => $args, "sslverify" => false ) );
    
    // Ensure the request was successful
    
    if ( is_wp_error( $request ) or wp_remote_retrieve_response_code( $request ) != 200 ) {
      return FALSE;
    } 
    
    if (isset($request["body"])) {
	
	    $response = maybe_unserialize( $request["body"] );
		
      if ( is_object( $response ) ) {

        if (isset($response->valid) && !$response->valid) {
          // uncache the validity of the current licence key
          MPC::incl("settings");
          MPC_Settings::uncache_licence_key($licence_key);
        }

        return $response;
      }  

    }
    
    return false;
    
  }
  
  public static function plugins_api( $false, $action, $args ) {
    
    $update_api = MASTERPRESS_UPDATE_API;
    
    if (defined("MASTERPRESS_UPDATE_API_TEST")) {
      $update_api = MASTERPRESS_UPDATE_API_TEST;
    }
    
    $plugin_slug = plugin_basename( __FILE__ );
    
    // Check if the plugins API is about this plugin

    if (isset($args->slug)) {
      if ($args->slug != $plugin_slug) {
        return $false;
      }
    } else {
      return $false;
    }
    
    // Prepare API request
    
    $args = array(
      'action' => 'plugin_information',
      'key' => get_site_option("mp_licence_key"),
      'plugin_name' => $plugin_slug,
      'domain' => $_SERVER['SERVER_NAME']
    );
    // Send the request for detailed information
    
    $response = self::update_request( $args );

    return $response;
    
  }
  
  
  public static function unregister_post_type($object_type) {
    global $wp_post_types;
    
    if (isset($wp_post_types[$object_type])) {
      unset($wp_post_types[$object_type]);
    }
  }

  
  public static function unregister_taxonomy_from_object_type($taxonomy, $object_type) {

  	global $wp_taxonomies;

  	if ( !isset($wp_taxonomies[$taxonomy]) )
  		return false;

  	if ( ! get_post_type_object($object_type) )
  		return false;

  	foreach (array_keys($wp_taxonomies[$taxonomy]->object_type) as $array_key) {
  		if ($wp_taxonomies[$taxonomy]->object_type[$array_key] == $object_type) {
  			unset ($wp_taxonomies[$taxonomy]->object_type[$array_key]);
  			return true;
  		}
  	}
  	return false;

  }     

  public static function get_cap_key($key) {
    global $wf;
      
    $ret = $key;
    
    if (MasterPress::$cap_mode == "standard") {
      $ret = "manage_options";
    } 
    
    $ret = apply_filters("mp_cap_key", $ret, $key);

    if (in_array($key, array("manage_user_roles", "edit_user_roles", "manage_mp_settings")) && $wf->the_user->is_an("Administrator")) {
      // ensure that admins can perform functions to allow themselves access
      $ret = "manage_options";
    }
    
    return $ret;

  }
  
  public static function current_user_can($key, $op = "or") {
    
    if (MasterPress::$cap_mode != "specific") {
      return current_user_can("manage_options");
    } else {
      
      $delim = ",";
      
      if (preg_match("/\\+/", $key)) {
        $op = "and";
        $delim = "+";
      }
      
      if (!is_array($key)) {
        $keys = explode($delim, $key);
      } else {
        $keys = $key;
      }
      
      if (count($keys) == 1) {
        return current_user_can(self::get_cap_key($keys[0]));
      } else {
        
        
        $can = $op == "or" ? false : true;
        
        foreach ($keys as $key) {
          if ($op == "or") {
            $can = $can || current_user_can(self::get_cap_key($key));
          } else {
            $can = $can && current_user_can(self::get_cap_key($key));
          }
        }
        
        return $can;
      }
     
    }

  }
  
  public static function enqueue_mediaelement() {
    wp_enqueue_script( 'mediaelement' );
    wp_enqueue_style( 'mediaelement' );
  }

  
  
  public static function enqueue_codemirror() {
    
    wp_enqueue_script( 'codemirror' );
    wp_enqueue_script( 'codemirror-modes' );

    wp_enqueue_style( 'codemirror' );
    wp_enqueue_style( 'codemirror-themes' );
  
  }
  

  

} // MasterPress
