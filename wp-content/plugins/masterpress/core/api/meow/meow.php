<?php

/* 

  Title: MEOW - MasterPress Extensions Of WOOF
    
  Description:
    MEOW extends the objects in WOOF (Wordpress Object-Oriented Framework) 
    with methods that allow access of the custom fields within your WordPress 
    posts and pages, allowing for an extremely concise and very powerful template language. 

  Author Info:
    Created By - Traversal <http://traversal.com.au>
  
  Requires: 
    Wordpress 3.2 - http://wordpress.org
    PHP 5.2.3

*/

global $mp;

MPU::incl("api/woof/woof.php");

function meow_autoloader($class) {
  $file_name = woof_dasherize($class);
  $path = plugin_dir_path( __FILE__ ) . $file_name . '.php';
  
  if (file_exists($path)) {
    include_once($path);
  }
}


if (function_exists("spl_autoload_register")) {
  
  spl_autoload_register('meow_autoloader');

} else {

  MPU::incl("api/meow/meow-data-provider.php");
  MPU::incl("api/meow/meow-field-set-collection.php");
  MPU::incl("api/meow/meow-virtual-field-set-collection.php");
  MPU::incl("api/meow/meow-field-set.php");
  MPU::incl("api/meow/meow-field-set-creator.php");
  MPU::incl("api/meow/meow-field.php");
  MPU::incl("api/meow/meow-field-creator.php");
  MPU::incl("api/meow/meow-post.php");
  MPU::incl("api/meow/meow-attachment.php");
  MPU::incl("api/meow/meow-post-type.php");
  MPU::incl("api/meow/meow-site.php");
  MPU::incl("api/meow/meow-taxonomy.php");
  MPU::incl("api/meow/meow-user.php");
  MPU::incl("api/meow/meow-term.php");
  MPU::incl("api/meow/meow-rest-api.php");

}

/* 
  MEOW: The top-level object for the new MasterPress Object-Oriented API
  This is accessed through the new global variable $mp (masterpress framework)
*/


class MEOW extends WOOF {

  // re-define directories that point to WOOF-specific directories
  public $content_dir = MASTERPRESS_CONTENT_DIR; 
  public $content_url = MASTERPRESS_CONTENT_URL;

  public $content_image_cache_folder = MASTERPRESS_CONTENT_IMAGE_CACHE_FOLDER;
  public $content_image_cache_url = MASTERPRESS_CONTENT_IMAGE_CACHE_URL;
  public $content_image_cache_dir = MASTERPRESS_CONTENT_IMAGE_CACHE_DIR;

  public $content_image_from_url_folder = MASTERPRESS_CONTENT_IMAGE_FROM_URL_FOLDER;
  public $content_image_from_url_url = MASTERPRESS_CONTENT_IMAGE_FROM_URL_URL;
  public $content_image_from_url_dir = MASTERPRESS_CONTENT_IMAGE_FROM_URL_DIR;

  public $content_file_from_url_folder = MASTERPRESS_CONTENT_FILE_FROM_URL_FOLDER;
  public $content_file_from_url_url = MASTERPRESS_CONTENT_FILE_FROM_URL_URL;
  public $content_file_from_url_dir = MASTERPRESS_CONTENT_FILE_FROM_URL_DIR;

  protected static $_regard_field_terms = false;

  public $field_sets = array();
  
	protected $posts = array();
  protected $post_class = "MEOW_Post";
  protected $post_type_class = "MEOW_PostType";
  protected $user_class = "MEOW_User";
  protected $term_class = "MEOW_Term";
  protected $attachment_class = "MEOW_Attachment";
  protected $taxonomy_class = "MEOW_Taxonomy";
  protected $site_class = "MEOW_Site";
  protected $write_panel_info = array();
  protected $rest_api_class = "MEOW_REST_API";
  
  public $_rest_api;
  
  public function regard_field_terms($setting = null) {
    if (!is_null($setting)) {
      self::$_regard_field_terms = $setting;
    }
    
    return self::$_regard_field_terms;
  }
  
