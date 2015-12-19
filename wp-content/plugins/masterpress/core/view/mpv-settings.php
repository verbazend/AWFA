<?php

class MPV_Settings extends MPV { 
  
  function manage() {
    
    global $wf;
    
    ?>

    <?php MPV::messages() ?>

    <?php MPV::form_open() ?>


    <div id="settings">

    <button id="bt-save-changes-top" type="submit" class="simple-primary"><?php _e("Save Settings", MASTERPRESS_DOMAIN) ?></button>
    
    <div class="fs fs-licence fs-with-tabs">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="certificate"></i><strong><?php _e("Licence", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("your registration info", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
        
      <div class="fsc">
      <div class="fscb">
        
        <div class="f">
          <label for="licence_key" class="icon"><i class="key"></i><?php _e("Key", MASTERPRESS_DOMAIN)?>:</label>
          <div class="fw">
            <input id="licence_key" name="licence_key" type="text" value="<?php echo get_site_option("mp_licence_key") ?>" class="text mono" />
            
            <div id="licence-progress"></div>
            
            <?php
            
            // check the transient
            
            $current_key = MasterPress::licence_key();
            
            $cache_key = MPC_Settings::cache_key($current_key);
            
            $info = array("valid" => false, "reason" => "");
            
            $valid = $wf->cache($cache_key);
              
            if ($valid != "yes") {

              // if it's not valid, let's check again
              $info = MPC_Settings::do_validate_licence_key($current_key);

            } else {
              $info["valid"] = true;
            }
            
            $valid_style = ( $current_key && $info["valid"] ) ? 'display: block;" ' : 'display: none;';
            $invalid_style = ( $current_key != "" && !$info["valid"] ) ? 'display: block;" ' : 'display: none;';
            $empty_style = ( $current_key == "" ) ? 'display: block;" ' : 'display: none;';
            
            if ($info["reason"] != "") {
              $info["reason"] = " - ".$info["reason"];
            }
            
            ?>
            
            <p id="licence-empty" class="note" style="<?php echo $empty_style ?>">
              <?php _e("A valid licence key is required for access to automatic updates and support.", MASTERPRESS_DOMAIN); ?>
            </p>

            <p id="licence-valid" class="note" style="<?php echo $valid_style ?>">
              <i class="tick-circle"></i>
              <?php _e("Your licence key is valid", MASTERPRESS_DOMAIN); ?>
            </p>

            <p id="licence-invalid" class="note" style="<?php echo $invalid_style ?>">
              <i class="error-circle"></i>
              <?php _e("Key not valid", MASTERPRESS_DOMAIN); ?>
              <span class="reason"><?php echo $info["reason"] ?></span>
            </p>
            
          </div>
        </div>
        <!-- /.f -->
    
        
      </div>
      </div>
    
    </div>
    <!-- /.fs -->


    

    <div class="fs fs-capabilities fs-with-tabs">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="masterpress"></i><strong><?php _e("MasterPress Capabilities", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("the keys used to control access to features of MasterPress", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
        
      <div class="fsc">
      <div class="fscb">
        
    
        <?php $cap = get_site_option("mp_cap", "standard") ?>
        
          <div class="fw fwl">
            <input id="mp_cap_standard" name="mp_cap" value="standard" <?php echo WOOF_HTML::checked_attr( $cap == "standard" ) ?> type="radio" class="radio" />
            <label for="mp_cap_standard" class="radio"><?php _e('<em>Standard</em> - all features are allowed for user roles with the <span class="tt">manage_options</span> capability (typical plug-in setup)', MASTERPRESS_DOMAIN) ?></label>
          </div>
          <!-- /.fw -->

          <div class="fw fwl">
            <input id="mp_cap_specific" name="mp_cap" value="specific" <?php echo WOOF_HTML::checked_attr( $cap == "specific" ) ?> type="radio" class="radio" />
            <label for="mp_cap_specific" class="radio"><?php _e('<em>Specific</em> - allows fine-grained control over MasterPress', MASTERPRESS_DOMAIN) ?></label>

            <?php
            
            $style = "";
            
            if ($cap == "standard") {
              $style = ' style="display: none;"';
            }
            
            ?>
            
            <div id="specific-keys" <?php echo $style ?> class="eg">
              <p><strong><?php _e("Section Access", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">manage_masterplan, manage_post_types, manage_taxonomies, manage_templates, manage_user_roles, manage_site_field_sets, manage_shared_field_sets, manage_mp_settings, manage_mp_tools</span></p>
              <p><strong><?php _e("Masterplans", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">export_masterplan, import_masterplan, backup_masterplan, restore_masterplan</span></p>
              <p><strong><?php _e("Post Types", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">create_post_types, edit_post_types, delete_post_types, manage_post_type_field_sets, create_post_type_field_sets, edit_post_type_field_sets, delete_post_type_field_sets, create_post_type_fields, edit_post_type_fields, delete_post_type_fields</span></p>
              <p><strong><?php _e("Taxonomies", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">create_taxonomies, edit_taxonomies, delete_taxonomies, manage_taxonomy_field_sets, create_taxonomy_field_sets, edit_taxonomy_field_sets, delete_taxonomy_field_sets, create_taxonomy_fields, edit_taxonomy_fields, delete_taxonomy_fields</span></p>
              <p><strong><?php _e("Templates", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_templates, manage_template_field_sets, create_template_field_sets, edit_template_field_sets, delete_template_field_sets, create_template_fields, edit_template_fields, delete_template_fields</span></p>
              <p><strong><?php _e("User Roles", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">create_user_roles, edit_user_roles, manage_user_role_field_sets, create_user_role_field_sets, edit_user_role_field_sets, delete_user_role_field_sets, create_user_role_fields, edit_user_role_fields, delete_user_role_fields</span></p>
              <p><strong><?php _e("Site Field Sets", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">create_site_field_sets, edit_site_field_sets, delete_site_field_sets, create_site_fields, edit_site_fields, delete_site_fields</span></p>
              <p><strong><?php _e("Shared Field Sets", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">create_shared_field_sets, edit_shared_field_sets, delete_shared_field_sets, create_shared_fields, edit_shared_fields, delete_shared_fields</span></p>
            </div>
            
            
            
            <div id="mp-grant" <?php echo $style ?> class="mp-grant">
            <input id="mp_cap_grant" name="mp_cap_grant" value="true" type="checkbox" <?php echo WOOF_HTML::checked_attr($cap == "standard") ?> class="checkbox" />
            <label for="mp_cap_grant" class="radio"><?php _e('On save, add all capabilities above to roles with the <span class="tt">manage_options</span> capability.', MASTERPRESS_DOMAIN) ?></label>
            <p class="note">
            <?php _e("Note: you can also add these capabilities at a later time in the <em>User Roles</em> section, under the <b>Masterpress</b> tab when creating or editing a user role."); ?>
            </p>
                
            </div>

          </div>
          <!-- /.fw -->
       
      </div>
      </div>
    
    </div>
    <!-- /.fs -->
    
    
    
    <div class="fs fs-server-info">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="server"></i><strong><?php _e("Support Info", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("useful information for support requests", MASTERPRESS_DOMAIN) ?></h3>
        <div class="buttons">
          <button id="select-all-server-info" class="button button-small button-select-all" type="button"><?php _e('Select <strong>All</strong>', MASTERPRESS_DOMAIN) ?></button>
        </div>
      </div>
      </div>
        
      <div class="fsc">
      <div class="fscb">
        
        <?php
        
        global $wp_version, $wpdb;
        
        $active_plugin_list = array();
        $inactive_plugin_list = array();
        
        $plugins = get_plugins();
        
        foreach( $plugins as $plugin_file => $data ) {

          $pi = $data["Name"] . " v" . $data["Version"] . " - ".$data["PluginURI"];

      		if ( is_plugin_active( $plugin_file ) )  { 
      		  $active_plugin_list[] = $pi;
          } else {
      		  $inactive_plugin_list[] = $pi;
          }
      	}
      	
      	
        ?>
        
        <textarea id="server-info" readonly="readonly"><?php printf( __( "MasterPress Version: %s", MASTERPRESS_DOMAIN), MASTERPRESS_VERSION ) ?>  
<?php printf( __( "WordPress Version: %s", MASTERPRESS_DOMAIN), $wp_version ) ?>  
<?php printf( __( "Multi-site: %s", MASTERPRESS_DOMAIN), is_multisite() ? __("yes") : __("no") ) ?>  
<?php printf( __( "Host: %s", MASTERPRESS_DOMAIN), $_SERVER["HTTP_HOST"] ) ?>  

# PHP ENVIRONMENT

<?php printf( __( "PHP Version: %s", MASTERPRESS_DOMAIN), phpversion() ) ?>  
<?php printf( __( "MySQL Version: %s", MASTERPRESS_DOMAIN), $wpdb->db_version() ) ?>  
<?php printf( __( "Operating System: %s", MASTERPRESS_DOMAIN), PHP_OS ) ?>  

<?php printf( __( "Memory Limit: %s", MASTERPRESS_DOMAIN), ini_get( 'memory_limit' ) ) ?>  
<?php printf( __( "Max Execution Time: %ss", MASTERPRESS_DOMAIN), ini_get( 'max_execution_time' ) ) ?>  
<?php printf( __( "File Uploads: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_off_on( 'file_uploads' ) ) ?>  
<?php printf( __( "Post Max Size: %s", MASTERPRESS_DOMAIN), ini_get( 'post_max_size' ) ) ?>  
<?php printf( __( "Upload Max File Size: %s", MASTERPRESS_DOMAIN), ini_get( 'upload_max_filesize' ) ) ?>  
<?php printf( __( "Allow URL fopen: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_off_on( 'allow_url_fopen' ) ) ?>  

<?php printf( __( "Short Open Tag: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_off_on( 'short_open_tag' ) ) ?>  
<?php printf( __( "Display Errors: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_off_on( 'register_globals' ) ) ?>  

<?php printf( __( "Register Globals: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_setting( 'register_globals' ) ) ?>  
<?php printf( __( "Magic Quotes GPC: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_setting( 'magic_quotes_gpc' ) ) ?>  
<?php printf( __( "Magic Quotes Runtime: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_setting( 'magic_quotes_runtime' ) ) ?>  
<?php printf( __( "Safe Mode: %s", MASTERPRESS_DOMAIN), MasterPress::ini_get_setting( 'safe_mode' ) ) ?>  

# ACTIVE WORDPRESS PLUGINS

<?php echo implode("\n", $active_plugin_list); ?>


# INACTIVE WORDPRESS PLUGINS

<?php echo implode("\n", $inactive_plugin_list); ?>


# SERVER SOFTWARE

<?php echo $_SERVER["SERVER_SOFTWARE"] ?>

</textarea>
      </div>
      </div>
    
    </div>
    <!-- /.fs -->
    
    
    
    </div>
    <!-- /#settings -->
    
    <?php

  }
  
  
}

?>