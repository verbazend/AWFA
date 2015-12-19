<?php

/*
	MasterPress Post Type Class
*/

class MPM_Taxonomy extends MPM {

  protected $field_sets;
  protected $taxonomy_field_sets;
  protected $columns_by_key;
  
  public static function find_by_name($name) {
    return self::find_by("name", $name);
  }
  
  public static function find_by_name_in($values) {
    return self::find_by_in("name", $values);
  }
  
	public function __construct() {
    
    $this->data = array(
      "name" => "",
      "plural_name" => "",
      "object_type" => "",
      "labels" => array(
        "name" => '?',
        "singular_name" => '?',
        "search_items" => 'Search ?',
        "popular_items" => 'Popular ?',
        "all_items" => 'All ?',
        "parent_item" => 'Parent ?',
        "parent_item_colon" => 'Parent ?:',
        "edit_item" => 'Edit ?',
        "update_item" => 'Update ?',
        "add_new_item" => 'Add New ?',
        "new_item_name" => 'New ? Name',
        "separate_items_with_commas" => 'Separate ? with commas',
        "add_or_remove_items" => 'Add or remove ?',
        "choose_from_most_used" => 'Choose from the most used ?',
        "menu_name" => '?'
      ),
      "disabled" => false,
      "show_in_nav_menus" => true,
      "show_ui" => true,
      "show_tagcloud" => true,
      "show_manage_filter" => true,
      "hierarchical" => false,
      "title_icon" => "",
      "hide_term_ui" => false,
      "rewrite" => array(
        "slug" => "",
        "with_front" => true,
        "hierarchical" => false
      ),
      "update_count_callback" => '',
      "query_var" => '',
      "capabilities" => array(
        "manage_terms" => 'manage_categories',
        "edit_terms" => 'manage_categories',
        "delete_terms" => 'manage_categories',
        "assign_terms" => 'edit_posts'
      ),
      "visibility" => array(),
      "_builtin" => false,
      "_external" => false

    );
      
    $this->metadata = array(
      "query_allowed" => true
    );
      
	}

  public function term_count() {
    global $wpdb;
    return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE `taxonomy` = '".$this->name."'"); 
  }

  public function meta_count() {
    global $wpdb;
    $count = $wpdb->get_var("SELECT COUNT(*) FROM ".MPU::site_table("termmeta")." WHERE `taxonomy` = '".$this->name."'");
    return $count;
  }

  public function delete_meta() {
    global $wpdb;
    $wpdb->query("DELETE FROM ".MPU::site_table("termmeta")." WHERE `taxonomy` = '".$this->name."'");
  }


  public function field_sets($orderby = "position asc") {
    
    $sql = "SELECT * FROM ".MPU::table("field-sets")." WHERE ( type = 'x' OR type = 's') AND ".MPM::visibility_rlike("taxonomies", $this->name)." ORDER BY $orderby";
      
    if ($orderby == "position asc") { // try to return a cached version
      
      if (!isset($this->field_sets)) {
        $this->field_sets = MPM::get_models("field-set", $sql); 
      }

      return $this->field_sets;
      
    } 
    
    return MPM::get_models("field-set", $sql);
    
  }

  public function taxonomy_field_sets($orderby = "position asc") {
    
    $sql = "SELECT * FROM ".MPU::table("field-sets")." WHERE ( type = 'x' ) AND ".MPM::visibility_rlike("taxonomies", $this->name)." ORDER BY $orderby";
      
    if ($orderby == "position asc") { // try to return a cached version
      
      if (!isset($this->taxonomy_field_sets)) {
        $this->taxonomy_field_sets = MPM::get_models("field-set", $sql); 
      }

      return $this->taxonomy_field_sets;
      
    } 
    
    return MPM::get_models("field-set", $sql);
    
  }
  
  public function field_set_count() {
    global $wpdb;
    $sets = $wpdb->get_col("SELECT id FROM ".MPU::table("field-sets")." WHERE type = 'x' AND ".MPM::visibility_rlike("taxonomies", $this->name));
    return count($sets);
  }


  public function columns_by_key() {

    if (!isset($this->columns_by_key)) {

      foreach ($this->columns as $column) {
        if (isset($column["title"])) {
          $key = "custom_".WOOF_Inflector::underscore($column["title"]);
        }
      
        if (isset($column["core"])) {
          $key = $column["core"];
        }
        
        $this->columns_by_key[$key] = $column;
        
      }
      
    }

    return $this->columns_by_key;
  }
  
