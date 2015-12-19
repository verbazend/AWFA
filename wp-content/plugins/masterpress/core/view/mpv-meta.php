<?php

class MPV_Meta {
  
  public static $queued_templates = array();
  
  public static function type_key($object) {
    $class = get_class($object);
    $parts = explode("_", $class);
    $last = count($parts) - 1;
    return strtolower($parts[$last]);
  }
  
  public static function inline_head($sets, $object) {
    
    global $typenow;
    
    ?>
    <!-- inline styles to change the padding of specific metaboxes, since WordPress has no hook to class them -->
    
    <style type="text/css">
    
    <?php 
        
    $metabox_inside_rules = "padding: 0; margin: 0;"; 

    $field_types_loaded = array();
    
    $object_type_name = "";
    
    $object_type = self::type_key($object);
    
    if ($object_type == "term" && isset($_GET["taxonomy"])) {
      $object_type_name = $_GET["taxonomy"];
    }
    
    
    foreach ($sets as $set) {
      echo "#field-set-".$set->html_id()." .inside { ".$metabox_inside_rules." } "; 
    }
    
    // include the field types, and setup color icon rollover states
    foreach (MPFT::type_keys() as $type) {

      if ($type_class = MPFT::type_class($type)) {
        // echo field type icons
        echo ".mp-set-summary .mpft-".$type."-summary .empty h4 i { background-image: url(".MPU::type_icon_url($type, false)."); } ";
        echo ".mp-set-summary .mpft-".$type."-summary h4 i { background-image: url(".MPU::type_icon_url($type, true)."); } ";
        
        MPU::mq2x_start();
        
        echo ".mp-set-summary .mpft-".$type."-summary .empty h4 i { background-image: url(".MPU::type_image($type, "icon-gray.png", "")->resize("w=32&h=32")->url."); background-size: 16px 16px; } ";
        echo ".mp-set-summary .mpft-".$type."-summary h4 i { background-image: url(".MPU::type_image($type, "icon-color.png", "")->resize("w=32&h=32")->url."); background-size: 16px 16px; } ";
        
        MPU::mq2x_end();
        
      }
      
    }
      
    ?>
    
    </style>

    <script type="text/javascript">
      jQuery.mp.object_id = <?php echo $object->id() ?>;
      jQuery.mp.object_type = '<?php echo self::type_key($object) ?>';
      jQuery.mp.object_type_name = '<?php echo $object_type_name ?>';
      
      jQuery.mp.lang.check_all_items = '<?php echo esc_js(__("Check All Items", MASTERPRESS_DOMAIN)) ?>';
      jQuery.mp.lang.uncheck_all_items = '<?php echo esc_js(__("Uncheck All Items", MASTERPRESS_DOMAIN)) ?>';
      jQuery.mp.lang.remove_checked_items = '<?php echo esc_js(__("Remove Checked Items", MASTERPRESS_DOMAIN)) ?>';
      jQuery.mp.lang.confirm_remove_checked_items = '<?php echo esc_js(__("Remove Checked Items: Are You Sure?", MASTERPRESS_DOMAIN)) ?>';
    </script>
    
    <script type="text/javascript">
    
    jQuery.mpft_lang = {};
    
    <?php 
    
    $field_types_loaded = array();

    foreach ($sets as $set) {

      // include the field types, and setup color icon rollover states
      foreach ($set->fields() as $field) {
        
        if (!in_array($field->type, $field_types_loaded)) {

          $field_types_loaded[] = $field->type;

          if ($ftc = MPFT::type_class($field->type)) {
            $lang = call_user_func( array($ftc, "ui_lang" ) );
            
            if (count($lang)) {
            ?>
            jQuery.mpft_lang['<?php echo $field->type ?>'] = <?php echo json_encode($lang) ?>;
            <?php
            
            }
          }
        
        }
        
      }
    
    }
    
    ?>
    
    </script>
    
    <script id="mp-set-preview-multiple-template" type="text/html">
    
    <?php
    $expand_all = __("Expand All", MASTERPRESS_DOMAIN);
    $check_all_items = __("Check All Items", MASTERPRESS_DOMAIN);
    $collapse_all = __("Collapse All", MASTERPRESS_DOMAIN);
    $check_all_items = __("Check All Items", MASTERPRESS_DOMAIN);
    
    $label_one_item = __("One Item", MASTERPRESS_DOMAIN);
    $label_n_items = __("%d Items", MASTERPRESS_DOMAIN);
    $label_no_items = __("No Items", MASTERPRESS_DOMAIN);

    
    ?>
    
    <div class="mpv mp-set preview multiple { lang: { item: '<?php echo $label_one_item ?>', items: '<?php echo $label_n_items ?>', no_items: '<?php echo $label_no_items ?>' } }">
  
    <div class="mp-set-inner">
    
    <div class="mp-set-head">
    
    <ul class="mp-controls lt">
    </ul>
    
    <ul class="mp-controls rt">
    <li class="control-expand-all"><button type="button" class="icon ir expand-all" title="<?php echo $expand_all ?>"><?php echo $expand_all ?></button></li>
    <li title="<?php echo $check_all_items ?>" class="control-check-all"><input type="checkbox" class="checkbox check-all" /></li>
    <li class="control-collapse-all"><button type="button" class="icon ir collapse-all" title="<?php echo $collapse_all ?>"><?php echo $collapse_all ?></button></li>
    </ul>
    
    </div>
    
    <div class="mp-set-items"></div>
    
    </div>
    
    </div>
    
    </script>
    
    
    <script id="mp-set-preview-template" type="text/html">
    
    <div class="mpv mp-set preview read-only single"><div class="mp-set-inner"></div></div>
    
    </script>
    
    
    <script id="mp-postbox-template" type="text/html">
    <div id="{{id}}" class="postbox">
      <div class="handlediv" title="<?php _e("Click to toggle") ?>"><br></div><h3 class="hndle"><span>{{title}}</span></h3>
      <div class="inside">
      {{{html}}}
      </div>
    </div>
    </script>
    
    <script id="invalid-fields-publish-message-template" type="text/html">
    <p id="invalid-fields-publish-message" class="publish-message invalid"><i class="error-circle"></i><span><?php echo __("Some required fields are missing. Please provide values for any fields marked in red, and try again", MASTERPRESS_DOMAIN); ?></span></p>
    </script>

    <script id="title-publish-message-template" type="text/html">
    <p id="title-publish-message" class="publish-message invalid"><i class="error-circle"></i><span><?php echo __("The title for this item must be entered. Please provide a title and try again", MASTERPRESS_DOMAIN); ?></span></p>
    </script>
    
    <?php 
    
    // output the script and link tags for each loaded field type

    $include_pe_style = true;
    $include_pe_script = true;
    
    if (!(MASTERPRESS_DEBUG || WP_DEBUG)) {

      // try to get the combined file

      $mpft_css_url = MPU::type_styles_file_url();
      
      if ($mpft_css_url) {
        $include_pe_style = false; // no separate scripts
        // include the file
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $mpft_css_url ?>" />
        <?php
      }


      $mpft_js_url = MPU::type_scripts_file_url();
      
      if ($mpft_js_url) {
        $include_pe_script = false; // no separate scripts
        // include the file
        ?>
        <script type="text/javascript" src="<?php echo $mpft_js_url ?>"></script>
        <?php
      }


      
    } 
    
    
    foreach (MPFT::type_keys() as $type) {
      if ($include_pe_style) {
        MPFT::meta_style($type);
      }
      
      if ($include_pe_script) {
        MPFT::meta_script($type);
      }
    }
  
    // output the handlebars templates for sets and fields 
  
    foreach ($sets as $set) {
      if ($set->allow_multiple) {
        self::get_set_templates($object, $set);
      }
    }
    
    echo implode(" ", self::$queued_templates);
    self::$queued_templates = array();
  }
  
  
  public static function get_set_templates($object, $set, $args = array()) {
  
    $html = '';
    
    $meow_set = new MEOW_FieldSetCreator($set->name, $object, $set);
    
    // output the set template
    
    $id = "mpft_ui_{$set->name}";

    if (isset($args["guid"])) {
      $id .= "_".$args["guid"];
    }

    if (!$set->current_user_can_edit()) {
      $args["readonly"] = true;
    }
    
    $set_item_template_data = MPV_Meta::set_item_template_data($meow_set, $args);
    
    if (!$set->allow_multiple && !WOOF::is_true_arg($args, "nested")) {
      $set_item_template_data["set_index"] = "1";
    }

    self::$queued_templates[] = '<script id="'.$id.'" type="text/x-handlebars-template">'.WOOF::render_template( MPV_Meta::set_item_template(), $set_item_template_data, true ).'</script>';
    
    return $html;
  }
  
