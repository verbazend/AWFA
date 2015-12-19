<?php

class MEOW_Site extends WOOF_Site { 

	public function set($name) {
    global $wf;
    return $wf->set($name, $this);
  }
  
  public function field($name, $set_index = 1) {
    global $wf;
    return $wf->field($name, $set_index, $this);
  }
  
  public function s($name) {
    return $this->set($name);
  }
  
  public function f($name, $set_index = 1) {
    return $this->field($name, $set_index);
  }
  
  public function blank() {
    return $this->id == -1;
  }
  
  public function has_field($name) {
    $field = $this->field($name);
    
    if (!$field->exists() || $field->blank()) {
      return false;
    }
    
    return $field;
  }
  
  public function has_set($name) {
    $set = $this->set($name);

    if (!$set->exists() || $set->blank()) {
      return false;
    }
    
    return $set;
  }
  
  public function has($name) {

    if ($set = $this->has_set($name)) {
      return $set;
    } else {
      if ($field = $this->has_field($name)) {
        return $field;
      }
    } 
    
    return false;
  }
  
  public function __get($name) {
    
    if (method_exists($this, $name)) {
      return $this->{$name}();
    } else if (isset($this->item->{$name})) {
      return $this->item->{$name};
    } 
    
    $set = $this->set($name);
    
    if (is_woof_silent($set)) {
      return parent::__get($name);
    } else {
      return $set;
    }
    
  }
  
}
