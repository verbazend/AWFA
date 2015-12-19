<?php

class MPFT_RelatedPostType extends MPFT {
  
  private static $values_keys = array(); // cache for summary
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Related Post Type(s)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Related Post Types", MASTERPRESS_DOMAIN);
  }
  
  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Allows selection of one or more related post types from the list of types supported in the current site", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __category 
      The category for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
    
    Valid Values:
      * Text Content
      * Text Content (Specialized)
      * Media
      * Related Object
      * Related Object Type
      * Value-Based Content
      * Value-Based Content (Specialized)
      * Other
  */
  
  public static function __category() {
    return "Related Object Type";
  }




  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Create / Edit Field - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: options_form 
      Returns the HTML for the "Field Type Options" panel for this field type in the MasterPress Create / Edit Field screen  

    Arguments:
      $options - Array, an associative array of loaded key / value options for this field instance (empty if this field is being created)

    Returns:
      String
  */

  public static function options_form( $options ) {

    $p = self::type_prefix(__CLASS__);

    $defaults = array();

    if (MPC::is_create()) {
      $defaults = array("maxwidth" => 580, "height" => 300, "control_style" => "drop_down_list");
    }

    $options = wp_parse_args( $options, $defaults );

    if (MPC::is_create()) {
      $options["post_types"] = array();
    }


    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $post_types_label = __("Available Post Types:", MASTERPRESS_DOMAIN);

    $post_types_note = __("Select the post types available for selection in the field control", MASTERPRESS_DOMAIN);

    $post_types_items = array();

    MPM::incl("post-type");

    $post_types = MPM_PostType::find("orderby=disabled,name ASC");

    $post_types_selected = self::option_value($options, "post_types", array());

    foreach ($post_types as $post_type) {

      if (!$post_type->disabled && $post_type->show_ui && $post_type->still_registered()) {
        $post_types_items[$post_type->display_label()] = $post_type->name;
      }

    }

    $post_types_checkboxes = '';
        
    if (!is_array($post_types_selected)) {
      $post_types_selected = explode(",", $post_types_selected);
    }
  
    $id = $p."post-types-";
    
    foreach ($post_types as $post_type) {


      if (!$post_type->disabled && $post_type->show_ui && $post_type->still_registered()) {
      
        $value = $post_type->name;
        $label = $post_type->display_label();
      
        $id_suffix = WOOF::sanitize($value);
      
        $attr = array( "id" => $id."_".$id_suffix, "class" => "checkbox", "type" => "checkbox", "name" => "type_options[post_types][]", "value" => $value );
      
        if (in_array($value, $post_types_selected)) {
          $attr["checked"] = "checked";
        }
      
        $post_types_checkboxes .= WOOF_HTML::open("div", "class=fwi");
      
        $post_types_checkboxes .= WOOF_HTML::tag("input", $attr );
      
        $post_types_checkboxes .= WOOF_HTML::tag("label", array( "for" => $id."_".$id_suffix, "class" => "checkbox mp-icon-post-type-".$value ), $label );

        $post_types_checkboxes .= WOOF_HTML::close("div");

      }

    }
    


    $basic = MPFT::options_select_basic( $p, $options );
    $multi_layout = MPFT::options_select_multi_layout( $p, $options );
    $control_style = MPFT::options_select_control_style( $p, $options );
    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $height = MPFT::options_height( $p, $options );
    $placeholder = MPFT::options_placeholder($p, $options);


    $control_selections_label = __("Selection Display - <span>settings for the display of the selected post types</span>", MASTERPRESS_DOMAIN); 



$html = <<<HTML

    {$control_style}

    <div class="f f-post-types">
      <p class="label">{$post_types_label}</p>
      <div class="fw">

      <div id="{$p}post-types-wrap">
      {$post_types_checkboxes}
      </div>

      <div id="{$p}post-types-controls" class="controls">
        <button type="button" class="button button-small select-all">Select All</button>
        <button type="button" class="button button-small select-none">Select None</button>
      </div>
      <!-- /.controls -->

      <p class="note">{$post_types_note}</p>
      </div>
    </div>
    <!-- /.f -->
    
    {$placeholder}
    {$basic}
    {$maxwidth}

    <div id="{$p}control-selections-wrap" class="divider">
    <h4><i class="buttons"></i>{$control_selections_label}</h4>  
    {$multi_layout}
    </div>
HTML;

    return $html;

  }




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: summary 
      Returns the HTML to render the NON-EMPTY summary for this field type. The "summary" is the grid block for this field in the collapsed view of the set it belongs to.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */

  public static function summary( MEOW_Field $field ) {
    if (!$field->blank()) {
      $ret = self::truncate_for_summary(self::summary_width(), implode(", ", self::$values_keys));
      self::$values_keys[] = array(); // reset the cache
      return $ret;
    }

    return "";
  }


  
  /*
    Static Method: ui_options 
      Returns an array of keys of the type options in the field definition which should be passed through to the JavaScript MPFT widget.
      accessible as a ui_options hash in the jQuery UI widget. The default behaviour is not to pass any of these through, and you
      should avoid passing options that are richly typed, as they are passed through in class-attribute metadata on the field ui element.
      
    Returns:
      Array - of string keys for options required. 
  */

  public static function ui_options() {
    return "control_style,multi_layout,basic";
  }

  
  /*
    Static Method: value_for_save
      Transforms the value in the control into an appropriate value for the database
      
    Arguments: 
      $value - String, the value posted for this control
      $field - MPM_Field, a model object representing the field definition

    Returns:
      mixed - a value that's appropriate for storing in the database
  */

  public static function value_for_save( $value, MPM_Field $field ) {
    return MPFT::one_or_more_value_for_save( $value, $field );
  }
  
  
  /*
    Static Method: select 
      Gets the select for this control. This is factored out of the ui so it can be used for AJAX refreshing. 
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - an HTML string for the select
      
  */
  
  public static function select($options, $value, $blank = false, $editable = true) {

    global $wf;

    $control_style = self::option_value($options, "control_style", "drop_down_list");

    if ($control_style == "dual_list_box") {
      // legacy patch for dual list box - it had bad performance for a large number of items
      $control_style = "list_box_multiple";
    }
    
    $maxwidth = self::option_value($options, "maxwidth");
    $height = self::option_value($options, "height");

    $selected_values = array();
    $items = array();
    $options_attr = array();
    $values_select = "";
    $values_options = array_flip($value);
    $values_options_attr = array_flip($value);
    $count = -1;
    $multiple = count($options["post_types"]) > 1;
    self::$values_keys = array();

    // build a list of related post types

    foreach ($options["post_types"] as $post_type) {

      $post_type = $wf->types($post_type);

      if ($post_type->exists()) { 
        $count++;

        $text = $post_type->plural_label();
        $val = $post_type->name();

        $items[$text] = $val;
        
        $attr = array( "data-icon" => "mp-icon mp-icon-post-type mp-icon-post-type-".$val );

        $options_attr[] = $attr;

        if (!$blank && in_array($val, $value)) {
          $values_options[$val] = $text;
          $values_options_attr[$val] = $attr;
          $selected_values[] = $val;
        } 


      }

    }

    self::$values_keys = array_values($values_options);

    $select_style = "";

    $select_attr = array("id" => "{{id}}", "name" => "{{name}}");

    if (is_numeric($maxwidth)) {
      $select_style .= "width: 99%; max-width: ".$maxwidth."px;";
    } 

    $select_attr["class"] = "with-icons";
    
    if ($control_style == "list_box_multiple") {
      $select_attr["multiple"] = "multiple";
      $select_attr["name"] = "{{name}}[]";
    } 


    if ($select_style != "") {
      $select_attr["style"] = $select_style;
    }


    $basic = self::option_value($options, "basic") == "yes";

    if ($control_style == "drop_down_list") {

      $placeholder = self::option_value($options, "placeholder", __("-- Select a Post Type --", MASTERPRESS_DOMAIN));
    
      if ($basic) {
        $items = array($placeholder => "") + $items;
        array_unshift($options_attr, array());
      } else {
        $items = array("" => "") + $items;
        array_unshift($options_attr, array());
        $select_attr["data-placeholder"] = $placeholder;
      }

    
    } else {
      
      $placeholder = self::option_value($options, "placeholder", __("-- Select Post Types --", MASTERPRESS_DOMAIN));
      $select_attr["data-placeholder"] = $placeholder;
      $select_attr["data-value-input"] = "{{id}}-value-input";

      if (!$basic) {
        // ensure the select control does not affect the values posted, the hidden input is responsible for this
        $select_attr["name"] = "src_".$select_attr["name"];
      }
      
    }


    if (!$editable) {
      $select_attr["data-placeholder"] = __("-- None Selected --", MASTERPRESS_DOMAIN);
      $select_attr["disabled"] = "disabled";
    }

    return WOOF_HTML::select( 
      $select_attr,
      $items,
      $selected_values,
      $options_attr
    );

  }


  /*
    Static Method: ui 
      Returns the HTML to render the interface for this field type.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */

  public static function ui( MEOW_Field $field ) {
    return MPFT::select_ui( $field, __CLASS__ );
  }

  
  

  // - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post (AJAX) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public static function refresh() {

    global $wf;

    $values = json_decode(stripslashes($_REQUEST["values"]), true);
    $model_id = $_REQUEST["model_id"];

    if (!is_array($values)) {
      $values = array($values);
    }
    
    $field = MPM_Field::find_by_id($model_id);
    
    if ($field) {
      $selects = self::selects($field->type_options, $values);
      $info["select"] = WOOF::render_template($selects["select"], array("id" => $_REQUEST["id"], "name" => str_replace("[]", "", $_REQUEST["name"])  ) );
      self::ajax_success( $info );
    }
    
  }
  
  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  protected $_post_type;
  protected $_post_types;
  
  public function iterator_items() {
    return $this->post_types()->items();
  }

  public function count() {
    return count($this->post_types());
  }

  public function offsetGet($index) {
    return $this->post_types[$index];
  }

  public function offsetExists($index) {
    return $index <= count($this->post_types());
  }
  
  public function get_delegate() {
    return $this->post_type();
  }

  public function forward($name) {
    return $this->get_delegate()->__get($name);
  }
  
  public function post_types() {
    global $wf;
    
    if (!isset($this->_post_types)) {
      $value = $this->value();
    
      if (!is_array($value)) {
        $value = array($value);
      }
    
      $post_types = $wf->types->find_by_in("name", $value);
      $this->_post_types = $post_types->sort_to("name", $value);
    }
    
    return $this->_post_types;
  }

      

  public function post_type() {
    global $wf;
    
    if (!isset($this->_post_type)) {

      $value = $this->value();
      
      if (!is_array($value)) { // single post relation
        if (!$this->blank()) {
          $this->_post_type = $wf->post_types($this->value());
        } else {
          $this->_post_type = new WOOF_Silent(__("No post type has been set for this field", MASTERPRESS_DOMAIN));
        }  
      } else {
        // grab the first post
        
        $post_types = $this->post_types();
        
        if ($post_types->count()) {
          $this->_post_type = $post_types->first();
        }
        else {
          $this->_post = new WOOF_Silent(__("No post type has been set for this field", MASTERPRESS_DOMAIN));
        }
        
      }
      
    } 
    
    return $this->_post_type;    
  }
  
  
  public function change() {
    unset($this->_post_type, $this->_post_types);
  }
  
  public function value_for_set($value) {
    
    global $wf;

    // make sure this is a valid value for the types available in this control
    
    $post_types = $this->field()->info->type_options["post_types"];
    
    $values = $this->as_array( $value );
    
    if ($this->is_multi_select()) {
      
      $ret = array();
      
      foreach ($values as $val) {
        $post_type = $wf->type($val);
        
        if ($post_type->exists()) {
          $ret[] = $post_type->name;
        }
      
      }
      
      return $ret;
      
    } 
    else {
      
      $value = $values[0];
      
      $post_type = $wf->type($value);
    
      if ($post_type->exists()) {
        return $post_type->name;
      }
    
    }

    return "";
    
  }
  
}