<?php

class MPC {

  public $action = "";
  public $id;
  public $parent;
  
  protected $insert_id;
  protected $errors;
  protected $field_errors;

  protected $key;

  public $caps = array();
  
  public function cap($key) {
    $caps = $this->caps;
    
    if (isset($caps[$key])) {
      return $caps[$key];
    }
    
    return "manage_options";
  }
  
  public static function incl($file, $base = "core/controller/mpc-") {
    include_once(MPU::path($base.$file).".php");
  }

  
  public static function is_edit() {
    $action = MasterPress::$action;
    
    if (isset($_GET["source_action"])) {
      $action = $_GET["source_action"];
    }

    return substr($action, 0, 4) == "edit";
  }

  public static function is_delete() {
    $action = MasterPress::$action;

    if (isset($_GET["source_action"])) {
      $action = $_GET["source_action"];
    }

    return substr($action, 0, 6) == "delete";
  }
  
  public static function is_deleting($id, $action = "") {
    if ($action != "") {
      return MasterPress::$action == $action && MasterPress::$id == $id;
    } else {
      return MPC::is_delete() && MasterPress::$id == $id;
    }
  }
  
  public static function is_create() {
    $action = MasterPress::$action;
    
    if (isset($_GET["source_action"])) {
      $action = $_GET["source_action"];
    }
    
    return substr($action, 0, 6) == "create";    
  }

  public static function action_type() {
    $parts = explode("-", MasterPress::$action);
    
    return $parts[0];
  }
  
  
  public static function handle_icon($upload_key, $select_key) {
    
    if (isset($_REQUEST[$select_key]) && $_REQUEST[$select_key] != "") {
      
      // the icon selector has been used

      $val = $_REQUEST[$select_key];
      
      // check if this icon has been copied across already.
      
      $file_name = "lib-".str_replace("/", "--", $val);
      
      $src = MASTERPRESS_EXTENSIONS_ICONS_DIR.$val;
      $dest = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$file_name;

      if (!file_exists($dest)) {
        if (copy($src, $dest)) {
          return $file_name;
        }
      } else {
        return $file_name;
      }

      
    } 
    
    if (isset($_REQUEST[$upload_key])) {
      return $_REQUEST[$upload_key];
    }
  
    return "";
    
  }


  public static function handle_capabilities() {
    
    $caps = array();
    
    foreach (array_keys($_REQUEST["capabilities"]) as $key) {

      if (isset($_REQUEST["capabilities_custom"][$key]) && $_REQUEST["capabilities_custom"][$key] != "") {
        // a custom value has been entered, so use that
        $caps[$key] = $_REQUEST["capabilities_custom"][$key];
      } else if (isset($_REQUEST["capabilities"][$key])) {
        $caps[$key] = $_REQUEST["capabilities"][$key];
      }  
    
    }

    return $caps;

  }
  
  public function error_count() {
    return count($this->errors) + count($this->field_errors);
  }
  
  public function validate($action) {
    return true;
  }

  
  public function get_visibility_part($part) {

    if (isset($_POST["visibility_type"])) {
      $vt = $_POST["visibility_type"];
	  }

    $val = "*";
    
    if (isset($_POST["visibility"][$part])) {
      $val = $_POST["visibility"][$part];
    } else {
      
      if (isset($vt[$part])) {
        // keep an empty selection if the visibility type is indeed settable
        $val = "";
      } 
    
    }

    $ret = array(
      "key" => $part,
      "val" => $val // carry through the value if the type preference is not provided
    );
    
    $selected = "";
    
    if (isset($_POST["visibility_".$part])) {
      $selected = implode(",", $_POST["visibility_".$part]);
    } 
    
    $vtp = "";
    
    if (isset($vt[$part])) {
      $vtp = $vt[$part];
    }
    
    
    if ($vtp == "allow") {
      $ret["val"] = $selected;
    } else if ($vtp == "deny") {
      $ret["key"] = "not_".$part;
      $ret["val"] = $selected;
    } else if ($vtp == "all") {
      $ret["val"] = "*";
    } else if ($vtp == "none") {
      $ret["val"] = "";
    } 

    if (is_null($ret["val"]) && is_null($vtp)) {
      $ret["val"] = "*";
    }
    
    return $ret;

  }
  
