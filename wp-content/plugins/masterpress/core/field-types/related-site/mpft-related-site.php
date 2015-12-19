<?php

class MPFT_RelatedSite extends MPFT {
  
  private static $values_keys = array(); // cache for summary
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Related Site(s)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Related Sites", MASTERPRESS_DOMAIN);
  }
  
  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Allows selection of a related site from a list of the available sites in your Wordpress multisite network", MASTERPRESS_DOMAIN);
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
    return "Related Object";
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

    global $wf;

    $defaults = array();

    if (MPC::is_create()) {
      $defaults = array("maxwidth" => 580, "height" => 200);
    }

    $options = wp_parse_args( $options, $defaults );

    $p = self::type_prefix(__CLASS__);

    // setup variables to insert into the heredoc string
    // (this is required since we cannot call functions within heredoc strings)

    $exclude_current = MPFT::options_exclude_current( $p, $options, __('Check to exclude the current site from this list', MASTERPRESS_DOMAIN) );

    $basic = MPFT::options_select_basic( $p, $options );
    $multi_layout = MPFT::options_select_multi_layout( $p, $options );
    $control_style = MPFT::options_select_control_style( $p, $options );
    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $height = MPFT::options_height( $p, $options );
    $placeholder = MPFT::options_placeholder($p, $options);

    $control_selections_label = __("Selection Display - <span>settings for the display of the selected sites</span>", MASTERPRESS_DOMAIN); 


$html = <<<HTML

    {$control_style}
    {$basic}
    {$exclude_current}
    {$placeholder}
    {$maxwidth}

    <div id="{$p}control-selections-wrap" class="divider">
    <h4><i class="buttons"></i>{$control_selections_label}</h4>  
    {$multi_layout}
    </div>
    
