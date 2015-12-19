<?php

class MPFT_RelatedUser extends MPFT {
  
  private static $values_keys = array(); // cache for summary
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Related User(s)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Related Users", MASTERPRESS_DOMAIN);
  }
  
  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Allows selection of one or more related users from a list of Wordpress users for this site", MASTERPRESS_DOMAIN);
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

    global $meow_provider;

    $defaults = array();

    if (MPC::is_create()) {
      $defaults = array("maxwidth" => 580, "height" => 200);
    }

    $options = wp_parse_args( $options, $defaults );

    $p = self::type_prefix(__CLASS__);


    // setup variables to insert into the heredoc string
    // (this is required since we cannot call functions within heredoc strings)

    $user_roles_label = __("Available User Roles:", MASTERPRESS_DOMAIN);

    $user_roles_note = __("Specify the roles that users must have to be available for selection in the field control", MASTERPRESS_DOMAIN);

    $user_roles_items = array();

    $user_roles = $wf->roles();

    $user_roles_selected = array();
    
    foreach ($user_roles as $role) {
      $user_roles_selected[] = $role->id();
      $user_roles_items[$role->name()] = $role->id();
    }

    if (!MPC::is_create()) {
      $user_roles_selected = self::option_value($options, "user_roles");  
    }
    
    $user_roles_checkboxes = WOOF_HTML::input_checkbox_group( "type_options[user_roles][]", $p."user-roles-", $user_roles_items, $user_roles_selected, WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")); 

    $ex_label = __("Details", MASTERPRESS_DOMAIN);
    $title_label = __("Name", MASTERPRESS_DOMAIN);

    $results_row_style = MPFT::options_select_results_row_style( $p, $options, array("excerpt" => $ex_label, "title" => $title_label ) );

    $basic = MPFT::options_select_basic( $p, $options );
    $multi_layout = MPFT::options_select_multi_layout( $p, $options );
    $control_style = MPFT::options_select_control_style( $p, $options );
    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $grouping = MPFT::options_select_grouping( $p, $options, __("Role", MASTERPRESS_DOMAIN) );
    
    $results_input_length = MPFT::options_input_text( $p, $options, "results_input_length", __("Minimum Input Length", MASTERPRESS_DOMAIN), __("Enter the number of characters required before any results are displayed.<br />This is useful for large numbers of posts, where performance may become poor.", MASTERPRESS_DOMAIN));

    $control_options_label = __("Control Options", MASTERPRESS_DOMAIN); 
    $control_results_label = __("Results Display - <span>settings for the display of the available users</span>", MASTERPRESS_DOMAIN); 
    $control_selections_label = __("Selection Display - <span>settings for the display of the selected users</span>", MASTERPRESS_DOMAIN); 


    $placeholder = MPFT::options_placeholder($p, $options);


// build a taxonomies grid
    
    $results_row_item_prop_label = __("Row Item Properties:", MASTERPRESS_DOMAIN);
    $results_row_item_prop_note = __("Defines the user properties used to derive the info shown in result rows.<br /><b>Note: </b>Details and images will be truncated / resized automatically.", MASTERPRESS_DOMAIN);
    
    
    $row_style = self::option_value($options, "row_style", "icon_title");

    $grid  = WOOF_HTML::open("table", "class=grid mini not-selectable grid-row-item-prop&cellspacing=0");
      $grid .= WOOF_HTML::open("thead");
      
        $grid .= WOOF_HTML::tag("th", "class=icon user-role", WOOF_HTML::tag("i", "class=user-role", "") . WOOF_HTML::tag("span", null, __("User Role", MASTERPRESS_DOMAIN)));
        $grid .= WOOF_HTML::tag("th", "class=icon title", WOOF_HTML::tag("i", "class=title-bar", "") . WOOF_HTML::tag("span", null, $title_label));
        $grid .= WOOF_HTML::tag("th", "class=".(($row_style == "icon_title" || $row_style == "image_title") ? "disabled " : "")."icon excerpt", WOOF_HTML::tag("i", "class=content-bar", "") . WOOF_HTML::tag("span", null, $ex_label));
        $grid .= WOOF_HTML::tag("th", "class=".(($row_style == "icon_title" || $row_style == "icon_title_excerpt") ? "disabled " : "")."icon image", WOOF_HTML::tag("i", "class=image", "") . WOOF_HTML::tag("span", null, __("Image", MASTERPRESS_DOMAIN)));

      $grid .= WOOF_HTML::close("thead");

      $grid .= WOOF_HTML::open("tbody");
      
      $count = 1;
      
      
      foreach ($user_roles as $user_role) {
        
        $classes = array("user-role-".$user_role->id);
        
        if ($count == 1) {
          $classes[] = "first";
        }
        
        $attr = array("class" => implode(" ", $classes));
        
        if (!in_array($user_role->id, $user_roles_selected)) {
          $attr["style"] = "display: none;";
        }


        $grid .= WOOF_HTML::open("tr", $attr);

        $count++;
          
          $span = WOOF_HTML::tag("span", array(), $user_role->name());
          
          $grid .= WOOF_HTML::tag("td", "class=first user-role", $span);
          $grid .= WOOF_HTML::open("td", "class=title");
          
            $default = "full_name";
            $value = isset($options["result_row_prop"][$user_role->id]["title"]) ? $options["result_row_prop"][$user_role->id]["title"] : $default;
            
            if ($value == "") {
              $value = $default;
            }
            
            $input_attr = array(
              "type" => "text",
              "name" => "type_options[result_row_prop][".$user_role->id."][title]",
              "class" => "text",
              "value" => $value
            );

            $grid .= WOOF_HTML::open("div");

            $grid .= WOOF_HTML::tag("input", $input_attr);

            $grid .= WOOF_HTML::tag(
              "button", 
              array(
                "type" => "button",
                "class" => "ir",
                "data-dialog" => "dialog-user-role-".$user_role->id,
                "data-filter" => "text",
                "title" => __("Select Field", MASTERPRESS_DOMAIN)
              ),
              "select"
            );

            $grid .= WOOF_HTML::close("div");

          
          $grid .= WOOF_HTML::close("td");
          $grid .= WOOF_HTML::open("td", "class=excerpt".(($row_style == "icon_title" || $row_style == "image_title") ? " disabled" : ""));

            $default = "role_name";
            $value = isset($options["result_row_prop"][$user_role->id]["excerpt"]) ? $options["result_row_prop"][$user_role->id]["excerpt"] : $default;
            
            if ($value == "") {
              $value = $default;
            }

            $input_attr = array(
              "type" => "text",
              "name" => "type_options[result_row_prop][".$user_role->id."][excerpt]",
              "class" => "text",
              "value" => $value
            );

            $grid .= WOOF_HTML::open("div");

            $grid .= WOOF_HTML::tag("input", $input_attr);
       
            $grid .= WOOF_HTML::tag(
              "button", 
              array(
                "type" => "button",
                "class" => "ir",
                "data-dialog" => "dialog-user-role-".$user_role->id,
                "data-filter" => "text",
                "title" => __("Select Field", MASTERPRESS_DOMAIN)
              ),
              "select"
            );

            $grid .= WOOF_HTML::close("div");
       
          
          $grid .= WOOF_HTML::close("td");
          $grid .= WOOF_HTML::open("td", "class=image".(($row_style == "icon_title" || $row_style == "icon_title_excerpt") ? " disabled" : ""));

            $default = "avatar";
            $value = isset($options["result_row_prop"][$user_role->id]["image"]) ? $options["result_row_prop"][$user_role->id]["image"] : $default;
            
            if ($value == "") {
              $value = $default;
            }
            
            $input_attr = array(
              "type" => "text",
              "name" => "type_options[result_row_prop][".$user_role->id."][image]",
              "class" => "text",
              "value" => $value
            );

            $grid .= WOOF_HTML::open("div");

            $grid .= WOOF_HTML::tag("input", $input_attr);
          
            $grid .= WOOF_HTML::tag(
              "button", 
              array(
                "type" => "button",
                "class" => "ir",
                "data-dialog" => "dialog-user-role-".$user_role->id,
                "data-filter" => "image",
                "title" => __("Select Field", MASTERPRESS_DOMAIN)
              ),
              "select"
            );

            $grid .= WOOF_HTML::close("div");
            
          $grid .= WOOF_HTML::close("td");

        $grid .= WOOF_HTML::close("tr");
        
      }

      $grid .= WOOF_HTML::close("tbody");
    $grid .= WOOF_HTML::close("table");
    


    // build dialogs for selecting row properties in the grid
    
    $dialogs = "";
    
    foreach ($user_roles as $user_role) {
      
      $dialogs .= WOOF_HTML::open(
        "div", 
        array(
          "class" => "wp-dialog dialog dialog-fields",
          "id" => "dialog-user-role-".$user_role->id,
          "data-title" => __("Select a Field", MASTERPRESS_DOMAIN)
        )
      );
    
      $fs = $meow_provider->user_role_field_sets($user_role->id);

      $field_options = array();
      $field_options[""] = "";

      $field_options_attr = array("");

      $field_options[__("( Built-in Fields )", MASTERPRESS_DOMAIN)] = array(
        
        "options" => array(
          __("Full Name", MASTERPRESS_DOMAIN) => "full_name",
          __("Role Name", MASTERPRESS_DOMAIN) => "role_name",
          __("Login", MASTERPRESS_DOMAIN) => "login",
          __("Email", MASTERPRESS_DOMAIN) => "email",
          __("Avatar", MASTERPRESS_DOMAIN) => "feature_image"
        ),
        "options_attr" => array(
          array("data-icon" => "mp-icon mp-icon-field-type-text-box", "class" => "text"),
          array("data-icon" => "mp-icon mp-icon-field-type-text-box", "class" => "text"),
          array("data-icon" => "mp-icon mp-icon-field-type-text-box", "class" => "text"),
          array("data-icon" => "mp-icon mp-icon-field-type-text-box", "class" => "text"),
          array("data-icon" => "mp-icon mp-icon-field-type-image", "class" => "image")
        )

      );
        
      foreach ($fs as $set) {
      
        $fo = array();
        $fo_attr = array();
        
        foreach ($set->fields() as $field) {

          if ($type_class = MPFT::type_class($field->type)) {
            $image = call_user_func( array($type_class, "supports_image") ) ? " image" : "";
            $text = call_user_func( array($type_class, "supports_text") ) ? " text" : "";

            $fo[$field->display_label()] = $set->name.".".$field->name;
            $fo_attr[] = $field_options_attr[] = array("class" => $image.$text, "data-icon" => "mp-icon mp-icon-field-type-".$field->type);
          } 

          $field_options[$set->display_label()] = array("options" => $fo, "options_attr" => $fo_attr);

        }

      } 
              
      $dialogs .= WOOF_HTML::select(array("name" => "add-field-column-field-sets", "class" => "with-icons select2-source", "data-placeholder" => __("-- Select a Field --", MASTERPRESS_DOMAIN)), $field_options, "", $field_options_attr);
      $dialogs .= WOOF_HTML::close("div");
      
    }
    
$html = <<<HTML

  
    {$dialogs}

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


    <div class="divider">
    <h4><i class="menu-gray"></i>{$control_results_label}</h4>  
    {$grouping}
    {$results_input_length}
    {$results_row_style}
    
    <div id="{$p}results-row-item-prop-f" class="results-row-item-prop-f f">
    <p class="label">{$results_row_item_prop_label}</p> 
    
    <div class="fw">
      {$grid}
      <p class="note">{$results_row_item_prop_note}</p> 
    </div>
    
    </div>
    <!-- /.f -->
    
    </div>
    
    <div id="{$p}control-selections-wrap" class="divider">
    <h4><i class="buttons"></i>{$control_selections_label}</h4>  
    {$multi_layout}
    </div>
    
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
    Static Method: summary_width 
      Return an integer value of how many grid units the field summary should occupy in summaries for this set. 
      
    Returns:
      integer - value must be in the range 1 to 4 
  */

  public static function summary_width() {
    return 2;
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
    return "control_style,multi_layout,basic,results_input_length";
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
    
    $selected_values = array();
    $items = array();
    $values_select = "";
    $values_options = array_flip($value);
    $values_options_attr = array_flip($value);
    
    $options_attr = array();
    
    $count = -1;
    $multiple = count($options["user_roles"]) > 1;
    self::$values_keys = array();

    $row_style = self::option_value($options, "row_style", "icon_title");
    $result_row_prop = self::option_value($options, "result_row_prop", array());

    $grouping = self::option_value($options, "grouping");


    $maxwidth = self::option_value($options, "maxwidth");

    // build a list of users

    foreach ($options["user_roles"] as $role_id) {
      
      $role = $wf->role($role_id);

      $users = array("options" => array(), "options_attr" => array());

      if ($role && $role->exists()) {
        
        $count++;

        foreach ($role->users() as $user) {
          $count++;

          $uid = $user->id();

          
          $title = self::get_row_prop_title($user, $result_row_prop, $role_id);

          if ($title == "") {
            $title = $user->full_name();
          }

          // ensure there are no duplicate titles

          $tcount = 2;
          
          while (isset($users["options"][$title])) {
            $title = $title . " ( ".$tcount." )";
            $tcount++;
          }
          
          $users["options"][$title] = $uid;

          $attr = array( );

          // retrieve special properties
          
          $attr = array();
          
          if ($row_style == "icon_title_excerpt" || $row_style == "image_title_excerpt") {
            $attr = array_merge( $attr, self::get_row_prop_role_name($user, $result_row_prop, $role_id) );
          }

          if ($row_style == "image_title_excerpt" || $row_style == "image_title") {
            $attr = array_merge( $attr, self::get_row_prop_image($user, $result_row_prop, $role_id, $row_style == "image_title" ? 40 : 60) );
          }
          

          $users["options_attr"][] = $attr;
          
          
          if (in_array($uid, $value)) {
            $selected_values[] = $uid;
            $values_options[$uid] = $title;
            $values_options_attr[$uid] = $attr;
          } 

        }

        if (count($users["options"])) {
          $label = WOOF_Inflector::pluralize($role->name());
          $users["optgroup_attr"] = array("label" => $label, "data-selection-prefix" => $role->name().": ");
          $items[$label] = $users;
        }
      }

    }

    self::$values_keys = array_values($values_options);

    $select_style = "";

    $select_attr = array("id" => "{{id}}", "name" => "{{name}}");

    if (is_numeric($maxwidth)) {
      $select_style .= "width: 99%; max-width: ".$maxwidth."px;";
    } else {
      $select_style .= "width: 580px;";
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
      // add a prompting empty label
      $items = array("" => "") + $items;
      //array_unshift($options_attr, array());
      $select_attr["data-placeholder"] = self::option_value($options, "placeholder", "-- Select a user --");
    } else {
      $select_attr["data-placeholder"] = self::option_value($options, "placeholder", "-- Select users --");
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
  
  protected $_users;
  protected $_user;
  
  public function iterator_items() {
    return $this->users()->items();
  }

  public function count() {
    return count($this->users());
  }

  public function offsetGet($index) {
    return $this->users[$index];
  }

  public function offsetExists($index) {
    return $index <= count($this->users());
  }
  
  public function __toString() {
    return $this->users()->flatten("login");
  }
    
  public function users() {
    global $wf;
    
    if (!isset($this->_users)) {
      $value = $this->value();
    
      if (!is_array($value)) {
        $value = array($value);
      }
    
      $users = $wf->users->find_by_in("id", $value);
      $this->_users = $users->sort_to("id", $value);  
    }
    
    return $this->_users;
  }
  
  public function user() {
    
    global $wf;

    if (!$this->_user) {

      $value = $this->value();
      
      if (!is_array($value)) { // single user relation
        if (!$this->blank()) {
          $this->_user = $wf->user($this->value());
        } else {
          $this->_user = new WOOF_Silent(__("No user has been set for this field", MASTERPRESS_DOMAIN));
        }  
      } else {
        // grab the first user
        
        $users = $this->users();
        
        if ($users->count()) {
          $this->_user = $users->first();
        } else {
          $this->_user = new WOOF_Silent(__("No user has been set for this field", MASTERPRESS_DOMAIN));
        }
        
        
      }
      
    } 
    
    return $this->_user;    
  }
  
  public function json() {
    
    $json = array();
    
    if ($this->is_multi_select()) {

      foreach ($this->users() as $the) {
        $json[] = array("href" => $the->json_href());
      }
      
    } else {
      
      $json["href"] = $this->user->json_href();
    
    }
    
    return $json;
    
  }
  
  public function col() {
    
    if (!$this->blank()) {
      return WOOF_HTML::tag("a", array("href" => admin_url("user-edit.php?user_id=".$this->val())), $this->user->fullname());
    }
    
  }
  
  public function get_delegate() {
    return $this->user();
  }

  public function forward($name) {
    return $this->get_delegate()->__get($name);
  }

  public function change() {
    unset($this->_user, $this->_users);
  }
  
  public function value_for_set($value) {
    
    global $wf;

    // make sure this is a valid value for the types available in this control

    $values = $this->as_array( $value );
    
    if ($this->is_multi_select()) {
      
      $ret = array();
      
      foreach ($values as $val) {
      
        $user = $wf->user($val);
    
        if ($user->exists()) {
          $ret[] = $user->id();
        }
      
      }
      
      return $ret;
      
    } 
    else {

      $value = $values[0];
      
      $user = $wf->user($value);

      if ($user->exists()) {
        return $user->id();
      }
    
    }

    return "";
    
  }


}