<?php

class MPFT_TextBox extends MPFT {

  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Text Box", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Text Box", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A standard HTML single-line text input control", MASTERPRESS_DOMAIN);
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
    return "Text Content";
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

    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $maxlength_label = __("Maximum Length:", MASTERPRESS_DOMAIN);
    $maxlength_note = __("(characters)", MASTERPRESS_DOMAIN);
    $maxlength = self::option_value($options, "maxlength");
    
    $status_checked_attr = WOOF_HTML::checked_attr(self::option_value($options, "status") == "yes");
    $status_label = __("show a count of remaining characters", MASTERPRESS_DOMAIN);

    $default_value_label = __("Default Value:", MASTERPRESS_DOMAIN);
    $default_value_note = __("Use the preview of your control above to set a default value", MASTERPRESS_DOMAIN);
    $default_value = self::option_value($options, "default_value");

    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $font = MPFT::options_font( $p, $options );

$html = <<<HTML

    <div class="f">
      <label for="{$p}maxlength">{$maxlength_label}</label>
      <div id="fw-{$p}maxlength" class="fw">
        <input id="{$p}maxlength" name="type_options[maxlength]" type="text" value="{$maxlength}" class="text" /><span class="note">{$maxlength_note}</span>

        <div id="{$p}status-wrap">
          <input id="{$p}status" type="checkbox" name="type_options[status]" {$status_checked_attr} value="yes" class="checkbox" />
          <label for="{$p}status" class="checkbox">{$status_label}</label>
        </div>

      </div>
    </div>
    <!-- /.f -->

    {$maxwidth}
    {$font}

    <div class="f">
      <label for="{$p}default_value">{$default_value_label}</label>
      <div id="fw-{$p}default-value" class="fw">
        <input id="{$p}default-value" name="type_options[default_value]" type="text" value="{$default_value}" class="text" />
        <p class="note">{$default_value_note}</p>
      </div>
    </div>
    <!-- /.f -->


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
      return self::truncate_for_summary(self::summary_width(), $field->value());
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
    return __("( no content )", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: ui_lang 
      Returns an array of language strings accessable via the "lang" property of the mpft-based JavaScript widget 
      
    Returns:
      array - an array of strings. These strings should be prepared for i18n with Wordpress' __() function
  */
   
  public static function ui_lang() {
    return array(
      "characters_remaining" => __("%d characters remaining", MASTERPRESS_DOMAIN),
      "character_remaining" => __("%d character remaining", MASTERPRESS_DOMAIN),
      "no_characters_remaining" => __("no characters remaining", MASTERPRESS_DOMAIN)
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

    $readonly = WOOF_HTML::readonly_attr(!$field->is_editable());
    
    $options = $field->info->type_options;
    
    $maxlength = self::option_value($options, "maxlength");
    $font = self::option_value($options, "font");
    $status = self::option_value($options, "status");
    $maxwidth = self::option_value($options, "maxwidth");

    $maxlength_attr = "";

    if (is_numeric($maxwidth)) {
      $maxwidth = "{$maxwidth}px";
    } else {
      $maxwidth = "auto";
    }

    $status = "";

    if ($maxlength && is_numeric($maxlength)) {
      $maxlength_attr = WOOF_HTML::attr("maxlength=$maxlength");

      if ($status == "yes") {
        $status = '<div class="status">&nbsp;</div>';
      }

      if (trim($maxwidth) == "") {
        // if the maxlength is set, roughly match the width of the input to the number of characters
        $maxwidth = ($maxlength + 12)."ex";
      }
    }

    $value = htmlspecialchars($field->value());

    $html = <<<HTML

    <div class="f">
      <input id="{{id}}" name="{{name}}" type="text" $readonly value="{$value}" class="text" {$maxlength_attr} style="max-width: {$maxwidth}; font-family: {$font}" />
      $status
    </div>

HTML;

    return $html;

  }

  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
}