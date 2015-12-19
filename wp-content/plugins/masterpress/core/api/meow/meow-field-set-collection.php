<?php

/* 
  MEOW_FieldSetCollection: A representation of a set supporting multiple items
*/

class MEOW_FieldSetCollection extends WOOF_Collection {

  public $creator = false;
   
  protected $name;
  protected $object;
  protected $info;
  protected $_blank = false;
  
  protected $dirty = false;
  protected $updated = false;
  
  protected $_base = 1;
  
  public static $sort_field;
  public static $sort_values;
  public static $sort_property;
  
  
  function __construct($name, $object, &$data = null) {

    global $meow_provider;
    $this->name = $name;
    $this->object = $object;
    
    // build the items collection
    
    if (is_null($data)) {
      // fetch the set
      $set = $meow_provider->set($name, $this->object);
    } else {
      $set = $data;
    }
    
    // data structure


    // store the info
    $this->info = $set["info"];
    
    if ($this->info) {
      // the set is merely used here to ascertain an appropriate count. 
      // the (cached) data is accessed later, when requesting field values 
       
       
      if ($set["count"]) {

        for ($i = 1; $i <= $set["count"]; $i++) {
          // build a new set shell, with appropriate set index
          $this->_items[$i] = new MEOW_FieldSet($name, $i, $this->object, $this->info, $this, $data);
        }

      } else {
        
        $this->_blank = true; 
        
        if ($this->info->allow_multiple) {
          $this->_items = array();
        } else {
          $this->_items = array( 1 =>  new MEOW_FieldSet($name, 1, $this->object, $this->info, $this, $data) );
        }
        
      }
      
    } else {

      $this->_items = array(); //array( 1 =>  new MEOW_FieldSet($name, 1, $this->object, $this->info, $this, $data) );
    }
    
  }
    
  public function fields() {
    $fields = array();
    
    foreach ($this->info->fields() as $field) {
      $fields[] = $this->field($field->name);
    }
    
    return new WOOF_Collection($fields);
  }


  function versions() {
    
    global $wpdb, $meow_provider;
    
    $versions = array();
    
    if ($this->object) {
      $sql = "SELECT v.*, u.* FROM `".MPU::site_table("versions")."` v LEFT OUTER JOIN $wpdb->users u ON u.ID = v.user_id WHERE v.object_id = ".$this->object->id()." AND v.field_set_name = '".$this->name."' AND v.object_type = '".$meow_provider->type_key($this->object)."' ORDER BY version_id DESC";
      $versions = $wpdb->get_results($sql);
    }
    
    return $versions;
  }
  
  function insert($values = array()) {
    return $this->create($values, true);
  }
  
  function debug_data() {
    
    $data = array();
    
    $fields = $this->fields();
       
    foreach ($this as $set_item) {

      $set_item_data = $set_item->debug_data();
    
      if ($this->info->allow_multiple) {
        $data[] = $set_item_data;
      } else {
        $data = $set_item_data;
      }
    
    }
    
    return $data;
    
  }
  
  function create($values = array(), $update = false) {
    if ($this->info->allow_multiple) {
      $next = $this->count() + 1;
      $set = new MEOW_FieldSet($this->name, $next, $this->object, $this->info, $this);
      $this->_items[$next] = $set;
      $this->mark_dirty();
    } else {
      $set = $this->first();
    }
    
    if (count($values)) {
      
      $val = wp_parse_args($values, array());
        
      foreach ($val as $key => $value) {
        $set->field($key)->set_value($value);
      }
      
    }
    
    if ($update) {
      // save data immediately
      $set->update();
    }
    
    return $set;
  }
  
  function remove_all() {
    $this->_items = array();
    $this->mark_dirty();
  }
  
  function remove($index) {
    if (isset($this->_items[$index])) {
      unset($this->_items[$index]);
      $this->mark_dirty();
    }
  }
  
