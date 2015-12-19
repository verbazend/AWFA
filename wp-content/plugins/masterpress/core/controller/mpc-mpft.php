<?php

// a controller that dispatches into the correct field type class. 
// this is needed so that javascript field type widgets can call ajax methods in the
// current wordpress environment.

class MPC_MPFT extends MPC {
  
  // dispatch the call to the correct field type static method
  public function dispatch() {
    
    $type = str_replace("_", "-", $_REQUEST["type"]);
    $method = $_REQUEST["type_method"];

    if ($ftc = MPFT::type_class($type)) {

      if (method_exists($ftc, $method)) {
        return call_user_func( array($ftc, $method) );
      } else {
        self::ajax_error( sprintf(__("The method '%s' could not be called in the PHP Field Type Class '%s'. Please check that the method exists.", MASTERPRESS_DOMAIN), $method, $ftc) );
      }
      
    } else {
      self::ajax_error( sprintf(__("AJAX method '%s' could not be called as the PHP Field Type Class '%s' could not be found for type key '%s'. Please check that everything is setup correctly in this field type.", MASTERPRESS_DOMAIN), $ftc, $type));
    }
  
  }
  
}
