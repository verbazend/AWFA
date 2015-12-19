<?php

class MPFT_ListBox extends MPFT {

  private static $values_keys = array(); // cache for summary

  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("List Box", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("List Boxes", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A standard HTML select-multiple control, for selecting one or more specific values from a list", MASTERPRESS_DOMAIN);
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
          "buttons" => "select_all,select_none",
          "width" => 400, 
          "height" => 160,
          "allow_multiple" => "yes" 
      ); 
    }

    $options = wp_parse_args( $options, $defaults );

    $p = self::type_prefix(__CLASS__);


    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $maxwidth_label = __("Maximum Width:", MASTERPRESS_DOMAIN);
    $height_label = __("Height:", MASTERPRESS_DOMAIN);

    $height_note = __("(pixels)", MASTERPRESS_DOMAIN);
    $maxwidth_note = __("(pixels)", MASTERPRESS_DOMAIN);


    $maxwidth = self::option_value($options, "maxwidth");
    $values = self::option_value($options, "values");
    $default_value = self::option_value($options, "default_value");
    $buttons = self::option_value($options, "buttons");
    $height = self::option_value($options, "height");

    $allow_multiple_label = __("Multiple Selections?:", MASTERPRESS_DOMAIN);
    $allow_multiple_checked_attr = WOOF_HTML::checked_attr( $options["allow_multiple"] == "yes" );

    $values_label = __("Choices:", MASTERPRESS_DOMAIN);
    $values_note = __('Specify the labels for each choice on separate lines. To use a different underlying <span class="tt">value</span> for any of the choices,<br /> use the form <strong><span class="tt">label = value</span></strong>. To create a group of choices, prefix the line you wish to use as a group label with <span class="tt">--</span> (group labels are not selectable)', MASTERPRESS_DOMAIN);

    $default_value_label = __("Default State:", MASTERPRESS_DOMAIN);
    $default_value_note = __("Use the preview of your control above to setup the default state", MASTERPRESS_DOMAIN);


