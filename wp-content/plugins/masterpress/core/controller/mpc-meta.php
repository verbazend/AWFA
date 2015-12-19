<?php

class MPC_Meta extends MPC {

  protected static $version_saved = false;
  
  public static function save_post_meta($post_id) {
    self::save_meta($post_id, "post");
  }

  public static function save_term_meta($term_id) {
    self::save_meta($term_id, "term");
  }

  public static function save_user_meta($user_id) {
    self::save_meta($user_id, "user");
  }

  public static function save_site_meta($site_id) {
    self::save_meta($site_id, "site");
  }
  
  public static function get_version() {
    
    global $wpdb;
    
    if (isset($_GET["id"])) {
      
      $id = $_GET["id"];
      $version = $wpdb->get_row("SELECT * FROM `".MPU::site_table("versions")."` WHERE `version_id` = $id");
      
      if ($version) {
        
        $ret = array("value" => MPU::db_decode($version->value));
        
        if (isset($_GET["fetch_template"], $_GET["model_id"])) {

          $set = MPM_FieldSet::find_by_id($_GET["model_id"]);
          
          if ($set) {
            $template = MPV_Meta::get_preview_set_template($set);
            
            $ret["template"] = $template;
          }
          
          
        }
        
        self::ajax_success( $ret );
        
      } else {
        self::ajax_error(__("The version could not be found", MASTERPRESS_DOMAIN));
      }
      
    } else {
      
      self::ajax_error(__("Cannot fetch version. No version id was supplied", MASTERPRESS_DOMAIN));
      
    }
    
  }
  
  public static function delete_term_meta($term_id, $meta_key, $taxonomy = NULL) {
    global $wpdb;

    $taxonomy = self::infer_taxonomy_name($taxonomy);

    $sql = "DELETE FROM `".MPU::site_table("termmeta")."` WHERE `term_id` = ".$term_id." AND `meta_key` = '".$meta_key."'";
    
    if ($taxonomy != "") {
      $sql .= " AND taxonomy = '".$taxonomy."' ";
    }
    
    return $wpdb->query($sql);
  }
  
  
  public static function infer_taxonomy_name($taxonomy = NULL) {
    if (is_null($taxonomy)) {
      if (isset($_REQUEST["taxonomy"])) {
        $taxonomy = $_REQUEST["taxonomy"];
      }
      
      if (is_null($taxonomy)) {
        $taxonomy = get_query_var($taxonomy);
      }
    }
    
    if (is_null($taxonomy)) {
      $taxonomy = "";
    }
    
    return $taxonomy;
  }
  
  public static function add_term_meta($term_id, $meta_key, $meta_value, $taxonomy = NULL) {
    global $wpdb, $wf;
    
    $taxonomy = self::infer_taxonomy_name($taxonomy);
    
    $result = $wpdb->insert( 
      MPU::site_table("termmeta"), 
      array(
        "term_id" => $term_id,
        "meta_key" => $meta_key,
        "meta_value" => $meta_value,
        "taxonomy" => $taxonomy
      ),
      array("%d", "%s", "%s", "%s")
    );
    
    if ($result) {
      return $wpdb->insert_id;
    }
    
    return false;

  }

  public static function delete_site_meta($site_id, $meta_key) {
    global $wpdb;
    return $wpdb->query("DELETE FROM `".MPU::table("sitemeta")."` WHERE `site_id` = ".$site_id." AND `meta_key` = '".$meta_key."'");
  }


  public static function add_site_meta($site_id, $meta_key, $meta_value) {
    global $wpdb;
    
    $result = $wpdb->insert( 
      MPU::table("sitemeta"), 
      array(
        "site_id" => $site_id,
        "meta_key" => $meta_key,
        "meta_value" => $meta_value
      ),
      array("%d", "%s", "%s")
    );
    
    if ($result) {
      return $wpdb->insert_id;
    }
    
    return false;

  }
  
