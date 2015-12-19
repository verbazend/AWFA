<?php


class MPM_SharedFieldSet extends MPM_FieldSet {

  public function is_shared() {
    return true;
  }

  public static function table() {
    return MPM::table("field-sets");
  }
  
  public function infer_position() {
  
    /*
    we need to find the appropriate starting sub position for this field set, assuming it comes AFTER other field sets 
    */
  
    global $wpdb;
    $max_pos = $wpdb->get_var("SELECT MAX(`position`) FROM `".MPM::table("field-sets")."` WHERE `type` = 's'");

    if ($max_pos) {
      $this->position = (int) $max_pos + 1;
    } else {
      $this->position = 1;
    }
  
  }

  public function construct_type() {
    return "s";
  }  
  public static function find($args = array()) {
    $defaults = array(
      "where" => "`type` = 's'"
    );
    
    $r = wp_parse_args($args, $defaults);
    
    return parent::find($r);  
  }


  public static function find_by_name($name) {
    return self::find( array( "where" => "`name` = '{$name}' AND `type` = 's'" ) ); 
  }



  public function validate_name($op) {
    if ($this->name != "") {
      // check there isn't already a field set with this name 
      
      $extra_check = "";
      
      if ($op != "insert") {
        $extra_check = " AND id <> ".$this->id." ";
      }
      
      $result = self::find( array("where" => "`type` = 's' AND `name` = '{$this->name}'". $extra_check ) );

      if ($result) {
        $this->err(sprintf(__("Sorry, a shared field set named <em>%s</em> already exists", MASTERPRESS_DOMAIN), $this->name), "name");
      }

    }
  }
    
  
  public function tbl() {
    return MPU::table("field_sets");
  } 

  
  // static extension methods, only needed because we can't use PHP 5.3... sob

  public static function find_by_id($id) {
    return MPM::find_by_id(self::k(__CLASS__), $id, MPU::table("field_sets"));
  }

  public static function delete_by_id($id) {
    return MPM::delete_by_id(self::k(__CLASS__), $id, MPU::table("field_sets"));
  }

  public static function find_by($field, $value, $format = "%s") {
    return MPM::find_by(self::k(__CLASS__), $field, $value, $format, MPU::table("field_sets"));
  }
  
  public static function find_by_in($field, $values, $format = "%s") {
    return MPM::find_by_in(self::k(__CLASS__), $field, $values, $format, MPU::table("field_sets"));
  }
  
  public static function find_by_id_in($values) {
    return MPM::find_by_id_in(self::k(__CLASS__), $values, MPU::table("field_sets"));
  }
  

}

?>