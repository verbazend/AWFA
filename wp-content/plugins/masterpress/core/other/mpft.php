<?php


class MPFT extends WOOF_Wrap {
  
  // The field type class will not be responsible for loading data. 
  // Data related to this field will be supplied to it by other objects in MasterPress
  
  protected $info;
  protected $field;
  protected $field_set;
  protected $field_set_collection;
  
  
  protected static $type_classes = array();
  protected static $type_properties = array();
  
  protected static $core_type_keys;
  protected static $extension_type_keys;
  protected static $type_keys;

  protected static $color_icon;
  protected static $gray_icon;

  function __construct( $info, $field ) {
    $this->info = $info;
    $this->field = $field;
  } 

  public function update() {
    if ($field = $this->field()) {
      $field->update();
    }
  }

  public function field() {
    return $this->field;
  }

  public function set_value($value) {
    if (isset($this->field)) {
      $this->field->set_value($value);
    }
  }
  
  public function data($data = NULL) {
    if (isset($this->field)) {
      return $this->field->data;
    }
    
    // this case shouldn't generally happen, but is an absolute fallback
      
    return (object) array("val" => "", "__blank" => true, "prop" => array());
  }
  
  public function blank() {
    
    $data = $this->data();
    
    if (isset($data->__blank)) {
      return $data->__blank;
    }
    
    return true;
  }
  
  public function valid() {
    return true;
  }
  
  public static function enqueue() {
    
  }

  public function prop_val($name) {
    $data = $this->data();
    
    if (isset($data->prop[$name])) {
      return $data->prop[$name];
    }
  
    return new WOOF_Silent( sprintf( __("There is no property named %s", MASTERPRESS_DOMAIN), $name ) );
  }
  
  public function prop($name = null) {
    if (is_null($name)) {
      return $this->field->prop;
    }
    
    return $this->field->prop($name);
  }
  
  public static function option_value($options, $key, $fallback = "") {
    if (isset($options) && isset($options[$key]) && $options[$key] != "") {
      return $options[$key];
    }
    
    return $fallback;
  }
  
  public static function type_class($type) {
    // gets the class associated with a type key AND includes the class

    if (!$type) {
      return false;
    }
    
    if (!isset(self::$type_classes[$type])) {
      
      self::$type_classes[$type] = false;
    
      if (MPU::incl_type($type)) {
        // check if the class itself exists
        $type_class = MPU::type_class($type);
        
        if (class_exists($type_class)) {
          self::$type_classes[$type] = $type_class;
        }

      } 
      
    }
      
    return self::$type_classes[$type];

  }
  
  public static function parse_meta_key($key) {
    // infer the field and set name from the meta data
	        
    // here, we'll make this nice and simple, and assume that the meta key structure is as follows:
  
    // set_name.sub_set_name.field_name:property_name
  
    // (nested sets will be introduced in a later version)
  
    // split into set/field properties
  
    $sfp = explode(":", $key);

    $sf = $sfp[0];
  
    $property_name = false;
    
    if (count($sfp) > 1) {
      $property_name = $sfp[1];
    }

    // determine the sets and fields
  
    $sfp_parts = explode(".", $sf);
  
    $set_name = array_shift($sfp_parts);
    $field_name = array_pop($sfp_parts);
    
    return array($set_name, $field_name, $property_name);
  
  }
  
  public static function meta_key($set_name, $field_name, $property_name = "") {
    
    $ret = $set_name.".".$field_name;
    
    if ($property_name != "") {
      $ret .= ":".$property_name;
    }
    
    return $ret;
    
  }
  
  
  public static function type_properties($type) {

    if (!$type) {
      return false;
    }
    
    if (!isset(self::$type_properties[$type])) {
      
      $prop_keys = array("field_id");
      
      if ($type_class = MPFT::type_class($type)) {
        
        $ui_prop = call_user_func( array($type_class, "ui_prop") );
        
        if (!is_array($ui_prop)) {
          $ui_prop = explode(",", $ui_prop);
        }
        
        $prop_keys = wp_parse_args( $ui_prop, $prop_keys);
      }

      self::$type_properties[$type] = $prop_keys;
      
    }
      
    return self::$type_properties[$type];
    
  }
  
  
  public static function enqueue_style($class, $name, $url) {
    $key = self::type_key($class);
    wp_enqueue_style($name, MPU::type_file_url($key, $url));
  }
  
  // -- MasterPress Utility Methods

  public static function __description() {
    return "";
  }

  public static function __category() {
    return "Other"; // fallback category
  }
  
  public static function summary_width() {
    return 1;
  }

