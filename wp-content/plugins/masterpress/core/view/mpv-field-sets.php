<?php

class MPV_FieldSets extends MPV {
  
  public static function __s() {
    return __("Post Type Field Set", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("Post Type Field Sets", MASTERPRESS_DOMAIN);
  }

  public static function __s_short() {
    return __("Field Set", MASTERPRESS_DOMAIN);
  }

  public static function __p_short() {
    return __("Field Sets", MASTERPRESS_DOMAIN);
  }




  public function confirm_delete_field_panel($field, $object_name, $cancel_link) {
    ?>
    
    <div class="panel delete-panel delete-field-panel">

      <?php $this->form_open() ?> 
      <div class="panel-content">
        <header class="title">
          <h1><?php printf( __("Are you sure you want to delete the %s <em>%s</em>?", MASTERPRESS_DOMAIN), $object_name, $field->display_label() ); ?></h1>
          <div class="actions">
            <button class="button-primary button-delete" type="submit"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></button>
            <?php echo $cancel_link; ?>
          </div>  
        </header>

        <?php $meta_count = $field->meta_count(); ?>

        <?php if ($meta_count) : ?>
        
        <div class="panel-divider"><span><span>&nbsp;</span></span></div>
        
        <div class="content">

        <p>
        <?php printf( __("This field has <strong>%s</strong>.", MASTERPRESS_DOMAIN ), MPU::__items($meta_count, __("%d custom field data record", MASTERPRESS_DOMAIN), __("%d custom field data records", MASTERPRESS_DOMAIN ) )) ?><br />
        <?php _e("Please indicate how you would like to handle this related information upon deletion:", MASTERPRESS_DOMAIN); ?>
        </p>
        
        <div class="f">
          <span class="label label-icon"><i class="database"></i><?php _e("Field (Meta) Data:", MASTERPRESS_DOMAIN) ?></span>
          <div class="fw">
            <input id="field_data_delete" name="field_data" type="radio" value="delete" checked="checked" class="radio" /> 
            <label for="field_data_delete" class="radio"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></label>
            <input id="field_data_keep" name="field_data" type="radio" value="keep" class="radio" /> 
            <label for="field_data_keep" title="<?php _e("Data is stored in the Wordpress postmeta table.", MASTERPRESS_DOMAIN) ?>" class="radio with-tooltip"><?php _e("Keep in Database", MASTERPRESS_DOMAIN) ?></label>

          </div>
          <!-- /.fw -->
        </div>
        <!-- /.f -->
        
        </div>
        <!-- /.content -->
        
        <?php endif; ?>
      
      </div>
      <!-- /.panel-content -->
 
      </form>
    </div>
    
    <?php
  }

  public function confirm_delete_panel($field_set, $object_name, $cancel_link) {
    ?>
    
    <div class="panel delete-panel delete-field-set-panel">
      <?php $this->form_open() ?> 
      <div class="panel-content">
        <header class="title">
          <h1><?php printf( __("Are you sure you want to delete the %s <em>%s</em>?", MASTERPRESS_DOMAIN), $object_name, $field_set->display_label() ); ?></h1>
          <div class="actions">
            <button class="button-primary button-delete" type="submit"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></button>
            <?php echo $cancel_link; ?>
          </div>  
        </header>
 
        <!-- /.title -->

        <?php $meta_count = $field_set->meta_count(); ?>

        <?php if ($meta_count) : ?>
        
        <div class="panel-divider"><span><span>&nbsp;</span></span></div>
        
        <div class="content">
          
          <p>
          <?php printf( __("This field set has <strong>%s</strong>.", MASTERPRESS_DOMAIN ), MPU::__items($meta_count, __("%d custom field data record", MASTERPRESS_DOMAIN), __("%d custom field data records", MASTERPRESS_DOMAIN ) ) ); ?><br />
          <?php _e("Please indicate how you would like to handle this related information upon deletion:", MASTERPRESS_DOMAIN); ?>
          <?php if (is_multisite()) : ?>
          <?php _e("<br /><b>Note:</b> If you choose to <em>Delete</em> data, it will only be removed from the <b>current site</b> in your multisite network.", MASTERPRESS_DOMAIN); ?>
          <?php endif; ?>
          </p>


          <div class="f">
            <span class="label label-icon"><i class="database"></i><?php _e("Field Set (Meta) Data:", MASTERPRESS_DOMAIN) ?></span>
            <div class="fw">
              <input id="field_data_delete" name="field_data" type="radio" value="delete" class="radio"  checked="checked" /> 
              <label for="field_data_delete" class="radio"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></label>
              <input id="field_data_keep" name="field_data" type="radio" value="keep" class="radio" /> 
              <label for="field_data_keep" title="stored in the post meta table" class="radio with-tooltip"><?php _e("Keep in Database", MASTERPRESS_DOMAIN) ?></label>

            </div>
            <!-- /.fw -->
          </div>
          <!-- /.f -->


          
        </div>
        <!-- /.content -->
         
        <?php endif; ?>


      </div>
      <!-- /.panel-content -->
 
      </form>
    </div>
    
    <?php
  }
  
  public function confirm_delete($field_set) {
    $info = MasterPress::$view;
    $parent = $info->parent;
    MPV_FieldSets::confirm_delete_panel($field_set, __("Field Set", MASTERPRESS_DOMAIN), MPV::action_link("post-types", "manage-field-sets", __("Cancel", MASTERPRESS_DOMAIN), "parent=".$parent->id, array( "class" => "button button-small primary" )) );
  }
  
  public function confirm_delete_field($field) {
    $info = MasterPress::$view;
    $parent = $info->parent;
    MPV_FieldSets::confirm_delete_field_panel($field, __("Field", MASTERPRESS_DOMAIN), MPV::action_link("post-types", "manage-field-sets", __("Cancel", MASTERPRESS_DOMAIN), "parent=".$parent->id, array( "class" => "button button-small primary" )) );
  }
  
  public function grid() {
    
    
    MPC::incl("post-types");
    MPV::incl("fields");
    MPV::incl("post-types");
    
    $info = MasterPress::$view;
    
    $parent = $info->parent;

    $has_actions = MasterPress::current_user_can("edit_post_type_field_sets,delete_post_type_field_sets,edit_post_type_fields,delete_post_type_fields");
    $can_edit = MasterPress::current_user_can("edit_post_type_field_sets");
    $can_delete = MasterPress::current_user_can("delete_post_type_field_sets");
    $can_create = MasterPress::current_user_can("create_post_type_field_sets");

    $can_create_fields = MasterPress::current_user_can("create_post_type_fields");
    $can_edit_fields = MasterPress::current_user_can("edit_post_type_fields");
    $can_delete_fields = MasterPress::current_user_can("delete_post_type_fields");

    $less = ($can_create_fields) ? 1 : 0;

    $colspan = ( $has_actions ? 4 : 3 ) - $less;
    
    ?>
    
    <div class="title-info-panel">
      <div class="title-info-panel-lt">
        <?php if (get_class($parent) == "MPM_PostType") : ?>
        <h3 class="post-type"><i class="mp-icon mp-icon-post-type mp-icon-post-type-<?php echo $parent->name ?>"></i><span class="label"><?php echo MPV_PostTypes::__s() ?>:</span><span class="value"> <?php echo $parent->display_label() ?></span></h3> 
        <input id="post_type_name" type="hidden" value="<?php echo $parent->name ?>" />
        <?php endif; ?>
      </div>
    </div>

    
    
    <?php MPV::messages(); ?>

    <?php $field_sets = MPM_FieldSet::find_by_post_type($parent, "name ASC"); ?>
    
    <?php MPV::field_set_icon_styles($field_sets); ?>
    
    <?php
    
    foreach ($field_sets as $field_set) {

      if (MPC::is_deleting($field_set->id, "delete-field-set")) {
        
        $this->confirm_delete($field_set);
        
      } else {
        
        foreach ($field_set->fields() as $field) {
          
          if (MPC::is_deleting($field->id, "delete-field")) {
            $this->confirm_delete_field($field);
          }
          
        }
        
      }
      
    }
    
    ?>
    
    
    
    <?php if (count($field_sets)) : ?>
      
    <table cellspacing="0" class="grid grid-field-sets">

    <thead>
    <tr>
      <th class="first type"><i class="types"></i><span><?php _e("Type", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="label"><i class="label-string"></i><span><?php _e("Label", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="front-end-name <?php echo $has_actions ? "" : "last" ?>"><i class="script-php"></i><span><?php _e("Front End Name", MASTERPRESS_DOMAIN) ?></span></th>
      <?php if ($has_actions) : ?>
      <th class="actions last"><i class="buttons"></i><span><?php _e("Actions", MASTERPRESS_DOMAIN) ?></span></th>
      <?php endif; ?>
    </tr>
    </thead>
   
    <tbody>
    
    

    <?php $fg_count = 0; ?>
    <?php foreach ($field_sets as $field_set) : $fg_count++; ?>  
      
    <?php $display = $field_set->display_label(); ?>

    <?php
    
      $display_td = $display;
      
      if ($field_set->icon != "") {
        $display_td = WOOF_HTML::tag("span", array("class" => "with-icon field-set-".$field_set->id), $display);
      }

    ?>
    
    <?php 
    
    $deleting_class = MPC::is_deleting($field_set->id, "delete-field-set") ? 'deleting' : ''; 
    $editable_class = $can_edit ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("post-types", "edit-field-set", array("id" => $field_set->id, "parent" => MasterPress::$parent) )."' }" : "";
    
    ?>
    
    <?php $disabled = $field_set->disabled ? "disabled" : ""; $title = $field_set->disabled ? ' title="'.__("this field set is disabled", MASTERPRESS_DOMAIN).'" ' : ""; ?>
    
    <?php
    if (!$field_set->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("field set is not currently available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
    }
    ?>
    
    <tr <?php echo $title ?> class="<?php echo $disabled ?> <?php echo $editable_class.$deleting_class ?> <?php echo MPV::updated_class("edit-field-set,create-field-set", $field_set->id) ?> <?php echo $meta ?>">
      <?php if ($field_set->allow_multiple) : ?>
      <th class="first type icon"><i class="metabox-add-remove-large" title="<?php _e("Field Set (Multiple Items)", MASTERPRESS_DOMAIN) ?>"></i></th>
      <?php else : ?>
      <th class="first type icon"><i class="metabox-large" title="<?php _e("Field Set", MASTERPRESS_DOMAIN) ?>"></i></th>
      <?php endif; ?>
      <th class="label"><strong><?php echo $display_td ?></strong></th>
      <th class="front-end-name <?php echo $has_actions ? "" : "last" ?>"><span class="tt"><?php echo $field_set->display_name() ?></span></th>

      <?php if ($has_actions) : ?>

      <th class="actions last">
      <div>
      <?php if (MPC::is_deleting($field_set->id, "delete-field-set")) : ?>
        <span class="confirm-action">&nbsp;</span>
      <?php else: ?>
        <?php if ($can_edit) : ?>
          <?php echo MPV::action_button("post-types", "edit-field-set", self::__edit(), array("id" => $field_set->id, "parent" => MasterPress::$parent), array("class" => "button button-edit") ); ?>
        <?php endif; ?>

        <?php if ($can_delete) : ?>
          <?php echo MPV::action_button("post-types", "delete-field-set", self::__delete(), array("id" => $field_set->id, "parent" => MasterPress::$parent), array("class" => "button button-delete") ); ?>
        <?php endif; ?>
      <?php endif; ?>
      </div>
      </th>

      <?php endif; // has_actions ?>

    </tr>  
    
        
    <?php 

    $count = 0; 
    $fields = $field_set->fields();
    
    ?>
    
    <?php foreach ($fields as $field) : $count++; $first = $count == 1 ? 'first' : ''; $disabled = $field_set->disabled || $field->disabled ? "disabled" : ""; $title = ($field_set->disabled || $field->disabled) ? ' title="'.__("this field is disabled", MASTERPRESS_DOMAIN).'" ' : ""; ?>
    
    <?php
    if (!$field_set->in_current_site() || !$field->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("field is not currently available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
    }
    ?>
    
    <?php 
    
    $deleting_class = MPC::is_deleting($field_set->id, "delete-field-set") || MPC::is_deleting($field->id, "delete-field") ? 'deleting' : ''; 
    $editable_class = $can_edit_fields ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("post-types", "edit-field", array("id" => $field->id, "gparent" => $_GET["parent"], "parent" => $field_set->id) )."' }" : "";

    
    ?>

  
    <?php
    
    if ($type_class = MPFT::type_class($field->type)) : ?>
    

    <tr <?php echo $title ?> class="sub <?php echo $editable_class.$deleting_class ?> <?php echo $disabled ?> <?php echo $first ?> <?php echo $count % 2 == 0 ? "even" : "" ?> <?php echo MPV::updated_class("edit-field,create-field", $field->id) ?> <?php echo $meta ?>">
      <td class="type icon first" title="<?php echo call_user_func( array($type_class, "__s") ) ?>"><span class="mp-icon mp-icon-field-type-<?php echo $field->type ?>"></span></td>
      <td class="label"><strong><?php echo $field->display_label() ?></strong></td>
      <td class="front-end-name <?php echo $has_actions ? "" : "last" ?>"><span class="tt"><span class="arrow">-&gt;&nbsp;</span><?php echo $field->display_name() ?></span></td>

      <?php if ($has_actions) : ?>

      <td class="actions last">
      <div>
      <?php if (MPC::is_deleting($field->id, "delete-field")) : ?>
        <span class="confirm-action">&nbsp;</span>
      <?php else: ?>
        <?php if ($can_edit_fields) : ?>
          <?php echo MPV::action_button("post-types", "edit-field", self::__edit(), array("id" => $field->id, "gparent" => $_GET["parent"], "parent" => $field_set->id), array("class" => "button button-edit") ); ?>
        <?php endif; ?>

        <?php if ($can_delete_fields) : ?>
          <?php echo MPV::action_button("post-types", "delete-field", self::__delete(), array("id" => $field->id, "gparent" => $_GET["parent"], "parent" => $parent->id), array("class" => "button button-delete") ); ?>
        <?php endif; ?>
      <?php endif; ?>
      </div>
      </td>

      <?php endif; // has_actions ?>
      
    </tr>
   
    <?php else: ?>
    <!-- TODO - TYPE CLASS DOESN'T EXIST -->
    
    <?php endif; ?>
  
    <?php endforeach; ?>
  
    <tr class="summary <?php echo $can_create_fields ? "editable" : "" ?>">
      <?php if (count($fields)) : ?>
      <td colspan="<?php echo $colspan ?>" class="first <?php echo $can_create_fields ? "" : "last" ?>"><?php printf( __( "%s contains %s", MASTERPRESS_DOMAIN ), $display, MPU::__items( $field_set->field_count(), __("%d custom field", MASTERPRESS_DOMAIN), __("%d custom fields", MASTERPRESS_DOMAIN) )  ) ?></td>
      <?php else: ?>
      <td colspan="<?php echo $colspan ?>" class="hl first last"><span><?php printf( __( "<strong>%s</strong> is not yet active as it contains <em>no custom fields</em>. Click here to create one", MASTERPRESS_DOMAIN ), $display ); ?></span></td>
      <?php endif; ?>

      <?php if ($can_create_fields) : ?>
      <td class="last actions <?php echo count($fields) ? "" : "hl" ?>">
      <?php echo MPV::action_button("post-types", "create-field", self::__create(MPV_Fields::__s()), array("gparent" => $_GET["parent"], "parent" => $field_set->id), array("class" => "button create-field") ); ?>
      </td>
      <?php endif; // can_create_fields ?>

    </tr>
    <tr class="gap <?php if ($fg_count == count($field_sets)) { echo "gap-last"; } ?>">
    <td colspan="4">&nbsp;</td>  
    </tr>
    
  
    <?php endforeach; ?>

    </tbody>
  
    </table>

    <?php if ($can_create) : ?>
    <div class="grid-foot-controls">
    <?php echo MPV::action_button("post-types", "create-field-set", MPV::__create( MPV_FieldSets::__s() ), array("parent" => $parent->id ) ); ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
  
    <?php if ($can_create) : ?>
    <a href="<?php echo MasterPress::admin_url("post-types", "create-field-set", array("parent" => MasterPress::$parent)) ?>" class="no-items-create">
    <i class="plus-circle"></i>
	<span><?php printf( __( "The <strong>%s</strong> post type currently has <em>no field sets</em>. Click here to create one.", MASTERPRESS_DOMAIN ), $parent->display_label() );  ?></span>
    </a>
    <?php endif; ?>
    
    <?php endif; ?>
  
    <?php
     
  } // end grid()


  public function form() {
    $model = MasterPress::$model;
    $info = MasterPress::$view;
    
    $parent = $info->parent;
    
    global $wf;
    
    $prefix = "";
    $default_key = "edit_posts";
    $keys = array();


    ?>
    
    <div class="mpv-field-sets-form">
      
    <?php if (isset(MasterPress::$parent)) : ?>
    <div class="title-info-panel">
      <div class="title-info-panel-lt">
        <?php if (get_class($parent) == "MPM_PostType") : ?>
          
          <?php
          
          $post_type = $parent;
          
          $prefix = $post_type->name."_"; 
          $default_key = "edit_".$post_type->capability_type."s";
          $keys = array($default_key);
          
          ?>
          
          <h3 class="post-type"><i class="mp-icon mp-icon-post-type mp-icon-post-type-<?php echo $post_type->name ?>"></i><span class="label"><?php _e("Post Type:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $parent->labels["name"] ?></span></h3> 
          <input id="post_type_name" type="hidden" value="<?php echo $parent->name ?>" />
        <?php elseif ($model->template) : ?>

          <?php
        
          $file = MasterPress::$parent;
          $page_templates = array_flip(get_page_templates());
          $template_name = $page_templates[$file];
        
          ?>
          <input id="templates" name="templates" type="hidden" value="<?php echo $file ?>" />
          <h3><i class="template"></i><span class="label"><?php _e("Template:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $template_name ?></span></h3> 
        
        <?php elseif ($model->role) : ?>

          <?php
        
          $role_name = MasterPress::$parent;
          $prefix = $role_name."_";

          ?>
          <input id="roles" name="roles" type="hidden" value="<?php echo $role_name ?>" />
          <h3><i class="user-role"></i><span class="label"><?php _e("User Role:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $role_name ?></span></h3> 

        <?php elseif ($model->taxonomy) : ?>
          
          <?php
        
          $taxonomy_id = MasterPress::$parent;
          
          $tax = MPM_Taxonomy::find_by_id($taxonomy_id);
          
          $prefix = $tax->name."_";

          $default_key = $tax->capabilities["edit_terms"];

          $keys = array(
            $tax->capabilities["manage_terms"],
            $tax->capabilities["edit_terms"],
            $tax->capabilities["assign_terms"]
          );


          ?>
          <input id="taxonomies" name="taxonomies" type="hidden" value="<?php echo $tax->name ?>" />
          <h3><i class="mp-icon mp-icon-taxonomy mp-icon-taxonomy-<?php echo $tax->name ?>"></i><span class="label"><?php _e("Taxonomy:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $tax->display_label() ?></span></h3> 

        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php MPV::messages(); ?>

    <div class="mpv-field-sets-form">
      
    <div class="f">
      <label for="name" class="icon"><i class="script-php"></i><?php _e("Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="name_original" name="name_original" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />
        <input id="name_last" name="name_last" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />
        <input id="name" name="name" type="text" class="text mono key" maxlength="255" value="<?php echo $model->name ?>" /><em class="required">(required)</em>
        <p>
          <?php _e("This is a unique identifier for the field set used in the MasterPress API. Since it can be used as a PHP variable name, it is restricted to being a lowercase string with words separated by underscores.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>

    </div>
    <!-- /.f -->

    <div id="f-singular_name" class="f">
      <label for="singular_name" class="icon" maxlength="255"><i class="script-php"></i><?php _e("<strong>Singular</strong> Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="singular_name" name="singular_name" type="text" value="<?php echo $model->singular_name ?>" class="text mono key" /><em class="required">(required)</em>

        <p>
          <?php _e("The singular form of <em>Name</em>, following the same naming conventions", MASTERPRESS_DOMAIN); ?>
        </p>

      </div>
    </div>
    <!-- /.f -->
  
    <div class="f">
      <label for="allow_multiple" class="icon"><i class="add-remove"></i><?php _e("Allow Multiple Items", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="allow_multiple" name="allow_multiple" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->allow_multiple ) ?> class="checkbox" />
        <p id="name_warning" class="warning">
		  <i class="error-circle"></i>
          <?php _e("For multiple-item field sets, <em>Name</em> should be specified in <strong>plural form</strong>. Please verify that this is correct above.", MASTERPRESS_DOMAIN) ?>
        </p>
      </div>

    </div>
    <!-- /.f -->
    
    
    
    <div class="f clearfix">
      <label id="label-icon" for="icon" class="icon"><i class="image-small"></i><?php _e("Title Icon (16 x 16)", MASTERPRESS_DOMAIN) ?>:</label>
      <div class="fw">

        <div id="icon-file-uploader" class="icon-uploader file-uploader { ids: { drop: 'icon_drop_area' }, input: '#icon', inputName: 'icon_ul', base_url: '<?php echo MASTERPRESS_GLOBAL_CONTENT_URL ?>', params: { dir: 'menu-icons/' }, limit: 1, lang: { buttonChoose: '<?php _e("Choose from Computer&hellip;", MASTERPRESS_DOMAIN) ?>', buttonReplace: '<?php _e("Replace file&hellip;", MASTERPRESS_DOMAIN) ?>' } }">
          
          <div id="icon_drop_area" class="drop-area"><?php _e("Drop file here to upload", MASTERPRESS_DOMAIN) ?></div>

          <?php 
          
          
          $file_name = $model->icon;
          $file_class = "";
          $clear_class = "";
          $preview_class = "";
          
          if ($file_name == "") {
            $file_name = __("( None )", MASTERPRESS_DOMAIN);
            $file_class = "name-none";
            $preview_class = "preview-none";
            $style = "";
            $clear_class = "hidden";
          } else {
            $style = ' style="background-image: url('.MPU::field_set_icon_url($model->icon).')" ';
          }
          
          
          ?>
          
          <div class="file">
            <span class="preview <?php echo $preview_class ?>" <?php echo $style ?>></span><span data-none="<?php echo __("( None )", MASTERPRESS_DOMAIN) ?>" class="name <?php echo $file_class ?>"><?php echo $file_name ?></span>
            <button type="button" class="<?php echo $clear_class ?> clear ir" title="<?php _e("Clear", MASTERPRESS_DOMAIN) ?>">Clear</button>
          </div>
          
          <input id="icon" name="icon" value="<?php echo $model->icon ?>" type="hidden" />
          <div class="uploader-ui"></div>
          
        </div>
        <!-- /.file-uploader -->
        
        <?php MPV::icon_select($model->icon, "menu-icon-select", "icon_select", "icon-file-uploader"); ?>
        

      </div>
    </div>
    <!-- /.f -->
        
    <div class="f">
      <label for="expanded" class="icon"><i class="expand"></i><?php _e("Expanded", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="expanded" name="expanded" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->expanded ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#expanded' }">
          <?php _e("this field set will be expanded (not summarised) by default in the edit post screen, but can still be collapsed. It is recommended that this is <strong>unchecked if <em>Allow Multiple Items</em> is checked.</strong>", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    <?php if (!$model->role) : ?>

    <div class="f">
      <label for="sidebar" class="icon"><i class="sidebar"></i><?php _e("Show in Sidebar", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="sidebar" name="sidebar" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->sidebar ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#sidebar' }">
          <?php _e("this field set will be positioned in the sidebar of the edit post screen by default (but may still be dragged across to the main content area by users)", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    <?php endif; ?>

    <div class="f">
      <label for="disabled" class="icon"><i class="slash"></i><?php _e("Disabled", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="disabled" name="disabled" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->disabled ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#disabled' }">
          <?php _e("disabling a field set will keep its definition in the database but it will not be available in any post editing screens in WordPress, which may be <strong>preferable to deleting it</strong> completely.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->
    
    <div class="f">
      <label for="versions_store" class="icon"><i class="metabox-versions size-20"></i><?php _e("Version Limit", MASTERPRESS_DOMAIN) ?></label>
      <div class="fw">
        <input id="versions" name="versions" type="text" value="<?php echo $model->versions ?>" class="text" />
        <p class="note">
          <?php _e("Set a <b>maximum number of versions</b> of content based on this field set to retain.<br />Versions can be used to restore previous revisions of content at a later time.<br />Set this value to 0 if you do not wish to retain previous versions.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->
      

    <div class="fs fs-labels">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="label-string"></i><strong><?php _e("Labels", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("displayed in the <em>Edit Post</em> and MasterPress admin screens", MASTERPRESS_DOMAIN) ?></h3>

        <div class="buttons">
          <button id="autofill-labels" class="button button-autofill" type="button"><?php _e('<strong>Auto-Fill</strong> Labels', MASTERPRESS_DOMAIN) ?></button>
        </div>


      </div>
      </div>
    
        
      <div class="fsc fscs">
      <div class="fscb">
      <div class="scroll">

        <div class="f">
          <label for="label_name"><?php _e("Set Label", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_name" name="labels[name]" value="<?php echo $model->labels["name"] ?>" type="text" class="text { tmpl: '{{plural_name}}' }" />
            <em class="recommended">(recommended)</em>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_description"><?php _e("Description", MASTERPRESS_DOMAIN)?>:</label>
          <div class="fw">
            <textarea id="label_description" name="labels[description]"><?php echo $model->label("description") ?></textarea>
            <p>
              <?php _e("Displayed on the Create / Edit Post screen in the info area above the fields in this set.", MASTERPRESS_DOMAIN); ?>
            </p>
          </div>
        </div>
        <!-- /.f -->

        <?php if ($model->shared) : ?>
        <div class="f">
          <label for="label_description_user"><?php _e("Description (User)", MASTERPRESS_DOMAIN)?>:</label>
          <div class="fw">
            <textarea id="label_description_user" name="labels[description_user]"><?php echo $model->label("description_user") ?></textarea>
            <p>
              <?php _e("Displayed on the Create / Edit User and Profile screen in the info area above the fields in this set.<br />If not specified, the standard <em>Description</em> label will be used (if available)", MASTERPRESS_DOMAIN); ?>
            </p>
          </div>
        </div>
        <!-- /.f -->
        
        <div class="f">
          <label for="label_description_term"><?php _e("Description (Term)", MASTERPRESS_DOMAIN)?>:</label>
          <div class="fw">
            <textarea id="label_description_term" name="labels[description_term]"><?php echo $model->label("description_term") ?></textarea>
            <p>
              <?php _e("Displayed on the Edit (Taxonomy) Term in the info area above the fields in this set.<br />If not specified, the standard <em>Description</em> label will be used (if available)", MASTERPRESS_DOMAIN); ?>
            </p>
          </div>
        </div>
        <!-- /.f -->
        <?php endif; ?>

        <div class="f f-allow-multiple">
          <label for="label_singular_name"><?php _e("<em>Singular</em> Name", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_singular_name" name="labels[singular_name]" value="<?php echo $model->label("singular_name") ?>" type="text" class="text { tmpl: '{{singular_name}}' }" />
            <em class="recommended">(recommended)</em>
          </div>
        </div>
        <!-- /.f -->

        <div class="f f-allow-multiple">
          <label for="label_add"><?php _e("Add", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_add" name="labels[add]" value="<?php echo $model->label("add") ?>" type="text" class="text { tmpl: '<?php _e("Add {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f f-allow-multiple">
          <label for="label_add_another"><?php _e("Add Another", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_add_another" name="labels[add_another]" value="<?php echo $model->label("add_another") ?>" type="text" class="text { tmpl: '<?php _e("Add Another {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->
              
        <div class="f f-allow-multiple">
          <label for="label_remove"><?php _e("Remove", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_remove" name="labels[remove]" value="<?php echo $model->label("remove") ?>" type="text" class="text { tmpl: '<?php _e("Remove {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }"  />
          </div>
        </div>
        <!-- /.f -->

        <div class="f f-allow-multiple">
          <label for="label_click_to_add"><?php _e("Click to Add", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_click_to_add" name="labels[click_to_add]" value="<?php echo $model->label("click_to_add") ?>" type="text" class="text { tmpl: '<?php _e("Click to add {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f f-allow-multiple">
          <label for="label_one_item"><?php _e("1 Item", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_one_item" name="labels[one_item]" value="<?php echo $model->label("one_item") ?>" type="text" class="text { tmpl: '<?php _e("1 {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f f-allow-multiple">
          <label for="label_n_items"><?php _e("n Items", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_n_items" name="labels[n_items]" value="<?php echo $model->label("n_items") ?>" type="text" class="text { tmpl: '<?php _e("%d {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->
        
        <div class="f f-allow-multiple">
          <label for="label_no_items"><?php _e("No Items", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_no_items" name="labels[no_items]" value="<?php echo $model->label("no_items") ?>" type="text" class="text { tmpl: '<?php _e("No {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

      </div>
      </div>
      </div>

    </div>
    <!-- /.fs -->



    <div class="fs fs-capability-keys clearfix">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="key"></i><strong><?php _e("Capabilities", MASTERPRESS_DOMAIN) ?></strong> - <?php _e('the keys used to control access to this field set', MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb clearfix">
      
        <div class="f f-capabilities f-capabilities-editable clearfix">
          <label for="label-capabilities-editable"><?php _e("Editable", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            
            <?php 
            
              $val = $model->capability("editable", false);
              
              $caps = array_unique(array_merge($keys, array("edit_posts", "edit_pages")));
              
              $caps = array( __("-- None (Always Editable) --", MASTERPRESS_DOMAIN) => "" ) + $caps;
              
              if (MPC::is_edit()) {
                $caps[] = "edit_".$prefix.$model->name;
              }

              $custom_val = "";
              
              if (!in_array($val, $caps)) {
                $custom_val = $val;
              } 

              echo WOOF_HTML::select( array("id" => "capabilities-editable", "name" => "capabilities[editable]", "class" => "capabilities" ), 
                $caps,
                $val
              );
      
            ?>
            
            <label for="capabilities-editable-custom" class="capabilities-custom"><?php _e("OR custom value") ?></label>
            <input id="capabilities-editable-custom" name="capabilities_custom[editable]" value="<?php echo $custom_val ?>" type="text" class="text mono capabilities-custom" />
            
            <p class="note">
              <?php _e("All fields in this set will be read-only for users without this capability.<br />Individual fields may override this with their own capabilities.", MASTERPRESS_DOMAIN) ?>
            </p>
          </div>
        </div>
        <!-- /.f -->
        
        <div class="f f-capabilities f-capabilities-visible clearfix">
          <label for="label-capabilities-visible"><?php _e("Visible", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            
            <?php 
            
              $val = $model->capability("visible", false);
              
              $caps = array_unique(array_merge($keys, array("edit_posts", "edit_pages")));

              $caps = array( __("-- None (Always Visible) --", MASTERPRESS_DOMAIN) => "" ) + $caps;
              
              if (MPC::is_edit()) {
                $caps[] = "view_".$prefix.$model->name;
              }

              $custom_val = "";
              
              if (!in_array($val, $caps)) {
                $custom_val = $val;
              } 

              echo WOOF_HTML::select( array("id" => "capabilities-visible", "name" => "capabilities[visible]", "class" => "capabilities" ), 
                $caps,
                $val
              );
      
            ?>
            
            <label for="capabilities-visible-custom" class="capabilities-custom"><?php _e("OR custom value") ?></label>
            <input id="capabilities-visible-custom" name="capabilities_custom[visible]" value="<?php echo $custom_val ?>" type="text" class="text mono capabilities-custom" />
            
            <p class="note">
              <?php _e("This field set will be invisible for users without this capability", MASTERPRESS_DOMAIN) ?>
            </p>
          </div>
        </div>
        <!-- /.f -->

        
        
        
        
      </div>
      </div>

    </div>
    <!-- /.fs -->
    

    <?php
    
    
    $args = array();

    $args["supports"] = array();
    
    if (is_multisite() && MASTERPRESS_MULTISITE_SHARING) {
      $args["supports"] = array("multisite");
    }
  
    if ($model->is_shared()) {
      $args["supports"][] = "post_types";
    }
    
    if (!isset($info->is_template_set) && $model->is_shared() ) {
      $args["supports"][] = "templates";
    }
    
    if (!isset($info->is_role_set) && $model->is_shared() ) {
      $args["supports"][] = "roles";
    }

    if (!isset($info->is_taxonomy_set) && $model->is_shared() ) {
      $args["supports"][] = "taxonomies";
    }

    $args["labels"] = array(
      "title" =>  __("control the visibility of this field set within WordPress", MASTERPRESS_DOMAIN),   
      "title_multisite" =>  __("specify the sites in the multisite network that this field set is available in", MASTERPRESS_DOMAIN),   
      "title_post_types" =>  __("specify the post types that this field set is available in", MASTERPRESS_DOMAIN),   
      "title_templates" => __("specify the templates that this field set is available in", MASTERPRESS_DOMAIN),   
      "title_roles" => __("specify the roles that users must have for this field set to be available in their profile edit screen", MASTERPRESS_DOMAIN),   
      "multisite_all" => __( "All Sites" ),
      "post_types_all" => __( "All Post Types" ),
      "templates_all" => __( "All Templates" )
      
    );

    $args["defaults"] = array(
      "post_types" => "all"
    );

  
    if ($model->type == "p") {
      
      $post_type = $parent;
      
      $templates = $post_type->templates();
      
      if (count($templates)) {
        $args["supports"][] = "templates";
        $args["templates"] = array();
        
        $all = array_flip(get_page_templates());
        
        foreach ($templates as $t) {
          $name = $all[$t->id];
          $args["templates"][$name] = $t->id;
        }
      
      }
      
      
    }
    
    if (isset($parent) && is_multisite()) {
      
      $site_options = array();
      
      foreach ($parent->sites() as $site) {
        $site_options[$site->full_path()] = $site->id();
      }
      
      $args["sites"] = $site_options;
      
    };
        
    MPV::fs_visibility( $model, $args ); 
    
    ?>
    
    <?php if ($info->is_template_set) : ?>
    <input id="visibility-templates" name="visibility[templates]" type="hidden" value="<?php echo $file ?>" />   
    <?php endif; ?>

    <?php if ($info->is_role_set) : ?>
    <input id="visibility-roles" name="visibility[roles]" type="hidden" value="<?php echo $role_name ?>" />   
    <?php endif; ?>

    <?php if ($info->is_taxonomy_set) : ?>
    <input id="visibility-taxonomies" name="visibility[taxonomies]" type="hidden" value="<?php echo $tax->name ?>" />   
    <?php endif; ?>

    <?php if ($model->is_post_type() && isset($parent)) : ?>
    <input id="visibility-post-types" name="visibility[post_types]" type="hidden" value="<?php echo $parent->name ?>" />   
    <?php endif; ?>
    
    
    
    
    <div id="f-position" class="f clearfix">
      <label for="position" class="icon"><i class="sort"></i><?php _e("Set Position", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">

          <div class="sortable-list sortable-list-fields">
            <span class="arrow"></span>
            
            <?php
            
            // build a field list
            
            $field_sets = array();
            
            if ($model->shared) {
              $field_sets = MPM_SharedFieldSet::find( array( "orderby" => "position ASC" ) );
            } else if ($model->template) {
              $field_sets = MPM_TemplateFieldSet::find_by_template( MasterPress::$parent );
            } else if ($model->role) {
              $field_sets = MPM_RoleFieldSet::find_by_role( MasterPress::$parent );
            } else if ($model->taxonomy) {
              $field_sets = MPM_TaxonomyFieldSet::find_by_taxonomy( $tax );
            } else if ($model->site) {
              $field_sets = MPM_SiteFieldSet::find( array( "orderby" => "position ASC" ) );
            } else {
              $sql = "SELECT * FROM ".MPU::table("field-sets")." WHERE ( type = 'p' ) AND ".MPM::visibility_rlike("post_types", $parent->name)." ORDER BY position";
              $field_sets = MPM::get_models("field-set", $sql);
            }
            
            $sort_field_sets = array();
            
            // now build a representable list of field_sets
            
            $icon = $model->shared ? MPU::img_url("icon-shared-field-set.png") : MPU::img_url("icon-field-set-small.png");
            
            $icon_class = $model->shared ? "metabox-share" : "metabox";
            
            foreach ($field_sets as $field_set) {
              
              $position = $field_set->position;
            
              if (isset($_POST["other_position"]) && $_POST["other_position"][$field_set->id]) {
                $position = $_POST["other_position"][$field_set->id];
              }

              $sort_field_sets[] = array( "position" => $position, "disabled" => $field_set->disabled, "current" => $field_set->id == $model->id, "label" => $field_set->display_label(), "icon_class" => $icon_class, "icon" => $icon, "id" => $field_set->id );
            }

            
            if (MPC::is_create()) {
              $label = $model->display_label();
              
              if (!$label || $label == "") {
                $label = "?";
              }
              
              $sort_field_sets[] = array( "position" => $model->position, "disabled" => $model->disabled, "current" => true, "label" => $model->display_label(), "icon" => $icon, "icon_class" => $icon_class );
            }
            
            $count = 0;
            
            ?>
            
            <ul>
              <?php foreach ($sort_field_sets as $f) : $count++; $first = $count == 1 ? "first " : ""; $current = $f["current"] ? 'current ' : '';  ?>

              <?php
                $disabled = $f["disabled"] ? 'disabled' : '';
                $disabled_title = $f["disabled"] ? __("This field set is disabled", MASTERPRESS_DOMAIN) : '';
              ?>

              <li class="<?php echo $first.$current ?> <?php echo $disabled ?>" title="<?php echo $disabled_title ?>">
                <i class="<?php echo $icon_class ?>"></i>
                <?php if ($f["current"]) : ?>
                <span class="inline fill { src: '#label_name,#label_singular_name'}"><?php echo $f["label"] ?></span>
                <input id="position" name="position" value="<?php echo $f["position"] ?>" type="hidden" class="text" />
                <?php else: ?>
                <span class="inline"><?php echo $f["label"] ?></span>
                <input id="other_position" name="other_position[<?php echo $f["id"] ?>]" value="<?php echo $f["position"] ?>" type="hidden" />
                <?php endif; ?>
              </li>
              <?php endforeach; ?>
            </ul>
            
          </div>
          <!-- /.sortable-list -->
          
        <p>
          <?php _e("Drag set to the desired <em>default</em> position in the WordPress <em>Create / Edit Post</em> screen.<br />Note: Field sets are displayed in a standard WordPress UI panel (metabox), which can be dragged around by the user to also influence the order.", MASTERPRESS_DOMAIN) ?>
        </p>
      </div>
    </div>
    <!-- /.f -->
    
    
    
    </div>
    <!-- /.mpv-field-sets-form -->

    <?php
    
  }
  

}

?>