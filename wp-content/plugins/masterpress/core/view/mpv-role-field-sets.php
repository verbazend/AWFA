<?php 

class MPV_RoleFieldSets extends MPV {

  public static function __s() {
    return __("User Role Field Set", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("User Role Field Sets", MASTERPRESS_DOMAIN);
  }
  

  public function confirm_delete_field($field) {
    MPV::incl("field-sets");
    $info = MasterPress::$view;
    $parent = MasterPress::$parent;
    MPV_FieldSets::confirm_delete_field_panel($field, __("User Role Field", MASTERPRESS_DOMAIN), MPV::action_link("roles", "manage-field-sets", __("Cancel", MASTERPRESS_DOMAIN), "parent=".$parent, array( "class" => "button button-small primary" )) );
  }

  public function confirm_delete($field_set) {
    MPV::incl("field-sets");
    $info = MasterPress::$view;
    $parent = MasterPress::$parent;
    MPV_FieldSets::confirm_delete_panel($field_set, __("User Role Field Set", MASTERPRESS_DOMAIN), MPV::action_link("roles", "manage-field-sets", __("Cancel", MASTERPRESS_DOMAIN), "parent=".$parent, array( "class" => "button button-small primary" )) );
  }



  
  public function grid() {

    MPC::incl("post-types");
    MPV::incl("fields");
    MPV::incl("post-types");
    
    $info = MasterPress::$view;
    
    $role_name = MasterPress::$parent;

    ?>
    
    <div class="title-info-panel">
      <div class="title-info-panel-lt">
        <h3><i class="user-role"></i><span class="label"><?php _e("User Role:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $role_name ?></span></h3> 
      </div>
    </div>
    

  
    <?php MPV::messages(); ?>
    
    <div class="grid-set">
    
    <?php $field_sets = MPM_RoleFieldSet::find_by_role( $role_name, "name" ); ?>

    <?php MPV::field_set_icon_styles($field_sets); ?>

    <?php
    
    $has_actions = MasterPress::current_user_can("edit_user_role_field_sets,delete_user_role_field_sets,edit_user_role_fields,delete_user_role_fields");

    $can_edit = MasterPress::current_user_can("edit_user_role_field_sets");
    $can_delete = MasterPress::current_user_can("delete_user_role_field_sets");
    $can_create = MasterPress::current_user_can("create_user_role_field_sets");

    $can_edit_fields = MasterPress::current_user_can("edit_user_role_fields");
    $can_delete_fields = MasterPress::current_user_can("delete_user_role_fields");
    $can_create_fields = MasterPress::current_user_can("create_user_role_fields");

    $less = ($can_create_fields) ? 1 : 0;

    $colspan = ( $has_actions ? 4 : 3 ) - $less;


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
    
    <?php $fg_count = 0; ?>

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
   
    
    <?php foreach ($field_sets as $field_set) : $fg_count++; ?>  
      
    <?php 
    $display = $field_set->display_label(); 
    
      $display_td = $display;
      
      if ($field_set->icon != "") {
        $display_td = WOOF_HTML::tag("span", array("class" => "with-icon field-set-".$field_set->id), $display);
      }
    
    $deleting_class = MPC::is_deleting($field_set->id, "delete-field-set") ? 'deleting' : ''; 
    $editable_class = $can_edit ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("roles", "edit-field-set", array("id" => $field_set->id, "parent" => $role_name) )."' }" : "";
    
    
    ?>
  
    <?php $disabled = $field_set->disabled ? "disabled" : ""; $title = $field_set->disabled ? ' title="'.__("this field set is disabled", MASTERPRESS_DOMAIN).'" ' : ""; ?>

    <?php
    if (!$field_set->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("this field set is not currently available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
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
          <?php echo MPV::action_button("roles", "edit-field-set", self::__edit(), array("id" => $field_set->id, "parent" => $role_name), array("class" => "button button-edit") ); ?>
        <?php endif; ?>

        <?php if ($can_delete) : ?>
          <?php echo MPV::action_button("roles", "delete-field-set", self::__delete(), array("id" => $field_set->id, "parent" => $role_name), array("class" => "button button-delete") ); ?>
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
    
    <?php foreach ($fields as $field) : $count++; $first = $count == 1 ? 'first' : ''; $disabled = $field_set->disabled || $field->disabled ? "disabled" : ""; $title = $field_set->disabled ? ' title="'.__("this field is disabled", MASTERPRESS_DOMAIN).'" ' : ""; ?>

    <?php
    if (!$field_set->in_current_site() || !$field->in_current_site()) {
      $disabled = "disabled";
      $title = ' title="'.__("this field is not currenty available in this site (multi-site setting)", MASTERPRESS_DOMAIN).'" ';
    }
    ?>


    <?php 
    
    $deleting_class = MPC::is_deleting($field_set->id, "delete-field-set") || MPC::is_deleting($field->id, "delete-field") ? 'deleting' : ''; 
    $editable_class = $can_edit_fields ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("roles", "edit-field", array("id" => $field->id, "gparent" => $_GET["parent"], "parent" => $field_set->id) )."' }" : "";

    ?>
      
    <?php if ($type_class = MPFT::type_class($field->type)) : ?>

    <tr <?php echo $title ?> class="sub <?php echo $editable_class.$deleting_class ?> <?php echo $disabled ?> <?php echo $first ?> <?php echo $count % 2 == 0 ? "even" : "" ?> <?php echo MPV::updated_class("edit-field,create-field", $field->id) ?> <?php echo $meta ?>">
      <td class="type icon first" title="<?php echo call_user_func( array($type_class, "__s") ) ?>"><span class="mp-icon mp-icon-field-type-<?php echo $field->type ?>"></span></td>
      <td class="label"><strong><?php echo $field->display_label() ?></strong></td>
      <td class="front-end-name <?php echo $has_actions ? "" : "last" ?>"><span class="tt"><span class="arrow">-&gt;&nbsp;</span><?php echo $field->display_name() ?></span></td>

      <?php if ($has_actions) : ?>

      <td class="actions last">
      <?php if (MPC::is_deleting($field->id, "delete-field")) : ?>
        <span class="confirm-action">&nbsp;</span>
      <?php else: ?>
      <div>
        <?php if ($can_edit_fields) : ?>
          <?php echo MPV::action_button("roles", "edit-field", self::__edit(), array("id" => $field->id, "gparent" => $_GET["parent"], "parent" => $field_set->id), array("class" => "button button-edit") ); ?>
        <?php endif; ?>

        <?php if ($can_delete_fields) : ?>
          <?php echo MPV::action_button("roles", "delete-field", self::__delete(), array("id" => $field->id, "gparent" => $_GET["parent"], "parent" => $role_name), array("class" => "button button-delete") ); ?>
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
      <td colspan="<?php echo $colspan ?>" class="hl first last"><span><?php printf( __( "<strong>%s</strong> is not yet active as it contains <em>no custom fields</em>. Click here to create one", MASTERPRESS_DOMAIN ), $field_set->display_label() ); ?></span></td>
      <?php endif; ?>

      <?php if ($can_create_fields) : ?>
      <td class="last actions <?php echo count($fields) ? "" : "hl" ?>">
      <?php echo MPV::action_button("roles", "create-field", self::__create(MPV_Fields::__s()), array("gparent" => $_GET["parent"], "parent" => $field_set->id), array("class" => "button create-field")  ); ?>
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
    <?php echo MPV::action_button("roles", "create-field-set", MPV::__create( MPV_RoleFieldSets::__s() ), array( "parent" => $role_name ) ); ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
  
    <?php if ($can_create) : ?>
    <a href="<?php echo MasterPress::admin_url("roles", "create-field-set", array("parent" => MasterPress::$parent ) ) ?>" class="no-items-create">
	<i class="plus-circle"></i>
    <span><?php printf( __( "The '<em>%s</em>' role currently has <em>no field sets</em>. Click here to create one.", MASTERPRESS_DOMAIN ), $role_name  ) ?></span>
    </a>
    <?php endif; ?>
  
    <?php endif; ?>

    
    </div>
  

  
    <?php

  } // end grid()
  
  public function form() {
    MPV::incl("field-sets");
    MasterPress::$model->role = true;
    
    $mpv_field_sets = new MPV_FieldSets();
    $mpv_field_sets->form();

  } // end form()

}

?>
