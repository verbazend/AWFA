<?php

class MPC_PostTypes extends MPC {

  public $caps = array(
    "create"  => "create_post_types",
    "edit"    => "edit_post_types",
    "delete"  => "delete_post_types"
  );
  
  public function init() {
    $action = MasterPress::$action;
    
    if ($action == "manage-field-sets" || $action == "create-field-set" || $action == "edit-field-set" || $action == "delete-field-set"  || $action == "delete-field") {
      // inform the view what to render in the info panel
      MasterPress::$view->parent = MPM_PostType::find_by_id(MasterPress::$parent);
    } 

    if ($action == "edit-field" || $action == "create-field") {
      // inform the view what to render in the info panel
      MasterPress::$view->parent = MPM_FieldSet::find_by_id(MasterPress::$parent);
      
      $p = MasterPress::$view->parent;
      
      if ($p) {
        MasterPress::$view->post_type = $p->post_type();
      }
      
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

        $post_type = MPM_PostType::find_by_name($_POST["name"]);
        $post_type->supports = implode(",", $_POST["supports"]);
        $post_type->disabled = isset($_POST["disabled"]);
        $post_type->visibility = $this->get_visibility_val("sites");
        $post_type->columns = $_POST["columns"];
        $post_type->labels = $_POST["labels"];
        
        $post_type->menu_icon = self::handle_icon("menu_icon", "menu_icon_select");
        
        if ($_POST["name"] == "page" && $post_type->menu_icon == "") {
          $post_type->menu_icon = "menu-icon-pages.png"; // restore default icon
        }

        if ($_POST["name"] == "post" && $post_type->menu_icon == "") {
          $post_type->menu_icon = "menu-icon-posts.png"; // restore default icon
        }
        
        if ($action == "edit") {
          $post_type->update(MasterPress::$id);
        }
        

      } else if ($external) {

        $post_type = MPM_PostType::find_by_name($_POST["name"]);
        $post_type->columns = $_POST["columns"];
        $post_type->menu_icon = self::handle_icon("menu_icon", "menu_icon_select");
        
        if ($action == "edit") {
          $post_type->update(MasterPress::$id);
        }
        
      } else {


        $post_type = new MPM_PostType(true);
    
        // consume the post data
        $post_type->name = $_POST["name"];
        $post_type->plural_name = $_POST["plural_name"];
        $post_type->disabled = isset($_POST["disabled"]);
        $post_type->labels = $_POST["labels"];
        $post_type->description = $_POST["description"];
        $post_type->publicly_queryable = isset($_POST["publicly_queryable"]);
        $post_type->exclude_from_search = isset($_POST["exclude_from_search"]);
        $post_type->show_ui = isset($_POST["show_ui"]);
        $post_type->show_in_menu = isset($_POST["show_in_menu"]);
        $post_type->hierarchical = isset($_POST["hierarchical"]);
        $post_type->menu_position = $_POST["menu_position"];
        $post_type->menu_sub_position = $_POST["menu_sub_position"];
        
        $post_type->menu_icon = self::handle_icon("menu_icon", "menu_icon_select");
        
        $post_type->manage_sort_order = $_POST["manage_sort_order"];
        
        $cap_type = $_POST["capability_type"];
        
        if ($cap_type == "specific") {
          $post_type->capability_type = $_POST["name"];
        } else if ($cap_type == "custom" && trim($_POST["capability_type_custom_value"]) != "") {
          $post_type->capability_type = $_POST["capability_type_custom_value"];
        } else {
          $post_type->capability_type = $cap_type;
        }
      
        $post_type->capabilities = MPC::post_val("capabilities");
        
        $post_type->map_meta_cap = isset($_POST["map_meta_cap"]);
        $post_type->hierarchical = isset($_POST["hierarchical"]);
        $post_type->supports = MPC::post_implode_val("supports");
        $post_type->permalink_epmask = $_POST["permalink_epmask"];
        $post_type->has_archive = isset($_POST["has_archive"]);
        $post_type->visibility = $this->get_visibility_val("sites,post_types");

        $post_type->show_in_menu = isset($_POST["show_in_menu"]);

        $rewrite = array(
          "slug" => $_POST["rewrite"]["slug"],
          "with_front" => isset($_POST["rewrite"]["with_front"]),
          "feeds" => isset($_POST["rewrite"]["feeds"])
        );
        
        $post_type->rewrite = $rewrite;
      
        $post_type->query_var = $_POST["query_var"];
        $post_type->can_export = isset($_POST["can_export"]);
        $post_type->show_in_nav_menus = isset($_POST["show_in_nav_menus"]);
        $post_type->_builtin = false;
        $post_type->_external = $_POST["_external"] == "true";
        $post_type->columns = $_POST["columns"];

        if ($action == "create") {
          $post_type->insert();
        } else if ($action == "edit") {
          $post_type->update(MasterPress::$id);

          if ($post_type->is_valid()) {
            global $meow_provider;
            $meow_provider->migrate_post_type($post_type, $_POST["name_original"]);
          }

        }
        
        
      }
      
      if ($post_type->is_valid() && !$external) {

        // auto-generate a sprite icon
        MPU::create_icon_sprite($post_type->menu_icon, "", true);

        // attach any taxonomies and shared field sets
        
        $post_type->unlink_taxonomies();
        
        if (MPC::post_val("taxonomies") != "" && count(MPC::post_val("taxonomies"))) {
          $post_type->link_taxonomies( MPM_Taxonomy::find_by_name_in( $_POST["taxonomies"] ) );
        }
        
        // update the menu positions of other post types
        
        $omp = MPC::post_val("other_menu_position");
        $omsp = MPC::post_val("other_menu_sub_position");
        
        if (isset($omp) && is_array($omp)) {
          foreach ($omp as $name => $position) {
            $wpdb->update(MPM::table("post-types"), array( "menu_position" => $position, "menu_sub_position" => $omsp[$name] ), array( "name" => $name ), "%d", "%s" ); 
          }
        }

        MasterPress::flag_for_rewrite();

      } 
    
      return $post_type;
      
      
    } else if ($action == "delete") {

      
      $pt = MPM_PostType::find_by_id(MasterPress::$id);
      $pt->delete(
        array(
          "posts" => $_POST["posts"],
          "posts_reassign_type" => $_POST["posts_reassign_type"],
          "field_sets" => $_POST["field_sets"],
          "field_data" => $_POST["field_data"]
        )
      );
      
      MasterPress::flag_for_rewrite();

      return true;
      
    } else if ($action == "create-field-set" || $action == "edit-field-set") {

      $field_set = new MPM_FieldSet();
      // consume the post data
      
      $field_set->name = $_POST["name"];
      $field_set->singular_name = $_POST["singular_name"];
      $field_set->disabled = isset($_POST["disabled"]);
      $field_set->labels = $_POST["labels"];
      $field_set->allow_multiple = isset($_POST["allow_multiple"]);
      $field_set->visibility = $_POST["visibility"];
      $field_set->capabilities = self::handle_capabilities();
      $field_set->type = "p"; // p = shared
      $field_set->position = $_POST["position"];
      $field_set->icon = self::handle_icon("icon", "icon_select");
      $field_set->expanded = isset($_POST["expanded"]);
      $field_set->sidebar = isset($_POST["sidebar"]);
      $field_set->versions = $_POST["versions"];
      $field_set->visibility = $this->get_visibility_val("sites,templates,post_types");
      
      $post_type = MPM_PostType::find_by_id(MasterPress::$parent);
      
      // inform the validation of the post type
      $field_set->meta("post_type", $post_type);
      
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
        
				if (isset($_POST["other_position"])) {
	        $op = $_POST["other_position"];
        
	        if (isset($op) && is_array($op)) {
	          foreach ($op as $id => $position) {
	            $wpdb->update(MPM::table("field-sets"), array( "position" => $position ), array( "id" => $id ), "%d", "%d" ); 
	          }
          
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
      $field->summary_options = $_POST["summary_options"];
      $field->required = isset($_POST["required"]);
      $field->labels = $_POST["labels"];
      $field->type = $_POST["type"]; 
      $field->icon = self::handle_icon("icon", "icon_select");
      
      if (isset($_POST["type_options"])) {
        $field->type_options = $_POST["type_options"];
      }
    
      $field->position = $_POST["position"];
      $field->visibility = $this->get_visibility_val("sites,templates,post_types");
      $field->capabilities = self::handle_capabilities();

      $fg = MPM_FieldSet::find_by_id($_POST["parent"]);
        
        
      if (MPC::is_create()) {
        $field->insert();
      } else if (MPC::is_edit()) {
        $field->update(MasterPress::$id);
      }

      if ($field->is_valid()) {

        // update other menu positions

        if (MPC::is_edit()) {
          global $meow_provider;
          $meow_provider->migrate_field_meta($field, $_POST["name_original"]);
        }
        
        if (isset($_POST["other_position"])) {
          $op = $_POST["other_position"];
        
          if (isset($op) && is_array($op)) {
            foreach ($op as $id => $position) {
              $wpdb->update(MPM::table("fields"), array( "position" => $position ), array( "id" => $id ), "%d", "%d" ); 
            }
          
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
    
    if (!MasterPress::current_user_can("manage_post_type_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to manage post type field sets.", MASTERPRESS_DOMAIN));
    }
    
    MPV::incl("field-sets");
    
    $actions = array();
    
    if (MasterPress::current_user_can("create_post_type_field_sets")) {
      $actions = array( MPV::action_button( $this->key(), "create-field-set", MPV::__create( "" ), array("parent" => MasterPress::$parent), array('class' => 'add-new-h2') ) );
    }
    
    $this->setup_view( array(
        "view" => "field-sets",
        "method" => "grid",
        "title_args" => array( 
          "actions" => $actions, 
          "info_panel" => true,
          "text" => MPV_FieldSets::__p() 
        )
      )
    );

  }


  // -- EDIT FIELD GROUP

  public function edit_field_set() {

    if (!MasterPress::current_user_can("edit_post_type_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to edit post type field sets.", MASTERPRESS_DOMAIN));
    }

    $this->edit( array(
        "cap_verified" => true,
        "view" => "field-sets",
        "model" => "field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
    
  }

  // -- CREATE FIELD GROUP

  public function create_field_set() {

    if (!MasterPress::current_user_can("create_post_type_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to create post type field sets.", MASTERPRESS_DOMAIN));
    }

    $this->create( array(
        "cap_verified" => true,
        "view" => "field-sets",
        "model" => "field-set",
        "manage" => "manage-field-sets",
        "title_args" => array( "info_panel" => true )
      )
    );
  }

  public function create_field() {

    if (!MasterPress::current_user_can("create_post_type_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to create post type fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("edit_post_type_fields")) {
      wp_die(__("Sorry, you do not have the required capability to edit post type fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("delete_post_type_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to delete post type field sets.", MASTERPRESS_DOMAIN));
    }

    $this->delete( array(
        "cap_verified" => true,
        "manage" => "manage-field-sets",
        "model" => "field-set",
      )
    );
    
  }

  public function delete_field() {

    if (!MasterPress::current_user_can("create_post_type_field_sets")) {
      wp_die(__("Sorry, you do not have the required capability to delete post type fields.", MASTERPRESS_DOMAIN));
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
        __("Post Types define the <em>types of content posts you can publish</em> on your site, beyond the Posts, Pages, Attachments, and Menu types that WordPress offers by default.", MASTERPRESS_DOMAIN ) 
      )
    );

  }
  
  public function create_help() {

    return array( 
      
      MPV::overview_tab( 
        __("This screen lets you create a new custom post type for your WordPress Site", MASTERPRESS_DOMAIN ) 
      )

    );

  }
  
  public function manage_field_sets_help() {
    return array( 
      MPV::overview_tab( 
        __("Field Sets define the grouping and types of custom content fields available for this post type.", MASTERPRESS_DOMAIN ) 
      )
    );
  }



}
