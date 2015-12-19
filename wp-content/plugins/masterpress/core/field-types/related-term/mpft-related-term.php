<?php

class MPFT_RelatedTerm extends MPFT {
  
  private static $values_keys = array(); // cache for summary
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Related Term(s)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Related Term(s)", MASTERPRESS_DOMAIN);
  }
  
  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Allows selection of one or more taxonomy terms from a list of terms attached to one or more taxonomies", MASTERPRESS_DOMAIN);
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

    global $meow_provider;

    global $wf;
    
    $p = self::type_prefix(__CLASS__);

    $defaults = array();

    if (MPC::is_create()) {
      $defaults = array("maxwidth" => 580, "height" => 300, "control_style" => "drop_down_list");
    }

    $options = wp_parse_args( $options, $defaults );

    if (MPC::is_create()) {
      $options["post_types"] = array();
    }
      
    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $taxonomies_label = __("Available Taxonomies:", MASTERPRESS_DOMAIN);

    $taxonomies_note = __("Select the taxonomies of terms available for selection in the field control", MASTERPRESS_DOMAIN);

    $taxonomies_items = array();

    MPM::incl("taxonomy");

    $taxonomies = MPM_Taxonomy::find("orderby=disabled,name ASC");

    $taxonomies_selected = array();

    foreach ($taxonomies as $taxonomy) {
    
      if (!$taxonomy->disabled && $taxonomy->show_ui && $taxonomy->still_registered()) {
        $taxonomies_selected[] = $taxonomy->name;
        $taxonomies_items[$taxonomy->display_label()] = $taxonomy->name;
      }

    }

    if (!MPC::is_create()) {
      $taxonomies_selected = self::option_value($options, "taxonomies");
    }

    if (!is_array($taxonomies_selected)) {
      $taxonomies_selected = explode(",", $taxonomies_selected);
    }
  
    $id = $p."taxonomies-";
    
    $taxonomies_checkboxes = "";
    
    foreach ($taxonomies as $taxonomy) {


      if (!$taxonomy->disabled && $taxonomy->show_ui && $taxonomy->still_registered()) {
      
        $value = $taxonomy->name;
        $label = $taxonomy->display_label();
      
        $id_suffix = WOOF::sanitize($value);
      
        $attr = array( "id" => $id."_".$id_suffix, "class" => "checkbox", "type" => "checkbox", "name" => "type_options[taxonomies][]", "value" => $value );
      
        if (in_array($value, $taxonomies_selected)) {
          $attr["checked"] = "checked";
        }
      
        $taxonomies_checkboxes .= WOOF_HTML::open("div", "class=fwi");
        $taxonomies_checkboxes .= WOOF_HTML::tag("input", $attr );
        $taxonomies_checkboxes .= WOOF_HTML::tag("label", array( "for" => $id."_".$id_suffix, "class" => "checkbox mp-icon-taxonomy mp-icon-taxonomy-".$value ), $label );
        $taxonomies_checkboxes .= WOOF_HTML::close("div");

      }

    }
    
    $ex_label = __("Description", MASTERPRESS_DOMAIN);
    $title_label = __("Name", MASTERPRESS_DOMAIN);
    
    $results_row_style = MPFT::options_select_results_row_style( $p, $options, array("excerpt" => $ex_label, "title" => $title_label ) );

    $placeholder = MPFT::options_placeholder($p, $options);

    $basic = MPFT::options_select_basic( $p, $options );
    $multi_layout = MPFT::options_select_multi_layout( $p, $options );
    $control_style = MPFT::options_select_control_style( $p, $options );
    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $height = MPFT::options_height( $p, $options );
    $grouping = MPFT::options_select_grouping( $p, $options, __("Taxonomy", MASTERPRESS_DOMAIN) );

    $results_input_length = MPFT::options_input_text( $p, $options, "results_input_length", __("Minimum Input Length", MASTERPRESS_DOMAIN), __("Enter the number of characters required before any results are displayed.<br />This is useful for large numbers of posts, where performance may become poor.", MASTERPRESS_DOMAIN));


    $data_source_label = __("Data Source - <span>specify the terms available for selection</span>", MASTERPRESS_DOMAIN); 
    $control_options_label = __("Control Options", MASTERPRESS_DOMAIN); 
    $control_results_label = __("Results Display - <span>settings for the display of the available terms</span>", MASTERPRESS_DOMAIN); 
    $control_selections_label = __("Selection Display - <span>settings for the display of the selected terms</span>", MASTERPRESS_DOMAIN); 



    // build a taxonomies grid
    
    $results_row_item_prop_label = __("Row Item Properties:", MASTERPRESS_DOMAIN);
    $results_row_item_prop_note = __("Defines the term properties used to derive the info shown in result rows.<br /><b>Note: </b>Descriptions and images will be truncated / resized automatically.", MASTERPRESS_DOMAIN);
    
    
    $row_style = self::option_value($options, "row_style", "icon_title");

    $grid  = WOOF_HTML::open("table", "class=grid mini not-selectable grid-row-item-prop&cellspacing=0");
      $grid .= WOOF_HTML::open("thead");
      
        $grid .= WOOF_HTML::tag("th", "class=icon taxonomy", WOOF_HTML::tag("i", "class=tags", "") . WOOF_HTML::tag("span", null, __("Taxonomy", MASTERPRESS_DOMAIN)));

        $grid .= WOOF_HTML::tag("th", "class=icon title", WOOF_HTML::tag("i", "class=title-bar", "") . WOOF_HTML::tag("span", null, $title_label));
        $grid .= WOOF_HTML::tag("th", "class=".(($row_style == "icon_title" || $row_style == "image_title") ? "disabled " : "")."icon excerpt", WOOF_HTML::tag("i", "class=content-bar", "") . WOOF_HTML::tag("span", null, $ex_label));
        $grid .= WOOF_HTML::tag("th", "class=".(($row_style == "icon_title" || $row_style == "icon_title_excerpt") ? "disabled " : "")."icon image", WOOF_HTML::tag("i", "class=image", "") . WOOF_HTML::tag("span", null, __("Image", MASTERPRESS_DOMAIN)));



      $grid .= WOOF_HTML::close("thead");

      $grid .= WOOF_HTML::open("tbody");
      
      $count = 1;
      
      
      foreach ($taxonomies as $taxonomy) {
        if (!$taxonomy->disabled && $taxonomy->show_ui) {
          
          $classes = array("taxonomy-".$taxonomy->name);
          
          if ($count == 1) {
            $classes[] = "first";
          }
          
          $attr = array("class" => implode(" ", $classes));
          
          if (!in_array($taxonomy->name, $taxonomies_selected)) {
            $attr["style"] = "display: none;";
          }

  
          $grid .= WOOF_HTML::open("tr", $attr);

          $count++;
            
            $span = WOOF_HTML::tag("span", array("class" => "mp-icon-taxonomy mp-icon-taxonomy-".$taxonomy->name ), $taxonomy->display_label());
            
            $grid .= WOOF_HTML::tag("td", "class=first taxonomy", $span);
            $grid .= WOOF_HTML::open("td", "class=title");
            
              $default = "name";
              $value = isset($options["result_row_prop"][$taxonomy->name]["title"]) ? $options["result_row_prop"][$taxonomy->name]["title"] : $default;
              
              if ($value == "") {
                $value = $default;
              }
              
              $input_attr = array(
                "type" => "text",
                "name" => "type_options[result_row_prop][".$taxonomy->name."][title]",
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
                  "data-dialog" => "dialog-taxonomy-".$taxonomy->name,
                  "data-filter" => "text",
                  "title" => __("Select Field", MASTERPRESS_DOMAIN)
                ),
                "select"
              );

              $grid .= WOOF_HTML::close("div");
            
            $grid .= WOOF_HTML::close("td");
            $grid .= WOOF_HTML::open("td", "class=excerpt".(($row_style == "icon_title" || $row_style == "image_title") ? " disabled" : ""));

              $default = "description";
              $value = isset($options["result_row_prop"][$taxonomy->name]["excerpt"]) ? $options["result_row_prop"][$taxonomy->name]["excerpt"] : $default;
              
              if ($value == "") {
                $value = $default;
              }

              $input_attr = array(
                "type" => "text",
                "name" => "type_options[result_row_prop][".$taxonomy->name."][excerpt]",
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
                  "data-dialog" => "dialog-taxonomy-".$taxonomy->name,
                  "data-filter" => "text",
                  "title" => __("Select Field", MASTERPRESS_DOMAIN)
                ),
                "select"
              );

              $grid .= WOOF_HTML::close("div");
                          
            $grid .= WOOF_HTML::close("td");
            $grid .= WOOF_HTML::open("td", "class=image".(($row_style == "icon_title" || $row_style == "icon_title_excerpt") ? " disabled" : ""));

              $default = "";
              $value = isset($options["result_row_prop"][$taxonomy->name]["image"]) ? $options["result_row_prop"][$taxonomy->name]["image"] : $default;
              
              if ($value == "") {
                $value = $default;
              }
              
              $input_attr = array(
                "type" => "text",
                "name" => "type_options[result_row_prop][".$taxonomy->name."][image]",
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
                  "data-dialog" => "dialog-taxonomy-".$taxonomy->name,
                  "data-filter" => "image",
                  "title" => __("Select Field", MASTERPRESS_DOMAIN)
                ),
                "select"
              );

              $grid .= WOOF_HTML::close("div");
              
            $grid .= WOOF_HTML::close("td");

          $grid .= WOOF_HTML::close("tr");
          
        }
      }

      $grid .= WOOF_HTML::close("tbody");
    $grid .= WOOF_HTML::close("table");
  
    
    // build dialogs for selecting row properties in the grid
    
    $dialogs = "";
    
    foreach ($taxonomies as $taxonomy) {
      
      if (!$taxonomy->disabled && $taxonomy->show_ui) {
      
        $dialogs .= WOOF_HTML::open(
          "div", 
          array(
            "class" => "dialog dialog-fields",
            "id" => "dialog-taxonomy-".$taxonomy->name,
            "data-title" => __("Select a Field", MASTERPRESS_DOMAIN)
          )
        );
      
        $fs = $meow_provider->taxonomy_field_sets($taxonomy->name);

        $field_options = array();
        $field_options[""] = "";

        $field_options_attr = array("");

        $field_options[__("( Built-in Fields )", MASTERPRESS_DOMAIN)] = array(
          
          "options" => array(
            __("Name", MASTERPRESS_DOMAIN) => "name",
            __("Description", MASTERPRESS_DOMAIN) => "description"
          ),
          "options_attr" => array(
            array("data-icon" => "mp-icon mp-icon-field-type-text-box", "class" => "text"),
            array("data-icon" => "mp-icon mp-icon-field-type-text-box-multiline", "class" => "text")
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
      
    }
    


$html = <<<HTML

    {$dialogs}
    {$control_style}

    <div class="f f-taxonomies">
      <p class="label">{$taxonomies_label}</p>
      <div class="fw">

      <div id="{$p}taxonomies-wrap">
      {$taxonomies_checkboxes}
      </div>

      <div id="{$p}taxonomies-controls" class="controls">
        <button type="button" class="button button-small select-all">Select All</button>
        <button type="button" class="button button-small select-none">Select None</button>
      </div>
      <!-- /.controls -->

      <p class="note">{$taxonomies_note}</p>
      </div>
    </div>
    <!-- /.f -->
    
    {$placeholder}
    {$basic}
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

  public static function term_parent_names($term, $result_row_prop, $tax_name, $parents = array()) {
    $parents[] = self::get_row_prop_title($term, $result_row_prop, $tax_name);
      
    if ($term->has_parent()) {
      return self::term_parent_names($term->parent(), $result_row_prop, $tax_name, $parents);
    }

    return array_reverse($parents);
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
    Static Method: summary_label_classes
      Returns an array of classes to apply to the label in the field summary (the grid block in the collapsed view of the field set)
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>

    Returns:
      Array - of strings
  */

  public static function summary_label_classes( MEOW_Field $field ) {

    $options = $field->info->type_options;
    
    $taxonomies = self::option_value($options, "taxonomies");
    
    // if we are using a single taxonomy, use the icon for that taxonomy

    if (count($taxonomies) == 1) {
      return array("mp-icon-taxonomy mp-icon-taxonomy-".$taxonomies[0]);
    }
    
    return array();
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

    $maxwidth = self::option_value($options, "maxwidth", 580);

    $selected_values = array();
    $items = array();
    $options_attr = array();
    $values_select = "";
    $values_options = array_flip($value);
    $values_options_attr = array_flip($value);
    $count = -1;
    $multiple = count($options["taxonomies"]) > 1;
    self::$values_keys = array();

    $result_row_prop = self::option_value($options, "result_row_prop", array());

    $row_style = self::option_value($options, "row_style", "icon_title");

    $max = 0;
    
    $grouping = self::option_value($options, "grouping");

    // build a list of related taxonomies

    if ($grouping == "flat" || !$multiple) {

      $query = array();
      
      
      foreach( $options["taxonomies"] as $tax_name) {
        
        $tax = $wf->taxonomy($tax_name);
        
        if ($tax->hierarchical) {
          $all = $tax->flatten_terms($query);
        } else {
          $all = $tax->terms($query);
        }
        
        foreach ($all as $term) {
        
          //$tax = $term->taxonomy();

          $count++;
        
          $pn = self::term_parent_names($term, $result_row_prop, $tax_name);

          $title = implode(" &rarr; ", $pn);
        
          if ($title == "") {
            $title = __("( no title )", MASTERPRESS_DOMAIN);
          } 

          // ensure there are no duplicate titles (causes bugs which are VERY hard to understand)
      
          $tcount = 2;
    
          while (isset($terms["options"][$title])) {
            $title = $title . " ( ".$tcount." )";
            $tcount++;
          }


          $pid = $tax_name.":".$term->id();

          $attr = array();
  		$data_icon = "mp-icon-taxonomy mp-icon-taxonomy-".$tax_name;

          // retrieve special properties

  		    $attr["data-icon"] = $data_icon;
  		    
  		    if ($term->has_parent()) {
  		      $attr["class"] = "child";
          }
        
          if ($row_style == "icon_title_excerpt" || $row_style == "image_title_excerpt") {
            $attr = array_merge( $attr, self::get_row_prop_desc($term, $result_row_prop, $tax_name) );
          }

          if ($row_style == "image_title_excerpt" || $row_style == "image_title") {
            $attr = array_merge( $attr, self::get_row_prop_image($term, $result_row_prop, $tax_name, $row_style == "image_title" ? 40 : 60) );
          }
                    
          $items[$title] = $pid;
          $options_attr[] = $attr;


          if (!$blank && in_array($pid, $value)) {
            $values_options[$pid] = $title;
            $values_options_attr[$pid] = $attr;
            $selected_values[] = $pid;
  		  $select_attr["data-icon"] = $data_icon;
          } 
      

        }
      
      } // foreach taxonomies 


    } else { 
    
      
      foreach ($options["taxonomies"] as $tax_name) {

        $taxonomy = $wf->taxonomy($tax_name);

        if ($taxonomy->exists()) { 

          $count++;

          $tax_name = $taxonomy->name();
        
          // build an optgroup list
          $terms = array("options" => array(), "options_attr" => array());


          if ($taxonomy->hierarchical) {
            $all = $taxonomy->flatten_terms();
          } else {
            $all = $taxonomy->terms();
          }
        

          foreach ($all as $term) {
            $count++;

            $pn = self::term_parent_names($term, $result_row_prop, $tax_name);

            $title = implode(" &rarr; ", $pn);
          
            // ensure there are no duplicate titles (causes bugs which are VERY hard to understand)
          
            $tcount = 2;
        
            while (isset($terms["options"][$title])) {
              $title = $title . " ( ".$tcount." )";
              $tcount++;
            }

            $pid = $tax_name.":".$term->id();

	        $attr = array();
			$data_icon = "mp-icon-taxonomy mp-icon-taxonomy-".$tax_name;

	        // retrieve special properties

			    $attr["data-icon"] = $data_icon;
        
            if ($row_style == "icon_title_excerpt" || $row_style == "image_title_excerpt") {
              $attr = array_merge( $attr, self::get_row_prop_desc($term, $result_row_prop, $tax_name) );
            }
            
            
            if ($row_style == "image_title_excerpt" || $row_style == "image_title") {
              $attr = array_merge( $attr, self::get_row_prop_image($term, $result_row_prop, $tax_name, $row_style == "image_title" ? 40 : 60) );
            }
        
        
            $terms["options"][$title] = $pid;
            $terms["options_attr"][] = $attr;

            if (!$blank && in_array($pid, $value)) {
              $values_options[$pid] = $title;
              $values_options_attr[$pid] = $attr;
              $selected_values[] = $pid;
			  $select_attr["data-icon"] = $data_icon;
            } 

          }

          if (count($terms)) {
            $terms["optgroup_attr"] = array("label" => $taxonomy->label(), "data-selection-prefix" => $taxonomy->singular_label().": ");
            $items[$taxonomy->label()] = $terms;
          }
       
        }

      }
    
    
    }
  
    self::$values_keys = array_values($values_options);

    $select_style = "";

    $select_attr = array("id" => "{{id}}", "name" => "{{name}}");

    if ($control_style == "drop_down_list") {
      $basic = self::option_value($options, "basic") == "yes";
      $placeholder = self::option_value($options, "placeholder", __("-- Select an Item --", MASTERPRESS_DOMAIN));
    } else {
      $placeholder = self::option_value($options, "placeholder", __("-- Select Items --", MASTERPRESS_DOMAIN));
    }

    $max = strlen($placeholder) * 8;
    
    $select_attr["class"] = "with-icons";
   
    if ($control_style != "list_box_multiple") {

      if (is_numeric($maxwidth)) {
        $select_style .= "width: 99%; max-width: ".$maxwidth."px;";
      } else {
        
        if ($placeholder != "") {
          $select_style .= " width: ".$max."px; "; 
        }
      }
    
    } else {
      $select_style .= "width: ".$maxwidth."px;";
    }
    

    if (in_array($control_style, array("list_box_multiple", "list_box"))) {
      if (isset($options["height"]) && is_numeric($options["height"])) {
        $select_style .= "height: ".$options["height"]."px;";
      } else {
        $select_style .= "height: 250px;";
      }
    }

    if ($control_style == "list_box_multiple") {
      $select_attr["multiple"] = "multiple";
      $select_attr["name"] = "{{name}}[]";
    } else if ($control_style == "list_box") {
      $select_attr["size"] = "2";
    }

    if ($select_style != "") {
      $select_attr["style"] = $select_style;
    }

    $basic = self::option_value($options, "basic") == "yes";

    if ($control_style == "drop_down_list") {

      $placeholder = self::option_value($options, "placeholder", $placeholder);
    
      if ($basic) {
        $items = array($placeholder => "") + $items;
        array_unshift($options_attr, array());
      } else {
        $items = array("" => "") + $items;
        array_unshift($options_attr, array());
        $select_attr["data-placeholder"] = $placeholder;
      }
      
    } else {

      $select_attr["data-value-input"] = "{{id}}-value-input";
      $select_attr["data-placeholder"] = self::option_value($options, "placeholder", $placeholder);

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

    if (!is_array($values)) {
      $values = array($values);
    }
    
    $model_id = $_REQUEST["model_id"];
    
    $field = MPM_Field::find_by_id($model_id);
    
    if ($field) {
      $selects = self::selects($field->type_options, $values);
      $info["select"] = WOOF::render_template($selects["select"], array("id" => $_REQUEST["id"], "name" => str_replace("[]", "", $_REQUEST["name"])  ) );
      self::ajax_success( $info );
    }
    
  }
  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  protected $_term;
  protected $_terms;
  
  public function __toString() {
    return $this->terms()->flatten("name");
  }
  
  public function iterator_items() {
    return $this->terms()->items();
  }

  public function count() {
    return count($this->terms());
  }

  public function offsetGet($index) {
    return $this->terms[$index];
  }

  public function offsetExists($index) {
    return $index <= count($this->terms());
  }
  
  public function get_delegate() {
    return $this->term();
  }
  
  public function forward($name) {
    $dg = $this->get_delegate();
    return $dg->__get($name);
  }

  public function is($term, $taxonomy = null) {
    
    $my_term = $this->term();
    
    if (is_null($taxonomy)) {
      // try to infer the taxonomy from those selected
      
      $taxonomies = $this->field()->info->type_options["taxonomies"];
        
      foreach ($taxonomies as $tax) {
        $check = $my_term->is($term, $tax);
        
        if ($check) {
          return true;
        }

      }
      
    } else {
      return $my_term->is($term, $taxonomy);
    }
    
    return false;

  }


  public function terms() {
    global $wf;
    
    if (!isset($this->_terms)) {
      
      $value = $this->value();
      
      if ($value != "") {
        if (!is_array($value)) {
          $value = array($value);
        }

        $terms = $wf->terms_by_id($value);
        $this->_terms = $terms->sort_to("taxonomy_and_id", $value);
      
      } else {
        $this->_terms = $wf->wrap_terms(array());
      }
    
      
    }
    
    
    return $this->_terms;
  }

  public function add_term( $term, $taxonomy = NULL ) {
    
    global $wf;
    
    $term = $wf->term( $term, $taxonomy );
    
    if (!$term->exists()) {

      if (isset($taxonomy) && is_string($term)) {
        
        $tax = $wf->taxonomy( $taxonomy );
        
        if ($tax->exists()) {
          $new_term = $tax->create();
          $new_term->slug = $term;
          $new_term->update();
          
          $this->terms->append( $new_term );
          $this->update();
        }
        
      }
      
    } else {
      
      $this->terms->append( $term );
      $this->set_value( $this->terms );
      $this->update();
      
    }
    
  }

  public function remove_term( $term, $taxonomy = NULL ) {
    
    global $wf;
    
    $to_remove = $wf->term( $term, $taxonomy );
    
    if (!$to_remove->exists() && isset($taxonomy) && is_string($term)) {
      // this may be a new term
      $to_remove = $wf->taxonomy( $taxonomy )->create();
      $to_remove->slug = $term;
    }
    
    if ($to_remove->exists()) {

      $retain = array();
      
      foreach ($this->terms() as $term) {

        if ( ! ($term->slug == $to_remove->slug && $term->taxonomy_name == $to_remove->taxonomy_name ) ) {
          $retain[] = $term;
        }

      }
    
      $this->set_value( $wf->collection( $retain ) );
      $this->update();
      
    }
    
    
  }
  
  
  public function term() {
    global $wf;
    
    if (!isset($this->_term)) {

      $value = $this->value();
      
      if (!is_array($value)) { // single post relation
        if (!$this->blank()) {
          $this->_term = $this->terms()->first();
        } else {
          $this->_term = new WOOF_Silent(__("No term has been set for this field", MASTERPRESS_DOMAIN));
        }  
      } else {
        // grab the first term
        
        $terms = $this->terms();
        
        if ($terms->count()) {
          $this->_term = $terms->first();
        }
        else {
          $this->_term = new WOOF_Silent(__("No term has been set for this field", MASTERPRESS_DOMAIN));
        }        
        
      }
      
    } 
    
    return $this->_term;    
  }
  
  function col_summary() {
    
    global $wf;
    
    $p = $this->term();
    
    if ($this->is_multi_select()) {
      
      $terms = $this->terms();
      
      if (count($terms)) {
        
        if (count($terms) == 1) {
          return $terms->first()->edit_link();
        } else {
        
          $links = array();
          
          $tbt = $terms->group_by("taxonomy_name");

          foreach ($tbt as $tax_name => $items) {

            $ids = $items->flatten("id", "sep=,");

            $attr = array(
              "href" => admin_url("edit-tags.php?taxonomy=".$tax_name."&mp_term__in=".$ids."&mp_view=".urlencode( sprintf( __("Related to '%s'", MASTERPRESS_DOMAIN), $wf->the->title() ) ))
            );
            
            $tax = $wf->taxonomy($tax_name);
            
            $text = WOOF::items_number(count($items), sprintf( __("No %s", MASTERPRESS_DOMAIN), $tax->plural_label()), sprintf( __("1 %s", MASTERPRESS_DOMAIN), $tax->singular_label()), sprintf( __("%d %s", MASTERPRESS_DOMAIN), count($items), $tax->plural_label() ) );
            $links[] = WOOF_HTML::tag("a", $attr, $text);
          }
          
          return WOOF_HTML::tag("div", "class=mp-col", implode("<br />", $links));
          
        }
      } else {
        return "";
      }
      
    } else {

      if ($p->exists()) {
        return $p->edit_link();
      }
      
    }
    
    
  }
  
  public function json() {
    
    $json = array();
    
    if ($this->is_multi_select()) {

      foreach ($this->terms() as $the) {
        $json[] = array("href" => $the->json_href());
      }
      
    } else {
      
      $json["href"] = $this->term->json_href();
      
    }
    
    return $json;
    
  }

  
  function col() {
    
    global $wf;

    
    $links = array();
    
    $terms = $this->terms();
    
    $tbt = $terms->group_by("taxonomy_name");
    
    $use_labels = false;
    
    if (count($tbt) > 1) {
      $use_labels = true;
    }
    
    foreach ($tbt as $tax_name => $items) {
      
      $tax = $wf->taxonomy($tax_name);
      $label = trim( $tax->singular_label() );
      
      foreach ($items as $term) {
      
        $text = $term->name();
        
        $title = "";
        
        if ($use_labels && $label != "") {
          $title = $label.":&nbsp;".$text;
        }
        
        $attr = array(
          "href" => admin_url("edit-tags.php?action=edit&taxonomy=".$tax_name."&tag_ID=".$term->id()),
          "title" => $title
        );
        
        $links[] = WOOF_HTML::tag("a", $attr, $text);
      }

    }
    
    return WOOF_HTML::tag("div", "class=mp-col", implode(", ", $links));

  }
  
  public function change() {
    unset($this->_term, $this->_terms);
  }
  
  public function value_for_set($value) {
    
    global $wf;

    // make sure this is a valid value for the types available in this control
    
    $taxonomies = $this->field()->info->type_options["taxonomies"];
    
    $values = $this->as_array( $value );
    
    if ($this->is_multi_select()) {
      
      $ret = array();
      
      foreach ($values as $val) {
        
        foreach ($taxonomies as $taxonomy) {
      
          $term = $wf->term($val, $taxonomy);
      
          if ($term->exists()) {
            // assume the first match is the one
            // also, this control requires a "taxonomy:id" tuple as the value of the term, so ensure that
            $ret[] = $term->taxonomy_and_id();
            break 1;
          }
          
        }
      
      }
      
      return $ret;
      
    } 
    else {
      
      $value = $values[0];
      
      foreach ($taxonomies as $taxonomy) {
      
        $term = $wf->term($value, $taxonomy);
      
        if ($term->exists()) {
          // assume the first match is the one
          // also, this control requires a "taxonomy:id" tuple as the value of the term, so ensure that
          return $term->taxonomy_and_id();
        }

      }
    
    }

    return "";
    
  }
  
}