	public static function post_val($key) {
		if (isset($_POST[$key])) {
			return $_POST[$key];
		}
		
		return "";
	}

	public static function post_implode_val($key, $sep = ",") {
		if (isset($_POST[$key])) {
			return implode($sep, $_POST[$key]);
		}
		
		return "";
	}

  public function get_visibility_val($supports = "templates,sites,post_types,roles,taxonomies") {
    
    if (!is_array($supports)) {
      $supports = explode(",", $supports);
    }
    
    $visibility = array();

    $sites = $this->get_visibility_part("sites");
    $visibility[$sites["key"]] = $sites["val"];


    if (in_array("templates", $supports)) {
      $templates = $this->get_visibility_part("templates");
      $visibility[$templates["key"]] = $templates["val"];
    }
    
    if (in_array("post_types", $supports)) {
      $post_types = $this->get_visibility_part("post_types");
      $visibility[$post_types["key"]] = $post_types["val"];
    }

    if (in_array("roles", $supports)) {
      $roles = $this->get_visibility_part("roles");
      $visibility[$roles["key"]] = $roles["val"];
    }

    if (in_array("taxonomies", $supports)) {
      $taxonomies = $this->get_visibility_part("taxonomies");
      $visibility[$taxonomies["key"]] = $taxonomies["val"];
    }
    
    return $visibility;

  }
  
  
  public function submit() {
    return true;
  }
  
  public function manage_actions() {
    if (MasterPress::current_user_can($this->cap("create"))) {
      return array("create");
    }
  }
  
  public function setup_view($args = array()) {
    
    $class = get_class($this);
    
    $defaults = array(
      "view" => $this->view_key(),
      "title_args" => array(),
      "form" => false,
      "method" => "manage",
      "method_args" => array()
    );
    
    $r = wp_parse_args($args, $defaults);

    MasterPress::$view_class = MasterPress::view_class($r["view"]);
    
    MPV::incl($r["view"]);

    $view_class = MasterPress::$view_class;
    MasterPress::$view = new $view_class();
    
    $view = MasterPress::$view;

    $view->title_args = $r["title_args"];
    $view->method = $r["method"];
    $view->method_args = $r["method_args"];
    
    // auto-form informs MasterPress that it should enclose the view in a form with an appropriate
    // action attribute to post back to the correct controller / action, based on the current values
    // of MasterPress::$controller and MasterPress::$action
    $view->auto_form = $r["form"];

    $this->init();

  }
  
  public function init() {
    
  }
  
  public function manage() {

    $view_key = $this->view_key();
    
    MPV::incl($view_key);
    MasterPress::$view_class = MasterPress::view_class($view_key);
    
    $view_class = MasterPress::$view_class;
    MasterPress::$view = new $view_class();
    
    $view = MasterPress::$view;
    
    $view->title_args = array( 
      "text"    => call_user_func( array( $view_class, "__p" ) ),
      "actions" => $this->manage_actions(),
      "controller" => $this->key()
    );

    $view->method = "grid";

    $this->init();
  }

  public function delete($args = array()) {


    $defaults = array(
      "model" => $this->model_key(),
      "view" => $this->view_key(),
      "manage" => "manage",
      "cap_verified" => false
    );

    $r = wp_parse_args($args, $defaults);

    if (!$r["cap_verified"] && !MasterPress::current_user_can($this->cap("edit"))) {
      wp_die(__("Sorry, you do not have the required capability to delete this item.", MASTERPRESS_DOMAIN));
    }

    $model_class = MasterPress::model_class($r["model"]);
    
    $qs = array("from" => MasterPress::$action);
          
    if (isset(MasterPress::$parent)) {
      $qs["parent"] = MasterPress::$parent;
    }

    
    if ($this->is_postback()) {
      
      if (apply_filters("mp_mpc_delete", $do = true)) {

        if ($this->submit()) {
          wp_redirect( MasterPress::admin_url( $this->key(), $r["manage"], $qs, false ) );
          exit();
        }
      
      } else {
        wp_redirect( MasterPress::admin_url( $this->key(), $r["manage"], $qs, false ) );
        exit();
      }
    
    } else {

      // when not a postback, the delete action is a confirmation only  (the view will check if this is a delete action and render the confirmation)
      
      $method = str_replace("-", "_", $r["manage"]);
      
      if (method_exists($this, $method)) {
        call_user_func(array($this, $method));
      }

    }
    
    $this->init();


  }
    