  function mark_dirty() {
    $this->_blank = !count($this->_items);
    $this->dirty = true;
  }

  function mark_updated($val = true) {
    $this->updated = $val;
  }
  
  function is_updated() {
    return $this->updated;
  }

  function is_dirty() {
    return $this->dirty;
  }
  
  function __toString() {
    return "";
  }
  
  function update() {
    
    if ($this->dirty && $this->exists()) {
      
      $this->dirty = false; // do this now in case of any SQL errors
      $this->updated = true;
      
      global $wpdb;
      global $wf, $meow_provider;
  
      $value_type = null;
      
      $object_type = $meow_provider->type_key($this->object);
   
      $object_id = $this->object->id();
      
      if ($object_type == "term") {
        $tax = $this->object->taxonomy->name();
        
        if ($tax && !is_woof_silent($tax)) {
          $value_type = $tax;
        }
      }

      /* Delete the old post meta */
  
			$this->object->switch_site();
			
      $set_name = $this->name;
  
      foreach ( $this->info->fields as $field) {
        
        if (!$field->disabled && $field->in_current_site()) {
         
          $meta_name = $set_name.".".$field->name;

          MPC_Meta::delete_object_meta($object_type, $object_id, $meta_name);
  
          // now go through the properties too

          $dont_care = MPFT::type_class($field->type);
    
          foreach (MPFT::type_properties($field->type) as $prop) {
            MPC_Meta::delete_object_meta($object_type, $object_id, $meta_name.":".$prop);
          }
        
        }
      
      }
  
      $model_id_prop_stored = array();
  
    
      // Create the new values
    
      foreach( $this->_items as $set_index => $set_item ) {
      
        foreach ($this->info->fields as $field) {
          
          if (!$field->disabled && $field->in_current_site()) {

            // here the field type should prepare the value, if necessary
    
            // grab the type
    
            $model_id = $field->id;
    
            $model = $field;
    
            $field_item = $this->field($field->name, $set_index);
      
            if ($field_item->exists()) {
              
              $value = $field_item->raw();
              
              $val = MPU::db_encode($value);
    
              if ($type_class = MPFT::type_class($model->type)) {
                $val = MPU::db_encode( call_user_func_array( array($type_class, "value_for_save"), array($value, $model)) );
              }
              
  
              // create the post meta
    
              $meta_name = MPFT::meta_key($set_name, $field->name);
    
              //echo "would add $object_type, $object_id, $meta_name, $val<br>";

              
              if (! (!$this->info->allow_multiple && $val == "") ) {
                // don't record blank entries for non-multiple field sets, as this is wasteful
              
                MPC_Meta::add_object_meta($object_type, $object_id, "{$meta_name}", $val, $value_type);

                // now store the properties
    
                foreach (MPFT::type_properties($model->type) as $prop) {

                  if ($prop == "field_id" && !isset($model_id_prop_stored[$model_id])) {
                    $model_id_prop_stored[$model_id] = true;
          
                    MPC_Meta::add_object_meta($object_type, $object_id, "{$meta_name}:{$prop}", $model_id, $value_type);
        
                  } else {

                    $prop_value = $field_item->prop_val($prop);
        
                    if (!isset($prop_value)) {
                      $prop_value = "";
                    }
                    
                    if ($prop != "field_id") {
                      MPC_Meta::add_object_meta($object_type, $object_id, "{$meta_name}:{$prop}", $prop_value, $value_type);
                    }
                  
                  } // $prop = field_id

                } // foreach MPFT::type_properties
            
              } // if (!allow_multiple)
            
            } // if (field_item->exists)
          
          } // if (!field->disabled)
        
    
        } // endforeach $fields
    
    
      } // endforeach ($this->items)

      $this->object->restore_site();

    } // if ($dirty)
    
    
  }
  
  function count() {
    
    if ($this->_blank) {
      return 0;
    } 

    return count($this->_items);
  }
  
