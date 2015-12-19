<?php

class MPC_SiteContent extends MPC {

  
  public static function inline_head() {
    global $wf;
    $site = $wf->site();
    MPV_Meta::inline_head(self::assigned_field_sets(), $site);
  }

  public static function assigned_field_types() {
    global $meow_provider;
    return $meow_provider->site_field_types();
  }

  public static function assigned_field_sets() {
    global $meow_provider;
    return $meow_provider->site_field_sets();
  }
  

  public function manage() { 

    global $meow_provider, $wf;
    
    $this->setup_view( array(
        "title_args" => array( "text" => __("Site Content", MASTERPRESS_DOMAIN) )
      )
    );

    
    $site = $wf->site();
    
    MasterPress::$view->sets = self::assigned_field_sets();
    
    MPC::incl("meta");
    MPV::incl("meta");
    
    if (MPC::is_postback()) {
      // save the meta - this code should be moved into the controller at some stage
      MPC_Meta::save_site_meta($site->id());
      // redirect back to here
      wp_redirect( MasterPress::admin_url( "site-content", "manage", array("updated" => "true"), false ) );
      exit();
    }
    
  } 

}

?>