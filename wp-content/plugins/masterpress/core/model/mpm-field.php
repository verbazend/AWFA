<?php

/*
	MasterPress Field Model
*/

class MPM_Field extends MPM {

  protected $_field_set;
  
  
	public function __construct() {
    
     $this->data = array(
      "name" => "",
      "disabled" => false,
      "position" => 0,
      "type" => "text-box",
      "type_options" => array(),
      "required" => false,
      "summary_options" => false,
      "icon" => '',
      "visibility" => array(),
      "capabilities" => array(
        "visible" => "",
        "editable" => ""
      ),
      "labels" => array(
        "name" => __("?", MASTERPRESS_DOMAIN),
        "description" => "",
        "tooltip_help" => "",
        "add_another" => __("Add Another ?", MASTERPRESS_DOMAIN),
        "remove" => __("Remove ?", MASTERPRESS_DOMAIN)
      )
    );
    
	}
  
  public function capability($key, $fallback = true) {
    $cap = $this->capabilities;

    $defaults = array("visible" => "read", "editable" => "read");
    
    if (isset($cap[$key]) && $cap[$key] != "") {
      return $cap[$key];
    } 
    
    if ($fallback) {
      return $defaults[$key];
    }
    
    return "";
  }


  public function current_user_can_manage() {
    
    $cap = "manage_options";
    
    if (MasterPress::$cap_mode == "specific") {
      switch ($this->type) {
        case "p" :
          $cap = "edit_post_type_fields";
          break;
        case "x" :
          $cap = "edit_taxonomy_fields";
          break;
        case "t" :
          $cap = "edit_template_fields";
          break;
        case "s" :
          $cap = "edit_shared_fields";
          break;
        case "w" :
          $cap = "edit_site_fields";
          break;
      }
      
    } 
    
    return MasterPress::current_user_can($cap);
    
  }

      
  public function current_user_can_see($set_visible = true) {
    $cap = $this->capability("visible", false);
    
    if (trim($cap) == "") {
      return $set_visible;
    }
    
    return current_user_can($cap);
  }

  public function current_user_can_edit($set_editable = true) {
    
    $cap = $this->capability("editable", false);
    
    if (trim($cap) == "") {
      return $set_editable;
    }
       
    return current_user_can($cap);
  }
  
  public function label($key) {
    $labels = $this->labels;
    
    if (isset($labels[$key])) {
      return $labels[$key];
    } 
    
    return "";
  }

  public function options($key = null) {
    if (!is_null($key)) {
      return $this->data["type_options"][$key];
    }

    return $this->data["type_options"];
  }
  
  public function manage_url($set) {
    
    $url = "";
    
    switch ($set->type) {
      
      case "p" : // Post type
        $pt = MPM_PostType::find_by_name($set->vis("post_types"));

        if (!$pt) {
          return '';
        }

        $url = MasterPress::admin_url("post-types", "edit-field", array("id" => $this->id, "gparent" => $pt->id, "parent" => $set->id ) );
        break;

      case "t" : // Template
        $url = MasterPress::admin_url("templates", "edit-field", array("id" => $this->id, "gparent" => $set->vis("templates"), "parent" => $set->id) );
        break;

      case "x" : // taXonomy
        
        $tax = MPM_Taxonomy::find_by_name($set->vis("taxonomies"));
        
        if (!$tax) {
          return '';
        }

        $url = MasterPress::admin_url("taxonomies", "edit-field", array("id" => $this->id, "gparent" => $tax->id, "parent" => $set->id) );
        break;

      case "s" : // Shared
        $url = MasterPress::admin_url("shared-field-sets", "edit-field", array("id" => $this->id, "parent" => $set->id) );
        break;

      case "r" : // Role
      $url = MasterPress::admin_url("roles", "edit-field", array("id" => $this->id, "gparent" => $set->vis("roles"), "parent" => $set->id) );
        break;

      case "w" : // site-Wide
        $url = MasterPress::admin_url("site-field-sets", "edit-field", array("id" => $this->id, "parent" => $set->id) );
        break;

    } 
    
    return $url;
  }
  