  public static function apply_default(MEOW_Field $field, MEOW_FieldSet $set, $field_type_class) {
    
    if ($field->blank()) {
      
      if (isset($field->info->type_options["default_value"])) {
        $default_value = $field->info->type_options["default_value"];
      } 
    
      // apply default value filters
      
      $field->info->field_set();

      $info = $set->info();
      
      $set_name = $info->name;
      $field_name = $field->info->name;
      
      
      if ($info->type == "s") {
        
        $filter = "shared";
        
      } else if ($info->type == "p") {

        $filter = $info->vis("post_types");

      } else if ($info->type == "x") {
        
        $filter = $info->vis("taxonomies");
        
      } else if ($info->type == "t") {
        
        $filter = $info->vis("templates");
        
      } else {
        
        $filter = "site";
        
      }
      
      
      $any_filter_key = "mp_field_default";
      
      $filter_key = "mp_default_{$filter}_{$set_name}_{$field_name}";
      $field_filter_key = "mp_default_{$set_name}_{$field_name}";

      $set_any_filter_key = "mp_default_{$set_name}";
      $set_filter_key = "mp_default_{$filter}_{$set_name}";
      

      if (!isset($default_value)) {
        $default_value = null;
      }
      
      $default_value = apply_filters( $filter_key, $default_value );
      $default_value = apply_filters( $field_filter_key, $default_value );
      $default_value = apply_filters( $set_filter_key, $default_value, $field_name );
      $default_value = apply_filters( $set_any_filter_key, $default_value, $field_name );
      
      // parse value using value_for_set
      
      if (!is_null($default_value)) {
        $default_value = $field->value_for_set($default_value);
      }
    
      $fill = false;
      
      if (!is_null($default_value)) {
        

        if (is_array($default_value)) {
        
          if (count($default_value)) {
            $fill = true;
          }
        
        } else {

          if (trim($default_value) != "") {
            $fill = true;
          }
        
        }
        
      }
      
      if ($fill) {
        $field->data( (object) array("prop" => array(), "val" => $default_value, "__blank" => false, "__default" => true) );
      }
      
    }
    
    
  }

  public static function format_date($val, $format = "d M Y") {
    
    if ($val && $val != "") {
      $date = strtotime($val);
      return date($format, $date); 
    }
    
    return "";
  }
  
  public static function prop_input($field, $key) {

    $attr = array(
      "id" => "{{prop_id}}-".$key,
      "name" => "{{prop_name}}[".$key."]",
      "autocomplete" => "off",
      "type" => "hidden",
      "class" => "prop-".$key
    );
    
    $val = $field->prop_val($key);
    
    if ($val && $val != "") {
      $attr["value"] = $val;
    }
      
    return WOOF_HTML::tag("input", $attr);
  }
  
  public static function prop_inputs($field, $keys) {
    
    if (!is_array($keys)) {
      $keys = explode(",", $keys);
    }
    
    $inputs = "";
    
    foreach ($keys as $key) {
      $inputs .= self::prop_input($field, $key);
    }

    return $inputs;
  }
  
  public static function prop_array($field, $keys) {
    
    $prop = array();

    if (!$field->creator) {
     
      if (!is_array($keys)) {
        $keys = explode(",", $keys);
      }
    
      foreach ($keys as $key) {
        $val = $field->prop($key);
      
        if ($val && $val != "") {
          $prop[$key] = $val;
        }
    
      }
    
    }
    
    return $prop;
    
  }
  
  public static function truncate_for_summary($width, $value) {
    $length = 110 + (($width - 1) * 130);
    return WOOF::truncate_advanced($value, $length, $etc = ' &hellip; ', false, true);
  }
  
  public static function label_is_header($options) {
    return false;
  }
  
  public static function label_suffix() {
    return "";
  }
  
  public static function ui_preserve_whitespace() {
    return false;
  }
  
  public static function ui_lang() {
    return array();
  }
  
  public static function ui_options() {
    return array();
  }
  
  public static function normalize_options($options) {
    return $options;
  }
  
  public static function extract_options($options, $option_keys = array()) {
    if (!is_array($option_keys)) {
      $option_keys = explode(",", $option_keys);
    }
    
    $ret = array();
    
    foreach ($option_keys as $key) {
      if (isset($options[$key])) {
        $val = $options[$key];
        
        if (is_array($val)) {
          $val = implode(",", $val);
        }
        
        $ret[$key] = $val;
      }
    }
    
    return $ret;
    
  }
  
  public function ajax_success($data = array()) {
    MPC::ajax_success($data);
	}						  
	
	public function ajax_error($error, $data = array()) {
    MPC::ajax_error($error, $data);
  }

  /* -- Default Implementations for Save / Load Preparation -- */
  
  public static function value_for_save($value, $field) {
    return $value;
  }

  public static function value_from_load($value, $field) {
    return stripslashes($value);
  }

  public static function prop_from_load($name, $value, $field) {
    return stripslashes($value);
  }

  public static function prop_for_save($name, $value, $field) {
    return $value;
  }
  

  // -- Related Field Results Methods
  
  public static function supports_image() {
    return false;
  }

  public static function supports_text() {
    return true;
  }


  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Create / Edit Field - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  
  public static function options_form($options = array()) {
    return '';
  }

  // -- Helpers 

  
  public static function options_input_text( $p, $options, $key, $label, $note = "", $default_value = null, $class = "text") {
    
    $value = self::option_value($options, $key, $default_value);
    
    if ($note != "") {
      $note = '<span class="note">'.$note.'</span>';
    }
    
    $html = <<<HTML
  
    <div id="{$p}{$key}-f" class="f {$key}-f">
      <label for="{$p}{$key}">{$label}</label>
      <div id="fw-{$p}{$key}" class="fw">
        <input id="{$p}{$key}" type="text" name="type_options[{$key}]" value="{$value}" class="{$class}" />{$note}
      </div>
    </div>

HTML;

    return $html;
    
  } 

