<?php

class MPC_Templates extends MPC {

  public $caps = array(
    "edit"    => "edit_templates"
  );

  protected function get_names($dir, &$names, &$dupes) {
    
    $handle = opendir($dir);

    // and scan through the items inside
    while (FALSE !== ($item = readdir($handle))) {
      
      $full_path = $dir.WOOF_DIR_SEP.$item;
      
      $pi = pathinfo($item);
      
      $file_name = $pi["basename"];
      
	  if (isset($pi["extension"])) {

		  $ext = strtolower($pi["extension"]);
      
	      if ($ext == "php") {
        
	        // find the template name
	        if ( preg_match( '|Template Name:(.*)$|mi', file_get_contents($full_path), $matches ) ) {
          
	          $tn = _cleanup_header_comment($matches[1]);
          
	          if (isset($names[$tn]) && $names[$tn] != $file_name) { 
	            // second condition ensures child theme overrides are ignored
	            $dupes[$tn] = $file_name; 
	          } else {
	            $names[$tn] = $file_name;
	          }
        
	        }
        
	      }

	  }	
	
      
    }
    
  }
  
  public function init() {
    
    global $wf;
    
    // check if we're in a child theme
    
    $template_dir = get_template_directory();
    $stylesheet_dir = get_stylesheet_directory();
    
    
    // check if there are any templates that have the same name, which can cause confusion

    $names = array(); 
    $dupes = array();
    
    self::get_names($template_dir, $names, $dupes);
    
    if ($stylesheet_dir != $template_dir) {
      self::get_names($stylesheet_dir, $names, $dupes);
    }
    
    
    if (count($dupes)) {
      
      $msg  = WOOF_HTML::tag("p", array(), __("<strong>Warning:</strong> your theme appears to contain multiple template files with the same <b>Template Name</b>.<br />This may cause some templates to become invisible to both WordPress and MasterPress.<br />Please verify the following, and make changes to your template files as necessary:", MASTERPRESS_DOMAIN)); 
      $msg .= WOOF_HTML::open("ul");
      
      foreach ($dupes as $template_name => $file_name) {
        $msg .= WOOF_HTML::tag("li", array(), sprintf( __('<span class="tt">%s</span> is called <b>%s</b>, which is already used by <span class="tt">%s</span>', MASTEPRESS_DOMAIN), $file_name, $template_name, $names[$template_name]) );
      }
         
      $msg .= WOOF_HTML::close("ul");

      MPV::warn($msg);
      
    }
  
    $action = MasterPress::$action;
    
    // inform the view what to render in the info panel
    MasterPress::$view->is_template_set = true;

    
    
    if ($action == "edit-field" || $action == "create-field") {
      // inform the view what to render in the info panel
      MasterPress::$view->parent = MPM_FieldSet::find_by_id(MasterPress::$parent);

      // enqueue the field type CSS
      $type = MasterPress::$model->type;
      
      MPFT::options_admin_head($type);

      // enqueue dependent scripts for all field types (could improve this in the future)
      foreach (MPFT::type_keys() as $type) {
        if ($ftc = MPFT::type_class($type)) {
          call_user_func( array($ftc, "enqueue") );
        }
      }


    }

    if ($action == "create-field") {
      MasterPress::$model->field_set_id = MasterPress::$parent;
      MasterPress::$model->infer_position();
    }

  }
  
  public function manage_actions() {
    return array();
  }
  
  public function submit() {
    
    global $wpdb;
    
    $action = MasterPress::$action;
      
    if ($action == "edit") {
      
      $template = new MPM_Template();
      
      if ($_POST["supports_type"] == "inherit") {
        $template->supports = "*";
      } else {
        $template->supports = implode(",", $_POST["supports"]);
      }

      $template->visibility = $this->get_visibility_val("post_types");
      
      $template->update(MasterPress::$id);
      
      return $template;
      
    } else if ($action == "create-field-set" || $action == "edit-field-set") {
      
      $field_set = new MPM_TemplateFieldSet();
      // consume the post data
      
      $field_set->name = $_POST["name"];
      $field_set->singular_name = $_POST["singular_name"];
      $field_set->disabled = isset($_POST["disabled"]);
      $field_set->labels = $_POST["labels"];
      $field_set->capabilities = self::handle_capabilities();
      $field_set->visibility = $_POST["visibility"];
      $field_set->allow_multiple = isset($_POST["allow_multiple"]);
      $field_set->type = "t"; // t = template
      $field_set->icon = self::handle_icon("icon", "icon_select");
      $field_set->position = $_POST["position"];
      $field_set->expanded = isset($_POST["expanded"]);
      $field_set->sidebar = isset($_POST["sidebar"]);
      $field_set->versions = $_POST["versions"];
      $field_set->visibility = $this->get_visibility_val();
      
      if (MPC::is_create()) {
        $field_set->insert();
      } else if (MPC::is_edit()) {
        $field_set->update(MasterPress::$id);
      }

      if ($field_set->is_valid()) {

        if (MPC::is_edit()) {
          global $meow_provider;
          $meow_provider->migrate_field_set_meta($field_set, $_POST["name_original"]);
        }

        // we don't attach post types to these. they are implicitly linked to the built-in "page" post type
        
        // update other menu positions
        
        $op = $_POST["other_position"];
        
        if (isset($op) && is_array($op)) {
          foreach ($op as $id => $position) {
            $wpdb->update(MPM::table("field-sets"), array( "position" => $position ), array( "id" => $id ), "%d", "%d" ); 
          }
          
        }
        
        
      }        
      
      return $field_set;
      
    } else if ($action == "delete-field-set") {
      
      $fg = MPM_FieldSet::find_by_id(MasterPress::$id);

      if ($fg) {
        $field_data_action = $_POST["field_data"];
        if ($field_data_action == "delete") {
          $fg->delete_meta();
        }
      }

      $fg->delete();
      
      return true;

    } else if ($action == "delete-field") {

      $f = MPM_Field::find_by_id(MasterPress::$id);

      if ($f) {

        $field_data_action = $_POST["field_data"];
        
        if ($field_data_action == "delete") {
          $f->delete_meta();
        }

      }
      
      $f->delete();
      
      return true;
      
    } else if ($action == "create-field" || $action == "edit-field") {
      
      // FIELD OPERATIONS (NOT FIELD GROUPS!)
       
      $field = new MPM_Field();
      // consume the post data
      
      $field->field_set_id = $_POST["parent"];
      $field->name = $_POST["name"];
      $field->disabled = isset($_POST["disabled"]);
      $field->required = isset($_POST["required"]);
      $field->summary_options = $_POST["summary_options"];
      $field->labels = $_POST["labels"];
      $field->icon = self::handle_icon("icon", "icon_select");
      $field->type = $_POST["type"]; 
      $field->type_options = $_POST["type_options"];
      $field->position = $_POST["position"];
      $field->visibility = $this->get_visibility_val();
      $field->capabilities = self::handle_capabilities();

      
      if (MPC::is_create()) {
        $field->insert();
      } else if (MPC::is_edit()) {
        $field->update(MasterPress::$id);
      }

      if ($field->is_valid()) {
        
        if (MPC::is_edit()) {
          global $meow_provider;
          $meow_provider->migrate_field_meta($field, $_POST["name_original"]);
        }

        // update other menu positions
        
        $op = $_POST["other_position"];
        
        if (isset($op) && is_array($op)) {
          foreach ($op as $id => $position) {
            $wpdb->update(MPM::table("fields"), array( "position" => $position ), array( "id" => $id ), "%d", "%d" ); 
          }
          
        }

        
      }    
      
      
      
      return $field;

    } 
    
    
    return false;
  }
  


