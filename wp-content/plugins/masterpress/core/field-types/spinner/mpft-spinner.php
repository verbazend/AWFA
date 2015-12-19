<?php

class MPFT_Spinner extends MPFT {

  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Spinner", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Spinner", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A numeric text input with with increment / decrement arrow buttons", MASTERPRESS_DOMAIN);
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
    return "Value-Based Content (Specialized)";
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
      $defaults = array("step" => 1, "min" => 0, "format" => "0", "maxwidth" => 100, $negative_red = "yes");
    }

    $options = wp_parse_args( $options, $defaults );

    $negative_red_label = __("Show Negative in red?:", MASTERPRESS_DOMAIN);

    $min_label = __("Minimum Value:", MASTERPRESS_DOMAIN);
    $max_label = __("Maximum Value:", MASTERPRESS_DOMAIN);

    $default_label = __("Default Value:", MASTERPRESS_DOMAIN);

    $step_label = __("Step By:", MASTERPRESS_DOMAIN);
    $step_note = __("Enter a numeric step amount for the up / down buttons or arrow keys.", MASTERPRESS_DOMAIN);

    $format_label = __("Number Format:", MASTERPRESS_DOMAIN);
    $format_note = __("Enter a numeric format to use made up of zeros (0), hashes (#), commas (,) a decimal point, and other symbols.<br />0 represents a decimal that will always be present (generally only used on either side of a decimal point).<br /># is a decimal that is present if needed, and commas and other symbols are placed into the same position.<br />Example: $#,###,###,##0.00 is a currency amount with 2 decimal places always shown,<br />and a leading zero always in front of the decimal point.", MASTERPRESS_DOMAIN);

    $negative_red = self::option_value($options, "negative_red");

    $negative_red_checked_attr = WOOF_HTML::checked_attr($negative_red == "yes");

    $min = self::option_value($options, "min");
    $default = self::option_value($options, "default");
    $max = self::option_value($options, "max");
    $step = self::option_value($options, "step");
    $format = self::option_value($options, "format");

    $maxwidth = MPFT::options_maxwidth( $p, $options );

    $html = <<<HTML

    <div class="f">
      <label for="{$p}min">{$min_label}</label>
      <div id="fw-{$p}min" class="fw">
        <input id="{$p}min" name="type_options[min]" type="text" value="{$min}" class="text" />
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}max">{$max_label}</label>
      <div id="fw-{$p}max" class="fw">
        <input id="{$p}max" name="type_options[max]" type="text" value="{$max}" class="text" />
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}default">{$default_label}</label>
      <div id="fw-{$p}default" class="fw">
        <input id="{$p}default" name="type_options[default]" type="text" value="{$default}" class="text" />
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}step">{$step_label}</label>
      <div id="fw-{$p}step" class="fw">
        <input id="{$p}step" name="type_options[step]" type="text" value="{$step}" class="text" />
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}format">{$format_label}</label>
      <div id="fw-{$p}format" class="fw">
        <input id="{$p}format" name="type_options[format]" type="text" value="{$format}" class="text" />
        <p class="note">{$format_note}</p>
      </div>
    </div>
    <!-- /.f -->

    {$maxwidth}

    <div class="f">
      <label for="{$p}negative_red">{$negative_red_label}</label>
      <div id="fw-{$p}negative_red" class="fw">
        <input id="{$p}negative_red" name="type_options[negative_red]" type="checkbox" {$negative_red_checked_attr} value="yes" class="checkbox" />
      </div>
    </div>
    <!-- /.f -->



HTML;

    return $html;

  }




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: empty_summary 
      Returns the HTML to render the EMPTY summary for this field type. The "summary" is the grid block for this field in the collapsed view of the set it belongs to.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */
  
  public static function empty_summary( MEOW_Field $field ) {
    return __("( no content )", MASTERPRESS_DOMAIN);
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
    return array(
      "step", "min", "max", "format", "negative_red"
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

    // $field here is a MEOW_Field, which is a class that encapsulates the value of a field AND the info associated with it

    $options = $field->info->type_options;
    
    $maxlength = "";
    $font = "";
    $maxwidth = "";
    $default = "";

    $readonly = WOOF_HTML::readonly_attr( !$field->is_editable() );

    $maxwidth = self::option_value($options, "maxwidth");

    if (isset($options["maxlength"])) {
      $maxlength = $options["maxlength"];
    }
  
    if (isset($options["font"])) {
      $font = $options["font"];
    }
  
    if (isset($options["default"])) {
      $default = $options["default"];
    }
  
    if (is_numeric($maxwidth)) {
      $maxwidth = "{$maxwidth}px";
    } else {
      $maxwidth = "auto";
    }

    $maxlength_attr = "";
    
    $status = "";

    if ($maxlength && is_numeric($maxlength)) {
      $maxlength_attr = WOOF_HTML::attr("maxlength=$maxlength");

      if (trim($maxwidth) == "") {
        // if the maxlength is set, roughly match the width of the input to the number of characters
        $maxwidth = ($maxlength + 12)."ex";
      }
    }

    $value = $field->value();

    if ($field->blank()) {
      $value = self::option_value($options, "default");
    }
    
    $html = <<<HTML

    <div class="f">
      <div class="input-spinner">
        <input id="{{id}}" name="{{name}}" type="text" $readonly value="{$value}" autocomplete="off" class="text" {$maxlength_attr} style="max-width: {$maxwidth};" />
        <div class="buttons">
          <button tabindex="-1" type="button" class="ir up"><span>Up</span></button>
          <button tabindex="-1" type="button" class="ir down"><span>Down</span></button>
        </div>
      </div>
    </div>

HTML;

    return $html;

  }

  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
}