  public static function options_textarea( $p, $options, $key, $label, $note = "", $class = "mono", $default_value = null) {
    
    $value = self::option_value($options, $key, $default_value);
    
    if ($note != "") {
      $note = '<span class="note">'.$note.'</span>';
    }
    
    $html = <<<HTML
  
    <div id="{$p}{$key}-f" class="f {$key}-f">
      <label for="{$p}{$key}">{$label}</label>
      <div id="fw-{$p}{$key}" class="fw">
        <textarea id="{$p}{$key}" type="text" name="type_options[{$key}]" class="{$class}">{$value}</textarea>{$note}
      </div>
    </div>

HTML;

    return $html;
    
  }
    
  
  public static function options_select( $p, $options, $select_options, $key, $label, $note = "", $default_value = null, $select_attr = array(), $options_attr = array()) {

    $val = self::option_value($options, $key, $default_value);
    
    $sattr = wp_parse_args( $select_attr, array("id" => $p.$key, "name" => "type_options[".$key."]") );
    
    $select = WOOF_HTML::select(
      $sattr,
      $select_options,
      $val,
      $options_attr
    );

    if ($note != "") {
      $note = '<span class="note">'.$note.'</span>';
    }

    $html = <<<HTML

    <div id="{$p}{$key}-f" class="f {$key}-f">
      <label for="{$p}{$key}">{$label}</label>
      <div id="fw-{$p}{$key}" class="fw">
        {$select}{$note}
      </div>
    </div>
    
HTML;
  
    return $html;
    
  }


  public static function options_radio_list( $p, $options, $radio_options, $key, $label, $note = "", $default_value = null) {

    $val = self::option_value($options, $key, $default_value);
    
    $select = WOOF_HTML::input_radio_group(
      "type_options[".$key."]", $p.$key,
      $radio_options,
      $val
    );

    if ($note != "") {
      $note = '<span class="note">'.$note.'</span>';
    }

    $html = <<<HTML

    <div id="{$p}{$key}-f" class="f {$key}-f">
      <label for="{$p}{$key}">{$label}</label>
      <div id="fw-{$p}{$key}" class="fw">
        {$select}{$note}
      </div>
    </div>
    
HTML;
  
    return $html;
    
  }  
  
  public static function options_checkbox( $p, $options, $key, $label, $note = "", $val = "yes" ) {

    $checked = WOOF_HTML::checked_attr(self::option_value($options, $key) == $val);

    if ($note != "") {
      $note = '<p class="note '.$key.'-note checkbox-alt-label { for_el: \'#'.$p.$key.'\' }">'.$note.'</p>';
    }
    
    $html = <<<HTML

    <div id="{$p}{$key}-f" class="f {$key}-f">
      <label for="{$p}{$key}">{$label}</label>
      <div id="fw-{$p}{$key}" class="fw">
        <input id="{$p}{$key}" name="type_options[{$key}]" value="yes" type="checkbox" class="checkbox" {$checked} />{$note}
      </div>
    </div>

HTML;

    return $html;

  }
  
  
  

  // Utility methods for fields that are common across field types
  
  public static function options_select_multi_layout( $p, $options ) {

    $multi_layout_label = __("Multi-Select Layout:", MASTERPRESS_DOMAIN);

    $multi_layout_float_label = __("Partial rows (Float)", MASTERPRESS_DOMAIN);
    $multi_layout_float_note = __("Best for items with short labels", MASTERPRESS_DOMAIN);

    $multi_layout_block_label = __("Full Rows (Block)", MASTERPRESS_DOMAIN);
    $multi_layout_block_note = __("Better for items with long labels", MASTERPRESS_DOMAIN);
    
    $layout = self::option_value($options, "multi_layout", "float");
      
    $checked_float = WOOF_HTML::checked_attr($layout == "float");
    $checked_block = WOOF_HTML::checked_attr($layout == "block");

    $html = <<<HTML

    <div id="{$p}multi_layout-f" class="f multi_layout-f">

      <p class="label">{$multi_layout_label}</label>

      <div id="fw-{$p}multi_layout" class="fw">
        <div class="ml-float">
          <input id="{$p}multi_layout_float" type="radio" name="type_options[multi_layout]" {$checked_float} value="float" class="radio" />
          <label for="{$p}multi_layout_float">{$multi_layout_float_label}<br /><span class="note">{$multi_layout_float_note}</span></label>
        </div>
      
        <div class="ml-block">
          <input id="{$p}multi_layout_block" type="radio" name="type_options[multi_layout]" {$checked_block} value="block" class="radio" />
          <label for="{$p}multi_layout_block">{$multi_layout_block_label}<br /><span class="note">{$multi_layout_block_note}</span></label>
        </div>
      
      </div>

    </div>
    <!-- /.f -->

HTML;

    return $html;
    
  }
  