  public function columns() {
    
    $default = array(
      array("core" => "cb", "title" => ""),
      array("core" => "name", "title" => "Name", "disabled" => "", "content" => "{col.name}"),
      array("core" => "description", "title" => "Description", "disabled" => "", "content" => "{col.description}" ),
      array("core" => "slug", "title" => "Slug", "disabled" => "", "content" => "{col.slug}" ),
      array("core" => "posts", "title" => "[Post Type]", "title_readonly" => "yes", "disabled" => "", "content" => "{col.posts}" )
    );
    
    if ($this->columns == "") {
      return $default;
    }

    return $this->columns;

  }
  
  public function rep() {
    
    $rep = $this->data() + array();
    
    $rep["field_sets"] = array();
    
    foreach ($this->taxonomy_field_sets("id asc") as $field_set) {
      $rep["field_sets"][] = $field_set->rep();
    } 
    
    return $rep;
    
  }



  public function delete_terms() {
    global $wpdb;

    $term_ids = $wpdb->get_col("SELECT term_id FROM $wpdb->term_taxonomy WHERE `taxonomy` = '".$this->name."'"); 
    
    if (count($term_ids)) {
      // delete the terms / taxonomy releationships
      $wpdb->get_col("DELETE FROM $wpdb->term_taxonomy WHERE `taxonomy` = '".$this->name."'"); 

      // delete the terms themselves
      $wpdb->get_col("DELETE FROM $wpdb->term_taxonomy WHERE `term_id` IN (".implode(",", $term_ids).")'"); 
    }
    
  }

  public function reassign_terms($new_taxonomy_name) {
    global $wpdb;

    if ($new_taxonomy_name && $new_taxonomy_name != '') {
      $wpdb->query("UPDATE $wpdb->term_taxonomy SET `taxonomy` = '$new_taxonomy_name' WHERE `taxonomy` = '".$this->name."'"); 
    }
    
  }
  
  public function delete($args = array()) {
    
    $r = wp_parse_args($args, array(
      "existing_terms" => "leave",
      "existing_terms_reassign_taxonomy" => "",
      "field_sets" => "keep",
      "field_data" => "keep"
    ));
    
    $existing_action = $r["existing_terms"];
        
    if ($existing_action == "delete") {
      $this->delete_terms();
    } else if ($existing_action == "reassign") {
      $this->reassign_terms($r["existing_terms_reassign_taxonomy"]);
    }
    
    if ($existing_action != "reassign" && $r["field_sets"] == "delete") {
      $this->delete_field_sets();
    }

    if (isset($r["field_data"]) && $r["field_data"] == "delete") {
      $tax->delete_meta();
    }
    
    parent::delete();
  }
  

  public function delete_field_sets() {
   
    global $wpdb;
    
    // delete the field sets of type 'x'

    $sql = "SELECT id FROM ".MPU::table("field-sets")." WHERE type = 'x' AND ".MPM::visibility_rlike("taxonomies", $this->name);
          
    $tax_sets = $wpdb->get_col($sql);

    if (count($tax_sets)) {
      // delete JUST the POST TYPE field sets assigned to this post type
      $wpdb->query("DELETE FROM ".MPU::table("field-sets")." WHERE id IN (".implode(",", $tax_sets).") ");
      
      // delete the fields within those sets
      $wpdb->query("DELETE FROM ".MPU::table("fields")." WHERE field_set_id IN (".implode(",", $tax_sets).") ");
    }
  }


  public function validate($op) {
    
    global $wpdb;
    
    if ($this->name == "") {
      $this->err(__("<em>Singular Name</em> must be provided", MASTERPRESS_DOMAIN), "name");
    } else {
      
      // check there isn't already a post with this name
      $result = self::find_by_name($this->name);

      if ($result && $result->id != $this->id) {
        $this->err(sprintf(__("Sorry, a taxonomy named <em>%s</em> already exists", MASTERPRESS_DOMAIN), $this->name), "name");
      }

    }

    if ($this->plural_name == "") {
      $this->err(__("<em>Plural Name</em> must be provided", MASTERPRESS_DOMAIN), "plural_name");
    }

    return $this->error_count() == 0;
  }
  
  public function object_type() {
    return $this->get("object_type", array());
  }
  
  public function linked_to_post_type($post_type) {
    return in_array($post_type->name, $this->object_type());
  }
    
