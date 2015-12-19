<?php

class MPV_SharedFieldSets extends MPV {

  public static function __s() {
    return __("Shared Field Set", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("Shared Field Sets", MASTERPRESS_DOMAIN);
  }

  public static function __s_short() {
    return __("Set", MASTERPRESS_DOMAIN);
  }

  public static function __p_short() {
    return __("Sets", MASTERPRESS_DOMAIN);
  }
  
  public function confirm_delete_field($field) {
    MPV::incl("field-sets");
    $info = MasterPress::$view;
    $parent = $info->parent;
    MPV_FieldSets::confirm_delete_field_panel($field, __("Shared Field", MASTERPRESS_DOMAIN), MPV::action_link("shared-field-sets", "manage", __("Cancel", MASTERPRESS_DOMAIN), "", array( "class" => "button button-small primary" )) );
  }

  public function confirm_delete($field_set) {
    MPV::incl("field-sets");
    $info = MasterPress::$view;
    $parent = $info->parent;
    MPV_FieldSets::confirm_delete_panel($field_set, __("Shared Field Set", MASTERPRESS_DOMAIN), MPV::action_link("shared-field-sets", "manage", __("Cancel", MASTERPRESS_DOMAIN), "", array( "class" => "button button-small primary" )) );
  }
  
  
  public function grid() {

    MPC::incl("post-types");
    MPV::incl("fields");
    MPV::incl("post-types");
    
    $info = MasterPress::$view;
    
    ?>
    
    
    <?php MPV::messages(); ?>


    <div class="grid-set">
    
    <?php $field_sets = MPM_SharedFieldSet::find( array("orderby" => "name ASC" ) ); ?>

    <?php MPV::field_set_icon_styles($field_sets); ?>

    <?php
    
    $has_actions = MasterPress::current_user_can("edit_shared_field_sets,delete_shared_field_sets,edit_shared_fields,delete_shared_fields");

    $can_edit = MasterPress::current_user_can("edit_shared_field_sets");
    $can_delete = MasterPress::current_user_can("delete_shared_field_sets");
    $can_create = MasterPress::current_user_can("create_shared_field_sets");

    $can_edit_fields = MasterPress::current_user_can("edit_shared_fields");
    $can_delete_fields = MasterPress::current_user_can("delete_shared_fields");
    $can_create_fields = MasterPress::current_user_can("create_shared_fields");

    $less = ($can_create_fields) ? 1 : 0;

    $colspan = ( $has_actions ? 7 : 6 ) - $less;
    
    foreach ($field_sets as $field_set) {

      if (MPC::is_deleting($field_set->id, "delete")) {
        
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
    
    <table cellspacing="0" class="grid grid-field-sets grid-shared-field-sets">

    <thead>
    <tr>
      <th class="first type"><i class="types"></i><span><?php _e("Type", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="label"><i class="label-string"></i><span><?php _e("Label", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="front-end-name"><i class="script-php"></i><span><?php _e("Front End Name", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="post-types"><i class="pins"></i><span><?php _e("Post Types", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="taxonomies"><i class="tags"></i><span><?php _e("Taxonomies", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="roles <?php echo $has_actions ? "" : "last" ?>"><i class="user-role"></i><span><?php _e("User Roles", MASTERPRESS_DOMAIN) ?></span></th>
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

      $deleting_class = MPC::is_deleting($field_set->id, "delete") ? 'deleting' : ''; 
      $editable_class = $can_edit ? " editable " : "";
      $meta = $can_edit ? "{ href: '".MasterPress::admin_url("shared-field-sets", "edit", array("id" => $field_set->id) )."' }" : "";

    ?>

    <?php $disabled = $field_set->disabled ? "disabled" : ""; $title = $field_set->disabled ? ' title="'.__("this field set is disabled", MASTERPRESS_DOMAIN).'" ' : ""; ?>

    <?php
    
    if (!$field_set->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("field set is not currently available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
    }
    
    ?>

        
    <tr id="field_set_<?php echo $field_set->name ?>" <?php echo $title ?> class="<?php echo $disabled ?> <?php echo $editable_class ?> <?php echo $deleting_class ?> <?php echo MPV::updated_class("edit,create", $field_set->id) ?> <?php echo $meta ?>">
      <?php if ($field_set->allow_multiple) : ?>
      <th class="first type icon"><i class="metabox-add-remove-large" title="<?php _e("Field Set (Multiple Items)", MASTERPRESS_DOMAIN) ?>"></i></th>
      <?php else : ?>
      <th class="first type icon"><i class="metabox-large" title="<?php _e("Field Set", MASTERPRESS_DOMAIN) ?>"></i></th>
      <?php endif; ?>
      <th class="label"><strong><?php echo $display_td ?></strong></th>
      <th class="front-end-name"><span class="tt"><?php echo $field_set->display_name() ?></span></th>
      <th class="post-types">
        
        <?php 
        
        $post_types = $field_set->post_types(); 

        $vis = $field_set->visibility;
        
        $vis_post_types = "";

        if (isset($vis["post_types"])) {
          $vis_post_types = $vis["post_types"];
        }
        
        if ($vis_post_types == "*") {
          $post_type_display = __("( All )", MASTERPRESS_DOMAIN);
          
        } else {

          $post_type_display = __("( None )", MASTERPRESS_DOMAIN);

          if (count($post_types)) {
            $post_type_links = array();

            foreach ($post_types as $post_type) {
              $post_type_links[] = $post_type->labels["name"];
            }

            $post_type_display = implode($post_type_links, ", ");
          }
        }
        
        echo $post_type_display;
        ?>
        
        
        
      </th>

      <th class="taxonomies">
        
        <?php 
        
        $vis = $field_set->visibility;
        
        
        $vis_tax = "";
        
        if (isset($vis["taxonomies"])) {
          $vis_tax = $vis["taxonomies"];
        }
        
        if ($vis_tax == "*") {
          $taxonomy_display = __("( All )", MASTERPRESS_DOMAIN);
          
        } else {
        
          $taxonomy_display = __("( None )", MASTERPRESS_DOMAIN);
          
          if ($vis_tax != "") {
            
            $tax_models = MPM_Taxonomy::find_by_name_in(explode(",", $vis_tax));
            
            $td = array();
            
            foreach ($tax_models as $tax) {
              $td[] = $tax->display_label();
            }
            
            $taxonomy_display = implode(", ", $td);
          }
        
        }
        
        echo $taxonomy_display;
        ?>
        
      </th>

      <th class="roles <?php echo $has_actions ? "" : "last" ?>">
        
        <?php 
        
        $vis = $field_set->visibility;
        
        $vis_roles = "";
        
        if (isset($vis["roles"])) {
          $vis_roles = $vis["roles"];
        }
        
        if ($vis_roles == "*") {
          $role_display = __("( All )", MASTERPRESS_DOMAIN);
          
        } else {
          
          if (is_null($vis_roles) || $vis_roles == "") {
            $role_display = __("( None )", MASTERPRESS_DOMAIN);
          } else {
            $role_display = implode(", ", explode(",", $vis_roles));
          }
        
        }
        
        echo $role_display;
        ?>
        
      </th>

      <?php if ($has_actions) : ?>
            
      <th class="actions last">
      <?php if (MPC::is_deleting($field_set->id, "delete")) : ?>
        <span class="confirm-action">&nbsp;</span>
      <?php else: ?>
      <div>
        <?php if ($can_edit) : ?>
          <?php echo MPV::action_button("shared-field-sets", "edit", self::__edit(), array("id" => $field_set->id), array("class" => "button button-edit") ); ?>
        <?php endif; ?>
        
        <?php if ($can_delete) : ?>
          <?php echo MPV::action_button("shared-field-sets", "delete", self::__delete(), array("id" => $field_set->id), array("class" => "button button-delete") ); ?>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      </th>

      <?php endif; // has_actions ?>

    </tr>  
    
    
    <?php 
    
    $count = 0; 
    $fields = $field_set->fields();
    
    ?>
    
    <?php foreach ($fields as $field) : $count++; $first = $count == 1 ? 'first' : ''; $disabled = $field_set->disabled || $field->disabled ? "disabled" : ""; $title = $field_set->disabled || $field->disabled ? ' title="'.__("this field is disabled", MASTERPRESS_DOMAIN).'" ' : ""; ?>

    <?php
    if (!$field_set->in_current_site() || !$field->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("field is not currently available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
    }
    ?>
        
    <?php 
    
    $deleting_class = MPC::is_deleting($field_set->id, "delete") || MPC::is_deleting($field->id, "delete-field") ? 'deleting' : ''; 
    $editable_class = $can_edit_fields ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("shared-field-sets", "edit-field", array("id" => $field->id, "parent" => $field_set->id) )."' }" : "";

    
    ?>
  
    <?php if ($type_class = MPFT::type_class($field->type)) : ?>

    <tr <?php echo $title ?> class="sub <?php echo $editable_class.$deleting_class ?> <?php echo $first ?> <?php echo $disabled ?> <?php echo $count % 2 == 0 ? "even" : "" ?> <?php echo MPV::updated_class("edit-field,create-field", $field->id) ?> <?php echo $meta ?>">
      <td class="type icon first" title="<?php echo call_user_func( array($type_class, "__s") ) ?>"><span class="mp-icon mp-icon-field-type-<?php echo $field->type ?>"></span></td>
      <td class="label"><strong><?php echo $field->display_label() ?></strong></td>
      <td class="front-end-name"><span class="tt"><span class="arrow">-&gt;&nbsp;</span><?php echo $field->display_name() ?></span></td>
      <td class="post-types">
        
        <?php 
        
        $vis = $field->visibility;
        
        $vis_post_types = "";

        if (isset($vis["post_types"])) {
          $vis_post_types = $vis["post_types"];
        }
        
        if ($vis_post_types == "*") {

          $post_type_display = '<span class="inherit">( '.__("same as set", MASTERPRESS_DOMAIN).' )</span>';

        } else {

          $post_types = $field->post_types(); 
          
          $post_type_display = MPV::note_none();
        
          if (count($post_types)) {
            $post_type_links = array();
          
            foreach ($post_types as $post_type) {
              $post_type_links[] = $post_type->labels["name"];
            }

            $post_type_display = implode($post_type_links, ", ");
          }
        
        }
        
        echo $post_type_display;
          
        ?>
        
      </td>

      <td class="taxonomies">
        
        <?php 
        
        $vis = $field->visibility;

        $vis_tax = "";
        
        if (isset($vis["taxonomies"])) {
          $vis_tax = $vis["taxonomies"];
        }
        
        if ($vis_tax == "*") {

          $tax_display = '<span class="inherit">( '.__("same as set", MASTERPRESS_DOMAIN).' )</span>';

        } else {

          $tax_display = MPV::note_none();
        
          if ($vis_tax != "") {
            
            $tax_models = MPM_Taxonomy::find_by_name_in(explode(",", $vis_taxonomies));
            
            $td = array();
            
            foreach ($tax_models as $tax) {
              $td[] = $tax->display_label();
            }
            
            $tax_display = implode(", ", $td);
          }
        
        }
        
        echo $tax_display;
          
        ?>
        
      </td>
      
      <td class="roles <?php echo $has_actions ? "" : "last" ?>">
        
        <?php 
        
        $vis = $field->visibility;
        
        $vis_roles = "";
        
        if (isset($vis["roles"])) {
          $vis_roles = $vis["roles"];
        }
        
        if ($vis_roles == "*") {

          $role_display = '<span class="inherit">( '.__("same as set", MASTERPRESS_DOMAIN).' )</span>';

        } else {

          $role_display = MPV::note_none();
        
          if ($vis_roles != "") {
            $role_display = implode(", ", explode(",", $vis_roles));
          }
        
        }
        
        echo $role_display;
          
        ?>
        
      </td>

      <?php if ($has_actions) : ?>

      <td class="actions last">
      <?php if (MPC::is_deleting($field->id, "delete-field")) : ?>
        <span class="confirm-action">&nbsp;</span>
      <?php else: ?>
      <div>
        <?php if ($can_edit_fields) : ?>
          <?php echo MPV::action_button("shared-field-sets", "edit-field", self::__edit(), array("id" => $field->id, "parent" => $field_set->id), array("class" => "button button-edit") ); ?>
        <?php endif; ?>

        <?php if ($can_delete_fields) : ?>
          <?php echo MPV::action_button("shared-field-sets", "delete-field", self::__delete(), array("id" => $field->id, "parent" => $field_set->id), array("class" => "button button-delete") ); ?>
        <?php endif; ?>
        
      </div>
      <?php endif; ?>
      
      </td>
      <?php endif; // has_actions ?>
    </tr>
    
    <?php endif; ?>
      
    <?php endforeach; ?>
  
    <tr class="summary <?php echo $can_create_fields ? "editable" : "" ?>">
      <?php if (count($fields)) : ?>
      <td colspan="<?php echo $colspan ?>" class="first <?php echo $can_create_fields ? "" : "last" ?>"><?php printf( __( "%s contains %s", MASTERPRESS_DOMAIN ), $display, MPU::__items( $field_set->field_count(), __("%d custom field", MASTERPRESS_DOMAIN), __("%d custom fields", MASTERPRESS_DOMAIN) ) ) ?></td>
      <?php else: ?>
      <?php if ($can_create_fields) : ?>
      <td colspan="<?php echo $colspan ?>" class="hl first last"><span><?php printf( __( "<strong>%s</strong> is not yet active as it contains <em>no custom fields</em>. Click here to create one", MASTERPRESS_DOMAIN ), $display ); ?></span></td>
      <?php endif; ?>
      <?php endif; ?>

      <?php if ($can_create_fields) : ?>
      <td class="last actions <?php echo count($fields) ? "" : "hl" ?>">
      <?php echo MPV::action_button("shared-field-sets", "create-field", self::__create(MPV_Fields::__s()), array("parent" => $field_set->id), array("class" => "button create-field")  ); ?>
      </td>
      <?php endif; ?>
    </tr>
    <tr class="gap <?php if ($fg_count == count($field_sets)) { echo "gap-last"; } ?>">
    <td colspan="7">&nbsp;</td>  
    </tr>
    
  
    <?php endforeach; ?>

    </tbody>
  
    </table>

    <?php if ($can_create) : ?>
    <div class="grid-foot-controls">
    <?php echo MPV::action_button("shared-field-sets", "create", MPV::__create( MPV_SharedFieldSets::__s() ), array() ); ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
  
    <?php if ($can_create) : ?>
    <a href="<?php echo MasterPress::admin_url("shared-field-sets", "create") ?>" class="no-items-create">
	<i class="plus-circle"></i>
    <span><?php _e( "There are currently no Shared Field Sets. Click here to create one." ) ?></span>
    </a>
    <?php endif; ?>
  
    <?php endif; ?>

    
    </div>
  
  
    <?php

  } // end grid()
  
  public function form() {
    MPV::incl("field-sets");
    MasterPress::$model->shared = true;

    $mpv_field_sets = new MPV_FieldSets();
    $mpv_field_sets->form();

  } // end form()

}

?>