  public static function options_select_results_row_style( $p, $options, $labels = array() ) {
    
    $l = wp_parse_args(
      $labels,
      array(
        "title" => __("Title", MASTERPRESS_DOMAIN),
        "excerpt" => __("Excerpt", MASTERPRESS_DOMAIN)
      )
    );
    
    $row_style_label = __("Row Style:", MASTERPRESS_DOMAIN);
    $row_style_icon_title_label = __("Icon, ".$l["title"], MASTERPRESS_DOMAIN);
    $row_style_icon_title_excerpt_label = __("Icon, ".$l["title"].", ".$l["excerpt"], MASTERPRESS_DOMAIN);
    $row_style_image_title_excerpt_label = __("Image, ".$l["title"].", ".$l["excerpt"], MASTERPRESS_DOMAIN);
    $row_style_image_title_label = __("Image, ".$l["title"], MASTERPRESS_DOMAIN);
    
    
    $style = self::option_value($options, "row_style", "icon_title");
      
    $checked_icon_title = WOOF_HTML::checked_attr($style == "icon_title");
    $checked_icon_title_excerpt = WOOF_HTML::checked_attr($style == "icon_title_excerpt");
    $checked_image_title_excerpt = WOOF_HTML::checked_attr($style == "image_title_excerpt");
    $checked_image_title = WOOF_HTML::checked_attr($style == "image_title");

    
    $html = <<<HTML

    <div id="{$p}results-row-style-f" class="f results-row-style-f">

      <p class="label">{$row_style_label}</label>

      <div id="fw-{$p}results-row-style-f" class="fw">
        <div class="row-style-icon-title row-style">
          <input id="{$p}row_style_icon_title" type="radio" name="type_options[row_style]" {$checked_icon_title} value="icon_title" class="radio" />
          <label for="{$p}row_style_icon_title" class="radio">{$row_style_icon_title_label}</label>
        </div>

        <div class="row-style-icon-title-excerpt row-style">
          <input id="{$p}row_style_icon_title_excerpt" type="radio" name="type_options[row_style]" {$checked_icon_title_excerpt} value="icon_title_excerpt" class="radio" />
          <label for="{$p}row_style_icon_title_excerpt" class="radio">{$row_style_icon_title_excerpt_label}</label>
        </div>

        <div class="row-style-image-title row-style">
          <input id="{$p}row_style_image_title" type="radio" name="type_options[row_style]" {$checked_image_title} value="image_title" class="radio" />
          <label for="{$p}row_style_image_title" class="radio">{$row_style_image_title_label}</label>
        </div>

        <div class="row-style-image-title-excerpt row-style">
          <input id="{$p}row_style_image_title_excerpt" type="radio" name="type_options[row_style]" {$checked_image_title_excerpt} value="image_title_excerpt" class="radio" />
          <label for="{$p}row_style_image_title_excerpt" class="radio">{$row_style_image_title_excerpt_label}</label>
        </div>


      </div>

    </div>
    <!-- /.f -->

HTML;

    return $html;
    
  }
  
  
  public static function options_select_basic( $p, $options ) {
    return self::options_checkbox($p, $options, "basic", __("Use Native Control:", MASTERPRESS_DOMAIN), __('MasterPress uses <a href="http://ivaynberg.github.com/select2/" target="_blank">Select2 by Igor Vaynberg</a> to improve the usability of select controls. Check this box to disable Select2 for this field.', MASTERPRESS_DOMAIN));
  }

  public static function options_exclude_current( $p, $options, $note = null) {
    
    if (is_null($note)) {
      $note = __('Check to exclude the current item being edited from this list', MASTERPRESS_DOMAIN); 
    }
    
    return self::options_checkbox($p, $options, "exclude_current", __("Exclude Current?", MASTERPRESS_DOMAIN), $note);
  }
  
  
  public static function options_select_grouping( $p, $options, $label_type = null ) {
    if (is_null($label_type)) {
      $label_type = _("Type");
    }

    $radio_options = array(
      sprintf( __("By %s", MASTERPRESS_DOMAIN), $label_type) => "group", 
      __("None (Flat List)", MASTERPRESS_DOMAIN) => "flat"
    );

    return self::options_radio_list($p, $options, $radio_options, "grouping", __("Grouping:", MASTERPRESS_DOMAIN), "", "group");
  }

  public static function options_placeholder( $p, $options ) {
    return self::options_input_text($p, $options, "placeholder", __("Placeholder Text:", MASTERPRESS_DOMAIN), __("Enter a custom prompt for user selection.", MASTERPRESS_DOMAIN) );
  }

  public static function options_maxwidth( $p, $options ) {
    return self::options_input_text($p, $options, "maxwidth", __("Maximum Width:", MASTERPRESS_DOMAIN), __("(pixels)", MASTERPRESS_DOMAIN) );
  }

  public static function options_maxheight( $p, $options ) {
    return self::options_input_text($p, $options, "maxheight", __("Maximum Height:", MASTERPRESS_DOMAIN), __("(pixels)", MASTERPRESS_DOMAIN) );
  }

