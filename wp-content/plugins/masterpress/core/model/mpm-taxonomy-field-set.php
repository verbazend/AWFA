<?php


class MPM_TaxonomyFieldSet extends MPM_FieldSet {

  public static function table() {
    return MPM::table("field-sets");
  }

  public function tbl() {
    return MPU::table("field_sets");
  } 
  
  public $fields;
  
	public function __construct() {
    
    $this->data = array(
      "name" => "",
      "singular_name" => "",
      "disabled" => false,
      "position" => 0,
      "expanded" => true,
      "icon" => "",
      "sidebar" => false,
      "type" => "x",
      "allow_multiple" => false,
      "visibility" => array(),
      "capabilities" => array(
        "visible" => "edit_posts",
        "editable" => "edit_posts"
      ),
      "versions" => 10,
      "labels" => array(
        "name" => __("?", MASTERPRESS_DOMAIN),
        "description" => __("", MASTERPRESS_DOMAIN),
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
    return "x";
  }

  public static function find_by_name($name) {
    return self::find( array( "where" => "`name` = '{$name}' AND type = 'p'" ) ); 
  }

  public static function find_by_taxonomy($taxonomy, $orderby = "position ASC") {
    $sql = "SELECT * FROM ".MPU::table("field-sets")." WHERE type = 'x' AND ".MPM::visibility_rlike("taxonomies", $taxonomy->name)." ORDER BY $orderby ";
    return MPM::get_models("field-set", $sql);
  }
  

  public function infer_position() {
    
  }

  public static function find($args = array()) {
    $defaults = array(
      "where" => "`type` = 'x'"
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
      
      $taxonomy_name = $this->visibility["taxonomies"];

      if ($this->name != "") {

        $extra_check = "";
      
        if ($op != "insert") {

          $extra_check = " AND fg.id <> ".$this->id." ";

        }
      
      }
    
      // check if a field set already exists specific to this post type 

      if ($post_type) {
        $sql = "SELECT * FROM ".MPU::table("field-sets")." fg WHERE type = 'x' AND fg.name = '{$this->name}' AND ".MPM::visibility_rlike("taxonomies", $taxonomy_name).$extra_check;
        $result = MPM_FieldSet::get_model("field-set", $sql );
      }

      if (isset($result) && $result) {
        $this->err(sprintf(__("Sorry, a field set named <em>%s</em> already exists for taxonomy <em>%s</em>. Please enter another name.", MASTERPRESS_DOMAIN), $this->name, $taxonomy_name), "name");
      }
      
    }
  }
  
  public function linked_to_taxonomy($taxonomy) {
    return $this->visible_in("taxonomies", $taxonomy->name);
  }
    
  public function taxonomies() {
    $sql = "SELECT * FROM `".MPM::table("taxonomies")."` ".$this->visibility_clause("taxonomies", "name", true);
    return MPM::get_models("taxonomy", $sql);
  }

  public function taxonomy() {
    $t = $this->taxonomies();

    if (count($t)) {
      return $t[0];
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
    
      // TODO - fix this!
      
      /*
      $sql = "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (".implode(",", $field_ids).")";
      $rows = $wpdb->get_results($sql); 
      
      // now get the record count for those meta data groups
       
      foreach ($rows as $row) {
        list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
        $sql = "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = ".$row->post_id." AND ( meta_key = '".MPFT::meta_key($set_name, $field_name)."')";
        
        $count += $wpdb->get_var($sql);
      }
    
      */
      
      return $count;
    
    }
    
  }

  public function delete_meta() {
    
    global $wpdb;
    
    // get a list of field ids for this set
    
    $field_ids = $wpdb->get_col("SELECT id FROM ".MPU::table("fields")." WHERE field_set_id = ".$this->id);
    
    if (count($field_ids)) {

      /* TODO - fix this once meta is established
      // get a list of meta values bound to this field.
     
      $sql = "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (".implode(",", $field_ids).")";
      $rows = $wpdb->get_results($sql); 
      
      foreach ($rows as $row) {
        list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
        $sql = "DELETE FROM $wpdb->postmeta WHERE post_id = ".$row->post_id." AND ( meta_key = '".MPFT::meta_key($set_name, $field_name)."' OR meta_key LIKE '".MPFT::meta_key($set_name, $field_name).":%' )";
        $wpdb->query($sql);
      }
      */
      
    }
  
  }
  
  
  // static extension methods, only needed because we can't use PHP 5.3... sob

  public static function find_by_id($id) {
    return MPM::find_by_id(self::k(__CLASS__), $id, MPU::table("field_sets"));
  }

  public static function delete_by_id($id) {
    return MPM::delete_by_id(self::k(__CLASS__), $id, MPU::table("field_sets"));
  }

  public static function find_by($field, $value, $format = "%s") {
    return MPM::find_by(self::k(__CLASS__), $field, $value, $format, MPU::table("field_sets"));
  }
  
  public static function find_by_in($field, $values, $format = "%s") {
    return MPM::find_by_in(self::k(__CLASS__), $field, $values, $format, MPU::table("field_sets"));
  }
  
  public static function find_by_id_in($values) {
    return MPM::find_by_id_in(self::k(__CLASS__), $values, MPU::table("field_sets"));
  }
  
}

?>