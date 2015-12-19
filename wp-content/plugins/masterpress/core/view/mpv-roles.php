<?php

class MPV_Roles extends MPV {

  public static function __s() {
    return __("User Role", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("User Roles", MASTERPRESS_DOMAIN);
  }

  public function confirm_delete($role) {
    global $wf;

    ?>
    
    <div class="panel delete-panel delete-role-panel">
      <?php $this->form_open() ?> 
      <div class="panel-content">
        <header class="title">
          <h1><?php printf( __("Are you sure you want to delete the <em>%s</em> Role? This operation is not reversible!</span>", MASTERPRESS_DOMAIN), $role->name ); ?></h1>

          <div class="actions">
            <button class="button-primary button-delete" type="submit"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></button>
            <?php echo MPV::action_link("roles", "manage", __("Cancel", MASTERPRESS_DOMAIN), "", array( "class" => "button button-small primary" )); ?>
          </div>  

        </header>


      </div>
      <!-- /.panel-content -->

      </form>

    </div>
    

    <?php
    
  }
  
  
  public function grid($id = null) {

    global $wf;
    
    MPV::incl("field-sets");
    MPV::incl("fields");
    
    $essential_roles = array("administrator");
    $default_role = get_option("default_role");
    
    $roles = $wf->roles();
    
    $has_actions = MasterPress::current_user_can("edit_user_roles,delete_user_roles,manage_user_role_field_sets");
    $can_edit = MasterPress::current_user_can("edit_user_roles");
    $can_delete = MasterPress::current_user_can("delete_user_roles");
    $can_create = MasterPress::current_user_can("create_user_roles");
    $can_manage_field_sets = MasterPress::current_user_can("manage_user_role_field_sets");

    $less = ($can_create && !$has_actions) ? 1 : 0;
    $colspan = ( $has_actions ? 5 : 4 ) - $less;

    
    foreach ($roles as $role) {
      if (MPC::is_deleting($role->id)) {
        self::confirm_delete($role);
      }
    }
    
    
  ?>


  <?php MPV::messages(); ?>
  
  <table cellspacing="0" class="grid grid-roles">
    <thead>
    <tr>
      <th class="first label"><i class="label-string"></i><span><?php _e("Label", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="front-end-name"><i class="script-php"></i><span><?php _e("Name", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="number-of-users"><i class="users"></i><span><?php _e("Users", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="field-sets"><i class="metaboxes"></i><span><?php _e("Field Sets", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="number-of-capabilities <?php echo $has_actions ? "" : "last" ?>"><i class="key"></i><span><?php _e("Capabilities", MASTERPRESS_DOMAIN) ?></span></th>
      <?php if ($has_actions) : ?>
      <th class="actions last"><i class="buttons"></i><span><?php _e("Actions", MASTERPRESS_DOMAIN) ?></span></th>
      <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    
    
    <?php $count = 0; $first = "first"; ?>
  
    <?php foreach ($roles as $role) : $count++; ?>
      
    <?php $role_id = $role->id(); ?>
    
    <?php 
    
    $deleting_class = MPC::is_deleting($role_id, "delete") ? 'deleting' : ''; 
    $editable_class = $can_edit ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("roles", "edit", "id=".$role->id())."' }" : "";
    
    
    ?>
    
    <tr class="<?php echo $first ?> <?php echo $editable_class.$deleting_class ?> <?php echo MPV::updated_class("edit,create", $role->id()) ?> <?php echo $count % 2 == 0 ? "even" : "" ?> sub <?php echo $meta ?>">
      <td class="first label"><strong><?php echo $role->name ?></strong></td>
      <td class="front-end-name"><strong><?php echo $role->id ?></strong></td>
      
      <td class="number-of-users"><a href="<?php echo admin_url("users.php?role=".$role->id()) ?>"><?php echo WOOF::items_number(count($role->users), '<span class="note">( none )</span>', "1 user", "%d users") ?></a></td>
      <td class="field-sets <?php echo $can_manage_field_sets ? "manage" : "" ?>">
        <?php if ($can_manage_field_sets) : ?>
        <a href="<?php echo MasterPress::admin_url( "roles", "manage-field-sets", "parent=".$role->id())?>" title="<?php echo strip_tags(self::__manage( MPV_FieldSets::__p() )) ?>">
        <i class="go"></i>
		<?php endif; ?>
        
        <?php 
        
        $field_sets = MPM_RoleFieldSet::find_by_role( $role->id() ); 
        $field_set_display = MPV::note_none();
        
        if (count($field_sets)) {
          $field_set_links = array();
          
          foreach ($field_sets as $field_set) {
            if (!$field_set->is_shared()) {
              $field_set_links[] =  $field_set->display_label();
            }
          }

          if (count($field_set_links)) {
            $field_set_display = implode($field_set_links, ", ");
          }
        
        }
        
        echo $field_set_display;
        ?>
        <?php if ($can_manage_field_sets) : ?>
        </a>
        <?php endif; ?>
      </td>
      
      <?php
      
      $cap_count = count(array_diff(array_keys($role->capabilities), $wf->legacy_capabilities));
      
      if (MasterPress::$cap_mode == "standard") {
        $cap_count = $cap_count - count(array_intersect(array_keys($role->capabilities), MasterPress::$capabilities));
      }

      ?>

      <td class="number-capabilities <?php echo $has_actions ? "" : "last" ?>"><?php echo WOOF::items_number($cap_count, '<span class="note">( none )</span>', "1 capability", "%d capabilities") ?></td>
      <?php if ($has_actions) : ?>
      <td class="actions last">
      <div>
        <?php if (MPC::is_deleting($role_id)) : ?>
        
        <span class="confirm-action"><?php _e("Please Confirm Delete Action", MASTERPRESS_DOMAIN) ?></span>
        
        <?php else: ?>

        <?php if ($can_delete) : ?>
          <?php if (in_array($role_id, $essential_roles)) : ?>
          <span class="note"><?php _e("( required )") ?></span>
          <?php elseif ($role_id == $default_role) : ?>
          <span class="default-note note"><?php _e("( default )") ?></span>
          <?php else : ?>
          <?php echo MPV::action_button("roles", "delete", self::__delete( ), "id=".$role->id(), array("class" => "button button-delete")); ?>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($can_edit) : ?>
          <?php echo MPV::action_button("roles", "edit", self::__edit( ), "id=".$role->id(), array("class" => "button button-edit")); ?>
        <?php endif; ?>
        
        <?php if ($can_manage_field_sets) : ?>
          <?php echo MPV::action_button("roles", "manage-field-sets", self::__manage( MPV_FieldSets::__p_short() ), "parent=".$role->id(), array("class" => "button button-manage")); ?>
        <?php endif; ?>
        
        <?php endif; ?>

      </div>
      </td>
      <?php endif; ?>
    </tr>
    
    <?php $first = ""; ?>
    <?php endforeach; ?>

    <tr class="summary <?php echo $can_create ? "editable" : "" ?>">
      <td colspan="<?php echo $colspan ?>" class="first <?php echo $can_create ? "" : "last" ?>"><?php _e(  MPU::__items( $count, __("<strong>%d</strong> User Role", MASTERPRESS_DOMAIN), __("%d User Roles", MASTERPRESS_DOMAIN)   ) ) ?></td>
      <td class="last">
      <?php if ($can_create) : ?>
      <?php echo MPV::action_button("roles", "create", self::__create(MPV_Roles::__s()), "", array( "class" => "button button-create" ) ); ?>
      <?php endif; ?>
      </td>
    </tr>
    
    
    </tbody>
    </table>
    
    <?php
    
  } // end grid()
  
  
  
  public function form($type) {
    global $wf;

    $role_id = MasterPress::$id;
    $role = $wf->role($role_id);
    
    $role_name = "";
    
    $core = in_array($role_id, array("administrator", "subscriber"));
    
    if (MPC::is_edit()) {
      $caps = $role->capabilities;
      $role_name = $role->name;
    } else {
      $caps = array("read" => true);
    }
  
    $mp_section_caps = array(
  		'manage_masterplan' => __("Export, Import, Backup and Restore your Masterplan", MASTERPRESS_DOMAIN),
  		'manage_post_types' => __("Access the post types admin area", MASTERPRESS_DOMAIN),
  		'manage_taxonomies' => __("Access the taxonomies admin area", MASTERPRESS_DOMAIN),
  		'manage_templates' => __("Access the templates admin area", MASTERPRESS_DOMAIN),
  		'manage_user_roles' => __("Access the user roles admin area. Note: users in the Administrator role will ALWAYS have access to this area, to avoid potential lock-out scenarios", MASTERPRESS_DOMAIN),
  		'manage_site_field_sets' => __("Access the site field set administration area", MASTERPRESS_DOMAIN),
  		'manage_shared_field_sets' => __("Access the shared field set administration area", MASTERPRESS_DOMAIN),
  		'manage_mp_settings' => __("Manage MasterPress settings. Note: users in the Administrator role will ALWAYS have access to this area, to avoid potential lock-out scenarios", MASTERPRESS_DOMAIN),
    	'manage_mp_tools' => __("Access the MasterPress Tools Menus", MASTERPRESS_DOMAIN)
    );
    
    $mp_masterplan_caps = array(
  		'export_masterplan' => __("Export the current masterplan to a zip file", MASTERPRESS_DOMAIN),
  		'import_masterplan' => __("Replace the current masterplan with the contents of a masterplan zip file", MASTERPRESS_DOMAIN),
  		'backup_masterplan' => __("Backup the current masterplan", MASTERPRESS_DOMAIN),
  		'restore_masterplan' => __("Restore the masterplan from a backup", MASTERPRESS_DOMAIN)
    );
    
    $mp_post_types_caps = array(
  		'create_post_types' => __("Create a new post type", MASTERPRESS_DOMAIN),
  		'edit_post_types' => __("Edit existing post types", MASTERPRESS_DOMAIN),
  		'delete_post_types' => __("Delete post types", MASTERPRESS_DOMAIN),
  		'manage_post_type_field_sets' => __("Manage the custom field sets associated with post types.", MASTERPRESS_DOMAIN),
    	'create_post_type_field_sets' => __("Create custom field sets associated with post types.", MASTERPRESS_DOMAIN),
    	'edit_post_type_field_sets' => __("Edit existing custom field sets associated with post types.", MASTERPRESS_DOMAIN),
    	'delete_post_type_field_sets' => __("Delete existing custom field sets associated with post types.", MASTERPRESS_DOMAIN),
    	'create_post_type_fields' => __("Create custom fields associated with post types.", MASTERPRESS_DOMAIN),
    	'edit_post_type_fields' => __("Edit custom fields associated with post types.", MASTERPRESS_DOMAIN),
    	'delete_post_type_fields' => __("Delete custom fields associated with post types.", MASTERPRESS_DOMAIN)    	
    );

    $mp_taxonomies_caps = array(
  		'create_taxonomies' => __("Create a new taxonomy", MASTERPRESS_DOMAIN),
  		'edit_taxonomies' => __("Edit existing taxonomies", MASTERPRESS_DOMAIN),
  		'delete_taxonomies' => __("Delete taxonomies", MASTERPRESS_DOMAIN),
  		'manage_taxonomy_field_sets' => __("Manage the custom field sets associated with taxonomies.", MASTERPRESS_DOMAIN),
    	'create_taxonomy_field_sets' => __("Create custom field sets associated with taxonomies.", MASTERPRESS_DOMAIN),
    	'edit_taxonomy_field_sets' => __("Edit custom field sets associated with taxonomies.", MASTERPRESS_DOMAIN),
    	'delete_taxonomy_field_sets' => __("Delete custom field sets associated with taxonomies.", MASTERPRESS_DOMAIN),
    	'create_taxonomy_fields' => __("Create custom fields associated with taxonomies.", MASTERPRESS_DOMAIN),
    	'edit_taxonomy_fields' => __("Edit custom fields associated with taxonomies.", MASTERPRESS_DOMAIN),
    	'delete_taxonomy_fields' => __("Delete custom fields associated with taxonomies.", MASTERPRESS_DOMAIN)    	
    );

    $mp_templates_caps = array(
  		'edit_templates' => __("Edit content settings for templates", MASTERPRESS_DOMAIN),
  		'manage_template_field_sets' => __("Manage the custom field sets associated with templates.", MASTERPRESS_DOMAIN),
    	'create_template_field_sets' => __("Create custom field sets associated with templates.", MASTERPRESS_DOMAIN),
    	'edit_template_field_sets' => __("Edit custom field sets associated with templates.", MASTERPRESS_DOMAIN),
    	'delete_template_field_sets' => __("Delete custom field sets associated with templates.", MASTERPRESS_DOMAIN),
    	'create_template_fields' => __("Create custom fields associated with templates.", MASTERPRESS_DOMAIN),
    	'edit_template_fields' => __("Edit custom fields associated with templates.", MASTERPRESS_DOMAIN),
    	'delete_template_fields' => __("Delete custom fields associated with templates.", MASTERPRESS_DOMAIN)    	
    );

    $mp_user_roles_caps = array(
  		'create_user_roles' => __("Create a new user role", MASTERPRESS_DOMAIN),
  		'edit_user_roles' => __("Edit existing user roles. Note: users in the Administrator role will ALWAYS be able to edit user roles, to avoid potential lock-out scenarios", MASTERPRESS_DOMAIN),
  		'delete_user_roles' => __("Delete user roles", MASTERPRESS_DOMAIN),
  		'manage_user_role_field_sets' => __("Manage the custom field sets associated with user roles.", MASTERPRESS_DOMAIN),
    	'create_user_role_field_sets' => __("Create custom field sets associated with user roles.", MASTERPRESS_DOMAIN),
    	'edit_user_role_field_sets' => __("Edit custom field sets associated with user roles.", MASTERPRESS_DOMAIN),
    	'delete_user_role_field_sets' => __("Delete custom field sets associated with user roles.", MASTERPRESS_DOMAIN),
    	'create_user_role_fields' => __("Create custom fields associated with user roles.", MASTERPRESS_DOMAIN),
    	'edit_user_role_fields' => __("Edit custom fields associated with user roles.", MASTERPRESS_DOMAIN),
    	'delete_user_role_fields' => __("Delete custom fields associated with user roles.", MASTERPRESS_DOMAIN)    	
    );

    $mp_site_field_sets_caps = array(
  		'create_site_field_sets' => __("Create site field sets, and the fields within them", MASTERPRESS_DOMAIN),
  		'edit_site_field_sets' => __("Edit existing site field sets, and the fields within them", MASTERPRESS_DOMAIN),
  		'delete_site_field_sets' => __("Delete existing site field sets, and the fields within them", MASTERPRESS_DOMAIN),
    	'create_site_fields' => __("Create custom fields associated with sites.", MASTERPRESS_DOMAIN),
    	'edit_site_fields' => __("Edit custom fields associated with sites.", MASTERPRESS_DOMAIN),
    	'delete_site_fields' => __("Delete custom fields associated with sites.", MASTERPRESS_DOMAIN)    	
    );

    $mp_shared_field_sets_caps = array(
  		'create_shared_field_sets' => __("Create shared field sets, and the fields within them", MASTERPRESS_DOMAIN),
  		'edit_shared_field_sets' => __("Edit existing shared field sets, and the fields within them", MASTERPRESS_DOMAIN),
  		'delete_shared_field_sets' => __("Delete existing shared field sets, and the fields within them", MASTERPRESS_DOMAIN),
    	'create_shared_fields' => __("Create shared custom fields.", MASTERPRESS_DOMAIN),
    	'edit_shared_fields' => __("Edit shared custom fields.", MASTERPRESS_DOMAIN),
    	'delete_shared_fields' => __("Delete shared custom fields.", MASTERPRESS_DOMAIN)    	
    );

    
    $ac = array_merge( 
      $wf->core_capabilities,
      array_keys($mp_section_caps),
      array_keys($mp_masterplan_caps),
      array_keys($mp_post_types_caps),
      array_keys($mp_taxonomies_caps),
      array_keys($mp_templates_caps),
      array_keys($mp_user_roles_caps),
      array_keys($mp_site_field_sets_caps),
      array_keys($mp_shared_field_sets_caps)
    );
    
    $all_caps = array_fill_keys($ac, true);
    
    $other_caps = array();
    
    // run through the all other roles, and determine any other custom caps
      
    foreach ($wf->roles as $role) {
      foreach ($role->capabilities() as $key => $cap) {
        
        if (!isset($all_caps[$key])) {
          $other_caps[$key] = true;
        }
      } 
    }
    

    $dashboard_caps = array(
      'read' => __("Access the Dashboard and Users &gt; Your Profile", MASTERPRESS_DOMAIN),
  		'edit_dashboard' => __("Configure screen options and individual dashboard panels (metaboxes)", MASTERPRESS_DOMAIN),
  		'manage_options' => __("Access installed Plugin settings and these areas in WordPress Settings - General, Writing, Reading, Discussion, Permalinks", MASTERPRESS_DOMAIN),
      'update_core' => __("Auto-update WordPress itself", MASTERPRESS_DOMAIN)
    );
    
    $admin_user_caps = array(
  		'list_users' => __("View a list of the users in the site", MASTERPRESS_DOMAIN),
  		'add_users' => __("Add a new user to the site", MASTERPRESS_DOMAIN),
  		'edit_users' => __("Edit users in the site", MASTERPRESS_DOMAIN), 
  		'create_users' => __("Create other users", MASTERPRESS_DOMAIN),
  		'remove_users' => __("Remove other users from a role", MASTERPRESS_DOMAIN),
  		'delete_users' => __("Remove users altogether", MASTERPRESS_DOMAIN), 
  		'promote_users' => __("Change the role of other users", MASTERPRESS_DOMAIN)
  	);
  	
  	$admin_plugin_caps = array(
      'activate_plugins' => __("Access the Plugins main screen", MASTERPRESS_DOMAIN),
  		'install_plugins' => __("Access Plugins &gt; Add New", MASTERPRESS_DOMAIN),
  		'delete_plugins' => __("Remove plugins via the Plugins screen", MASTERPRESS_DOMAIN),
  		'edit_plugins' => __("Access Plugins &gt; Plugin Editor", MASTERPRESS_DOMAIN),
  		'update_plugins' => __("Auto-update plugins", MASTERPRESS_DOMAIN)
    );
    
    $admin_theme_caps = array(
  		'edit_themes' => __("Access Appearance &gt; Theme Editor to edit theme files"),
      'edit_theme_options' => __("Access the Background, Header, Menus, Widgets sections of Appearance, and access theme-specific options", MASTERPRESS_DOMAIN),
    	'install_themes' => __("Access Appearance &gt; Add New Themes to install new themes", MASTERPRESS_DOMAIN),
  		'update_themes' => __("Auto-update themes", MASTERPRESS_DOMAIN),
  		'delete_themes' => __("Delete themes", MASTERPRESS_DOMAIN),
  		'switch_themes' => __("Access Appearance &gt; Themes to change the active theme", MASTERPRESS_DOMAIN)
    );

    
    $core_object_caps = array(
  		'manage_categories' => __("Manage categories, tags, and custom taxonomies using this capability", MASTERPRESS_DOMAIN), 
    );

    $content_caps = array(
  		'manage_links' => __("Manage links", MASTERPRESS_DOMAIN),
      'moderate_comments' => __("Access moderation controls in the Comments screen (Requires edit_posts capability to access)", MASTERPRESS_DOMAIN),
      'upload_files' => __("Access the Media, and Media &gt; Add New screens, and attach uploads to post content", MASTERPRESS_DOMAIN),
  		'import' => __("Access Tools &gt; Import to import content into the site", MASTERPRESS_DOMAIN),
  		'export' => __("Access Tools &gt; Export to export content from the site", MASTERPRESS_DOMAIN),
      'unfiltered_html' => __("Allowed to use HTML and JavaScript code in page, post, and comment content"),
  		'unfiltered_upload' => __("Allowed to upload file types that are not regarded as secure", MASTERPRESS_DOMAIN)
    );
    
    $post_caps = array(
  		'edit_posts' => __("Access Posts, Posts &gt; Add New, Comments, and Comments &gt; Awaiting Moderation areas for standard posts, and custom post types with this capability", MASTERPRESS_DOMAIN),
  		'edit_others_posts' => __("Edit other users' posts", MASTERPRESS_DOMAIN),
  		'edit_private_posts' => __("Edit posts marked as private", MASTERPRESS_DOMAIN),
  		'edit_published_posts' => __("Edit posts that have already been published", MASTERPRESS_DOMAIN),
  		'delete_posts' => __("Delete posts", MASTERPRESS_DOMAIN),
  		'delete_others_posts' => __("Delete other users' posts", MASTERPRESS_DOMAIN),
  		'delete_private_posts' => __("Delete posts marked as private", MASTERPRESS_DOMAIN),
  		'delete_published_posts' => __("Delete posts that have already been published", MASTERPRESS_DOMAIN),
  		'publish_posts' => __("Publish posts - that is, elevate them beyond Draft status", MASTERPRESS_DOMAIN),
  		'read_private_posts' => __("See posts marked as private in the admin and site", MASTERPRESS_DOMAIN)
    );
    
    
    $page_caps = array(
  		'edit_pages' => __("Access Pages, Pages &gt; Add New, Comments, and Comments &gt; Awaiting Moderation areas for standard pages, and custom post types with this capability", MASTERPRESS_DOMAIN),
  		'edit_others_pages' => __("Edit other users' pages", MASTERPRESS_DOMAIN),
  		'edit_private_pages' => __("Edit pages marked as private", MASTERPRESS_DOMAIN),
  		'edit_published_pages' => __("Edit pages that have already been published", MASTERPRESS_DOMAIN),
  		'delete_pages' => __("Delete pages", MASTERPRESS_DOMAIN),
  		'delete_others_pages' => __("Delete other users' pages", MASTERPRESS_DOMAIN),
  		'delete_private_pages' => __("Delete pages marked as private", MASTERPRESS_DOMAIN),
  		'delete_published_pages' => __("Delete pages tat have already been published", MASTERPRESS_DOMAIN),
  		'publish_pages' => __("Publish pages - that is, elevate them beyond Draft status", MASTERPRESS_DOMAIN),
  		'read_private_pages' => __("See pages marked as private in the admin and site", MASTERPRESS_DOMAIN)
  	);
  	
  	
  	$level_caps = array(
  		'level_1' => __("User Level 1 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_2' => __("User Level 2 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_3' => __("User Level 3 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_4' => __("User Level 4 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_5' => __("User Level 5 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_6' => __("User Level 6 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_7' => __("User Level 7 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_8' => __("User Level 8 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_9' => __("User Level 9 (in the legacy user level system)", MASTERPRESS_DOMAIN),
  		'level_10' => __("User Level 10 (in the legacy user level system)", MASTERPRESS_DOMAIN)
  	);
  		
  ?>

    <?php MPV::messages(); ?>
  
    <?php
    
    
    function cap_section($name, $label, $section_caps, $caps, $first = FALSE, $icon_class = "", $custom = false, $admin_lock = false) {
      
      $ck = array_keys($caps);
      
      $title = "";
      
      if ($admin_lock) {
        $title = __("This capability cannot be changed for the Administrator role, as it may cause WordPress to become inaccessible");
      }
      
      ?>

      <div id="cap-<?php echo $name ?>" class="fss cap-section <?php echo $first ? "first" : "" ?>">
        <div class="title">
          <?php if ($icon_class == "") : ?>
          <h4><i class="cap-<?php echo $name ?>"></i><?php echo $label ?></h4>
          <?php else : ?>
          <h4><i class="<?php echo $icon_class ?>"></i><?php echo $label ?></h4>
          <?php endif; ?>
          
          <?php if (!$admin_lock) : ?>
          <div class="buttons">
            <button class="button button-small button-select-all" type="button"><?php _e('Select <strong class="all">All</strong>', MASTERPRESS_DOMAIN) ?></button>
            <button class="button button-small button-select-none" type="button"><?php _e('Select <strong class="none">None</strong>', MASTERPRESS_DOMAIN) ?></button>
          </div>
          <?php endif; ?>
        </div>

        <div class="f" title="<?php echo $title ?>">
          
          <?php foreach ($section_caps as $key => $desc) :  ?>
          
          <?php if (!is_numeric($key)) : ?>
             
          <div class="fw">
            
          
          <?php if (!$custom) : ?>
          <span data-tooltip="<?php echo htmlentities($desc) ?>" class="info with-mptt"></span>
          <?php endif; ?>
          
          <?php if ($admin_lock) : ?>
          <input id="cap-lock<?php echo $key ?>" name="cap_lock[<?php echo $key ?>]" value="" disabled="disabled" type="checkbox" <?php echo WOOF_HTML::checked_attr( in_array($key, $ck, TRUE)) ?> class="checkbox" />
          <input id="cap-<?php echo $key ?>" name="cap[<?php echo $key ?>]" value="yes" type="hidden" />
          <label for="cap-<?php echo $key ?>" class="checkbox disabled"><?php echo $key ?></label>
          <?php else: ?>
          <input id="cap-<?php echo $key ?>" name="cap[<?php echo $key ?>]" value="yes" type="checkbox" <?php echo WOOF_HTML::checked_attr( in_array($key, $ck, TRUE)) ?> class="checkbox" />
          <label for="cap-<?php echo $key ?>" class="checkbox"><?php echo $key ?></label>
          <?php endif; ?>
          
          </div>

          <?php endif; ?>

          <?php endforeach; ?>
          
        </div>
        <!-- /.f -->
          
      </div>
      <!-- /.cap-section -->
      
      <?php
    }
    
    
    ?>

    <div class="f">
      <label for="name" class="icon"><i class="script-php"></i><?php _e("Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="name" name="name" type="text" <?php if (MPC::is_edit()) { echo ' readonly="readonly" '; } ?> class="<?php if (MPC::is_edit()) { echo 'readonly'; } ?> text mono key" maxlength="20" value="<?php echo $role_id ?>" />
      </div>
    </div>
    <!-- /.f -->
    
    <div class="f">
      <label for="display_name" class="icon"><i class="label-string"></i><?php _e("Label", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="display_name" name="display_name" type="text" <?php if (MPC::is_edit()) { echo ' readonly="readonly" '; } ?> class="<?php if (MPC::is_edit()) { echo 'readonly'; } ?> text { func: 'val', src: '#name', format: 'titleize' }" maxlength="40" value="<?php echo $role_name ?>" />
      </div>
    </div>
    <!-- /.f -->
  
    <div class="fs fs-capabilities fs-with-tabs">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="key"></i><strong><?php _e("Capabilities", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("determines the tasks that users in this role are able to perform", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
        
        <?php
        
        // check for custom post type specific capabilities, and list these
          

        $cpt_template = "edit_CPTs,edit_others_CPTs,edit_private_CPTs,edit_published_CPTs,delete_CPTs,delete_others_CPTs,delete_private_CPTs,delete_published_CPTs,publish_CPTs,read_private_CPTs";

        $first = false;

        $pt_caps = array();
        
        foreach (MPM_PostType::find() as $post_type) {

          if ($post_type->capability_type != "post" && $post_type->capability_type != "page") {

            $key = $post_type->name;

            $key_str = str_replace("CPT", $key, $cpt_template);

            $keys = array_fill_keys(explode(",", $key_str), true);

            $all_caps = $all_caps + $keys;

            $pt_caps[] = array("post_type" => $post_type, "keys" => $keys);
            

            $first = false;
          }

        }
        
        
        // check for custom taxonomy specific capabilities, and list these

        $default_tax_caps = array(
          "manage_terms" => "manage_categories",
          "edit_terms" => "manage_categories",
          "delete_terms" => "manage_categories",
          "assign_terms" => "edit_posts"
        );

        $tax_caps = array();
        
        foreach (MPM_Taxonomy::find() as $tax) {

          $my_caps = $tax->capabilities;

          $diff = array_diff ($my_caps, $default_tax_caps);

          if (count($diff)) {

            $keys = array_fill_keys(array_values($diff), true);

            $all_caps = $all_caps + $keys;
            
            $tax_caps[] = array("tax" => $tax, "keys" => $keys);

            $first = false;

          }

        }
        
        
          
        // check for custom field set keys
        
        $fs_caps = array();
        
        foreach (MPM_FieldSet::find(array("where" => "")) as $field_set) {
          
          $my_caps = array_fill_keys( array( $field_set->capability("visible"), $field_set->capability("editable") ), true);
          
          $diff = array_diff_key( $my_caps, $all_caps );
          
          if (count($diff)) {
            $fs_caps = array_merge($fs_caps, $diff);
          }
          
          
        }

        
        

        $f_caps = array();

        // now check for custom field or field set keys
        
        foreach (MPM_Field::find() as $field) {

          $my_caps = array_fill_keys( array( $field->capability("visible"), $field->capability("editable") ), true);
          
          $diff = array_diff_key( $my_caps, $all_caps );
          
          if (count($diff)) {
            $f_caps = array_merge($f_caps, $diff);
          }
          
        }
        
        ?>
        
        <ul class="fs-tabs">
          <li><a href="#cap-dashboard" class="current"><span><i class="dashboard"></i><?php _e("Dashboard", MASTERPRESS_DOMAIN) ?></span></a></li>
          <li><a href="#cap-post-types"><span><i class="pins"></i><?php _e("Posts &amp; Pages", MASTERPRESS_DOMAIN) ?></span></a></li>
          <li><a href="#cap-taxonomies"><span><i class="tags"></i><?php _e("Terms", MASTERPRESS_DOMAIN) ?></span></a></li>

          <?php if (count($fs_caps) + count($f_caps)) : ?>
          <li><a href="#cap-fields"><span><i class="text-box"></i><?php _e("Fields", MASTERPRESS_DOMAIN) ?></span></a></li>
          <?php endif; ?>
          
          <?php $mp_cap_style = get_site_option("mp_cap", "standard") == "specific" ? '' : ' style="display: none;" '; ?>
          
          <li <?php echo $mp_cap_style ?>><a href="#cap-masterpress"><span><i class="masterpress"></i><?php _e("MasterPress", MASTERPRESS_DOMAIN) ?></span></a></li>
          
          <li><a href="#cap-custom"><span><i class="hammer"></i><?php _e("Custom", MASTERPRESS_DOMAIN) ?></span></a></li>
        </ul>

        <div id="cap-dashboard" class="tab-panel current">

        <?php 
        
        $admin_lock = $role_id == "administrator";
        
        cap_section("dashboard", __("Access &amp; Customisation", MASTERPRESS_DOMAIN), $dashboard_caps, $caps, true, "", false, $admin_lock); 
        cap_section("core-content", __("Content, Comments &amp; Links", MASTERPRESS_DOMAIN), $content_caps, $caps, false); 
        cap_section("admin-user", __("User Admin", MASTERPRESS_DOMAIN), $admin_user_caps, $caps, false); 
        cap_section("admin-plugin", __("Plugin Admin", MASTERPRESS_DOMAIN), $admin_plugin_caps, $caps, false); 
        cap_section("admin-theme", __("Theme Admin", MASTERPRESS_DOMAIN), $admin_theme_caps, $caps, false); 
        cap_section("levels",     __("User Levels&nbsp;- <span>using these keys should be avoided, but some legacy themes and plug-ins may still make use of them</span>", MASTERPRESS_DOMAIN), $level_caps, $caps, false); 

        ?>

        </div>
        <!-- /#cap-dashboard -->
        
        <div id="cap-post-types" class="tab-panel">
          <?php
          cap_section("post", __("Posts - <span>custom post types will generally use these capabilities unless the <em>specific</em> or <em>custom</em> key styles are used</span>", MASTERPRESS_DOMAIN), $post_caps, $caps, true, "", false); 
          cap_section("page", __("Pages", MASTERPRESS_DOMAIN), $page_caps, $caps, false); 

          if (count($pt_caps)) {
            foreach ($pt_caps as $pair) {
              extract($pair);
              cap_section($post_type->name, $post_type->display_label(), $keys, $caps, $first, "mp-icon mp-icon-post-type mp-icon-post-type-".$post_type->name, true ); 
            }
          }

          ?>
        </div>

        <div id="cap-taxonomies" class="tab-panel">
        <?php
          cap_section("core-object", __("Categories &amp; Tags - <span>custom taxonomies will have generally use these capabilities unless otherwise specified", MASTERPRESS_DOMAIN), $core_object_caps, $caps, true); 
          
          if (count($tax_caps)) {
            foreach ($tax_caps as $pair) {
              extract($pair);
              cap_section($tax->name, $tax->display_label(), $keys, $caps, $first, "mp-icon mp-icon-taxonomy mp-icon-taxonomy-".$tax->name, true ); 
            }
          }
          
        ?>
        </div>
        
        
        <div id="cap-masterpress" class="tab-panel" <?php echo $mp_cap_style ?>>
        <?php
        
          cap_section("mp_sections",     __("Section Access", MASTERPRESS_DOMAIN), $mp_section_caps, $caps, true); 
          cap_section("mp_masterplan",   __("Masterplan", MASTERPRESS_DOMAIN), $mp_masterplan_caps, $caps); 
          cap_section("mp_post_types",   __("Post Types", MASTERPRESS_DOMAIN), $mp_post_types_caps, $caps); 
          cap_section("mp_taxonomies",   __("Taxonomies", MASTERPRESS_DOMAIN), $mp_taxonomies_caps, $caps); 
          cap_section("mp_templates",    __("Templates", MASTERPRESS_DOMAIN), $mp_templates_caps, $caps); 
          cap_section("mp_user_roles",      __("User Roles", MASTERPRESS_DOMAIN), $mp_user_roles_caps, $caps); 
          cap_section("mp_site_field_sets",   __("Site Field Sets", MASTERPRESS_DOMAIN), $mp_site_field_sets_caps, $caps); 
          cap_section("mp_shared_field_sets",   __("Shared Field Sets", MASTERPRESS_DOMAIN), $mp_shared_field_sets_caps, $caps); 
        
        ?>
        </div>
        
        
        <?php if (count($fs_caps) + count($f_caps)) : ?>
        <div id="cap-fields" class="tab-panel">
          
        <?php
        
        $first = true;
        
        if (count($fs_caps)) {
          $all_caps = $all_caps + $fs_caps;
          cap_section("field-sets", __("Field Sets", MASTERPRESS_DOMAIN), $fs_caps, $caps, $first, "", true ); 
          $first = false;
        }
        
        if (count($f_caps)) {
          $all_caps = $all_caps + $f_caps;
          cap_section("fields", __("Fields", MASTERPRESS_DOMAIN), $f_caps, $caps, $first, "", true ); 
          $first = false;
        }


        
        ?>
        
        </div>
        <?php endif; ?>
        
        <div id="cap-custom" class="tab-panel">

        <div id="cap-add-custom" class="fss cap-section first">
          <div class="title">
            <h4><i class="cap-add-custom"></i><?php _e("New - <span>enter new capability keys for this role, which will appear under <b>Keys</b> below after updating this role</span>") ?></h4>
          </div>
          
          <div class="f" title="<?php echo $title ?>">
          
            <?php for ($i=0; $i<12; $i++) : ?>
            <div class="fw">
              <input type="text" name="new_caps[]" class="text mono" value="" />
            </div>
            <?php endfor; ?>
          
          </div>
          <!-- /.f -->
          
        </div>

          
        
        <?php
        
        // finally, check for any caps recorded in the role, but not in core or all caps.
        
        $other_caps = array_diff_key($other_caps, $all_caps);
        
        if (count($other_caps)) {
          cap_section("other", __("Keys - <span>additional recorded capabilities (from themes, plug-ins, and custom additions)</span>", MASTERPRESS_DOMAIN), $other_caps, $caps, $first, "", true ); 
          $first = false;

          $all_caps = $all_caps + $other_caps;

        }
        
        ?>
          
          
        </div>
        
      
      </div>
      </div>

    </div>
    <!-- /.fs -->

        
    <input type="hidden" id="all-caps" name="all_caps" value="<?php echo implode(",", array_keys($all_caps)) ?>" />  

    <?php
    
  } // end form
  
  
}

?>