<?php

class MPFT_DatePicker extends MPFT {
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Date Picker", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Date Pickers", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A text-input control with a popup calendar for selecting a date and time", MASTERPRESS_DOMAIN);
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
      $defaults = array("mode" => "single");
    }

    $options = wp_parse_args( $options, $defaults );

    $mode = self::option_value($options, "mode");
    
    if ($mode == "") {
      $mode = "single";
    }

    $p = self::type_prefix(__CLASS__);



    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $mode_label = __("Selection Mode:", MASTERPRESS_DOMAIN);

    $mode_select = WOOF_HTML::input_radio_group(
      "type_options[mode]", $p."mode-", 
      array(__("Single Date", MASTERPRESS_DOMAIN) => "single", __("Start / End Date", MASTERPRESS_DOMAIN) => "start_end" ),
      self::option_value($options, "mode"),
      WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")
    );

    $mindate_label = __("Minimum Date:", MASTERPRESS_DOMAIN);
    $maxdate_label = __("Maximum Date:", MASTERPRESS_DOMAIN);
    $default_value_label = __("Default Date:", MASTERPRESS_DOMAIN);

    $time_select_label = __("Allow Time Selection?", MASTERPRESS_DOMAIN);
    $time_select_checked_attr = WOOF_HTML::checked_attr(self::option_value($options, "timeselect") == "yes");

    $date_format_note = __("Note: for minumum, maximum, and default dates, you may also enter a number of days from today (e.g. +7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '-1y -1m').", MASTERPRESS_DOMAIN);

    $button_text = esc_js(__("choose&hellip;", MASTERPRESS_DOMAIN));

    $font_select = WOOF_HTML::select(
      array("id" => $p."font", "name" => "type_options[font]"), 
      array(
        "Sans-Serif&nbsp;&nbsp;-&nbsp;&nbsp;Helvetica, Arial, sans-serif" => "helvetica, arial, sans-serif", 
        "Serif&nbsp;&nbsp;-&nbsp;&nbsp;Georgia, Times New Roman, serif" => "georgia, 'times new roman', serif", 
        "Fixed Width&nbsp;&nbsp;-&nbsp;&nbsp;Consolas, Menlo, Andale Mono, Lucida Console, monospace" => "consolas, menlo, 'andale mono', 'lucida console', monospace"
      ),
      self::option_value($options, "font")
    );

 
    $mindate = esc_attr(self::option_value($options, "mindate"));
    $maxdate = esc_attr(self::option_value($options, "maxdate"));
    $default_value = esc_attr(self::option_value($options, "default_value"));

$html = <<<HTML


    <!--
    TODO - re-enable dual date support (not important right now)
    <div class="f">
      <label for="{$p}mode_select">{$mode_label}</label>
      <div id="fw-{$p}mode_select" class="fw">
        {$mode_select}
      </div>
    </div>
    -->
    <!-- /.f -->

    <div class="f">
      <label for="{$p}timeselect">{$time_select_label}</label>
      <div id="fw-{$p}timeselect" class="fw">
        <input id="{$p}timeselect" type="checkbox" name="type_options[timeselect]" {$time_select_checked_attr} value="yes" class="checkbox" />
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}mindate">{$mindate_label}</label>
      <div id="fw-{$p}mindate" class="fw">
        <input id="{$p}mindate" name="type_options[mindate]" type="text" maxlength="4" value="{$mindate}" class="text date { buttonText: '{$button_text}' }" />
      </div>
    </div>
    <!-- /.f -->


    <div class="f">
      <label for="{$p}maxdate">{$maxdate_label}</label>
      <div id="fw-{$p}maxdate" class="fw">
        <input id="{$p}maxdate" name="type_options[maxdate]" type="text" maxlength="4" value="{$maxdate}" class="text date { buttonText: '{$button_text}' }" />
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}default_value">{$default_value_label}</label>
      <div id="fw-{$p}default_value" class="fw">
        <input id="{$p}default_value" name="type_options[default_value]" type="text" defaultlength="4" value="{$default_value}" class="text date { buttonText: '{$button_text}' }" />
      </div>
    </div>
    <!-- /.f -->

    <p class="note format-note">
      {$date_format_note}
    </p>