  /* -- Field Set Actions -- */

  // -- MANAGE FIELD GROUPS
  
  public function manage_field_sets() {
    
    if (!MasterPress::current_user_can("manage_template_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to manage template field sets.", MASTERPRESS_DOMAIN));
    }
    
    MPV::incl("template-field-sets");
    
    $actions = array();
    
    if (MasterPress::current_user_can("create_template_field_sets")) {
      $actions = array( MPV::action_button("templates", "create-field-set", MPV::__create( "" ), array("parent" => MasterPress::$parent), array('class' => 'add-new-h2') ) );
    }
    
    $this->setup_view( array(
        "cap_verified" => true,
        "view" => "template-field-sets",
        "method" => "grid",
        "title_args" => array( 
          "actions" => $actions, 
          "info_panel" => true,
          "text" => MPV_TemplateFieldSets::__p() 
        )
      )
    );

  }


  // -- EDIT FIELD GROUP

  public function edit_field_set() {

    if (!MasterPress::current_user_can("edit_template_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to edit template field sets.", MASTERPRESS_DOMAIN));
    }

    $this->edit( array(
        "cap_verified" => true,
        "view" => "template-field-sets",
        "model" => "template-field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
    
  }

  // -- CREATE FIELD GROUP

  public function create_field_set() {

    if (!MasterPress::current_user_can("create_template_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to create template field sets.", MASTERPRESS_DOMAIN));
    }

    $this->create( array(
        "cap_verified" => true,
        "view" => "template-field-sets",
        "model" => "template-field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
    

  }

  public function create_field() {

    if (!MasterPress::current_user_can("create_template_fields")) {
      wp_die(__("Sorry, you do not have the required capability to create template fields.", MASTERPRESS_DOMAIN));
    }

    $this->create( array(
        "cap_verified" => true,
        "view" => "fields",
        "model" => "field",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
  }

  public function edit_field() {

    if (!MasterPress::current_user_can("edit_template_fields")) {
      wp_die(__("Sorry, you do not have the required capability to edit template fields.", MASTERPRESS_DOMAIN));
    }

    $this->edit( array(
        "cap_verified" => true,
        "view" => "fields",
        "model" => "field",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
  }

  public function delete_field_set() {

    if (!MasterPress::current_user_can("create_template_fields")) {
      wp_die(__("Sorry, you do not have the required capability to delete template field sets.", MASTERPRESS_DOMAIN));
    }

    $this->delete( array(
        "cap_verified" => true,
        "manage" => "manage-field-sets",
        "model" => "template-field-set",
      )
    );
    
  }

  public function delete_field() {

    if (!MasterPress::current_user_can("delete_template_fields")) {
      wp_die(__("Sorry, you do not have the required capability to delete template fields.", MASTERPRESS_DOMAIN));
    }

    $this->delete( array(
        "cap_verified" => true,
        "manage" => "manage-field-sets",
        "model" => "field",
      )
    );
    
  }
  
  
  public function manage_help() {

    return array( 
      
      MPV::overview_tab( 
        __("Templates are PHP-based HTML files in your theme directory that customise the presentation of one or more Pages in your site.", MASTERPRESS_DOMAIN ) 
      )

    );

  }

  
  public function manage_field_sets_help() {

    return array( 
      
      MPV::overview_tab( 
        __("<em>Template</em> Field Sets define the grouping and types of custom content fields available to <em>this specific Template</em>.", MASTERPRESS_DOMAIN ) 
      )

    );
    
  }
  
  
  
}

?>