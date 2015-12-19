<?php 

class MPC_Taxonomies extends MPC {

  public $caps = array(
    "create"  => "create_taxonomies",
    "edit"    => "edit_taxonomies",
    "delete"  => "delete_taxonomies"
  );

  public function init() {
    $action = MasterPress::$action;
    
    // inform the view what to render in the info panel
    MasterPress::$view->is_taxonomy_set = true;

    if ($action == "manage-field-sets" || $action == "create-field-set" || $action == "edit-field-set" || $action == "delete-field-set"  || $action == "delete-field") {
      // inform the view what to render in the info panel
      MasterPress::$view->parent = MPM_Taxonomy::find_by_id(MasterPress::$parent);
    } 

    if ($action == "edit-field" || $action == "create-field") {
      // inform the view what to render in the info panel
      MasterPress::$view->parent = MPM_FieldSet::find_by_id(MasterPress::$parent);
      
      $p = MasterPress::$view->parent;
      
      // enqueue dependent scripts for all field types (could improve this in the future)
      foreach (MPFT::type_keys() as $type) {
        if ($ftc = MPFT::type_class($type)) {
          call_user_func( array($ftc, "enqueue") );
        }
      }
      

      // enqueue the field type CSS
      $type = MasterPress::$model->type;
      
      MPFT::options_admin_head($type);
      
      
    } 

    if ($action == "create-field-set") { 
      MasterPress::$model->infer_position();
    }
    
    if ($action == "create-field") {
      MasterPress::$model->field_set_id = MasterPress::$parent;
      MasterPress::$model->infer_position();
    }

  }
  
  public function submit() {

    global $wpdb;
    
    $action = MasterPress::$action;
    
    if ($action == "create" || $action == "edit") {

      $builtin = $_POST["_builtin"] == "true";
      $external = $_POST["_external"] == "true";
      
      if ($builtin) {
        
        $tax = MPM_Taxonomy::find_by_name($_POST["name"]);
        $tax->disabled = isset($_POST["disabled"]);
        $tax->object_type = $_POST["post_types"];
        $tax->visibility = $this->get_visibility_val("sites");
        $tax->title_icon = self::handle_icon("title_icon", "title_icon_select");
        $tax->columns = $_POST["columns"];

        if ($action == "edit") {
          $tax->update(MasterPress::$id);
        }

      } else if ($external) {

        $tax = MPM_Taxonomy::find_by_name($_POST["name"]);
        $tax->columns = $_POST["columns"];
        $tax->title_icon = self::handle_icon("title_icon", "title_icon_select");

        if ($action == "edit") {
          $tax->update(MasterPress::$id);
        }
        
      } else {
        
        $tax = new MPM_Taxonomy();
    
        // consume the post data
        $tax->name = $_POST["name"];
        $tax->plural_name = $_POST["plural_name"];
        $tax->object_type = MPC::post_val("post_types");
        $tax->disabled = isset($_POST["disabled"]);
        $tax->labels = $_POST["labels"];
        $tax->show_ui = isset($_POST["show_ui"]);
        $tax->hide_term_ui = isset($_POST["hide_term_ui"]);
        $tax->show_in_nav_menus = isset($_POST["show_in_nav_menus"]);
        $tax->show_manage_filter = isset($_POST["show_manage_filter"]);
        $tax->show_tagcloud = isset($_POST["show_tagcloud"]);
        $tax->hierarchical = isset($_POST["hierarchical"]);
        $tax->query_var = $_POST["query_var"];
        $tax->title_icon = self::handle_icon("title_icon", "title_icon_select");
        $tax->capabilities = $_POST["capabilities"];
        $tax->update_count_callback = $_POST["update_count_callback"];
        $tax->visibility = $this->get_visibility_val("sites,taxonomies");
        $tax->columns = $_POST["columns"];

        $rewrite = array(
          "slug" => $_POST["rewrite"]["slug"],
          "with_front" => isset($_POST["rewrite"]["with_front"]),
          "hierarchical" => isset($_POST["rewrite"]["hierarchical"])
        );

        $tax->rewrite = $rewrite;
        $tax->_builtin = false;
        $tax->_external = $external;

        if ($action == "create") {
          $tax->insert();
        } else if ($action == "edit") {
          $tax->update(MasterPress::$id);

          if ($tax->is_valid()) {
            global $meow_provider;
            $meow_provider->migrate_taxonomy($tax, $_POST["name_original"]);
          }

        }
        
      }
      
      if ($tax->is_valid()) {
        MasterPress::flag_for_rewrite();
      } 
    
      return $tax;
      
    } else if ($action == "delete") {
      
      $tax = MPM_Taxonomy::find_by_id(MasterPress::$id);
      
      if ($tax) {

        $tax->delete(
          array(
            "existing_terms" => MPC::post_val("existing_terms"),
            "existing_terms_reassign_taxonomy" => MPC::post_val("existing_terms_reassign_taxonomy"),
            "field_sets" => MPC::post_val("field_sets"),
            "field_data" => MPC::post_val("field_data")
          )
        );
      
      }
      
      MasterPress::flag_for_rewrite();

      return true;
    
    } else if ($action == "create-field-set" || $action == "edit-field-set") {
      
      $field_set = new MPM_TaxonomyFieldSet();
      // consume the post data
      
      $field_set->name = $_POST["name"];
      $field_set->singular_name = $_POST["singular_name"];
      $field_set->disabled = isset($_POST["disabled"]);
      $field_set->labels = $_POST["labels"];
      $field_set->allow_multiple = isset($_POST["allow_multiple"]);
      $field_set->visibility = $_POST["visibility"];
      $field_set->capabilities = self::handle_capabilities();
      $field_set->icon = self::handle_icon("icon", "icon_select");
      $field_set->type = "x"; // x = taXonomy
      $field_set->position = $_POST["position"];
      $field_set->expanded = isset($_POST["expanded"]);
      $field_set->sidebar = isset($_POST["sidebar"]);
      $field_set->versions = $_POST["versions"];
      $field_set->visibility = $this->get_visibility_val("sites,taxonomies");

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
      
      $fg = MPM_TaxonomyFieldSet::find_by_id(MasterPress::$id);
      
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
      $field->summary_options = $_POST["summary_options"];
      $field->required = isset($_POST["required"]);
      $field->labels = $_POST["labels"];
      $field->icon = self::handle_icon("icon", "icon_select");
      $field->type = $_POST["type"]; 
      $field->type_options = $_POST["type_options"];
      $field->position = $_POST["position"];
      $field->visibility = $this->get_visibility_val("sites,taxonomies");
      $field->capabilities = self::handle_capabilities();

      $fg = MPM_TaxonomyFieldSet::find_by_id($_POST["parent"]);
        
        
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
  
  }



  /* -- Field Set Actions -- */

  // -- MANAGE FIELD GROUPS
  
  public function manage_field_sets() {
    
    if (!MasterPress::current_user_can("manage_taxonomy_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to manage taxonomy field sets.", MASTERPRESS_DOMAIN));
    }
    
    MPV::incl("taxonomy-field-sets");
    
    $actions = array();
    
    if (MasterPress::current_user_can("create_taxonomy_field_sets")) {
      $actions = array( MPV::action_button( $this->key(), "create-field-set", MPV::__create( "" ), array("parent" => MasterPress::$parent), array('class' => 'add-new-h2') ) );
    }
    
    $this->setup_view( array(
        "cap_verified" => true,
        "view" => "taxonomy-field-sets",
        "method" => "grid",
        "title_args" => array( 
          "actions" => $actions, 
          "info_panel" => true,
          "text" => MPV_TaxonomyFieldSets::__p() 
        )
      )
    );

  }


