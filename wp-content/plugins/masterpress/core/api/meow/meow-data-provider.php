<?php 



class MEOW_DataProvider extends WOOF_Wrap {
  
  private static $run_count = 0;
  private $post_relations = array();
  private $term_relations = array();
  private $term_relations_fwd = array();
  private $user_relations = array();

  private $post_relations_fields = array();
  private $term_relations_fields = array();
  private $user_relations_fields = array();
  
  private $optimize_mode = "";
  
  public $object_ids = array();
  
  public $template_switch;
  
  public $data;
  
  protected $termmeta;
  protected $sitemeta;
  
  public function __construct() {
    global $wpdb;
    $this->data = array();
    $this->sitemeta = MPU::table("sitemeta");
  }

	public function termmeta() {
		return MPU::site_table("termmeta");
	}
	
  public function type_key($object) {
    return $this->type_key_class(get_class($object)); 
  }

  public function type_key_class($class) {
    $parts = explode("_", $class);
    $last = count($parts) - 1;
    return strtolower($parts[$last]);
    //return strtolower(preg_replace("/(MEOW_|WOOF_)/", "", $class)); 
  }

  public function set_optimize_mode( $mode ) {
    $this->optimize_mode = $mode;
  }
  
  public function set_map($object) {
    
    global $wpdb;
    
    // get all of the data that's assigned to 
    
    // first, grab all of the distinct fields recorded in the database
    
    $object_type = $this->type_key($object);
    
    $object_id = $object->id();
    
    if ($object_type == "post") {
      // grab the data - here we'll get all of the field values within this set
      $sql = "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} pm WHERE post_id = $object_id AND meta_key LIKE '%.%' AND meta_key NOT LIKE '%.%.%' ORDER BY meta_key";
      $row_key = "post_id";
    } else if ($object_type == "user") {
      $sql = "SELECT DISTINCT meta_key FROM {$wpdb->usermeta} WHERE user_id = $object_id AND meta_key LIKE '%.%' AND meta_key NOT LIKE '%.%.%' ORDER BY meta_key";
      $row_key = "user_id";
    } else if ($object_type == "term") {

      $tax = $object->taxonomy();
      
      $tax_sql = "";
      
      if ($tax) {
        $tax_name = $tax->name();
        $tax_sql = " tm.taxonomy = '$tax_name' AND ";
      }

      
      $sql = "SELECT DISTINCT meta_key FROM {$this->termmeta()} tm INNER JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE $tax_sql meta_key LIKE '%.%' AND meta_key NOT LIKE '%.%.%' ORDER BY meta_key";
      
      $row_key = "term_id";
    } else if ($object_type == "site") {
      $sql = "SELECT DISTINCT meta_key FROM {$this->sitemeta} WHERE site_id = {$object_id} AND meta_key LIKE '%.%' AND meta_key NOT LIKE '%.%.%' ORDER BY meta_key";
    }
    
    $set_map = array();
    
    if (isset($sql)) {
      $results = $wpdb->get_results($sql);
    
      foreach ($results as $row) {
        
        list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);
        
        if ($set_name != "" && $field_name != "") {
          if (!isset($set_map[$set_name])) {
            $set_map[$set_name] = array();
          }
          
          $set_map[$set_name][$field_name] = true;
        }
        
      }
    }

