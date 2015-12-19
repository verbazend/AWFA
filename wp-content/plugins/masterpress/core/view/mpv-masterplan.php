<?php

class MPV_Masterplan extends MPV { 

  function manage() {
    
    global $wf;
    
    ?>

    <?php MPV::messages() ?>
  
    <?php

    MPV::incl("post-types");
    MPV::incl("shared-field-sets");
    MPV::incl("templates");
    MPV::incl("taxonomies");
    MPV::incl("roles");
  
    $disabled = "";
  
    $view = MasterPress::$view;
    
    $post_types = MPM_PostType::find("orderby=disabled,name ASC");
    $taxonomies = MPM_Taxonomy::find("orderby=disabled,name ASC");
    $shared_field_sets = MPM_SharedFieldSet::find("orderby=disabled,name ASC");
    $templates = get_page_templates();
    $site_field_sets = MPM_SiteFieldSet::find("orderby=disabled,name ASC"); 
    $roles = $wf->roles(); 

    ?>

    <div class="fs fs-masterplan fs-with-tabs">
    
        
      <div class="fsc">
      <div class="fscb">
        
        <?php
            $tab = "";
            
            if (isset($_REQUEST["tab"])) {
              $tab = $_REQUEST["tab"];
            }
          ?>

          <?php 
            
            // check for restoreable backups

            
            $backups = array();
            
            if (file_exists(MASTERPRESS_CONTENT_MASTERPLANS_DIR)) {

            $iterator = new DirectoryIterator(MASTERPRESS_CONTENT_MASTERPLANS_DIR);
            
            foreach ($iterator as $file) {

              $file_name = $file->getFileName();
    
              if (substr($file_name, 0, 1) == ".") {
                continue;
              }
              
              if (preg_match("/\_backup\.(?:(.*)\.)?([0-9]+)\.masterplan\.zip/", $file_name, $matches)) {
                
                $tag = $matches[1];
                $date = $matches[2];
                
                $fdate = $wf->date_format("[date-time-long]", strtotime($date));
                
                $label = $fdate;
                
                if ($tag != "") {
                  $label = $fdate." ( ".$tag." )";
                }
                
                $backups[] = array("file" => $file_name, "label" => $label);
                
                
              }
              
            }
            
            }
          
          ?>
          
        <ul class="fs-tabs">
          <li><a href="#masterplan-overview" class="<?php echo $tab != '' ? '' : 'current' ?>"><span><i class="info"></i>Overview</span></a></li>
          <?php if (MasterPress::current_user_can("export_masterplan")) : ?>
          <li><a href="#masterplan-export" class="<?php echo $tab == "export" ? 'current' : '' ?>"><span><i class="arrow-curve-right"></i>Export</span></a></li>
          <?php endif; ?>
          <?php if (MasterPress::current_user_can("import_masterplan")) : ?>
          <li><a href="#masterplan-import" class="<?php echo $tab == "import" ? 'current' : '' ?>"><span class="import"><i class="arrow-curve-left"></i>Import</span></a></li>
          <?php endif; ?>
          <?php if (MasterPress::current_user_can("backup_masterplan")) : ?>
          <li><a href="#masterplan-backup" class="<?php echo $tab == "backup" ? 'current' : '' ?>"><span class="backup"><i class="buoy"></i>Backup</span></a></li>
          <?php endif; ?>
          <?php if (count($backups)) : ?>
          <?php if (MasterPress::current_user_can("restore_masterplan")) : ?>
          <li><a href="#masterplan-restore" class="<?php echo $tab == "restore" ? 'current' : '' ?>"><span class="restore"><i class="clock-history"></i>Restore</span></a></li>
          <?php endif; ?>
          <?php endif; ?>
        </ul>
    
<!-- OVERVIEW -->


        <div id="masterplan-overview" class="tab-panel <?php echo $tab != '' ? '' : 'current' ?>">
          
          <?php if (isset($_GET["import-complete"])) : ?>
          
          <ul class="mp-messages">
            <li class="success"><?php _e("Import Complete - your new site Masterplan is shown below.", MASTERPRESS_DOMAIN) ?></li>
          </ul>
          
          <?php endif; ?>

          <?php if (isset($_GET["restore-complete"])) : ?>
          
          <ul class="mp-messages">
            <li class="success"><?php _e("Restore Complete - your site Masterplan is shown below.", MASTERPRESS_DOMAIN) ?></li>
          </ul>
          
          <?php endif; ?>
        
          <div class="row first-row">
          
            <div class="col post-types">
            
              <?php 
              
              $count = 0;
              
              foreach ($post_types as $post_type) { 
                if ($post_type->still_registered()) {
                  $count++;
                }
              }
              
              ?>
                
              <h4><i class="pin"></i>
              <?php if (MasterPress::current_user_can("manage_post_types")) : ?>
              <a href="<?php echo MasterPress::admin_url("post-types", "manage") ?>" title="<?php _e("Manage Post Types", MASTERPRESS_DOMAIN) ?>" class="icon">
              <?php else : ?>
              <span class="icon">
              <?php endif; ?>
              
              <?php echo WOOF::items_number( $count, __("<em>No</em> Post Types", MASTERPRESS_DOMAIN), __("<b>%d</b> Post Type", MASTERPRESS_DOMAIN), __("<b>%d</b> Post Types", MASTERPRESS_DOMAIN) ) ?>
              
              <?php if (MasterPress::current_user_can("manage_post_types")) : ?>
              </a>
              <?php else : ?>
              </span>
              <?php endif; ?>
              </h4>
            
              <div class="body">
              
                <ul class="count-<?php echo $count ?>">
                <?php foreach ($post_types as $post_type) : $default_icon = $post_type->menu_icon_exists() ? "" : "default-icon"; $field_sets = $post_type->post_type_field_sets(); $fsc = count($field_sets); $disabled = $post_type->disabled ? 'disabled ' : ''; $disabled_title = $post_type->disabled ? __('This post type is currently disabled', MASTERPRESS_DOMAIN) : ''; ?>
                  
                <?php if ($post_type->still_registered()) : ?>
                <li class="<?php echo $disabled ?>post-type-<?php echo $post_type->name ?> <?php echo $default_icon ?> linkify" data-name="<?php echo $post_type->name ?>" title="<?php echo $disabled_title ?>">
                  
                  <?php if (MasterPress::current_user_can("manage_post_types+edit_post_types")) : ?>
                  <a href="<?php echo MasterPress::admin_url("post-types", "edit", "id=".$post_type->id) ?>" class="item mp-icon-post-type mp-icon-post-type-<?php echo $post_type->name ?>">
                  <?php else: ?>
                  <div class="item mp-icon-post-type mp-icon-post-type-<?php echo $post_type->name ?>">  
                  <?php endif; ?>
                  
                  <?php echo WOOF::truncate($post_type->display_label(), "length=15&words=0") ?>

                  <?php if (MasterPress::current_user_can("manage_post_types+edit_post_types")) : ?>
                  </a>
                  <?php else: ?>
                  </div>  
                  <?php endif; ?>
                  
                  <?php if (MasterPress::current_user_can("manage_post_types+manage_post_type_field_sets")) : ?>
                  <a href="<?php echo MasterPress::admin_url("post-types", "manage-field-sets", "parent=".$post_type->id) ?>" title="<?php _e("Manage Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></a>
                  <?php else : ?>
                  <div title="<?php _e("Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></div>
                  <?php endif; ?>
                    
                  <?php foreach ($post_type->taxonomies() as $tax) : ?> 
                  <span class="tax-link-<?php echo $tax->name ?> related-link asterisk"></span>
                  <?php endforeach; ?>

                  <?php foreach ($post_type->field_sets() as $fs) : ?>
                  <?php if ($fs->is_shared()) : ?>
                  <span class="shared-field-set-link-<?php echo $fs->name ?> related-link asterisk"></span>
                  <?php endif; ?>
                  <?php endforeach; ?>


                </li>
                <?php endif; ?>

                <?php endforeach; ?>
                
                <?php if (MasterPress::current_user_can("create_post_types")) : ?>
                <li class="divide"><a href="<?php echo MasterPress::admin_url("post-types", "create") ?>" class="create-link"><i></i><?php echo MPV::__create( MPV_PostTypes::__s() ) ?></a></li>
                <?php endif; ?>

                </ul>
            
              </div>
              <!-- /.body -->
            
            </div>
            <!-- /.col -->
          
          
            <div class="col taxonomies">
            
              <?php 
              
              $count = 0;
              
              foreach ($taxonomies as $tax) { 
                if ($tax->still_registered()) {
                  $count++;
                }
              }
              
              ?>
              
              <h4><i class="tag"></i>
              <?php if (MasterPress::current_user_can("manage_taxonomies")) : ?>
              <a href="<?php echo MasterPress::admin_url("taxonomies", "manage") ?>" title="<?php _e("Manage Taxonomies", MASTERPRESS_DOMAIN) ?>" class="icon">              
              <?php else : ?>
              <span class="icon">
              <?php endif; ?>
              
              <?php echo WOOF::items_number( $count, __("<em>No</em> Taxonomies", MASTERPRESS_DOMAIN), __("<b>%d</b> Taxonomy", MASTERPRESS_DOMAIN), __("<b>%d</b> Taxonomies", MASTERPRESS_DOMAIN) ) ?>
              
              <?php if (MasterPress::current_user_can("manage_taxonomies")) : ?>
              </a>
              <?php else : ?>
              </span>
              <?php endif; ?>
              </h4>
              
              <div class="body">
              
                <ul class="count-<?php $count ?>">
                <?php foreach ($taxonomies as $tax) : $field_sets = $tax->taxonomy_field_sets(); $fsc = count($field_sets); $disabled = $tax->disabled ? 'disabled ' : ''; $disabled_title = $tax->disabled ? __('This taxonomy is currently disabled', MASTERPRESS_DOMAIN) : ''; ?>
                
                <?php if ($tax->still_registered()) : ?>

                <li class="<?php echo $disabled ?> linkify" data-name="<?php echo $tax->name ?>" title="<?php echo $disabled_title ?>">
                  
                  <?php if (MasterPress::current_user_can("manage_taxonomies+edit_taxonomies")) : ?>
                  <a href="<?php echo MasterPress::admin_url("taxonomies", "edit", "id=".$tax->id) ?>" class="item mp-icon-taxonomy mp-icon-taxonomy-<?php echo $tax->name ?>">
                  <?php else: ?>
                  <div class="item mp-icon-taxonomy mp-icon-taxonomy-<?php echo $tax->name ?>">  
                  <?php endif; ?>
                  
                  <?php echo WOOF::truncate($tax->display_label(), "length=15&words=0") ?>
                  
                  <?php if (MasterPress::current_user_can("manage_taxonomies+edit_taxonomies")) : ?>
                  </a>
                  <?php else: ?>
                  </div>  
                  <?php endif; ?>
                  
                  
                  <?php if (MasterPress::current_user_can("manage_taxonomies+manage_taxonomy_field_sets")) : ?>
                  <a href="<?php echo MasterPress::admin_url("taxonomies", "manage-field-sets", "parent=".$tax->id) ?>" title="<?php _e("Manage Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></a>
                  <?php else : ?>
                  <div title="<?php _e("Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></div>
                  <?php endif; ?>


                  <?php foreach ($tax->post_types() as $pt) : ?> 
                  <span class="post-type-link-<?php echo $pt->name ?> related-link asterisk"></span>
                  <?php endforeach; ?>

                  <?php foreach ($tax->field_sets() as $fs) : ?>
                  <?php if ($fs->is_shared()) : ?>
                  <span class="shared-field-set-link-<?php echo $fs->name ?> related-link asterisk"></span>
                  <?php endif; ?>
                  <?php endforeach; ?>
                
                </li>
                
                <?php endif; ?>

                <?php endforeach; ?>
                
                <?php if (MasterPress::current_user_can("create_taxonomies")) : ?>
                <li class="divide"><a href="<?php echo MasterPress::admin_url("taxonomies", "create") ?>" class="create-link"><i></i><?php echo MPV::__create( MPV_Taxonomies::__s() ) ?></a></li>
                <?php endif; ?>
                </ul>
            
              </div>
              <!-- /.body -->
            
            </div>
            <!-- /.col -->
          
            <div class="col field-sets shared-field-sets">
            
            
              <h4><i class="metabox-share"></i>
              <?php if (MasterPress::current_user_can("manage_shared_field_sets")) : ?>
              <a href="<?php echo MasterPress::admin_url("shared-field-sets", "manage") ?>" title="<?php _e("Manage Shared Field Sets", MASTERPRESS_DOMAIN) ?>" class="icon">              
              <?php else : ?>
              <span class="icon">
              <?php endif; ?>
              
              <?php echo WOOF::items_number( count($shared_field_sets), __("<em>No</em> Shared Field Sets", MASTERPRESS_DOMAIN), __("<b>%d</b> Shared Field Set", MASTERPRESS_DOMAIN), __("<b>%d</b> Shared Field Sets", MASTERPRESS_DOMAIN) ) ?>
              
              <?php if (MasterPress::current_user_can("manage_shared_field_sets")) : ?>
              </a>
              <?php else : ?>
              </span>
              <?php endif; ?>
              </h4>
            
              <div class="body">
              
                <ul class="count-<?php echo count($shared_field_sets) ?>">
                <?php foreach ($shared_field_sets as $field_set) : $multiple = $field_set->allow_multiple ? "allow-multiple" : ''; $disabled = $field_set->disabled ? 'disabled ' : ''; $disabled_title = $field_set->disabled ? __('This shared field set is currently disabled', MASTERPRESS_DOMAIN) : ''; ?>
                <li class="<?php echo $disabled ?>shared-field-set-<?php echo $field_set->name ?> linkify" data-name="<?php echo $field_set->name ?>" title="<?php echo $disabled_title ?>">



                  <?php if (MasterPress::current_user_can("manage_shared_field_sets+edit_shared_field_sets")) : ?>
                  <a href="<?php echo MasterPress::admin_url("shared-field-sets", "edit", "id=".$field_set->id) ?>" title="<?php _e("Edit Shared Field Set", MASTERPRESS_DOMAIN) ?>" class="item <?php echo $multiple ?>">
                  <?php else: ?>
                  <div class="item <?php echo $multiple ?>">  
                  <?php endif; ?>

                  <?php if ($multiple) : ?>
                  <i class="metabox-add-remove"></i>
                  <?php else : ?>
                  <i class="metabox"></i>
                  <?php endif; ?>
                  
                  <?php echo $field_set->display_label() ?></a>
                  
                  <?php if (MasterPress::current_user_can("manage_shared_field_sets+edit_shared_field_sets")) : ?>
                  </a>
                  <?php else: ?>
                  </div>
                  <?php endif; ?>
                
                  <?php foreach ($field_set->post_types() as $post_type) : ?> 
                  <span class="post-type-link-<?php echo $post_type->name ?> related-link asterisk"></span>
                  <?php endforeach; ?>

                  <?php foreach ($field_set->taxonomies() as $tax) : ?> 
                  <span class="tax-link-<?php echo $tax->name ?> related-link asterisk"></span>
                  <?php endforeach; ?>
                
                  <?php foreach ($field_set->roles() as $role) : ?> 
                  <span class="role-link-<?php echo $role->id ?> related-link asterisk"></span>
                  <?php endforeach; ?>
                
                </li>
                <?php endforeach; ?>
                
                <?php if (MasterPress::current_user_can("create_shared_field_sets")) : ?>
                <li class="divide"><a href="<?php echo MasterPress::admin_url("shared-field-sets", "create") ?>" class="create-link"><i></i><?php echo MPV::__create( MPV_SharedFieldSets::__s() ) ?></a></li>
                <?php endif; ?>

                </ul>
            
              </div>
              <!-- /.body -->
            
            </div>
            <!-- /.col -->
          
          
          </div>
          <!-- /.row -->
        
          <div class="row">
          
          
          
            <div class="col templates">

              <h4><i class="template"></i>
              <?php if (MasterPress::current_user_can("manage_templates")) : ?>
              <a href="<?php echo MasterPress::admin_url("templates", "manage") ?>" title="<?php _e("Manage Templates", MASTERPRESS_DOMAIN) ?>" class="icon">              
              <?php else : ?>
              <span class="icon">
              <?php endif; ?>
              
              <?php echo WOOF::items_number( count($templates), __("<em>No</em> Templates", MASTERPRESS_DOMAIN), __("<b>%d</b> Template", MASTERPRESS_DOMAIN), __("<b>%d</b> Templates", MASTERPRESS_DOMAIN) ) ?>
              
              <?php if (MasterPress::current_user_can("manage_templates")) : ?>
              </a>
              <?php else : ?>
              </span>
              <?php endif; ?>
              </h4>
              
              
              <div class="body">
              
                <ul class="count-<?php echo count($templates) ?>">
                <?php $count = 0; ?>
                <?php foreach ($templates as $template => $file) : $count++; 
              
                $fsc = count(MPM_TemplateFieldSet::find_by_template( $file ));
              
                ?>
                <li class="linkify" data-name="<?php echo $file ?>">

                  <?php if (MasterPress::current_user_can("manage_templates+edit_templates")) : ?>
                  <a href="<?php echo MasterPress::admin_url("templates", "edit", "id=".$file) ?>" class="item">
                  <?php else: ?>
                  <div class="item">  
                  <?php endif; ?>
                  
                  <?php echo WOOF::truncate($template, "length=20&words=0") ?>
                  
                  <?php if (MasterPress::current_user_can("manage_templates+edit_templates")) : ?>
                  </a>
                  <?php else: ?>
                  </div>  
                  <?php endif; ?>
                  
                  
                  <?php if (MasterPress::current_user_can("manage_templates+manage_template_field_sets")) : ?>
                  <a href="<?php echo MasterPress::admin_url("templates", "manage-field-sets", "parent=".$file) ?>" title="<?php _e("Manage Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></a>
                  <?php else : ?>
                  <div title="<?php _e("Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></div>
                  <?php endif; ?>
                  
                </li>
                <?php endforeach; ?>
                </ul>
            
              </div>
              <!-- /.body -->
            
            </div>
            <!-- /.col -->
          
            <div class="col field-sets site-field-sets">
            
              <h4><i class="sitemap"></i>
              <?php if (MasterPress::current_user_can("manage_site_field_sets")) : ?>
              <a href="<?php echo MasterPress::admin_url("site-field-sets", "manage") ?>" title="<?php _e("Manage Site Field Sets", MASTERPRESS_DOMAIN) ?>" class="icon">
              <?php else : ?>
              <span class="icon">
              <?php endif; ?>
              
              <?php echo WOOF::items_number( count($site_field_sets), __("<em>No</em> Site Field Sets", MASTERPRESS_DOMAIN), __("<b>%d</b> site Field Set", MASTERPRESS_DOMAIN), __("<b>%d</b> site Field Sets", MASTERPRESS_DOMAIN) ) ?>
              
              <?php if (MasterPress::current_user_can("manage_site_field_sets")) : ?>
              </a>
              <?php else : ?>
              </span>
              <?php endif; ?>
              </h4>
              
              <div class="body">
              
                <ul class="count-<?php echo count($site_field_sets) ?>">
                <?php foreach ($site_field_sets as $field_set) : $multiple = $field_set->allow_multiple ? "allow-multiple" : '';  $disabled = $field_set->disabled ? 'disabled ' : ''; $disabled_title = $field_set->disabled ? __('This site field set is currently disabled', MASTERPRESS_DOMAIN) : ''; ?>
                <li class="<?php echo $disabled ?>site-field-set-<?php echo $field_set->name ?> linkify" data-name="<?php echo $field_set->name ?>" title="<?php echo $disabled_title ?>">

                  <?php if (MasterPress::current_user_can("manage_site_field_sets+edit_site_field_sets")) : ?>
                  <a href="<?php echo MasterPress::admin_url("site-field-sets", "edit", "id=".$field_set->id) ?>" title="<?php _e("Edit Site Field Set", MASTERPRESS_DOMAIN) ?>" class="item <?php echo $multiple ?>">
                  <?php else: ?>
                  <div class="item <?php echo $multiple ?>">  
                  <?php endif; ?>

                  <?php if ($multiple) : ?>
                  <i class="metabox-add-remove"></i>
                  <?php else : ?>
                  <i class="metabox"></i>
                  <?php endif; ?>

                  <?php echo $field_set->display_label() ?></a>
                  
                  <?php if (MasterPress::current_user_can("manage_site_field_sets+edit_site_field_sets")) : ?>
                  </a>
                  <?php else: ?>
                  </div>
                  <?php endif; ?>
                  

                </li>
                <?php endforeach; ?>
                <?php if (MasterPress::current_user_can("create_site_field_sets")) : ?>
                <li class="divide"><a href="<?php echo MasterPress::admin_url("site-field-sets", "create") ?>" class="create-link"><i></i><?php echo MPV::__create( __("Site Field Set", MASTERPRESS_DOMAIN) ) ?></a></li>
                <?php endif; ?>
                </ul>
            
              </div>
              <!-- /.body -->
            
            </div>
            <!-- /.col -->

            <div class="col roles">

              <h4><i class="user"></i>
              <?php if (MasterPress::current_user_can("manage_user_roles")) : ?>
              <a href="<?php echo MasterPress::admin_url("roles", "manage") ?>" title="<?php _e("Manage User Roles", MASTERPRESS_DOMAIN) ?>" class="icon">              
              <?php else : ?>
              <span class="icon">
              <?php endif; ?>

              <?php echo WOOF::items_number( count($roles), __("<em>No</em> User Roles", MASTERPRESS_DOMAIN), __("<b>%d</b> User Role", MASTERPRESS_DOMAIN), __("<b>%d</b> User Roles", MASTERPRESS_DOMAIN) ) ?>
              
              <?php if (MasterPress::current_user_can("manage_user_roles")) : ?>
              </a>
              <?php else : ?>
              </span>
              <?php endif; ?>
              </h4>
              
              
              <div class="body">
                
                <?php
                
                // cap checking has side-effects to the roles collection, causing an infinite loop. We need to cache the info here
                
                $role_cache = array();
                
                foreach ($roles as $role) {
                  $role_cache[] = array("name" => $role->name(), "id" => $role->id());
                  
                
                }
                
                ?>
                
                <ul class="count-<?php echo count($roles) ?>">
                <?php foreach ($role_cache as $role) : $count++; 
                $fsc = count(MPM_RoleFieldSet::find_by_role( $role["id"] ));
                ?>
                <li class="linkify" data-name="<?php echo $role["id"] ?>">
                  
                  <?php if (MasterPress::current_user_can("manage_user_roles+edit_user_roles")) : ?>
                  <a href="<?php echo MasterPress::admin_url("roles", "edit", "id=".$role["id"]) ?>" class="item">
                  <?php else: ?>
                  <div class="item">  
                  <?php endif; ?>
                                
                  <i class="user-role"></i>

                  <?php echo WOOF::truncate($role["name"], "length=20&words=0") ?>
                  
                  <?php if (MasterPress::current_user_can("manage_user_roles+edit_user_roles")) : ?>
                  </a>
                  <?php else: ?>
                  </div>  
                  <?php endif; ?>
                  
                  
                  <?php if (MasterPress::current_user_can("manage_user_roles+manage_user_role_field_sets")) : ?>
                  <a href="<?php echo MasterPress::admin_url("roles", "manage-field-sets", "parent=".$role["id"]) ?>" title="<?php _e("Manage Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></a>
                  <?php else : ?>
                  <div title="<?php _e("Field Sets", MASTERPRESS_DOMAIN) ?>" class="field-sets count-<?php echo $fsc ?>"><?php echo $fsc ?></div>
                  <?php endif; ?>
                  
                  
                  <?php foreach ($shared_field_sets as $fs) : ?>
                  <?php if ($fs->visible_in("roles", $role["id"])) : ?>
                  <span class="shared-field-set-link-<?php echo $fs->name ?> related-link asterisk"></span>
                  <?php endif; ?>
                  <?php endforeach; ?>
                
                </li>
                <?php endforeach; ?>
                <?php if (MasterPress::current_user_can("create_user_roles")) : ?>
                <li class="divide"><a href="<?php echo MasterPress::admin_url("roles", "create") ?>" class="create-link"><i></i><?php echo MPV::__create( __("User Role", MASTERPRESS_DOMAIN) ) ?></a></li>
                <?php endif; ?>
                </ul>
            
              </div>
              <!-- /.body -->
            
            </div>
            <!-- /.col -->
                    
          </div>
          <!-- /.row -->
        
        </div>
        <!-- /#masterplan-overview -->


<!-- EXPORT -->

      <?php if (MasterPress::current_user_can("export_masterplan")) : ?>

        <div id="masterplan-export" class="masterplan-export tab-panel <?php echo $tab == "export" ? 'current' : '' ?>">

        <?php MPV::form_open() ?>

          <div id="export-progress" class="progress">
            <?php _e("Exporting Masterplan. Please wait&hellip;", MASTERPRESS_DOMAIN) ?>
          </div>
          <!-- /#export-progress -->
          
          <div id="export-summary" class="summary">
            
            <div id="export-download">

              <p><?php _e("Export completed successfully", MASTERPRESS_DOMAIN) ?></p>
              <a id="export-file-download" href="#" data-message="<?php _e( "Download %s", MASTERPRESS_DOMAIN) ?>"><?php printf( __( "Download %s", MASTERPRESS_DOMAIN ), "file.zip" ) ?></a>
              
            </div>
            <!-- /#export-download -->
            
            
            <div id="extras-summary" class="extras-summary">
              
              <p>
                <?php _e("Note: the following dependent files were included in the Masterplan package", MASTERPRESS_DOMAIN) ?>
              </p>
                
              <div id="extras-icons">
                <h4><?php _e("Icons", MASTERPRESS_DOMAIN); ?></h4>
                
                <ul>
                  
                </ul>
              </div>
              <!-- /#extras-icons -->

              <div id="extras-types">
                <h4><?php _e("Field Types", MASTERPRESS_DOMAIN); ?></h4>
                
                <ul>
                  
                </ul>
              </div>
              <!-- /#extras-field-types -->
              
              
            </div>
            
          </div>
          <!-- /#export-summary -->

          <div id="export-ui">
            
          <div id="export-package">
            
            <div class="title">
              <h4 class="package-file"><i class="zip"></i><?php _e("Package File", MASTERPRESS_DOMAIN) ?></h4>
              <button id="button-export" type="submit" class="button-export simple-primary">Export</button>
            </div>
            <!-- /.title -->
            
            <div id="f-export-filename" class="f">
            
              <label for="export_filename"><?php _e("Name:", MASTERPRESS_DOMAIN) ?></label>
            
              <div class="fw">
                <input id="export_filename" spellcheck="false" name="export_filename" type="text" value="<?php echo $wf->sanitize( $wf->site_name() ) ?>" class="text" />
                <span id="export_filename_extension" class="note"><?php echo ".".$wf->format_date("[date-time-sortable]") ?>.masterplan.zip</span>
              </div>
              <!-- /.fw -->
            
            </div>
            <!-- /.f -->

          </div>
          <!-- /#export-package -->
          
          <div id="export-readme">
            
            <h4 class="readme"><i class="document-text"></i><?php _e('Read Me<span> - this Markdown-formatted text will be stored in <span class="tt">README.markdown</span> inside the Masterplan package</span>', MASTERPRESS_DOMAIN); ?></h4>
            
            <div id="f-export_readme">
<textarea id="export_readme" name="export_readme">
# Masterplan for <?php echo $wf->sites()->first()->name ?> #

+ By: <?php echo $wf->the_user()->fullname() ?>

+ Created: <?php echo $wf->format_date("[date-time-long]") ?>


-------------------------------------------------

</textarea>
          </div>
          
          </div>
          <!-- /#export-readme -->
          
          <div id="export-items">
            
            <div class="title-buttons">
              <h4 class="export-items"><i class="hand-point"></i><?php _e("Export Items<span> - check the items you wish to include in the Masterplan package</span>", MASTERPRESS_DOMAIN); ?></h4>
              <div class="buttons">
                <button id="export-select-all" type="button" class="button">Select <span class="all">All</span></button>
                <button id="export-select-none" type="button" class="button">Select <span class="none">None</span></button>
              </div>
              
            </div>
            <!-- /.title-buttons -->
            
            <div class="fsi">
            <div class="fsibv">
              <div class="fsit">
                <h4 class="post-types"><i class="pin"></i><?php _e("Post Types", MASTERPRESS_DOMAIN) ?></h4>
                <input id="post-types-check" name="post-types-all" type="checkbox" class="checkbox" checked="checked" />
              </div>
              
              <div class="fsic">
                
                <ul class="object-tree post-types">
                <?php foreach ($post_types as $post_type) : ?>
                  
                <?php if ($post_type->still_registered()) : ?>

                <li class="post-type-<?php echo $post_type->name ?>">
                  <input id="ref-post-type-<?php echo $post_type->name ?>" name="ref[post_types][<?php echo $post_type->name ?>][selected]" checked="checked" value="true" type="hidden" />
                  <input id="export-post-type-<?php echo $post_type->name ?>" name="export[post_types][<?php echo $post_type->name ?>][selected]" checked="checked" value="true" type="checkbox" class="checkbox" />
                  <label for="export-post-type-<?php echo $post_type->name ?>" class="mp-icon-post-type mp-icon-post-type-<?php echo $post_type->name ?>"><?php echo $post_type->display_label() ?></label>

                <?php $field_sets = $post_type->post_type_field_sets() ?>
                
                <?php if (count($field_sets)) : ?>
                  <ul class="field-sets">
                  <?php foreach ($field_sets as $field_set) : $class = $field_set->allow_multiple ? 'metabox-add-remove-large' : 'metabox-large'; ?>
                  <li class="field-set<?php echo $class ?>">
                    <input id="export-post-type-<?php echo $post_type->name ?>-field-set-<?php echo $field_set->id ?>" checked="checked" name="export[post_types][<?php echo $post_type->name ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="checkbox" class="checkbox" />
                    <input id="ref-post-type-<?php echo $post_type->name ?>-field-set-<?php echo $field_set->id ?>" name="ref[post_types][<?php echo $post_type->name ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="hidden" />
                    <label for="export-post-type-<?php echo $post_type->name ?>-field-set-<?php echo $field_set->id ?>" class="field-set"><i class="<?php echo $class ?>"></i><?php echo $field_set->display_label() ?></label>
                    
                    <?php $fields = $field_set->fields() ?>
                
                    <?php if (count($fields)) : ?>
                      <ul class="fields">
                      <?php foreach ($fields as $field) : ?>
                      <?php if ($type_class = MPFT::type_class($field->type)) : ?>
                      <li class="field">
                        <input id="export-post-type-<?php echo $post_type->name ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="export[post_types][<?php echo $post_type->name ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="checkbox" class="checkbox" />
                        <input id="ref-post-type-<?php echo $post_type->name ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="ref[post_types][<?php echo $post_type->name ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="hidden" />
                        <label for="export-post-type-<?php echo $post_type->name ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" class="mp-icon-field-type-<?php echo $field->type ?>"><?php echo $field->display_label() ?></label>
                      </li>  
                      <?php endif; ?>
                      <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                
                  </li>  
                  <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                
                </li>
                
                <?php endif; ?>
                
                <?php endforeach; ?>  
                </ul>
                
                
              </div>
              <!-- /.fsic -->
              
            </div>
            </div>
            <!-- /.fsi -->
            
            
            
            <div class="fsi">
            <div class="fsibv">
              <div class="fsit">
                <h4 class="taxonomies"><i class="tag"></i><?php _e("Taxonomies", MASTERPRESS_DOMAIN) ?></h4>
                <input id="taxonomies-check" name="taxonomies-all" type="checkbox" class="checkbox" checked="checked" />
              </div>
              
              <div class="fsic">
                
                <ul class="object-tree taxonomies">
                <?php foreach ($taxonomies as $tax) : ?>

                <?php if ($tax->still_registered()) : ?>

                <li>
                  <input id="ref-taxonomy-<?php echo $tax->name ?>" name="ref[taxonomies][<?php echo $tax->name ?>][selected]" value="true" type="hidden" />
                  <input id="export-taxonomy-<?php echo $tax->name ?>" name="export[taxonomies][<?php echo $tax->name ?>][selected]" checked="checked" value="true" type="checkbox" class="checkbox" />
                  <label for="export-taxonomy-<?php echo $tax->name ?>" class="mp-icon-taxonomy mp-icon-taxonomy-<?php echo $tax->name ?>"><?php echo $tax->display_label() ?></label>

                <?php $field_sets = $tax->taxonomy_field_sets() ?>
                
                <?php if (count($field_sets)) : ?>
                  <ul class="field-sets">
                  <?php foreach ($field_sets as $field_set) : $class = $field_set->allow_multiple ? 'metabox-add-remove-large' : 'metabox-large'; ?>
                  <li class="field-set<?php echo $class ?>">
                    <input id="ref-taxonomy-<?php echo $tax->name ?>-field-set-<?php echo $field_set->id ?>" name="ref[taxonomies][<?php echo $tax->name ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="hidden" />
                    <input id="export-taxonomy-<?php echo $tax->name ?>-field-set-<?php echo $field_set->id ?>" name="export[taxonomies][<?php echo $tax->name ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" checked="checked" type="checkbox" class="checkbox" />
                    <label for="export-taxonomy-<?php echo $tax->name ?>-field-set-<?php echo $field_set->id ?>" class="field-set"><i class="<?php echo $class ?>"></i><?php echo $field_set->display_label() ?></label>

                    <?php $fields = $field_set->fields() ?>
                
                    <?php if (count($fields)) : ?>
                      <ul class="fields">
                      <?php foreach ($fields as $field) : ?>
                      <?php if ($type_class = MPFT::type_class($field->type)) : ?>
                      <li class="field">
                        <input id="ref-taxonomy-<?php echo $tax->name ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" name="ref[taxonomies][<?php echo $tax->name ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="hidden" />
                        <input id="export-taxonomy-<?php echo $tax->name ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="export[taxonomies][<?php echo $tax->name ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="checkbox" class="checkbox" />
                        <label for="export-taxonomy-<?php echo $tax->name ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>"  class="mp-icon-field-type-<?php echo $field->type ?>"><?php echo $field->display_label() ?></label>
                      </li>  
                      <?php endif; ?>
                      <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                
                  </li>  
                  <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                
                </li>
                <?php endif; ?>

                <?php endforeach; ?>  
                </ul>
                
                
              </div>
              <!-- /.fsic -->
              
            </div>
            </div>
            <!-- /.fsi -->
            
            <?php if (count($shared_field_sets)) : ?>

            <div class="fsi">
            <div class="fsibv">
              <div class="fsit">
                <h4 class="shared-field-sets"><i class="metabox-share"></i><?php _e("Shared Field Sets", MASTERPRESS_DOMAIN) ?></h4>
                <input id="shared-field-sets-check" name="shared-field-sets-all" type="checkbox" class="checkbox" checked="checked" />
              </div>
              
              <div class="fsic">
                
                <ul class="object-tree shared-field-sets field-sets">
                <?php foreach ($shared_field_sets as $field_set) : $class = $field_set->allow_multiple ? 'metabox-add-remove-large' : 'metabox-large'; ?>
                <li class="field-set<?php echo $class ?>">
                  <input id="ref-shared-field-set-<?php echo $field_set->id ?>" name="ref[shared_field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="hidden" />
                  <input id="export-shared-field-set-<?php echo $field_set->id ?>" name="export[shared_field_sets][<?php echo $field_set->id ?>][selected]" value="true" checked="checked" type="checkbox" class="checkbox" />
                  <label for="export-shared-field-set-<?php echo $field_set->id ?>" class="field-set"><i class="<?php echo $class ?>"></i><?php echo $field_set->display_label() ?></label>

                  <?php $fields = $field_set->fields() ?>
              
                  <?php if (count($fields)) : ?>
                    <ul class="fields">
                    <?php foreach ($fields as $field) : ?>
                    <?php if ($type_class = MPFT::type_class($field->type)) : ?>
                    <li class="field">
                      <input id="ref-shared-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="ref[shared_field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="hidden" />
                      <input id="export-shared-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="export[shared_field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="checkbox" class="checkbox" />
                      <label for="export-shared-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" class="mp-icon-field-type-<?php echo $field->type ?>"><?php echo $field->display_label() ?></label>
                    </li>  
                    <?php endif; ?>
                    <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
              
                </li>  
                <?php endforeach; ?>
                </ul>
                
                
              </div>
              <!-- /.fsic -->
              
            </div>
            </div>
            <!-- /.fsi -->
            
            <?php endif; ?>
          
          
            <?php if (count($site_field_sets)) : ?>

            <div class="fsi">
            <div class="fsibv">
              <div class="fsit">
                <h4 class="site-field-sets"><i class="sitemap"></i><?php _e("Site Field Sets", MASTERPRESS_DOMAIN) ?></h4>
                <input id="site-field-sets-check" name="site-field-sets-all" type="checkbox" class="checkbox" checked="checked" />
              </div>
              
              <div class="fsic">
                
                  <ul class="object-tree site-field-sets field-sets">
                  <?php foreach ($site_field_sets as $field_set) : $class = $field_set->allow_multiple ? 'metabox-add-remove-large' : 'metabox-large'; ?>
                  <li class="field-set<?php echo $class ?>">
                    <input id="ref-site-field-set-<?php echo $field_set->id ?>" name="ref[site_field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="hidden" />
                    <input id="export-site-field-set-<?php echo $field_set->id ?>" name="export[site_field_sets][<?php echo $field_set->id ?>][selected]" value="true" checked="checked" type="checkbox" class="checkbox" />
                    <label for="export-site-field-set-<?php echo $field_set->id ?>" class="field-set"><i class="<?php echo $class ?>"></i><?php echo $field_set->display_label() ?></label>

                    <?php $fields = $field_set->fields() ?>
                
                    <?php if (count($fields)) : ?>
                      <ul class="fields">
                      <?php foreach ($fields as $field) : ?>
                      <?php if ($type_class = MPFT::type_class($field->type)) : ?>
                      <li class="field">
                        <input id="ref-site-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="ref[site_field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="hidden" />
                        <input id="export-site-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="export[site_field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="checkbox" class="checkbox" />
                        <label for="export-site-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" class="mp-icon-field-type-<?php echo $field->type ?>"><?php echo $field->display_label() ?></label>
                      </li>  
                      <?php endif; ?>
                      <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                
                  </li>  
                  <?php endforeach; ?>
                  </ul>
                
                
              </div>
              <!-- /.fsic -->
              
            </div>
            </div>
            <!-- /.fsi -->
            
            <?php endif; ?>
            
          
          
            <div class="fsi">
            <div class="fsibv">
              <div class="fsit">
                <h4 class="templates"><i class="template"></i><?php _e("Templates", MASTERPRESS_DOMAIN) ?></h4>
                <input id="templates-check" name="templates-all" type="checkbox" class="checkbox" checked="checked" />
              </div>
              
              <div class="fsic">
                
                <ul class="object-tree">
              

                <?php foreach ($templates as $template => $file) : $id = $wf->sanitize($file); ?>
                <li class="template-<?php echo $id ?>">

                  <input id="ref-template-<?php echo $wf->sanitize($file) ?>" name="ref[templates][<?php echo $file ?>][selected]" checked="checked" value="true" type="hidden" />
                  <input id="export-template-<?php echo $wf->sanitize($file) ?>" name="export[templates][<?php echo $file ?>][selected]" checked="checked" value="true" type="checkbox" class="checkbox" />
                  <label for="export-template-<?php echo $wf->sanitize($file) ?>" class="template"><i class="template"></i><?php echo $template ?></label>


                  <?php $field_sets = MPM_TemplateFieldSet::find_by_template( $file ); ?>

                <?php if (count($field_sets)) : ?>
                  <ul class="field-sets">
                  <?php foreach ($field_sets as $field_set) : $class = $field_set->allow_multiple ? 'metabox-add-remove-large' : 'metabox-large'; ?>
                  <li class="field-set<?php echo $class ?>">
                    <input id="ref-template-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>" name="ref[templates][<?php echo $file ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="hidden" />
                    <input id="export-template-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>" checked="checked" name="export[templates][<?php echo $file ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="checkbox" class="checkbox" />
                    <label for="export-template-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>" class="field-set"><i class="<?php echo $class ?>"></i><?php echo $field_set->display_label() ?></label>
                    
                    <?php $fields = $field_set->fields(); ?>
                
                    <?php if (count($fields)) : ?>
                      <ul class="fields">
                      <?php foreach ($fields as $field) : ?>
                      <?php if ($type_class = MPFT::type_class($field->type)) : ?>
                      <li class="field">
                        <input id="ref-template-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" name="ref[templates][<?php echo $file ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="hidden" />
                        <input id="export-template-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="export[templates][<?php echo $file ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="checkbox" class="checkbox" />
                        <label for="export-template-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" class="mp-icon-field-type-<?php echo $field->type ?>"><?php echo $field->display_label() ?></label>
                      </li>  
                      <?php endif; ?>
                      <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                
                  </li>  
                  <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                
                </li>

                <?php endforeach; ?>  

                </ul>
                
                
              </div>
              <!-- /.fsic -->
              
            </div>
            </div>
            <!-- /.fsi -->
            
            
            
            <div class="fsi">
            <div class="fsibv">
              <div class="fsit">
                <h4 class="roles"><i class="user-role"></i><?php _e("User Roles", MASTERPRESS_DOMAIN) ?></h4>
                <input id="roles-check" name="roles-all" type="checkbox" class="checkbox" checked="checked" />
              </div>
              
              <div class="fsic">
                
                <ul class="object-tree">
              

                <?php foreach ($roles as $role) : $id = $role->id() ?>
                <li>
                  <input id="ref-role-<?php echo $id ?>" name="ref[roles][<?php echo $id ?>][selected]" checked="checked" value="true" type="hidden" />
                  <input id="export-role-<?php echo $id ?>" name="export[roles][<?php echo $id ?>][selected]" checked="checked" value="true" type="checkbox" class="checkbox" />
                  <label for="export-role-<?php echo $id ?>" class="role"><i class="user-role"></i><?php echo $role->name ?></label>


                <?php $field_sets = MPM_RoleFieldSet::find_by_role( $id ) ?>

                <?php if (count($field_sets)) : ?>
                  <ul class="field-sets">
                  <?php foreach ($field_sets as $field_set) : $class = $field_set->allow_multiple ? 'metabox-add-remove-large' : 'metabox-large'; ?>
                  <li class="field-set<?php echo $class ?>">
                    <input id="ref-role-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>" checked="checked" name="ref[roles][<?php echo $id ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="hidden" />
                    <input id="export-role-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>" checked="checked" name="export[roles][<?php echo $id ?>][field_sets][<?php echo $field_set->id ?>][selected]" value="true" type="checkbox" class="checkbox" />
                    <label for="export-role-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>" class="field-set"><i class="<?php echo $class ?>"></i><?php echo $field_set->display_label() ?></label>
                    
                    <?php $fields = $field_set->fields(); ?>
                
                    <?php if (count($fields)) : ?>
                      <ul class="fields">
                      <?php foreach ($fields as $field) : ?>
                      <?php if ($type_class = MPFT::type_class($field->type)) : ?>
                      <li class="field">
                        <input id="ref-role-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" name="ref[roles][<?php echo $id ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="hidden" />
                        <input id="export-role-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" checked="checked" name="export[roles][<?php echo $id ?>][field_sets][<?php echo $field_set->id ?>][fields][<?php echo $field->id ?>]" value="true" type="checkbox" class="checkbox" />
                        <label for="export-role-<?php echo $id ?>-field-set-<?php echo $field_set->id ?>-field-<?php echo $field->id ?>" class="mp-icon-field-type-<?php echo $field->type ?>"><?php echo $field->display_label() ?></label>
                      </li>  
                      <?php endif; ?>
                      <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                
                  </li>  
                  <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                
                </li>

                <?php endforeach; ?>  

                </ul>
                
                
              </div>
              <!-- /.fsic -->
              
            </div>
            </div>
            <!-- /.fsi -->
            
          
          
            
          </div>
          <!-- /#export-items -->
          
          
          </div>
          <!-- /#export-ui -->

          
          
        <?php MPV::form_close() ?>

              
        </div>
        <!-- /#masterplan-export -->
        
      <?php endif; ?>


      <?php if (MasterPress::current_user_can("import_masterplan")) : ?>

        <div id="masterplan-import" class="tab-panel <?php echo $tab == "import" ? 'current' : '' ?>">

          <?php if (!MPV::is_postback()) : ?>

          <?php MPV::form_open() ?>
            
          <div id="import-file-wrap">
            
            <div class="title">
              <h4 class="upload"><i class="upload"></i><?php _e("Package Upload", MASTERPRESS_DOMAIN) ?></h4>
            </div>
            <!-- /.title -->

            
            <label id="label-import_file" for="import_file"><?php _e("Upload a Masterplan zip package to begin.<br  /><b>Note:</b> you will be able to review the contents of the package before going ahead with the import.") ?></label>
          
            <div id="import-file-uploader" class="file-uploader { allowedExtensions: ['zip'], ids: { drop: 'import_file_drop_area' }, input: '#import_file', inputName: 'import_file_ul', base_url: '<?php echo MASTERPRESS_GLOBAL_CONTENT_URL ?>', params: { dir: 'tmp/' }, limit: 1, lang: { buttonReplace: '<?php _e("Select a Different File&hellip;") ?>', buttonChoose: '<?php _e("Choose from Computer&hellip;", MASTERPRESS_DOMAIN) ?>'} }">
            
              <div id="import_file_drop_area" class="drop-area"><?php _e("Drop file here to upload", MASTERPRESS_DOMAIN) ?></div>

              <?php 
            
              $file_name = __("( None )", MASTERPRESS_DOMAIN);
              $file_class = "name-none";

              ?>
            
              <div class="file">
                <span class="preview"></span><span data-none="<?php echo __("( None )", MASTERPRESS_DOMAIN) ?>" class="name <?php echo $file_class ?>"><?php echo $file_name ?></span>
              </div>
            
              <input id="import_file" name="import_file" value="" type="hidden" />
              <div class="uploader-ui"></div>
            
            </div>
            <!-- /.file-uploader -->
          
          </div>
          <!-- /#import-file-wrap -->
          
          <p id="import-fetching-summary"><?php _e("Fetching Masterplan info - Please wait&hellip;", MASTERPRESS_DOMAIN); ?></p>
          

          <div id="import-confirmation">
            
            <div class="title">
              <h4 class="upload"><i class="tick-circle"></i><?php _e("Confirmation", MASTERPRESS_DOMAIN) ?> - <span><?php _e("select import options and review the uploaded package", MASTERPRESS_DOMAIN) ?></span></h4>
              <button id="button-import" type="submit" class="button-import simple-primary">Import</button>
            </div>
            <!-- /.title -->
                
            <div class="content">
                        
              <div class="f">
                <div class="fw">
                  <input id="import-backup" name="import_backup" value="yes" checked="checked" type="checkbox" class="checkbox" />
                  <label for="import-backup" class="checkbox"><?php _e("<strong>Backup the existing setup</strong> before importing ( highly recommended )", MASTERPRESS_DOMAIN); ?></label>
                </div>
              </div>
              <!-- /.f -->

              <div id="f-types-overwrite" class="f">
                <div class="fw">
                  <input id="import-types-overwrite" name="import_types_overwrite" value="yes" checked="checked" type="checkbox" class="checkbox" />
                  <label for="import-types-overwrite" class="checkbox"><?php _e("<strong>Overwrite</strong> existing field type extensions with those included in the imported package", MASTERPRESS_DOMAIN); ?></label>
                </div>
              </div>
              <!-- /.f -->
            
              <div class="f f-mode">
                <p>
                  <?php _e("When items are present in the existing setup, but not in the imported Masterplan:", MASTERPRESS_DOMAIN) ?>
                </p>
                
                <div class="fw">
                  <input id="import-mode-replace" name="import_mode" value="replace" checked="checked" type="radio" class="radio" />
                  <label for="import-mode-replace" class="radio"><?php _e('Remove the items from the existing setup ( <strong class="replace">completely replace</strong> the existing setup )', MASTERPRESS_DOMAIN); ?></label>
                </div>

                <div class="fw">
                  <input id="import-mode-append" name="import_mode" value="append" type="radio" class="radio" />
                  <label for="import-mode-append" class="radio"><?php _e('Keep the items in the existing setup ( <strong class="append">append to</strong> the existing setup )', MASTERPRESS_DOMAIN); ?></label>
                </div>

                
              </div>
              <!-- /.f -->
            
            
            </div>
            <!-- /.content -->
            
          </div>
                  

          
          <div id="import-preview" class="import-preview">

            <input type="hidden" name="tab" value="import" />
            <input id="import-masterplan" type="hidden" name="import_masterplan" value="" />
            
            
            <?php
            
            // build a template list for the importer, since get_page_templates() is not available early in execution
            
            foreach (get_page_templates() as $template => $file) :
              ?>
              <input name="templates[<?php echo $file ?>]" type="hidden" value="<?php echo $template ?>" />
            <?php endforeach; ?>
            
            <div id="import-rep">
              
              <div class="fsi post-types">
              <div class="fsibv">
                <div class="fsit">
                  <h4 class="post-types"><i class="pin"></i><?php _e("Post Types", MASTERPRESS_DOMAIN) ?></h4>
                </div>
              
                <div class="fsic">
                
                  <ul class="object-tree post-types">
                 
                  </ul>
                
                </div>
                <!-- /.fsic -->
              
              </div>
              </div>
              <!-- /.fsi -->
            
            
            
              <div class="fsi taxonomies">
              <div class="fsibv">
                <div class="fsit">
                  <h4 class="taxonomies"><i class="tag"></i><?php _e("Taxonomies", MASTERPRESS_DOMAIN) ?></h4>
                </div>
              
                <div class="fsic">
                
                  <ul class="object-tree taxonomies">
                
                  </ul>
                
                
                </div>
                <!-- /.fsic -->
              
              </div>
              </div>
              <!-- /.fsi -->
            
              <div class="fsi shared-field-sets">
              <div class="fsibv">
                <div class="fsit">
                  <h4 class="shared-field-sets"><i class="metabox-share"></i><?php _e("Shared Field Sets", MASTERPRESS_DOMAIN) ?></h4>
                </div>
              
                <div class="fsic">
                  <ul class="object-tree shared-field-sets field-sets">
                  </ul>
                </div>
                <!-- /.fsic -->
              
              </div>
              </div>
              <!-- /.fsi -->
          
              <div class="fsi site-field-sets">
              <div class="fsibv">
                <div class="fsit">
                  <h4 class="site-field-sets"><i class="sitemap"></i><?php _e("Site Field Sets", MASTERPRESS_DOMAIN) ?></h4>
                </div>
              
                <div class="fsic">
                
                    <ul class="object-tree site-field-sets field-sets">
                    </ul>
                
                </div>
                <!-- /.fsic -->
              
              </div>
              </div>
              <!-- /.fsi -->
          
          
              <div class="fsi templates">
              <div class="fsibv">
                <div class="fsit">
                  <h4 class="templates"><i class="template"></i><?php _e("Templates", MASTERPRESS_DOMAIN) ?></h4>
                </div>
              
                <div class="fsic">
                
                  <ul class="object-tree">
                  </ul>
                
                </div>
                <!-- /.fsic -->
              
              </div>
              </div>
              <!-- /.fsi -->
            
            
            
              <div class="fsi roles">
              <div class="fsibv">
                <div class="fsit">
                  <h4 class="roles"><i class="user-role"></i><?php _e("User Roles", MASTERPRESS_DOMAIN) ?></h4>
                </div>
                
                <div class="fsic">
                
                  <ul class="object-tree">
                  </ul>
                
                </div>
                <!-- /.fsic -->
              
              </div>
              </div>
              <!-- /.fsi -->
            
              
            </div>
            <!-- /#import-rep -->
              

          </div>
          <!-- /#import-summary -->

          <?php MPV::form_close() ?>
          
          <?php else: ?>
          
          
          
          <?php endif; ?>
        
        </div>
        <!-- /#masterplan-import -->
        
      <?php endif; ?>

      <?php if (MasterPress::current_user_can("backup_masterplan")) : ?>
        
        <div id="masterplan-backup" class="masterplan-export tab-panel" style="display: none;">
          
          <ul class="mp-messages">
            <li class="notification"><?php _e("Note - this utility does <b>not</b> backup your WordPress content.", MASTERPRESS_DOMAIN) ?></li>
          </ul>
          
          
          <?php MPV::form_open() ?>

            <div id="backup-progress" class="progress">
              <?php _e("Backing up Masterplan. Please wait&hellip;", MASTERPRESS_DOMAIN) ?>
            </div>
            <!-- /#backup-progress -->

            <div id="backup-summary" class="summary">

              <div id="backup-message">
                <p><?php _e("Backup created successfully", MASTERPRESS_DOMAIN) ?></p>
              </div>
              <!-- /#backup-message -->


              <div id="backup-extras-summary" class="extras-summary">
                <p>
                  <?php _e("Note: the following dependent files were included in the Masterplan package", MASTERPRESS_DOMAIN) ?>
                </p>

                <div id="backup-extras-icons">
                  <h4><?php _e("Icons", MASTERPRESS_DOMAIN); ?></h4>

                  <ul>

                  </ul>
                </div>
                <!-- /#backup-extras-icons -->

                <div id="backup-extras-types">
                  <h4><?php _e("Field Types", MASTERPRESS_DOMAIN); ?></h4>

                  <ul>

                  </ul>
                </div>
                <!-- /#backup-extras-field-types -->

              </div>
              <!-- /#backup-extras-summary -->

            </div>

            <div id="backup-ui">

            <div class="title">
              <h4 class="package-file"><i class="zip"></i><?php _e("Package File", MASTERPRESS_DOMAIN) ?></h4>
              <button id="button-backup" type="submit" class="button-export simple-primary">Backup</button>
            </div>
            <!-- /.title -->

            <div id="f-backup-filename" class="f">
              
              <label for="backup_filename"><?php _e("Name:", MASTERPRESS_DOMAIN) ?></label>

              <div class="fw">
                <span id="backup_filename_prefix" class="note">_backup.</span>
                <input id="backup_filename" spellcheck="false" name="backup_filename" type="text" value="" class="text" />
                <input id="backup_filename_suffix" spellcheck="false" name="backup_filename_suffix" type="hidden" value="<?php echo ".".$wf->format_date("[date-time-sortable]") ?>.masterplan.zip" class="text" />
                <span id="backup_filename_extension" class="note"><?php echo ".".$wf->format_date("[date-time-sortable]") ?>.masterplan.zip</span>
              </div>
              <!-- /.fw -->

              <p id="backup_note"><?php _e("Use the editable field as a way to tag the backup for reference later ( optional )") ?></p>

            </div>
            <!-- /.f -->


            <div id="backup-readme">

              <h4 class="readme"><i class="document-text"></i><?php _e('Read Me<span> - this Markdown-formatted text will be stored in <span class="tt">README.markdown</span> inside the Masterplan backup</span>', MASTERPRESS_DOMAIN); ?></h4>

              <div id="f-backup_readme">
<textarea id="backup_readme" name="backup_readme">
# Masterplan Backup for <?php echo $wf->sites()->first()->name ?> #

+ By: <?php echo $wf->the_user()->fullname() ?>

+ Created: <?php echo $wf->format_date("[date-time-long]") ?>


-------------------------------------------------

</textarea>
            </div>

            </div>
            <!-- /#backup-readme -->
          
          </div>
          
          <?php MPV::form_close() ?>
          
        </div>
        <!-- /#masterplan-backup -->

      <?php endif; ?>

      <?php if (MasterPress::current_user_can("restore_masterplan")) : ?>
        
        <?php if (count($backups)) : ?>

        <div id="masterplan-restore" class="tab-panel <?php echo $tab == "restore" ? 'current' : '' ?>">

        <?php MPV::form_open() ?>

          <div class="title">
            <h4 class="package-file"><i class="zip"></i><?php _e("Package Selection", MASTERPRESS_DOMAIN) ?> - <span><?php _e("select a backup package to restore from", MASTERPRESS_DOMAIN) ?></span></h4>
            <button id="button-restore" type="submit" class="button-restore simple-primary">Restore</button>
          </div>
          <!-- /.title -->

          <div id="f-restore-file" class="f">
           
          <label for="restore-masterplan" class="select"><?php _e("File:", MASTERPRESS_DOMAIN) ?></label>
          
          <div class="fw">
          
          <select id="restore-masterplan" data-empty="<?php _e("Please Select a Masterplan to restore", MASTERPRESS_DOMAIN) ?>" name="restore_masterplan">
            <option value=""><?php _e("Select a backup package") ?></option>
            <?php foreach ( $backups as $backup ) : ?>
            <option value="<?php echo $backup["file"] ?>"><?php echo $backup["label"] ?></option>
            <?php endforeach; ?>
          </select>
               
                   
          </div>
                 
        
          <?php MPV::form_close() ?>
  
        </div>
        <!-- /#masterplan-restore -->
        
        <?php endif; ?>
      
      <?php endif; ?>
      
      </div>
      </div>

    </div>
    <!-- /.fs .fs-masterplan -->
    


  <?php
  }
}

?>