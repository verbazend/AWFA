<?php

class MPV_PostTypes extends MPV {

  public static function __s() {
    return __("Post Type", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("Post Types", MASTERPRESS_DOMAIN);
  }
  
  public function confirm_delete($post_type) {
    ?>
    <div class="panel delete-panel delete-post-type-panel">
      <?php $this->form_open() ?> 
      <div class="panel-content">
        <header class="title">
          <h1><?php printf( __("Are you sure you want to delete the <em>%s</em> post type? This action is not reversible!", MASTERPRESS_DOMAIN), $post_type->display_label() ); ?></h1>

          <div class="actions">
            <button class="button-primary button-delete" type="submit"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></button>
            <?php echo MPV::action_link("post-types", "manage", __("Cancel", MASTERPRESS_DOMAIN), "id=".$post_type->id, array( "class" => "button" )); ?>
          </div>  

        </header>

        <?php
          $post_count = $post_type->post_count();
          $meta_count = $post_type->meta_count();
          $field_set_count = $post_type->field_set_count();
          
          $not_this_post_type = array();
          
          foreach (MPM_PostType::find(array("orderby" => "_builtin,name")) as $pt) {
            if ($pt->name != $post_type->name) {
              $not_this_post_type[] = $pt;
            }
          }
            
          $dep_count = $post_count + $meta_count + $field_set_count;
          
        ?>
        
        <?php if ( ( $dep_count > 0 ) || $post_type->_external) : ?>

        <div class="panel-divider"><span><span>&nbsp;</span></span></div>
        
        <div class="content">
        
        
          <?php if ($post_type->_external) : ?>
          <p class="soft-warning"><i class="warning-octagon"></i><?php _e("This post type was not created by MasterPress and will not be removed upon deletion if it is still externally defined in a plug-in or theme.", MASTERPRESS_DOMAIN) ?></p>
          <?php endif; ?>

          <?php if ($dep_count > 0) : ?>
            
          <p>
          <?php echo __("This post type has:", MASTERPRESS_DOMAIN) ?>
          </p>
          
          <ul class="items">
          <?php if ($post_count > 0) : ?><li><?php echo MPU::__items($post_count, __("%d post record", MASTERPRESS_DOMAIN), __("%d post records", MASTERPRESS_DOMAIN)) ?></li><?php endif; ?>
          <?php if ($meta_count > 0) : ?><li><?php echo MPU::__items($meta_count, __("%d custom field (meta) data records", MASTERPRESS_DOMAIN), __("%d custom field (meta) data records", MASTERPRESS_DOMAIN)) ?></li><?php endif; ?>
          <?php if ($field_set_count > 0) : ?><li><?php echo MPU::__items($field_set_count, __("%d field set definition", MASTERPRESS_DOMAIN), __("%d field set definitions", MASTERPRESS_DOMAIN)) ?></li><?php endif; ?>
          </ul>
        
          <p>
          <?php _e("Please indicate how you would like to handle this related information upon deletion:", MASTERPRESS_DOMAIN); ?>
          <?php if ($meta_count > 0 && is_multisite()) : ?>
          <?php _e("<br /><b>Note:</b> If you choose to <em>Delete</em> Meta Data, it will only be removed from the <b>current site</b> in your multisite network.", MASTERPRESS_DOMAIN); ?>
          <?php endif; ?>
          </p>
          
          <?php if ($post_count > 0) : ?>
            
          <div id="f-posts" class="f">
            <span class="label label-icon"><i class="pins"></i><?php _e("Existing Posts:", MASTERPRESS_DOMAIN) ?></span>
            <div class="fw">
              <input id="posts_delete" name="posts" type="radio" value="delete" checked="checked" class="radio" /> 
              <label for="posts_delete" class="radio"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></label>
              <input id="posts_reassign" name="posts" type="radio" value="reassign" class="radio" /> 
              <label for="posts_reassign" class="radio with-control"><?php _e("Re-assign to Post Type:", MASTERPRESS_DOMAIN) ?></label>
              <select id="posts_reassign_type" name="posts_reassign_type" class="check { el: '#posts_reassign' }">
              <?php foreach ($not_this_post_type as $pt) : ?>
              <?php if ($pt->name != $post_type->name): ?>
              <option value="<?php echo $pt->name ?>"><?php echo $pt->display_label() ?></option>  
              <?php endif; ?>
              <?php endforeach; ?>
              </select>
              <input id="posts_trash" name="posts" type="radio" value="trash" class="radio" /> 
              <label for="posts_trash" class="radio"><?php _e("Trash", MASTERPRESS_DOMAIN) ?></label>
              <input id="posts_keep" name="posts" type="radio" value="keep" class="radio" /> 
              <label for="posts_keep" class="radio"><?php _e("Keep", MASTERPRESS_DOMAIN) ?></label>
              
            </div>
            <!-- /.fw -->

            <div class="fw">
            </div>
            <!-- /.fw -->

          </div>
          <!-- /.f -->
          
          <?php endif; ?>
          
          <?php if ($field_set_count > 0) : ?>
          
          <div class="f">
            <span class="label label-icon"><i class="metaboxes"></i><?php _e("Field Set Definitions:", MASTERPRESS_DOMAIN) ?></span>
            <div class="fw">
              <input id="field_sets_delete" name="field_sets" type="radio" value="delete" checked="checked" class="radio" /> 
              <label id="label_field_sets_delete" for="field_sets_delete" class="radio"><?php _e("Delete <span>&nbsp;(Shared Field Sets will not be deleted)</span>", MASTERPRESS_DOMAIN) ?></label>
              <input id="field_sets_keep" name="field_sets" type="radio" value="keep" disabled="disabled" class="radio" /> 
              <label id="label_field_sets_keep" for="field_sets_keep" title="<?php _e("Re-attach by creating a post type with the same name", MASTERPRESS_DOMAIN) ?>" class="disabled radio with-tooltip"><?php _e("Keep", MASTERPRESS_DOMAIN); ?></label>
              
            </div>
            <!-- /.fw -->
          </div>
          <!-- /.f -->

          <?php endif; ?>

          <?php if ($meta_count > 0) : ?>
          

          <div class="f">
            <span class="label label-icon"><i class="database"></i><?php _e("Meta Data:", MASTERPRESS_DOMAIN) ?></span>
            <div class="fw">
              <input id="field_data_delete" name="field_data" type="radio" value="delete" class="radio" checked="checked" /> 
              <label id="label_field_data_delete" for="field_data_delete" class="radio"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></label>
              <input id="field_data_keep" name="field_data" type="radio" value="keep" class="radio" /> 
              <label id="label_field_data_keep" for="field_data_keep" title="stored in the post meta table" class="radio with-tooltip"><?php _e("Keep", MASTERPRESS_DOMAIN) ?></label>

            </div>
            <!-- /.fw -->
          </div>
          <!-- /.f -->

          <?php endif; ?>

          <?php endif; ?>
        
        </div>
        <!-- /.content -->

        <?php endif; ?>
      

        <!-- /.title -->
      </div>
      <!-- /.panel-content -->

      </form>
    </div>
    
    <?php
  }
  
  public function grid($id = null) {

    MPV::incl("field-sets");
    MPV::incl("fields");
    MPV::incl("taxonomies");
    MPC::incl("taxonomies");
    
    $post_types = MPM_PostType::find("orderby=disabled,name ASC");
    
    $has_actions = MasterPress::current_user_can("edit_post_types,delete_post_types,manage_post_type_field_sets");
    $can_edit = MasterPress::current_user_can("edit_post_types");
    $can_delete = MasterPress::current_user_can("delete_post_types");
    $can_create = MasterPress::current_user_can("create_post_types");
    $can_manage_field_sets = MasterPress::current_user_can("manage_post_type_field_sets");

    $less = $can_create ? 1 : 0;
    $colspan = ( $has_actions ? 8 : 7 ) - $less;


  ?>

  <?php MPV::messages(); ?>
  
  <?php 
  foreach ($post_types as $post_type) {
    if (MPC::is_deleting($post_type->id)) {
      self::confirm_delete($post_type);
    }
  }
  
  ?>
  
  <table cellspacing="0" class="grid grid-post-types">
    <thead>
    <tr>
      <th class="first menu-icon"><i class="menu-icon" title="<?php _e("Menu Icon", MASTERPRESS_DOMAIN) ?>"></i><span class="ac"><?php _e("Menu Icon", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="menu-label"><i class="label-string"></i><span><?php _e("Label", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="post-type-name"><i class="script-php"></i><span><?php _e("Name", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="hierarchical"><i class="hierarchy"></i><span class="ac" title="<?php _e("Hierarchical?", MASTERPRESS_DOMAIN) ?>"><?php _e("Hierarchical?", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="field-sets"><i class="metaboxes"></i><span><?php _e("Field Sets", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="shared-field-sets"><i class="med share-metaboxes"></i><span><?php _e("Shared Field Sets", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="taxonomies <?php echo $has_actions ? "" : "last" ?>"><i class="tags"></i><span><?php _e("Taxonomies", MASTERPRESS_DOMAIN) ?></span></th>
      <?php if ($has_actions) : ?>
      <th class="actions last"><i class="buttons"></i><span><?php _e("Actions", MASTERPRESS_DOMAIN) ?></span></th>
      <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    
    
    <?php $count = 0; ?>
    
    <?php foreach ($post_types as $post_type) : $disabled = ( $post_type->disabled ) ? "disabled" : "";  $title = $post_type->disabled ? ' title="'.__("this post type is disabled", MASTERPRESS_DOMAIN).'" ' : ""; 
    
    if (!$post_type->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("this post type is not currently available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
    }

    
    ?>
    
    <?php if ($post_type->still_registered()) : $count++; $first = $count == 1 ? 'first' : ''; ?>
      
    <?php 
    
    $deleting_class = MPC::is_deleting($post_type->id, "delete") ? 'deleting' : ''; 
    $editable_class = $can_edit ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("post-types", "edit", "id=".$post_type->id)."' }" : "";
    
    ?>
    

    <tr <?php echo $title ?> class="<?php echo $first ?> <?php echo $editable_class.$deleting_class ?> <?php echo $disabled ?> <?php echo MPV::updated_class("edit,create", $post_type->id) ?> <?php echo $count % 2 == 0 ? "even" : "" ?> sub <?php echo $meta ?>">
      <td class="first menu-icon icon"><span class="mp-icon-post-type mp-icon-post-type-<?php echo $post_type->name ?>"></span></td>
      <td class="menu-label"><strong><?php echo $post_type->labels["menu_name"] ?></strong></td>
      <td class="post-type-name"><span class="tt"><?php echo $post_type->name ?></span></td>
      <td class="hierarchical"><?php echo $post_type->hierarchical ? '<i class="tick-small"></i><span class="ac" title="'.__("this post type is hierarchical", MASTERPRESS_DOMAIN).'">yes</span>' : '<span class="note" title="'.__("this post type is not hierarchical", MASTERPRESS_DOMAIN).'">&ndash;</span>' ?></td>

      <?php $field_sets = $post_type->field_sets("name ASC"); ?>

      <?php if ($post_type->show_ui) : ?>
        
      <td class="field-sets <?php echo $can_manage_field_sets ? "manage { href: '".MasterPress::admin_url( "post-types", "manage-field-sets", "parent=".$post_type->id)."' }" : "" ?>">
        <?php if ($can_manage_field_sets) : ?>
        <a href="<?php echo MasterPress::admin_url( "post-types", "manage-field-sets", "parent=".$post_type->id)?>" title="<?php echo strip_tags(self::__manage( MPV_FieldSets::__p() )) ?>">
		<i class="go"></i>
        <?php endif; ?>

        <?php 
        
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
      <?php else: ?>
      <td class="field-sets">
      <span title="<?php _e("This post type does not support field sets as the 'show ui' parameter is off", MASTERPRESS_DOMAIN); ?>"><i class="na"></i><?php _e("N/A", MASTERPRESS_DOMAIN); ?></span>  
      </td>
      <?php endif; ?>

      <?php if ($post_type->show_ui) : ?>
    
      <td class="shared-field-sets">
        
        <?php 
        
        $field_set_display = MPV::note_none();
      
        if (count($field_sets)) {
          $field_set_links = array();
          
          foreach ($field_sets as $field_set) {
            if ($field_set->is_shared()) {
              $field_set_links[] = $field_set->display_label(); 
            }
          }

          if (count($field_set_links)) {
            $field_set_display = implode($field_set_links, ", ");
          }
        
        }
        
        echo $field_set_display;
        ?>
        
      </td>
      
      <?php else: ?>
      <td class="shared-field-sets">
      <span title="<?php _e("This post type does not support shared field sets as the 'show ui' parameter is off", MASTERPRESS_DOMAIN); ?>"><i class="na"></i><?php _e("N/A", MASTERPRESS_DOMAIN); ?></span>  
      </td>
      <?php endif; ?>


      <td class="taxonomies <?php echo $has_actions ? "" : "last" ?>">
        <?php 
        $taxonomies = $post_type->taxonomies(); 
        
        $tax_display = MPV::note_none();
        
        if (count($taxonomies)) {
          $tax_links = array();
          
          foreach ($taxonomies as $tax) {
            $tax_links[] = $tax->labels["name"]; 
          }

          $tax_display = implode($tax_links, ", ");
        }
        
        echo $tax_display;
        ?>
      </td>      

      <?php if ($has_actions) : ?>

      <td class="actions last">
      <div>
        <?php if (MPC::is_deleting($post_type->id)) : ?>
        
        <span class="confirm-action"><?php _e("Please Confirm Delete Action", MASTERPRESS_DOMAIN) ?></span>
        
        <?php else: ?>

          <?php if ($can_manage_field_sets) : ?>

            <?php if ($post_type->show_ui) :  ?>
            <?php echo MPV::action_button("post-types", "manage-field-sets", self::__manage( MPV_FieldSets::__p_short() ), "parent=".$post_type->id, array("class" => "button button-manage")); ?>
            <?php else: ?>
            <?php echo MPV::action_button("post-types", "manage-field-sets", self::__manage( MPV_FieldSets::__p_short() ), "parent=".$post_type->id, array("title" => "This post type does not support field sets as the 'show ui' parameter is off", "disabled" => "disabled", "class" => "button button-manage")); ?>
            <?php endif; ?>
      
          <?php endif; ?>
        
          <?php if ($can_edit) : ?>
            <?php echo MPV::action_button("post-types", "edit", self::__edit( "" ), "id=".$post_type->id, array( "class" => "button primary button-edit" )); ?>
          <?php endif; ?>

          <?php if ($can_delete) : ?>
            <?php if ($post_type->_builtin) : ?>
            <span class="note" title="<?php _e("This post type cannot be deleted as it is built-in to WordPress", MASTERPRESS_DOMAIN) ?>"><?php _e("( Built-in )", MASTERPRESS_DOMAIN) ?></span>
            <?php elseif ($post_type->_external) : ?>
            <span class="note" title="<?php _e("This post type cannot be deleted as it was not created by MasterPress", MASTERPRESS_DOMAIN) ?>"><?php _e("( External )", MASTERPRESS_DOMAIN) ?></span>
            <?php else: ?>
        
            <?php echo MPV::action_button("post-types", "delete", self::__delete( "" ), "id=".$post_type->id, array( "class" => "button button-delete", "title" => "Delete - Requires Confirmation" )); ?>
            <?php endif; ?>
          <?php endif; ?>
      
        <?php endif; // has_actions ?>
      </div>
      </td>
      
      <?php endif; ?>
      
    </tr>
  
    <?php endif; ?>

    <?php endforeach; ?>

    <tr class="summary <?php echo $can_create ? "editable" : "" ?>">
      <td colspan="<?php echo $colspan ?>" class="first <?php echo $can_create ? "" : "last" ?>"><?php _e(  MPU::__items( $count, __("<strong>%d</strong> Post Type", MASTERPRESS_DOMAIN), __("%d Post Types", MASTERPRESS_DOMAIN)   ) ) ?></td>
      <?php if ($can_create) : ?>
      <td class="last">
      <?php echo MPV::action_button("post-types", "create", self::__create(MPV_PostTypes::__s()), "", array( "class" => "button button-create" ) ); ?>
      </td>
      <?php endif; ?>
    </tr>
    

    </tbody>
    </table>


    
    <?php
    


  } // end grid()
  
  

  public function form($type) {
    global $wf, $meow_provider;
    $model = MasterPress::$model;
  ?>

    <?php MPV::messages(); ?>
  
    <input type="hidden" name="_builtin" value="<?php echo $model->_builtin ? "true" : "false" ?>" />
    <input type="hidden" name="_external" value="<?php echo $model->_external ? "true" : "false" ?>" />
    
    <div class="f">
      <label for="name" class="icon"><i class="script-php"></i><?php _e("<strong>Singular</strong> Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="name_original" name="name_original" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />
        <input id="name_last" name="name_last" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />
        <input id="name" name="name" type="text" <?php echo MPV::read_only_attr($model->_builtin || MPC::is_edit()) ?> class="<?php echo MPV::read_only_class($model->_builtin || $model->_external) ?> text mono key" maxlength="20" value="<?php echo $model->name ?>" /><?php if (!$model->_builtin && !$model->_external) { ?><em class="required"><?php _e("(required)", MASTERPRESS_DOMAIN) ?></em><?php } ?>
        <p>
          <?php _e("This is a unique identifier for the custom post type in the WordPress and MasterPress APIs. It is not displayed, and by convention it <strong>must</strong> be a singular form, lowercase string with underscores to separate words.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
      
      <p id="name_warning" class="warning">
        <i class="error-circle"></i><?php _e("Note: check that you have definitely entered a <strong>singular word</strong> here, as the singular form of <em>Plural Name</em> is currently different to this value.", MASTERPRESS_DOMAIN) ?>
      </p>

    </div>
    <!-- /.f -->
    
    <div class="f">
      <label for="plural_name" class="icon"><i class="script-php"></i><?php _e("<strong>Plural</strong> Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="plural_name" name="plural_name" <?php echo MPV::read_only_attr($model->_builtin || MPC::is_edit()) ?> type="text" value="<?php echo $model->plural_name ?>" class="<?php echo MPV::read_only_class($model->_builtin || $model->_external) ?> text mono key" />
        <?php if (!$model->_builtin && !$model->_external) { ?><em class="required">(required)</em><?php } ?>
        <?php if (MPC::is_edit() && !$model->_builtin && !$model->_external) : ?>
        <button id="plural_name_suggest" type="button" class="button button-small"><?php _e("Suggest", MASTERPRESS_DOMAIN) ?></button>
        <?php endif; ?>
        <p>
          <?php _e("The plural form of <em>Singular Name</em>, following the same naming conventions", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    
    <div class="f clearfix">
      <label id="label-menu_icon" for="menu_icon" class="icon"><i class="image-small"></i><?php _e("Icon (16 x 16)", MASTERPRESS_DOMAIN) ?>:</label>
      <div class="fw">

        <div id="icon-file-uploader" class="icon-uploader file-uploader { ids: { drop: 'menu_icon_drop_area' }, input: '#menu_icon', inputName: 'menu_icon_ul', base_url: '<?php echo MASTERPRESS_GLOBAL_CONTENT_URL ?>', params: { dir: 'menu-icons/' }, limit: 1, lang: { buttonChoose: '<?php _e("Choose from Computer&hellip;", MASTERPRESS_DOMAIN) ?>', buttonReplace: '<?php _e("Replace file&hellip;", MASTERPRESS_DOMAIN) ?>' } }">
          
          <div id="menu_icon_drop_area" class="drop-area"><?php _e("Drop file here to upload", MASTERPRESS_DOMAIN) ?></div>

          <?php 
          
          $file_name = $model->menu_icon;
          $file_class = "";
          $clear_class = "";
          
          if ($file_name == "") {
            $file_name = __("( None )", MASTERPRESS_DOMAIN);
            $file_class = "name-none";
            $clear_class = "hidden";
          }
          
          ?>
          
          <div class="file">
            <span class="preview" style="background-image: url('<?php echo MPU::menu_icon_url($model->menu_icon, true, "post_type", true) ?>');"></span><span data-none="<?php echo __("( None )", MASTERPRESS_DOMAIN) ?>" class="name <?php echo $file_class ?>"><?php echo $file_name ?></span>
            <button type="button" class="<?php echo $clear_class ?> clear ir" title="<?php _e("Clear", MASTERPRESS_DOMAIN) ?>">Clear</button>
          </div>
          
          <input id="menu_icon" name="menu_icon" value="<?php echo $model->menu_icon ?>" type="hidden" />
          <div class="uploader-ui"></div>
          
        </div>
        <!-- /.file-uploader -->
        
        <?php MPV::icon_select($model->menu_icon, "menu-icon-select", "menu_icon_select", "icon-file-uploader"); ?>
        
      </div>
    </div>
    <!-- /.f -->
    
    <?php if (!$model->_external) : ?>
  
    <div class="f">
      <label for="disabled" class="icon"><i class="slash"></i><?php _e("Disabled", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="disabled" name="disabled" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->disabled ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#disabled' }">
          <?php 
          if ($model->_builtin) :
            _e("Since this post type is built-in, disabling it may render some WordPress themes unusable.<br />This is only recommended for sites that are highly customised.", MASTERPRESS_DOMAIN);
          else: 
            _e("disabling a post type will keep its definition in the database but it will not be registered in WordPress, which will often be <strong>preferable to deleting it</strong> entirely.", MASTERPRESS_DOMAIN);
          endif;
          ?>
        </p>
      </div>
    </div>
    <!-- /.f -->
    
    <?php endif; ?>
    
    
    
    <?php if (!$model->_builtin && !$model->_external) : ?>
    
    <div class="f">
      <label for="description" class="icon"><i class="metabox-text"></i><?php _e("Description", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <textarea id="description" name="description" rows="4" cols="100"><?php echo $model->description ?></textarea>
      </div>
    </div>
    <!-- /.f -->


    <div class="f">
      <label for="hierarchical" class="icon"><i class="hierarchy"></i><?php _e("Hierarchical", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="hierarchical" name="hierarchical" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->hierarchical ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#hierarchical' }">
          <?php _e("hierarchical post types behave like <strong>pages</strong> in WordPress, where each post can have parent and child posts", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

   


    <div class="f">
      <label for="show_ui" class="icon"><i class="metabox-menu"></i><?php _e("Show UI", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="show_ui" name="show_ui" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->show_ui ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#show_ui' }">
          <?php _e("uncheck this to internalize this post type, which will cause many other features to be unsupported", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="show_in_nav_menus" class="icon"><i class="menu-gray"></i><?php _e("Show in Nav Menus", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="show_in_nav_menus" name="show_in_nav_menus" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->show_in_nav_menus ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#show_in_nav_menus' }">
          <?php _e("allow selection of posts of this type in WordPress custom menus", MASTERPRESS_DOMAIN); ?> 
        </p>
      </div>
    </div>
    <!-- /.f -->

      
    <div class="f">
      <label for="show_in_menu" class="icon"><i class="menu-icon"></i><?php _e("Show in Admin Menu", MASTERPRESS_DOMAIN) ?>?</label>

      <div class="fw">
        <input id="show_in_menu" name="show_in_menu" type="checkbox" <?php echo WOOF_HTML::checked_attr( $model->show_in_menu ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#show_in_menu' }">
          <?php _e("show this post type in the WordPress admin menu", MASTERPRESS_DOMAIN); ?> 
        </p>
      </div>  
    <!-- /.fw -->
    </div>

    <div class="f">
      <label for="manage_sort_order" class="icon"><i class="sort-a-z"></i><?php _e("Default Manage Order", MASTERPRESS_DOMAIN) ?></label>

      <div class="fw">
        
        <?php
        
        $options = array(
          "post_date|desc" => "Post Date, Descending",
          "title|asc" => "Title, Ascending",
          "menu_order|asc" => "Menu Order, Ascending",
          "post_date|asc" => "Post Date, Ascending",
          "title|desc" => "Title, Descending",
          "menu_order|desc" => "Menu Order, Descending"
        );
        
        ?>
        
        <select id="manage_sort_order" name="manage_sort_order">
        <?php foreach ($options as $value => $text) : ?>
          <option <?php echo WOOF_HTML::selected_attr($value == $model->manage_sort_order) ?> value="<?php echo $value ?>"><?php echo $text ?></option>  
        <?php endforeach; ?>
        </select>
        
        <p class="note">
          <?php _e("Specify the default sort order in the manage post screen (for when the user hasn't yet clicked on a table header to manually sort). For chronological types (such as blog posts) a descending date order is appropriate, but for other post types a title or menu order (user-defined) sort might be better.", MASTERPRESS_DOMAIN); ?> 
        </p>
      </div>  
    <!-- /.fw -->
    </div>
    
    <?php endif; ?>

    
    
    <?php
    
    if (is_multisite() && MASTERPRESS_MULTISITE_SHARING) {
    
      $args["supports"] = array("multisite");
      
      $args["labels"] = array(
        "title" =>  __("control the visibility of this Post Type within WordPress", MASTERPRESS_DOMAIN),   
        "title_multisite" =>  __("specify the sites in the multisite network that this Post Type is available in", MASTERPRESS_DOMAIN),   
        "multisite_all" => __( "All Sites" )
      );

      MPV::fs_visibility( $model, $args ); 
    
    }
    
    ?>

    
    <?php if (!$model->_external) : ?>

    <div class="fs fs-taxonomies">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="tags"></i><strong><?php _e("Taxonomies", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("attach taxonomies to this post type", MASTERPRESS_DOMAIN) ?></h3>
        <div class="buttons">
          <button class="button button-small button-select-all" type="button"><?php _e('Select <strong class="all">All</strong>', MASTERPRESS_DOMAIN)  ?></button>
          <button class="button button-small button-select-none" type="button"><?php _e('Select <strong class="none">None</strong>', MASTERPRESS_DOMAIN) ?></button>
        </div>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
        
        <?php $taxonomies = MPM_Taxonomy::find(array("orderby" => "name ASC")); ?>
        
        <?php foreach ($taxonomies as $tax) : $disabled = $tax->disabled ? ' disabled' : ''; $disabled_title = $tax->disabled ? __("This taxonomy is disabled", MASTERPRESS_DOMAIN) : ''; $builtin = $tax->_builtin ? '&nbsp;'.__('(Built-in)', MASTERPRESS_DOMAIN) : ''; ?>
          
        <?php if ($tax->still_registered()) : ?>
          
        <div class="fw">
          <input id="taxonomies_<?php echo $tax->name ?>" data-builtin="<?php echo $tax->_builtin ?>" data-title="<?php echo $tax->display_label() ?>" name="taxonomies[]" value="<?php echo $tax->name ?>" type="checkbox" <?php echo WOOF_HTML::checked_attr( $tax->linked_to_post_type($model) || MPV::in_post_array("taxonomies", $tax->name) ) ?> class="checkbox" />
          <label for="taxonomies_<?php echo $tax->name ?>" class="checkbox <?php echo $disabled ?>" title="<?php echo $disabled_title ?>"><?php echo $tax->labels["name"] ?><span><?php echo $builtin ?></span></label>
        </div>
        <!-- /.fw -->
        
        <?php endif; ?>
      
        <?php endforeach; ?>
        
        
      </div>
      </div>

    </div>
    <!-- /.fs -->

    <div class="fs fs-supports">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="gear"></i><strong><?php _e("Supports", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("controls the user interface for creating and editing posts of this type", MASTERPRESS_DOMAIN) ?></h3>
        <div class="buttons">
          <button class="button button-small button-select-all" type="button"><?php _e('Select <strong class="all">All</strong>', MASTERPRESS_DOMAIN) ?></button>
          <button class="button button-small button-select-none" type="button"><?php _e('Select <strong class="none">None</strong>', MASTERPRESS_DOMAIN) ?></button>
        </div>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
        
        <input id="supports_pb" name="supports_pb" type="hidden" value="true" />
        
        <div id="fs-supports-1">
          
					<?php
						
					$title_disabled = !($model->_builtin || $model->_external);
					$title_title = "";
					
					$title_suffix = "";
					
					if ($title_disabled) {
						$title_title = ' title="'.__("The title feature must be enabled", MASTERPRESS_DOMAIN).'" ';
					}

					
					$dis = WOOF_HTML::disabled_attr($title_disabled);
					
					if (!($model->_builtin || $model->_external)) {
						$checked = WOOF_HTML::checked_attr( true );
					} else {
						$checked = WOOF_HTML::checked_attr( MPV::in_csv("title", $model->supports) );
					}
					
					?>

          <div class="fw">
            <input id="supports_title" name="supports[]" value="title" <?php echo $dis ?> <?php echo $title_title ?> <?php echo $checked ?> type="checkbox" class="checkbox { tags: ['title'] }" />
						
						<?php if ($title_disabled) : ?> 
	          <input id="supports_title_val" name="supports[]" value="title" type="hidden" />
						<?php endif; ?>
							 
            <label for="supports_title" class="checkbox"><?php _e("Title", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show a text input to edit the post title", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_editor" name="supports[]" value="editor" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("editor", $model->supports) ) ?> type="checkbox" class="checkbox { tags: ['auto_excerpt'] }" />
            <label for="supports_editor" class="checkbox"><?php _e("Editor", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show the main content box", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_comments" name="supports[]" value="comments" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("comments", $model->supports) ) ?> type="checkbox" class="checkbox { tags: ['comment_count'] }" />
            <label for="supports_comments" class="checkbox"><?php _e("Comments", MASTERPRESS_DOMAIN); ?><span> - <?php _e("the <em>ability</em> to allow / disallow comments on posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_trackbacks" name="supports[]" value="trackbacks" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("trackbacks", $model->supports) ) ?> type="checkbox" class="checkbox { tags: ['trackback_count'] }" />
            <label for="supports_trackbacks" class="checkbox"><?php _e("Trackbacks", MASTERPRESS_DOMAIN); ?><span> - <?php _e("the <em>ability</em> to allow / disallow trackbacks to posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_post_formats" name="supports[]" value="post-formats" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("post-formats", $model->supports)) ?> type="checkbox" class="checkbox { tags: ['post_format'] }" />
            <label for="supports_post_formats" class="checkbox"><?php _e("Post Formats", MASTERPRESS_DOMAIN); ?><span> - <?php _e("used by themes to change the display of posts of this type", MASTERPRESS_DOMAIN); ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_front_page" name="supports[]" value="front-page" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("front-page", $model->supports)) ?> type="checkbox" class="checkbox { tags: ['front_page'] }" />
            <label for="supports_front_page" class="checkbox"><?php _e("Front Page", MASTERPRESS_DOMAIN); ?><span> - <?php _e("allow posts of this type to be selected as the front page", MASTERPRESS_DOMAIN); ?></span></label>
          </div>
          <!-- /.fw -->
          
        </div>
        <!-- /.fs-supports-1 -->


        <div id="fs-supports-2">

          <div class="fw">
            <input id="supports_revisions" name="supports[]" value="revisions" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("revisions", $model->supports)) ?> type="checkbox" class="checkbox" />
            <label for="supports_revisions" class="checkbox"><?php _e("Revisions", MASTERPRESS_DOMAIN); ?><span> - <?php _e("allow revisions for posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_author" name="supports[]" value="author" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("author", $model->supports)) ?> class="checkbox" />
            <label for="supports_author" class="checkbox"><?php _e("Author", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show a select box for changing the author", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_excerpt" name="supports[]" value="excerpt" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("excerpt", $model->supports)) ?> class="checkbox { tags: ['excerpt'] }" />
            <label for="supports_excerpt" class="checkbox"><?php _e("Excerpt", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show a text area for writing a custom excerpt", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_page_attributes" name="supports[]" value="page-attributes" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("page-attributes", $model->supports) ) ?> class="checkbox" />
            <label for="supports_page_attributes" class="checkbox"><?php _e("Type Attributes", MASTERPRESS_DOMAIN); ?><span> - <?php _e("Show the UI for editing Template, Menu Order and Parent", MASTERPRESS_DOMAIN); ?></span></label>
          </div>
          <!-- /.fw -->

          <div class="fw">
            <input id="supports_thumbnail" name="supports[]" value="thumbnail" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("thumbnail", $model->supports) ) ?> class="checkbox { tags: ['thumbnail'] }" />
            <label for="supports_thumbnail" class="checkbox"><?php _e("Thumbnail", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show the <em>standard</em> thumbnail upload for posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
          </div>
          <!-- /.fw -->

          
        </div>
        <!-- /.fs-supports-2 -->

      
      </div>
      </div>

    </div>
    <!-- /.fs -->

    <?php else : ?>
    
    
    
    <?php endif; ?>
  
    <script id="custom-column-template" type="text/html">
    <li class="column clearfix">
      <div class="head">
        <input name="columns[{{index}}][title]" value="Column" type="text" class="text" />
        <input name="columns[{{index}}][disabled]" value="" type="hidden" class="disabled" />
        <span class="handle"></span>
      </div>
  
      <div class="body">
        <textarea name="columns[{{index}}][content]" class="column-content mono editable"></textarea>
      </div>
      
      <div class="foot">
      <div>
        <button class="text remove"><i></i><?php _e("Remove") ?></button>
      </div>
      </div>

    </li>
    
    </script>
    
    <script id="tax-add-button-template" type="text/html">

    <div class="control taxonomy-control {{taxonomy}}">
      <button data-taxonomy="{{taxonomy}}" data-title="{{title}}" type="button" class="text add taxonomy taxonomy-{{taxonomy}}"><?php printf( __('<em class="create">Add</em> <b>%s</b> Column', MASTERPRESS_DOMAIN ), "{{title}}" ) ?></button>
    </div>
    
    </script>
    
    
    <?php if ($model->show_ui) : ?>
      
    <div class="fs fs-column-builder clearfix">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="grid"></i><strong><?php _e("Columns") ?></strong> - <?php _e("specify the columns shown in the manage listing for posts of this type") ?></h3>
      </div>
      </div>
    
      <div class="fsc clearfix">
      <div class="fscb clearfix">

        
        <div class="columns-clip">
        
        <div class="columns-wrap clearfix">
        
        <ul class="columns clearfix">
        
          <?php 
          
          $count = 0; 
          $core_enabled = array(); 
          
          ?>
          
          <?php foreach ($model->columns() as $column) : ?>
          <?php
            
            $class = array("column clearfix");
            
            $disabled = "";
            
            $core = "";
            $title = isset($column["title"]) ? $column["title"] : "";
            $content = "";
            
            if (isset($column["content"])) {
              $content = stripslashes($column["content"]);
            }
            
            if (isset($column["core"])) {
              $core = $column["core"];
              $content = "{{col.".$core."}}";
            }
            
            if (isset($column["disabled"])) {
              $disabled = $column["disabled"];
            }
            
            if ($core != "") {
              $class[] = "core";
              $class[] = $core;
              
              $data_core = ' data-core="'.$core.'" ';
            }
            
            if ($core == "cb") {
              $class[] = "checkbox nosort";
            } 
            
            if ($disabled == "yes") {
              $class[] = "disabled";
            } else {
              if ($core != "") {
                $core_enabled[] = $core;
              }
            }
            
            
          
          ?>
          
          
          <li <?php echo $data_core ?> class="<?php echo implode(" ", $class) ?>">
            <div class="head">
              <?php if ($core == "cb") : ?>
              <input type="checkbox" />
              <input name="columns[<?php echo $count ?>][core]" value="cb" type="hidden" />
              <?php else: ?>
                
                <?php if ($core == "comments") : ?>
                <span class="icon"></span>
                <input name="columns[<?php echo $count ?>][title]" value="<?php echo $title ?>" type="text" class="text hidden" />
                <?php else: ?>
                <input name="columns[<?php echo $count ?>][title]" value="<?php echo $title ?>" type="text" class="text" />
                <?php endif; ?>
              
                <?php if ($core != "") : ?>
                <input name="columns[<?php echo $count ?>][core]" value="<?php echo $core ?>" type="hidden" />
                <?php endif; ?>
                
                <input name="columns[<?php echo $count ?>][disabled]" value="<?php echo $disabled ?>" type="hidden" class="disabled" />
                <span class="handle"></span>
  
              <?php endif; ?>
            </div>
            <!-- /.head -->

            <div class="body clearfix">
              <?php if ($core == "cb") : ?>
              <input type="checkbox" />
              <?php else: ?>
                
                <?php if ($core == "comments") : ?>
                <span class="icon"></span>
                <textarea name="columns[<?php echo $count ?>][content]" readonly="true" class="content mono readonly hidden"></textarea>
                <?php elseif ($core != "") : ?>
                <textarea name="columns[<?php echo $count ?>][content]" title="<?php _e("Content cannot be changed as this is a built-in column") ?>" readonly="true" class="content mono readonly"><?php echo $content ?></textarea>
                <?php else : ?>
                <textarea name="columns[<?php echo $count ?>][content]" class="content mono editable" style="z-index: <?php echo 200 - $count ?>"><?php echo $content ?></textarea>
                <?php endif; ?>
              
              <?php endif; ?>
            </div>
            
            <div class="foot">
            <div>
              <?php if ($core != "cb" && $core != "title") : ?>
              <button type="button" class="text remove"><i></i><?php _e("Remove") ?></button>
              <?php endif; ?>
            </div>
            </div>

          </li>
          
          <?php $count++; ?>

          <?php endforeach; ?>

        </ul>
      
        </div>
        <!-- /.columns-wrap -->

        <div class="core-columns">
          <div class="f">
            <p class="label"><i class="wall"></i><?php _e("Built-in columns:", MASTERPRESS_DOMAIN) ?></p>
            
            <?php
              
              $core_columns = array(
                "Author" => "author",
                "Categories" => "categories",
                "Tags" => "tags",
                "Comments" => "comments",
                "Date" => "date"
              );
                
            ?>
            
            <?php foreach ($core_columns as $label => $key) : ?>
              
            <?php
          
            $checked_attr = WOOF_HTML::checked_attr(in_array($key, $core_enabled)); 
            $style = "";
            
            $supports = explode(",", $model->supports);
            
            if ($key == "categories" && !$model->linked_to_taxonomy_name("category")) {
              $style = 'style="display: none" ';
            } else if ($key == "tags" && !$model->linked_to_taxonomy_name("post_tag")) {
              $style = 'style="display: none" ';
            } else if ($key == "author" && !in_array("author", $supports)) {
              $style = 'style="display: none" ';
            } else if ($key == "comments" && !in_array("comments", $supports)) {
              $style = 'style="display: none" ';
            }
            
            
            ?>
              
            <div <?php echo $style ?> id="fw-core-column-<?php echo $key ?>" class="fw">
              
              <input id="core-column-<?php echo $key ?>" <?php echo $checked_attr ?> value="<?php echo $key ?>" type="checkbox" class="checkbox" />
              <label for="core-column-<?php echo $key ?>" class="checkbox"><?php echo $label ?></label>
              
            </div>
            <!-- /.fw -->
            
            <?php endforeach; ?>
          
          </div>
        
        </div>
        <!-- /.core-columns -->
        
        <?php if (MPC::is_edit()) : ?>

        <style type="text/css">
        <?php foreach (MPFT::type_keys() as $key) : ?>
        .select2-results .field-type-<?php echo $key ?>, .select2-container .field-type-<?php echo $key ?> { background-repeat: no-repeat; background-image: url(<?php echo MPU::type_icon_url($key) ?>); }
        <?php endforeach; ?>
        </style>

          
        <div id="dialog-fields" data-title="<?php _e("Select a Field to Display in the Column", MASTERPRESS_DOMAIN) ?>">
          <?php
          
          $fs = $meow_provider->post_type_field_sets($model->name);

          $field_options = array();
          $field_options[""] = "";

          $field_options_attr = array("");
          
          foreach ($fs as $set) {
            
            $fo = array();
            $fo_attr = array();
            
            foreach ($set->fields() as $field) {
              $fo[$field->display_label()] = $set->name.".".$field->name;
              $fo_attr[] = $field_options_attr[] = array("data-icon" => "mp-icon field-type-".$field->type);
            }

            $field_options[$set->display_label()] = array("options" => $fo, "options_attr" => $fo_attr);
          } 
                    
          echo WOOF_HTML::select(array("id" => "add-field-column-field-sets", "name" => "add-field-column-field-sets", "class" => "with-icons select2-source", "data-placeholder" => __("-- Select a Field --", MASTERPRESS_DOMAIN)), $field_options, "", $field_options_attr);

          ?>
        </div>
        <!-- /#dialog-fields -->
        
        <?php endif; ?>
        
        <div class="custom-columns">
          <?php if (MPC::is_edit() && count($field_options) > 1) : ?>
          
          <div class="control">
            <button id="add-field-column" type="button" class="text add field"><i></i><?php _e('<em class="create">Add</em> <b>Field</b> Column', MASTERPRESS_DOMAIN) ?></button>
          </div>

          <?php endif; ?>

          <div class="control">
            <button id="add-custom-column" type="button" class="text add"><i></i><?php _e('<em class="create">Add</em> <b>Custom</b> Column', MASTERPRESS_DOMAIN) ?></button>
          </div>
          
          <?php
          
          $taxonomies = $model->taxonomies();            
             
          ?>
          
          <?php foreach ($model->taxonomies() as $tax) : ?>
          <div class="control taxonomy-control <?php echo $tax->name ?>">
            <button data-taxonomy="<?php echo $tax->name ?>" data-title="<?php echo $tax->display_label() ?>" type="button" class="text add taxonomy taxonomy-<?php echo $tax->name ?>"><i></i><?php printf( __('<em class="create">Add</em> <b>%s</b> Column', MASTERPRESS_DOMAIN ), $tax->display_label() ) ?></button>
          </div>
          <?php endforeach; ?>
          
        </div>
        <!-- /.custom-columns -->
        
        </div>
        <!-- /.columns-clip -->

      </div>
      </div>

    </div>
    <!-- /.fs -->
      
    <?php endif; ?>
    
    <?php if (!$model->_builtin && !$model->_external) : ?>
      
    <div class="fs fs-menu-options clearfix">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="menu-icon"></i><strong><?php _e("Admin Menu", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("controls where your post type appears in the WordPress administration menu", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
  
      <div class="fsc clearfix">
      <div class="fscb clearfix">
        
        
        <div class="f clearfix">
          <label for="menu_position" class="label-sortable-list"><?php _e("Position", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            
            <?php 
              // Build a menu, which we'll co-populate with info from the post types and then sort by menu position and sub position
              
              $post_post_type = MPM_PostType::find_by_name("post");
              $page_post_type = MPM_PostType::find_by_name("page");
              
              $menus = array(
                array( "label" => __("Posts", MASTERPRESS_DOMAIN),    "class" => "icon16 icon-post", "disabled" => ( (bool) $post_post_type->disabled ), "built_in" => true, "position" => 5, "sub_position" => 0 ),
                array( "label" => __("Media", MASTERPRESS_DOMAIN),    "class" => "icon16 icon-media", "built_in" => true, "position" => 10, "sub_position" => 0 ),
                array( "label" => __("Links", MASTERPRESS_DOMAIN),    "class" => "icon16 icon-links", "built_in" => true, "position" => 15, "sub_position" => 0 ),
                array( "label" => __("Pages", MASTERPRESS_DOMAIN),    "class" => "icon16 icon-page", "disabled" => ( (bool) $page_post_type->disabled ), "built_in" => true, "position" => 20, "sub_position" => 0 ),
                array( "label" => __("Comments", MASTERPRESS_DOMAIN), "class" => "icon16 icon-comments", "built_in" => true, "position" => 25, "sub_position" => 0 ),
                array( "divider" => true, "position" => 64, "sub_position" => 0 ),
                array( "label" => __("Plugins", MASTERPRESS_DOMAIN),  "class" => "icon16 icon-plugins", "built_in" => true, "position" => 65, "sub_position" => 0 ),
                array( "label" => __("Users", MASTERPRESS_DOMAIN),    "class" => "icon16 icon-users", "built_in" => true, "position" => 70, "sub_position" => 0 ),
                array( "label" => __("Tools", MASTERPRESS_DOMAIN),    "class" => "icon16 icon-tools", "built_in" => true, "position" => 75, "sub_position" => 0 ),
                array( "label" => __("Settings", MASTERPRESS_DOMAIN), "class" => "icon16 icon-settings", "built_in" => true, "position" => 80, "sub_position" => 0 ),
                array( "divider" => true, "position" => 100, "sub_position" => 0 )
              );

              // Splice with post-types data (non built-in)
              
              $post_types = MPM_PostType::find( array( "where" => "_builtin = 0 AND name <> '{$model->name}'", "orderby" => "menu_position,menu_sub_position") );
              
              foreach ($post_types as $post_type) {
                if ($post_type->show_in_menu) {
                  
                  if (!$post_type->_external) {
                    
                  // if post variables are set for these positions, use those instead of the DB values -->

                  $position = $post_type->menu_position;
                  $sub_position = $post_type->menu_sub_position;
                
                  if (isset($_POST["other_menu_position"]) && isset($_POST["other_menu_position"][$post_type->name])) {
                    $position = $_POST["other_menu_position"][$post_type->name];
                  }

                  if (isset($_POST["other_menu_sub_position"]) && isset($_POST["other_menu_sub_position"][$post_type->name])) {
                    $sub_position = $_POST["other_menu_sub_position"][$post_type->name];
                  }

                  $bi = false;
                  
                  $menus[] = array( "name" => $post_type->name, "label" => $post_type->labels["menu_name"], "disabled" => $post_type->disabled, "icon" => MPU::menu_icon_url($post_type->menu_icon, true, "post_type", true), "built_in" => $bi, "position" => (int) $position, "sub_position" => (int) $sub_position );
                  
                  }
                
                } 
              }
              
              // Finally, add the post type currently being created / edited
              
              $menus[] = array( "disabled" => $model->disabled, "current" => true, "name" => $model->name == "" ? "?" : $model->name, "label" => $model->labels["menu_name"], "icon" => $model->menu_icon == "" ? MPU::img_url("icon-no-icon.png") : MPU::menu_icon_url($model->menu_icon), "built_in" => false, "position" => (int) $model->menu_position, "sub_position" => (int) $model->menu_sub_position ); 
              
              
              // MPU::img_url("icon-no-icon.png")
              
              function mp_menu_sort($a, $b) {

                if ($a["position"] == $b["position"]) {
                  if ($a["sub_position"] == $b["sub_position"]) {
                    return 0;
                  } else {
                    return $a["sub_position"] > $b["sub_position"] ? 1 : -1;
                  }
                } else if ($a["position"] > $b["position"]) {
                  return 1;
                } else {
                  return -1;
                }
                    
                return 0;
              }
              
              usort($menus, "mp_menu_sort");
            
              // MPV::dump($menus);
              
              $count = 0;
            ?>
                
            <div class="sortable-list sortable-list-menu">
              <span class="arrow"></span>
              
              <div class="lists clearfix">

              <ul>
                <?php foreach ($menus as $menu) : $count++; $first = $count == 1 ? "first " : ""; ?>
                
                <?php
                  
                  $disabled = '';
                  $disabled_title = '';
                  
                  if (isset($menu["disabled"]) && $menu["disabled"]) {
                    $disabled = 'disabled';
                    $disabled_title = __("This post type is disabled", MASTERPRESS_DOMAIN);
                  }
                
                ?>
                
                <?php 
                if (isset($menu["divider"])) : 
                ?>
                  <li class="divide nomove"></li>  
                <?php 
                elseif (isset($menu["built_in"]) && $menu["built_in"]) : 
                  ?>
                  <li class="<?php echo $first ?> <?php echo $disabled ?> bi nomove { base_pos: <?php echo $menu["position"] ?>}" title="<?php echo $disabled_title ?>">
                    <span class="icon <?php echo $menu["class"] ?>"></span>
                    <span><?php echo $menu["label"] ?></span>
                  </li>
                <?php 
                elseif (isset($menu["current"])) :
                ?>
                <li class="current <?php echo $disabled ?>" title="<?php echo $disabled_title ?>">
                  <input id="menu_position" name="menu_position" value="<?php echo $menu["position"] ?>" type="hidden" class="pos" />
                  <input id="menu_sub_position" name="menu_sub_position" value="<?php echo $menu["sub_position"] ?>" type="hidden" class="sub_pos" />
                  <span class="icon mp-icon mp-icon-post-type mp-icon-post-type-<?php echo $menu["name"] ?>"></span>
                  <span class="fill { src: '#label_menu_name' }">?</span>
                </li>
                <?php
                else: 
                ?>
                <li class="<?php echo $disabled ?>" title="<?php echo $disabled_title ?>">
                  <input id="other_menu_position_<?php echo $menu["name"] ?>" name="other_menu_position[<?php echo $menu["name"] ?>]" value="<?php echo $menu["position"] ?>" type="hidden" class="pos" />
                  <input id="other_menu_sub_position_<?php echo $menu["name"] ?>" name="other_menu_sub_position[<?php echo $menu["name"] ?>]" value="<?php echo $menu["sub_position"] ?>" type="hidden" class="sub_pos" />
                  <span class="icon mp-icon mp-icon-post-type mp-icon-post-type-<?php echo $menu["name"] ?>"></span>
                  <span><?php echo $menu["label"] ?></span>
                </li>
                <?php
                endif; 
                ?>
              
                <?php endforeach; ?>
                
                        
                <li class="nomove holder"></li>
              </ul>
              
              </div>
              <!-- /.lists -->
              
              <div class="help">
              <p>
                <?php _e("Drag post type to the desired position in the admin menu.", MASTERPRESS_DOMAIN) ?>
              </p>

              <p>
                <?php _e("Note: You cannot adjust the positions of the built-in menus, but you can position items before and after them (with the exception that items must be positioned after <em>Posts</em>).", MASTERPRESS_DOMAIN); ?> 
              </p>

              <p>
                <em><?php _e("The menu presented here is a representation only, and does not include other menus registered by your theme and other plug-ins.", MASTERPRESS_DOMAIN); ?></em> 
              </p>
              </div>
            
            </div>
            <!-- /.sortable-list -->
            
          </div>
        </div>
        <!-- /.f -->        

      
      </div>
      </div>

    </div>
    <!-- /.fs -->

    <?php endif; ?>
    
      
      
    
    <?php if (!$model->_builtin && !$model->_external) : ?>
    
    <div class="fs fs-url-options clearfix">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="globe"></i><strong><?php _e("URL Options", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("controls how your post type is accessible via URLs in your site", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
  
    
      <div class="fsc clearfix">
      <div class="fscb clearfix">
      
      <div class="f">
        <label for="rewrite_slug" class="text"><?php _e("Rewrite Slug", MASTERPRESS_DOMAIN) ?>:</label>
        <div class="fw">
          <input id="rewrite_slug" name="rewrite[slug]" type="text" value="<?php echo $model->rewrite["slug"] ?>" class="text mono" />
          <p>
            <?php _e("The slug your post types will sit underneath in the URL structure.", MASTERPRESS_DOMAIN); ?><br />
            <?php _e("The default value follows the popular convention of using the lowercase sanitized version of <em>Plural Name</em>.", MASTERPRESS_DOMAIN); ?>
          </p>
        </div>
      </div>
      <!-- /.f -->


      <div class="fw clearfix">
        <input id="has_archive" name="has_archive" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->has_archive ) ?> class="checkbox" />
        <label for="has_archive" class="checkbox"><?php printf( __("Has Archive Page? - <span>allows an archive page for this post type, using the template named %s", MASTERPRESS_DOMAIN ), 'archive-<em class="post-type-name fill { src: \'#name\', format: \'dasherize\' }\">'.$model->name.'</em>.php</span>') ?></label>
      </div>
    
      <div class="fw clearfix">
        <input id="rewrite_with_front" name="rewrite[with_front]" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->rewrite["with_front"] ) ?> class="checkbox" />
        <label for="rewrite_with_front" class="checkbox"><?php _e("With Front? - <span>Append the above slug to the top level URL set in your permalink settings.</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->

      <div class="fw">
        <input id="rewrite_feeds" name="rewrite[feeds]" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->rewrite["feeds"] ) ?> class="checkbox" />
        <label for="rewrite_feeds" class="checkbox"><?php _e("Has Feeds? - <span>Create RSS / ATOM feeds for this post type.</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->

      <div class="f clearfix">
        <label for="permalink_epmask" class="text"><?php _e("Permalink End-Point Mask", MASTERPRESS_DOMAIN) ?>:</label>
        <div class="fw">
          
          <?php
          
          //$options = array("EP_PERMALINK", "EP_ATTACHMENT", "EP_DATE", "EP_YEAR", "EP_MONTH", "EP_DAY", "EP_ROOT", "EP_COMMENTS", "EP_SEARCH", "EP_CATEGORIES", "EP_TAGS", "EP_AUTHORS", "EP_PAGES", "EP_ALL");
          //echo WOOF_HTML::select( array("multiple" => "multiple", "id" => "permalink_epmask", "name" => "permalink_epmask"), $options, $model->permalink_epmask );

          ?>
          
          <input id="permalink_epmask" name="permalink_epmask" type="text" value="<?php echo $model->permalink_epmask ?>" class="text mono" />
          <p>
            <?php _e('Separate multiple end-point constants with a pipe. e.g. <span class="tt">EP_PAGES | EP_TAGS</span>', MASTERPRESS_DOMAIN); ?>
          </p>
        </div>
      </div>
      <!-- /.f -->
              
                    
      <div class="fw clearfix">
        <input id="supports_top_level_slugs" name="supports[]" value="top-level-slugs" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("top-level-slugs", $model->supports) ) ?> type="checkbox" class="checkbox" />
        <label for="supports_top_level_slugs" class="checkbox"><?php _e("Allow Rewrite Slug to be ommitted?", MASTERPRESS_DOMAIN); ?><span> - <?php _e("allows you to also access posts of this type <em>without</em> the rewrite slug in the URL.<br/>Be careful with this setting - it may affect site performance, and pages with the same slug as a post of this type will no longer be accessible.", MASTERPRESS_DOMAIN) ?></span></label>
      </div>
      <!-- /.fw -->

      </div>
      </div>

    </div>
    <!-- /.fs -->

    <div class="fs fs-query-data-options">
      
      <div class="fst">
      <div class="fstb">
        <h3><i class="database"></i><strong><?php _e("Query &amp; Data Options", MASTERPRESS_DOMAIN) ?></strong> - <?php _e(" controls the visibility of this post type in database queries and site searches, and exportability", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
      
      <div class="fw fw">
        <input id="publicly_queryable" name="publicly_queryable" value="true" <?php echo WOOF_HTML::checked_attr( $model->publicly_queryable ) ?> type="checkbox" class="checkbox" />
        <label for="publicly_queryable" class="checkbox"><?php _e("Publicly Queryable? - <span>Allow queries on this post type from the front-end WordPress API</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->


      <div class="f">
        <label for="query_var" class="text"><?php _e("Query Variable", MASTERPRESS_DOMAIN) ?>:</label>
        <div class="fw">
          <input id="query_var" name="query_var" type="text" value="<?php echo $model->query_var ?>" class="text mono" />
          <p>
            <?php _e('Enter the query variable used to query posts of this type with <span class="tt">query_posts</span> or <span class="tt">WP_Query</span>. Generally this should simply be the default value of the <em>Singular Name</em>, unless you have a good reason to change it.', MASTERPRESS_DOMAIN) ?>
          </p>
        </div>
      </div>
      <!-- /.f -->
      
      
      <div class="fw">
        <input id="exclude_from_search" name="exclude_from_search" value="true" <?php echo WOOF_HTML::checked_attr( $model->exclude_from_search ) ?> type="checkbox" class="checkbox" />
        <label for="exclude_from_search" class="checkbox"><?php _e("Exclude from Search? - <span>Hide items of this post type in site search results</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->

      
      <div class="fw">
        <input id="can_export" name="can_export" value="true" <?php echo WOOF_HTML::checked_attr( $model->can_export ) ?> type="checkbox" class="checkbox" />
        <label for="can_export" class="checkbox"><?php _e("Exportable? - <span>Allow this post type to be exported via the WordPress Export Tool</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->

      
      </div>
      </div>

    </div>
    <!-- /.fs -->
        
    <?php endif; ?>
    
    <?php if (!$model->_external) : ?>
    
    <div class="fs fs-labels">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="label-string"></i><strong><?php _e("Labels", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("displayed throughout the WordPress administration UI", MASTERPRESS_DOMAIN) ?></h3>

        <div class="buttons">
          <button id="autofill-labels" class="button button-autofill" type="button"><?php _e('<strong>Auto-Fill</strong> Labels', MASTERPRESS_DOMAIN) ?></button>
        </div>

      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
      
        <div class="f">
          <label for="label_singular_name"><?php _e("<em>Singular</em> Name:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_singular_name" name="labels[singular_name]" value="<?php echo $model->labels["singular_name"] ?>" type="text" class="text { tmpl: '{{singular_name}}' }" />
            <em class="recommended">(<?php _e("recommended", MASTERPRESS_DOMAIN) ?>)</em>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_name"><?php _e("<em>Plural</em> Name:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_name" name="labels[name]" value="<?php echo $model->labels["name"] ?>"  type="text" class="text { tmpl: '{{plural_name}}' }" />
            <em class="recommended">(<?php _e("recommended", MASTERPRESS_DOMAIN) ?>)</em>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_menu_name"><?php _e("Menu Name:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_menu_name" name="labels[menu_name]" value="<?php echo $model->labels["menu_name"] ?>" type="text" class="text { tmpl: '<?php _e("{{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("The name to give menu items", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->


        <div class="f">
          <label for="label_add_new"><?php _e("Add New:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_add_new" name="labels[add_new]" value="<?php echo $model->labels["add_new"] ?>"  type="text" class="text { tmpl: '<?php _e("Add New", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Menu label for creating a post of this type", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_all_items"><?php _e("All Items:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_all_items" name="labels[all_items]" value="<?php echo $model->label("all_items") ?>"  type="text" class="text { tmpl: '<?php _e("All {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("The all items text used in the menu", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->
        
        <div class="f">
          <label for="label_add_new_item"><?php _e("Add New Item:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_add_new_item" name="labels[add_new_item]" value="<?php echo $model->labels["add_new_item"] ?>" type="text" class="text { tmpl: '<?php _e("Add New {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }"  />
            <span class="fdesc"><?php _e("Header shown when creating a new item of this type", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_edit"><?php _e("Edit:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_edit" name="labels[edit]" value="<?php echo $model->labels["edit"] ?>" type="text" class="text { tmpl: '<?php _e("Edit", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Menu label for editing posts of this type", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_edit_item"><?php _e("Edit Item:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_edit_item" name="labels[edit_item]" value="<?php echo $model->labels["edit_item"] ?>" type="text" class="text { tmpl: '<?php _e("Edit {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Header shown when editing posts of this type", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_new_item"><?php _e("New Item Label:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_new_item" name="labels[new_item]" value="<?php echo $model->labels["new_item"] ?>" type="text" class="text { tmpl: '<?php _e("New {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Shown in the favorites menu in the admin header", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_view"><?php _e("View:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_view" name="labels[view]" value="<?php echo $model->labels["view"] ?>" type="text" class="text { tmpl: '<?php _e("View {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Used as text in links to view posts of this type", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_view_item"><?php _e("View Item:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_view_item" name="labels[view_item]" value="<?php echo $model->labels["view_item"] ?>" type="text" class="text { tmpl: '<?php _e("View {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Text for the button alongside the permalink on the edit post screen", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_search_items"><?php _e("Search Items:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_search_items" name="labels[search_items]" value="<?php echo $model->labels["search_items"] ?>" type="text" class="text { tmpl: '<?php _e("Search {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Button text for the search box on the edit post screen", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_not_found"><?php _e("Not Found:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_not_found" name="labels[not_found]" value="<?php echo $model->labels["not_found"] ?>" type="text" class="text { lowercase: true, tmpl: '<?php _e("No {{plural_name}} found", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Text to display when no posts of this type are found through search in the admin", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_not_found_in_trash"><?php _e("Not Found In Trash:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_not_found_in_trash" name="labels[not_found_in_trash]" value="<?php echo $model->labels["not_found_in_trash"] ?>" type="text" class="text { lowercase: true, tmpl: '<?php _e("No {{plural_name}} found in Trash", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Text to display when no posts of this type are in the trash", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f hierarchical-only">
          <label for="label_parent_item_colon"><?php _e("Parent Item (Colon):", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_parent_item_colon" name="labels[parent_item_colon]" value="<?php echo $model->labels["parent_item_colon"] ?>" type="text" class="text { tmpl: '<?php _e("Parent {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("The label for the parent selector on the edit posts screen (hierarchical only)", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_no_posts"><?php _e("No Posts:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_no_posts" name="labels[no_posts]" value="<?php echo $model->label("no_posts") ?>" type="text" class="text { tmpl: '<?php _e("No {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Used when listing posts in a master-detail relationship", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_one_post"><?php _e("One Post:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_one_post" name="labels[one_post]" value="<?php echo $model->label("one_post") ?>" type="text" class="text { tmpl: '<?php _e("1 {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Used when listing posts in a master-detail relationship", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->
        
        <div class="f">
          <label for="label_n_posts"><?php _e("n Posts:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_n_posts" name="labels[n_posts]" value="<?php echo $model->label("n_posts") ?>" type="text" class="text { tmpl: '<?php _e("%d {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Used when listing posts in a master-detail relationship", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_remove_post"><?php _e("Remove Post:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_remove_post" name="labels[remove_post]" value="<?php echo $model->label("remove_post") ?>" type="text" class="text { tmpl: '<?php _e("Remove {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
            <span class="fdesc"><?php _e("Used when listing posts in a master-detail relationship", MASTERPRESS_DOMAIN); ?></span>
          </div>
        </div>
        <!-- /.f -->

      
      </div>
      </div>

    </div>
    <!-- /.fs -->
    <?php endif; ?>
  
    <?php if (!$model->_builtin && !$model->_external) : ?>

    <div class="fs fs-capability-keys">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="key"></i><strong><?php _e("Capabilities", MASTERPRESS_DOMAIN) ?></strong> - <?php _e('the keys used to control access to this post type.', MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
      
        <div class="fw fwl">
          <input id="capability_type_post" name="capability_type" value="post" <?php echo WOOF_HTML::checked_attr( $model->capability_type == "post" ) ?> type="radio" class="radio" />
          <label for="capability_type_post" class="radio"><?php _e('<em>Post</em> style, based on the partial key <span class="tt">post</span>', MASTERPRESS_DOMAIN) ?></label>
          
          <div class="eg">
            <p><strong><?php _e("primitive capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_posts, edit_others_posts, publish_posts, read_private_posts</span></p>
            <p><strong><?php _e("meta capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_post, read_post, delete_post, read, delete_posts, delete_private_posts, delete_published_posts, delete_others_posts, edit_private_posts, edit_published_posts</span></p>          
          </div>
        </div>
        <!-- /.fw -->

        <div class="fw fwl">
          <input id="capability_type_page" name="capability_type" value="page" <?php echo WOOF_HTML::checked_attr( $model->capability_type == "page" ) ?> type="radio" class="radio" />
          <label for="capability_type_page" class="radio"><?php _e('<em>Page</em> style, based on the partial key <span class="tt">page</span>', MASTERPRESS_DOMAIN) ?></label>

          <div class="eg">
            <p><strong><?php _e("primitive capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_pages, edit_others_pages, publish_pages, read_private_pages</span></p>
            <p><strong><?php _e("meta capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_page, read_page, delete_page, read, delete_pages, delete_private_pages, delete_published_pages, delete_others_pages, edit_private_pages, edit_published_pages</span></p>          
          </div>

        </div>
        <!-- /.fw -->

        <div class="fw fwl">
          <input id="capability_type_specific" name="capability_type" value="specific" <?php echo WOOF_HTML::checked_attr( $model->capability_type == "specific" || $model->capability_type == $model->name ) ?> type="radio" class="radio" />
          <label for="capability_type_specific" class="radio"><?php _e("<em>Specific</em>, based on <em>Singular Name</em>", MASTERPRESS_DOMAIN) ?></label>

          <div class="eg">
            <p><strong><?php _e("primitive capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_<span class="fill { src: '#name' }">?</span>s, edit_others_<span class="fill { src: '#name' }">?</span>s, publish_<span class="fill { src: '#name' }">?</span>s, read_private_<span class="fill { src: '#name' }">?</span>s</span></p>
            <p><strong><?php _e("meta capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_<span class="fill { src: '#name' }">?</span>, read_<span class="fill { src: '#name' }">?</span>, delete_<span class="fill { src: '#name' }">?</span>, read, delete_<span class="fill { src: '#name' }">?</span>s, delete_private_<span class="fill { src: '#name' }">?</span>s, delete_published_<span class="fill { src: '#name' }">?</span>s, delete_others_<span class="fill { src: '#name' }">?</span>s, edit_private_<span class="fill { src: '#name' }">?</span>s, edit_published_<span class="fill { src: '#name' }">?</span>s</span></p>          
          </div>

        </div>
        <!-- /.fw -->

        <div id="fw_capability_type_custom" class="fw fwl">
          
          <?php
          
          $custom_value = "";
          $custom_checked = false;
          
          if ($model->capability_type != $model->name && $model->capability_type != "post" && $model->capability_type != "page") {
            $custom_checked = true;
            $custom_value = $model->capability_type;
          }
          
          
          ?>
          <input id="capability_type_custom" name="capability_type" value="custom" <?php echo WOOF_HTML::checked_attr( $custom_checked ) ?> type="radio" class="radio" />
          <label id="label-capability_custom_value" for="capability_type_custom" class="radio"><?php _e("<em>Custom</em>, based on the partial key (singular)", MASTERPRESS_DOMAIN) ?>:</label>

          
          <input id="capability_type_custom_value" name="capability_type_custom_value" value="<?php echo $custom_value ?>" type="text" class="text mono" />
          
          <div class="eg">
          <p><strong><?php _e("primitive capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, edit_others_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, publish_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, read_private_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span></span></p>
          <p><strong><?php _e("meta capabilities", MASTERPRESS_DOMAIN) ?>:</strong> <span class="tt">edit_<span class="custom-fill { src: '#capability_type_custom_value' }">?</span>, read_<span class="custom-fill { src: '#capability_type_custom_value' }">?</span>, delete_<span class="custom-fill { src: '#capability_type_custom_value' }">?</span>, read, delete_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, delete_private_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, delete_published_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, delete_others_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, edit_private_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span>, edit_published_<span class="custom-fill { src: '#capability_type_custom_value', format: 'pluralize' }">?</span></span></p>          
          </div>
          

        </div>
        <!-- /.fw -->
                 
        <div class="fw">
          <input id="map_meta_cap" name="map_meta_cap" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->map_meta_cap ) ?> class="checkbox" />
          <label for="map_meta_cap" class="checkbox"><?php printf( __('Map Meta Capabilities? - <span>whether to use the internal default meta capability handling. See the section on the <span class="tt">capabilities</span> argument %s</span>', MASTERPRESS_DOMAIN ), '<a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments" target="_blank">'.__(" here", MASTERPRESS_DOMAIN).'</a>') ?></label>
        </div>
     
      
      </div>
      </div>

    </div>
    <!-- /.fs -->

    
    <?php endif; // !$model->_builtin ?>

    

    <?php
  } // end form

  
}

?>