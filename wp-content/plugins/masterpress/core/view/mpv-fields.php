<?php

class MPV_Fields extends MPV {
  
  public static function __s() {
    return __("Field", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("Fields", MASTERPRESS_DOMAIN);
  }

  public function grid() {
    ?>

    <?php
  
  } // end grid()
  
  public function form() {
    
    global $wf;
    
    $model = MasterPress::$model;
    
    $info = MasterPress::$view;
    
    $parent = $info->parent;
    
    $post_type = null;
    
    $post_type = $info->post_type;
  
    $prefix = "";
    $default_key = "";
    $keys = array();
    
    ?>
    
    <?php if (is_object($parent)) : ?>
    <div class="title-info-panel">
      <div class="title-info-panel-lt">
        <?php if ($parent->type == 's') : ?>
        <h3><i class="metabox-share"></i><span class="label"><?php _e("Shared Field Set", MASTERPRESS_DOMAIN) ?>:</span><span class="value"> <?php echo $parent->display_label() ?></span></h3> 
        <?php elseif ($parent->type == 'w') : ?>
        <h3><i class="metabox-sitemap-large"></i><span class="label"><?php _e("Site Field Set", MASTERPRESS_DOMAIN) ?>:</span><span class="value"> <?php echo $parent->display_label() ?></span></h3> 
        <?php elseif ($parent->type == "p") : ?>
          
        <?php

        $prefix = $post_type->name."_"; 
        $keys = array("edit_".$post_type->capability_type."s");

        ?>
        
        <h3><i class="mp-icon mp-icon-post-type mp-icon-post-type-<?php echo $post_type->name ?>"></i><span class="label"><?php _e("Post Type", MASTERPRESS_DOMAIN) ?>:</span><span class="value"> <?php echo $post_type->display_label() ?></span></h3> 
        <h3><i class="divide"></i><i class="metabox"></i><span class="label"><?php _e("Field Set", MASTERPRESS_DOMAIN) ?>:</span><span class="value"> <?php echo $parent->display_label() ?></span></h3> 
        
        <?php elseif ($parent->type == 't') : ?>
        <?php
        
        $file = $parent->visibility["templates"];
        $page_templates = array_flip(get_page_templates());
        $template_name = $page_templates[$file];
        
        ?>
        <input id="templates" name="templates" type="hidden" value="<?php echo $file ?>" />
        
        <h3><i class="template"></i><span class="label"><?php _e("Template:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $template_name ?></span></h3> 
        <h3><i class="divide"></i><i class="metabox"></i><span class="label"><?php _e("Field Set", MASTERPRESS_DOMAIN) ?>:</span><span class="value"> <?php echo $parent->display_label() ?></span></h3> 
        
        <?php elseif ($parent->type == 'r') : ?>
        <?php
        
        $role_name = $parent->visibility["roles"];
        $prefix = $role_name."_";
        
        ?>
        <input id="templates" name="templates" type="hidden" value="<?php echo $role_name ?>" />
        <h3><i class="user-role"></i><span class="label"><?php _e("User Role:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $role_name ?></span></h3> 
        <h3><i class="divide"></i><i class="metabox"></i><span class="label"><?php _e("Field Set", MASTERPRESS_DOMAIN) ?>:</span><span class="value"> <?php echo $parent->display_label() ?></span></h3> 
        
        
        <?php elseif ($parent->type == 'x') : ?>
        <?php
        
        $taxonomy_name = $parent->visibility["taxonomies"];
        $prefix = $taxonomy_name."_";
        $tax = MPM_Taxonomy::find_by_name($taxonomy_name);

        $keys = array(
          $tax->capabilities["manage_terms"],
          $tax->capabilities["edit_terms"],
          $tax->capabilities["assign_terms"]
        );
        
        ?>
        <input id="taxonomies" name="taxonomies" type="hidden" value="<?php echo $taxonomy_name ?>" />
        <h3><i class="mp-icon mp-icon-taxonomy-<?php echo $taxonomy_name ?>"></i><span class="label"><?php _e("Taxonomy:", MASTERPRESS_DOMAIN) ?></span><span class="value"> <?php echo $tax->display_label() ?></span></h3> 
        <h3><i class="divide"></i><i class="metabox"></i><span class="label"><?php _e("Field Set", MASTERPRESS_DOMAIN) ?>:</span><span class="value"> <?php echo $parent->display_label() ?></span></h3> 
        
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php MPV::messages(); ?>

    <div class="mpv-fields-form">
      
    <div class="f">
      <label for="name" class="icon"><i class="script-php"></i><?php _e("Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="name_original" name="name_original" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />
        <input id="name_last" name="name_last" type="hidden" class="text mono key" maxlength="20" value="<?php echo $model->name ?>" />

        <input id="name" name="name" type="text" class="text mono key" maxlength="255" value="<?php echo $model->name ?>" /><em class="required">(required)</em>
        <p>
          <?php _e("This is a unique identifier for the field in the MasterPress API. Since it can be used as a PHP variable name, it is restricted to being a lowercase string with words separated by underscores.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>

    </div>
    <!-- /.f -->

    
    
    <div id="f-type" class="f clearfix">
      <label for="type" class="icon"><i class="types"></i><?php _e("Field Type", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        
        
        <select id="type" name="type" class="with-icons select2-source" data-icon="mp-icon mp-icon-field-type-<?php echo $model->type ?>">
        <?php foreach (MPFT::types_by_category() as $category => $types) : ?>
        <?php if (count($types)) : ?>

        <optgroup label="<?php echo $category ?>">

        <?php foreach ($types as $type) : ?>
          <?php
            $type_class = MPFT::type_class($type);
          ?>
          <option data-icon="mp-icon mp-icon-field-type-<?php echo $type ?>" <?php echo WOOF_HTML::selected_attr($type == $model->type) ?> data-description="<?php echo addslashes(call_user_func( array($type_class, "__description") ) ) ?>" value="<?php echo $type ?>"><?php echo call_user_func( array($type_class, "__s") ) ?></option>  
        <?php endforeach; ?>
        
        </optgroup>
        
        <?php endif; ?>
        <?php endforeach; ?>
        </select>
        
        
        <p id="field-type-description"></p>
        
      </div>
    </div>
    <!-- /.f -->


    <div class="f">
      <label for="required" class="icon"><i class="warning-octagon"></i><?php _e("Required", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="required" name="required" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->required ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#required' }">
          <?php _e("A field value must be specified by the user, or the content editing form will not submit.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->
    
    <div id="f-disabled" class="f">
      <label for="disabled" class="icon"><i class="slash"></i><?php _e("Disabled", MASTERPRESS_DOMAIN) ?>?</label>
      <div class="fw">
        <input id="disabled" name="disabled" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( $model->disabled ) ?> class="checkbox" />
        <p class="checkbox-alt-label { for_el: '#disabled' }">
          <?php _e("disabling a field will hide it from all content editing screens in WordPress, which may be <strong>preferable to deleting it</strong> completely.", MASTERPRESS_DOMAIN); ?>
        </p>
      </div>
    </div>
    <!-- /.f -->  
    
    <?php
      
      $options_form = "";
      
      $type_options = $model->type_options;
      
      if (isset($_POST["type_options"])) {
        $type_options = $_POST["type_options"];
      }
              
      if ($type_class = MPFT::type_class($model->type)) { 
        $options_form = call_user_func_array( array($type_class, "options_form"), array(MPM::array_stripslashes($type_options)) );
      }
      
    ?>
    
    <div class="fs fs-field-type-options" <?php echo $options_form == "" ? ' style="display:none;"' : '' ?>>
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="switch"></i><strong><?php _e("Field Type Options", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("defines the user-interface for this field", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
        
      <div class="fsc">
      <div class="fscb">
      
        <div id="field-type-loading" class="fs-loading">Please wait&hellip;</div>
        
        <div id="field-type-options-content">
          <?php if ($type_class = MPFT::type_class($model->type)): ?>
            
          <div id="mpft-<?php echo $model->type ?>-options" class="mpft-options">
          <?php echo $options_form ?>
          </div>
          
          <?php endif; ?>
          
        </div>
        <!-- /#field-type-options-content -->
      
      </div>
      </div>

    </div>
    <!-- /.fs -->
    
        

    <div class="fs fs-labels">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="label-string"></i><strong><?php _e("Labels", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("displayed throughout the WordPress administration UI", MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
        
      <div class="fsc">
      <div class="fscb">
      
        <div class="f">
          <label for="label_name"><?php _e("Field Label", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            <input id="label_name" name="labels[name]" value="<?php echo stripslashes($model->labels["name"]) ?>" type="text" class="text { tmpl: '{{name}}' }" />
            <em class="recommended">(recommended)</em>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_description"><?php _e("Description", MASTERPRESS_DOMAIN)?>:</label>
          <div class="fw">
            <input id="label_description" name="labels[description]" type="text" maxlength="50" value="<?php echo stripslashes($model->label("description")) ?>" class="text" />
            <p>
              <?php _e("Appears adjacent to the label in brackets to provide a little extra information about the field.<br />For more extensive information, use <em>Tooltip Help</em>.", MASTERPRESS_DOMAIN); ?>
            </p>
          </div>
        </div>
        <!-- /.f -->

        <div class="f">
          <label for="label_tooltip_help"><?php _e("Tooltip Help", MASTERPRESS_DOMAIN)?>:</label>
          <div class="fw">
            <textarea id="label_tooltip_help" name="labels[tooltip_help]"><?php echo stripslashes($model->label("tooltip_help")) ?></textarea>
            <p>
              <?php _e("Provides more extensive help for this field through a tooltip, revealed by mousing over a question-mark badge adjacent to the field label", MASTERPRESS_DOMAIN); ?>
            </p>
          </div>
        </div>
        <!-- /.f -->
    
        
      
      </div>
      </div>

    </div>
    <!-- /.fs .fs-labels -->
    
    
    
    
    
    <?php
    
      
    $args = array();

    $args["supports"] = array();

    // $args["supports"][] = "fields"; COMING SOON

    $fields = $parent->fields();
    
    $fields_to_use = array();
    
    foreach ($fields as $field) {
      if ($field->id != $model->id) {
        $fields_to_use[] = $field;
      }
    }
    
    $args["fields"] = $fields_to_use;


    
    if (is_multisite()) {
      $args["supports"][] = "multisite";
    }
    
    if ($parent->is_shared()) {
      $args["supports"][] = "post_types";
      $args["supports"][] = "roles";
      $args["supports"][] = "taxonomies";
      $args["supports"][] = "templates";
    }
    
    $args["labels"] = array(
      "title" =>  __("control the visibility of this field within WordPress", MASTERPRESS_DOMAIN),   
      "title_multisite" =>  __("specify the sites in the multisite network that this field is available in", MASTERPRESS_DOMAIN),   
      "title_post_types" =>  __("specify the post types that this field is available in", MASTERPRESS_DOMAIN),   

      "templates_all" => sprintf( __( "All Templates <span>( that the <em>%s</em> field set is available in )</span>" ), $parent->display_label() ),
      "multisite_all" => sprintf( __( "All Sites <span>( that the <em>%s</em> field set is available in )</span>" ), $parent->display_label() ),
      "post_types_all" => sprintf( __( "All Post Types <span>( that the <em>%s</em> field set is available in )</span>" ), $parent->display_label() ),
      "roles_all" => sprintf( __( "All User Roles <span>( that the <em>%s</em> field set is available in )</span>" ), $parent->display_label() ),
      "taxonomies_all" => sprintf( __( "All Taxonomies <span>( that the <em>%s</em> field set is available in )</span>" ), $parent->display_label() )
    );

    $args["defaults"] = array(
      "taxonomies" => "all",
      "roles" => "all",
      "post_types" => "all",
      "multisite" => "all"
    );
  
    $args["post_types"] = $parent->post_types();
    
    if (isset($parent) && is_multisite()) {
      
      $site_options = array();
      
      foreach ($parent->sites() as $site) {
        $site_options[$site->full_path()] = $site->id();
      }
      
      $args["sites"] = $site_options;
      
    };
    
    // filter the available roles
    $roles = $parent->vis("roles");
    
    if ($roles != "" && $roles != "*") {
      $role_names = explode(",", $roles);
      
      $rc = array();
      
      foreach ($role_names as $role_name) {
        $rc[] = $wf->role($role_name);  
      }
      
      $args["roles"] = new WOOF_Collection($rc);
    }
    
    MPV::fs_visibility( $model, $args ); 
  
    ?>
    
    
    <div class="fs fs-capability-keys clearfix">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="key"></i><strong><?php _e("Capabilities", MASTERPRESS_DOMAIN) ?></strong> - <?php _e('the keys used to control access to this field', MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb clearfix">
      
      <div class="f f-capabilities f-capabilities-editable clearfix">
          <label for="label-capabilities-editable"><?php _e("Editable", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            
            <?php 
            
              $val = $model->capability("editable", false);
              $custom_val = "";

              $caps = array_unique(array_merge($keys, array("edit_posts", "edit_pages", "edit_".$prefix.$parent->name)));
              
              $caps = array( __("-- None (Same as Field Set) --", MASTERPRESS_DOMAIN) => "" ) + $caps;
              
              if (MPC::is_edit()) {
                $caps[] = "edit_".$prefix.$parent->name."_".$model->name;
              }


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
              <?php _e("This field will be read-only for users without this capability", MASTERPRESS_DOMAIN) ?>
            </p>
          </div>
        </div>
        <!-- /.f -->
        
        <div class="f f-capabilities f-capabilities-visible clearfix">
          <label for="label-capabilities-visible"><?php _e("Visible", MASTERPRESS_DOMAIN) ?>:</label>
          <div class="fw">
            
            <?php 
            
              $val = $model->capability("visible", false);
              
              $caps = array_unique(array_merge($keys, array("edit_posts", "edit_pages", "view_".$prefix.$parent->name)));
              
              $caps = array( __("-- None (Same as Field Set) --", MASTERPRESS_DOMAIN) => "" ) + $caps;
              
              if (MPC::is_edit()) {
                $caps[] = "view_".$prefix.$parent->name."_".$model->name;
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
              <?php _e("This field will be invisible for users without this capability", MASTERPRESS_DOMAIN) ?>
            </p>
          </div>
        </div>
        <!-- /.f -->

        
      
      </div>
      </div>

    </div>
    <!-- /.fs -->
    

    <div class="fs fs-summary-options clearfix">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="set-summary"></i><strong><?php _e("Summary Options", MASTERPRESS_DOMAIN) ?></strong> - <?php _e('settings for the collapsed summary display', MASTERPRESS_DOMAIN) ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb clearfix">
        

      <div class="f">
        <label for="summary_options_emphasise"><?php _e("Emphasise", MASTERPRESS_DOMAIN) ?>?</label>
        <div class="fw">
          <input id="summary_options_emphasise" name="summary_options[emphasise]" type="checkbox" value="true" <?php echo WOOF_HTML::checked_attr( isset($model->summary_options["emphasise"]) && $model->summary_options["emphasise"] ) ?> class="checkbox" />
          <p class="checkbox-alt-label { for_el: '#summary_options_emphasise' }">
            <?php _e("Show the summary for this field in <strong>bold text</strong>, which may often be appropriate for the first field in a set, or for other important fields in your content.", MASTERPRESS_DOMAIN); ?>
          </p>
        </div>
      </div>
      <!-- /.f -->

      <div class="f f-summary_width">
        <label for="summary_options_width"><?php _e("Block Width", MASTERPRESS_DOMAIN) ?>?</label>
        <div class="fw">
          <input id="summary_options_width" name="summary_options[width]" type="text" maxlength="1" value="<?php echo isset($model->summary_options["width"]) ? $model->summary_options["width"] : "" ?>" class="text" />
          <p class="note">
            <?php _e("Override block width of the summary, when the field's set is collapsed (min value 1, max value 5)<br />Leave this value empty to use the block width recommended by the field type<br />The pixel width of the summary will be the value here x 150.", MASTERPRESS_DOMAIN); ?>
          </p>
        </div>
      </div>
      <!-- /.f -->

      <div class="f clearfix">
        <label id="label-icon" for="icon"><?php _e("Icon (16 x 16)", MASTERPRESS_DOMAIN) ?>:</label>
        <div class="fw">

          <div id="icon-file-uploader" class="icon-uploader file-uploader { ids: { drop: 'icon_drop_area' }, input: '#icon', inputName: 'icon_ul', base_url: '<?php echo MASTERPRESS_GLOBAL_CONTENT_URL ?>', params: { dir: 'menu-icons/' }, limit: 1, lang: { buttonChoose: '<?php _e("Choose from Computer&hellip;", MASTERPRESS_DOMAIN) ?>', buttonReplace: '<?php _e("Replace file&hellip;", MASTERPRESS_DOMAIN) ?>' } }">
          
            <div id="icon_drop_area" class="drop-area"><?php _e("Drop file here to upload", MASTERPRESS_DOMAIN) ?></div>

            <?php 
          
          
            $file_name = $model->icon;
            $file_class = "";
            $preview_class = "";
            $clear_class = "";
          
            if ($file_name == "") {
              $file_name = __("( None )", MASTERPRESS_DOMAIN);
              $file_class = "name-none";
              $preview_class = "preview-none";
              $clear_class = "hidden";
              $style = "";
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
                
            
      
      </div>
      </div>

    </div>
    <!-- /.fs -->
    
    

    <?php
    
      // build a field list
      
      $fields = $parent->fields();
    
    ?>
    
    
    <?php if ( ( MPC::is_edit() && count($fields) > 1) || (MPC::is_create() && count($fields) > 0) ) : ?>
      
    <div id="f-position" class="f clearfix">
      <p class="label label-icon icon position"><?php _e("Position in set", MASTERPRESS_DOMAIN)?>:</p>
      <div class="fw">

          <div class="sortable-list sortable-list-fields">
            <span class="arrow"></span>
            
            <?php
            
            // build a field list
            
            $sort_fields = array();
            
            // now build a representable list of fields
            
                
            foreach ($fields as $field) {
              
              $position = $field->position;
            
              if (isset($_POST["other_position"]) && isset($_POST["other_position"][$field->id])) {
                $position = $_POST["other_position"][$field->id];
              }

              if ($ftc = MPFT::type_class($field->type)) {
                $sort_fields[] = array( "position" => $position, "disabled" => (bool) $field->disabled, "current" => $field->id == $model->id, "label" => $field->display_label(), "type" => $field->type, "id" => $field->id );
              }
            }
            
            if (MPC::is_create()) {
              $label = $model->display_label();
              
              if (!$label || $label == "") {
                $label = "?";
              }
            
            
              $sort_fields[] = array( "position" => $model->position, "disabled" => (bool) $field->disabled, "current" => true, "label" => $model->display_label(), "type" => $model->type );

            }
            
            $count = 0;
            
            ?>
            
            <ul>
              <?php foreach ($sort_fields as $f) : $count++; $first = $count == 1 ? "first " : ""; $current = $f["current"] ? 'current ' : '';  ?>

              <?php
                $disabled = $f["disabled"] ? 'disabled' : '';
                $disabled_title = $f["disabled"] ? __("This field is disabled", MASTERPRESS_DOMAIN) : '';
              ?>

              <li class="<?php echo $first.$current ?>  <?php echo $disabled ?>" title="<?php echo $disabled_title ?>">
                <span class="icon mp-icon mp-icon-field-type-<?php echo $f["type"] ?>"></span>
                <?php if ($f["current"]) : ?>
                <span class="fill { src: '#label_name'}"><?php echo $f["label"] ?></span>
                <input id="position" name="position" value="<?php echo $f["position"] ?>" type="hidden" />
                <?php else: ?>
                <span><?php echo $f["label"] ?></span>
                <input id="other_position_<?php echo $f["id"] ?>" name="other_position[<?php echo $f["id"] ?>]" value="<?php echo $f["position"] ?>" type="hidden" />
                <?php endif; ?>
              </li>
              <?php endforeach; ?>
            </ul>
            
          </div>
          <!-- /.sortable-list -->
          
        <p>
          <?php printf(__("Drag field to the desired position in the %s set.<br />Note: changes to the positions of other fields will also be saved.", MASTERPRESS_DOMAIN), '<em>'.$parent->display_label().'</em>') ?>
        </p>
      </div>
    </div>
    <!-- /.f -->
    
    <?php else: ?>
    <input id="position" name="position" value="1" type="hidden" />
    <?php endif; ?>
    
    
    
    </div>
    <!-- /.mpv-fields-form -->
    
    <?php
  } // end form()
  
}

?>