<?php

// a special class used by MasterPress when creating Field Set UIs

class MEOW_FieldSetCreator extends MEOW_FieldSet {
  
  public $creator = true;
  
  function __construct($name, $object, $info) {
    $this->name = $name;
    $this->set_index = 0;
    $this->object = $object;
    $this->info = $info;
  }

  function field($name) {
    return new MEOW_FieldCreator($this->name.".".$name, $this->object, $this->info);
  }
  
  function blank($name = NULL) {
    return true;
  }
  
  
}
