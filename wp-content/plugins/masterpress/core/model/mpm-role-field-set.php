<?php


class MPM_RoleFieldSet extends MPM_FieldSet {

  public function infer_position() {
  
    /*
    we need to find the appropriate starting sub position for this field set, assuming it comes AFTER other field sets 
    */
  
    global $wpdb;
    $max_pos = $wpdb->get_var("SELECT MAX(`position`) FROM `".MPM::table("field-set")."` WHERE `type` = 'r'");

    if ($max_pos) {
      $this->position = (int) $max_pos + 1;
    } else {
      $this->position = 1;
    }
  
  }
  
  public function construct_type() {
    return "r";
  }

  public static function find($args = array()) {
    $defaults = array(
      "where" => "`type` = 'r'"
    );
    
    $r = wp_parse_args($args, $defaults);
    
    return parent::find($r);  
  }


  public static function find_by_name($name) {
    return self::find( array( "where" => "`name` = '{$name}' AND `type` = 'r'" ) ); 
  }

  public static function find_by_role($role, $orderby = "position ASC") {
    return self::find( array( "where" => MPM::visibility_rlike("roles", $role)." AND `type` = 'r'", "orderby" => $orderby ) ); 
  }

  public static function find_by_name_and_role($name, $template) {
    return self::find( array( "where" => "`name` = '{$name}' AND ".MPM::visibility_rlike("roles", $role)." AND `type` = 'r'", "orderby" => $orderby ) ); 
  }

  
  
  public function validate_name() {
    $class = get_class($this);
    
    // check there isn't already a template field set with this name attached to this template 
    $result = call_user_func_array( array($class, "find_by_name_and_role"), array($this->name, $this->visibility["roles"]));

    if ($result && count($result)) {

      if ( ( $op == "insert" ) || ( $result[0]->id != $this->id ) ) {
        $this->err(sprintf(__('Sorry, a role field set named <em>%s</em> already exists for the role <span class="tt">%s</span>. Please enter a unique name', MASTERPRESS_DOMAIN), $this->name, $this->visibility["roles"]), "name");
      }
    }
      
    // we'll now allow shared field sets of the same name, noting that the specificity will be like CSS, where Template Field Sets are more specific than Post Type Field Sets, which are more specific than SHared Field Sets
      
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