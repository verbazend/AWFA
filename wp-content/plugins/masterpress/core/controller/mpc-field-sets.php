<?php

class MPC_FieldSets extends MPC {
  
  protected $db_type;
  protected $model_class;

  public function __construct($db_type, $model_class) {
    $this->db_type = $db_type;
    $this->model_class = $model_class;
  }
  
  public function init() {
    $action = MasterPress::$action;
    
    if ($action == "create-field" || $action == "edit-field") {
      // inform the view what to render in the info panel
      
      MasterPress::$view->parent = call_user_func_array( array($this->model_class, "find_by_id"), array(MasterPress::$parent));

      MasterPress::$model->field_set_id = MasterPress::$parent;
      MasterPress::$model->infer_position();

      // enqueue dependent scripts for all field types (could improve this in the future)
      foreach (MPFT::type_keys() as $t) {
        if ($ftc = MPFT::type_class($t)) {
          call_user_func( array($ftc, "enqueue") );
        }
      }

      // enqueue the field type CSS
      $type = MasterPress::$model->type;

      MPFT::options_admin_head($type);

    } else if ($action == "create") {
      
      MasterPress::$model->infer_position();
    }

  }
  
  
  public function submit() {
    
    global $wpdb;
    
    $action = MasterPress::$action;
    
    if ($action == "create" || $action == "edit") {
      
      $field_set = new $this->model_class();
      // consume the post data
      
      $field_set->name = $_POST["name"];
      $field_set->singular_name = $_POST["singular_name"];
      $field_set->disabled = isset($_POST["disabled"]);
      $field_set->labels = $_POST["labels"];
      $field_set->visibility = MPC::post_val("visibility");
      $field_set->capabilities = self::handle_capabilities();
      $field_set->allow_multiple = isset($_POST["allow_multiple"]);
      $field_set->type = $this->db_type;
      $field_set->position = $_POST["position"];
      $field_set->icon = self::handle_icon("icon", "icon_select");
      $field_set->expanded = isset($_POST["expanded"]);
      $field_set->sidebar = isset($_POST["sidebar"]);
      $field_set->versions = $_POST["versions"];
      $field_set->visibility = $this->get_visibility_val();

      if ($action == "create") {
        $field_set->insert();
      } else if ($action == "edit") {
        $field_set->update(MasterPress::$id);
      }

      if ($field_set->is_valid()) {

        if (MPC::is_edit()) {
          global $meow_provider;
          $meow_provider->migrate_field_set_meta($field_set, $_POST["name_original"]);
        }

        // update other menu positions
        
        $op = MPC::post_val("other_position");
        
        if (isset($op) && is_array($op)) {
          foreach ($op as $id => $position) {
            $wpdb->update(MPM::table("field-sets"), array( "position" => $position ), array( "id" => $id ), "%d", "%d" ); 
          }
          
        }
        
        
      }        
      
      return $field_set;
      
    } else if ($action == "delete") { 

      $fg = MasterPress::$view->parent = call_user_func_array( array($this->model_class, "find_by_id"), array(MasterPress::$id));

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
        
        $op = MPC::post_val("other_position");
        
        if (isset($op) && is_array($op)) {
          foreach ($op as $id => $position) {
            $wpdb->update(MPM::table("fields"), array( "position" => $position ), array( "id" => $id ), "%d", "%d" ); 
          }
          
        }

        // clear the parent for the redirect
      
        MasterPress::$parent = null;

        
      }    
      
      
      return $field;
    } 
    
    
  }

  
  
  

  

}
