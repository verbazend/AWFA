<?php

class MPFT_Checkbox extends MPFT {
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Checkbox", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Checkboxes", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A single checkbox, to represent an on / off toggle state", MASTERPRESS_DOMAIN);
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
    return "Value-Based Content";
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

    $options = wp_parse_args( $options, array() );

    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required as we cannot call functions within heredoc strings)

    $value_label = __("Underlying value:", MASTERPRESS_DOMAIN);
    $value_note = __('Leave blank to simply use <span class="tt">"true"</span> as the value.', MASTERPRESS_DOMAIN);

    $default_value = self::option_value($options, "default_value");
    $value = self::option_value($options, "value");
    
    $checked_label = __("Checked by default?:", MASTERPRESS_DOMAIN);
    $checked_checked_attr = WOOF_HTML::checked_attr( $default_value != "");

    $lang_checked = esc_js( __("( checked )", MASTERPRESS_DOMAIN) );
    $lang_not_checked = esc_js( __("( not checked )", MASTERPRESS_DOMAIN) );

    $html = <<<HTML

    <div class="f">
      <label for="{$p}checked">{$checked_label}</label>
      <div id="fw-{$p}checked" class="fw">
        <input id="{$p}checked" name="type_options[default_value]" type="checkbox" {$checked_checked_attr} value="true" class="checkbox" />
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}value">{$value_label}</label>
      <div id="fw-{$p}value" class="fw">
        <input id="{$p}value" name="type_options[value]" type="text" value="{$value}" class="text mono" />
        <p class="note">{$value_note}</p>
      </div>
    </div>
    <!-- /.f -->


HTML;

    return $html;

  }  




  // - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  /*
    Static Method: ui_lang 
      Returns an array of language strings accessable via the "lang" property of the mpft-based JavaScript widget 
      
    Returns:
      array - an array of strings. These strings should be prepared for i18n with Wordpress' __() function
  */
   
  public static function ui_lang() {
    return array(
      "checked" => __("( checked )", MASTERPRESS_DOMAIN)
    );
  }

  /*
    Static Method: label_suffix 
      Returns a suffix to append to the label above this control 
      
    Returns:
      String
  */
     
  public static function label_suffix() {
    return "?";
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
    if ($value == "true") {
      return true;
    }
    
    return false;
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
    
    // $field here is a MEOW_Field, which is a class that encapsulates the value of a field AND the info associated with it

    $options = $field->info->type_options;

    $readonly = WOOF_HTML::readonly_attr( !$field->is_editable() );
    
    $checked_value = trim($options["value"]);
    
    $checked_checked_attr = WOOF_HTML::checked_attr($field->checked());
    
    if ($checked_value == "") {
      $checked_value = "true";
    }

    $lang_checked = esc_js( __("( checked )", MASTERPRESS_DOMAIN) );
    $lang_not_checked = esc_js( __("( not checked )", MASTERPRESS_DOMAIN) );
    
    
    
    if ($field->is_editable()) {
      
    $html = <<<HTML

    <input id="{{id}}" name="{{name}}" type="checkbox" {$checked_checked_attr} value="{$checked_value}" class="checkbox { lang: { 'checked' : '{$lang_checked}', 'not_checked' : '{$lang_checked}' } }" />

HTML;

    } else {
    
      // setup a hidden value for checkboxes that are currently checked, to simulate a "readonly" state
      
      if ($field->checked()) {
        // we only need submit the value if the the checkbox is currently checked
        $hidden = <<<HTML
        <input id="{{id}}" name="{{name}}" type="hidden" value="{$checked_value}"  />
HTML;

      } 
    
    $html = <<<HTML
    <input id="{{id}}-display" name="display_{{name}}" type="checkbox" disabled="disabled" {$checked_checked_attr} value="{$checked_value}" class="checkbox { lang: { 'checked' : '{$lang_checked}', 'not_checked' : '{$lang_checked}' } }" />
    $hidden
HTML;
  
      
    }
    
    return $html;
    
  }

  /*
    Static Method: summary 
      Returns the HTML to render the NON-EMPTY summary for this field type. The "summary" is the grid block for this field in the collapsed view of the set it belongs to.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */

  public static function summary( MEOW_Field $field ) {
    return "( checked )";
  }

  /*
    Static Method: empty_summary 
      Returns the HTML to render the EMPTY summary for this field type. The "summary" is the grid block for this field in the collapsed view of the set it belongs to.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */
  
  public static function empty_summary( MEOW_Field $field ) {
    return __("( not checked )", MASTERPRESS_DOMAIN);
  }

  
  

  // - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  
  function value() {
    $val = parent::value();

    
    if (isset($val) && $val != '') {
  	  return true;
    }
    
    return false;
  }

  function value_for_set($value) {
    
    $underlying = $this->field()->info->type_options["value"];

    if ($underlying == "") {
      $underlying = "true";
    }

    if ($value === true || $value === "true" || $value === 1 || $value === "1") {
      return $underlying; 
    }

    if (!$value) {
      return "";
    }
    
    return $value;
  }
  
  function checked() {
    // synonym for value (for readability)
    return $this->value();
  }

  function is() {
    // synonym for value (for readability)
    return $this->checked();
  }
  
  function col() {
    return $this->icon();
  }
  
  function icon() {
    $class = array("mp-bool");
    
    $checked = $this->checked();
    
    if ($checked) {
      $class[] = "yes";
    } else {
      $class[] = "no";
    }
    
    return WOOF_HTML::tag("div", array("class" => implode(" ", $class)), $checked ? __("yes") : __("no"));
  }
  
}