  public static function options_minheight( $p, $options ) {
    return self::options_input_text($p, $options, "minheight", __("Minimum Height:", MASTERPRESS_DOMAIN), __("(pixels)", MASTERPRESS_DOMAIN) );
  }
  
  
  public static function options_height( $p, $options ) {
    return self::options_input_text($p, $options, "height", __("Height:", MASTERPRESS_DOMAIN), __("(pixels)", MASTERPRESS_DOMAIN) );
  }

  
  public static function options_select_control_style( $p, $options ) {
    $select_options = array(
      __("<b>Single:</b> Drop Down", MASTERPRESS_DOMAIN) => "drop_down_list",
      __("<b>Multiple:</b> List Box", MASTERPRESS_DOMAIN) => "list_box_multiple" 
    );

    return self::options_radio_list($p, $options, $select_options, "control_style", __("Control Style:", MASTERPRESS_DOMAIN), "", "drop_down_list");
  }

  public static function options_post_orderby( $p, $options ) {
    $select_options = array(
      __("Title - Ascending", MASTERPRESS_DOMAIN) => "title,asc", 
      __("Post Date - Descending", MASTERPRESS_DOMAIN) => "post_date,desc",
      __("Post Date - Ascending", MASTERPRESS_DOMAIN) => "post_date,asc",
      __("Menu Order", MASTERPRESS_DOMAIN) => "menu_order,asc"
    );

    return self::options_select($p, $options, $select_options, "orderby", __("Order By:", MASTERPRESS_DOMAIN));
  }


  
  public static function options_font( $p, $options ) {

    $select_options = array(
      "Sans-Serif&nbsp;&nbsp;-&nbsp;&nbsp;Helvetica, Arial, sans-serif" => "helvetica, arial, sans-serif", 
      "Serif&nbsp;&nbsp;-&nbsp;&nbsp;Georgia, Times New Roman, serif" => "georgia, 'times new roman', serif", 
      "Fixed Width&nbsp;&nbsp;-&nbsp;&nbsp;Consolas, Menlo, Andale Mono, Lucida Console, monospace" => "consolas, menlo, 'andale mono', 'lucida console', monospace"
    );

    return self::options_select($p, $options, $select_options, "font", __("Font:", MASTERPRESS_DOMAIN), __("The first avalable font from this list will be used", MASTERPRESS_DOMAIN));

  }
  


  
  
  
  
  /* -- Post Edit UI -- (Default Implementations of methods that field type developers should override) */

  

  public static function ui($field) {
    return '';
  }

  public static function summary($field) {
    if (!$field->blank()) {
      return $field->value();
    }
  }

  public static function empty_summary($field) {
    return __("( none )", MASTERPRESS_DOMAIN);
  }

  public static function ui_prop() {
    return array();
  }
  

  /* -- Admin Methods -- */
  
  public static function options_admin_head($type) {
    if ($type != "") {
      
      $css_file = MPU::type_file_path($type, "mpft-$type.options.css");

      if (file_exists($css_file)) {
        wp_enqueue_style( "mpft-options-$type", MPU::type_file_url($type, "mpft-$type.options.css")."?".filemtime($css_file) );
      }
      
    }

    $js_file = MPU::type_file_path($type, "mpft-$type.options.js");
    
    if (file_exists($js_file)) {
      add_action("admin_head", create_function("", "echo '<script type=\"text/javascript\">window.mpft_script = \'".MPU::type_file_url($type, "mpft-$type.options.js")."?".filemtime($js_file)."\';</script>';"));
    }

    // now enqueue any type-dependent scripts
    
    if ($type_class = self::type_class($type)) {
      call_user_func( array($type_class, "enqueue") );
    }
      
      
  }
  
  public static function meta_style($type) {
    if ($type != "") {

      $css_file = MPU::type_file_path($type, "mpft-$type.css");

      if (file_exists($css_file)) : ?>
        <link rel="stylesheet" type="text/css" href="<?php echo MPU::type_file_url($type, "mpft-$type.css")."?".filemtime($css_file) ?>" />
      <?php 
      endif;
      
    }
  
  }
  
  public static function meta_script($type) {

    if ($type != "") {

      $js_file = MPU::type_file_path($type, "mpft-$type.js");

      if (file_exists($js_file)) : ?>
        <script type="text/javascript" src="<?php echo MPU::type_file_url($type, "mpft-$type.js")."?".filemtime($js_file) ?>"></script>
      <?php 
      endif;
      
    }

  }
  
  public static function incl($type) {
    MPU::incl_type($type);
  }

  public static function type_prefix($class) {
    return MPU::dasherize(str_replace("MPFT_", "mpft-", $class))."-";
  } 

  public static function type_key($class) {
    return MPU::dasherize(str_replace("MPFT_", "", $class));
  }
  
  public static function color_icon($type) {
    return MPU::type_icon_url($type, true);
  }

  public static function gray_icon($type) {
    return MPU::type_icon_url($type, false);
  }
  
