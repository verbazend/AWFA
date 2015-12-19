<?php

class MPFT_CheckboxList extends MPFT {

  private static $values_keys = array(); // cache for summary


  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Checkbox List", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Checkbox Lists", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Grouped checkboxes, allowing one or more specific values to be selected", MASTERPRESS_DOMAIN);
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

    $defaults = array();

    if (MPC::is_create()) {
      $defaults = array();
    }

    $options = wp_parse_args( $options, $defaults );

    $values = self::option_value($options, "values");

    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $values_label = __("Choices:", MASTERPRESS_DOMAIN);
    $values_note = __('Specify the labels for each choice on separate lines. To use a different underlying <span class="tt">value</span> for any of the choices,<br /> use the form <strong><span class="tt">label = value</span></strong>.', MASTERPRESS_DOMAIN);

    $default_value_label = __("Default State:", MASTERPRESS_DOMAIN);
    $default_value_note = __("Use the preview of your control above to setup the default state", MASTERPRESS_DOMAIN);

    $option_values = WOOF_HTML::option_values($values);

    $default_value_checkboxes = WOOF_HTML::input_checkbox_group( "type_options[default_value][]", $p."default-value-", $option_values, self::option_value($options, "default_value"), WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")); 


$html = <<<HTML

    <div class="f">
      <label for="{$p}values">{$values_label}</label>
      <div id="fw-{$p}values" class="fw">
        <textarea id="{$p}values" class="mono" name="type_options[values]">{$values}</textarea>
        <span class="note">{$values_note}</span>
      </div>
    </div>
    <!-- /.f -->

    <div id="{$p}default-value-f" class="f">
      <label for="{$p}default-value">{$default_value_label}</label>
      <div id="fw-{$p}default-value" class="fw">
        <div class="preview">{$default_value_checkboxes}</div>

        <div id="{$p}default-value-controls" class="controls">
          <button type="button" class="button button-small select-all">Select All</button>
          <button type="button" class="button button-small select-none">Select None</button>
        </div>
        <!-- /.controls -->

        <p class="note">{$default_value_note}</p>

      </div>
    </div>
    <!-- /.f -->

HTML;

    return $html;

  }




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: label_is_header
      Returns true if the label for this field that sits above the User Interface should be a header (h4) tag instead.
      Implement this function to return TRUE if the field UI is more complex with its own labels, and the header can't be bound to any specific control.
      The base MPFT class implements this to return FALSE by default.
      
    Arguments: 
      $options - an associative array containing the UI options from the field definition
      
    Returns:
      Boolean
  */

  public static function label_is_header( $options ) {
    return true;
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
    if (!$field->blank()) {
      $ret = self::truncate_for_summary(self::summary_width(), implode(", ", self::$values_keys));
      self::$values_keys[] = array(); // reset the cache
      return $ret;
    }

    return "";
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

    $select_none_label = __("Select None", MASTERPRESS_DOMAIN);
    $select_all_label = __("Select All", MASTERPRESS_DOMAIN);

    $disabled = WOOF_HTML::disabled_attr(!$field->is_editable());
    
    $field_value = $field->value();
    
    if (!is_array($field_value)) {
      $field_value = array();
    }
    
    $selected_values = $field_value;

    $values = WOOF_HTML::option_values($options["values"]);

    self::$values_keys = array();

    if (!$field->blank()) {
      // populate the values
      $selected_values = array();

      foreach ($values as $key => $value) {
        if (in_array($value, $field_value)) {
          self::$values_keys[] = $key; // cache the keys for the summary, so we don't have to look them up again!
          $selected_values[] = $value;
        }
      }

    }

    
    $hidden = "";
    $buttons = "";
    $checkbox_name = "{{name}}[]";
    $checkbox_id = "{{id}}";
    
    if (!$field->is_editable()) {
      
      $hidden_name = $checkbox_name;
      $hidden_id = $checkbox_id;

      $checkbox_name = "display_".$checkbox_name;
      $checkbox_id = "display_".$checkbox_id;
      
      foreach ($selected_values as $value) {
        $hidden .= '<input id="'.$hidden_id.'" name="'.$hidden_name.'" value="'.$value.'" type="hidden" />';
      }
      
    } else {
      
      $buttons = <<<HTML
      
      <div class="buttons">
        <button type="button" class="button button-small check-all">{$select_all_label}</button>
        <button type="button" class="button button-small uncheck-all">{$select_none_label}</button>
      </div>
HTML;

    }
    
    $checkboxes = WOOF_HTML::input_checkbox_group( "{{name}}[]", "{{id}}", $values, $selected_values, WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div"), !$field->is_editable()); 

    $html = <<<HTML

    <div class="f">
      <div class="fw">
      {$checkboxes}
      {$hidden}
      </div>
      {$buttons}
    </div>

HTML;

    return $html;

  }
  
  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  function iterator_items() {
    return $this->value();
  }
  
  function values() {
    return $this->value();
  }
  
  public function count() {
    return count($this->values());
  }

  public function offsetGet($index) {
    $vals = $this->values();
    
    if (isset($vals[$index])) {
      return $vals[$index];
    }
    
    return "";
  }

  public function offsetExists($index) {
    $vals = $this->values();
    return isset($vals[$index]);
  }
  
  
  function contains($value, $strict = false) {
    return in_array($value, $this->values(), $strict);
  }

  function __toString() {
    // here we'll return a CSV interpretation of the selected values
    return implode(",", $this->value());
  }
  
  function html() {
    // here we'll return a CSV interpretation of the selected values
    return $this->__toString();
  }
      
}