HTML;

    return $html;

  }




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: ui_options 
      Returns an array of keys of the type options in the field definition which should be passed through to the JavaScript MPFT widget.
      accessible as a ui_options hash in the jQuery UI widget. The default behaviour is not to pass any of these through, and you
      should avoid passing options that are richly typed, as they are passed through in class-attribute metadata on the field ui element.
      
    Returns:
      Array - of string keys for options required. 
  */

  public static function ui_options() {
    return "control_style,multi_layout,basic";
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
  
  
  /*
    Static Method: select 
      Gets the select for this control. This is factored out of the ui so it can be used for AJAX refreshing. 
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - an HTML string for the select
      
  */
  
  public static function select($options, $value, $blank = false, $editable = true) {
   
    global $wf;

    $control_style = self::option_value($options, "control_style", "drop_down_list");

    if ($control_style == "dual_list_box") {
      // legacy patch for dual list box - it had bad performance for a large number of items
      $control_style = "list_box_multiple";
    }

    $exclude_current = self::option_value($options, "exclude_current") == "yes";

    $current_site_id = $wf->site()->id();
    
    $blank = false;
    $selected_values = array();
    $options_attr = array();
    $values_select = "";
    $values_options = array_flip($value);
    $values_options_attr = array_flip($value);
    $count = -1;
    $items = array();

    $height = self::option_value($options, "height");
    
    $maxwidth = self::option_value($options, "maxwidth");

    // build a list of sites

    self::$values_keys = array();
    
    foreach ($wf->sites() as $site) {
      
      $count++;

      $title = $site->name();
      
      $sid = $site->id();
      
      $attr = array();

      if (!($exclude_current && $sid == $current_site_id)) {
  
        if (!$blank && in_array($sid, $value)) {
          $values_options[$sid] = $title;
          $values_options_attr[$sid] = $attr;
          $selected_values[] = $sid;
        } 

        $items[$title] = $sid;

      }
    
    }

    self::$values_keys = array_values($values_options);
    
    $select_style = "";

    $select_attr = array("id" => "{{id}}", "name" => "{{name}}");

    if (is_numeric($maxwidth)) {
      $select_style .= "width: 99%; max-width: ".$maxwidth."px;";
    } else {
      if ($control_style == "dual_list_box") {
        $select_style .= "width: 580px;";
      }
    }

    if ($control_style == "list_box_multiple") {
      if (is_numeric($height)) {
        $select_style .= "height: ".$options["height"]."px;";
      } else {
        $select_style .= "height: 250px;";
      }
    }

    if ($control_style == "list_box_multiple") {
      $select_attr["multiple"] = "multiple";
      $select_attr["name"] = "{{name}}[]";
    } 


    if ($select_style != "") {
      $select_attr["style"] = $select_style;
    }


    $basic = self::option_value($options, "basic") == "yes";

    if ($control_style == "drop_down_list") {

      $placeholder = self::option_value($options, "placeholder", __("-- Select a Site --", MASTERPRESS_DOMAIN));
    
      if ($basic) {
        $items = array($placeholder => "") + $items;
        array_unshift($options_attr, array());
      } else {
        $items = array("" => "") + $items;
        array_unshift($options_attr, array());
        $select_attr["data-placeholder"] = $placeholder;
      }

    
    } else {
      
      $placeholder = self::option_value($options, "placeholder", __("-- Select Sites --", MASTERPRESS_DOMAIN));
      $select_attr["data-placeholder"] = $placeholder;
      $select_attr["data-value-input"] = "{{id}}-value-input";

      if (!$basic) {
        // ensure the select control does not affect the values posted, the hidden input is responsible for this
        $select_attr["name"] = "src_".$select_attr["name"];
      }
      
    }


    if (!$editable) {
      $select_attr["data-placeholder"] = __("-- None Selected --", MASTERPRESS_DOMAIN);
      $select_attr["disabled"] = "disabled";
    }

    return WOOF_HTML::select( 
      $select_attr,
      $items,
      $selected_values,
      $options_attr
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
    return MPFT::select_ui( $field, __CLASS__ );
  }



  
  // - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post (AJAX) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public static function refresh() {

    global $wf;

    $values = json_decode(stripslashes($_REQUEST["values"]), true);
    $model_id = $_REQUEST["model_id"];

    if (!is_array($values)) {
      $values = array($values);
    }
    
    $field = MPM_Field::find_by_id($model_id);
    
    if ($field) {
      $selects = self::selects($field->type_options, $values);
      $info["select"] = WOOF::render_template($selects["select"], array("id" => $_REQUEST["id"], "name" => str_replace("[]", "", $_REQUEST["name"])  ) );
      self::ajax_success( $info );
    }
    
  }
    

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  protected $_site;
  protected $_sites;

  public function iterator_items() {
    return $this->sites()->items();
  }

  public function count() {
    return count($this->sites());
  }

  public function offsetGet($index) {
    return $this->sites[$index];
  }

  public function offsetExists($index) {
    return $index <= count($this->sites());
  }

  
  public function get_delegate() {
    return $this->site();
  }

  public function forward($name) {
    return $this->get_delegate()->__get($name);
  }
  
  
  public function sites() {
    global $wf;

    if (!isset($this->_sites)) {
      $value = $this->value();
    
      if (!is_array($value)) {
        $value = array($value);
      }
    
      $this->_sites = $wf->sites->find_by_in("id", $value);
    }
    
    return $this->_sites;
  }
  
  public function site() {
    
    global $wf;

    if (!$this->_site) {

      $value = $this->value();
      
      if (!is_array($value)) { // single site relation
        if (!$this->blank()) {
          
          $this->_site = $wf->site($this->value());
        
        } else {
          $this->_site = new WOOF_Silent(__("No site has been set for this field", MASTERPRESS_DOMAIN));
        }  
      } else {
        // grab the first site
        
        $sites = $this->sites();
        
        if ($sites->count()) {
          $this->_site = $sites->first();
        }
        else {
          $this->_site = new WOOF_Silent(__("No site has been set for this field", MASTERPRESS_DOMAIN));
        }         

      }
      
    } 
    
    return $this->_site;    
  }


  public function change() {
    unset($this->_sites, $this->site);
  }

  public function value_for_set($value) {
    
    global $wf;

    // make sure this is a valid value for the types available in this control
    
    $sites = $this->field()->info->type_options["sites"];

    $values = $this->as_array( $value );
    
    if ($this->is_multi_select()) {
      
      $ret = array();
      
      foreach ($values as $val) {
        $site = $wf->site($val);
        
        if ($site->exists()) {
          $ret[] = $site->id;
        }
      
      }
      
      return $ret;
      
    } 
    else {
      
      $value = $values[0];
      
      $site = $wf->site($value);
    
      if ($site->exists()) {
        return $site->id;
      }
    
    }

    return "";
    
  }
  
  
}