  function sort($field, $order = "ASC") {
    $sorted_items = $this->_items;

    self::$sort_field = $field;
    
    if ($order == "ASC") {
      usort( $sorted_items, create_function('$a,$b', 'return strcmp($a->f(MEOW_FieldSetCollection::$sort_field), $b->f(MEOW_FieldSetCollection::$sort_field));'));
    } else {
      usort( $sorted_items, create_function('$a,$b', 'return strcmp($b->f(MEOW_FieldSetCollection::$sort_field), $a->f(MEOW_FieldSetCollection::$sort_field));'));
    }
    
    return new MEOW_VirtualFieldSetCollection($sorted_items);
  }

  function range($from, $to = null) {
    if ($to == null) {
      $to = $this->count();
    }
    
    $slice = array_slice($this->_items, $from - 1, $to - ($from - 1));
    
    return new MEOW_VirtualFieldSetCollection($slice);
  }
      
  function sort_to($field, $values, $order = "ASC") {
    $sorted_items = $this->_items;

    self::$sort_field = $field;
    self::$sort_values = $values;
    
    if ($order == "ASC") {
      usort( $sorted_items, create_function('$a,$b', 'return array_search($a->f(MEOW_FieldSetCollection::$sort_field), MEOW_FieldSetCollection::$sort_values) > array_search($b->f(MEOW_FieldSetCollection::$sort_field), MEOW_FieldSetCollection::$sort_values);'));
    } else {
      usort( $sorted_items, create_function('$a,$b', 'return array_search($b->f(MEOW_FieldSetCollection::$sort_field), MEOW_FieldSetCollection::$sort_values) < array_search($a->f(MEOW_FieldSetCollection::$sort_field), MEOW_FieldSetCollection::$sort_values);'));
    }
    
    
    return new MEOW_VirtualFieldSetCollection($sorted_items);
  }
  
  function psort($field, $property, $order = "ASC") {
    $sorted_items = $this->_items;

    self::$sort_field = $field;
    self::$sort_property = $property;
    
    if ($order == "ASC") {
      usort( $sorted_items, create_function('$a,$b', '$prop = MEOW_FieldSetCollection::$sort_property; return strcmp($a->f(MEOW_FieldSetCollection::$sort_field)->$prop(), $b->f(MEOW_FieldSetCollection::$sort_field)->$prop());'));
    } else {
      usort( $sorted_items, create_function('$a,$b', '$prop = MEOW_FieldSetCollection::$sort_property return strcmp($b->f(MEOW_FieldSetCollection::$sort_field)->$prop(), $a->f(MEOW_FieldSetCollection::$sort_field)->$prop());'));
    }
    
    return new MEOW_VirtualFieldSetCollection($sorted_items);
  }
  
