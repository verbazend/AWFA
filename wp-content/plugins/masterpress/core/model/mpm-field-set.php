<?php


class MPM_FieldSet extends MPM {

  public $fields;
  
	public function __construct() {
    
    $this->data = array(
      "name" => "",
      "singular_name" => "",
      "disabled" => false,
      "position" => 0,
      "expanded" => true,
      "sidebar" => false,
      "type" => $this->construct_type(),
      "allow_multiple" => false,
      "icon" => '',
      "versions" => 10,
      "visibility" => array(),
      "capabilities" => array(
        "visible" => "edit_posts",
        "editable" => "edit_posts"
      ),
      "labels" => array(
        "name" => __("?", MASTERPRESS_DOMAIN),
        "description" => __("", MASTERPRESS_DOMAIN),
        "description_user" => __("", MASTERPRESS_DOMAIN),
        "description_term" => __("", MASTERPRESS_DOMAIN),
        "singular_name" => __("?", MASTERPRESS_DOMAIN),
        "no_items" => __("No ?", MASTERPRESS_DOMAIN),
        "n_items" => __("%d ?", MASTERPRESS_DOMAIN),
        "one_item" => __("1 ?", MASTERPRESS_DOMAIN),
        "click_to_add" => __("Click to add ?", MASTERPRESS_DOMAIN),
        "add" => __("Add ?", MASTERPRESS_DOMAIN),
        "add_another" => __("Add Another ?", MASTERPRESS_DOMAIN),
        "remove" => __("Remove ?", MASTERPRESS_DOMAIN)
      )
    );
    
	}

  public function construct_type() {
    return "p";
  }
  
  public function label($key) {
    $labels = $this->labels;
    
    if (isset($labels[$key])) {
      return $labels[$key];
    } 
    
    return "";
  }
  
