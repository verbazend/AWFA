<?php


class MPM_Template extends MPM {

  public static function table() {
    return MPM::table("templates");
  }
  
  public static function where_format() {
    return "%s";
  }
  
  public function linked_to_post_type($post_type) {
    return $this->visible_in("post_types", $post_type->name);
  }

  public function post_types() {
    $sql = "SELECT * FROM `".MPM::table("post-types")."` ".$this->visibility_clause("post_types", "name", true);
    return MPM::get_models("post-type", $sql);
  }

  public function field_sets() {
    return MPM_TemplateFieldSet::find_by_template($this->id, "id ASC");
  }

  public function rep() {
    
    $rep = $this->data() + array();
    
    $rep["field_sets"] = array();
    
    foreach ($this->field_sets("id asc") as $field_set) {
      $rep["field_sets"][] = $field_set->rep();
    } 
    
    return $rep;
    
  }
    
  public static function find_by_id($id) {
    
    global $wpdb;
    
    $model = MPM::get_model("template", "SELECT * FROM `".self::table()."` WHERE id = '{$id}'");
    
    if (!$model) {
      
      // this model is a little different - there's no notion of "creating" a template via MasterPress
      // (as you create one by creating a new file)
      
      // this model is simply a bucket to store information associated with the template, so we just auto-create one if it doesn't exist
      
      // get the current value of the page "supports" value
      
      // $page = MPM_PostType::find_by_name("page");
      
      $wpdb->insert( self::table(), array("id" => $id, "supports" => "*", "visibility" => 'json:{"sites":"*","post_types":"page"}' ) );
      
      $model = MPM::get_model("template", "SELECT * FROM `".self::table()."` WHERE id = '{$id}'");
      
    }
    
    
    return $model;
    
  }
  

  
  // static extension methods, only needed because we can't use PHP 5.3... sob

  public static function find_by_id_in($values) {
    return MPM::find_by_id_in(self::k(__CLASS__), $values, MPU::table("field_sets"));
  }
  

}

?>