    $buttons_label = __("Selection Buttons:", MASTERPRESS_DOMAIN);
    $buttons_checkboxes = WOOF_HTML::input_checkbox_group( "type_options[buttons][]", $p."buttons-", array("Select All" => "select_all", "Select None" => "select_none"), $buttons, WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")); 


    $default_value_attr = array( "id" => $p."default-value", "name" => "type_options[default_value][]" );

    if ($options["allow_multiple"] == "yes") {
      $default_value_attr["multiple"] = "multiple";
    } else {
      $default_value_attr["size"] = 2;
    }


    $default_value_select = WOOF_HTML::select( 
      $default_value_attr,
      WOOF_HTML::option_values($values, "", true),
      $default_value
    );

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
      <label for="{$p}maxwidth">{$maxwidth_label}</label>
      <div id="fw-{$p}maxwidth" class="fw">
        <input id="{$p}maxwidth" type="text" name="type_options[maxwidth]" maxlength="4" value="{$maxwidth}" class="text" /><span class="note">{$maxwidth_note}</span>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}height">{$height_label}</label>
      <div id="fw-{$p}height" class="fw">
        <input id="{$p}height" type="text" name="type_options[height]" value="{$height}" class="text" /><span class="note">{$height_note}</span>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}allow-multiple">{$allow_multiple_label}</label>
      <div id="fw-{$p}allow-multiple" class="fw">
        <input id="{$p}allow-multiple" name="type_options[allow_multiple]" type="checkbox" {$allow_multiple_checked_attr} value="yes" class="checkbox" />
      </div>
    </div>
    <!-- /.f -->


    <div id="{$p}default-value-f" class="f">
      <label for="{$p}default-value">{$default_value_label}</label>
      <div id="fw-{$p}default-value" class="fw">

        <div class="preview">
          {$default_value_select}
          <div id="{$p}default-value-controls" class="controls">
            <button type="button" class="button button-small select-all">Select All</button>
            <button type="button" class="button button-small select-none">Select None</button>
          </div>
          <!-- /.controls -->
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
    
    $options = $field->info->type_options;

    $maxwidth = "";
    $height = "";
    
    $maxwidth = self::option_value($options, "maxwidth");

    if (isset($options["height"])) {
      $height = $options["height"];
    }
  

    if (is_numeric($maxwidth)) {
      $maxwidth = "{$maxwidth}px";
    } else {
      $maxwidth = "580px";
    }
        
    if (is_numeric($height)) {
      $height = "{$height}px";
    } else {
      $height = "160px";
    }

    $size = "";
    
    $select_attr = array(
      "id" => "{{id}}", "name" => "{{name}}", "size" => "2", "style" => "max-width: {$maxwidth}; height: {$height};"
    );

    $allow_multiple = $options["allow_multiple"] == "yes";
    
    if ($allow_multiple) {
      $select_attr["multiple"] = "multiple";
      $select_attr["name"] = "{{name}}[]";
      $select_attr["data-placeholder"] = self::option_value($options, "placeholder", "-- Select an item --");
    }

    $select_none_label = __("Select None", MASTERPRESS_DOMAIN);
    $select_all_label = __("Select All", MASTERPRESS_DOMAIN);


    $values = WOOF_HTML::option_values($field->info->options("values"), "", true);

    $field_values = $field->values();
    $selected_values = $field_values;
    
    if (!is_array($field_values)) {
      $field_values = explode(",", $field_values);
    }
    
    self::$values_keys = array();
    
    if (!$field->blank()) {
      // populate the values
      $selected_values = array();
      
      foreach ($values as $key => $value) {
        
        if ($allow_multiple) {
          
          if (is_array($value)) {
            
            foreach ($value as $sub_key => $sub_value) {
              if (in_array($sub_value, $field_values)) {
                self::$values_keys[] = $sub_key; // cache the keys for the summary, so we don't have to look them up again!
                $selected_values[] = $sub_value;
              }
            }
            

          } else {

            if (in_array($value, $field_values)) {
              self::$values_keys[] = $key; // cache the keys for the summary, so we don't have to look them up again!
              $selected_values[] = $value;
            }

          }
          
        } else {
          
          if (is_array($value)) {
            
            foreach ($value as $sub_key => $sub_value) {
              if ($sub_value == $field_values) {
                self::$values_keys[] = $sub_key; // cache the keys for the summary, so we don't have to look them up again!
                $selected_values[] = $sub_value;
              }
            }
          
          }
          else {
            
            if ($value == $field_values) {
              self::$values_keys[] = $key; // cache the keys for the summary, so we don't have to look them up again!
              $selected_values = $value;
            }
          
          }
          
        }
        
      }

    }
      
    if (!$field->is_editable()) {
      $select_attr["disabled"] = "disabled";
      $select_attr["data-placeholder"] = __("-- None Selected --", MASTERPRESS_DOMAIN);
    }

    $basic = self::option_value($options, "basic") == "yes";

      
    $val = implode(",", $field_values);
    
    $select_attr["data-value-input"] = "{{id}}-value-input";
    $input = '<input type="hidden" id="{{id}}-value-input" name="{{name}}" type="hidden" value="'.$val.'" class="select2-hidden" />';
    
    if (!$basic) {
      // ensure the select control does not affect the values posted, the hidden input is responsible for this
      $select_attr["name"] = "src_".$select_attr["name"];
    }
    
    $select = WOOF_HTML::select( 
      $select_attr,
      $values,
      $selected_values
    );

    $buttons = "";

    if ($allow_multiple) {
    
      if (isset($options["buttons"])) {
        if (in_array("select_all", $options["buttons"])) {
          $buttons .= '<button type="button" class="button button-small select-all">'.$select_all_label.'</button>';
        }

        if (in_array("select_none", $options["buttons"])) {
          $buttons .= '<button type="button" class="button button-small select-none">'.$select_none_label.'</button>';
        }
      }
      
    }
  
    $html = "$input $select";
    
    return $html;
    
  }

  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
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
  
  function html() {
    return implode(", ", $this->val());
  }
  
  function contains($value, $strict = false) {
    return in_array($value, $this->values(), $strict);
  }
  
  function values() {
    return $this->val();
  }

  function __toString() {
    return $this->html();
  }

}