  public function post_types() {
    return MPM_PostType::find_by_in("name", $this->object_type(), "%s");
  }

  public function title_icon_url($fallback = true, $object_icon = true) {
    return MPU::menu_icon_url($this->title_icon, $fallback, "taxonomy", $object_icon);
  }

  public function title_icon_exists() {
    return MPU::menu_icon_exists($this->title_icon);
  }

  public function display_label() {
    
    if (isset($this->labels["name"]) && $this->labels["name"] != "") {
      return $this->labels["name"];
    }
    
    return WOOF_Inflector::titleize($this->name);
  }

	public function still_registered() {
	  global $wf;
	  $tax = $wf->taxonomy($this->name);
	  return (!$this->_external) || ($this->_external && !is_woof_silent($tax));
  }

  
  public static function insert_builtin() {

    /*
      here, we ignore any built-in taxonomies that have no UI, as these are not taxonomies that should
      be attached to custom post types
    */
    
    global $wpdb;
    
    if (!self::find_by_name("category")) {
      
      $tax_category = new MPM_Taxonomy();
      $tax_category->set( array(
        
        "name" => "category",
        "plural_name" => "categories",
        "labels" => array(
          "name" => 'Categories',
          "singular_name" => 'Category',
          "search_items" => 'Search Categories',
          "popular_items" => '',
          "all_items" => 'All Categories',
          "parent_item" => 'Parent Category',
          "parent_item_colon" => 'Parent Category:',
          "edit_item" => 'Edit Category',
          "update_item" => 'Update Category',
          "add_new_item" => 'Add New Category',
          "new_item_name" => 'New Category Name',
          "separate_items_with_commas" => '',
          "add_or_remove_items" => '',
          "choose_from_most_used" => '',
          "menu_name" => 'Categories'
        ),
        "disabled" => false,
        "show_in_nav_menus" => true,
        "show_ui" => true,
        "show_tagcloud" => true,
        "object_type" => array("post", "page"),
        "hierarchical" => true,
        "rewrite" => array(
          "slug" => "category",
          "with_front" => true,
          "hierarchical" => true
        ),
        "update_count_callback" => '_update_post_term_count',
        "query_var" => 'category_name',
        "capabilities" => array(
          "manage_terms" => 'manage_categories',
          "edit_terms" => 'manage_categories',
          "delete_terms" => 'manage_categories',
          "assign_terms" => 'edit_posts'
        ),
        "title_icon" => "",
        "_builtin" => true,
        "_external" => false
      ));
    
      $tax_category->insert();
      
    }


    if (!self::find_by_name("post_tag")) {
      
      $tax_tag = new MPM_Taxonomy();
      $tax_tag->set( array(
        
        "name" => "post_tag",
        "plural_name" => "post_tags",
        "labels" => array(
          "name" => 'Post Tags',
          "singular_name" => 'Post Tag',
          "search_items" => 'Search Tags',
          "popular_items" => 'Popular Tags',
          "all_items" => 'All Tags',
          "parent_item" => '',
          "parent_item_colon" => '',
          "edit_item" => 'Edit Tag',
          "update_item" => 'Update Tag',
          "add_new_item" => 'Add New Tag',
          "new_item_name" => 'New Tag',
          "separate_items_with_commas" => 'Separate tags with commas',
          "add_or_remove_items" => 'Add or remove tags',
          "choose_from_most_used" => 'Choose from the most used tags',
          "menu_name" => 'Tags'
        ),
        "disabled" => false,
        "show_in_nav_menus" => true,
        "show_ui" => true,
        "show_tagcloud" => true,
        "object_type" => array("post", "page"),
        "hierarchical" => false,
        "rewrite" => array(
          "slug" => "tag",
          "with_front" => true,
          "hierarchical" => false
        ),
        "update_count_callback" => '_update_post_term_count',
        "query_var" => 'tag',
        "capabilities" => array(
          "manage_terms" => 'manage_categories',
          "edit_terms" => 'manage_categories',
          "delete_terms" => 'manage_categories',
          "assign_terms" => 'edit_posts'
        ),
        "visibility" => array(),
        "title_icon" => "",
        "_builtin" => true,
        "_external" => false
      ));

      $tax_tag->insert();

    }
    
  

  }
  
  
  
  // static extension methods, only needed because we can't use PHP 5.3... sob

  public static function find_by_id($id) {
    return parent::find_by_id(self::k(__CLASS__), $id);
  }

  public static function delete_by_id($id, $class) {
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