	function field($name, $set_index = 1, $object = NULL, $object_type = NULL) {
	  global $wf;
	  
	  if (is_null($object)) {
	    $object = $this->object();
	  } 

    list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($name);
	  
	  $set = $this->set($set_name);
	  
	  $set_item = $set[$set_index];
	  
	  return new MEOW_Field($name, $set_index, $object, $set_item);
  }

	function f($name, $set_index = 1, $object = NULL, $object_type = NULL) {
    return $this->field($name, $set_index, $object, $object_type);
  }

  protected function infer_object($object = NULL, $object_type = NULL) {

    global $meow_provider;
    
	  if (!$object) {
    
      if (is_null($object_type)) {
        if (is_tax()) {
          $object_type = "term";
        } else if (is_author()) {
          $object_type = "user";
        } else {
          $object_type = "post";
        }
      }

      if ($object_type == "post") {
        $object = $this->the();
      } else if ($object_type == "term") {
        $object = $this->the_term();
      } else if ($object_type == "user") {
        $object = $this->the_user();
      } else if ($object_type == "site") {
        $object = $this->the_site();
      } 
    
    }
    
    if ($object) {

      if (!$object_type) {
        $object_type = $meow_provider->type_key( $object );
      }

    } else {
      $object = new WOOF_Silent(__("There is no current object", MASTERPRESS_DOMAIN));
    }
    
    return array("object" => $object, "object_type" => $object_type);

  }
  
  function val($name, $set_index = 1, $object = NULL) {
    return $this->field($name, $set_index, $object)->val();
  }
  
  function set($name, $object = NULL, $object_type = NULL) {
    
    $info = $this->infer_object($object, $object_type);
    
    extract($info);
    
    if ($object->exists()) {
      
      $site_id = $object->site_id();
      
      $here = isset($this->field_sets[$site_id][$object_type][$object->id][$name]);
      
      if ($here) {
        // the set needs to be refetched if it has been updated
        $here = !$this->field_sets[$site_id][$object_type][$object->id][$name]->is_updated();
      }
      
      if (!$here) {

        $set = new MEOW_FieldSetCollection($name, $object);
      
        if (!isset($this->field_sets[$site_id])) {
          $this->field_sets[$site_id] = array();
        }

        if (!isset($this->field_sets[$site_id][$object_type])) {
          $this->field_sets[$site_id][$object_type] = array();
        }

        if (!isset($this->field_sets[$site_id][$object_type][$object->id] )) {
          $this->field_sets[$site_id][$object_type][$object->id]  = array();
        }
        
        if ($set->exists()) {
          $this->field_sets[$site_id][$object_type][$object->id][$name] = $set;
        } else {
          $this->field_sets[$site_id][$object_type][$object->id][$name] = new WOOF_Silent(sprintf(__("Cannot find the field set named '%s'", MASTERPRESS_DOMAIN), $name));
        }
      
      }
    
      return $this->field_sets[$site_id][$object_type][$object->id][$name];
    
    }
    
    return new WOOF_Silent(__("No current object to retrieve the property or set named '$name'", MASTERPRESS_DOMAIN));

  }
  
  
  
  // synonyms, for lazy programmers :) 

	function s($name, $object = NULL, $object_type = NULL) {
    return $this->set($name, $object, $object_type);
  }
  
  function __get($name) {

    // is we're accessing the set named "content" and it exists, prioritise this first
    // this allows us to call "content()" to get the standard content FIELD.
    
    if ($name == "content") {
      $set = $this->set("content");
      
      if ($set->exists()) {
        return $set;
      }
    }

    if ($name == "posts" || $name == "pages") {
      return parent::__get($name);
    }
    
    // first, look for a property named $name
    $result = $this->get($name);
    
    if (!is_woof_silent($result)) {
      return $result;
    }    
    
    // next, try to get a set named $name
    $result = $this->s($name);
    
    if (!is_woof_silent($result)) {
      return $result;
    }    

    // next try the parent, which will get post types, or taxonomies with this name
    return parent::__get($name);
    
  }
  
  function incoming($args = array()) {
    return $this->object()->incoming($args);
  }

  public function query_incoming($args = array()) {
    $r = wp_parse_args($args);
    $r["query"] = "1";
    return $this->incoming($r);
  }

  public function has_field($name) {
    return $this->object()->has_field($name);
  }
  
  public function has_set($name) {
    return $this->object()->has_set($name);
  }
  
