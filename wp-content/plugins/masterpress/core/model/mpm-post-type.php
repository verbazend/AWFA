<?php

class MPM_PostType extends MPM {

  protected $field_sets;
  protected $post_type_field_sets;
  protected $columns_by_key;
  
  public static function find_by_name($name) {
    return self::find_by("name", $name);
  }
  
  public static function find_by_name_in($values) {
    return self::find_by_in("name", $values);
  }

  public function __construct($infer = false) {
    
    $this->data = array(
      "name" => "",
      "plural_name" => "",
      "disabled" => false,
      "labels" => array(
        "name" => __("?", MASTERPRESS_DOMAIN),
        "singular_name" => __("?", MASTERPRESS_DOMAIN),
        "add_new" => __("Add New", MASTERPRESS_DOMAIN),
        "add_new_item" => __("Add New ?", MASTERPRESS_DOMAIN),
        "edit_item" => __("Edit ?", MASTERPRESS_DOMAIN),
        "new_item" => __("New ?", MASTERPRESS_DOMAIN),
        "all_items" => __("All ?", MASTERPRESS_DOMAIN),
        "view_item" => __("View ?", MASTERPRESS_DOMAIN),
        "search_items" => __("Search ?", MASTERPRESS_DOMAIN),
        "not_found" => __("No ? found.", MASTERPRESS_DOMAIN),
        "not_found_in_trash" => __("No ? found in Trash.", MASTERPRESS_DOMAIN),
        "parent_item_colon" => "Parent ?:", 
        "menu_name" => __("?", MASTERPRESS_DOMAIN),
        "no_posts" => __("?", MASTERPRESS_DOMAIN),
        "one_post" => __("?", MASTERPRESS_DOMAIN),
        "n_posts" => __("?", MASTERPRESS_DOMAIN),
        "remove_post" => __("?", MASTERPRESS_DOMAIN),
      ),
      "description" => "",
      "publicly_queryable" => true,
      "exclude_from_search" => false,
      "show_ui" => true,
      "show_in_menu" => true,
      "menu_position" => 25,
      "menu_sub_position" => 0,
      "menu_icon" => "",
      "manage_sort_order" => "post_date|desc",
      "capability_type" => "post",
      "capabilities" => array(),
      "map_meta_cap" => true,
      "hierarchical" => false,
      "supports" => "title,editor",
      "permalink_epmask" => "EP_PERMALINK",
      "has_archive" => true,
      "rewrite" => array( "slug" => "", "with_front" => false, "feeds" => true, "pages" => true ), 
      "query_var" => "post",
      "can_export" => true,
      "show_in_nav_menus" => true,
      "visibility" => array(),
      "_builtin" => false,
      "_external" => false  
    );
    
    if ($infer) {
      $this->infer_sub_position();
    }
  
	}
	
	public function permalink_epmask() {
	  $parts = explode("|", $this->permalink_epmask);
	  
	  if (!count($parts)) {
	    return EP_PERMALINK;
    }
    
    $mask = 0;
    
	  foreach ($parts as $part) {
	    
	    if (defined($part)) {
	      
  	    $val = constant($part);
	    
  	    if (!is_null($val)) {
  	      $mask |= $val;
        }
      
      }
    
    }
    
    return $mask;
  }
	
	public function label($key) {
	  if (isset($this->labels[$key])) {
	    return $this->labels[$key];
    } else {
      
      if ($key == "no_posts") {
        return "No " . $this->label("name");
      } else if ($key == "one_post") {
        return "1 " . $this->label("singular_name");
      } else if ($key == "n_posts") {
        return "%d " . $this->label("name");
      } else if ($key == "remove_post") {
        return "Remove " . $this->label("singular_name");
      }
      
    }
    
    return "";
  }
  
	public function still_registered() {
	  global $wf;
	  $type = $wf->type($this->name);
	  return (!$this->_external) || ($this->_external && !is_woof_silent($type));
  }
  
