<?php

class MPFT_TextBoxMultiline extends MPFT {

  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Text Box (Multiple-line)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Text Boxes (Multiple-line)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A standard HTML text area (multiple-line) control", MASTERPRESS_DOMAIN);
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
      $defaults = array("height" => 160);
    }

    $options = wp_parse_args( $options, $defaults );

    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $maxlength_label = __("Maximum Length:", MASTERPRESS_DOMAIN);
    $maxlength_note = __("(characters)", MASTERPRESS_DOMAIN);

    $status_label = __("show a count of remaining characters", MASTERPRESS_DOMAIN);

    $status = self::option_value($options, "status");
    $maxlength = self::option_value($options, "maxlength");

    $status_checked_attr = WOOF_HTML::checked_attr($status == "yes");


    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $height = MPFT::options_height( $p, $options );
    $font = MPFT::options_font( $p, $options );

$html = <<<HTML

    <div class="f">
      <label for="{$p}maxlength">{$maxlength_label}</label>
      <div id="fw-{$p}maxlength" class="fw">
        <input id="{$p}maxlength" type="text" name="type_options[maxlength]" value="{$maxlength}" class="text" /><span class="note">{$maxlength_note}</span>

        <div id="{$p}status-wrap">
          <input id="{$p}status" type="checkbox" name="type_options[status]" {$status_checked_attr} value="yes" class="checkbox" />
          <label for="{$p}status" class="checkbox">{$status_label}</label>
        </div>

      </div>
    </div>
    <!-- /.f -->

    {$maxwidth}
    {$height}
    {$font}

HTML;

    return $html;

  }




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: summary_width 
      Return an integer value of how many grid units the field summary should occupy in summaries for this set. 
      
    Returns:
      integer - value must be in the range 1 to 4 
  */

  public static function summary_width() {
    return 2;
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
      return nl2br(self::truncate_for_summary(self::summary_width(), $field->value()));
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
    Static Method: ui_preserve_whitespace
      Returns true if the the HTML UI for this field type should have white-space preserved.
      By default base the MPFT class implements this to return FALSE by default, but you should implement the method to return TRUE
      if your field type contains an editor (textarea, etc) that requires line breaks and whitespace preserved.
      
    Returns:
      Boolean
  */

  public static function ui_preserve_whitespace() {
    return true;
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

    $options = $field->info->type_options;
    
    $readonly = WOOF_HTML::readonly_attr(!$field->is_editable());

    $font = self::option_value($options, "font");
    $maxwidth = self::option_value($options, "maxwidth");
    $height = self::option_value($options, "height");
    $status = self::option_value($options, "status");
    
    $maxlength = self::option_value($options, "maxlength");

    if (is_numeric($height)) {
      $height = "{$height}px";
    } else {
      $height = "220px";
    }

    if (is_numeric($maxwidth)) {
      $maxwidth = "max-width: {$maxwidth}px; ";
    }

    $meta = "";
    $status = "";

    if (is_numeric($maxlength)) {
      $meta = " { maxlength: $maxlength }";

      if ($status == "yes") {
        $status = '<div class="status">&nbsp;</div>';
      }
    }

    $value = htmlspecialchars($field->value());

    $html = <<<HTML

    <div class="f">  
      <textarea id="{{id}}" name="{{name}}" type="text" value="{{value}}" {$readonly} class="text{$meta}" style="height: {$height}; min-height: {$height}; {$maxwidth} font-family: {$font}">{$value}</textarea>
      {$status}
    </div>

HTML;


    return $html;

  }

  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

}