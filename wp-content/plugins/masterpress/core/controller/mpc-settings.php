<?php

class MPC_Settings extends MPC {

  public function init() {
    MasterPress::enqueue_codemirror();
  }

  public function manage() { 
    
    global $wf;
    
    if ($this->is_postback()) {

      update_site_option("mp_licence_key", $_POST["licence_key"]);
      
      
      // save the capabilities
      
      $mp_cap = $_POST["mp_cap"];
      
      update_site_option("mp_cap", $mp_cap );
      
      if ($mp_cap == "specific" && isset($_POST["mp_cap_grant"])) {
        // grant all of the masterpress capabilities to all roles with manage_options
        
        foreach ($wf->roles() as $role) {
          
          if ($role->can("manage_options")) {
            
            foreach (MasterPress::$capabilities as $cap) {
              $role->add_cap($cap);
            }
            
          } 
        }


      }
      
      // needs a redirect, as role changes may cause menus to become available or drop away
      wp_redirect( admin_url("options-general.php?page=masterpress-settings&updated=1") );
    }
    
    /*
    if ($_GET["updated"]) {
      MPV::notify(__("Settings Updated", MASTERPRESS_DOMAIN));
    }
    */
    
    $this->setup_view( array(
        "title_args" => array( "text" => __("MasterPress Settings", MASTERPRESS_DOMAIN) )
      )
    );
    
    
  } // manage
  
  public static function valid_key_format($key) {
    return strlen(trim($key)) >= 33;
  }
  
  public static function cache_key($key) {
    return "mp_valid_".$key;
  }

  public static function uncache_licence_key($key) {
    global $wf;
    $wf->uncache(self::cache_key($key));
  }
  
  public static function do_validate_licence_key($key) {
    
    global $wf;
    
    // Please do not modify this code. 
    // Purchase a licence and help us support future development of this software. Thanks!

    $info = array();
    
    $current_key = MasterPress::licence_key();
    $cache_key = MPC_Settings::cache_key($current_key);

    // clear the current transient

    $wf->uncache($cache_key);
    
    if (self::valid_key_format($key)) {
      
      $args = array(
        "action" => "licence-check",
        "key" => $key,
        "domain" => $_SERVER['SERVER_NAME']
      );

      // Send the request to check for an update
    
      $response = MasterPress::update_request( $args );
      
      $result = $response->result;

      $info["error"] = $response->error;
      $info["valid"] = $result["valid"];
      $info["reason"] = $result["reason"];

      $wf->cache($cache_key, $info["valid"] ? "yes" : "no", "3d");
      
    } else {

      $info["error"] = __("Invalid licence key format", MASTERPRESS_DOMAIN);
      $info["valid"] = false;
      $info["reason"] = __("must be at least 33 characters long");
      
      $wf->cache($cache_key, $info["valid"] ? "yes" : "no", "3d");

    }
    
    return $info;
    
  }
  
  
  public function validate_licence_key() {
    
    $info = self::do_validate_licence_key($_GET["key"]);
    
    self::ajax_success( $info );
    exit;
    
  }
  

}

?>