  public function log_model_errors($model) {
    $model->bubble_errors($this->errors, $this->field_errors);
    
    if ($this->error_count()) {
      
      foreach( $this->field_errors as $field => $error ) {
        MPV::err($error);
      }

      foreach( $this->errors as $error ) {
        MPV::err($error);
      }
      
    }
  }
  
  public function get_view_info($model) {
    $this->insert_id = $model->insert_id();
  }
  
  public function create($args = array()) {
    
    
    $model = null;

    $defaults = array(
      "init_model" => true,
      "view" => $this->view_key(), 
      "model" => $this->model_key(),
      "manage" => "manage",
      "title_args" => array(),
      "cap_verified" => false
    );

    $r = wp_parse_args($args, $defaults);

    if (!$r["cap_verified"] && !MasterPress::current_user_can($this->cap("create"))) {
      wp_die(__("Sorry, you do not have the required capability to create an item of this type.", MASTERPRESS_DOMAIN));
    }


    MasterPress::$model_class = MasterPress::model_class($r["model"]);
    MasterPress::$view_class = MasterPress::view_class($r["view"]);

    $view_class = MasterPress::$view_class;
    $model_class = MasterPress::$model_class;

    $manage = $r["manage"];

    MPV::incl($r["view"]);
    
    
    if ($this->is_postback()) {
              
      $key = $this->key();

      if (apply_filters("mp_mpc_create", $do = true)) {
      
        $model = $this->submit();
        MasterPress::$model = $model; 
      
        if ($model && is_object($model)) {
          $valid = $model->is_valid();
        } else {
          $valid = $model;
        } 
      
        if ( $valid ) {
          // hallelulah! we can now do proper redirects, rather than a fake action.
          // this prevents issues with refreshing the manage page after a save showing the form with "this item already exists"
        
          $qs = array("from" => MasterPress::$action);
        
          self::fill_redirect_parent($qs);

          if (is_object($model)) {
            $id = $model->id;

            if (isset($id)) {
              $qs["id"] = $id;
            }

          } else {
            $qs["id"] = MasterPress::$id;
          }
      
          wp_redirect( MasterPress::admin_url( $key, $manage, $qs, false ) );
          exit();
          return true;
        } else {
          if (is_object($model)) {
            $this->log_model_errors($model);
          }
        }
       
      } else {
        
        $qs = array( "id" => $_POST["id"], "from" => MasterPress::$action );

        self::fill_redirect_parent($qs);

        // submit disabled - simply redirect back to the manage screen
        wp_redirect( MasterPress::admin_url( $key, $manage, $qs, false ) );
        exit;
        
      } // apply_filters
       
    } else {
    
      if ($r["init_model"] && class_exists($model_class)) {
        $model = new $model_class();
        MasterPress::$model = $model; 
      } 

    }
    
    // set view variables
    
    // this is an unusual setup for MVC, as we need to delay the rendering of the view,
    // as this is done by WordPress admin menu hook for the plug-in 

    $this->setup_view( 
      array(
        "view" => $r["view"],
        "title_args" => wp_parse_args($r["title_args"], array( "text" => MPV::__create( call_user_func( array( $view_class, "__s" ) ) ), "actions" => "save", "info_panel" => false, "controller" => $this->key() )),
        "form" => true,
        "method" => "form",
        "method_args" => array( "type" => "create" ),
      )
    );
    

  }
  
