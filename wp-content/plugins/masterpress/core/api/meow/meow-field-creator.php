<?php

class MEOW_FieldCreator extends MEOW_Field {
  
  public $creator = true;
  
  function __construct($name, $object, $info) {
   
    $this->name = $name; // a full name (set.field)
    $this->set_index = 0;
    $this->object = $object;
    $this->info = $info;
    
    if ($this->exists()) {

      if ($type_class = MPFT::type_class($this->info->type)) {
        // instantiate the delegate
        $this->type_delegate = new $type_class($this->info, $this);
      } else {
        $this->type_delegate = new MPFT($this->info, $this);
      }
    
    }
    
  }
  
}