  public function infer_sub_position() {
    /*
    we need to find the appropriate starting sub position for this post type, assuming it comes AFTER
    other post types in the same slot 
    */
    global $wpdb;
    $max_pos = $wpdb->get_var("SELECT MAX(`menu_sub_position`) FROM `".MPM::table("post-types")."` WHERE `menu_position` = ".$this->menu_position);

    if ($max_pos) {
      $this->menu_sub_position = $max_pos + 1;
    } else {
      $this->menu_sub_position = 1;
    }
    
  }
  
  public function delete($args = array()) {
    
    $r = wp_parse_args($args, array(
      "posts" => "leave",
      "posts_reassign_type" => "",
      "field_sets" => "keep",
      "field_data" => "keep"
    ));
    
    $this->unlink_taxonomies();
    
    $posts_action = $r["posts"];
    
    if ($posts_action == "delete") {
      $this->delete_posts();
    } else if ($posts_action == "trash") {
      $this->trash_posts();
    } else if ($posts_action == "reassign") {
      $this->reassign_posts($r["posts_reassign_type"], $r["field_sets"]);
    } 
    
    if ($posts_action != "reassign" && $r["field_sets"] == "delete") {
      $this->delete_field_sets();
    }
    
    if (isset($_POST["field_data"]) && $r["field_data"] == "delete") {
      $this->delete_meta();
    }
    
    parent::delete();
  }
  
  
  public function validate($op) {
    
    global $wpdb;
    
    if ($this->name == "") {
      $this->err(__("<em>Singular Name</em> must be provided", MASTERPRESS_DOMAIN), "name");
    } else {
      
      // check there isn't already a post type with this name
      $result = self::find_by_name($this->name);

      if ($result != "" && $result->id != $this->id) {
        $this->err(sprintf(__("Sorry, a post type named <em>%s</em> already exists", MASTERPRESS_DOMAIN), $this->name), "name");
      }

    }

    if ($this->plural_name == "") {
      $this->err(__("<em>Plural Name</em> must be provided", MASTERPRESS_DOMAIN), "plural_name");
    }

  }

  public function unlink_taxonomies() {
    global $wpdb;
    
    $taxonomies = $this->taxonomies();

    foreach ($taxonomies as $tax) {
      // update the taxonomy to exclude this post type
      $tax->object_type = WOOF::array_remove($tax->object_type, $this->name);
      $tax->update();
    }

  }
  
  public function templates() {
    $sql = "SELECT * FROM `".MPM::table("templates")."` WHERE ".MPM::visibility_rlike( "post_types", $this->name );
    return MPM::get_models("template", $sql);
  }


  public function link_taxonomy($taxonomy) {
    self::link_taxonomies( array($taxonomy) );
  }
  
  public function link_taxonomies($taxonomies) {
    global $wpdb;

    foreach ($taxonomies as $tax) {
      $object_type = $tax->object_type();
      array_push($object_type, $this->name);
      $object_type = array_unique($object_type);
      $tax->object_type = $object_type;
      $tax->update();
    }

  }
  
  public function linked_to_taxonomy($taxonomy) {
    global $wpdb;
    return !is_null($wpdb->get_var("SELECT `id` FROM `".MPM::table("taxonomies")."` WHERE `name` = '{$taxonomy->name}' AND object_type RLIKE '\"".$this->name."\"' "));
  }

  public function linked_to_taxonomy_name($taxonomy_name) {
    global $wpdb;
    return !is_null($wpdb->get_var("SELECT `id` FROM `".MPM::table("taxonomies")."` WHERE `name` = '{$taxonomy_name}' AND object_type RLIKE '\"".$this->name."\"' "));
  }
  
  public function taxonomies() {
    return MPM::get_models("taxonomy", "SELECT * FROM `".MPM::table("taxonomies")."` WHERE object_type RLIKE '\"".$this->name."\"'");
  }
  
