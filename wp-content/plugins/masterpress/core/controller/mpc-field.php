<?php

class MPC_Field extends MPC {

  
  public function options() {
    
    // construct the HTML for the options UI
    
    $id = "";
    $type = "";
    
    if (isset($_REQUEST["type"])) {
      $type = $_REQUEST["type"];
    }
  
    if (isset($_GET["id"])) {
      $id = $_GET["id"];
    }
  
    // if the id is present, load a model
    
    if ($id && $id != "") {
      MPM::incl("field");
      $model = MPM_Field::find_by_id($id);
    }
      
    if ($type != "") {

      if ($type_class = MPFT::type_class($type)) {
        $type_options = array();
        
        if (isset($model) && isset($model->type_options)) {
          $type_options = $model->type_options;
        }
        
        $form = call_user_func_array( array($type_class, "options_form"), array(MPM::array_stripslashes($type_options)) );
      }
      
      $css_url = "";
      
      $css_file = MPU::type_file_path($type, "mpft-$type.options.css");

      if (file_exists($css_file)) {
        $css_url = MPU::type_file_url($type, "mpft-$type.options.css")."?".filemtime($css_file);
      }
      
      $js_file = MPU::type_file_path($type, "mpft-$type.options.js");

      if (file_exists($js_file)) {
        $js_url = MPU::type_file_url($type, "mpft-$type.options.js")."?".filemtime($js_file);
      }
    
      $ret = array( "form" => $form, "css_file" => $css_url, "type" => $type );
      
      if (isset($js_url)) {
        $ret["js_file"] = $js_url;
      }

      self::ajax_success( $ret );
      
    } else {
      self::ajax_error( __("field type options could not be loaded", MASTERPRESS_DOMAIN) );
    }
    
  }
  
  
  

}
