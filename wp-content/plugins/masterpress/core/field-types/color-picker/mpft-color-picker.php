<?php


class MPFT_ColorPicker extends MPFT {

  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: JavaScript Enqueues - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: enqueue 
      A callback to enqueue any JavaScript dependencies for this field type. 
      Field-type specific javascript files must be placed directly in the folder for this field type.
      
    Example Implementation:
      > wp_enqueue_script( 'jquery-some-plugin', plugins_url('jquery.some-plugin.js', __FILE__), array("jquery") );
      
  */
  
  public static function enqueue() {
    //if (!MasterPress::has_iris()) {
	    wp_enqueue_script("mp-iris");
    //}
  }



  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Color Picker", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Color Pickers", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __('A color picker powered by Iris', MASTERPRESS_DOMAIN);
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

    $defaults = array();

    if (MPC::is_create()) {
      $defaults = array("default_value" => "#");
    }

    $options = wp_parse_args( $options, $defaults );

    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $value = self::option_value($options, "default_value", "");
    
    $style = "";
    
    if ($value != "") {
      $style = WOOF_HTML::attr(array("style" => "background-color: $value"));
      
    }
    
    $default_value_label = __("Default Color:", MASTERPRESS_DOMAIN);

$html = <<<HTML

    <div class="f">
      <label for="{$p}default-value">{$default_value_label}</label>
      <div id="fw-{$p}default-value" class="fw">
	    <div class="input-wrap">
	       <input id="{$p}default-value" name="type_options[default_value]" maxlength="7" type="text" value="{$options['default_value']}" class="text" />
	       <span id="{$p}colorpreview" {$style}></span>
		</div>
	
        <div id="{$p}colorpicker"></div>

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
    
    $style = "";
    $value = "";

    if (!$field->blank()) {
      $value = $field->value();
      $style = "background-color: ".esc_attr($value).";";
    }

    $html = <<<HTML

    <div class="color-info">
      <span class="well-wrap"><span style="{$style}" class="well"></span>
      </span><span class="value">{$value}</span>
    </div>

HTML;

    return $html;

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

    $readonly = WOOF_HTML::readonly_attr(!$field->is_editable());

    $preview_class = "";
    
    if ($readonly) {
      $preview_class = " readonly";
    }
    
    $value = $field->value();

    $html = <<<HTML

      <div class="fw {$preview_class}">
        <div class="input-wrap">
        <input id="{{id}}" name="{{name}}" $readonly type="text" value="{$value}" class="text" />
        <span class="colorpreview {$preview_class}"></span>
        </div>
        <div class="colorpicker"></div>
      </div>

      <div class="color-info">
        <span class="well-wrap"><span class="well" style="background-color: {$value}"></span></span>
        <span class="value">{$value}</span>
      </div>

HTML;

    return $html;

  }  
  
  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  
}