    return ($set_map);
  
  }
  
  public function create_version($object_id, $object_type, $object_type_meta = "", $dirty = array(), $versions_count = array()) {
    
    global $wf, $wpdb, $current_user;
    
    $initial = !is_array($dirty) && $dirty == "initial";
    
    if ($object_type == "post") {
      $object = $wf->post($object_id);
    } else if ($object_type == "term") {
      $object = $wf->term_by_id($object_id, $object_type_meta);
    } else if ($object_type == "user") {
      $object = $wf->user($object_id);
    } else if ($object_type == "site") {
      $object = $wf->site($object_id);
    }
    
    if (isset($object) && !is_woof_silent($object)) {
      
      $sets_to_record = array();

      $sets = array();
      
      foreach ($versions_count as $set => $count) {
        
        if ($count != 0) {

          // don't record if version recording is off (zero value)
        
          $sql = "SELECT * FROM ".MPU::site_table('versions')." WHERE object_id = $object_id AND object_type = '$object_type' "; 
        
          if ($object_type_meta != "") {
            $sql .= " AND object_type_meta = '$object_type_meta' ";
          }

          $sql .= " AND field_set_name = '".$set."'";

          $vr = $wpdb->get_results($sql);

          $record = false;
          
          if ($initial) {
            if (count($vr) == 0) {
              $record = true;
            }
          }
          
          if (is_array($dirty) && isset($dirty[$set])) {
            $record = true;
          }
          
          if ($record) {
            $sets_to_record[$set] = count($vr) - $count; // store the number of versions to delete (the -1 is because we ALSO store the current version)
          }
        
          
        }
        
      }
      
      
      if (!count($sets_to_record)) {
        return array(); // no need to do anything
      }
      
      $data = $this->get_meta($object);
      
      
      foreach ($data as $set => $fields) {
              
        if (isset($sets_to_record[$set])) {
        
          $json = json_encode($fields);

          if (! ( $initial && $json && $json == "[]" ) ) {
            
            $wpdb->insert( MPU::site_table("versions"), array(
                "date" => date("Y-m-d H:i:s"),
                "user_id" => $wf->the_user()->id,
                "object_id" => $object_id,
                "object_type" => $object_type,
                "object_type_meta" => $object_type_meta,
                "field_set_count" => count($fields),
                "field_set_name" => $set,
                "value" => "json:".$json
              )
            );
          
          
            if ($sets_to_record[$set] > 0) {
              // delete older versions of the content
            
              $sql = "SELECT version_id FROM ".MPU::site_table('versions')." WHERE object_id = $object_id AND object_type = '$object_type' "; 
              if ($object_type_meta != "") {
                $sql .= " AND object_type_meta = '$object_type_meta' ";
              }
              $sql .= " AND field_set_name = '".$set."' ORDER BY version_id LIMIT ".$sets_to_record[$set];
          
              $versions_to_delete = $wpdb->get_col($sql);
            
              if (count($versions_to_delete)) {
                $wpdb->query("DELETE FROM ".MPU::site_table('versions')." WHERE version_id IN (".implode(",", $versions_to_delete).")");
              }
            }
          
          }
        
        
        }

      
      }
      
      $this->uncache_data();
      
      return true;
      
    }
    
  }
  
  public function uncache_data() {
    unset($this->data);
    $this->data = array();
  }
  
  public function get_meta($object) {
    
    $map = $this->set_map($object);
    
    $object_type = $this->type_key($object);
    $object_id = $object->id();

    $site_id = $object->site_id();

    if ($object_type == "term") {
      $object_id = $object->taxonomy_name().":".$object_id;
    }

    // now for each item in the map, we'll fill the data
    
    $fd = &$this->data[$site_id][$object_type]["fields"];
    
    $data = array();
    
    
    foreach ($map as $set_name => $fields) {
    
      // prime the data 
      $dont_care = $this->set($set_name, $object);

      if (!isset($data[$set_name])) {
        $data[$set_name] = array();
      }
      
      foreach ($fields as $field => $nc) {
        
        if (isset($fd[$set_name.".".$field]["data"][$object_id])) {
          $values = &$fd[$set_name.".".$field]["data"][$object_id];
          
          foreach ($values as $index => $fv) {
            if (!isset($data[$set_name][$index])) {
              $data[$set_name][$index] = array();
            }
            
            
            $data[$set_name][$index][$field] = array("value" => $fv->val, "prop" => $fv->prop);
          }
          
        }
      
      }
      
      // convert associative array to standard array
      $data[$set_name] = array_values($data[$set_name]);
      
    }
    
    return $data;

  }
  
  public function set($name, $object, $with_raw = false, $fetch = true) {
    
    global $wp_query;
    
    global $wf;
    
    global $wpdb;
    
    // check if this set already exists under the object id

    $object_id = $object->id();

    $c_object_id = $object_id;
    
    $type = $this->type_key($object);
    
    $site_id = $object->site_id();

	  if (!isset($this->data[$site_id])) {
      $this->data[$site_id] = array();
    }
    
	  if (!isset($this->data[$site_id][$type])) {
      $this->data[$site_id][$type] = array();
    }
    
    
    $dt = &$this->data[$site_id][$type];

    // init the sets and fields arrays (this is now restructured, to optimise repeat visits for blank data)
    if (!isset($dt["sets"])) {
      $dt["sets"] = array();
    }

    if (!isset($dt["fields"])) {
      $dt["fields"] = array();
    }
    
    $dsets = &$dt["sets"];
	  $dfields = &$dt["fields"];
    
    // grab the info
    
    if (isset($this->template_switch)) {
      $template = $this->template_switch;
    } else {
      $template = $wf->template_for_post($object_id);
    }
  
    if ($type == "post") {
      
      $assigned_sets = $this->post_type_field_sets($object->type_name(), $template);
    } else if ($type == "term") {
      
      $tax = $object->taxonomy();
      
      if ($tax) {
        $tax_name = $tax->name();
        $assigned_sets = $this->taxonomy_field_sets($tax_name);
      }

      $c_object_id = $tax_name.":".$object_id;

    } else if ($type == "user") {

      $role = $object->role();
      
      if ($role) {
        $role_name = $role->name();
        $assigned_sets = $this->role_field_sets($role_name);
      }
      
    } else if ($type == "site") {
      $assigned_sets = $this->site_field_sets();
    }
    
    if (isset($assigned_sets[$name])) {
      $set = $assigned_sets[$name];
    }
  
    if (!isset($set)) {
      // this set doesn't exist at all for this object, so no need to query, return blank info
      return array("info" => null, "count" => 0 );
    }
    
    $set_id = $name."_".$set->id;
    
    if (!array_key_exists($set_id, $dsets)) {
      $dsets[$set_id] = array();
    }
    
    $dset = &$dsets[$set_id];
    
    if (!isset($dset["info"])) {
      $dset["info"] = array();
    }
    
    
    $dsi = &$dset["info"];
    
    if (!isset($dsi[$c_object_id])) {
      $dsi[$c_object_id] = $assigned_sets[$name];
    }
    
    // setup the field info here too
    
    foreach ($assigned_sets[$name]->fields() as $field_name => $field) {
      
      $full_name = MPFT::meta_key($name, $field_name);

      if (!isset($dfields[$full_name])) {
        $dfields[$full_name] = array("info" => array());  // indexed by object id
      }
      
      $dfields[$full_name]["info"][$c_object_id] = $field;

    }
    
    $should_fetch = $fetch;
    
    if ($this->optimize_mode == "post_list") {
      // don't force a fetch if we're running in an optimised mode
      $should_fetch = false;
    }
     
    
    if (isset($dset["count"][$c_object_id]) && !$should_fetch) {
      
      // note that either of these may be empty, but we've certainly looked it up before
      
      $info = null;
      $count = 0;
      
      if (isset($dsi[$c_object_id])) {
        $info = $dsi[$c_object_id];
      }
      
      if (isset($dset["count"][$c_object_id])) {
        $count = $dset["count"][$c_object_id];
      }
      
      return array("info" => $info, "count" => $count);

    } else {
      
      // the set data hasn't been primed for this type of object (post, tax, user)
      
      $dset["count"] = array();

      $dsd = &$dset["count"];
      
      // now switch the the correct site for this object
      // we only want to switch sites on the META query, as all other queries need to operate in the database for the main site

      $object->switch_site();

      if ($type == "post") {
        // grab the data - here we'll get all of the field values within this set
        
        if ($this->optimize_mode == "post_list") {

          // fetch the fields for the current posts in the list

          $ids = array( $object_id );
          
          if (count($wp_query->posts)) {

            $ids = array();

            foreach ($wp_query->posts as $the) {
              $ids[] = $the->ID;
            }
          
          }
          
          $obj_sql = " post_id IN ( " . implode( ",", $ids ) . " ) AND ";
          
        } else {

          $obj_sql = " post_id = $object_id AND ";

        }
        
        
        $mkl = " meta_key LIKE '{$name}.%' ";
        
        $sql = "SELECT meta_key, meta_value, post_id, post_type FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE $obj_sql $mkl ORDER BY post_id, meta_key, meta_id";  
        
        $row_key = "post_id";
	    } else if ($type == "user") {
        
        $obj_sql = " user_id = $object_id AND ";
      
        $sql = "SELECT * FROM {$wpdb->usermeta} WHERE $obj_sql meta_key LIKE '{$name}.%' ORDER BY user_id, meta_key, umeta_id";
        $row_key = "user_id";
      } else if ($type == "term") {
        
        $obj_sql = "";
        
        $obj_sql = " tm.term_id = $object_id AND ";
        
        $sql = "SELECT tm.meta_key, tm.meta_value, tm.term_id, tt.taxonomy FROM {$this->termmeta()} tm INNER JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE $obj_sql tm.taxonomy = '".$tax_name."' AND tt.taxonomy = '".$tax_name."' AND meta_key LIKE '{$name}.%' ORDER BY term_id, meta_key, tmeta_id";
        $row_key = "term_id";
      } else if ($type == "site") {
        
        $sql = "SELECT sm.meta_key, sm.meta_value, sm.site_id FROM {$this->sitemeta} sm WHERE sm.site_id = {$object_id} AND meta_key LIKE '{$name}.%' ORDER BY site_id, meta_key, smeta_id";
        $row_key = "site_id";
      }

      $results = $wpdb->get_results($sql);

      $object->restore_site();

            
      if (isset($ids) && $ids) {
        // look for post ids not listed in the results.
        
        $res_ids = array();
        
        foreach ($results as $row) {
          $res_ids[$row->post_id] = true;
        }
        
        $no_values = array_diff($ids, array_keys($res_ids));
        
        foreach ($no_values as $row_id) {
          $dset["count"][$row_id] = 0;
        }
      }
           
      if (count($results) != 0) {
        
        $seen = array();
        $set_index = 1;
        $prop_index = 1;
        $last_row_id = null;
        
        $sd = array(); 

        $last_full_field_name = "";
        $last_prop_name = "";
        
      	foreach ($results as $row) {
  	      
  	      $row_id = $row->$row_key;
	        
	        if ($type == "term") {
	          $row_id = $row->taxonomy . ":" . $row_id;
          }
          
          list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($row->meta_key);

          $full_field_name = MPFT::meta_key($set_name, $field_name);
          
          if ($prop_name == "" && ( $full_field_name != $last_full_field_name || $row_id != $last_row_id )) {
            $set_index = 1;
            $prop_index = 1;
          } else {
            $set_index++;
          }
        
          if ($row_id != $last_row_id) {
            $row_set = null;
	        
  	        if ($type == "post") {
              // we need to infer the set id for this meta key
              // get the assigned field sets
          
              if (isset($this->template_switch) && $row_id == $object_id) {
                $row_template = $this->template_switch;
              } else {
                $object->switch_site();
                $row_template = $wf->template_for_post($row_id);
                $object->restore_site();

              }
              
              $row_assigned_sets = $this->post_type_field_sets($row->post_type, $row_template);
            } else if ($type == "term") {
              $row_assigned_sets = $this->taxonomy_field_sets($row->taxonomy);
            } else if ($type == "user") {
              if ($user = $wf->user($row_id)) {
                if ($role = $user->role()) {
                  $row_assigned_sets = $this->role_field_sets($role->name());
                }
              }
            
            } else if ($type == "site") {
              $row_assigned_sets = $this->site_field_sets();
            }
            
            if (isset($row_assigned_sets[$set_name])) {
              $row_set = $row_assigned_sets[$set_name];
            }
            
          }
          
          if ($prop_name != $last_prop_name) {
            $prop_index = 1;
          } else {
            $prop_index++;
          }
          
          
          $seen[$full_field_name] = true;
          
          $last_full_field_name = $full_field_name;
          $last_prop_name = $prop_name;

	        $last_row_id = $row_id;
	        
          //$field_info = $set_info->fields[$field_name];
        
          // init the set if not there already (note that this could be for OTHER objects)

          if (isset($row_set)) {
            
            $field_info = null;
            
            if (isset($row_set->fields[$field_name])) {
              $field_info = $row_set->fields[$field_name];
            }
          
            $row_set_id = $set_name."_".$row_set->id;
            
            if (!isset($dsets[$row_set_id])) {
              $dsets[$row_set_id] = array("count" => array(), "info" => array());  // each indexed by object id
            }
          
            // insert set info

            if (!isset($dsets[$row_set_id]["info"][$row_id])) {
              $dsets[$row_set_id]["info"][$row_id] = $row_set;
            }

            if (!$prop_name) {
              
              $current_count = null;
              
              if (isset($dsets[$row_set_id]["count"][$row_id])) {
                $current_count = $dsets[$row_set_id]["count"][$row_id];
              }
            
              if (isset($current_count)) {
                
                // only reset the count if it's larger than the current count (prevents data truncation for data with holes in it)
                
                if ($set_index > $current_count) {
                  if (isset($field_info)) {
                    $dsets[$row_set_id]["count"][$row_id] = $set_index;
                  }
                }
                
              } else {
                if (isset($field_info)) {
                  // we only want to set a count if the field exists
                  $dsets[$row_set_id]["count"][$row_id] = $set_index;
                }
              }
            
            }
            
            // init the field array for this name, if not already setup
          
            if (!isset($dfields[$full_field_name])) {
              $dfields[$full_field_name] = array();  
            }
          
            if (!isset($dfields[$full_field_name]["info"])) {
              $dfields[$full_field_name]["info"] = array(); // indexed by object id
            }

            if (!isset($dfields[$full_field_name]["data"])) {
              $dfields[$full_field_name]["data"] = array(); // indexed by object id
            }

            // init the field values array for this object, if not already setup
          
            if (!isset($dfields[$full_field_name]["data"][$row_id])) {
              $dfields[$full_field_name]["data"][$row_id] = array(); // indexed by set index
            }
          
            
            
            
            if (isset($field_info)) {
              
              // only set the data if this field exists

              if ($prop_name == "") {

                // this is the main field value
                $value = $this->load_value($row->meta_value, $field_info);
          
                if (is_array($value)) {
                  $blank = !count($value);
                } else {
            	    $blank = ( trim($value) == "" || is_null($row->meta_value) );
                }
        
                $fv = (object) array( "prop" => array(), "val" => $value, "__blank" => $blank );

                if ($with_raw) {
                  $fv->raw_value = MPU::db_decode($row->meta_value);
                  $fv->raw_prop = array();
                }
                
                $dfields[$full_field_name]["data"][$row_id][$set_index] = $fv;
            
              } else {
                // this is a property on the field value
            
                if (isset($dfields[$full_field_name]["data"][$row_id][$prop_index]->prop)) {
                  $dfields[$full_field_name]["data"][$row_id][$prop_index]->prop[$prop_name] = $this->load_prop($prop_name, $row->meta_value, $field_info);
                  
                  if ($with_raw) {
                    $dfields[$full_field_name]["data"][$row_id][$prop_index]->raw_prop[$prop_name] = MPU::db_decode($row->meta_value);
                  }
                }
            
              }
            } 
          
          } // isset $row_set
          
          
        }  // endforeach
      
      } // count results 
      
      $info = null;
      $count = 0;
      
      if (isset($dsets[$set_id]["info"][$c_object_id])) {
        $info = $dsets[$set_id]["info"][$c_object_id];
      }

      if (isset($dsets[$set_id]["count"][$c_object_id])) {
        $count = $dsets[$set_id]["count"][$c_object_id];
      }
      
      return array("info" => $info, "count" => $count);

    } 
	  
  }
  
  
  public function field($name, $set_index, $object, $fetch = false) {
	  
	  $field_data = new stdClass();
	  $field_info = null;
	  
	  $object_id = $object->id();
    
    $c_object_id = $object_id;

    $site_id = $object->site_id();
    
    $type = $this->type_key($object);

    if ($type == "term") {
      $tax = $object->taxonomy();
      
      if ($tax) {
        $tax_name = $tax->name();
      }

      $c_object_id = $tax_name.":".$object_id;
    }
    
    // get the field info
    
    if (isset($this->data[$site_id][$type]["fields"][$name]["info"][$c_object_id])) {
      $field_info = $this->data[$site_id][$type]["fields"][$name]["info"][$c_object_id];
    }
  
    if (isset($this->data[$site_id][$type]["fields"][$name]["data"][$c_object_id][$set_index])) {
      $field_data = $this->data[$site_id][$type]["fields"][$name]["data"][$c_object_id][$set_index];
    }
    
    // look for the field

    if (!isset($field_data) || !isset($field_info)) {
      list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($name);
    }
    
    if (!isset($field_data)) {
      // get the data from the set
      $ignore = $this->set($set_name, $object, false, $fetch);
      
      if (isset($this->data[$site_id][$type]["fields"][$name]["info"][$c_object_id])) {
        $field_info = $this->data[$site_id][$type]["fields"][$name]["info"][$c_object_id];
      }
    
      if (isset($this->data[$site_id][$type]["fields"][$name]["data"][$c_object_id][$set_index])) {
        $field_data = $this->data[$site_id][$type]["fields"][$name]["data"][$c_object_id][$set_index];
      }
    
    } 


    return array("data" => &$field_data, "info" => &$field_info);
    
  }
  

  
  
  protected function get_post_relations($for = array(), $type = "post", $object) {
    
    // build a reverse map of all of the IDs that are related to in related post metadata
    
    if (!isset($this->post_relations[$type])) {
      
      global $wpdb;
      
      $this->post_relations[$type] = array();
      $this->post_relations_fields[$type] = array();
      
      // get a list of all meta keys that are related post fields

      if ($type == "post") {
        $sql = "SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-post')";
      } else if ($type == "user") {
        $sql = "SELECT DISTINCT meta_key FROM $wpdb->usermeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-post')";
      } else if ($type == "term") {
        $sql = "SELECT DISTINCT meta_key FROM {$this->termmeta()} WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-post')";
      }

  
      $fields = $wpdb->get_col($sql);
      
      if (count($fields)) {
        
        // run through the meta fields and remove the field id property
        
        $meta_keys = array(); 

        foreach ($fields as $field) {
          
          list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($field);
          $meta_keys[] = "'".MPFT::meta_key($set_name, $field_name)."'";
          
        }
        
        // now grab all meta values within these keys

        if ($type == "post") {
          $sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key IN (".implode(",", $meta_keys).")";
          $col = "post_id";
        } else if ($type == "user") {
          $sql = "SELECT * FROM $wpdb->usermeta WHERE meta_key IN (".implode(",", $meta_keys).")";
          $col = "user_id";
        } else if ($type == "term") {
          $col = "term_id";
          $sql = "SELECT * FROM {$this->termmeta()} WHERE meta_key IN (".implode(",", $meta_keys).")";
        }
        
        
        $rows = $wpdb->get_results($sql);
        
        // now run through them all, and store the reverse relationships
        
        $pr = array();
        $prf = array();
        
        foreach ($rows as $row) {
          $ids = MPU::db_decode($row->meta_value);
          
          if (!is_array($ids) && $ids != "") {
            $ids = array($ids);
          }

          if (is_array($ids)) {
            foreach ($ids as $id) {
              
              $iid = (int) $id;
              
              if (!isset($pr[$iid])) {
                $pr[$iid] = array();
              }
              
              $pr[$iid][] = (int) $row->{$col};
              
              
              if (!isset($prf[$row->meta_key])) {
                $prf[$row->meta_key] = array();
              }

              if (!isset($prf[$row->meta_key][$iid])) {
                $prf[$row->meta_key][$iid] = array();
              }
              
              $prf[$row->meta_key][$iid][] = (int) $row->{$col};

            } 
          }
          
        } 
        
        // remove dupes
        foreach ($pr as $id => $ids) {
          $this->post_relations[$type][$id] = array_unique($ids);
        }
        
        // remove dupes from the items stored by field
        foreach ($prf as $key => $rel) {
          foreach ($rel as $id => $ids) {
            if (!isset($this->post_relations_fields[$type][$key])) {
              $this->post_relations_fields[$type][$key] = array();
            }
            
            $this->post_relations_fields[$type][$key][$id] = array_unique($ids);
          }
        }

        
      }
      
      
    }
    
    
    if (count($for)) {
      
      // collect the fields
      
      $merge = array();
      
      foreach ($for as $key) {
        
        if (isset($this->post_relations_fields[$type][$key])) {
          
          $id_ids = $this->post_relations_fields[$type][$key];
          
          foreach ($id_ids as $id => $ids) {
            if (!isset($merge[$id])) {
              $merge[$id] = $ids;
            } else {
              $merge[$id] = array_merge($merge[$id], $ids);
            }
          }
          
        }

      }
      
      return $merge;
      
    } else {
      return $this->post_relations[$type];
    }
  
  }
  
  
  
  protected function get_term_relations($for = array(), $type = "post", $object) {
    
    // build a reverse map of all of the IDs that are related to in related term metadata
    
    if (!isset($this->term_relations[$type])) {
      
      global $wpdb;
      
      $this->term_relations[$type] = array();
      $this->term_relations_fwd[$type] = array();
      $this->term_relations_fields[$type] = array();

      // get a list of all meta keys that are related term fields
      
      if ($type == "post") {
        $sql = "SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-term')";
      } else if ($type == "user") {
        $sql = "SELECT DISTINCT meta_key FROM $wpdb->usermeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-term')";
      } else if ($type == "term") {
        $sql = "SELECT DISTINCT meta_key FROM {$this->termmeta()} WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-term')";
      }
    
      $fields = $wpdb->get_col($sql);
      
      if (count($fields)) {
        
        // run through the meta fields and remove the field id property
        
        $meta_keys = array(); 

        foreach ($fields as $field) {
          
          list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($field);
          $meta_keys[] = "'".MPFT::meta_key($set_name, $field_name)."'";
          
        }
        
        // now grab all meta values within these keys

        if ($type == "post") {
          $sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key IN (".implode(",", $meta_keys).")";
          $col = "post_id";
        } else if ($type == "user") {
          $sql = "SELECT * FROM $wpdb->usermeta WHERE meta_key IN (".implode(",", $meta_keys).")";
          $col = "user_id";
        } else if ($type == "term") {
          $col = "term_id";
          $sql = "SELECT * FROM {$this->termmeta()} WHERE meta_key IN (".implode(",", $meta_keys).")";
        }
        
        $rows = $wpdb->get_results($sql);
        
        // now run through them all, and store the reverse relationships
        
        $pr = array();
        $prf = array();

        // also store the forward rel
        $pf = array();
        
        foreach ($rows as $row) {
          
          $ids = MPU::db_decode($row->meta_value);
        
          if (!is_array($ids) && $ids != "") {
            $ids = array($ids);
          }
          
          if (is_array($ids)) {
            foreach ($ids as $id) {
              
              if (!isset($pr[(string)$id])) {
                $pr[(string)$id] = array();
              }

              if (!isset($pf[(int) $row->{$col}])) {
                $pf[(int) $row->{$col}] = array();
              }

              $pr[(string)$id][] = (int) $row->{$col};
              $pf[(int) $row->{$col}][] = (string) $id;
              
              if (!isset($prf[$row->meta_key])) {
                $prf[$row->meta_key] = array();
              }

              if (!isset($prf[$row->meta_key][$id])) {
                $prf[$row->meta_key][$id] = array();
              }
              
              $prf[$row->meta_key][$id][] = (int) $row->{$col};

            } 
          } 
          
          
          
        } 
        
        // remove dupes
        foreach ($pr as $id => $ids) {
          $this->term_relations[$type][$id] = array_unique($ids);
        }
        
        // remove dupes
        foreach ($pf as $id => $ids) {
          $this->term_relations_fwd[$type][$id] = array_unique($ids);
        }

        
        // remove dupes from the items stored by field
        foreach ($prf as $key => $rel) {
          foreach ($rel as $id => $ids) {
            if (!isset($this->term_relations_fields[$type][$key])) {
              $this->term_relations_fields[$type][$key] = array();
            }
            
            $this->term_relations_fields[$type][$key][$id] = array_unique($ids);
          }
        }
        
      } // count($fields)
      
      
    } // isset
    
    if (count($for)) {
      
      // collect the fields
      
      $to_merge = array();
      
      foreach ($for as $key) {
        if (isset($this->term_relations_fields[$type][$key])) {
          $to_merge = $this->term_relations_fields[$type][$key];
        }
      }
      
      return array_merge_recursive($to_merge);
      
    } else {
      
      return $this->term_relations[$type];
    }    
  }
  
  
  protected function get_user_relations($for = array(), $type = "post", $object) {
    
    // build a reverse map of all of the IDs that are related to in related user metadata
    
    if (!isset($this->user_relations[$type])) {
      
      global $wpdb;
      
      $this->user_relations[$type] = array();
      $this->user_relations_fields[$type] = array();
      
      // get a list of all meta keys that are related user fields
      
      if ($type == "post") {
        $sql = "SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-user')";
      } else if ($type == "user") {
        $sql = "SELECT DISTINCT meta_key FROM $wpdb->usermeta WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-user')";
      } else if ($type == "term") {
        $sql = "SELECT DISTINCT meta_key FROM {$this->termmeta()} WHERE meta_key LIKE '%:field_id' AND meta_value IN (SELECT id FROM ".MPU::table("fields")." WHERE `type` = 'related-user')";
      }
            
      $fields = $wpdb->get_col($sql);
      
      if (count($fields)) {
        
        // run through the meta fields and remove the field id property
        
        $meta_keys = array(); 

        foreach ($fields as $field) {
          
          list($set_name, $field_name, $prop_name) = MPFT::parse_meta_key($field);
          $meta_keys[] = "'".MPFT::meta_key($set_name, $field_name)."'";
          
        }
        
        // now grab all meta values within these keys

        if ($type == "post") {
          $sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key IN (".implode(",", $meta_keys).")";
          $col = "post_id";
        } else if ($type == "user") {
          $sql = "SELECT * FROM $wpdb->usermeta WHERE meta_key IN (".implode(",", $meta_keys).")";
          $col = "user_id";
        } else if ($type == "term") {
          $col = "term_id";
          $sql = "SELECT * FROM {$this->termmeta()} WHERE meta_key IN (".implode(",", $meta_keys).")";
        }
                
        $rows = $wpdb->get_results($sql);
        
        // now run through them all, and store the reverse relationships
        
        $pr = array();
        $prf = array();
        
        foreach ($rows as $row) {
          $ids = MPU::db_decode($row->meta_value);
          
          if (!is_array($ids) && $ids != "") {
            $ids = array($ids);
          }

          if (is_array($ids)) {
            foreach ($ids as $id) {
              if (!isset($pr[$id])) {
                $pr[(int)$id] = array();
              }

              $pr[(int)$id][] = (int) $row->{$col};
            } 
            
            
            if (!isset($prf[$row->meta_key])) {
              $prf[$row->meta_key] = array();
            }

            if (!isset($prf[$row->meta_key][$iid])) {
              $prf[$row->meta_key][$iid] = array();
            }
            
            $prf[$row->meta_key][$iid][] = (int) $row->{$col};
            
          }
          
        } 
        
        // remove dupes
        foreach ($pr as $id => $ids) {
          $this->user_relations[$id] = array_unique($ids);
        }
        
        // remove dupes from the items stored by field
        foreach ($prf as $key => $rel) {
          foreach ($rel as $id => $ids) {
            if (!isset($this->user_relations_fields[$type][$key])) {
              $this->user_relations_fields[$type][$key] = array();
            }
            
            $this->user_relations_fields[$type][$key][$id] = array_unique($ids);
          }
        }
        
        
      }
      
      
    }
    
    if (count($for)) {
      
      // collect the fields
      
      $to_merge = array();
      
      foreach ($for as $key) {
        if (isset($this->user_relations_fields[$type][$key])) {
          $to_merge = $this->user_relations_fields[$type][$key];
        }
      }
      
      return array_merge_recursive($to_merge);
      
    } else {
      return $this->user_relations[$type];
    }
    
  }
  
  
  public function incoming($object, $fields = array(), $incoming_type = "post") {

    $object_id = $object->id();

    
    // get the type of "this" object
    $type = $this->type_key($object);
    
    if ($type == "post") {
      
      $rel = $this->get_post_relations($fields, $incoming_type, $object);
      
      if (isset($rel[(string)$object_id])) {
        $ids = $rel[(string)$object_id];
      }
    
    } else if ($type == "term") {
      
      $rel = $this->get_term_relations($fields, $incoming_type, $object);

      if (isset($rel[$object->taxonomy->name().":".$object_id])) {
        $ids = $rel[$object->taxonomy->name().":".$object_id];
      }
      
    } else if ($type == "user") {
      $rel = $this->get_user_relations($fields, $incoming_type, $object);
      
      if (isset($rel[(string)$object_id])) {
        $ids = $rel[(string)$object_id];
      }
    
    }

    if (isset($ids)) {
      return $ids;
    }

    return array();
  }
  
  public function outgoing_terms($object, $fields = array()) {
    $object_id = $object->id();

    $dontcare = $this->get_term_relations($fields, "post", $object);

    if (isset($this->term_relations_fwd[$object_id])) {
      return $this->term_relations_fwd[$object_id];
    }
  }
  
  public function load_value($value, $field) {
    if ($type_class = MPFT::type_class($field->type)) {
      return MPU::db_decode( call_user_func_array( array($type_class, "value_from_load"), array($value, $field)) );
    }
  
    return MPU::db_decode($value);
  }
  
  
  public function load_prop($name, $value, $field) {

    if ($type_class = MPFT::type_class($field->type)) {
      return MPU::db_decode( call_user_func_array( array($type_class, "prop_from_load"), array($name, $value, $field)) );
    }
  
    return MPU::db_decode($value);
  }
  
  
  
  protected function assigned_select_list() {
    return "
      s.visibility as set_visibility, s.capabilities as set_capabilities, s.versions as set_versions, s.icon as set_icon, s.position as set_position, s.name as set_name, s.singular_name as set_singular_name, s.id as set_id, s.disabled as set_disabled, s.allow_multiple as set_allow_multiple, s.labels as set_labels, s.expanded as set_expanded, s.sidebar as set_sidebar, s.type as set_type, 
      f.visibility as field_visibility, f.capabilities as field_capabilities, f.icon as field_icon, f.position as field_position, f.name as field_name, f.id as field_id, f.labels as field_labels, f.summary_options as field_summary_options, f.disabled as field_disabled, f.required as field_required, f.type as field_type, f.type_options as field_type_options
    ";
  
  }
  
  
  public function template_field_sets($template) {
     
    global $wf, $wpdb;
     
    $args = $template;
    
    $ret = $this->property_cache_args("template_field_sets", $args );
    
    if (is_null($ret)) {
      
      $field_sets_table = MPM::table("field-sets");
      $fields_table = MPM::table("fields");

      $select_list = $this->assigned_select_list();
      
      $sql = "
        SELECT 
        $select_list
        FROM $field_sets_table s
        INNER JOIN $fields_table f ON s.id = f.`field_set_id`
        WHERE s.type = 't'
        AND ".MPM::visibility_rlike("templates", $template, "s.")."  
        AND f.disabled = 0 AND s.disabled = 0 
        ORDER BY set_type, set_position, field_position;
        ";
        
      
      $results = $wpdb->get_results($sql);
    
      $ret = $this->property_cache_args("template_field_sets", $args, $results );

    }
    
    return $ret;
  }

  public function post_type_field_types($post_type_name, $template_name = "") {
    
    $args = array($post_type_name, $template_name);
    
    if (is_null($ret = $this->property_cache_args("post_type_field_types", $args ))) {
      
      $sets = $this->post_type_field_sets($post_type_name, $template_name);
      
      $types = array();
      
      foreach ($sets as $set) {
        foreach ($set->fields as $field) {
          $types[] = $field->type;
        }
      }  
      
      $ret = $this->property_cache_args("post_type_field_types", $args, $types );
    }
  
    return $ret;
  }
  
  public function taxonomy_field_types($taxonomy_name) {
    
    $args = array($taxonomy_name);

    if (is_null($ret = $this->property_cache_args("taxonomy_field_types", $args ))) {
      
      $sets = $this->taxonomy_field_sets($taxonomy_name);
      
      $types = array();
      
      foreach ($sets as $set) {
        foreach ($set->fields as $field) {
          $types[] = $field->type;
        }
      }  
      
      $ret = $this->property_cache_args("taxonomy_field_types", $args, $types );
    }
  
    return $ret;
  }
  
  public function site_field_types() {
    
    $args = array("site");

    if (is_null($ret = $this->property_cache_args("site_field_types", $args ))) {
      
      $sets = $this->site_field_sets();
      
      $types = array();
      
      foreach ($sets as $set) {
        foreach ($set->fields as $field) {
          $types[] = $field->type;
        }
      }  
      
      $ret = $this->property_cache_args("site_field_types", $args, $types );
    }
  
    return $ret;
  }
  
  public function role_field_types($role_name) {
    
    $args = array($role_name);

    if (is_null($ret = $this->property_cache_args("role_field_types", $args ))) {
      
      $sets = $this->role_field_sets($role_name);
      
      $types = array();
      
      foreach ($sets as $set) {
        foreach ($set->fields as $field) {
          $types[] = $field->type;
        }
      }  
      
      $ret = $this->property_cache_args("role_field_types", $args, $types );
    }
  
    return $ret;
  }
  
  // gets all of the assigned field sets 
  // this method uses the minimal amount of SQL queries to get the sets and fields within, for performance
  
  protected function set_data_from_result($result) {
    return array(
      "position" => $result->set_position,
      "name" => $result->set_name,
      "singular_name" => $result->set_singular_name,
      "id" => $result->set_id,
      "disabled" => $result->set_disabled,
      "allow_multiple" => $result->set_allow_multiple,
      "labels" => $result->set_labels,
      "icon" => $result->set_icon,
      "expanded" => $result->set_expanded,
      "visibility" => $result->set_visibility,
      "capabilities" => $result->set_capabilities,
      "sidebar" => $result->set_sidebar,
      "versions" => $result->set_versions,
      "type" => $result->set_type
    );
  }
  
  protected function field_data_from_result($result) {
    return array(
      "field_set_id" => $result->set_id,
      "position" => $result->field_position,
      "name" => $result->field_name,
      "id" => $result->field_id,
      "type" => $result->field_type,
      "type_options" => $result->field_type_options,
      "summary_options" => $result->field_summary_options,
      "visibility" => $result->field_visibility,
      "icon" => $result->field_icon,
      "disabled" => $result->field_disabled,
      "capabilities" => $result->field_capabilities,
      "required" => $result->field_required,
      "labels" => $result->field_labels
    );
            
  }
  
  public function taxonomy_field_sets($taxonomy_name, $orderby = "position ASC") {
    
    global $wf;
    
    $args = array($taxonomy_name, $orderby);
    
    if (is_null($ret = $this->property_cache_args("taxonomy_field_sets", $args ))) {
    
      $this->field_types = array();
      
      global $wpdb;
  
      $field_sets_table = MPM::table("field-sets");
      $fields_table = MPM::table("fields");
  
      $select_list = $this->assigned_select_list();
  
      $sql = "
        SELECT 
        $select_list
        FROM $field_sets_table s
        INNER JOIN $fields_table f ON s.id = f.`field_set_id`
        WHERE  (s.type = 'x' OR s.type = 's') 
        AND ".MPM::visibility_rlike("taxonomies", $taxonomy_name, "s.")."  
        AND ".MPM::visibility_rlike("taxonomies", $taxonomy_name, "f.")."  
        AND f.disabled = 0 AND s.disabled = 0
        ORDER BY set_type, set_position, field_position;
      ";
      
      $field_sets = array();
      $fields_by_set = array();
  
      $results = $wpdb->get_results($sql);
  
      foreach ($results as $result) {
    
        $set_name = $result->set_name;
        $set_type = $result->set_type;
        $use_set = true;
        $use_field = true;
    
        if (isset($field_sets[$set_name])) {
      
          if ($set_type == $field_sets[$set_name]["type"]) {
            $use_set = false; // we already have this set recorded
          }
          
          if ($set_type == 's' && $field_sets[$set_name]["type"] == 'x') {
            // the existing set is a taxonomy type field set, which holds greater specificity than this shared set, so don't use this set or field
            $use_set = false;
            $use_field = false;
          }
          
          
        }
    
        if ($use_set) {
          
          $field_sets[$set_name] = $this->set_data_from_result($result);
        } 
    
        // now record the field too
    
        if ($use_field) {
          
          if (!in_array($result->field_type, $this->field_types)) {
            $this->field_types[] = $result->field_type; 
          }
          
          $fields_by_set[$set_name][$result->field_name] = $this->field_data_from_result($result);
        }
      
      } // endforeach
    
      
        
      $field_set_models = array();
      
      foreach ($field_sets as $field_set) {
        if ($field_set["type"] == "s") {
          $field_set_model = new MPM_SharedFieldSet();
        } else {
          $field_set_model = new MPM_TaxonomyFieldSet();
        }

        $field_set_model->set_from_row($field_set);
        
        $field_models = array();
        
        $fields = $fields_by_set[$field_set["name"]];
        
        if (is_array($fields) && count($fields)) {

          foreach ($fields as $field) {
            $field_model = new MPM_Field();
            $field_model->set_from_row($field);
            
            if ($field_model->in_current_site()) {
              $field_models[$field["name"]] = $field_model;
            }
          
          } 
        
          $field_set_model->set_fields($field_models);

        }
      
        if ($field_set_model->in_current_site()) {
          $field_set_models[$field_set_model->name] = $field_set_model;
        } 
        
      }
     
      
      $ret = $this->property_cache_args("taxonomy_field_sets", $args, $field_set_models );
    
      
    } 
    

    return $ret;
  }
  
  public function role_field_sets($role_name, $orderby = "position ASC") {
    
    global $wf;
    
    $args = array($role_name, $orderby);
    
    if (is_null($ret = $this->property_cache_args("role_field_sets", $args ))) {
    
      $this->field_types = array();
      
      global $wpdb;
  
      $field_sets_table = MPM::table("field-sets");
      $fields_table = MPM::table("fields");
  
      $select_list = $this->assigned_select_list();
  
      $sql = "
        SELECT 
        $select_list
        FROM $field_sets_table s
        INNER JOIN $fields_table f ON s.id = f.`field_set_id`
        WHERE  (s.type = 'r' OR s.type = 's') 
        AND ".MPM::visibility_rlike("roles", $role_name, "s.")."  
        AND ".MPM::visibility_rlike("roles", $role_name, "f.")."  
        AND f.disabled = 0 AND s.disabled = 0
        ORDER BY set_type, set_position, field_position;
      ";
      
      $field_sets = array();
      $fields_by_set = array();
  
      $results = $wpdb->get_results($sql);
  
      foreach ($results as $result) {
    
        $set_name = $result->set_name;
        $set_type = $result->set_type;
        $use_set = true;
        $use_field = true;
    
        if (isset($field_sets[$set_name])) {
      
          if ($set_type == $field_sets[$set_name]["type"]) {
            $use_set = false; // we already have this set recorded
          }
          
          if ($set_type == 's' && $field_sets[$set_name]["type"] == 'r') {
            // the existing set is a role type field set, which holds greater specificity than this shared set, so don't use this set or field
            $use_set = false;
            $use_field = false;
          }
          
          
        }
    
        if ($use_set) {
          
          $field_sets[$set_name] = $this->set_data_from_result($result);
        } 
    
        // now record the field too
    
        if ($use_field) {
          
          if (!in_array($result->field_type, $this->field_types)) {
            $this->field_types[] = $result->field_type; 
          }
          
          $fields_by_set[$set_name][$result->field_name] = $this->field_data_from_result($result);
        }
      
      } // endforeach
    
      
        
      $field_set_models = array();
      
      foreach ($field_sets as $field_set) {
        if ($field_set["type"] == "s") {
          $field_set_model = new MPM_SharedFieldSet();
        } else {
          $field_set_model = new MPM_RoleFieldSet();
        }

        $field_set_model->set_from_row($field_set);
        
        $field_models = array();
        
        $fields = $fields_by_set[$field_set["name"]];
        
        if (is_array($fields) && count($fields)) {

          foreach ($fields as $field) {
            $field_model = new MPM_Field();
            $field_model->set_from_row($field);
            
            if ($field_model->in_current_site()) {
              $field_models[$field["name"]] = $field_model;
            }
          
          } 
        
          $field_set_model->set_fields($field_models);

        }
      
        if ($field_set_model->in_current_site()) {
          $field_set_models[$field_set_model->name] = $field_set_model;
        } 
        
      }
     
      
      $ret = $this->property_cache_args("role_field_sets", $args, $field_set_models );
    
      
    } 
    

    return $ret;
  }
  
  
  public function site_field_sets($orderby = "position ASC") {
    
    global $wf;
    
    $args = array($orderby);
    
    if (is_null($ret = $this->property_cache_args("site_field_sets", $args ))) {
    
      $this->field_types = array();
      
      global $wpdb;
  
      $field_sets_table = MPM::table("field-sets");
      $fields_table = MPM::table("fields");
  
      $select_list = $this->assigned_select_list();
  
      $sql = "
        SELECT 
        $select_list
        FROM $field_sets_table s
        INNER JOIN $fields_table f ON s.id = f.`field_set_id`
        WHERE  (s.type = 'w') 
        AND f.disabled = 0 AND s.disabled = 0
        ORDER BY set_type, set_position, field_position;
      ";
      
      $field_sets = array();
      $fields_by_set = array();
  
      $results = $wpdb->get_results($sql);
  
      foreach ($results as $result) {
    
        $set_name = $result->set_name;
        $set_type = $result->set_type;
        $use_set = true;
        $use_field = true;
    
        if ($use_set) {
          $field_sets[$set_name] = $this->set_data_from_result($result);
        } 
    
        // now record the field too
    
        if ($use_field) {
          
          if (!in_array($result->field_type, $this->field_types)) {
            $this->field_types[] = $result->field_type; 
          }
          
          $fields_by_set[$set_name][$result->field_name] = $this->field_data_from_result($result);
        }
      
      } // endforeach
    
      
        
      $field_set_models = array();
      
      foreach ($field_sets as $field_set) {
        $field_set_model = new MPM_SiteFieldSet();

        $field_set_model->set_from_row($field_set);
        
        $field_models = array();
        
        $fields = $fields_by_set[$field_set["name"]];
        
        if (is_array($fields) && count($fields)) {

          foreach ($fields as $field) {
            $field_model = new MPM_Field();
            $field_model->set_from_row($field);
            
            if ($field_model->in_current_site()) {
              $field_models[$field["name"]] = $field_model;
            }
          
          } 
        
          $field_set_model->set_fields($field_models);

        }
      
        if ($field_set_model->in_current_site()) {
          $field_set_models[$field_set_model->name] = $field_set_model;
        } 
        
      }
     
      
      $ret = $this->property_cache_args("site_field_sets", $args, $field_set_models );
    
      
    } 
    

    return $ret;
  }
  
  
  public function post_type_field_sets($post_type_name, $template_name = "", $orderby = "position ASC") {

    global $wf;
    
    if (is_null($template_name)) {
      $template_name = "";
    }
    
    $args = array($post_type_name, $template_name, $orderby);
    
    if (is_null($ret = $this->property_cache_args("post_type_field_sets", $args ))) {
    
      $this->field_types = array();
      
      global $wpdb;
  
      $field_sets_table = MPM::table("field-sets");
      $fields_table = MPM::table("fields");
  
      $select_list = $this->assigned_select_list();
  
      $sql = "
        SELECT 
        $select_list
        FROM $field_sets_table s
        INNER JOIN $fields_table f ON s.id = f.`field_set_id`
        WHERE  (s.type = 'p' OR s.type = 's') 
        AND ".MPM::visibility_rlike("post_types", $post_type_name, "s.")."  
        AND ".MPM::visibility_rlike("post_types", $post_type_name, "f.");
        
      if ($template_name != "") {
        // filter by template too
        $sql .= "
          AND ".MPM::visibility_rlike("templates", $template_name, "s.")."  
          AND ".MPM::visibility_rlike("templates", $template_name, "f.");
      }  
      
      $sql .= " 
        AND f.disabled = 0 AND s.disabled = 0
        ORDER BY set_type, set_position, field_position;
      ";
      
      $field_sets = array();
      $fields_by_set = array();
  
      $results = $wpdb->get_results($sql);
    
      if ($template_name != "") {
        
        $template_results = $this->template_field_sets($template_name);

        if ($template_results && count($template_results)) {
          $results = array_merge($results, $template_results);
        }
        
      }
      
      foreach ($results as $result) {
    
        $set_name = $result->set_name;
        $set_type = $result->set_type;
        $use_set = true;
        $use_field = true;
    
        if (isset($field_sets[$set_name])) {
      
          if ($set_type == $field_sets[$set_name]["type"]) {
            $use_set = false; // we already have this set recorded
          }
      
          if ($set_type == 't' && in_array($field_sets[$set_name]["type"], array('s','p'))) {
            // clear any existing fields already set
            $fields_by_set[$set_name] = null;
          }
          
          if ($set_type == 's' && $field_sets[$set_name]["type"] == 'p') {
            // the existing set is a post type field set, which holds greater specificity than this shared set, so don't use this set or field
            $use_set = false;
            $use_field = false;
          }
          
          if (in_array($set_type, array('p','s')) && $field_sets[$set_name]["type"] == 't') {
            // the existing set is a template field set, which holds greater specificity than this shared or post type set, so don't use this set's info
            $use_set = false;
            $use_field = false;
          }
          
          
          
        }
    
        if ($use_set) {
          
          $field_sets[$set_name] = $this->set_data_from_result($result);
        } 
    
        // now record the field too
    
        if ($use_field) {
          
          if (!in_array($result->field_type, $this->field_types)) {
            $this->field_types[] = $result->field_type; 
          }
          
          $fields_by_set[$set_name][$result->field_name] = $this->field_data_from_result($result);
        }
      
      } // endforeach
    
      
        
      $field_set_models = array();
      
      foreach ($field_sets as $field_set) {

        if ($field_set["type"] == "s") {
          $field_set_model = new MPM_SharedFieldSet();
        } else {
          $field_set_model = new MPM_FieldSet();
        }

        $field_set_model->set_from_row($field_set);
        
        $field_models = array();
        
        $fields = $fields_by_set[$field_set["name"]];
        
        if (is_array($fields) && count($fields)) {

          foreach ($fields as $field) {
            $field_model = new MPM_Field();
            $field_model->set_from_row($field);
            
            if ($field_model->in_current_site()) {
              $field_models[$field["name"]] = $field_model;
            }
          
          } 
        
          $field_set_model->set_fields($field_models);

        }
      
        if ($field_set_model->in_current_site()) {
          $field_set_models[$field_set_model->name] = $field_set_model;
        } 
        
      }
     
      
      $ret = $this->property_cache_args("post_type_field_sets", $args, $field_set_models );
      
    } 
    

    return $ret;
  }
  
  
  // -- Data migration functions, for events such as field and field set name changes ---- 
  
  
  protected function migrate_field_set_meta_process($field_set, $name_original) {
    
    global $wpdb, $wf;

    // find all field data associated with this field
      
    $post_ids = array();
    $term_ids = array();
    $user_ids = array();
    $site_ids = array();

    foreach ($field_set->fields() as $field) {
      // get all of the keys and post ids that apply to this field
      $sql = "SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
      $post_ids = array_merge($post_ids, $wpdb->get_col($sql));

      $sql = "SELECT DISTINCT term_id FROM {$this->termmeta()} WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
      $term_ids = array_merge($term_ids, $wpdb->get_col($sql));

      $sql = "SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
      $user_ids = array_merge($user_ids, $wpdb->get_col($sql));

      $sql = "SELECT DISTINCT site_id FROM $this->sitemeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
      $site_ids = array_merge($site_ids, $wpdb->get_col($sql));
    }

    $post_ids = array_unique($post_ids);
    $term_ids = array_unique($term_ids);
    $user_ids = array_unique($user_ids);
    $site_ids = array_unique($site_ids);

    if (count($post_ids)) {
      $sql = "UPDATE $wpdb->postmeta SET meta_key = REPLACE(`meta_key`, '".$name_original.".', '".$field_set->name.".') WHERE meta_key RLIKE '^".esc_sql($name_original)."\.' AND post_id IN (".implode(",", $post_ids).")";
      $wpdb->query($sql);
    }

    if (count($term_ids)) {
      $sql = "UPDATE {$this->termmeta()} SET meta_key = REPLACE(`meta_key`, '".$name_original.".', '".$field_set->name.".') WHERE meta_key RLIKE '^".esc_sql($name_original)."\.' AND term_id IN (".implode(",", $term_ids).")";
      $wpdb->query($sql);
    }

    if (count($user_ids)) {
      $sql = "UPDATE $wpdb->usermeta SET meta_key = REPLACE(`meta_key`, '".$name_original.".', '".$field_set->name.".') WHERE meta_key RLIKE '^".esc_sql($name_original)."\.' AND user_id IN (".implode(",", $user_ids).")";
      $wpdb->query($sql);
    }

    if (count($site_ids)) {
      $sql = "UPDATE $this->sitemeta SET meta_key = REPLACE(`meta_key`, '".$name_original.".', '".$field_set->name.".') WHERE meta_key RLIKE '^".esc_sql($name_original)."\.' AND site_id IN (".implode(",", $site_ids).")";
      $wpdb->query($sql);
    }
    
  }
  
  
  protected function migrate_field_meta_process($field, $name_original) {
    
    global $wpdb, $wf;

    $field_set = $field->field_set();
    $set_name = esc_sql($field_set->name);
    
    // we need to move the meta data across from one key to another
      
    // find all field data associated with this field
    
    // get all of the keys and post ids that apply to this field
    $sql = "SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
    $post_ids = $wpdb->get_col($sql);

    $sql = "SELECT DISTINCT term_id FROM {$this->termmeta()} WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
    $term_ids = $wpdb->get_col($sql);

    $sql = "SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
    $user_ids = $wpdb->get_col($sql);

    $sql = "SELECT DISTINCT site_id FROM $this->sitemeta WHERE meta_key LIKE '%:field_id' AND meta_value = '".$field->id."'";
    $site_ids = $wpdb->get_col($sql);
    
    
    if (count($post_ids)) {
      $sql = "UPDATE $wpdb->postmeta SET meta_key = REPLACE(`meta_key`, '.".$name_original."', '.".$field->name."') WHERE meta_key RLIKE '".$set_name."\.".esc_sql($name_original)."($|:)' AND post_id IN (".implode(",", $post_ids).")";
      $wpdb->query($sql);
    }

    if (count($term_ids)) {
      $sql = "UPDATE {$this->termmeta()} SET meta_key = REPLACE(`meta_key`, '.".$name_original."', '.".$field->name."') WHERE meta_key RLIKE '".$set_name."\.".esc_sql($name_original)."($|:)' AND term_id IN (".implode(",", $term_ids).")";
      $wpdb->query($sql);
    }

    if (count($user_ids)) {
      $sql = "UPDATE $wpdb->usermeta SET meta_key = REPLACE(`meta_key`, '.".$name_original."', '.".$field->name."') WHERE meta_key RLIKE '".$set_name."\.".esc_sql($name_original)."($|:)' AND user_id IN (".implode(",", $user_ids).")";
      $wpdb->query($sql);
    }

    if (count($site_ids)) {
      $sql = "UPDATE $this->sitemeta SET meta_key = REPLACE(`meta_key`, '.".$name_original."', '.".$field->name."') WHERE meta_key RLIKE '".$set_name."\.".esc_sql($name_original)."($|:)' AND site_id IN (".implode(",", $site_ids).")";
      $wpdb->query($sql);
    }
    
  }
     
  public function migrate_field_set_meta($field_set, $name_original) {
    
    global $wf;
    
    if (isset($name_original) && $name_original != $field_set->name) {
      $wf->multisite_call( array($this, "migrate_field_set_meta_process"), array($field_set, $name_original) );
    }
    
  }  
  
  public function migrate_field_meta($field, $name_original) {
    
    global $wf;
    
    if (isset($name_original) && $name_original != $field->name) {
      $wf->multisite_call( array($this, "migrate_field_meta_process"), array($field, $name_original) );
    }
    
  }  
  
  
  protected function migrate_post_type_process($post_type, $name_original) {
    global $wpdb, $wf;
    
    // update the posts table
    
    // update the object_type field in taxonomies table
    
    // update any keys in visibility columns (fields and field sets)
    
    // update any keys in type options columns
    
    
    
  }

  protected function migrate_taxonomy_process($tax, $name_original) {
    global $wpdb, $wf;
    
    // update the term_taxonomy table

    // update the mp_termmeta table
    
    // update any keys in visibility columns (fields and field sets)
    
    // update any keys in type options columns
    
    // update any metadata containing the taxonomyname:id tuples
    
    
    
  }
  
  public function migrate_taxonomy($tax, $name_original) {
    
    global $wf;
    
    if (isset($name_original) && $name_original != $tax->name) {
      $wf->multisite_call( array($this, "migrate_taxonomy_process"), array($tax, $name_original) );
    }
    
  }

  public function migrate_post_type($post_type, $name_original) {
    
    global $wf;
    
    if (isset($name_original) && $name_original != $post_type->name) {
      $wf->multisite_call( array($this, "migrate_post_type_process"), array($post_type, $name_original) );
    }
    
  }
  
  
  
}
