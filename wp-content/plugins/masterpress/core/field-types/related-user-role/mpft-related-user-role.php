<?php

class MPFT_RelatedUserRole extends MPFT {
  
  private static $values_keys = array(); // cache for summary
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Related User Role(s)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Related User Roles", MASTERPRESS_DOMAIN);
  }
  
  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Allows selection of a related user role from a list of the available roles in your Wordpress site", MASTERPRESS_DOMAIN);
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
    return "Related Object Type";
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

    $user_roles_label = __("Available User Roles:", MASTERPRESS_DOMAIN);

    $user_roles_note = __("Specify the user roles available for selection in the field control", MASTERPRESS_DOMAIN);

    $user_roles_items = array();

    $user_roles = get_editable_roles();

    foreach ($wf->roles() as $role) {
      $user_roles_items[$role->name()] = $role->id();
    }

    $user_roles_checkboxes = WOOF_HTML::input_checkbox_group( "type_options[user_roles][]", $p."user-roles-", $user_roles_items, self::option_value($options, "user_roles"), WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")); 

    $basic = MPFT::options_select_basic( $p, $options );
    $multi_layout = MPFT::options_select_multi_layout( $p, $options );
    $control_style = MPFT::options_select_control_style( $p, $options );
    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $height = MPFT::options_height( $p, $options );
    $placeholder = MPFT::options_placeholder($p, $options);

    $control_selections_label = __("Selection Display - <span>settings for the display of the selected user roles</span>", MASTERPRESS_DOMAIN); 


$html = <<<HTML

    {$control_style}

    <div class="f f-user-roles">
      <p class="label">{$user_roles_label}</p>
      <div class="fw">

      <div id="{$p}user-roles-wrap">
      {$user_roles_checkboxes}
      </div>

      <div id="{$p}user-roles-controls" class="controls">
        <button type="button" class="button button-small select-all">Select All</button>
        <button type="button" class="button button-small select-none">Select None</button>
      </div>
      <!-- /.controls -->

      <p class="note">{$user_roles_note}</p>
      </div>
    </div>
    <!-- /.f -->

    {$basic}
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

    $blank = false;
    $selected_values = array();
    $options_attr = array();
    $values_select = "";
    $values_options = array_flip($value);
    $values_options_attr = array_flip($value);
    $count = -1;
    $multiple = count($options["user_roles"]) > 1;
    $items = array();

    $height = self::option_value($options, "height");
    
    $maxwidth = self::option_value($options, "maxwidth");

    // build a list of users

    self::$values_keys = array();

    foreach ($options["user_roles"] as $role_name) {
      $count++;

      $role = $wf->role($role_name);
      
      if (!$role || is_woof_silent($role)) {
        $role = $wf->role(strtolower($role_name));
      }
      
      if ($role && !is_woof_silent($role)) {
        $title = $role->name();

        $attr = array();

        if (!$blank && in_array($title, $value)) {

          $values_options[$title] = $title;
          $values_options_attr[$title] = $attr;
          $selected_values[] = $title;
        } 

        $items[] = $title;
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


      $placeholder = self::option_value($options, "placeholder", __("-- Select a User Role --", MASTERPRESS_DOMAIN));
    
      if ($basic) {
        $items = array($placeholder => "") + $items;
        array_unshift($options_attr, array());
      } else {
        $items = array("" => "") + $items;
        array_unshift($options_attr, array());
        $select_attr["data-placeholder"] = $placeholder;
      }

    
    } else {
      
      $placeholder = self::option_value($options, "placeholder", __("-- Select User Roles --", MASTERPRESS_DOMAIN));
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
  
  protected $_role;
  protected $_roles;

  public function iterator_items() {
    return $this->roles()->items();
  }

  public function count() {
    return count($this->roles());
  }

  public function offsetGet($index) {
    return $this->roles[$index];
  }

  public function offsetExists($index) {
    return $index <= count($this->roles());
  }

  
  public function get_delegate() {
    return $this->role();
  }

  public function forward($name) {
    return $this->get_delegate()->__get($name);
  }
  
  
  public function roles() {
    global $wf;

    if (!isset($this->_roles)) {
      $value = $this->value();
    
      if (!is_array($value)) {
        $value = array($value);
      }
    
      $roles = $wf->roles->find_by_in("name", $value);
      $this->_roles = $roles->sort_to("name", $value);    
    }
    
    return $this->_roles;
  }
  
  public function role() {
    
    global $wf;

    if (!$this->role) {

      $value = $this->value();
      
      if (!is_array($value)) { // single role relation
        if (!$this->blank()) {
          $this->role = $wf->role($this->value());
        } else {
          $this->role = new WOOF_Silent(__("No role has been set for this field", MASTERPRESS_DOMAIN));
        }  
      } else {
        // grab the first role
        
        $roles = $this->roles();
        
        if ($roles->count()) {
          $this->role = $roles->first();
        }
        else {
          $this->role = new WOOF_Silent(__("No role has been set for this field", MASTERPRESS_DOMAIN));
        }        
        
      }
      
    } 
    
    return $this->role;    
  }


  public function change() {
    unset($this->_roles, $this->role);
  }

  public function value_for_set($value) {
    
    global $wf;

    // make sure this is a valid value for the types available in this control
    
    $roles = $this->field()->info->type_options["roles"];

    $values = $this->as_array( $value );
    
    if ($this->is_multi_select()) {
      
      $ret = array();
      
      foreach ($values as $val) {
        $role = $wf->role($val);
        
        if ($role->exists()) {
          $ret[] = $role->name;
        }
      
      }
      
      return $ret;
      
    } 
    else {
      
      $value = $values[0];
      
      $role = $wf->role($value);
    
      if ($role->exists()) {
        return $role->name;
      }
    
    }

    return "";
    
  }
  
  
}