  public static function delete_object_meta($object_type, $object_id, $meta_key) {
    if ($object_type == "post") {
      delete_metadata("post", $object_id, $meta_key);
    } else if ($object_type == "term") {
      self::delete_term_meta($object_id, $meta_key);
    } else if ($object_type == "user") {
      delete_user_meta( $object_id, $meta_key );
    } else if ($object_type == "site") {
      self::delete_site_meta( $object_id, $meta_key );
    }
  }

  public static function add_object_meta($object_type, $object_id, $meta_key, $meta_value, $value_type = null) {
        
    if ($object_type == "post") {
      add_post_meta($object_id, $meta_key, $meta_value);
    } else if ($object_type == "term") {
      self::add_term_meta($object_id, $meta_key, $meta_value, $value_type);
    } else if ($object_type == "user") {
      add_user_meta( $object_id, $meta_key, $meta_value);
    } else if ($object_type == "site") {
      self::add_site_meta( $object_id, $meta_key, $meta_value);
    }
  }
  
  public static function post_val($key, $fallback = "") {
    if (isset($_POST[$key])) {
      return $_POST[$key];
    }
    
    return $fallback;
  }
  
  
  
  public static function save_meta($object_id, $object_type) {
    
    global $wpdb, $meow_provider;
    global $wf;
    
    do_action("mp_pre_save_meta", $object_id, $object_type);
    
    if (apply_filters("mp_do_save_meta", $do = true, $object_id, $object_type)) { 
    
      MPM::incl("field");

      $object_type_type = "";
    
      $current_data = array();

      if ($object_type == "post") {

        // check the edit post meta cap for this type
      
        $cap = "edit_post";
      
        $post = $wf->post($object_id);

      
        if ($post->exists()) {
          $type = $post->type();
      
          $cap = $type->cap("edit_post", "edit_post");
      
          // update the template meta
        
          if ($type->supports("mp-page-attributes")) {
            if (isset($_POST["page_template"])) {
              $template = $_POST["page_template"];
            
              if ($template == "") {
                delete_post_meta($object_id, "_wp_page_template");
              } else {
                update_post_meta($object_id, "_wp_page_template", $template);
              }
            }
          
          }
        
        }
      
        if ( !current_user_can( $cap, $object_id ) ) {
          return $object_id;
        }

      
      } else if ($object_type == "term") {
      
        // check the edit terms cap for the taxonomy
        $cap = "manage_categories";

        $taxonomy_name = self::infer_taxonomy_name();
        $tax = $wf->taxonomy($taxonomy_name);
      
        $object_type_type = $taxonomy_name;
      
        if ($tax->exists()) {
          $cap = $tax->cap("edit_terms", "manage_categories");
        }
    
        if ( !current_user_can( $cap ) ) {
          return $object_id;
        }
      } else if ($object_type == "user") {
        if ( !current_user_can( 'edit_users' ) ) {
          return $object_id;
        }
      } else if ($object_type == "site") {
        if ( !current_user_can( 'edit_posts' ) ) {
          return $object_id;
        }
      }
  
      $meta = self::post_val('mp_meta', array());
      $meta_order = self::post_val('mp_meta_order', array());
      $meta_model = self::post_val('mp_meta_model', array());
      $meta_prop = self::post_val('mp_meta_prop', array());
      $meta_field_ids = self::post_val('mp_meta_field_ids', array());

      $meta_dirty = self::post_val('mp_meta_dirty', array());
      $meta_versions = self::post_val('mp_meta_versions', array());

      $field_models = array();
    
      if (is_array($meta_field_ids)) {
        $meta_field_ids = array_unique($meta_field_ids);
      
        $results = MPM_Field::find_by_id_in($meta_field_ids);
      
        foreach ($results as $field) {
          $field_models[$field->id] = $field;
        }
      
      }
    
      $wpdb->show_errors();
    
      if (isset($_POST["mp_meta_model"])) {
      
        if ($object_type == "post") {
          if ( $the_post = wp_is_post_revision( $object_id ) ) {
            $object_id = $the_post;
          }
        } 

        try {
          $meow_provider->create_version($object_id, $object_type, $object_type_type, "initial", $meta_versions);
        } catch( Exception $e) {
          // silently catch, we REALLY don't want this to prevent a data save if something goes wrong!
        }
      
        /* Delete the old post meta */

        foreach ( $meta_model as $set_name => $fields ) {
        
          foreach ($fields as $field_name => $model_id) {
            $field = $field_models[$model_id];
          
            $meta_name = $set_name.".".$field_name;

            self::delete_object_meta($object_type, $object_id, $meta_name);
        
            // now go through the properties too

            $dont_care = MPFT::type_class($field->type);
          
            foreach (MPFT::type_properties($field->type) as $prop) {
              self::delete_object_meta($object_type, $object_id, $meta_name.":".$prop);
            }
             
          
          }
        
        }

        $model_id_prop_stored = array();
      
        // Create the new values
        foreach( $meta as $set_name => $set_items ) {
        
          $set_index = 0;
        
          if (is_array($set_items)) {
        
            foreach ($set_items as $fields) {

              $set_index++;

          
              foreach ($fields as $field_name => $value) {
            
                // here the field type should prepare the value, if necessary
            
                // grab the type
            
                $model_id = $meta_model[$set_name][$field_name];
            
                $model = $field_models[$model_id];
            
                $val = MPU::db_encode($value);
            
                if ($model) {
              
                  if ($type_class = MPFT::type_class($model->type)) {
                    $val = MPU::db_encode(call_user_func_array( array($type_class, "value_for_save"), array($value, $model)));
                  }
            
                  // create the post meta
              
                  $meta_name = MPFT::meta_key($set_name, $field_name);
              
                  self::add_object_meta($object_type, $object_id, "{$meta_name}", $val);

                  // now store the properties
              
                  foreach (MPFT::type_properties($model->type) as $prop) {

                    if ($prop == "field_id" && !isset($model_id_prop_stored[$model_id])) {
                      $model_id_prop_stored[$model_id] = true;

                      self::add_object_meta($object_type, $object_id, "{$meta_name}:{$prop}", $model_id);
                  
                    } else {
                  
                      $prop_set_index = $meta_order[$set_name][$set_index];

                      if (isset($meta_prop[$set_name][(int) $prop_set_index][$field_name][$prop])) {
                        $prop_value = $meta_prop[$set_name][(int) $prop_set_index][$field_name][$prop];
                      }
                  
                      if (isset($prop_value)) {
                        self::add_object_meta($object_type, $object_id, "{$meta_name}:{$prop}", $prop_value);
                      }
                  
                    }

                  }
          
              
                }
            
              } // endforeach $fields
          
          
              // fill in blanks for any values that weren't submitted (this happens with checkboxes that are not checked)
          
              foreach ($meta_model[$set_name] as $field_name => $model_id) {
                if (!isset($fields[$field_name])) {
                  $meta_name = MPFT::meta_key($set_name, $field_name);
                  self::add_object_meta($object_type, $object_id, "{$meta_name}", "");
              
                  // now store the properties for blanks (if required)
              
                  foreach (MPFT::type_properties($field->type) as $prop) {

                    if ($prop == "field_id" && !isset($model_id_prop_stored[$model_id])) {
                      $model_id_prop_stored[$model_id] = true;
                      self::add_object_meta($object_type, $object_id, "{$meta_name}:{$prop}", $model_id);

                    } else {
                  
                      if (isset($meta_prop[$set_name][$set_index][$field_name][$prop])) {
                        $prop_value = $meta_prop[$set_name][$set_index][$field_name][$prop];
                        self::add_object_meta($object_type, $object_id, "{$meta_name}:{$prop}", $prop_value);
                      }
                  
                    }

                  }

              
                }

              }

          
            } 

    
          } // endif is_array(set_items)


        } // foreach $set_name => $set_items


        // create the current content version

        if (!self::$version_saved) {
          try {
            $meow_provider->create_version($object_id, $object_type, $object_type_type, $meta_dirty, $meta_versions);
          } catch( Exception $e) {
          
            // silently catch, we REALLY don't want this to prevent a data save if something goes wrong!
          }

          self::$version_saved = true;
        }
      
      } // isset $_POST["mp_meta_model"]
    
    }
  
    do_action("mp_after_save_meta", $object_id, $object_type);

    
  }
  


}