  public function delete_posts() {
    global $wf;
    
    $pt = $wf->post_types($this->name);
    
    foreach ($pt->posts("post_status=any") as $post) {
      wp_delete_post($post->id(), true);
    }
    
    
  }
  
  public function trash_posts() {

    global $wf;
    
    $pt = $wf->post_types($this->name);
    
    if ($pt) {
      foreach ($pt->posts() as $post) {
        wp_delete_post($post->id(), false);
      }
    }
    
  }
  
  public function columns_by_key() {

    if (!isset($this->columns_by_key)) {

      
      foreach ($this->columns() as $column) {
        $title = "";
        
        if (isset($column["title"])) {
          $title = $column["title"];
        }
      
        $key = "custom_".WOOF_Inflector::underscore($title);
        
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
      array("core" => "title", "title" => "Title", "disabled" => "", "content" => "{col.title}"),
      array("core" => "author", "title" => "Author", "disabled" => "", "content" => "{col.author}" ),
      array("core" => "categories", "title" => "Categories", "disabled" => "", "content" => "{col.categories}" ),
      array("core" => "tags", "title" => "Tags", "disabled" => "", "content" => "{col.tags}" ),
      array("core" => "comments", "title" => "Comments"),
      array("core" => "date", "title" => "Date", "disabled" => "", "content" => "{col.date}")
    );
    
    if ($this->columns == "") {
      return $default;
    }

    return $this->columns;
  }
  
  public function reassign_posts($new_post_type_name, $field_sets_action = "keep") {
    global $wpdb;
    
    $post_type_name = $this->name;
    
    
    if ($new_post_type_name) {

      // get the list of post ids for the existing type, which we'll need for later
      $post_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->posts WHERE post_type = '$post_type_name'");

      // update the posts
      $wpdb->query("UPDATE $wpdb->posts SET post_type = '$new_post_type_name' WHERE post_type = '$post_type_name'");
      
      if ($field_sets_action == "keep") {
        
        // update the link tables for this post type
        
        // need to check if there are any clashes for field sets named the same thing

        $sql = "SELECT id, name, visibility FROM ".MPU::table("field-sets")." WHERE type = 'p' AND ".MPM::visibility_rlike("post_types", $this->name);
        
        $post_type_sets = $wpdb->get_results($sql);
          
          
        $sql = "SELECT id, name, visibility FROM ".MPU::table("field-sets")." WHERE type = 'p' AND ".MPM::visibility_rlike("post_types", $this->name);
        
        $new_post_type_sets = $wpdb->get_results($sql);
        
        $clashes = array();
        
        foreach ($post_type_sets as $post_type_set) {
          foreach ($new_post_type_sets as $new_post_type_set) {
            if ($post_type_set->name == $new_post_type_set->name) {
              
              // a set with this name already exists, so we'll rename this one to "_2"
              
              $sql = "UPDATE ".MPU::table("field-sets")." SET name = CONCAT(name, '_2'), singular_name = CONCAT(singular_name, '_2') WHERE id = ".$post_type_set->id;
              
              $wpdb->query($sql);
              
              // now we need to update the meta keys for any meta values that were bound to this set
               
              // now get the fields inside this clashing set
              
              $field_ids = $wpdb->get_col("SELECT id FROM ".MPU::table("fields")." WHERE field_set_id = ".$post_type_set->id);
    
              if (count($field_ids)) {
      
                // get a list of meta values bound to the fields in this clashing set.
    
                $sql = "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (".implode(",", $field_ids).")";
                $rows = $wpdb->get_results($sql); 
                
                // this will need some serious testing!

                foreach ($rows as $row) {
                  list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
                  $wpdb->query("UPDATE $wpdb->postmeta SET meta_key = REPLACE(meta_key, '".$set_name."', '".$set_name."_2') WHERE post_id = ".$row->post_id." AND ( meta_key = '".MPFT::meta_key($set_name, $field_name)."' OR meta_key LIKE '".MPFT::meta_key($set_name, $field_name).":%' ) ");
                }
      
                
              }
              
            }
          }
          
        }
        
        foreach ($post_type_sets as $set) {
          $visibility = MPU::db_decode($set->visibility);
          $visibility["post_types"] = $new_post_type_name;
          $sql = "UPDATE ".MPU::table("field-sets")." SET visibility = '".MPU::db_encode($visibility)."' WHERE id = ".$set->id;
          $wpdb->query($sql);
        }
          
      
        
        
      } else { 
        $this->delete_field_sets();
      }
      
      
    }

  }
   
  public function delete_field_sets() {
   
    global $wpdb;
    
    // delete the field sets of type 'p'

    $sql = "SELECT id FROM ".MPU::table("field-sets")." WHERE type = 'p' AND ".MPM::visibility_rlike("post_types", $this->name);
          
    $post_type_sets = $wpdb->get_col($sql);

    if (count($post_type_sets)) {
      
      // delete JUST the POST TYPE field sets assigned to this post type
      $wpdb->query("DELETE FROM ".MPU::table("field-sets")." WHERE id IN (".implode(",", $post_type_sets).") ");
      
      // delete the fields within those sets
      $wpdb->query("DELETE FROM ".MPU::table("fields")." WHERE field_set_id IN (".implode(",", $post_type_sets).") ");
      
    }
  }
       

  
  public function display_label() {
    return $this->labels["menu_name"];
  }
  

  public function field_sets($orderby = "position asc") {

    $sql = "SELECT * FROM ".MPU::table("field-sets")." WHERE ( type = 'p' OR type = 's') AND ".MPM::visibility_rlike("post_types", $this->name)." ORDER BY $orderby";
      
    if ($orderby == "position asc") { // try to return a cached version
      
      if (!isset($this->field_sets)) {
        $this->field_sets = MPM::get_models("field-set", $sql); 
      }

      return $this->field_sets;
      
    } 
    
    return MPM::get_models("field-set", $sql);
    
  }
  
  public function post_type_field_sets($orderby = "position asc") {
    $sql = "SELECT * FROM ".MPU::table("field-sets")." WHERE ( type = 'p') AND ".MPM::visibility_rlike("post_types", $this->name)." ORDER BY $orderby";
      
    if ($orderby == "position asc") { // try to return a cached version
      
      if (!isset($this->post_type_field_sets)) {
        $this->post_type_field_sets = MPM::get_models("field-set", $sql); 
      }

      return $this->post_type_field_sets;
      
    } 
    
    return MPM::get_models("field-set", $sql);
  }
  
  
  public function menu_icon_url($fallback = true, $object_icon = true) {
    return MPU::menu_icon_url($this->menu_icon, $fallback, "post_type", $object_icon);
  }

  public function menu_icon_exists() {
    return MPU::menu_icon_exists($this->menu_icon);
  }
  
  public function post_count() {
    global $wf;
    $posts = $wf->types($this->name)->posts("post_status=any");
    return $posts->count();
  }
  
  public function meta_count() {
    global $wpdb;
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE `post_id` IN (SELECT ID FROM $wpdb->posts WHERE post_type = '".$this->name."')");
    return $count;
  }

  public function delete_meta() {
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->postmeta WHERE `post_id` IN (SELECT ID FROM $wpdb->posts WHERE post_type = '".$this->name."')");
  }
  
  public function field_set_count() {
    global $wpdb;
    $post_type_sets = $wpdb->get_col("SELECT id FROM ".MPU::table("field-sets")." WHERE type = 'p' AND ".MPM::visibility_rlike("post_types", $this->name));
    return count($post_type_sets);
  }
  
  public function rep() {
    
    $rep = $this->data() + array();
    
    $rep["field_sets"] = array();
    
    foreach ($this->post_type_field_sets("id asc") as $field_set) {
      $rep["field_sets"][] = $field_set->rep();
    } 
    
    return $rep;
    
  }
  
  public static function insert_builtin() {

    // add the appropriate built-in post types (page and post) if required
    // we'll ignore menu item, attachment, and revision since there's no MasterPress tweaks applicable to those  

    $post_type_post = MPM_PostType::find_by_name("post");

    if (!$post_type_post) {

      $post_type_post = new MPM_PostType();
    
      $post_type_post->set(      
        array( 
          "name" => "post",
          "plural_name" => "posts",
          "disabled" => false,
          "labels" => array(
            "name" => __("Posts", MASTERPRESS_DOMAIN),
            "singular_name" => __("Post", MASTERPRESS_DOMAIN),
            "add_new" => __("Add New", MASTERPRESS_DOMAIN),
            "add_new_item" => __("Add New Post", MASTERPRESS_DOMAIN),
            "edit" => __("Edit Post", MASTERPRESS_DOMAIN),
            "edit_item" => __("Edit Post", MASTERPRESS_DOMAIN),
            "new_item" => __("New Post", MASTERPRESS_DOMAIN),
            "view" => __("View Post", MASTERPRESS_DOMAIN),
            "view_item" => __("View Post", MASTERPRESS_DOMAIN),
            "search_items" => __("Search Posts", MASTERPRESS_DOMAIN),
            "not_found" => __("No posts found.", MASTERPRESS_DOMAIN),
            "not_found_in_trash" => __("No posts found in Trash.", MASTERPRESS_DOMAIN),
            "parent_item_colon" => "", 
            "menu_name" => __("Posts", MASTERPRESS_DOMAIN)
          ),
        
          "description" => "",
          "publicly_queryable" => true,
          "exclude_from_search" => false,
          "show_ui" => true,
          "show_in_menu" => true,
          "menu_position" => 5,
          "menu_sub_position" => 0,
          "menu_icon" => "menu-icon-posts.png",
          "manage_sort_order" => "post_date|desc",
          "capability_type" => "post",
          "capabilities" => array(),
          "map_meta_cap" => true,
          "hierarchical" => false,
          "supports" => "title,editor,author,thumbnail,excerpt,trackbacks,custom-fields,comments,revisions,page-attributes,post-formats",
          "permalink_epmask" => "EP_PERMALINK",
          "has_archive" => true,
          "rewrite" => array( "slug" => "", "with_front" => true, "feeds" => true, "pages" => false ),
          "query_var" => "post",
          "can_export" => true,
          "show_in_nav_menus" => true,
          "_builtin" => true,
          "_external" => false
        )
      );
    
      $post_type_post->insert();
    
    }
      
    $tax_post_tag = MPM_Taxonomy::find_by_name("post_tag");
    $tax_category = MPM_Taxonomy::find_by_name("category");
    
    if ( !$post_type_post->linked_to_taxonomy( $tax_post_tag ) ) {
      $post_type_post->link_taxonomy( $tax_post_tag );
    }

    if ( !$post_type_post->linked_to_taxonomy( $tax_category ) ) {
      $post_type_post->link_taxonomy( $tax_category );
    }

    
    $post_type_page = MPM_PostType::find_by_name("page");
    
    if (!$post_type_page) {

      $post_type_page = new MPM_PostType();
    
      $post_type_page->set(      
        array( 
          "name" => "page",
          "plural_name" => "pages",
          "disabled" => false,
          "labels" => array(
            "name" => __("Pages", MASTERPRESS_DOMAIN),
            "singular_name" => __("Page", MASTERPRESS_DOMAIN),
            "add_new" => __("Add New", MASTERPRESS_DOMAIN),
            "add_new_item" => __("Add New Page", MASTERPRESS_DOMAIN),
            "edit" => __("Edit Page", MASTERPRESS_DOMAIN),
            "edit_item" => __("Edit Page", MASTERPRESS_DOMAIN),
            "new_item" => __("New Page", MASTERPRESS_DOMAIN),
            "view" => __("View Page", MASTERPRESS_DOMAIN),
            "view_item" => __("View Page", MASTERPRESS_DOMAIN),
            "search_items" => __("Search Pages", MASTERPRESS_DOMAIN),
            "not_found" => __("No pages found.", MASTERPRESS_DOMAIN),
            "not_found_in_trash" => __("No pages found in Trash.", MASTERPRESS_DOMAIN),
            "parent_item_colon" => __("Parent Page", MASTERPRESS_DOMAIN), 
            "menu_name" => __("Pages", MASTERPRESS_DOMAIN)
          ),
        
          "description" => "",
          "publicly_queryable" => true,
          "exclude_from_search" => false,
          "show_ui" => true,
          "show_in_menu" => true,
          "menu_position" => 20,
          "menu_sub_position" => 0,
          "menu_icon" => "menu-icon-pages.png",
          "manage_sort_order" => "menu_order|asc",
          "capability_type" => "page",
          "capabilities" => array(),
          "map_meta_cap" => true,
          "hierarchical" => true,
          "supports" => "title,editor,author,thumbnail,excerpt,trackbacks,custom-fields,comments,revisions,page-attributes,post-formats",
          "permalink_epmask" => "1",
          "has_archive" => true,
          "rewrite" => array( "slug" => "", "with_front" => false, "feeds" => false, "pages" => true ),
          "query_var" => "page",
          "can_export" => true,
          "show_in_nav_menus" => true,
          "_builtin" => true,
          "_external" => false
        )
      );
      
      $post_type_page->insert();

    }
      

	$post_type_attachment = MPM_PostType::find_by_name("attachment");
    
    if (!$post_type_attachment) {

      $post_type_attachment = new MPM_PostType();
    
      $post_type_attachment->set(      
        array( 
          "name" => "attachment",
          "plural_name" => "attachments",
          "disabled" => false,
          "labels" => array(
            "name" => __("Media", MASTERPRESS_DOMAIN),
            "singular_name" => __("Media", MASTERPRESS_DOMAIN),
            "add_new" => __("Add New", MASTERPRESS_DOMAIN),
            "add_new_item" => __("Add New Post", MASTERPRESS_DOMAIN),
            "edit" => __("Edit Media", MASTERPRESS_DOMAIN),
            "edit_item" => __("Edit Media", MASTERPRESS_DOMAIN),
            "new_item" => __("New Post", MASTERPRESS_DOMAIN),
            "view" => __("View Media", MASTERPRESS_DOMAIN),
            "view_item" => __("View Media", MASTERPRESS_DOMAIN),
            "search_items" => __("Search Media", MASTERPRESS_DOMAIN),
            "not_found" => __("No media found.", MASTERPRESS_DOMAIN),
            "not_found_in_trash" => __("No media found in Trash.", MASTERPRESS_DOMAIN),
            "parent_item_colon" => __("Parent Post", MASTERPRESS_DOMAIN), 
            "menu_name" => __("Media", MASTERPRESS_DOMAIN)
          ),
        
          "description" => "",
          "publicly_queryable" => true,
          "exclude_from_search" => false,
          "show_ui" => true,
          "show_in_menu" => true,
          "menu_position" => 20,
          "menu_sub_position" => 0,
          "menu_icon" => "menu-icon-media.png",
          "manage_sort_order" => "menu_order|asc",
          "capability_type" => "post",
          "capabilities" => array(),
          "map_meta_cap" => true,
          "hierarchical" => true,
          "supports" => "title,editor,author,thumbnail,excerpt,trackbacks,custom-fields,comments,revisions,page-attributes,post-formats",
          "permalink_epmask" => "1",
          "has_archive" => true,
          "rewrite" => array( "slug" => "", "with_front" => false, "feeds" => false, "pages" => true ),
          "query_var" => "page",
          "can_export" => true,
          "show_in_nav_menus" => true,
          "_builtin" => true,
          "_external" => false
        )
      );
      
      $post_type_attachment->insert();

    }

	
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