<?php


/* 
  MEOW_Field: A single field value from a field collection
*/

class MEOW_Field extends WOOF_Collection {
  
  public $creator = false;

  protected $dirty = false;

  protected $type_delegate;
  private $name;
  private $set_index;
  private $object;
  public $info;
  public $data;
  protected $field_set;
  
  
  
  public $_version_preview = false; 
  
  function __construct($name, $set_index, $object, $field_set, &$data = null) {
   
    global $meow_provider;
    
    
    $this->data = new stdClass();
    
    $this->name = $name; // a full name (set.field)
    $this->set_index = $set_index;
    $this->object = $object;
    
    $this->field_set = $field_set;
    
    if (is_null($data)) { 
      // grab the field data, info etc
      $field = $meow_provider->field($this->name, $this->set_index, $this->object);
    } else {
      $field = $data;
    }
  

    // store the info
    $this->info = $field["info"];
    
    if ($this->exists()) {
      $this->data = $field["data"];
    } else {
      $this->data = (object) array( "val" => "", "__blank" => true );
    }

    // now, we need to instantiate the correct MasterPress Field Type class based on the field's type
    
    
    if ($this->exists()) {

      if ($type_class = MPFT::type_class($this->info->type)) {
        // instantiate the delegate
        $this->type_delegate = new $type_class($this->info, $this);
      } else {
        $this->type_delegate = new MPFT($this->info, $this);
      }
    
    }
    
  }
  
  public function object() {
    return $this->object;
  }
  
  public function mark_dirty() {
    $this->dirty = true;
    $set = $this->field_set();
    
    if (is_object($set)) {
      $set->mark_dirty();
    }
  
  }
  
  public function field_name() {
    return $this->name;
  }
  
  public function update() {
    if ($coll = $this->field_set_collection()) {
      $coll->update();
      $this->dirty = false;
    }
  }
  
  public function debug_data() {
    return $this->debug_value();
  }
  
  public function debug_value() {
    if (isset($this->type_delegate)) {
      return $this->type_delegate->debug_value();
    }
    
    return "";
  }
    
  public function field_set() {
    return $this->field_set;
  }

  public function field_set_collection() {
    if ($set = $this->field_set) {
      return $set->field_set_collection();
    }
    
  }
  
  public function offsetExists($offset) {

    if (is_numeric($offset) && $this->type_delegate && method_exists($this->type_delegate, "offsetExists")) {
      return $this->type_delegate->offsetExists($offset);
    }

    return $this->__get($offset) != "";
  }

  public function offsetGet($offset) {
    
    if (is_numeric($offset) && $this->type_delegate && method_exists($this->type_delegate, "offsetGet")) {
      return $this->type_delegate->offsetGet($offset);
    }
    
    return $this->__get($offset);
  }
  
  public function strval() {
    return $this->__toString();
  }
  
  public function __toString() {
    if ($this->exists()) {

      if ($this->type_delegate) {
        if (MasterPress::$context == "col") {
          $val = $this->type_delegate->col();

          if ($val) {
						
						if (is_woof_silent($val)) {
							return $val->error;
						}
						
            return $val;
          }
          
          return "";
          
        } else if (MasterPress::$context == "json") { 
          
          if ($this->blank()) {
            return null;
          }
          
          return $this->type_delegate->json();

        } else {
          return $this->type_delegate->__toString();
        }
      } else {
        return $this->value();
      }

    }

    return "";
  }
  
  public function is_editable( ) {
    $set = $this->field_set();
    
    if ($this->_version_preview) {
      return false; // makes version previews READ ONLY always 
    }
    
    if ($set) {
      return $this->info->current_user_can_edit( $this->field_set()->is_editable() );
    }
    
    return $this->info->current_user_can_edit();
  }
  
  public function __call($name, $arguments) {
    // delegate the call to the type delegate

    if ($this->type_delegate && !$this->type_delegate->valid()) {
      return new WOOF_Silent( __("field value is blank or invalid", MASTERPRESS_DOMAIN ) );
    }
    
    if (method_exists($this->type_delegate, $name)) {
      return call_user_func_array (array($this->type_delegate, $name), $arguments); 
    } 
    
    // next, check for a property
    
    $prop = $this->prop($name);
    
    if (isset($prop)) {
      return $prop;
    }

    if ($name == "or") { // allow a fallback call
      
      if ($this->blank() && count($arguments)) {
        return $arguments[0];
      } else {
        return $this->value();
      }
    }

    // finally, try to call the "call" method on the type delegate, which forwards the call on to ITS delegate

    if ($this->type_delegate) {
      return call_user_func_array (array($this->type_delegate, "call"), array($name, $arguments));
    }
    
    return new WOOF_Silent( sprintf( __( "no method or property could be found for %s", MASTERPRESS_DOMAIN ), $name ) );
  }

  public function __set($name, $value) {
    $this->prop($name, $value);
    $this->mark_dirty();
  }
  
