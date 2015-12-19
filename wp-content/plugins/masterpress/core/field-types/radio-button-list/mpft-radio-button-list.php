<?php

class MPFT_RadioButtonList extends MPFT {

  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Radio Button List", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Radio Button Lists", MASTERPRESS_DOMAIN);
  }
  
  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Grouped radio buttons, allowing selection of a single value from one or more specific values", MASTERPRESS_DOMAIN);
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
      $defaults = array("maxlength" => 400 , "width" => 400, "height" => 160);
    }

    $options = wp_parse_args( $options, $defaults );


    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $values_label = __("Choices:", MASTERPRESS_DOMAIN);
    $values_note = __('Specify the labels for each choice on separate lines. To use a different underlying <span class="tt">value</span> for any of the choices,<br /> use the form <strong><span class="tt">label = value</span></strong>.', MASTERPRESS_DOMAIN);

    $default_value_label = __("Default State:", MASTERPRESS_DOMAIN);
    $default_value_note = __("Use the preview of your control above to setup the default state", MASTERPRESS_DOMAIN);

    $default_value = self::option_value($options, "default_value");
    $allow_uncheck = self::option_value($options, "allow_uncheck");
    $values = self::option_value($options, "values");

    $allow_uncheck_label = __("Allow Uncheck?:", MASTERPRESS_DOMAIN);
    $allow_uncheck_checked_attr = WOOF_HTML::checked_attr($allow_uncheck == "yes");
    $allow_uncheck_note = __("Include a button to uncheck all radio buttons in the set", MASTERPRESS_DOMAIN);

    $select_none_label = __("Select None", MASTERPRESS_DOMAIN);


    $option_values = WOOF_HTML::option_values($values);

    $default_value_radio_buttons = WOOF_HTML::input_radio_group( "type_options[default_value]", $p."default-value-", $option_values, $default_value, WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")); 


$html = <<<HTML

    <div class="f">
      <label for="{$p}values">{$values_label}</label>
      <div id="fw-{$p}values" class="fw">
        <textarea id="{$p}values" class="mono" name="type_options[values]">{$values}</textarea>
        <p class="note">{$values_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}allow-unselect">{$allow_uncheck_label}</label>
      <div id="fw-{$p}allow-unselect" class="fw">
        <input id="{$p}allow-unselect" name="type_options[allow_uncheck]" type="checkbox" {$allow_uncheck_checked_attr} value="yes" class="checkbox" />
        <span class="note checkbox-alt-label">{$allow_uncheck_note}</span>
      </div>
    </div>
    <!-- /.f -->

    <div id="{$p}default-value-f" class="f">
      <label for="{$p}default-value">{$default_value_label}</label>
      <div id="fw-{$p}default-value" class="fw">
        <div class="preview">{$default_value_radio_buttons}</div>
        <div id="{$p}default-value-controls" class="controls divider">
          <button type="button" class="button uncheck-all">{$select_none_label}</button>
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
    Static Method: empty_summary 
      Returns the HTML to render the EMPTY summary for this field type. The "summary" is the grid block for this field in the collapsed view of the set it belongs to.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */
  
  public static function empty_summary( MEOW_Field $field ) {
    return __("( none selected )", MASTERPRESS_DOMAIN);
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
      $value = $field->value();
      $values = WOOF_HTML::option_values($field->info->options("values"));
      $key = array_search($value, $values);
      return $key;
    }
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

    $controls = "";
    
    $options = $field->info->type_options;

    $allow_uncheck = self::option_value($options, "allow_uncheck");

    $select_none_label = __("Select None", MASTERPRESS_DOMAIN);

    $value = $field->value();


    $option_values = WOOF_HTML::option_values($options["values"]);

    $hidden = "";
    $name = "{{name}}";
    $id = "{{id}}";
    $buttons = "";
    
    if (!$field->is_editable()) {
      
      $hidden_name = $name;
      $hidden_id = $id;

      $name = "display_".$name;
      $id = "display_".$id;
      
      $hidden = '<input id="'.$hidden_id.'" name="'.$hidden_name.'" value="'.$value.'" type="hidden" />';
    
    } else {
      if (isset($options["allow_uncheck"]) && $allow_uncheck == "yes") {
      $controls = <<<HTML
      <div class="buttons">
        <button type="button" class="button button-small uncheck-all">{$select_none_label}</button>
      </div>
HTML;
      }

    }
    
    $radio_buttons = WOOF_HTML::input_radio_group( $name, $id, $option_values, $value, WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div"), !$field->is_editable()); 
    
    $html = <<<HTML

    <div class="f">
    <div class="fw">
    {$radio_buttons}
    {$hidden}
    </div>
    {$controls}
    </div>

HTML;

    return $html;

  }

  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 


  function text() {
    
    if (!isset($this->_tv)) {
      $options = $this->field()->info->type_options;

      if (isset($options, $options["values"])) {
        
        $this->_tv = WOOF_HTML::option_values($options["values"]);
      
        if (is_array($this->_tv)) {
          $this->_vt = array_flip($this->_tv);
        } else {
          $this->_tv = array();
          $this->_vt = array();
        }
      
      } else {
        $this->_tv = array();
        $this->_vt = array();
      }
      
    }
    
    $val = $this->value();
    
    if (isset($this->_vt[$val])) {
      return $this->_vt[$val];
    }

    return "";
    
  }
  

}