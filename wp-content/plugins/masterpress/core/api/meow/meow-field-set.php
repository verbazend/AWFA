<?php

/* 
  MEOW_FieldSet: A single field set item
*/


class MEOW_FieldSet extends WOOF_Collection {

  public $creator = false;

  protected $dirty = false;

  protected $name;
  protected $info;
  protected $set_index;
  protected $item;
  protected $object;
  protected $field_set_collection;
  protected $fields = array();
  protected $data;
  
  
  
  function __construct($name, $set_index = 1, $object, $info, $field_set_collection, &$data = null) {
    $this->name = $name;
    $this->set_index = $set_index;
    $this->object = $object;
    $this->item = array($this); // for single value iteration
    $this->info = $info;
    $this->field_set_collection = $field_set_collection;
    $this->data = $data;
  }
  
  public function offsetExists($offset) {
    return $this->has($offset);
  }

  public function offsetGet($offset) {
    return $this->field($offset);
  }
  
  function field_set_collection() {
    return $this->field_set_collection;
  }

  public function update() {
    if ($coll = $this->field_set_collection()) {
      $coll->update();
      $this->dirty = false;
    }
  }

  function remove() {
    if ($coll = $this->field_set_collection()) {
      $coll->remove($this->set_index);
    }
  }

  public function is_editable( ) {
    $can = $this->info->current_user_can_edit();
    return $can;
  }

  public function fields() {
    $fields = array();
    
    foreach ($this->info->fields() as $field) {
      $fields[] = $this->field($field->name);
    }
    
    return new WOOF_Collection($fields);
  }
  
  public function debug_data() {
  
    $data = array();
 
    $fields = $this->fields();
 
    foreach ($fields as $field) {
      $field_name = $field->info->name;
      $data[$field_name] = $field->debug_value();
    }
  
    return $data;

  }
  
  function field($name) {
    
    if (!is_woof_silent($name)) {
      
      if (!isset($this->fields[$name])) {

        $data = null;
        
        if (isset($this->data["data"][$this->set_index][$name])) {
          $data = $this->data["data"][$this->set_index][$name];
        }
        
        if ($this->exists()) {
          
          $field = new MEOW_Field($this->name.".".$name, $this->set_index, $this->object, $this, $data);
          $this->fields[$name] = $field;
          
        } else {
          // return a silent object for silent failure
          return new WOOF_Silent( sprintf( __( "No field named %s", MASTERPRESS_DOMAIN ), $name ) );
        }
      
      }
    
      if (isset($this->fields[$name])) {
        return $this->fields[$name];
      }
    
    }
  
    return new WOOF_Silent( sprintf( __( "No field named %s", MASTERPRESS_DOMAIN ), $name ) );
  }
  
  function count() {
    if (!$this->exists() || $this->blank()) {
      return 0;
    }

    return 1;
  }


  function blank($name = "") {

    if ($this->exists()) {
      
      $blank = true;
      
      foreach ($this->info->fields as $key => $info) {
        $blank = $blank && $this->field($key)->blank();
      }
      
      return $blank;
    }
    
    return true;
    
  }

  function index() {
    return $this->set_index;
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

  function exists() {
    if ($this->info) {
      return $this;
    }
    
    return null;
  }

  function info($info = NULL) {
    if ($info) {
      $this->info = $info;
    } else {
      return $this->info;
    }
  }

  function f($name) {
    return $this->field($name);
  }

  
  public function mark_dirty() {
    $this->dirty = true;
    $coll = $this->field_set_collection();
    
    if ($coll) {
      $coll->mark_dirty();
    }
  }
  
  public function __set($name, $value) {

    $field = $this->field($name);
      
    if ($field->exists()) {
      $field->set_value($value);
      $this->mark_dirty();
    }
    
  }

  public function __get($name) {
    // overload get to return a field of that name (with default params only)
    
    if ($name == "debug" || $name == "huh") {
      return $this->debug();
    }
    
    return $this->field($name);
  }

  public function __call($name, $arguments) {
    // silence the call
    
    return new WOOF_Silent(__("No method named $name", MASTERPRESS_DOMAIN));
  }
  
  
}