  public function __get($name) {
    
    // first check if this field has a data property named $name

    $prop = $this->prop($name);
    
    if (isset($prop)) {
      return $prop;
    }
    
    // delegate the property access to the type delegate
    
    if ($this->type_delegate) {
      
      if (!$this->type_delegate->valid()) {
        return new WOOF_Silent( __("invalid resource", MASTERPRESS_DOMAIN ) );
      }
      
      // next try to call "forward" on the delegate
      // this is a special function to allow "related" objects to forward the call on
      
      $value = $this->type_delegate->forward($name);

      if (!is_woof_silent($value)) {
        return $value;
      }
    
      $value = $this->type_delegate->get($name);
      
      
      if (!is_woof_silent($value)) {
        return $value;
      }
    }
      
    return new WOOF_Silent( sprintf( __( "No property named %s", MASTERPRESS_DOMAIN ), $name ) );
  }
  
  function raw() {
    if ($this->exists()) {
      return $this->type_delegate->raw();
    }
    
    return "";
  }
    
  function iterator_items() {
    
    if (isset($this->type_delegate)) {
      if (method_exists($this->type_delegate, "iterator_items")) {
        return $this->type_delegate->iterator_items();
      }
    }
    
    return array($this);
  }
  
  function val() {
    $args = func_get_args();
    return call_user_func_array( array($this, "value"), $args );
  }
 
  function set_value($value) {
    if ($type_class = MPFT::type_class($this->info->type)) {
      // instantiate the delegate
      $this->type_delegate = new $type_class($this->info, $this);
    } else {
      $this->type_delegate = new MPFT($this->info, $this);
    }

    $this->data->val = $this->type_delegate->value_for_set($value);
    $this->type_delegate->change();

    $this->mark_dirty();

    if (!isset($this->data->prop)) {
      $this->data->prop = array();
    }

    $this->data->__blank = false;

    return $value;
  }
  
  function value() {

    if ($this->exists()) {
      
      if ($this->type_delegate) {
        $args = func_get_args();
        return call_user_func_array( array($this->type_delegate, "value"), $args );
      }
      
    }
    
    return "";
  }
  
  function info($info = NULL) {
    if ($info) {
      $this->info = $info;
    } else {
      return $this->info;
    }
  }
  
  function set_index() {
    return $this->set_index;
  }
  
  function data($data = NULL) {
    if ($data) {
      $this->data = $data;

      if ($this->type_delegate) {
        $this->type_delegate->data($data);
      }

    } else {
      return $this->data;
    }
  }

  public function prop_val($name) {
    if (isset($this->data->prop[$name])) {
      return $this->data->prop[$name];
    }
    
    return ""; 
  }
  
  public function prop($name = null, $value = null) {
    
    if (is_null($name)) {
      return $this->data->prop;
    }
    
    if ($this->type_delegate) {

      if (!is_null($value)) {
        
        if (method_exists($this->type_delegate, "set_prop")) {
          $this->type_delegate->set_prop($name, $value);
          
        } else {
          $this->data->prop[$name] = $value;

          if ($type_class = MPFT::type_class($this->info->type)) {
            // re-instantiate the delegate
            $this->type_delegate = new $type_class($this->info, $this);
          } else {
            $this->type_delegate = new MPFT($this->info, $this);
          }
          

        }

        $this->mark_dirty();

        return $this->data->prop[$name];
        
      } else {
      
        if (method_exists($this->type_delegate, "get_prop")) {
          return $this->type_delegate->get_prop($name);
        }
        
        if (isset($this->data->prop[$name])) {
          return $this->data->prop[$name];
        }
      
      }

    } 
    
    if (!is_null($value)) {
      $this->data->prop[$name] = $value;
    }
    
    if (isset($this->data->prop[$name])) {
      return $this->data->prop[$name];
    }
    
    return null;
  }

  public function prop_value($name) {
    if (isset($this->data->prop[$name])) {
      return $this->data->prop[$name];
    }
    
    return "";
  }

  function exists() {
    if ($this->info) {
      return $this;
    }
    
    return null;
  }

  function fallback($default) {
    return $this->or_else($default);
  }

  
  function fb($default) {
    return $this->or_else($default);
  }

  function or_else($default) {
    
    if (!$this->exists() || $this->blank()) {
      return $default;
    }
    
    return $this->__toString();

  }


  function blank() {
    
    if (is_null($this->data)) {
      return true;
    }
    
    if (!$this->exists()) {
      return true;
    }

    if ($this->type_delegate && !$this->type_delegate->valid()) {
      return true;
    }
    
    if ($this->type_delegate) {
      return $this->type_delegate->blank();
    }
    
    return $this->data->__blank;
  }

  function is_empty() {
    return $this->blank();
  }

  function count() {
  
    if (!$this->exists() || $this->blank()) {
      return 0;
    } else {
      
      if ($this->type_delegate && method_exists($this->type_delegate, "count")) {
        return $this->type_delegate->count();
      }
      
    }
    
    return 1;
  }

  
  
}