  public function meta_count() {
    
    global $wpdb;
         
    $count = 0;
    
    // get a list of meta values bound to this field.
    
    $sql = "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$this->id."'";
    $rows = $wpdb->get_results($sql); 
      
    foreach ($rows as $row) {
      list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
      $sql = "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = ".$row->post_id." AND ( meta_key = '".MPFT::meta_key($set_name, $field_name)."' ) ";
      $count += $wpdb->get_var($sql);
    }
    
    return $count;
    
  }

  public function delete_meta() {
    
    global $wpdb;
    
    // get a list of meta values bound to this field.
     
    $sql = "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$this->id."'";
    $rows = $wpdb->get_results($sql); 
      
    foreach ($rows as $row) {
      list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
      
      // note that we delete the properties too, but only count the main fields.
      $sql = "DELETE FROM $wpdb->postmeta WHERE post_id = ".$row->post_id." AND ( meta_key = '".MPFT::meta_key($set_name, $field_name)."' OR meta_key LIKE '".MPFT::meta_key($set_name, $field_name).":%' )";
      
      $wpdb->query($sql);
      
    }
    
  }
  

  public function infer_position() {
  
    /*
    we need to find the appropriate starting sub position for this field, assuming it comes AFTER other fields in this set 
    */
  
    global $wpdb;
    $max_pos = $wpdb->get_var("SELECT MAX(`position`) FROM `".MPM::table("fields")."` WHERE `field_set_id` = {$this->field_set_id}");

    if ($max_pos) {
      $this->position = (int) $max_pos + 1;
    } else {
      $this->position = 1;
    }
  
  }
    
  public function validate($op) {

    
    if ($this->name == "") {
      $this->err(__("<em>Name</em> must be provided", MASTERPRESS_DOMAIN), "name");
      
    }
    

    if ($this->name != "") {
      // call into the validate_name function, so we can override its behaviour for the different types of field sets
      $this->validate_name($op);
    }
    
  }
  
  public function validate_name($op) {
    if ($this->name != "") {
      // check there isn't already a field with this name 
      
      $extra_check = "";
      
      if ($op != "insert") {
        $extra_check = " AND id <> ".$this->id." ";
      }
      
      $result = self::find( array("where" => "`field_set_id` = {$this->field_set_id} AND `name` = '{$this->name}'". $extra_check ) );

      if (isset($result) && $result) {
        $this->err(sprintf(__("Sorry, a field named <em>%s</em> already exists in this field set. Please enter a different <em>Name</em>", MASTERPRESS_DOMAIN), $this->name), "name");
      }
    }
  }
  
  public function field_set() {

    if (!$this->_field_set) {
      $this->_field_set = MPM_FieldSet::find_by_id( $this->field_set_id );
    }
    
    return $this->_field_set;

  }
  
  public function meta_name() {
    
    $set = $this->field_set();
    
    if ($set) {
      return $set->name."_".$this->name;
    }
    
    return $this->name;
  }
  
  
  public function linked_to_post_type($post_type) {
    return $this->visible_in("post_types", $post_type->name);
  }
    
  public function post_types() {
    $sql = "SELECT * FROM `".MPM::table("post-types")."` ".$this->visibility_clause("post_types", "name", true);
    return MPM::get_models("post-type", $sql);
  }
  
  public function display_label() {
    return $this->labels[ "name" ];
  }

  public function display_name() {
    return $this->name;
  }
  
  
  
  // static extension methods, only needed because we can't use PHP 5.3... sob

  public static function find_by_id($id) {
    return parent::find_by_id(self::k(__CLASS__), $id);
  }

  public static function delete_by_id($id) {
    return parent::delete_by_id(self::k(__CLASS__), $id);
  }

  public static function find($args = array()) {
    return parent::find(self::k(__CLASS__), $args);
  }
  
  public static function find_by($field, $value, $format = "%s") {
    return parent::find_by(self::k(__CLASS__), $field, $value, $format);
  }
  
  public static function find_by_in($field, $values, $format = "%s") {
    return parent::find_by_in(self::k(__CLASS__), $field, $values, $format);
  }
  
  public static function find_by_id_in($values) {
    return parent::find_by_id_in(self::k(__CLASS__), $values);
  }
  

}

?>