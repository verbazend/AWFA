<?php 

class MPV_Taxonomies extends MPV {

  public static function __s() {
    return __("Taxonomy", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("Taxonomies", MASTERPRESS_DOMAIN);
  }

  public function confirm_delete($tax) {

    ?>
    
    <div class="panel delete-panel delete-taxonomy-panel">
      <?php $this->form_open() ?> 
      <div class="panel-content">
        <header class="title">
          <h1><?php printf( __("Are you sure you want to delete the <em>%s</em> Taxonomy? This operation is not reversible!</span>", MASTERPRESS_DOMAIN), $tax->labels["name"] ); ?></h1>

          <div class="actions">
            <button class="button-primary button-delete" type="submit"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></button>
            <?php echo MPV::action_link("taxonomies", "manage", __("Cancel", MASTERPRESS_DOMAIN), "id=".$tax->id, array( "class" => "button button-small primary" )); ?>
          </div>  

        </header>

        <?php
          $term_count = $tax->term_count();
          $meta_count = $tax->meta_count();
          $field_set_count = $tax->field_set_count();
          
          $not_this_tax = array();
          
          foreach (MPM_Taxonomy::find(array("orderby" => "_builtin,name")) as $t) {
            if ($t->name != $tax->name) {
              $not_this_tax[] = $t;
            }
          }
            
          $dep_count = $term_count + $meta_count + $field_set_count;
          
        ?>
        
        <?php if ( ( $dep_count > 0 ) || $tax->_external) : ?>

        <div class="panel-divider"><span><span>&nbsp;</span></span></div>
        
        <div class="content">
        
        
          <?php if ($tax->_external) : ?>
          <p class="soft-warning"><i class="warning-triangle"></i><?php _e("This taxonomy was not created by MasterPress and will not be removed upon deletion if it is still externally defined in a plug-in or theme.", MASTERPRESS_DOMAIN) ?></p>
          <?php endif; ?>

          <?php if ($dep_count > 0) : ?>
            
          <p>
          <?php echo __("This taxonomy has:", MASTERPRESS_DOMAIN) ?>
          </p>
          
          <ul class="items">
          <?php if ($term_count > 0) : ?><li><?php echo MPU::__items($term_count, __("%d tern record", MASTERPRESS_DOMAIN), __("%d term records", MASTERPRESS_DOMAIN)) ?></li><?php endif; ?>
          <?php if ($meta_count > 0) : ?><li><?php echo MPU::__items($meta_count, __("%d custom field (meta) data records", MASTERPRESS_DOMAIN), __("%d custom field (meta) data records", MASTERPRESS_DOMAIN)) ?></li><?php endif; ?>
          <?php if ($field_set_count > 0) : ?><li><?php echo MPU::__items($field_set_count, __("%d field set definition", MASTERPRESS_DOMAIN), __("%d field set definitions", MASTERPRESS_DOMAIN)) ?></li><?php endif; ?>
          </ul>
        
          <p>
          <?php _e("Please indicate how you would like to handle this related information upon deletion:", MASTERPRESS_DOMAIN); ?>
          <?php if ($meta_count > 0 && is_multisite()) : ?>
          <?php _e("<br /><b>Note:</b> If you choose to <em>Delete</em> Meta Data, it will only be removed from the <b>current site</b> in your multisite network.", MASTERPRESS_DOMAIN); ?>
          <?php endif; ?>
          </p>
          
          <?php if ($term_count > 0) : ?>
            
          <div id="f-existing_terms" class="f">
            <span class="label label-icon"><i class="tags"></i><?php _e("Existing terms:", MASTERPRESS_DOMAIN) ?></span>
            <div class="fw">
              <input id="existing_terms_delete" name="existing_terms" type="radio" value="delete" checked="checked" class="radio" /> 
              <label for="existing_terms_delete" class="radio"><?php _e("Delete", MASTERPRESS_DOMAIN) ?></label>
              <input id="existing_terms_reassign" name="existing_terms" type="radio" value="reassign" class="radio" /> 
              <label for="existing_terms_reassign" class="radio with-control"><?php _e("Re-assign to Taxonomy:", MASTERPRESS_DOMAIN) ?></label>
              <select id="existing_terms_reassign_taxonomy" name="existing_terms_reassign_taxonomy" class="check { el: '#existing_terms_reassign' }">
              <?php foreach ($not_this_tax as $t) : ?>
              <?php if ($t->name != $tax->name): ?>
              <option value="<?php echo $t->name ?>"><?php echo $t->display_label() ?></option>  
              <?php endif; ?>
              <?php endforeach; ?>
              </select>
              <input id="existing_terms_keep" name="existing_terms" type="radio" value="keep" class="radio" /> 
              <label for="existing_terms_keep" class="radio"><?php _e("Keep", MASTERPRESS_DOMAIN) ?></label>
              
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
              <label id="label_field_sets_keep" for="field_sets_keep" title="<?php _e("Re-attach by creating a taxonomy with the same name", MASTERPRESS_DOMAIN) ?>" class="disabled radio with-tooltip"><?php _e("Keep", MASTERPRESS_DOMAIN); ?></label>
              
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
        


      </div>
      <!-- /.panel-content -->

      </form>

    </div>
    

    <?php
    
  }
  
  public function grid($id = null) {
    
    // include controllers to obtain correct URLs for actions
    MPV::incl("field-sets");
    MPV::incl("taxonomy-field-sets");
    MPV::incl("post-types");
    MPC::incl("post-types");
    
    $taxonomies = MPM_Taxonomy::find("orderby=disabled,name ASC");

    $has_actions = MasterPress::current_user_can("edit_taxonomies,delete_taxonomies,manage_taxonomy_field_sets");
    $can_edit = MasterPress::current_user_can("edit_taxonomies");
    $can_delete = MasterPress::current_user_can("delete_taxonomies");
    $can_create = MasterPress::current_user_can("create_taxonomies");
    $can_manage_field_sets = MasterPress::current_user_can("manage_taxonomy_field_sets");

    $less = ($can_create) ? 1 : 0;
    $colspan = ( $has_actions ? 8 : 7 ) - $less;


    foreach ($taxonomies as $tax) {
      if (MPC::is_deleting($tax->id)) {
        self::confirm_delete($tax);
      }
    }

    
  ?>


      
  <?php MPV::messages(); ?>
  
  <table cellspacing="0" class="grid grid-taxonomies">
    <thead>
    <tr>
      <th class="first title-icon"><i class="image-small"></i><span class="ac" title="<?php _e("Title Icon", MASTERPRESS_DOMAIN) ?>"><?php _e("Title Icon", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="menu-label"><i class="label-string"></i><span><?php _e("Menu Label", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="taxonomy-name"><i class="tag"></i><span><?php _e("Name", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="hierarchical"><i class="hierarchy"></i><span class="ac" title="<?php _e("Hierarchical?", MASTERPRESS_DOMAIN) ?>"><?php _e("Hierarchical?", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="field-sets"><i class="metaboxes"></i><span><?php _e("Field Sets", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="shared-field-sets"><i class="share-metaboxes"></i><span><?php _e("Shared Field Sets", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="post-types <?php echo $has_actions ? "" : "last" ?>"><i class="pins"></i><span><?php _e("Post Types", MASTERPRESS_DOMAIN) ?></span></th>
      <?php if ($has_actions) : ?>
      <th class="actions last"><i class="buttons"></i><span><?php _e("Actions", MASTERPRESS_DOMAIN) ?></span></th>
      <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    
    <?php $count = 0; ?>
    
    <?php foreach ($taxonomies as $tax) : $disabled = $tax->disabled ? "disabled" : ""; $title = $tax->disabled ? ' title="'.__("this taxonomy is disabled", MASTERPRESS_DOMAIN).'" ' : ""; ?>

    <?php

    if (!$tax->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("this taxonomy is not currently available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
    }
    
    ?>
      
    <?php 
    
    $deleting_class = MPC::is_deleting($tax->id, "delete") ? 'deleting' : ''; 
    $editable_class = $can_edit ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("taxonomies", "edit", "id=".$tax->id)."' }" : "";

    
    ?>
    
    
    <?php if ($tax->still_registered()) : $count++; $first = $count == 1 ? 'first' : ''; ?>

    <tr <?php echo $title ?> class="<?php echo $first ?>  <?php echo $editable_class.$deleting_class ?> <?php echo $disabled ?> <?php echo MPV::updated_class("edit,create", $tax->id) ?> <?php echo $count % 2 == 0 ? "even" : "" ?> sub <?php echo $meta ?>">
      <td class="first menu-icon icon"><span class="mp-icon-taxonomy mp-icon-taxonomy-<?php echo $tax->name ?>"></span></td>
      <td class="menu-label"><strong><?php echo $tax->labels["menu_name"] ?></strong></td>
      <td class="taxonomy-name"><span class="tt"><?php echo $tax->name ?></span></td>
      <td class="hierarchical"><?php echo $tax->hierarchical ? '<i class="tick-small"></i><span class="ac">yes</span>' : '<span class="note">&ndash;</span>' ?></td>

      <?php $field_sets = $tax->field_sets(); ?>
      
      <?php if ($tax->show_ui) : ?>

      <td class="field-sets <?php echo $can_manage_field_sets ? "manage { href: '".MasterPress::admin_url( "taxonomies", "manage-field-sets", "parent=".$tax->id)."' }" : "" ?>">
        
        <?php if ($can_manage_field_sets) : ?>
        <a href="<?php echo MasterPress::admin_url( "taxonomies", "manage-field-sets", "parent=".$tax->id)?>" title="<?php echo strip_tags(self::__manage( MPV_TaxonomyFieldSets::__p() )) ?>">
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
      <span title="<?php _e("This taxonomy does not support field sets as the 'show ui' parameter is off", MASTERPRESS_DOMAIN); ?>"><i class="na"></i><?php _e("N/A", MASTERPRESS_DOMAIN); ?></span>  
      </td>
      <?php endif; ?>

      <?php if ($tax->show_ui) : ?>

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
      <span title="<?php _e("This taxonomy does not support shared field sets as the 'show ui' parameter is off", MASTERPRESS_DOMAIN); ?>"><i class="na"></i><?php _e("N/A", MASTERPRESS_DOMAIN); ?></span>  
      </td>
      <?php endif; ?>

      <td class="post-types <?php echo $has_actions ? "" : "last" ?>">

        <?php 
        $post_types = $tax->post_types(); 
        
        $post_type_display = MPV::note_none();
        
        if (count($post_types)) {
          $post_type_links = array();
          
          foreach ($post_types as $post_type) {
            $post_type_links[] = $post_type->labels["name"]; 
          }

          $post_type_display = implode($post_type_links, ", ");
        }
        
        echo $post_type_display;
        ?>
        
      </td>
      
      <?php $field_sets = $tax->field_sets(); ?>

      
      <?php if ($has_actions) : ?>

      <td class="actions last">
      <div>
        <?php if (MPC::is_deleting($tax->id)) : ?>
          <span class="confirm-action">&nbsp;</span>
        <?php else: ?>

          <?php if ($can_manage_field_sets) : ?>

            <?php if ($tax->show_ui) :  ?>
            <?php echo MPV::action_button("taxonomies", "manage-field-sets", self::__manage( MPV_FieldSets::__p_short() ), "parent=".$tax->id, array("class" => "button button-manage")); ?> 
            <?php else: ?>
            <?php echo MPV::action_button("taxonomies", "manage-field-sets", self::__manage( MPV_FieldSets::__p_short() ), "parent=".$tax->id, array("title" => "This taxonomy does not support field sets as the 'show ui' parameter is off", "disabled" => "disabled", "class" => "button button-manage")); ?> 
            <?php endif; ?>

          <?php endif; ?>

          <?php if ($can_edit) : ?>
            <?php echo MPV::action_button("taxonomies", "edit", self::__edit( "" ), "id=".$tax->id, array( "class" => "button primary button-edit" )); ?>
          <?php endif; ?>

          <?php if ($can_delete) : ?>
            <?php if ($tax->_builtin) : ?>
            <span class="note" title="<?php _e("This taxonomy cannot be deleted as it is built-in to WordPress", MASTERPRESS_DOMAIN) ?>"><?php _e("( Built-in )", MASTERPRESS_DOMAIN) ?></span>
            <?php elseif ($tax->_external) : ?>
            <span class="note" title="<?php _e("This taxonomy cannot be deleted as it was not created by MasterPress", MASTERPRESS_DOMAIN) ?>"><?php _e("( External )", MASTERPRESS_DOMAIN) ?></span>
            <?php else : ?>
            <?php echo MPV::action_button("taxonomies", "delete", self::__delete( "" ), "id=".$tax->id, array( "class" => "button button-delete" )); ?>
            <?php endif; ?>
          <?php endif; ?>
        
        <?php endif; ?>
      </div>
      </td>

      <?php endif; // has_actions ?>

    </tr>

    <?php endif; ?>
    
    <?php endforeach; ?>

    <tr class="summary <?php echo $can_create ? "editable" : "" ?>">
      <td colspan="<?php echo $colspan ?>" class="first <?php echo $can_create ? "" : "last" ?>"><?php _e(  MPU::__items( $count, __("%d Taxonomies", MASTERPRESS_DOMAIN), __("%d Taxonomies", MASTERPRESS_DOMAIN)   ) ) ?></td>
      <?php if ($can_create) : ?>
      <td class="last actions">
      <?php echo MPV::action_button("taxonomies", "create", self::__create(MPV_Taxonomies::__s()), "", array( "class" => "button button-create" )); ?>
      </td>
      <?php endif; ?>
    </tr>
    
    </tbody>
    </table>
    
    <?php
    
  } // end grid()
  
  

  public function form($type) {
    $model = MasterPress::$model;
    
    global $meow_provider;
    
  ?>

    <?php MPV::messages(); ?>
  
    <input type="hidden" name="_builtin" value="<?php echo $model->_builtin ? "true" : "false" ?>" />
    <input type="hidden" name="_external" value="<?php echo $model->_external ? "true" : "false" ?>" />
    
    <div class="f">
      <label for="name" class="icon"><i class="script-php"></i><?php _e("<strong>Singular</strong> Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="name_original" name="name_original" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />
        <input id="name_last" name="name_last" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />
        <input id="name" name="name" <?php MPV::read_only_attr($model->_builtin || $model->_external || MPC::is_edit()) ?> type="text" class="text mono key <?php echo MPV::read_only_class($model->_builtin || $model->_external) ?>" maxlength="20" value="<?php echo $model->name ?>" /><?php if (!$model->_builtin && !$model->_external) { ?><em class="required">(required)</em><?php } ?>
        <p>
          <?php _e("This is a unique identifier for the taxonomy in the WordPress API. It is not displayed, and by convention it <strong>must</strong> be a singular form, lowercase string with underscores to separate words.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
      
      <p id="name_warning" class="warning">
		<i class="error-circle"></i>
        <?php _e("Note: check that you have definitely entered a <strong>singular word</strong> here, as the singular form of <em>Plural Name</em> is currently different to this value.", MASTERPRESS_DOMAIN) ?>
      </p>

    </div>
    <!-- /.f -->
    
    <div class="f">
      <label for="plural_name" class="icon"><i class="script-php"></i><?php _e("<strong>Plural</strong> Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="plural_name" name="plural_name" <?php MPV::read_only_attr($model->_builtin || $model->_external || MPC::is_edit()) ?> type="text" value="<?php echo $model->plural_name ?>" class="text mono key <?php echo MPV::read_only_class($model->_builtin || $model->_external) ?>" /><?php if (!$model->_builtin && !$model->_external) { ?><em class="required">(required)</em><?php } ?>
        <p>
          <?php _e("The plural form of <em>Singular Name</em>, following the same naming conventions", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    
    <div class="f clearfix">
      <label id="label-title_icon" for="title_icon" class="icon"><i class="image-small"></i><?php _e("Icon", MASTERPRESS_DOMAIN) ?>:</label>
      <div class="fw">
        <div id="icon-file-uploader" class="icon-uploader file-uploader { ids: { drop: 'menu_icon_drop_area' }, input: '#title_icon', inputName: 'title_icon_ul', base_url: '<?php echo MASTERPRESS_GLOBAL_CONTENT_URL ?>', params: { dir: 'menu-icons/' }, limit: 1, lang: { buttonChoose: '<?php _e("Choose From Computer&hellip;", MASTERPRESS_DOMAIN) ?>', buttonReplace: '<?php _e("Replace file&hellip;", MASTERPRESS_DOMAIN) ?>' } }">

          <div id="menu_icon_drop_area" class="drop-area"><?php _e("Drop file here to upload", MASTERPRESS_DOMAIN) ?></div>

          <?php 
              
          $file_name = $model->title_icon;
          $file_class = "";
          $clear_class = "";
          
          if ($file_name == "") {
            $file_name = __("( None )", MASTERPRESS_DOMAIN);
            $file_class = "name-none";
            $clear_class = "hidden";
          }
          
          ?>
              
          <div class="file">
            <span class="preview" style="background-image: url('<?php echo MPU::menu_icon_url($model->title_icon, true, "taxonomy") ?>');"></span><span data-none="<?php echo __("( None )", MASTERPRESS_DOMAIN) ?>" class="name <?php echo $file_class ?>"><?php echo $file_name ?></span>
            <button type="button" class="<?php echo $clear_class ?> clear ir" title="<?php _e("Clear", MASTERPRESS_DOMAIN) ?>">Clear</button>
          </div>
                            
          <input id="title_icon" name="title_icon" value="<?php echo $model->title_icon ?>" type="hidden" />
          <div class="uploader-ui"></div>

        </div>
        <!-- /.file-uploader -->
		
		    
		    <?php MPV::icon_select($model->title_icon, "title-icon-select", "title_icon_select", "icon-file-uploader"); ?>
	      
		
      </div>
    </div>
    <!-- /.f -->
    
    <?php if (!$model->_external) : ?>

    <div class="f">
      <label for="disabled" class="icon"><i class="slash"></i><?php _e("Disabled", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="disabled" name="disabled" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->disabled ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#disabled' }">
          <?php _e("disabling a taxonomy will keep its definition in the database but it will not be registered in WordPress, which will often be <strong>preferable to deleting it</strong> entirely.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    <?php endif; ?>
    

    <?php if (!$model->_builtin && !$model->_external) : ?>

    <div class="f">
      <label for="hierarchical" class="icon"><i class="hierarchy"></i><?php _e("Hierarchical", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="hierarchical" name="hierarchical" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->hierarchical ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#hierarchical' }">
          <?php _e("hierarchical taxonomies are like WordPress <em>Categories</em>, whereas non-hierarchical taxonomies are like WordPress <em>Tags</em>.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

   
    <div class="f">
      <label for="show_ui" class="icon"><i class="metabox-menu"></i><?php _e("Show UI", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="show_ui" name="show_ui" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->show_ui ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#show_ui' }">
          <?php _e("unchecking this will internalize this taxonomy, hiding it from both the admin menus and the edit post interface", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="hide_term_ui" class="icon"><i class="metabox-tags-small"></i><?php _e("Hide Standard Terms UI", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="hide_term_ui" name="hide_term_ui" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->hide_term_ui ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#hide_term_ui' }">
          <?php _e("hides the standard interface for assigning terms from this taxonomy to posts.<br>This may be useful if you are solely using this taxonomy to provide the values<br>for a <em>Related Terms</em> field.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="show_in_nav_menus" class="icon"><i class="menu-gray"></i><?php _e("Show in Nav Menus", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="show_in_nav_menus" name="show_in_nav_menus" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->show_in_nav_menus ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#show_in_nav_menus' }">
          <?php _e("allows this taxonomy to be selected in navigation menus", MASTERPRESS_DOMAIN); ?> 
        </p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="show_tagcloud" class="icon"><i class="tag-cloud size-20"></i><?php _e("Show Tag Cloud", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="show_tagcloud" name="show_tagcloud" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->show_tagcloud ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#show_tagcloud' }">
          <?php _e("check to allow the Tag Cloud widget to use this taxonomy", MASTERPRESS_DOMAIN); ?> 
        </p>
      </div>
    </div>
    <!-- /.f -->

    
    <div class="f">
      <label for="show_manage_filter" class="icon"><i class="funnel"></i><?php _e("Show Manage Filter", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="show_manage_filter" name="show_manage_filter" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->show_manage_filter ) ?> class="checkbox" />

        <p class="checkbox-alt-label { for_el: '#show_manage_filter' }">
          <?php _e("show a drop-down filter list of terms above post listings attached to this taxonomy", MASTERPRESS_DOMAIN); ?> 
        </p>
      </div>
    </div>
    <!-- /.f -->

    <?php endif; // !$model->_builtin ?>

    
	

  
    <?php if (!$model->_external) : ?>

    <div class="fs fs-post-types">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="pins"></i><strong><?php _e("Post Types", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("associate post types with this taxonomy", MASTERPRESS_DOMAIN) ?></h3>
        <div class="buttons">
          <button class="button button-small button-select-all" type="button"><?php _e('Select <strong class="all">All</strong>', MASTERPRESS_DOMAIN) ?></button>
          <button class="button button-small button-select-none" type="button"><?php _e('Select <strong class="none">None</strong>', MASTERPRESS_DOMAIN) ?></button>
        </div>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
        
        <?php $post_types = MPM_PostType::find(array("orderby" => "name ASC")); ?>
        
        <?php foreach ($post_types as $post_type) : $disabled = $post_type->disabled ? ' disabled' : ''; $disabled_title = $post_type->disabled ? __("This post type is disabled", MASTERPRESS_DOMAIN) : ''; $builtin = $post_type->_builtin ? '&nbsp;'.__('(Built-in)', MASTERPRESS_DOMAIN) : ''; ?>
        <div class="fw">
          <input id="post_types_<?php echo $post_type->name ?>" name="post_types[]" value="<?php echo $post_type->name ?>" type="checkbox" <?php echo WOOF_HTML::checked_attr( $model->linked_to_post_type($post_type) || MPV::in_post_array("post_types", $post_type->name) ) ?> class="checkbox" />
          <label for="post_types_<?php echo $post_type->name ?>" class="checkbox <?php echo $disabled ?>" title="<?php echo $disabled_title ?>"><?php echo $post_type->labels["name"] ?><span><?php echo $builtin ?></span></label>
        </div>
        <!-- /.fw -->
        
        <?php endforeach; ?>
        
        
      </div>
      </div>

    </div>
    <!-- /.fs -->

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
    

    <div class="fs fs-column-builder">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="grid"></i><strong><?php _e("Columns") ?></strong> - <?php _e("specify the columns shown in the manage listing for terms in this taxonomy") ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">

        
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
            $title = "";
            
						$title_readonly = "";

            if (isset($column["title"])) {
              $title = $column["title"];
            }
          
            $content = "";
            
            if (isset($column["title_readonly"])) {
              $title_readonly = ' readonly="true" title="'.__("This title cannot be changed, as it dynamically displays the active post type").'" ';
            }
            
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
                
                <input name="columns[<?php echo $count ?>][title]" <?php echo $title_readonly ?> value="<?php echo $title ?>" type="text" class="text" />
                
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
                "Name" => "name",
                "Description" => "description",
                "Slug" => "slug",
                "[Post Type]" => "posts",
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
          
          $fs = $meow_provider->taxonomy_field_sets($model->name);

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
            <button id="add-custom-column" type="button" class="text add"><i></i><?php _e('<em class="create">Add</em> Custom Column', MASTERPRESS_DOMAIN) ?></button>
          </div>
        </div>
        <!-- /.custom-columns -->
        
        </div>
        <!-- /.columns-clip -->

      </div>
      </div>

    </div>
    <!-- /.fs -->
    
    
    <?php
    
    if (is_multisite() && MASTERPRESS_MULTISITE_SHARING) {
    
      $args["supports"] = array("multisite");
      
      $args["labels"] = array(
        "title" =>  __("control the visibility of this Taxonomy within WordPress", MASTERPRESS_DOMAIN),   
        "title_multisite" =>  __("specify the sites in the multisite network that this Taxonomy is available in", MASTERPRESS_DOMAIN),   
        "multisite_all" => __( "All Sites" )
      );

      MPV::fs_visibility( $model, $args ); 
    
    }
    
    ?>
    
    
    <?php if (!$model->_builtin && !$model->_external) : ?>

    <div class="fs fs-url-options">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="globe"></i><strong><?php _e("URL Options", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("controls how your taxonomy is accessible via URLs in your site", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
  
    
      <div class="fsc">
      <div class="fscb">
      
      <div class="f">
        <label for="rewrite_slug" class="text"><?php _e("Rewrite Slug", MASTERPRESS_DOMAIN) ?>:</label>
        <div class="fw">
          <input id="rewrite_slug" name="rewrite[slug]" type="text" value="<?php echo $model->rewrite["slug"] ?>" class="text mono" />
          <p>
            <?php _e("The slug prepended to posts attached to this taxonomy in the URL structure.", MASTERPRESS_DOMAIN); ?><br />
            <?php _e("The default value follows the WordPress of using the lowercase sanitized version of <em>Singular Name</em>.", MASTERPRESS_DOMAIN); ?>
          </p>
        </div>
      </div>
      <!-- /.f -->
    
      <div class="fw">
        <input id="rewrite_with_front" name="rewrite[with_front]" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->rewrite["with_front"] ) ?> class="checkbox" />
        <label for="rewrite_with_front" class="checkbox"><?php _e("With Front? - <span>Append the above slug to the top level URL set in your permalink settings.</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->

      <div class="fw hierarchical-only">
        <input id="rewrite_hierarchical" name="rewrite[hierarchical]" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->rewrite["hierarchical"] ) ?> class="checkbox" />
        <label for="rewrite_hierarchical" class="checkbox"><?php _e("Hierarchical? - <span>Allow hierarchical urls, mirroring the hierarchy of the taxonomy.</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->
              
      </div>
      </div>

    </div>
    <!-- /.fs -->

    <div class="fs fs-query-data-options">
      
      <div class="fst">
      <div class="fstb">
        <h3><i class="database"></i><strong><?php _e("Query &amp; Data Options", MASTERPRESS_DOMAIN) ?></strong> - <?php _e(" controls API access to this taxonomy in database queries and site searches", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>

      <div class="fsc">
      <div class="fscb">

      <div class="fw">
        
        <input id="query_allowed" name="query_allowed" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->meta("query_allowed") ) ?> class="checkbox" />
        <label for="query_allowed" class="checkbox"><?php _e("Allow Queries? - <span>Allow this taxonomy to be queried in the database.</span>", MASTERPRESS_DOMAIN); ?></label>
      </div>  
      <!-- /.fw -->
      
      <div class="f">
        <label for="query_var" class="text"><?php _e("Query Variable", MASTERPRESS_DOMAIN) ?>:</label>
        <div class="fw">
          <input id="query_var" name="query_var" type="text" value="<?php echo $model->query_var ?>" class="text mono" />
          <p>
            <?php _e('Enter the query variable used to query this taxonomy with <span class="tt">query_posts</span> or <span class="tt">WP_Query</span>. Generally this should simply be the default value of the <em>Singular Name</em>, unless you have a good reason to change it.', MASTERPRESS_DOMAIN) ?>
          </p>
        </div>
      </div>
      <!-- /.f -->

      <div class="f">
        <label for="update_count_callback" class="text"><?php _e("Update Count Callback", MASTERPRESS_DOMAIN) ?>:</label>
        <div class="fw">
          <input id="update_count_callback" name="update_count_callback" type="text" value="<?php echo $model->update_count_callback ?>" class="text mono" />
          <p>
            <?php _e("The name of a function that will be called to update the count of an associated post type.", MASTERPRESS_DOMAIN) ?>
          </p>
        </div>
      </div>
      <!-- /.f -->
      
      
      </div>
      </div>

    </div>
    <!-- /.fs -->
        
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
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_add_new"><?php _e("Search Items:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_search_items" name="labels[search_items]" value="<?php echo $model->labels["search_items"] ?>"  type="text" class="text { tmpl: '<?php _e("Search {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_popular_items"><?php _e("Popular Items:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_popular_items" name="labels[popular_items]" value="<?php echo $model->labels["popular_items"] ?>" type="text" class="text { tmpl: '<?php _e("Popular {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }"  />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_all_items"><?php _e("All Items:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_all_items" name="labels[all_items]" value="<?php echo $model->labels["all_items"] ?>" type="text" class="text { tmpl: '<?php _e("All {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f hierarchical-only">
          <label for="label_parent_item"><?php _e("Parent Item:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_parent_item" name="labels[parent_item]" value="<?php echo $model->labels["parent_item"] ?>" type="text" class="text { tmpl: '<?php _e("Parent {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f hierarchical-only">
          <label for="label_parent_item_colon"><?php _e("Parent Item Colon:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_parent_item_colon" name="labels[parent_item_colon]" value="<?php echo $model->labels["parent_item_colon"] ?>" type="text" class="text { tmpl: '<?php _e("Parent {{singular_name}}:", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_edit_item"><?php _e("Edit Item:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_edit_item" name="labels[edit_item]" value="<?php echo $model->labels["edit_item"] ?>" type="text" class="text { tmpl: '<?php _e("Edit {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_update_item"><?php _e("Update Item:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_update_item" name="labels[update_item]" value="<?php echo $model->labels["update_item"] ?>" type="text" class="text { tmpl: '<?php _e("Update {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_add_new_item"><?php _e("Add New Item:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_add_new_item" name="labels[add_new_item]" value="<?php echo $model->labels["add_new_item"] ?>" type="text" class="text { tmpl: '<?php _e("Add New {{singular_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_new_item_name"><?php _e("New Item Name:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_new_item_name" name="labels[new_item_name]" value="<?php echo $model->labels["new_item_name"] ?>" type="text" class="text { tmpl: '<?php _e("New {{singular_name}} Name", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_separate_items_with_commas"><?php _e("Separate Items With Commas:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_separate_items_with_commas" name="labels[separate_items_with_commas]" value="<?php echo $model->labels["separate_items_with_commas"] ?>" type="text" class="text { tmpl: '<?php _e("Separate {{plural_name}} with commas", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_add_or_remove_items"><?php _e("Add or remove items:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_add_or_remove_items" name="labels[add_or_remove_items]" value="<?php echo $model->labels["add_or_remove_items"] ?>" type="text" class="text { tmpl: '<?php _e("Add or remove {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_choose_from_most_used"><?php _e("Choose from most used:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="label_choose_from_most_used" name="labels[choose_from_most_used]" value="<?php echo $model->labels["choose_from_most_used"] ?>" type="text" class="text { tmpl: '<?php _e("Choose from most used {{plural_name}}", MASTERPRESS_DOMAIN) ?>' }" />
          </div>
        </div>
        <!-- /.f -->
      
      </div>
      </div>

    </div>
    <!-- /.fs -->

    <div class="fs fs-capability-keys">

    
    <div class="fst">
      <div class="fstb">
        <h3><i class="key"></i><strong><?php _e("Capabilities", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("the keys used to control access to this taxonomy", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
      
        <div class="f">
          <label for="capability_manage_terms"><?php _e("Manage Terms:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="capability_manage_terms" name="capabilities[manage_terms]" value="<?php echo $model->capabilities["manage_terms"] ?>" type="text" class="text mono" />
            <p class="note">
              <?php _e('<span class="tt">manage_categories</span> is the typical value', MASTERPRESS_DOMAIN) ?> 
            </p>
            
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="capability_edit_terms"><?php _e("Edit Terms:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="capability_edit_terms" name="capabilities[edit_terms]" value="<?php echo $model->capabilities["edit_terms"] ?>"  type="text" class="text mono" />
            <p class="note">
              <?php _e('<span class="tt">manage_categories</span> is the typical value', MASTERPRESS_DOMAIN) ?> 
            </p>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="capability_delete_terms"><?php _e("Delete Terms:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="capability_delete_terms" name="capabilities[delete_terms]" value="<?php echo $model->capabilities["delete_terms"] ?>"  type="text" class="text mono" />
            <p class="note">
              <?php _e('<span class="tt">manage_categories</span> is the typical value', MASTERPRESS_DOMAIN) ?> 
            </p>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="capability_assign_terms"><?php _e("Assign Terms:", MASTERPRESS_DOMAIN); ?></label>
          <div class="fw">
            <input id="capability_assign_terms" name="capabilities[assign_terms]" value="<?php echo $model->capabilities["assign_terms"] ?>"  type="text" class="text mono" />
            <p class="note">
              <?php _e('<span class="tt">edit_posts</span> is the typical value', MASTERPRESS_DOMAIN) ?> 
            </p>
          </div>
        </div>
        <!-- /.f -->

      
      </div>
      </div>
      
      

    </div>
    <!-- /.fs -->

    
    <?php endif; // !$model->_builtin ?>

    

    <?php
  } // end form
  


}

?>