  public static function get_preview_set_template($set) {

    $html = '';
    
    $meow_set = new MEOW_FieldSetCreator($set->name, null, $set);
    
    $id = "mpft_ui_preview_{$set->name}";
    $set_item_template_data = MPV_Meta::set_item_template_data($meow_set, array("preview" => true));
    
    if (!$set->allow_multiple) {
      $set_item_template_data["set_index"] = 1;
    }
  
    $html .= WOOF::render_template( MPV_Meta::set_item_template(), $set_item_template_data, true );
  
    return $html;

  }
  

  public static function get_set($object, $set_items, $args = array("nested" => false)) {
    
    global $meow_provider, $wf;
    
    $data = array();
    
    $set = $set_items->info;
    
    // check for versions
    
    $set_args = $args;
    
    if (WOOF::is_false_arg($args, "nested")) {
      $versions = $set_items->versions();
    
      if (count($versions)) {
        $versions_options = array();
      
        $count = 0;
      
        foreach ($versions as $version) {
          $count++;

          $prefix = "";
        
          if ($count == 1) {
            $prefix .= __("Current", MASTERPRESS_DOMAIN);
          } else {
            $prefix = WOOF::items_number( $count - 1,  __("Current", MASTERPRESS_DOMAIN), __("1 version ago", MASTERPRESS_DOMAIN), __("%d versions ago", MASTERPRESS_DOMAIN) );
          }
        
          $text = $prefix." &mdash; ".$wf->format_date("[date-time-long]", strtotime(get_date_from_gmt($version->date)));
        
          if (!is_null($version->user_login)) {
            $text = $text." &mdash; ".$version->user_login;
          }
        
          if ($set->allow_multiple) {
            $text = $text." ( ".WOOF::items_number($version->field_set_count, __("no items", MASTERPRESS_DOMAIN), __("1 item", MASTERPRESS_DOMAIN), __("%d items", MASTERPRESS_DOMAIN))." )";
          }
      
          $versions_options[$text] = $version->version_id;
        }

        $selected_version = "";
      
        if (count($versions_options) > 1) {
          $vvals = array_values($versions_options);
          $selected_version = $vvals[1];
        }
      
        $data["versions_select_id"] = "versions-".$set->id;
        
        if ($set->current_user_can_edit()) {
          $data["versions_select"] = WOOF_HTML::select(
            array("id" => $data["versions_select_id"]),
            $versions_options,
            $selected_version
          );
    
          $data["versions_title"] = sprintf( __("Versions of '%s'", MASTERPRESS_DOMAIN), $set->display_label() );
          $set_args["versions_select"] = $data["versions_select"];
        }
      
          
      }
    }
    
    $set_classes = array();
    
    if ($set->allow_multiple) {
      $set_classes[] = "multiple";
    } else {
      $set_classes[] = "single";
    }
    
    
    $data["allow_remove"] = true;
    $data["set_allow_add"] = true;
    
    $data["copy_paste_title"] = __("Copy / Paste Content", MASTERPRESS_DOMAIN);


    if (!$set->current_user_can_edit()) {
      $data["copy_paste_title"] = __("Copy Content", MASTERPRESS_DOMAIN);
      $data["readonly"] = true;
      $data["allow_remove"] = false;
      $data["set_allow_add"] = false;
      $set_classes[] = "readonly";
    }
    
    
    $set_head_classes = array();
    
    $add_item_title = strip_tags($set->label("add_another"));
    
    if ($set_items->count() == 0) {
      $set_head_classes[] = "hidden";  
      $add_item_title = strip_tags($set->label("click_to_add"));
    } else {
      $data["set_foot_controls_style"] = "display: none;";  
    }
    
    $type_key = $meow_provider->type_key($object);
    
    $set_description = trim( $set->label("description") );
    
    if ($set_description != "") {
      $data["set_description"] = trim( $set->label("description") );
    }
  
    $set_type_description = $set->label("description_".$type_key);
  
    if (isset($set_type_description) && trim($set_type_description) != "") {
      $data["set_description"] = trim($set_type_description);
    }
    
    $data["set_head_classes"] = implode(" ", $set_head_classes);
    $data["set_classes"] = implode(" ", $set_classes);

    $data["set_id"] = $set->id;
    $data["set_name"] = $set->name;
    
    if (isset($args["guid"])) {
      $data["add_item_template"] = "mpft_ui_{$set->name}"."_".$args["guid"];
    }

    
    $data["set_dirty_name"] = "mp_meta_dirty[{$set->name}]";
    $data["set_versions_name"] = "mp_meta_versions[{$set->name}]";
    $data["set_versions"] = $set->versions;
    
    $data["set_allow_multiple"] = $set->allow_multiple;
    $data["set_expanded"] = $set->expanded;
  
    $data["add_item_title"] = $add_item_title;

    $data["set_name_sanitized"] = str_replace("_", "-", $wf->sanitize($set->name));
    
    if (!isset($set->icon) && $set->icon != "") {
      $data["set_icon"] = MPU::field_set_icon_url($set->icon);
      $data["set_icon_2x"] = MPU::field_set_icon_2x_url($set->icon);
    }
    
    $data["label_add_another"] = esc_js( strip_tags($set->label("add_another")) );
    $data["label_one_item"] = esc_js( $set->label("one_item"));
    $data["label_n_items"] = esc_js($set->label("n_items"));
    $data["label_no_items"] = esc_js($set->label("no_items"));
    $data["label_click_to_add"] = esc_js($set->label("click_to_add"));
    $data["label_remove_confirm"] = strip_tags($set->label("remove")).": ".__("Are you sure?", MASTERPRESS_DOMAIN);

    $data["set_items_count"] = MPU::__items($set_items->count(), $set->label("one_item"), $set->label("n_items"), $set->label("no_items"));
    
    if ($type_key == "post") {
      $data["save_nag_message"] = sprintf( __('To Save changes you must also <strong>Save Draft</strong> or <strong class="update">Update</strong> this %s', MASTERPRESS_DOMAIN ), $object->post_type()->singular_label() );
    } else if ($type_key == "term") {
      $data["save_nag_message"] = sprintf( __('To Save changes you must also <strong class="update">Update</strong> this %s', MASTERPRESS_DOMAIN ), $object->taxonomy()->singular_label());
    } else if ($type_key == "user") {
      $data["save_nag_message"] = __('To Save changes you must also <strong class="update">Update</strong> this user', MASTERPRESS_DOMAIN);
    }
    
    if (isset($data["save_nag_message"])) {
      $data["save_nag_tooltip"] = strip_tags($data["save_nag_message"]);
    }
    
    // loop through the fields, and output their model ids, so that the save action picks them up

    $data["fields"] = array();
    $data["field_icons"] = array();
    
    foreach ($set->fields() as $field) :
    
      if ($field->current_user_can_see()) {
        
        $field_data = array();
      
        $field_data["field_id"] = $field->id;
		
        $field_data["field_model_name"] = "mp_meta_model[{$set->name}][{$field->name}]";

        if (!is_null($field->icon) && $field->icon != "") {
          $data["field_icons"][] = array("url_2x" => MPU::field_icon_2x_url($field->icon), "url" => MPU::field_icon_url($field->icon), "field_path" => $set->name."-".$field->name);
        }
      
        $field_data["field_path"] = $set->name."-".$field->name;
      
        if (isset($field->labels["tooltip_help"])) {
          $tooltip_help = stripslashes(trim($field->label("tooltip_help")));
      
          if ($tooltip_help != "") {
            $field_data["tooltip_help"] = $tooltip_help;
          } 

        }
      
        $data["fields"][] = $field_data;
      
      }
    
    endforeach;

    // need to add creator support here!
    
    if ($set->creator) {
      
      $set_item = new MEOW_FieldSetCreator($set->name, $object, $set);
      
      $template_data = self::set_item_template_data($set_item, $set_args);
      $data["set_items"] = WOOF::render_template( self::set_item_template(), $template_data, false );

      
    } else {
      
      if ($set->allow_multiple) {

        $data["set_items"] = "";
      
        foreach ($set_items as $set_item) { 
          // grab the data for the set template
          $template_data = self::set_item_template_data($set_item, $set_args);
          $data["set_items"] .= WOOF::render_template( self::set_item_template(), $template_data, false );
        } 
    
        // update the set_items (if dirty)
        $set_items->update();
    
      
      } else {
      
        $set_item = new MEOW_FieldSet($set->name, 1, $object, $set, $set_items);
        // grab the data for the set template
        $template_data = self::set_item_template_data($set_item, $set_args);
        $data["set_items"] = WOOF::render_template( self::set_item_template(), $template_data, false );
      
      }
    
    }
    
    return WOOF::render_template( self::set_template(), $data, false );
    
  }
  
