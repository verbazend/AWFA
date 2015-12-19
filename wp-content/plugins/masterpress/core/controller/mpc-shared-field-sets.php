<?php

MPC::incl("field-sets");

class MPC_SharedFieldSets extends MPC_FieldSets {
  
  public $caps = array(
    "create"  => "create_shared_field_sets",
    "edit"    => "edit_shared_field_sets",
    "delete"  => "delete_shared_field_sets"
  );


  public function __construct() {
    parent::__construct("s", "MPM_SharedFieldSet");
  }

  public function manage_help() {

    return array( 
      
      MPV::overview_tab( 
        __("<em>Shared</em> Field Sets define the grouping and types of custom content fields available <em>across multiple post types</em> in your site.", MASTERPRESS_DOMAIN ) 
      )

    );

  }
  
  public function create_field() {
    
    if (!MasterPress::current_user_can("create_shared_fields")) {
      wp_die(__("Sorry, you do not have the required capability to create shared fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("edit_shared_fields")) {
      wp_die(__("Sorry, you do not have the required capability to edit shared fields.", MASTERPRESS_DOMAIN));
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

    if (!MasterPress::current_user_can("delete_shared_fields")) {
      wp_die(__("Sorry, you do not have the required capability to delete shared fields.", MASTERPRESS_DOMAIN));
    }
 
    $this->delete( array(
        "cap_verified" => true,
        "model" => "field",
      )
    );
    
  }
  
}

?>