  public function edit($args = array()) {
    
    $model = null;

    $defaults = array(
      "init_model" => true,
      "view" => $this->view_key(), 
      "model" => $this->model_key(),
      "manage" => "manage",
      "title_args" => array(),
      "cap_verified" => false
    );

    $r = wp_parse_args($args, $defaults);

    if (!$r["cap_verified"] && !MasterPress::current_user_can($this->cap("edit"))) {
      wp_die(__("Sorry, you do not have the required capability to edit this item.", MASTERPRESS_DOMAIN));
    }


    MasterPress::$model_class = MasterPress::model_class($r["model"]);
    MasterPress::$view_class = MasterPress::view_class($r["view"]);

    $view_class = MasterPress::$view_class;
    $model_class = MasterPress::$model_class;

    MPV::incl($r["view"]);

    $manage = $r["manage"];
    
    if ($this->is_postback()) {
      
      $key = $this->key();
        
      if (isset($_REQUEST["return"])) {
        $key = $_REQUEST["return"];

        if ($key == "masterpress") {
          $key = "";
        } else {
          $key = str_replace("masterpress-", "", $key);
        }
      
      }
          
      if (apply_filters("mp_mpc_edit", $do = true)) {

        $model = $this->submit();
        MasterPress::$model = $model; 
      
        $valid = false;
      
        if ($model && is_object($model)) {
          $valid = $model->is_valid();
        } else {
          $valid = $model;
        } 
      
        if ( $valid ) {
          // hallelulah! we can now do proper redirects, rather than a fake action.
          // this prevents issues with refreshing the manage page after a save showing the form with "this item already exists"

          $qs = array("from" => MasterPress::$action);
        
          self::fill_redirect_parent($qs);

          if (is_object($model)) {
            $id = $model->id;
        
            if (isset($id)) {
              $qs["id"] = $id;
            }
          } else {
            $qs["id"] = MasterPress::$id;
          }
      
          if (isset($_REQUEST["mp_redirect"])) {
            wp_redirect( $_REQUEST["mp_redirect"] . "&def_updated=1" );
            exit;
          }
          
          wp_redirect( MasterPress::admin_url( $key, $manage, $qs, false ) );

          exit();
          return true;
        } else {
          if (is_object($model)) {
            $this->log_model_errors($model);
          }
        }
      
      } else {

        $qs = array( "id" => $_POST["id"], "from" => MasterPress::$action );

        self::fill_redirect_parent($qs);
        
        // submit disabled - simply redirect back to the manage screen
        wp_redirect( MasterPress::admin_url( $key, $manage, $qs, false ) );
        exit;
        
      } // apply_filters
      
    } else {
      
      if ($r["init_model"] && class_exists($model_class)) {
        $model = call_user_func_array( array($model_class, "find_by_id"), array(MasterPress::$id) );
        MasterPress::$model = $model;
      }
    }
      
    // set view variables

    $this->setup_view( 
      array(
        "view" => $r["view"],
        "title_args" => wp_parse_args($r["title_args"], array( "text" => MPV::__edit( call_user_func( array( $view_class, "__s" ) ) ), "actions" => "update", "info_panel" => false, "controller" => $this->key() )),
        "form" => true,
        "method" => "form",
        "method_args" => array( "type" => "edit" ),
      )
    );

    
    
  }
  
 
  public function fill_redirect_parent(&$qs) {
    $p = self::redirect_parent();
    
    if ($p) {
      $qs["parent"] = $p;
    }
  }
  
  public function redirect_parent() {
    if (isset(MasterPress::$gparent)) {
      return MasterPress::$gparent;
    }

    if (isset(MasterPress::$parent)) {
      return MasterPress::$parent;
    }
  }
  
  public function is_postback() {
    return isset($_POST["postback"]);
  }
  
  
  
      
  public function key() {
    
    if (!$this->key) {
      
      $class = get_class($this);
      
      $parts = explode("_", $class);
    
      if (count($parts) > 1) {
        $this->key = MPU::dasherize($parts[1]);
      } else {
        $this->key = "";
      }
      
    }
      
    
    return $this->key;
  }

  public function model_key() {
    return WOOF_Inflector::singularize($this->key());
  }

  public function view_key() {
    return $this->key();
  }
		
	public function ajax_success($data = array()) {
    $r = wp_parse_args( $data, array( "success" => true ) );
    echo json_encode($r);
    exit();
	}						  
	
	public function ajax_error($error, $data = array()) {
    $r = wp_parse_args( $data, array( "error" => $error ) );
    echo json_encode($r);
    exit();
  }




}

?>