  public function has($name) {
    return $this->object()->has($name);
  }


  // ---- JSON API functions
  

  public function parse_field_list($fields) {
    
    preg_match_all( "/([a-zA-Z0-9\_\.\:]+)(?:\(([a-zA-Z0-9\.\:\(\)\_\,]*?)\))?(?:\,?)/", $fields, $matches );
    
    $ret = array();
    
    foreach ($matches[1] as $index => $name) {
      
      $ret[$name] = true;

      if ($matches[2][$index]) {
        $ret[$name] = self::$this->parse_field_list( $matches[2][$index] );
      }

    }
    
    return $ret;
    
  }
  
  public function flatten_field_list($fields) {
    
    if ($fields) {
      $all = array();
    
      foreach($fields as $name => $sub_fields) {
        
        $suffix = "";
        
        if ($sub_fields) {
          $suffix = "(".$this->flatten_field_list($sub_fields).")";
        }
        
        $all[] = $name . $suffix;
      }
      
      return implode(",", $all);
    }
  
    return "";
    
  }
  
  
  public function reduce_fields( $from, $get ) {
    $use = array();

    foreach ($get as $name => $fields) {
  
      if (isset($from[$name])) {

        $use[$name] = true; 

        if (is_array($from[$name])) {
          $use[$name] = array(); 
        }
    
        if (is_array($fields)) {
          $use[$name] = $this->reduce_fields( $from[$name], $fields );
        }
    
      }
  
    } 

    return $use;
  }
  
  public function has_get($key) {
    if (isset($_GET[$key])) {
      return $_GET[$key];
    }
    
    return false;
  }
  
  public function filter_field_list( $from ) {

    $from_list = $this->parse_field_list( $from );
    
    if ( $get_list = $this->has_get( "fields" )) {
      $get = $this->parse_field_list( $get_list );
      
      if (count($get)) {
        return $this->reduce_fields( $from_list, $get );
      }
      
    }
    
    return $from_list;
  }
  
  
  
  public function rest_api() {
    
    if (!isset($this->_rest_api)) {
      
      if ( apply_filters( "mp_rest", false ) ) {
        $this->_rest_api = new MEOW_REST_API();
      } else {
        $this->_rest_api = new WOOF_Silent( __("The REST API is not available in this site", MASTERPRESS_DOMAIN) );
      }
      
    } 
    
    return $this->_rest_api;
    
  }
  
  public function rest_api_slug() {
    
    $api = $this->rest_api();
    
    if ($api->exists()) {
      return $api->slug;
    }
    
    return "";
    
  }
  
  public function eval_json_field($obj, $name, $fields) {  
    
    global $wf;
    
    if (is_array($fields)) {
      
      // check if this $name corresponds to a field set
      
      $set = $obj->set($name);
      
      if ($set->exists()) {
      
        if (count($fields)) {
          
          $val = array();
         
          if ($set->info->allow_multiple) {
          
            foreach ($set as $item) {
            
              $ival = array();
            
              foreach ($fields as $n => $sub_fields) {

                $set_field = $item->field($n);
              
                if ($set_field->exists()) {
                  
                  if ($set_field->blank()) {
                    $ival[$n] = null;
                  } else {
                    $ival[$n] = $set_field->json();
                  }
                
                } else {
                  $ival[$n] = $wf->eval_expression( $n, $item );
                }
            
              }
            
              $val[] = $ival;
            
            }
          
          
          } else {
          
            foreach ($fields as $n => $sub_fields) {

              $set_field = $set->field($n);
            
              if ($set_field->exists()) {
                
                if ($set_field->blank()) {
                  $val[$n] = null;
                } else {
                  $val[$n] = $set_field->json();
                }
                
              } else {
              
                $val[$n] = $wf->eval_expression( $n, $set );
              }

            }
          
          }
        
        } else {
          $val = (object) null;
        }
        
        return $val;
        
      } 
      
    }
      
    return false;
    
    
  }

}


// Create a Data Provider Object (for internal use only)


$meow_provider = new MEOW_DataProvider();

/* -- Instantiate the API for the front-end -- */

// reset $wf so that it uses the more specialised MEOW class

unset($wf); 
$wf = new MEOW();