  // -- EDIT FIELD GROUP

  public function edit_field_set() {

    if (!MasterPress::current_user_can("edit_taxonomy_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to edit taxonomy field sets.", MASTERPRESS_DOMAIN));
    }

    $this->edit( array(
        "cap_verified" => true,
        "view" => "taxonomy-field-sets",
        "model" => "taxonomy-field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
    
  }

  // -- CREATE FIELD GROUP

  public function create_field_set() {

    if (!MasterPress::current_user_can("create_taxonomy_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to create taxonomy field sets.", MASTERPRESS_DOMAIN));
    }

    $this->create( array(
        "cap_verified" => true,
        "view" => "taxonomy-field-sets",
        "model" => "taxonomy-field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
    

  }

  public function create_field() {

    if (!MasterPress::current_user_can("create_taxonomy_fields")) {
      wp_die(__("Sorry, you do not have the required capability to create taxonomy fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("edit_taxonomy_fields")) {
      wp_die(__("Sorry, you do not have the required capability to edit taxonomy fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("delete_taxonomy_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to delete taxonomy field sets.", MASTERPRESS_DOMAIN));
    }

    $this->delete( array(
        "cap_verified" => true,
        "manage" => "manage-field-sets",
        "model" => "field-set",
      )
    );
    
  }

  public function delete_field() {

    if (!MasterPress::current_user_can("delete_taxonomy_fields")) {
      wp_die(__("Sorry, you do not have the required capability to delete taxonomy fields.", MASTERPRESS_DOMAIN));
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
        __("Taxonomies allow you to <em>classify posts and pages</em>, which is highly useful for presenting filtered lists of them throughout your site.", MASTERPRESS_DOMAIN ) 
      )

    );

  }

  
}

?>