  public function manage_url() {
    
    $url = "";
    
    switch ($this->type) {
      
      case "p" : // Post type
        $pt = MPM_PostType::find_by_name($this->vis("post_types"));

        if (!$pt) {
          return '';
        }

        $url = MasterPress::admin_url("post-types", "manage-field-sets", array("parent" => $pt->id) )."&from=edit-field-set";
        break;

      case "t" : // Template
        $url = MasterPress::admin_url("templates", "manage-field-sets", array("parent" => $this->vis("templates")) )."&from=edit-field-set";
        break;

      case "x" : // taXonomy
        
        $tax = MPM_Taxonomy::find_by_name($this->vis("taxonomies"));
        
        if (!$tax) {
          return '';
        }

        $url = MasterPress::admin_url("taxonomies", "manage-field-sets", array("parent" => $tax->id) )."&from=edit-field-set";
        break;

      case "s" : // Shared
        $url = MasterPress::admin_url("shared-field-sets", "manage" )."&from=edit";
        break;

      case "r" : // Role
      $url = MasterPress::admin_url("roles", "manage-field-sets", array("parent" => $this->vis("roles")) )."&from=edit-field-set";
        break;

      case "w" : // site-Wide
        $url = MasterPress::admin_url("site-field-sets", "manage" )."&from=edit";
        break;

    } 
    
    $url .= "&show=".$this->id;
    
    return $url;
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
          $cap = "manage_post_type_field_sets";
          break;
        case "x" :
          $cap = "manage_taxonomy_field_sets";
          break;
        case "t" :
          $cap = "manage_template_field_sets";
          break;
        case "s" :
          $cap = "manage_shared_field_sets";
          break;
        case "w" :
          $cap = "manage_site_field_sets";
          break;
      }
      
    } 
    
    return MasterPress::current_user_can($cap);
    
  }
     
  public function current_user_can_see() {
    // first check the set visibility
    
    $cap = $this->capability("visible", false);
    
    if (trim($cap) == "") {
      // no capability - user can always see
      return true; 
    }

    $set_visibility = current_user_can($cap);

    if (!$set_visibility) {
      return FALSE; // the entire set is not visible
    }
    
    // now make sure at least one field is visible 
    
    $fields_visible = false;
    
    foreach ($this->fields as $field) {
      $fields_visible = $fields_visible || $field->current_user_can_see();
    }
    
    return $fields_visible;
  }

  public function current_user_can_edit() {
    
    $cap = $this->capability("editable");
    
    if (trim($cap) == "") {
      return true;
    }
    
    return current_user_can($cap);
    
  }
  
  public static function find_by_name($name) {
    return self::find( array( "where" => "`name` = '{$name}' AND type = 'p'" ) ); 
  }

  public static function find_by_post_type($post_type, $orderby = "position ASC") {
    $sql = "SELECT * FROM ".MPU::table("field-sets")." WHERE type = 'p' AND ".MPM::visibility_rlike("post_types", $post_type->name)." ORDER BY $orderby ";
    return MPM::get_models("field-set", $sql);
  }
  

  public function infer_position() {
    
  }

  public static function find($args = array()) {
    $defaults = array(
      "where" => "`type` = 'p'"
    );
    
    $r = wp_parse_args($args, $defaults);
    
    return parent::find(self::k(__CLASS__), $r);  
  }
  
  
  
  public function validate($op) {

    
    if ($this->name == "") {
      $this->err(__("<em>Name</em> must be provided", MASTERPRESS_DOMAIN), "name");
      
    }
    
    
    if ($this->allow_multiple) {
      
      if ($this->singular_name == "") {
        $this->err(__("Since this set allows multiple items, <em>Singular Name</em> must also be provided", MASTERPRESS_DOMAIN), "singular_name");
      }
    }
    
    // call into the validate_name function, so we can override its behaviour for the different types of field sets
    $this->validate_name($op);
    
  }
  
  public function validate_name($op) {
    if ($this->name != "") {
      
      // check there isn't already a field set with this name 
      
      $post_type = $this->meta("post_type");

      if ($this->name != "") {

        $extra_check = "";
      
        if ($op != "insert") {

          $extra_check = " AND fg.id <> ".$this->id." ";

        }
      
      }
    
      // check if a field set already exists specific to this post type 

      if ($post_type) {
        $sql = "SELECT * FROM ".MPU::table("field-sets")." fg WHERE type = 'p' AND fg.name = '{$this->name}' AND ".MPM::visibility_rlike("post_types", $post_type->name).$extra_check;
        $result = MPM_FieldSet::get_model("field-set", $sql );
      }

      if (isset($result) && $result) {
        $this->err(sprintf(__("Sorry, a field set named <em>%s</em> already exists for post type <em>%s</em>. Please enter another name.", MASTERPRESS_DOMAIN), $this->name, $post_type->name), "name");
      }
      
    }
  }
  
  public function linked_to_post_type($post_type) {
    return $this->visible_in("post_types", $post_type->name);
  }

    
  public function post_types() {
    $sql = "SELECT * FROM `".MPM::table("post-types")."` ".$this->visibility_clause("post_types", "name", true);
    return MPM::get_models("post-type", $sql);
  }

  public function taxonomies() {
    $sql = "SELECT * FROM `".MPM::table("taxonomies")."` ".$this->visibility_clause("taxonomies", "name", true);
    return MPM::get_models("taxonomy", $sql);
  }

  public function roles() {
    global $wf;
    $roles = array();
    $info = $this->visibility_clause("roles", "name", true, "", "object");
    
    $all_roles = $wf->roles();
    
    if ($info["all"]) {
      return $all_roles;
    } else {
      
      if ($info["values"] != "") {
        foreach (explode(",", $info["values"]) as $role) {
          $roles[] = $wf->role($role);
        } 
      }
    
    }
    
    return $roles;
    
  }

  public function post_type() {
    $pt = $this->post_types();

    if (count($pt)) {
      return $pt[0];
    }
  }

  public function delete() {
    global $wpdb;
    $wpdb->query("DELETE FROM ".MPU::table("fields")." WHERE field_set_id = ".$this->id);
    parent::delete();
  }
  

  public function meta_count() {
    
    global $wpdb;
         
    $count = 0;
    
    // get a list of field ids for this set
    
    $field_ids = $wpdb->get_col("SELECT id FROM ".MPU::table("fields")." WHERE field_set_id = ".$this->id);
    
    if (count($field_ids)) {
      
      // get a list of meta values bound to the fields in this set.
    
      $sql = "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (".implode(",", $field_ids).")";
      $rows = $wpdb->get_results($sql); 
      
      // now get the record count for those meta data groups
       
      foreach ($rows as $row) {
        list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
        $sql = "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = ".$row->post_id." AND ( meta_key = '".MPFT::meta_key($set_name, $field_name)."')";
        
        $count += $wpdb->get_var($sql);
      }
    
      return $count;
    
    }
    
  }

  public function delete_meta() {
    
    global $wpdb;
    
    // get a list of field ids for this set
    
    $field_ids = $wpdb->get_col("SELECT id FROM ".MPU::table("fields")." WHERE field_set_id = ".$this->id);
    
    if (count($field_ids)) {

      // get a list of meta values bound to this field.
     
      $sql = "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (".implode(",", $field_ids).")";
      $rows = $wpdb->get_results($sql); 
      
      foreach ($rows as $row) {
        list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
        $sql = "DELETE FROM $wpdb->postmeta WHERE post_id = ".$row->post_id." AND ( meta_key = '".MPFT::meta_key($set_name, $field_name)."' OR meta_key LIKE '".MPFT::meta_key($set_name, $field_name).":%' )";
        $wpdb->query($sql);
      }

    }
  
  }
  
  
  public function rep() {
    
    $rep = $this->data() + array();
    
    $rep["fields"] = array();
    
    foreach ($this->fields("id asc") as $field) {
      $rep["fields"][] = $field->rep();
    } 
    
    return $rep;
    
  }


  public function query_fields($args = array()) {
    $r = wp_parse_args( array( "where" => "`field_set_id` = '{$this->id}'" ), $args );
    return MPM_Field::find( $r );
  }

  public function set_fields($fields) {
    $this->fields = $fields;
  }
  
  public function fields() {
    if (!isset($this->fields)) {
      $this->fields = $this->query_fields( array("orderby" => "position ASC") );
    }
    
    return $this->fields;
  }

  public function field_count() {
    return count($this->fields());
  }

  public function display_label() {
    return $this->labels[ "name" ];
  }

  public function display_name() {
    return $this->name;
  }

  public function html_id() {
    return str_replace("_", "-", $this->name);
  }

  public function is_post_type() {
    return $this->type == "p" || $this->type == "post_type";
  }
  
  public function is_shared() {
    return $this->type == "s" || $this->type == "shared";
  }

  public function is_template() {
    return $this->type == "t" || $this->type == "template";
  }

  public function is_taxonomy() {
    return $this->type == "x" || $this->type == "taxonomy";
  }

  public function is_role() {
    return $this->type == "r" || $this->type == "role";
  }
  
  
  
  
  // static extension methods, only needed because we can't use PHP 5.3... sob

  public static function find_by_id($id) {
    return parent::find_by_id(self::k(__CLASS__), $id);
  }

  public static function delete_by_id($id) {
    return parent::delete_by_id(self::k(__CLASS__), $id);
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