HTML;

    return $html;

  }




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: value_for_save
      Transforms the value in the POST data for the control into an appropriate value to store in the database
      
    Arguments: 
      $value - String, the value posted for this control
      $field - MPM_Field, a model object representing the field definition

    Returns:
      mixed - a value that's appropriate for storing in the database
  */

  public static function value_for_save( $value, MPM_Field $field ) {
    $date = strtotime($value);

    if ($date != 0) {
      return date("c", $date);
    }

    return "";

  }

  /*
    Static Method: value_from_load
      Transforms the value loaded from the database into a value appropriate for the control UI
      
    Arguments: 
      $value - String, the value posted for this control
      $field - MPM_Field, a model object representing the field definition

    Returns:
      String - a value that's appropriate for the field UI
  */

  public static function value_from_load( $value, MPM_Field $field ) {

    if ($value != "") {

      $date = strtotime($value);

      if ($date != 0) {
        
        if (isset($field->type_options["timeselect"])) {
          return date("d M Y h:i A", $date);
        } else {
          return date("d M Y", $date);
        }


      }

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
    return array("mode", "mindate", "maxdate", "default_value", "timeselect");
  }

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
    if (isset($options["mode"])) {
      return $options["mode"] != "single";
    }
  
    return false;
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

    $options = $field->info->type_options;

    $mode = "";
    $month = "";
    $year = "";
    $day = "";
    
    if (isset($options["mode"])) {
      $mode = $options["mode"];
    }

    if (!$field->blank()) {

      if ($mode != "start_end") {
        $time = strtotime($field->str());


        if ($time != 0) {
          $month = date("M", $time);
          $year = date("Y", $time);
          $day = date("d", $time);
        }

      }

    }

$html = <<<HTML

      <div class="mp-cal">
        <div class="cal-month-year">
          <span class="month">{$month}</span>&nbsp;
          <span class="year">{$year}</span>
        </div>

        <div class="cal-day">
          <span class="day">{$day}</span>
        </div>

      </div>
HTML;

    if ($mode == "start_end") {
      $html .= <<<HTML

      <div class="mp-cal cal-end">
        <div class="cal-month-year">
          <span class="month-end"></span>&nbsp;
          <span class="year-end"></span>
        </div>

        <div class="cal-day">
          <span class="day-end"></span>
        </div>

      </div>
HTML;
    }

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

    $options = $field->info->type_options;

    $readonly = WOOF_HTML::readonly_attr(!$field->is_editable());

    $value = $field->str();

    $mode = "";
    $year = "";
    $month = "";
    $day = "";

    if (isset($options["mode"])) {
      $mode = $options["mode"];
    }
    
    if ($mode != "start_end") {
      $time = strtotime($value);

      if ($time != 0) {
        $month = date("M", $time);
        $year = date("Y", $time);
        $day = date("d", $time);
      }

    }

    $start_date = __("Start Date:", MASTERPRESS_DOMAIN);
    $end_date = __("End Date:", MASTERPRESS_DOMAIN);

    $html = <<<HTML

    <div class="summary">
      <div class="mp-cal">
        <div class="cal-month-year">
          <span class="month">{$month}</span>&nbsp;
          <span class="year">{$year}</span>
        </div>

        <div class="cal-day">
          <span class="day">{$day}</span>
        </div>

      </div>
HTML;

    if ($mode == "start_end") {
      $html .= <<<HTML

      <div class="mp-cal cal-end">
        <div class="cal-month-year">
          <span class="month-end"></span>&nbsp;
          <span class="year-end"></span>
        </div>

        <div class="cal-day">
          <span class="day-end"></span>
        </div>

      </div>
HTML;

    }

    $html .= "</div>";

    if ($mode == "start_end") {
      $html .= <<<HTML

      <input id="{{id}}" name="{{name}}" {$readonly} type="hidden" value="{$value}" class="value" />

      <div class="wrap-pickers">

      <div class="wrap-picker wrap-picker-start">
        <label for="{{prop_id}}start" class="picker">{$start_date}</label>
        <input id="{{prop_id}}start" name="{{prop_name}}[start]" type="text" value="$value" class="text picker picker-start" />
      </div>

      <div class="wrap-picker wrap-picker-end">
        <label for="{{prop_id}}end" class="picker">{$end_date}</label>
        <input id="{{prop_id}}end" name="{{prop_name}}[end]" type="text" value="$value" class="text picker picker-end" />
      </div>

      </div>

HTML;

    } else {

      $html .= '<input id="{{id}}" name="{{name}}" '.$readonly.'" type="text" value="'.$value.'" class="text value picker" />';

    }

    return $html;

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
    return __("( no date selected )", MASTERPRESS_DOMAIN);
  }

  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public function cal($fallback = "") {
    
    $month = $this->date("[month-short]");
    $year = $this->date("[year]");
    $day = $this->date("[day]");

    $html = $fallback;
    
    if (!$this->blank()) {
$html = <<<HTML

    <div class="mp-cal">
        <div class="cal-month-year">
          <span class="month">{$month}</span>
          <span class="year">{$year}</span>
        </div>

        <div class="cal-day">
          <span class="day">{$day}</span>
        </div>

      </div>
HTML;
       
    } 
    
    return $html;
    
  }
  
  public function col() {
    return $this->cal();
  }
  
  public function json() {
    return $this->date("c");
  }
  
  function date($format = null) {
    // return a PHP date type

    global $wf;
    
    if (!$this->blank()) {
            
      $val = $this->value();
      
      if ($format) {
        return $wf->date_format($format, $val);
      }
    
      return $val;

    } else {

      if ($format) {
        return "";
      } else {
        return 0;
      }

    }
    
  }
  
  function format($format) {
    global $wf;
    
    return $wf->date_format($format, $this->value());
  }

  
  function past() {
    return $this->value() < time();
  }
  
  function future() {
    return $this->value() >= time();
  }
  
  function value() {
    return strtotime($this->field_value());
  }
  
  function value_for_set($value) {
    // allow timestamps and strings
    
    $field = $this->field;
    
    $format = "d M Y";
    
    if (isset($field->info->type_options["timeselect"])) {
      $format = "d M Y h:i A";
    }
        
    if (is_int($value)) {
      $time = $value;
    } else {
      $time = strtotime($value);
    }
    
    return date($format, $time);
    
  }
  
}