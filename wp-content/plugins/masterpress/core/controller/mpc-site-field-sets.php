<?php

MPC::incl("field-sets");

class MPC_SiteFieldSets extends MPC_FieldSets {
  
  public $caps = array(
    "create"  => "create_site_field_sets",
    "edit"    => "edit_site_field_sets",
    "delete"  => "delete_site_field_sets"
  );


  public function __construct() {
    parent::__construct("w", "MPM_SiteFieldSet");
  }
  
  public function init() {
    MasterPress::$view->is_site_set = true;
    parent::init();
  }

  public function manage_help() {

    return array( 
      
      MPV::overview_tab( 
        __("<em>Site</em> Field Sets define the grouping and types of custom content fields available <em>site-wide</em>, in a <em>Site Content</em> admin menu above your post types.", MASTERPRESS_DOMAIN ) 
      )

    );

  }
  
  public function create_field() {
    
    if (!MasterPress::current_user_can("create_site_fields")) {
      wp_die(__("Sorry, you do not have the required capability to create site fields.", MASTERPRESS_DOMAIN));
    }
    
    $this->create( array(
        "cap_verified" => true,
        "view" => "fields",
        "model" => "field",
        "title_args" => array( "info_panel" => true )
      )
    );
    
    
  }
  
  public function edit_field() {

    if (!MasterPress::current_user_can("edit_site_fields")) {
      wp_die(__("Sorry, you do not have the required capability to edit site fields.", MASTERPRESS_DOMAIN));
    }
    
     $this->edit( array(
        "cap_verified" => true,
        "view" => "fields",
        "model" => "field",
        "title_args" => array( "info_panel" => true )
      )
    );
    
  }
  
  
  public function delete_field() {

    if (!MasterPress::current_user_can("delete_site_fields")) {
      wp_die(__("Sorry, you do not have the required capability to delete site fields.", MASTERPRESS_DOMAIN));
    }
 
    $this->delete( array(
        "cap_verified" => true,
        "model" => "field",
      )
    );
    
  }
  
}

?>