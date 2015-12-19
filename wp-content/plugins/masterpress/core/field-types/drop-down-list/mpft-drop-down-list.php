<?php

class MPFT_DropDownList extends MPFT {
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Drop-down List", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Drop-down Lists", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A select control for choosing a single specific value from a list", MASTERPRESS_DOMAIN);
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
      $defaults = array(
        "prompting_label" => __("Please select an option:", MASTERPRESS_DOMAIN), 
        "maxwidth" => "", 
      ); 
    }

    $options = wp_parse_args( $options, $defaults );

    $maxwidth = self::option_value($options, "maxwidth");
    $prompting_label = self::option_value($options, "prompting_label");
    $default_value = self::option_value($options, "default_value");
    $values = self::option_value($options, "values");
    
    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $values_label = __("Choices:", MASTERPRESS_DOMAIN);
    $values_note = __('Specify the labels for each choice on separate lines. To use a different underlying <span class="tt">value</span> for any of the choices,<br /> use the form <strong><span class="tt">label = value</span></strong>. To create a group of choices, prefix the line you wish to use as a group label with <span class="tt">--</span> (group labels are not selectable)', MASTERPRESS_DOMAIN);

    $maxwidth_label = __("Maximum Width:", MASTERPRESS_DOMAIN);
    $maxwidth_note = __("(pixels)", MASTERPRESS_DOMAIN);

    $prompting_label_label = __("Empty Prompting Label:", MASTERPRESS_DOMAIN);
    $prompting_label_note = __("Enter a prompting label to use for an empty value, displayed above the <em>Choices</em>.<br />Clear this value if you don't wish to allow an empty value.", MASTERPRESS_DOMAIN);

    $default_value_label = __("Default State:", MASTERPRESS_DOMAIN);
    $default_value_note = __("Use the preview of your control above to setup the default state", MASTERPRESS_DOMAIN);


    $default_value_select = WOOF_HTML::select( 
      array("id" => $p."default-value", "name" => "type_options[default_value]"),
      WOOF_HTML::option_values($values, $prompting_label, true),
      $default_value
    );

    $html = <<<HTML

    <div class="f">
      <label for="{$p}maxwidth">{$maxwidth_label}</label>
      <div id="fw-{$p}maxwidth" class="fw">
        <input id="{$p}maxwidth" name="type_options[maxwidth]" type="text" maxlength="4" value="{$maxwidth}" class="text" /><span class="note">{$maxwidth_note}</span>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}values">{$values_label}</label>
      <div id="fw-{$p}values" class="fw">
        <textarea id="{$p}values" class="mono" name="type_options[values]">{$values}</textarea>
        <p class="note">{$values_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}prompting-label">{$prompting_label_label}</label>
      <div id="fw-{$p}prompting-label" class="fw">
        <input id="{$p}prompting-label" name="type_options[prompting_label]" type="text" value="{$prompting_label}" class="text" />
        <p class="note">{$prompting_label_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div id="{$p}default-value-f" class="f">
      <label for="{$p}default-value">{$default_value_label}</label>
      <div id="fw-{$p}default-value" class="fw">
        <div class="preview">
          {$default_value_select}
        </div>

        <p class="note">{$default_value_note}</p>
      </div>
    </div>
    <!-- /.f -->

HTML;

    return $html;

  }  




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: ui 
      Returns the HTML to render the interface for this field type.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */

  public static function ui( MEOW_Field $field ) {
    
    $options = $field->info->type_options;
    
    $maxwidth = self::option_value($options, "maxwidth");

    if (is_numeric($maxwidth)) {
      $maxwidth = "{$maxwidth}px; width: 99%";
    } else {
      $maxwidth = "auto";
    }

    $value = $field->value();

    $placeholder = self::option_value($options, "prompting_label");
    
    $items = WOOF_HTML::option_values($field->info->options("values"), "", true);

    $select_attr = array(
      "id" => "{{id}}", 
      "name" => "{{name}}", 
      "style" => "max-width: {$maxwidth};"
    );

    if ($placeholder != "" && $maxwidth == "auto") {
      
      $max = strlen($placeholder) * 8;

      // make the maxwidth attempt to fit the placeholder
      
      foreach ($items as $text => $val) {
        $max = max( $max, strlen($text) * 8 );
      }
      
      $select_attr["style"] .= " width: ".$max."px; "; 
    }
    
 
    if ($placeholder != "") {
      $select_attr["data-placeholder"] = $placeholder;
      $items[] = "";
    }

    if (!$field->is_editable()) {
      $select_attr["data-placeholder"] = __("-- None Selected --", MASTERPRESS_DOMAIN);
      $select_attr["disabled"] = "disabled";
    }

    $html = WOOF_HTML::select( 
      $select_attr,
      $items,
      $value
    );
  
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
    
    if (!$field->blank()) {
      $value = $field->value();
      $values = WOOF_HTML::option_values($field->info->options("values"));
      $key = array_search($value, $values);
      return $key;
    }
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
    
    

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  protected $_tv;
  protected $_vt;
  
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