  function filter($args, $case_sensitive = FALSE) {
    $filtered_items = array();
    
    if (!is_array($args)) {
      $args = explode("&", $args);
    }
    
    // extract the arguments
    
    foreach ($args as $arg) {

      $m = array();
    
      preg_match ( "/^([\w\-]+)(\#?\=|\#?[\>\<\$\^\*\~\!]=|\#?\>|\#?\<)(.*)$/" , $arg, $m );
      
      if ($m && count($m) == 4) {
        $fn = $m[1];
        $op = $m[2];
        
        if ($case_sensitive) {
          $val = $m[3];
        } else {
          $val = strtolower($m[3]);
        }

        foreach ($this->_items as $set_item) {
          
          $f = $set_item->f($fn);
          
          $fields = array();


          $fields[] = $f;
          
          $include = false;

          foreach ($fields as $f) {
            
            if (!$include) {
              
              $nv = 0;
              $nval = 0;
            
              if ($case_sensitive) {
                $fv = $f->raw();
              } else {
                $fv = strtolower($f->raw());
              }
            
              if (is_numeric($fv)) {
                $nv = (float) $fv;
              }
            
              if (is_numeric($val)) {
                $nval = (float) $val;
              }
            
              switch ($op) {
                case ">" : 
                  $include = $fv > $val;
                  break;
                case "#>" :
                  $include = $nv > $nval;
                  break;
                case "<" :
                  $include = $fv < $val;
                  break;
                case "#<" :
                  $include = $nv < $nval;
                  break;
                case ">=" :
                  $include = $fv >= $val;
                  break;
                case "#>=" :
                  $include = $nv >= $nval;
                  break;
                case "<=" :
                  $include = $fv <= $val;
                  break;
                case "#<=" :
                  $include = $nv <= $nval;
                  break;
                case "*=" :
                  $include = ( strpos($fv, $val) !== FALSE );
                  break;
                case "~=" :
                  $include = preg_match("/(\s|^)".$val."(\s|$)/", $fv );
                  break;
                case "!=" :
                  $include = $val != $fv;
                  break;
                case "#!=" :
                  $include = $nval != $nv;
                  break;
                case "^=" :
                  $include = ( $val == substr($fv, 0, strlen($val)) ); 
                  break;
                case "$=" :
                  $include = ( $val == substr($fv, 0, -strlen($val)) ); 
                  break;
                case "#=" :
                  $include = ( $nval == $nv ); 
                  break;
                default: // assume = 
                  $include = ( $val == $fv );
              }
            
            } // endif $include
            
          } // endforeach
          
          if ($include) {
            $filtered_items[] = $set_item;
          }
          
        }
    
      }
      
    }
    return new MEOW_VirtualFieldSetCollection($filtered_items);
     
  }
  
  function group_by($name) {
    
    $abt = array();
    $ret = array();
    
    foreach ($this->_items as $gi) {
      $fv = $gi->f($name)->raw();
      $abt[$fv][] = $gi;
    }
    
    foreach ($abt as $key => $arr) {
      $ret[$key] = new MEOW_VirtualFieldSetCollection($arr);
    }

    return $ret;
  }
  

  
    
  // within a set collection, the field function correctly assumes you
  // would want a field from the first set

  function field($name, $set_index = 1) {
    $item = $this->items($set_index);

    if (isset($item) && $item) {
      return $item->field($name);
    }
  
    return new WOOF_Silent( sprintf( __("No field named %s at index %s", MASTERPRESS_DOMAIN ), $name, $set_index ) );
  }

  function info() {
    return $this->info;
  }

  function exists() {
    if ($this->info) {
      return $this;
    }
    
    return null;
  }

  function blank($name = "") {
    if ($this->_blank) {
      return TRUE;
    }
    
    return !count($this->_items);
  }
  
  function has($name) {
    $field = $this->field($name);

    if (!$field->exists() || $field->blank())
      return FALSE;

    return $field;

  }
  
  function is($name) {
    if (!$this->field($name)->exists())
      return FALSE;

    return $this->field($name)->is();
  }
  
  

  // iterator methods - allows foreach over a set directly!

  function rewind() {
    $this->init_items();
    $this->iterator_valid = (FALSE !== reset($this->_items)); 
  }


  function valid() {
    if ($this->blank()) {
      return false;
    }

    return $this->iterator_valid;
  }
   
  // synonym, for lazy programmers
  function f($name, $set_index = 1) {
    return $this->field($name, $set_index);
  }
  
  public function __call($name, $arguments) {
    return new WOOF_Silent( sprintf( __( "no method or property named %s", MASTERPRESS_DOMAIN ), $name ) );
  }
  
  public function __get($name) {

    // we'll prioritise a property get to look for a field here - too many potential collisions otherwise

    $field = $this->field($name);
    
    if ($field->exists()) {
      return $field;
    } 
    
    return $this->get($name);

  }
  
  public function __set($name, $value) {

    $field = $this->field($name);
      
    if ($field->exists()) {
      $field->set_value($value);
      $this->dirty = true;
    }
    
  }

  
}