  public static function detail_post_type($post, $post_type) {
    $detail_posts = $post->posts("post_type=" . $post_type->name);
    
    $data = array();
      
    $set_head_classes = array();
      
    if (!count($detail_posts)) {
      $set_head_classes[] = "hidden";  
    }
    
    $data["set_head_classes"] = implode(" ", $set_head_classes);
    
    $data["set_items_count"] = MPU::__items($detail_posts->count(), $post_type->label("one_post"), $post_type->label("n_posts"), $post_type->label("no_posts"));

    echo WOOF::render_template( self::detail_post_template(), $data );

    
  } 
  
  public static function detail_post_template() {
    
    $remove_checked_items = __("Remove Checked Items", MASTERPRESS_DOMAIN);
    $expand_all = __("Expand All", MASTERPRESS_DOMAIN);
    $check_all_items = __("Check All Items", MASTERPRESS_DOMAIN);
    $collapse_all = __("Collapse All", MASTERPRESS_DOMAIN);

    return <<<HTML

    <div data-add_item_template="{{add_item_template}}" data-model_id="{{set_id}}" class="mpv mp-set mp-set-{{set_id}} {{set_classes}} { name: '{{set_name}}', lang: { add_another: '{{label_add_another}}', item: '{{label_one_item}}', items: '{{label_n_items}}', no_items: '{{label_no_items}}', click_to_add: '{{label_click_to_add}}', remove_confirm: '{{label_remove_confirm}}' } }">

    <input type="hidden" name="{{set_dirty_name}}" autocomplete="off" class="dirty" value="true" disabled="disabled" />
    <input type="hidden" name="{{set_versions_name}}" autocomplete="off" class="versions" value="{{set_versions}}" />

    {{#save_nag_message}}<div class="mp-metabox-message save-nag with-mptt" data-tooltip="{{save_nag_tooltip}}"><p class="warning"><i class="warning-octagon"></i><span>{{{save_nag_message}}}</span></p></div>{{/save_nag_message}}

    <div class="mp-set-inner">

    {{#set_description}}
    <div class="mp-set-description"><p>{{set_description}}</p></div>
    {{/set_description}}

    <div class="mp-set-head {{set_head_classes}}"  title="{{add_item_title}}">

    <ul class="mp-controls lt">
      <li class="li-add-set-item"><button type="button" class="text add-set-item" title="{{add_item_title}}"><i></i><span class="set-item-count">{{{set_items_count}}}</span></button></li>
    </ul>

    <ul class="mp-controls mp-set-controls rt">
    <li class="control-expand-all"><button type="button" class="icon ir expand-all {{#set_expanded}}disabled{{/set_expanded}}" {{#set_expanded}}disabled="disabled"{{/set_expanded}} title="{$expand_all}">{$expand_all}</button></li>
    <li class="control-remove-checked"><button type="button" class="icon ir remove-checked disabled" title="{$remove_checked_items}" disabled="disabled">{$remove_checked_items}</button></li>
    <li title="{$check_all_items}" class="control-check-all"><input type="checkbox" class="checkbox check-all" /></li>
    <li class="control-collapse-all"><button type="button" class="icon ir collapse-all {{^set_expanded}}disabled{{/set_expanded}}" {{#set_expanded}}disabled="disabled"{{/set_expanded}} title="{$collapse_all}">{$collapse_all}</button></li>
    </ul>

    </div>


    {{#fields}}
      <input name="mp_meta_field_ids[]" type="hidden" value="{{field_id}}" />
      <input name="{{field_model_name}}" type="hidden" value="{{field_id}}" />
      {{#tooltip_help}}
      <div id="mptt_{{set_id}}_{{field_id}}" class="mp-field-label-tooltip">
        {{{tooltip_help}}}
      </div>
      {{/tooltip_help}}
    {{/fields}}

    <div class="mp-set-items">{{{set_items}}}</div>

    <div class="mp-set-foot" title="{{add_item_title}}">

      <ul class="mp-controls lt">
        <li class="li-add-set-item last"><button type="button" class="text add-set-item" title="{{add_item_title}}"><i></i><span class="set-item-count">{{{set_items_count}}}</span></button></li>
      </ul>
    </div>


    </div>
    </div>

HTML;
    
    
  }
  
  
  
  public static function set($object, $set) {
    $meow_set = $object->set($set->name);
    echo self::get_set($object, $meow_set);
  }
  
  public static function field_ui_template_data(MEOW_FieldSet $set, MEOW_Field $field, $args = array()) {

    $r = wp_parse_args( $args,
      array("preview" => false, "readonly" => false, "preview_prefix" => "preview_", "id_base" => "mp_meta_", "name_base" => "mp_meta", "prop_name_base" => "mp_meta_prop", "prop_id_base" => "mp_meta_prop_" )
    );
    
    $set_info = $set->info();
    $field_info = $field->info();
    $ftc = MPFT::type_class($field_info->type);

    $i = $field->set_index();
    
    if (WOOF::is_true_arg($r, "creator")) {
      $i = "!!set_index!!";
    } else if ($field->creator) {
      // leave a placeholder for Handlebars to render into
      $i = "{{set_index}}";
    } 
    
    $prefix = "";
    
    if ($r["preview"]) {
      $prefix = $r["preview_prefix"];
    }
    
    $set_id = $prefix.$r["id_base"]."{$set_info->name}_$i"; 
    $set_name = $prefix.$r["name_base"]."[{$set_info->name}][$i]"; 

    if (WOOF::is_true_arg($r, "nested")) {
      $field_id = $prefix."{$set_id}_{$field_info->name}";
      $field_name = $prefix.$r["name_base"]."[{$i}][{$field_info->name}]";
      $field_prop_name = $prefix.$r["prop_name_base"]."[{$i}][{$field_info->name}]";
      $field_prop_id = $prefix.$r["prop_id_base"]."{$set_info->name}_{$i}_{$field_info->name}";
    
    } else {
      
      $field_id = $prefix."{$set_id}_{$field_info->name}";
      $field_name = $prefix."{$set_name}[{$field_info->name}]";
      $field_prop_name = $prefix.$r["prop_name_base"]."[{$set_info->name}][$i][{$field_info->name}]";
      $field_prop_id = $prefix.$r["prop_id_base"]."{$set_info->name}_{$i}_{$field_info->name}";
    }
    
    
    // prepare the data to return
    $d = array();
    
    $strip_whitespace = !call_user_func( array($ftc, "ui_preserve_whitespace") );
    
    if ($r["preview"]) {
      $field->_version_preview = true;
    }
  
    $d["type"] = $field_info->type;
    $d["ui"] = WOOF::render_template( call_user_func_array( array($ftc, "ui"), array($field)), array("id" => $field_id, "name" => $field_name, "prop_id" => $field_prop_id, "prop_name" => $field_prop_name), $strip_whitespace );
    
    return $d;
        
  }
  
  public static function set_item_template_data(MEOW_FieldSet &$set, $args = array()) {
    
    $r = wp_parse_args(
      $args,
      array("preview" => false, "readonly" => !$set->is_editable(), "id_base" => "mp_meta_", "name_base" => "mp_meta", "order_base" => "mp_meta_order", "summary_preview_base" => "mp_summary_preview_", "summary_base" => "mp_summary_" )
    );
    
    $d = array();
     
    $set_item_classes = array();
    $set_fields_classes = array();
    $set_summary_classes = array();
    
    $d["allow_remove"] = true;
    
    if ($r["preview"]) {
      $d["preview"] = true;
      $d["allow_remove"] = false;
    } 
    
    if ($r["readonly"]) {
      $d["readonly"] = true;
      $d["allow_remove"] = false;
    }

    
    $info = $set->info();
    
    if ($info->expanded) {
      $d["toggle_class"] = "collapse";
      $set_item_classes[] = "expanded";
      $set_summary_classes[] = "hidden";
    } else {
      $d["toggle_class"] = "expand";
      $set_item_classes[] = "collapsed";
      $set_fields_classes[] = "hidden";
    }
    
    if (isset($args["versions_select"])) {
      $d["versions_select"] = $args["versions_select"];
    }
    
    $d["lang_are_you_sure"] = esc_js(__("Are you sure?", MASTERPRESS_DOMAIN));

    $d["label_collapse"] = __("Collapse", MASTERPRESS_DOMAIN);
    

    $d["set_item_classes"] = implode(" ", $set_item_classes);
    $d["set_fields_classes"] = implode(" ", $set_fields_classes);
    
    $d["description"] = trim( $info->label("description") );

    $d["allow_multiple"] = $info->allow_multiple;

    $d["labels_toggle"] = __("Toggle", MASTERPRESS_DOMAIN);
    
    $d["set_labels_remove_plain"] = strip_tags($info->label("remove"));
    $d["set_labels_remove"] = $info->label("remove");
        
    $d["fields"] = array();

    if ( WOOF::is_true_arg($args, "creator") ) {
      $i = "!!set_index!!";
    } else if ($set->creator) {
      // leave a placeholder for Handlebars to render into
      $i = "{{set_index}}";
    } else { 
      $i = $set->index();
    }
    
    
    
    $d["set_index"] = $i;
    
    $set_id = $r["id_base"]."{$info->name}_$i"; 
    $set_name = $r["name_base"]."[{$info->name}][$i]"; 
    
    if (WOOF::is_true_arg($r, "nested")) {
      $d["order_name"] = $r["order_base"]."[$i]"; 
    } else {
      $d["order_name"] = $r["order_base"]."[{$info->name}][$i]"; 
    }
  
    
    
    $d["fields"] = array();
    
    $fields = $info->fields(); 
    $fi = 0; 
    $fc = 0;
    
    foreach ($fields as $field) {
      if ($type_class = MPFT::type_class($field->type)) {
        $fc++;
      }
    }
    
    foreach ($fields as $field) {

      if ($field->current_user_can_see()) {

        $field_id = "{$set_id}_{$field->name}";
        $field_name = "{$set_name}[{$field->name}]";

        if ($r["preview"]) {
          $field_summary_id = $r["summary_preview_base"]."{$info->name}_{$field->name}";
        } else {
          $field_summary_id = $r["summary_base"]."{$info->name}_{$field->name}";
        }
      
      
        $fd = array(); // the field data

        if ($ftc = MPFT::type_class($field->type)) {
          $fi++; 

          $fd["model_id"] = $field->id;
          $fd["readonly"] = false; // reset
    
          $fd["label_is_header"] = call_user_func_array( array($ftc, "label_is_header"), array($field->type_options));
          $fd["type"] = $field->type;
          $fd["type_widget"] = "mpft_".str_replace("-", "_", $field->type);
          $fd["label"] = $field->display_label();
          $fd["esc_label"] = esc_attr($field->display_label());
          $fd["label_suffix"] = call_user_func( array($ftc, "label_suffix") );
        
          $fd["field_path"] = $info->name."-".$field->name;

          $fd["prop_list"] = implode(",", MPFT::type_properties($field->type));
        
          $fd["description"] = trim( $field->label("description") );

          $fd["pos_class"] = WOOF_HTML::pos_class_1($fi, $fc, " mp-field-");

          if ($field->current_user_can_manage()) {
            $mu = $field->manage_url($info);
            
            $mu .= "&mp_redirect=".urlencode($_SERVER["REQUEST_URI"]);
            
            if ($mu) {
              $fd["go"] = '<a href="'.$mu.'" class="mp-go with-mptt" data-tooltip="'.__("Edit Field Definition", MASTERPRESS_DOMAIN).'">'.__("Edit Field Definition").'</a>';
            }


          }
          
          $field_classes = array("mpft-".$field->type);
        
          if ($fi == $fc) {
            $field_classes[] = "mp-field-last";
          }
        
          $field_summary_classes = array();
        
          if ($field->required) {
            $field_classes[] = "required";
            $field_summary_classes[] = "required";
          }
        
          if (isset($field->summary_options["emphasise"])) {
            $field_summary_classes[] = "em";
          }
        
          $fd["name"] = $field->name;
          $fd["template_id"] = "{$info->name}_{$field->name}";
          $fd["summary_id"] = $field_summary_id."_".$i;
          $fd["field_id"] = $field_id;


        
          $fd["id_first"] = $field_id;
        
          $opts = call_user_func( array($ftc, "ui_options") );
        
          $ui = array();
        
          if (count($opts) ) {
            $ui = call_user_func_array( array($ftc, "extract_options"), array($field->type_options, $opts) );
            $ui = call_user_func_array( array($ftc, "normalize_options"), array($ui) );
          }
        
        
        
          $ui_parts = array();
        
          if (count($ui)) {
            foreach( $ui as $key => $value ) {
              $ui_parts[] = "'$key':'".esc_attr(esc_js($value))."'"; 
            } 
          }
        
          // $fd["lang"] = "{".implode(",", $lang_parts)."}";

          $fd["ui"] = "{".implode(",", $ui_parts)."}";
        
          if (isset($field->labels["tooltip_help"])) {
            $tooltip_help = trim($field->label("tooltip_help"));

            if ($tooltip_help != "") {
              $fd["label_tooltip"] = "#mptt_".$info->id."_".$field->id;
            }
          }

          if (!$field->current_user_can_edit($set->is_editable()) && !$r["preview"]) {
            $fd["readonly"] = true;
          }
        
          $strip_whitespace = !call_user_func( array($ftc, "ui_preserve_whitespace") );
        
          $ftd_args = $r;
          
          if ($set->creator) {
            
            $meow_field = new MEOW_FieldCreator($field->name, null, $field);
          
            call_user_func_array( array($ftc, "apply_default"), array($meow_field, $set, $ftc) );
            $field_ui_data = self::field_ui_template_data($set, $meow_field, $ftd_args);
            $field_ui = WOOF::render_template( self::field_ui_template(), $field_ui_data, $strip_whitespace );
          
          } else {

            // here we regard the actual field value, and build the UI from that
            $meow_field = $set->field($field->name); 

            $action = "";
          
            if (isset($_GET["action"])) {
              $action = $_GET["action"];
            }

            if (!$info->allow_multiple && $action != "edit") {
              call_user_func_array( array($ftc, "apply_default"), array($meow_field, $set, $ftc) );
            }

            $field_ui_data = self::field_ui_template_data($set, $meow_field, $ftd_args);
            $field_ui = WOOF::render_template( self::field_ui_template(), $field_ui_data, $strip_whitespace );
        
          }

          
          if ($fi == $fc) {
            if ($meow_field->blank()) {
              $set_summary_classes[] = "last-empty";
            }
        
          }
          
          $fd["field_ui"] = $field_ui;
        
        
          // now build out the summary info
        
        
          $fd["summary_width"] = call_user_func( array($ftc, "summary_width") );
        
          if (isset($field->summary_options["width"])) {
            $sw = (int) $field->summary_options["width"];
          
            if ($sw >= 1 && $sw <= 5) {
              $fd["summary_width"] = $sw;
            }
          
          }
        
        
          $max_length = ( $fd["summary_width"] * 138 ) / 10;
        
          $fd["label_truncated"] = WOOF::truncate_basic( $fd["label"], $max_length, "<span>&hellip;</span>" );
      
          if ($fd["label"] != $fd["label_truncated"]) {
            $fd["label_title"] = strip_tags($fd["label"]);
          }
        
          $label_classes = array();
        
          if (method_exists($ftc, "summary_label_classes")) {
            $label_classes = call_user_func_array( array($ftc, "summary_label_classes"), array($meow_field) );
          } 
          
          $fd["label_classes"] = implode(" ", $label_classes);
        
          $empty = $meow_field->blank();


          $fd["empty_summary"] = call_user_func_array( array($ftc, "empty_summary"), array($meow_field) );
          $fd["summary"] = call_user_func_array( array($ftc, "summary"), array($meow_field) );

          if ($empty) {
            $field_summary_classes[] = "empty";
          } else {
            $fd["is_edit"] = "is-edit";
          }
        
          $fd["classes"] = implode(" ", $field_classes);
          $fd["summary_classes"] = implode(" ", $field_summary_classes);
        
          // add the field data to the main data array
          $d["fields"][] = $fd;  
      
      
        } // endif class_exists($ftc)
    
      } // current user can see
    
    } // endforeach
      
    
    $d["set_summary_classes"] = implode(" ", $set_summary_classes);


    return $d;
    
  }
  
  
  public static function field_ui_template() {
    
    return <<<HTML
      <div class="mp-field-ui mpft-{{type}}-ui clearfix">{{{ui}}}</div>
HTML;
  
  }

  public static function set_item_template() {
 
    $copy_paste = __("Copy / Paste Content", MASTERPRESS_DOMAIN);
    $versions = __("Versions", MASTERPRESS_DOMAIN);
    
    $cannot_edit = __("You do not have permission to edit this field");

    return <<<HTML
<div class="mp-set-item mp-set-item-{{set_index}} {{set_item_classes}}">

    <input name="{{order_name}}" type="hidden" value="{{set_index}}" class="set-order" />
    <input name="set_index" type="hidden" value="{{set_index}}" class="set-index" />
    <input name="original_set_index" type="hidden" value="{{set_index}}" class="original-set-index" />

    <ul class="mp-controls mp-set-controls">
      <li class="control-toggle"><button type="button" class="icon ir left toggle {{toggle_class}}"><span>{{labels_toggle}}</span></button></li>
      {{^allow_multiple}}
      {{#versions_select}}
      <li class="control-versions"><button type="button" class="icon ir versions" title="{$versions}">{$versions}</button></li>
      {{/versions_select}}
      {{^preview}}
      <li class="control-copy-paste"><button type="button" class="icon ir copy-paste" title="{{copy_paste_title}}">{$copy_paste}</button></li>
      {{/preview}}
      
      {{/allow_multiple}}
      {{#allow_multiple}}
      
      {{#allow_remove}}
      <li class="control-remove"><button type="button" class="icon ir left remove-set-item" title="{{set_labels_remove_plain}}"><span>{{{set_labels_remove}}}</span></button></li>
      {{/allow_remove}}
      {{^readonly}}
      <li class="control-check"><input type="checkbox" class="checkbox" value="true" name="mp_selection" /></li>
      {{/readonly}}
      <li class="control-set-index"><div class="set-index"><span>{{set_index}}</span></div></li>
      {{/allow_multiple}}
    </ul>

    <div class="mp-set-summary {{set_summary_classes}}">

    <div class="fade"></div>

    <ul>
    {{#fields}}
    <li id="{{summary_id}}" class="mp-field-summary mpft-{{type}}-summary mp-field-summary-{{field_path}} span-{{summary_width}} {{summary_classes}} { mp_field_id: 'mp-field-{{field_id}}' }">
      <h4 title="{{label_title}}" class="summary-label"><i class="{{label_classes}}"></i>{{{label_truncated}}}</h4>
      <div class="summary value-summary">{{{summary}}}</div>
      <div class="summary empty-summary">{{{empty_summary}}}</div>
    </li>
    {{/fields}}
    </ul>
    </div>
    
    <div class="mp-set-fields {{set_fields_classes}}">
      
      <div class="mp-item-divider"></div>
      <div class="mp-item-divider-mask"></div>
      {{#fields}}      
      <div data-prop="{{prop_list}}" data-name="{{name}}" id="mp-field-{{field_id}}" class="mp-field mp-field-{{type}} {{is_edit}} {{pos_class}} {{classes}} { model_id: {{model_id}}, {{#summary_width}}summary_width: {{summary_width}},{{/summary_width}} summary_id: '#{{summary_id}}', type: '{{type}}', type_widget: '{{type_widget}}', name: '{{name}}', template_id: '{{template_id}}', ui: {{ui}} }">
        
        <div class="mp-field-title">
        {{#label_is_header}}<h4 class="mp-field-label" data-label="{{esc_label}}">{{/label_is_header}}{{^label_is_header}}<label for="{{id_first}}" class="mp-field-label" data-label="{{esc_label}}">{{/label_is_header}}
        {{label}}{{label_suffix}}{{#description}}<span class="description"> (&nbsp;{{description}}&nbsp;)</span>{{/description}}
        {{#label_is_header}}</h4>{{/label_is_header}}{{^label_is_header}}</label>{{/label_is_header}}
        {{#readonly}}<a class="with-mptt mp-lock-icon" data-tooltip="$cannot_edit" href="#mp-tt-readonly">Read Only</a>{{/readonly}}
        {{#label_tooltip}}<a class="mp-tooltip-icon" href="{{label_tooltip}}">More Info</a>{{/label_tooltip}}
        {{{go}}}
        </div>
      
        {{{field_ui}}}

      </div>

      {{/fields}}

      <button class="ir collapse-lower" type="button" title="{{label_collapse}}"><strong><i></i>{{label_collapse}}</strong><span class="mask"></span></button>
    </div>
    <!-- /.mp-set-fields -->

    
  </div>
HTML;
  
  }
  
  public static function set_template() {
  
  $remove_checked_items = __("Remove Checked Items", MASTERPRESS_DOMAIN);
  $expand_all = __("Expand All", MASTERPRESS_DOMAIN);
  $check_all_items = __("Check All Items", MASTERPRESS_DOMAIN);
  $collapse_all = __("Collapse All", MASTERPRESS_DOMAIN);
  $copy_paste = __("Copy / Paste Content", MASTERPRESS_DOMAIN);

  $copy = __("Copy", MASTERPRESS_DOMAIN);
  $paste = __("Paste", MASTERPRESS_DOMAIN);

  $versions = __("Versions", MASTERPRESS_DOMAIN);

  $version_select_title = __("Version - <span>select a version to review content</span>", MASTERPRESS_DOMAIN);
  $version_content_title = __("Content - <span>restore all fields, or copy content out of specific fields manually</span>", MASTERPRESS_DOMAIN);
  $version_content_multiple_title = __("Content - <span>check the items you wish to restore, or copy content out of specific fields manually</span>", MASTERPRESS_DOMAIN);
  
  $versions_loading = __("fetching content", MASTERPRESS_DOMAIN);  

  $version_no_data = __("The selected version contains no content", MASTERPRESS_DOMAIN);

  $restore_button = __("Restore", MASTERPRESS_DOMAIN);
  $copy_label = __("Copy the content snippet below to the clipboard", MASTERPRESS_DOMAIN);
  $paste_label = __("Paste a copied content snippet into the text field below, and click OK", MASTERPRESS_DOMAIN);
  
  
  return <<<HTML
    
    {{#set_icon}}
    <style type="text/css">
    #poststuff #field-set-{{set_name_sanitized}} h3 em, #your-profile #field-set-{{set_name_sanitized}} h3 em, #createuser #field-set-{{set_name}} h3 em, #edittag #field-set-{{set_name_sanitized}} h3 em, #site-content #field-set-{{set_name_sanitized}} h3 em { padding-left: 25px; background-image: url({{set_icon}}); }
    </style>
    {{/set_icon}}

    {{#set_icon_2x}}
    <style type="text/css">
    @media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min--moz-device-pixel-ratio: 1.5), only screen and (min-device-pixel-ratio: 1.5) { #field-set-{{set_name_sanitized}} h3 em, #your-profile #field-set-{{set_name_sanitized}} h3 em, #createuser #field-set-{{set_name}} h3 em, #edittag #field-set-{{set_name_sanitized}} h3 em, #site-content #field-set-{{set_name_sanitized}} h3 em { padding-left: 25px; background-size: 16px 16px !important; background-image: url({{set_icon_2x}}) !important; } }
    </style>
    {{/set_icon_2x}}

    <style type="text/css">
    {{#field_icons}}
    .mp-set-summary .mp-field-summary-{{field_path}} h4 i { background-image: url({{url}}) !important; }
    @media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min--moz-device-pixel-ratio: 1.5), only screen and (min-device-pixel-ratio: 1.5) { .mp-set-summary .mp-field-summary-{{field_path}} h4 i { background-image: url({{url_2x}}) !important; } }

    {{/field_icons}}
    </style>
  
    <div data-add_item_template="{{add_item_template}}" data-model_id="{{set_id}}" class="mpv mp-set mp-set-{{set_id}} {{set_classes}} { name: '{{set_name}}', lang: { add_another: '{{label_add_another}}', item: '{{label_one_item}}', items: '{{label_n_items}}', no_items: '{{label_no_items}}', click_to_add: '{{label_click_to_add}}', remove_confirm: '{{label_remove_confirm}}' } }">
    
    <input type="hidden" name="{{set_dirty_name}}" autocomplete="off" class="dirty" value="true" disabled="disabled" />
    <input type="hidden" name="{{set_versions_name}}" autocomplete="off" class="versions" value="{{set_versions}}" />
    
    {{#save_nag_message}}<div class="mp-metabox-message save-nag with-mptt" data-tooltip="{{save_nag_tooltip}}"><p class="warning"><i class="warning-octagon"></i><span>{{{save_nag_message}}}</span></p></div>{{/save_nag_message}}
  
    <div class="mp-set-inner">
    
    {{#set_description}}
    <div class="mp-set-description"><p>{{set_description}}</p></div>
    {{/set_description}}

    {{#set_allow_multiple}}
    <div class="mp-set-head {{set_head_classes}}"  title="{{add_item_title}}">

    <ul class="mp-controls lt">
      {{#set_allow_add}}
      <li class="li-add-set-item"><button type="button" class="text add-set-item" title="{{add_item_title}}"><i></i><span class="set-item-count">{{{set_items_count}}}</span></button></li>
      {{/set_allow_add}}
      {{^set_allow_add}}
      <li class="control-set-item-count"><span class="set-item-count">{{{set_items_count}}}</span></li>
      {{/set_allow_add}}
    </ul>
    
    
      
    <ul class="mp-controls mp-set-controls rt">
    {{#versions_select}}
    <li class="control-versions"><button type="button" class="icon ir versions" title="{$versions}">{$versions}</button></li>
    {{/versions_select}}
    <li class="control-copy-paste"><button type="button" class="icon ir copy-paste" title="{{copy_paste_title}}">{$copy_paste}</button></li>
    <li class="control-expand-all"><button type="button" class="icon ir expand-all {{#set_expanded}}disabled{{/set_expanded}}" {{#set_expanded}}disabled="disabled"{{/set_expanded}} title="{$expand_all}">{$expand_all}</button></li>
    {{#allow_remove}}
    <li class="control-remove-checked"><button type="button" class="icon ir remove-checked disabled" title="{$remove_checked_items}" disabled="disabled">{$remove_checked_items}</button></li>
    {{/allow_remove}}
    {{^readonly}}
    <li title="{$check_all_items}" class="control-check-all"><input type="checkbox" class="checkbox check-all" /></li>
    {{/readonly}}
    <li class="control-collapse-all"><button type="button" class="icon ir collapse-all {{^set_expanded}}disabled{{/set_expanded}}" {{#set_expanded}}disabled="disabled"{{/set_expanded}} title="{$collapse_all}">{$collapse_all}</button></li>
    </ul>
      
    </div>
    {{/set_allow_multiple}}

    {{#versions_select}}
    <div data-title="{{versions_title}}" class="mpv mp-set-versions">

      <div class="version-select">
        
        <h3><i class="metabox-versions"></i>$version_select_title</h3>
          
        <div class="f-versions f clearfix">
          <div class="fw">
            {{{versions_select}}}
            <span class="loading">$versions_loading</span>
            <div class="version-no-data">$version_no_data</div>
          </div>
          <!-- /.fw -->
          
          <button type="button" class="restore simple-primary">$restore_button</button>
          
        </div>

      </div>

      <div class="version-content" style="display: none;">
        {{#set_allow_multiple}}
        <h3><i class="metabox-image-text"></i>$version_content_multiple_title</h3>
        {{/set_allow_multiple}}
        {{^set_allow_multiple}}
        <h3><i class="metabox-image-text"></i>$version_content_title</h3>
        {{/set_allow_multiple}}

        <div class="version-preview">
        
        </div>
        
      </div>
      <!-- /.version-content -->
      
    </div>
    {{/versions_select}}

    
    <div data-title="{{copy_paste_title}}" class="mpv mp-set-copy-paste">

      <ul class="fs-tabs">
        <li><a href="#copy-content-{{set_id}}" class="current"><span><i class="copy"></i>$copy</span></a></li>
        {{^readonly}}
        <li><a href="#paste-content-{{set_id}}"><span><i class="paste"></i>$paste</span></a></li>
        {{/readonly}}
      </ul>
      
      <div id="copy-content-{{set_id}}" class="copy-content tab-panel current">

        <label for="copy-rep-{{set_id}}">$copy_label</label> 
        <textarea id="copy-rep-{{set_id}}" class="mono copy-rep"></textarea>

      </div>

      {{^readonly}}
      <div id="paste-content-{{set_id}}" class="paste-content tab-panel">

        <label for="paste-rep-{{set_id}}">$paste_label</label> 
        <textarea id="paste-rep-{{set_id}}" class="mono paste-rep"></textarea>

      </div>
      {{/readonly}}
      
      
    </div>

    {{#fields}}
      <input name="mp_meta_field_ids[]" type="hidden" value="{{field_id}}" />
      <input name="{{field_model_name}}" type="hidden" value="{{field_id}}" />
      {{#tooltip_help}}
      <div id="mptt_{{set_id}}_{{field_id}}" class="mp-field-label-tooltip">
        {{{tooltip_help}}}
      </div>
      {{/tooltip_help}}
    {{/fields}}
    
    {{#set_allow_multiple}}
    <div class="mp-set-items">{{{set_items}}}</div>
    {{/set_allow_multiple}}

    {{^set_allow_multiple}}
    {{{set_items}}}
    {{/set_allow_multiple}}

    {{#set_allow_multiple}}
    <div class="mp-set-foot" title="{{add_item_title}}">

      <ul class="mp-controls lt">
        {{#set_allow_add}}
        <li class="li-add-set-item last"><button type="button" class="text add-set-item" title="{{add_item_title}}"><i></i><span class="set-item-count">{{{set_items_count}}}</span></button></li>
        {{/set_allow_add}}
        {{^set_allow_add}}
        <li class="control-set-item-count"><span class="set-item-count">{{{set_items_count}}}</span></li>
        {{/set_allow_add}}
      </ul>
      <ul class="mp-controls mp-set-controls rt" style="{{set_foot_controls_style}}">
        {{#versions_select}}
        <li class="control-versions"><button type="button" class="icon ir versions" title="{$versions}">{$versions}</button></li>
        {{/versions_select}}

        {{^readonly}}
        <li class="control-copy-paste"><button type="button" class="icon ir copy-paste" title="{{copy_paste_title}}">{$copy_paste}</button></li>
        {{/readonly}}
      </ul>
    </div>
    {{/set_allow_multiple}}


    </div>
    </div>

HTML;
  
  }
  

  
  
  

}