  public static function types_by_category($order = array( "Text Content", "Text Content (Specialized)", "Structured Content", "Media", "Related Object", "Related Object Type", "Value-Based Content", "Value-Based Content (Specialized)", "Other")) {
    $keys = MPFT::type_keys();
    
    $tbc = array();
    
    foreach ($keys as $key) {
      MPFT::incl($key);
      $type_class = MPU::type_class($key);
      
      if (class_exists($type_class)) {
        $category = call_user_func( array($type_class, "__category") );
        $tbc[$category][] = $key;
      }
    }

    $types_by_category = array();
    
    foreach ($order as $key) {
      if (isset($tbc[$key])) {
        $types_by_category[$key] = $tbc[$key];
      }
    }
    
    return $types_by_category;
  }
  
  public static function type_keys() {
    if (!self::$type_keys) {
      self::$type_keys = array_merge(self::core_type_keys(false), self::extension_type_keys(false));
      sort(self::$type_keys);
    }
    
    return self::$type_keys;
  }
  
  public static function core_type_keys() {
    if (!self::$core_type_keys) {
      self::$core_type_keys = MPU::file_list( MPU::core_type_dir() );
    }
    
    return self::$core_type_keys;
  }
  
  public static function extension_type_keys($sort = true) {
    if (!self::$extension_type_keys) {
      self::$extension_type_keys = MPU::file_list( MPU::extension_type_dir() );
    }
    
    return self::$extension_type_keys;
  }

  
  
  public function ui_prop_keys($class) {
    $type = self::type_key($class);
    return self::type_properties($type);
  }

  public function fill_prop($class, $prop_values) {
    $type = self::type_key($class);
    $prop = $this->ui_prop_keys($class);
    
    foreach ($prop as $key) {
      if (isset($prop_values[$key]) && !is_null($prop_values[$key])) {
        $this->field->prop($key, $prop_values[$key]);
      }
    }

  }
  

  public static function one_or_more_value_for_save( $value, MPM_Field $field ) {
    
    if (!is_array($value)) {
      
      if ($value != "") {
        $values = explode(",", $value);
      
        // only return an array if there is more than 1 value
        if (count($values) > 1) {
          return $values;
        }
      }
    
    }
    
    return $value;
  }
  
  /*
    Static Method: select_ui 
      Returns the HTML to render the interface for this field type.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */

  public static function select_ui( MEOW_Field $field, $class ) {
    
    $blank = $field->blank();
    $value = array();

    if (!$blank) {
      $value = $field->value();
    } 
    
    if (!is_array($value)) {
      $value = explode(",", $value);
    }
    
    
    $control_style = self::option_value($field->info->type_options, "control_style", "drop_down_list");

    if ($control_style == "dual_list_box") {
      $control_style = "list_box_multiple";
    }
    
    $input = "";
    
    $val = implode(",", $value);
    
    $row_style = self::option_value($field->info->type_options, "row_style", "icon_title");
    
    if ($control_style == "list_box_multiple") {
      $input = '<input type="hidden" id="{{id}}-value-input" name="{{name}}" data-row-style="'.$row_style.'" type="hidden" value="'.$val.'" class="select2-hidden" />';
    }

    $select = call_user_func_array( array( $class, "select" ), array($field->info->type_options, $value, $blank, $field->is_editable()) );
    
    $html = <<<HTML
      {$input}
      {$select}
HTML;

    return $html;
    
  }
  
  
  // -- Methods to help with row properties in related object field types 
  
  public static function get_row_prop($obj, $result_row_prop, $post_type_name, $prop_key, $default_prop) {
    
    $ret = "";
    
    // retrieve the excerpt
    
    $prop = $default_prop;
    
    if (isset($result_row_prop[$post_type_name][$prop_key])) {
      $prop = $result_row_prop[$post_type_name][$prop_key];
    }
    
    $val = WOOF::eval_token($prop, $obj);
    
    if (isset($val)) {
      $ret = $val;
    }
    
    return $ret;
    
  }

  public static function get_row_prop_excerpt($obj, $result_row_prop, $post_type_name) {
    
    $excerpt = self::get_row_prop($obj, $result_row_prop, $post_type_name, "excerpt", "excerpt");
    
    if ($excerpt && $excerpt != "") {
      return array("data-excerpt" => esc_attr( WOOF::truncate_basic(strip_tags($excerpt), 120) ) );
    } else {
      return array("data-no_excerpt" => "true", "data-excerpt" => __("(no summary)", MASTERPRESS_DOMAIN) );
    }
    
  }

  public static function get_row_prop_desc($obj, $result_row_prop, $post_type_name) {
    
    $excerpt = self::get_row_prop($obj, $result_row_prop, $post_type_name, "excerpt", "description");
    
    if ($excerpt && $excerpt != "") {
      return array("data-excerpt" => esc_attr( WOOF::truncate_basic(strip_tags($excerpt), 120) ) );
    } else {
      return array("data-no_excerpt" => "true", "data-excerpt" => __("(no description)", MASTERPRESS_DOMAIN) );
    }
    
  }

  public static function get_row_prop_full_name($obj, $result_row_prop, $role_id) {
    
    $full_name = self::get_row_prop($obj, $result_row_prop, $role_id, "title", "full_name");
    
    if ($full_name) {
      return $full_name;
    } else {
      return $obj->name();
    }
    
  }

