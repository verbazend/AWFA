<?php

class MPC_Roles extends MPC {

  public $caps = array(
    "create"  => "create_user_roles",
    "edit"    => "edit_user_roles",
    "delete"  => "delete_user_roles"
  );
  
  public function init() {
    
    $action = MasterPress::$action;
    
    // inform the view what to render in the info panel
    MasterPress::$view->is_role_set = true;

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
 
  public function delete($args) {
    
    
    global $wf;

    if (isset($args)) {
      parent::delete($args);
    } else {
      
      MPV::incl("roles");
    
      if ($this->is_postback()) {
      
        // delete the role
      
        $role = $wf->role(MasterPress::$id);
      
        if ($role && !is_woof_silent($role)) {
          remove_role($role->id);
          wp_redirect( MasterPress::admin_url( "roles", "manage", array("id" => MasterPress::$id), false ) );
          exit;
        }
      
      } else {


        $this->setup_view( array(
            "view" => "roles",
            "title_args" => array( 
              "text" => MPV_Roles::__p() 
            )
          )
        );    
      
        $this->manage();
      
      }
    
    }
    
  }
 
  
  public function submit() {
    
    global $wpdb, $wf;
    
    $action = MasterPress::$action;

    if ($action == "create") {
      
      $role_id = $_POST["name"];
      
      if ($role_id == "") {
        MPV::err(__("A role name must be provided", MASTERPRESS_DOMAIN) );
        return false;
      }
      
      // check that there isn't a role of this name already
      
      $role = $wf->role($role_id);
      
      if ($role && !is_woof_silent($role)) {
        MPV::err( sprintf( __("Sorry a role named %s already exists. Please choose another name", MASTERPRESS_DOMAIN), $role_id ) );
        return false;
      }
      
      // all okay, save the role
      
      if (isset($_POST["display_name"]) && trim($_POST["display_name"]) != "") {
        $display_name = $_POST["display_name"];
      } else {
        $display_name = WOOF_Inflector::titleize($role_id);
      }
      
      $caps = array_keys($_POST["cap"]);
      
      foreach ($_POST["new_caps"] as $cap) {
        
        if (trim($cap) != "") {
          $norm = WOOF_Inflector::underscore($cap);
        
          if (!in_array($norm, $_POST["all_caps"])) {
            $caps[] = $norm;
          }
        }
      
      }
         
      
      add_role($role_id, $display_name, array_fill_keys($caps, true) );
      
      return true;

    }  else if ($action == "edit") {

      $role_id = MasterPress::$id;

      $wf_role = $wf->role($role_id);
      
      if (!is_woof_silent($wf_role)) {
        
        $role = get_role($wf_role->id());
        
        $all_caps = explode(",", $_POST["all_caps"]);
        $selected_caps = array_keys($_POST["cap"]);

        $role_caps = array_keys($role->capabilities);
      
        $add_caps = array_diff($selected_caps, $role_caps);
        $remove_caps = array_diff($all_caps, $selected_caps);
        
        foreach ($remove_caps as $cap) {
          
          if ($role->has_cap($cap)) {
            $role->remove_cap($cap);
          }
          
        }


        foreach ($_POST["new_caps"] as $cap) {
        
          if (trim($cap) != "") {
            $norm = WOOF_Inflector::underscore($cap);
            
            if (!in_array($norm, $all_caps)) {
              $add_caps[] = $norm;
            }
          }
      
        }
      
        foreach ($add_caps as $cap) {

          if (!$role->has_cap($cap)) {
            $role->add_cap($cap);
          }
        
        }
        
      
      }

      return true;

    } else if ($action == "create-field-set" || $action == "edit-field-set") {
      
      $field_set = new MPM_RoleFieldSet();
      // consume the post data
      
      $field_set->name = $_POST["name"];
      $field_set->singular_name = $_POST["singular_name"];
      $field_set->disabled = isset($_POST["disabled"]);
      $field_set->labels = $_POST["labels"];
      $field_set->visibility = $_POST["visibility"];
      $field_set->capabilities = self::handle_capabilities();
      $field_set->allow_multiple = isset($_POST["allow_multiple"]);
      $field_set->type = "r"; // r = role
      $field_set->icon = self::handle_icon("icon", "icon_select");
      $field_set->position = $_POST["position"];
      $field_set->expanded = isset($_POST["expanded"]);
      $field_set->sidebar = false;
      $field_set->visibility = $this->get_visibility_val("sites,roles");
      
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

    if (!MasterPress::current_user_can("manage_user_role_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to manage user role field sets.", MASTERPRESS_DOMAIN));
    }
    
    MPV::incl("role-field-sets");
    
    $actions = array();
    
    if (MasterPress::current_user_can("create_user_role_field_sets")) {
      $actions = array( MPV::action_button("roles", "create-field-set", MPV::__create( "" ), array("parent" => MasterPress::$parent), array('class' => 'add-new-h2') ) );
    }
    
    
    $this->setup_view( array(
        "cap_verified" => true,
        "view" => "role-field-sets",
        "method" => "grid",
        "title_args" => array( 
          "actions" => $actions, 
          "info_panel" => true,
          "text" => MPV_RoleFieldSets::__p() 
        )
      )
    );

  }


  // -- EDIT FIELD GROUP

  public function edit_field_set() {

    if (!MasterPress::current_user_can("edit_user_role_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to edit user role field sets.", MASTERPRESS_DOMAIN));
    }

    $this->edit( array(
        "cap_verified" => true,
        "view" => "role-field-sets",
        "model" => "field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
    
  }

  // -- CREATE FIELD GROUP

  public function create_field_set() {

    if (!MasterPress::current_user_can("create_user_role_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to create user role field sets.", MASTERPRESS_DOMAIN));
    }

    $this->create( array(
        "cap_verified" => true,
        "view" => "role-field-sets",
        "model" => "role-field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
    

  }

  public function create_field() {

    if (!MasterPress::current_user_can("create_user_role_fields")) {
      wp_die(__("Sorry, you do not have the required capability to create user role fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("create_user_role_fields")) {
      wp_die(__("Sorry, you do not have the required capability to edit user role fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("create_user_role_fields")) {
      wp_die(__("Sorry, you do not have the required capability to delete user role field sets.", MASTERPRESS_DOMAIN));
    }
    
    $this->delete( array(
        "cap_verified" => true,
        "manage" => "manage-field-sets",
        "model" => "role-field-set",
      )
    );
    
  }

  public function delete_field() {

    if (!MasterPress::current_user_can("delete_user_role_fields")) {
      wp_die(__("Sorry, you do not have the required capability to delete user role fields.", MASTERPRESS_DOMAIN));
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
        __("User Roles are groupings for users within your current site.", MASTERPRESS_DOMAIN ) 
      )

    );

  }

  public function manage_field_sets_help() {

    return array( 
      
      MPV::overview_tab( 
        __("<em>User Role</em> Field Sets define the grouping and types of custom content fields available to <em>this specific User Role</em>.", MASTERPRESS_DOMAIN ) 
      )

    );

  }
  
  
}

?>