  public static function get_row_prop_role_name($obj, $result_row_prop, $role_id) {
    
    $role_name = self::get_row_prop($obj, $result_row_prop, $role_id, "excerpt", "role_name");
    
    if ($role_name) {
      return array("data-excerpt" => esc_attr( $role_name ) );
    } 
    
    // should never happen!
    return array("data-excerpt" => "");
    
  }
      
  public static function get_row_prop_title($obj, $result_row_prop, $post_type_name) {
    
    // shortcuts
    if (!isset($result_row_prop[$post_type_name]["title"])) {
      return $obj->title();
    }
    
    if (isset($result_row_prop[$post_type_name]["title"]) && $result_row_prop[$post_type_name]["title"] == "title") {
      return $obj->title();
    } 
    
    $title = self::get_row_prop($obj, $result_row_prop, $post_type_name, "title", "title");
    
    if (is_string($title)) {
      return $title;
    }
    
    if (is_object($title) && !is_woof_silent($title)) {
      return $title->__toString();
    }
    
    return $obj->title();
    
  }


  
  public static function get_row_prop_image($obj, $result_row_prop, $post_type_name, $width = 60) {

    $image = self::get_row_prop($obj, $result_row_prop, $post_type_name, "image", "featured_image");
    
    if ($image && !is_woof_silent($image) && !$image->blank() && $image->exists()) {
      
      $w = $width * 2;
      $h = $width * 2;
        
      if ($image->is_image() && $image->width() > $width) {
        $image = $image->resize("w=$w&h=$h");
      }
      
      return array("data-image" => $image->url, "data-image_width" => $width, "data-image_height" => $width);
    } else {
      return array("data-image" => __("no image", MASTERPRESS_DOMAIN), "data-no_image" => "true");
    }
    
  }

  
  
  function is_multi_select() {
    $cs = $this->field()->info->type_options["control_style"];
    return $cs == "list_box_multiple";
  }


  // -- API Methods
  
  function wrap($tag, $default = null) {
    return $this->t("<".$tag.">", $default);
  }
  
  function t($tmpl, $default = null) {
    
    $blank = $this->blank();
    
    if (!$blank || !is_null($default)) {
      if ($blank) {
        $value = $default;
      } else {
        $value = $this->__toString();
      }
      
      if (!preg_match("/\{\{.+\}\}/", $tmpl)) {
      
        // assume we want the value in the innermost tag
      
        WOOF::incl_phpquery();
        phpQuery::newDocumentHTML('<div class="context"></div>', $charset = 'utf-8');
        pq($tmpl)->appendTo(".context");
        pq("*:only-child:last")->append("{{val}}");
        $tmpl = pq(".context")->html();
      }

      return WOOF::render_template($tmpl, array("value" => $value, "val" => $value) );
    }
    
    return "";
    
  }
  
  // formatters (for expressions)
  
  function surround($left, $right, $chain = "") {
    if ($chain != "") {
      $val = $chain;
    } else {
      $val = $this->__toString();
    }
    
    if (trim($val) != "") {
      return $left.$val.$right;
    }
  }
  
  function prefix($with, $chain = "") {
    if ($chain != "") {
      $val = $chain;
    } else {
      $val = $this->__toString();
    }
    
    if (trim($val) != "") {
      return $with.$val;
    }
  }

  function suffix($with, $chain = "") {
    if ($chain != "") {
      $val = $chain;
    } else {
      $val = $this->__toString();
    }
    
    if (trim($val) != "") {
      return $val.$with;
    }
  }

  function pfs($with) {
    return $this->prefix($with." ");
  }

  function sfs($with) {
    return $this->suffix(" ".$with);
  }

  function bsfs($with) {
    return $this->suffix(" ".$with);
  }

  function bracket() {
    return $this->surround(" (", ")");
  }

  function b() {
    return $this->surround("<b>", "</b>");
  }

  function b_space() {
    return $this->surround("<b>", "</b> ");
  }

  function space_b() {
    return $this->surround(" <b>", "</b> ");
  }

  function em() {
    return $this->surround("<em>", "</em>");
  }

  function space_em() {
    return $this->surround(" <em>", "</em>");
  }

  function em_space() {
    return $this->surround("<em>", "</em> ");
  }
  
  function colon() {
    return $this->suffix(": ");
  }

  function b_colon() {
    return $this->surround("<b>", "</b>", $this->suffix(": "));
  }

  
  function nl2br($is_xhtml = true) {
    return nl2br($this->val());
  }

  function nl2($tag, $line_breaks = false, $xml = true) {
    return WOOF::nl2($this->val(), $tag, $line_breaks, $xml);
  }

  function nl2p($line_breaks = false, $xml = true) {
    return WOOF::nl2p($this->val(), $line_breaks, $xml);
  }
     
  function sanitize($args = array()) {
    
    $r = wp_parse_args( $args, array("with" => "-", "remove_single_quote" => true) );
    
    $result = sanitize_title_with_dashes($this->raw());

    if ($r["with"] != "-") {
      $result = str_replace("-", $r["with"], $result);
    }

    if ($r["remove_single_quote"]) {
      $result = str_replace("'", "", $result);
    }
    
    return $result;
  }

  function sanitize_email() {
    return sanitize_email($this->val());
  }
  
  function s($args = array()) {
    return $this->sanitize($args);
  }

  function esc_attr() {
    return esc_attr($this->val());
  }

  function esc_url($allowed_protocols = null) {
    return esc_url($this->val(), $allowed_protocols);
  }

  function esc_url_raw($allowed_protocols = null) {
    return esc_url_raw($this->val(), $allowed_protocols);
  }

  function esc_html() {
    return esc_html($this->val());
  }

  function esc_js() {
    return esc_js($this->val());
  }

  function esc_sql() {
    return esc_sql($this->val());
  }

  function esc_sql_like() {
    return like_escape($this->val());
  }
  
  function balance_tags() {
    return force_balance_tags($this->val());
  }
  
  function kses($allowed = null, $allowed_protocols = null) {
    global $allowedtags;
    
    if ($allowed == null) {
      $allowed = $allowedtags;
    }
    
    return wp_kses($this->val(), $allowed, $allowed_protocols);
  }

  function truncate($length = 160, $etc = "&hellip;") {
    return $this->truncate_custom( array("length" => $length, "etc" => $etc, "middle" => 0, "words" => 1) );
  }

  function truncate_middle($length = 160, $etc = "&hellip;") {
    return $this->truncate_custom( array("length" => $length, "etc" => $etc, "middle" => 1, "words" => 1) );
  }
  
  function truncate_custom($args = array()) {
    $str = strip_tags($this->value());
    return WOOF::truncate($str, $args);
  }


  function addslashes() {
    return addslashes($this->raw());
  }

  function stripslashes() {
    return stripslashes($this->raw());
  }

  function md5() {
    return md5($this->raw());
  }
  
  function shorthash($length = 12) {
    return substr($this->md5(), 0, $length);
  }
  
  function str() {
    return $this->__toString();
  }
  
  function __toString() {
    $data = $this->data();

    if (isset($data->val)) {
      if (is_array($data->val)) {
        return implode(", ", $data->val);
      } else {
        return (string) $data->val;
      }
        
    }
      
    return "";
  } 
  
  function col() {
    return $this->__toString();
  }
  
  function debug_value() {
    
    $val = $this->raw();

    if (is_array($val)) {
      return $val;
    }
    
    return htmlspecialchars($val);
  }

  function raw() {
    $data = $this->data();

    if (isset($data->val))
      return $data->val;
      
    return "";
  }

  function value() {
    $data = $this->data();

    if (isset($data->val)) {
      return $data->val;
    }
  
    return "";
  }

  protected final function field_value() {
    $data = $this->data();

    if (isset($data->val)) {
      return $data->val;
    }
  
    return "";
  }
  
  function value_for_set($value) {
    // override this method to allow validation and parsing of a value
    return $value;
  }
  
  function as_array($value) {
    
    if (is_woof_collection($value)) {
      $values = $value->items();
    } else if (is_object($value)) {
      $values = array( $value );
    } else if (is_array($value)) {
      $values = $value;
    } else {
      $values = explode(",", (string) $value);
    }
    
    return $values;
    
  }
  
  function change() {
    // override this method to uncache anything that is based on the current value of the field 
  }
  
  function val() {
    return $this->value();
  }

  function json() {
    return $this->value();
  }
  
  function is() {
    return $this->val();
  }
  
  function equals($val) {
    return $val == $this->val();
  }
  
  function is_numeric() {
    return is_numeric($this->raw());
  }
  
  function is_email() {
    return is_email($this->val());
  }

  function html() {
    // by default, html simply returns the value, but for some fields (say images) we'll want to provide a tag based on the value
    return $this->val();
  }

  function truncate_words($max_length, $trailing = "&nbsp;&htmlellip;") {
    $val = $this->raw();
    
    if (strlen($val) > $max_length) {
      $val = substr($val,0,$max);
      $i = strrpos($val," ");
      $val = substr($val,0,$i);
      $val = $val.$trailing;
    }
    
    return $val;
  }
  
  function int($fallback = 0) {
    $data = $this->data();

    if (is_numeric($data->val)) {
      return (int) $data->val;
    }
    
    return $fallback;
  }

  function float($fallback = 0.00) {
    $data = $this->data();

    if (is_numeric($data->val)) {
      return (float) $data->val;
    }
    
    return $fallback;
  }
  
  // call forwards a delegated call from a class derived from MPFT on to another delegate object (if the child class of MPFT supplies a get_delegate method)  
  // The reason this is needed is that call_user_func_array will not attempt to try __call in the receiving method.

  public function call($name, $arguments) {
    
    if (method_exists($this, "get_delegate")) {

      $delegate = $this->get_delegate();

      if (method_exists($delegate, $name)) {
        return call_user_func_array (array($delegate, $name), $arguments); 
      } 
    }
    
    return new WOOF_Silent( sprintf( __("No method or property could be found for %s", MASTERPRESS_DOMAIN ), $name ) );
  }

  public function forward($name) {
    
    return new WOOF_Silent( __("This field type does not need to forward calls", MASTERPRESS_DOMAIN));
    
  } 
  
  
}