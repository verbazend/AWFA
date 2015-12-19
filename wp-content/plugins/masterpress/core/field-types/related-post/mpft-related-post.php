<?php

class MPFT_RelatedPost extends MPFT {
  
  private static $values_keys = array(); // cache for summary
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Related Post(s)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Related Posts", MASTERPRESS_DOMAIN);
  }
  
  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Allows selection of one or more related posts", MASTERPRESS_DOMAIN);
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
    
    $p = self::type_prefix(__CLASS__);

    $defaults = array();

    if (MPC::is_create()) {
      $defaults = array("maxwidth" => 580, "height" => 300, "control_style" => "drop_down_list", "orderby" => "title,asc", "exclude_current" => "yes");
    }

    $options = wp_parse_args( $options, $defaults );

    
    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $post_types_label = __("Available Post Types:", MASTERPRESS_DOMAIN);
    $post_types_items = array();

    MPM::incl("post-type");

    $post_types = MPM_PostType::find("orderby=disabled,name ASC");

    $post_types_selected = array();
    
    foreach ($post_types as $post_type) {
      if (!$post_type->disabled && $post_type->show_ui && $post_type->still_registered()) {
        $post_types_selected[] = $post_type->name;
        $post_types_items[$post_type->display_label()] = $post_type->name;
      }

    }

    if (!MPC::is_create()) {
      $post_types_selected = self::option_value($options, "post_types");
    }
  
    $post_types_checkboxes = '';
        
    if (!is_array($post_types_selected)) {
      $post_types_selected = explode(",", $post_types_selected);
    }
  
    $id = $p."post-types-";
    
    foreach ($post_types as $post_type) {

      
      if (!$post_type->disabled && $post_type->show_ui && $post_type->still_registered()) {
        
        $value = $post_type->name;
        $label = $post_type->display_label();
      
        $id_suffix = WOOF::sanitize($value);
      
        $attr = array( "id" => $id."_".$id_suffix, "class" => "checkbox", "type" => "checkbox", "name" => "type_options[post_types][]", "value" => $value );
      
        if (in_array($value, $post_types_selected)) {
          $attr["checked"] = "checked";
        }
      
        $post_types_checkboxes .= WOOF_HTML::open("div", "class=fwi");
        $post_types_checkboxes .= WOOF_HTML::tag("input", $attr );
        $post_types_checkboxes .= WOOF_HTML::tag("label", array( "for" => $id."_".$id_suffix, "class" => "checkbox mp-icon-post-type mp-icon-post-type-".$value ), $label );
        $post_types_checkboxes .= WOOF_HTML::close("div");

      }
      
    }


    $basic = MPFT::options_select_basic( $p, $options );
    $exclude_current = MPFT::options_exclude_current( $p, $options, __('Excludes the current post being edited from the results', MASTERPRESS_DOMAIN) );
    $multi_layout = MPFT::options_select_multi_layout( $p, $options );
    $control_style = MPFT::options_select_control_style( $p, $options );

    $maxwidth = MPFT::options_maxwidth( $p, $options );
    $height = MPFT::options_height( $p, $options );
    $grouping = MPFT::options_select_grouping( $p, $options, __("Post Type", MASTERPRESS_DOMAIN) );
    $placeholder = MPFT::options_placeholder($p, $options);

    $results_input_length = MPFT::options_input_text( $p, $options, "results_input_length", __("Minimum Input Length", MASTERPRESS_DOMAIN), __("Enter the number of characters required before any results are displayed.<br />This is useful for large numbers of posts, where performance may become poor.", MASTERPRESS_DOMAIN));

    $orderby = MPFT::options_post_orderby( $p, $options);
    
    $post_count = MPFT::options_input_text( $p, $options, "post_count", __("Post Limit:", MASTERPRESS_DOMAIN), __("The latest <em>n</em> posts will be retrieved, where <em>n</em> is the number provided here.<br />Leave empty to retrieve all posts.", MASTERPRESS_DOMAIN));
    
    $results_row_style = MPFT::options_select_results_row_style( $p, $options );

    $taxonomy_query = MPFT::options_textarea( $p, $options, "taxonomy_query", __("Taxonomy Filter:", MASTERPRESS_DOMAIN) );
    
    
    $data_source_label = __("Query Options - <span>further specify the posts available for selection</span>", MASTERPRESS_DOMAIN); 
    $control_results_label = __("Results Display - <span>settings for the display of the available posts</span>", MASTERPRESS_DOMAIN); 
    $control_selections_label = __("Selection Display - <span>settings for the display of the selected posts</span>", MASTERPRESS_DOMAIN); 
    
    
    
    // build a post types grid
    
    $results_row_item_prop_label = __("Row Item Properties:", MASTERPRESS_DOMAIN);
    $results_row_item_prop_note = __("Defines the post properties used to derive the info shown in result rows.<br /><b>Note: </b>Excerpts and images will be truncated / resized automatically.", MASTERPRESS_DOMAIN);
    
    $row_style = self::option_value($options, "row_style", "icon_title");

    $grid  = WOOF_HTML::open("table", "class=grid mini not-selectable grid-row-item-prop&cellspacing=0");
      $grid .= WOOF_HTML::open("thead");
      
        $grid .= WOOF_HTML::tag("th", "class=post-type", WOOF_HTML::tag("i", "class=pins", "") . WOOF_HTML::tag("span", null, __("Post Type", MASTERPRESS_DOMAIN)));
        $grid .= WOOF_HTML::tag("th", "class=title", WOOF_HTML::tag("i", "class=title-bar", "") . WOOF_HTML::tag("span", null, __("Title", MASTERPRESS_DOMAIN)));
        $grid .= WOOF_HTML::tag("th", "class=".(($row_style == "icon_title" || $row_style == "image_title") ? "disabled " : "")."icon excerpt", WOOF_HTML::tag("i", "class=content-bar", "") . WOOF_HTML::tag("span", null, __("Excerpt", MASTERPRESS_DOMAIN)));
        $grid .= WOOF_HTML::tag("th", "class=".(($row_style == "icon_title" || $row_style == "icon_title_excerpt") ? "disabled " : "")."icon image", WOOF_HTML::tag("i", "class=image", "") . WOOF_HTML::tag("span", null, __("Image", MASTERPRESS_DOMAIN)));

      $grid .= WOOF_HTML::close("thead");

      $grid .= WOOF_HTML::open("tbody");
      
      $count = 1;
      
      
      foreach ($post_types as $post_type) {
        if (!$post_type->disabled && $post_type->show_ui) {
          
          $classes = array("post-type-".$post_type->name);
          
          if ($count == 1) {
            $classes[] = "first";
          }
          
          $attr = array("class" => implode(" ", $classes));
          
          if (!in_array($post_type->name, $post_types_selected)) {
            $attr["style"] = "display: none;";
          }

  
          $grid .= WOOF_HTML::open("tr", $attr);

          $count++;
            
            $span = WOOF_HTML::tag("span", array("class" => "mp-icon-post-type mp-icon-post-type-".$post_type->name ), $post_type->display_label());
            
            $grid .= WOOF_HTML::tag("td", "class=first post-type", $span);
            $grid .= WOOF_HTML::open("td", "class=title");
            
              $default = "title";
              $value = isset($options["result_row_prop"][$post_type->name]["title"]) ? $options["result_row_prop"][$post_type->name]["title"] : $default;
              
              if ($value == "") {
                $value = $default;
              }
              
              $grid .= WOOF_HTML::open("div");
              
              $input_attr = array(
                "type" => "text",
                "name" => "type_options[result_row_prop][".$post_type->name."][title]",
                "class" => "text",
                "value" => $value
              );

              $grid .= WOOF_HTML::tag("input", $input_attr);

              $grid .= WOOF_HTML::tag(
                "button", 
                array(
                  "type" => "button",
                  "class" => "ir",
                  "data-dialog" => "dialog-post-type-".$post_type->name,
                  "data-filter" => "text",
                  "title" => __("Select Field", MASTERPRESS_DOMAIN)
                ),
                "select"
              );
              
              $grid .= WOOF_HTML::close("div");
              
            $grid .= WOOF_HTML::close("td");
            $grid .= WOOF_HTML::open("td", "class=excerpt".(($row_style == "icon_title" || $row_style == "image_title") ? " disabled " : ""));

              $default = "excerpt";
              $value = isset($options["result_row_prop"][$post_type->name]["excerpt"]) ? $options["result_row_prop"][$post_type->name]["excerpt"] : $default;
              
              if ($value == "") {
                $value = $default;
              }

              $input_attr = array(
                "type" => "text",
                "name" => "type_options[result_row_prop][".$post_type->name."][excerpt]",
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
                  "data-dialog" => "dialog-post-type-".$post_type->name,
                  "data-filter" => "text",
                  "title" => __("Select Field", MASTERPRESS_DOMAIN)
                ),
                "select"
              );
            
              $grid .= WOOF_HTML::close("div");

            $grid .= WOOF_HTML::close("td");
            $grid .= WOOF_HTML::open("td", "class=image".(($row_style == "icon_title" || $row_style == "icon_title_excerpt") ? " disabled" : ""));

              $default = "featured_image";
              $value = isset($options["result_row_prop"][$post_type->name]["image"]) ? $options["result_row_prop"][$post_type->name]["image"] : $default;
              
              if ($value == "") {
                $value = $default;
              }
              
              $input_attr = array(
                "type" => "text",
                "name" => "type_options[result_row_prop][".$post_type->name."][image]",
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
                  "data-dialog" => "dialog-post-type-".$post_type->name,
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
    
    foreach ($post_types as $post_type) {
      
      if (!$post_type->disabled && $post_type->show_ui) {
      
        $dialogs .= WOOF_HTML::open(
          "div", 
          array(
            "class" => "dialog dialog-fields",
            "id" => "dialog-post-type-".$post_type->name,
            "data-title" => __("Select a Field", MASTERPRESS_DOMAIN)
          )
        );
      
        $fs = $meow_provider->post_type_field_sets($post_type->name);

        $field_options = array();
        $field_options[""] = "";

        $field_options_attr = array("");

        $field_options[__("( Built-in Fields )", MASTERPRESS_DOMAIN)] = array(
          
          "options" => array(
            __("Title", MASTERPRESS_DOMAIN) => "title",
            __("Excerpt", MASTERPRESS_DOMAIN) => "excerpt",
            __("Feature Image", MASTERPRESS_DOMAIN) => "feature_image"
          ),
          "options_attr" => array(
            array("data-icon" => "mp-icon mp-icon-field-type-text-box", "class" => "text"),
            array("data-icon" => "mp-icon mp-icon-field-type-text-box-multiline", "class" => "text"),
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
      
    }
    

    
$html = <<<HTML

    {$dialogs}
    
    <div class="pull-up">
    {$control_style}


    <div class="f f-post-types">
      <p class="label">{$post_types_label}</p>
      <div class="fw">

      <div id="{$p}post-types-wrap" class="clearfix">
      {$post_types_checkboxes}
      </div>

      <div id="{$p}post-types-controls" class="controls">
        <button type="button" class="button button-small select-all">Select All</button>
        <button type="button" class="button button-small select-none">Select None</button>
      </div>
      <!-- /.controls -->

      </div>
    </div>
    <!-- /.f -->
    
    {$placeholder}
    {$basic}
    {$maxwidth}
    </div>

    <div class="source-wrap divider">
    <h4><i class="database"></i>{$data_source_label}</h4>  
    {$exclude_current}
    {$orderby}
    {$post_count}
    {$taxonomy_query}
    </div>
    
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

  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public static function post_parent_names($post, $result_row_prop, $post_type_name, $parents = array()) {
    $parents[] = self::get_row_prop_title($post, $result_row_prop, $post_type_name);
    
    if ($post->has_parent()) {
      return self::post_parent_names($post->parent(), $result_row_prop, $post_type_name, $parents);
    }

    return array_reverse($parents);
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
    Static Method: summary_label_classes
      Returns an array of classes to apply to the label in the field summary (the grid block in the collapsed view of the field set)
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>

    Returns:
      Array - of strings
  */

  public static function summary_label_classes( MEOW_Field $field ) {

    $options = $field->info->type_options;
    
    $post_types = self::option_value($options, "post_types");
    
    // if we are using a single post type, use the icon for it as the summary icon

    if (count($post_types) == 1) {
      return array("mp-icon-post-type mp-icon-post-type-".$post_types[0]);
    }
    
    return array();
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
  
  public static function get_tax_query($qs) {
    
    $query = array();
    
    $lines = explode("\n", $qs);

    foreach ($lines as $line) {
      if (preg_match("/([a-z0-9-_]{1,20})\s(IN|=|!=|\<\>|NOT IN|NOT)\s(.+)/", $line, $matches)) {
        $tax_name = $matches[1];
        $op = $matches[2];
        $slugs = array_map( "trim", explode(",", $matches[3]) );
        
        if ($op == "<>") {
          $op = "!=";                
        }

        if ($op == "NOT") {
          $op = "NOT IN";                
        }
        
        $tq = array("taxonomy" => $tax_name, "operator" => $op, "terms" => $slugs, "field" => "slug");
        
        if (!isset($query['relation'])) {
          $query['relation'] = "OR";
        } 
        
        $query[] = $tq;
        
      }
      
    }
    
    return $query;
          
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

    $items = array();
    $selected_values = array();
    $options_attr = array();
    $values_options = array_flip($value);
    $values_options_attr = array_flip($value);
    $wcount = -1;
    $multiple = count($options["post_types"]) > 1;
    
    $maxwidth = self::option_value($options, "maxwidth", 580);

    
    $exclude_current = self::option_value($options, "exclude_current") == "yes";

    $max = 0;
    
    $select_attr = array("id" => "{{id}}", "name" => "{{name}}");

    if ($control_style == "drop_down_list") {
      $basic = self::option_value($options, "basic") == "yes";
      $placeholder = self::option_value($options, "placeholder", __("-- Select an Item --", MASTERPRESS_DOMAIN));
    } else {
      $placeholder = self::option_value($options, "placeholder", __("-- Select Items --", MASTERPRESS_DOMAIN));
    }

    $max = strlen($placeholder) * 8;
    
    $numberposts = self::option_value($options, "post_count");
    $orderbyorder = self::option_value($options, "orderby");

    $row_style = self::option_value($options, "row_style", "icon_title");
    $result_row_prop = self::option_value($options, "result_row_prop", array());

    $grouping = self::option_value($options, "grouping");

    $obp = explode(",", $orderbyorder);
    
    $orderby = $obp[0];
    $order = "asc";
    
    if (isset($obp[1])) {
      $order = $obp[1]; 
    }
    
    $is_post = isset($_GET["post"]);
    
    $the_id = $wf->the->id();
    
    // build a list of related posts

    if ($grouping == "flat" || !$multiple) {

      $query = array("cache_results" => 0, "post_type" => $options["post_types"], "orderby" => "post_date", "order" => "desc", "post_status" => array('publish', 'pending', 'draft', 'future', 'private'));

      if (is_numeric($numberposts)) {
        $query["posts_per_page"] = $numberposts;
      }
    
      if (isset($options["taxonomy_query"]) && trim($options["taxonomy_query"]) != "") {
      
        $tq = self::get_tax_query($options["taxonomy_query"]);
      
        if (count($tq)) {
          $query['tax_query'] = $tq;
        }
      
      }
      
      $all = $wf->posts($query);

      foreach ($options["post_types"] as $post_type_name) {

        $post_type = $wf->type($post_type_name);

        if ($post_type->exists()) {

        
          if ($post_type->hierarchical) {
            $all = $post_type->flatten_posts($query);
          } else {
            $all = $post_type->posts($query);

            if ($orderbyorder != "post_date,desc") {
              $all = $all->sort($orderby, $order);
            }

          }
      
          foreach ($all as $post) {
            $ptitle = "";
        
            $wcount++;
      
            $pid = $post->id();
        
            $post_type_name = $post_type->name();
            $post_type_singular_label = $post_type->singular_label();

            $pn = self::post_parent_names($post, $result_row_prop, $post_type_name);
    
            $ptitle = implode(" &rarr; ", $pn);

            if ($ptitle == "") {
              $ptitle = __("( no title )", MASTERPRESS_DOMAIN);
            } 

            $title = strip_tags($ptitle);
                  
            if ($multiple) {
              $title = $post_type_singular_label.": ".$title; 
            }
  
            $attr = array();
        		$data_icon = "mp-icon-post-type mp-icon-post-type-".$post_type_name;

            // retrieve special properties

        		$attr["data-icon"] = $data_icon;

            if ($row_style == "icon_title_excerpt" || $row_style == "image_title_excerpt") {
              $attr = array_merge( $attr, self::get_row_prop_excerpt($post, $result_row_prop, $post_type_name) );
            }

            if ($row_style == "image_title_excerpt" || $row_style == "image_title") {
              $attr = array_merge( $attr, self::get_row_prop_image($post, $result_row_prop, $post_type_name, $row_style == "image_title" ? 40 : 60) );
            }
    
            // ensure there are no duplicate titles (causes bugs which are VERY hard to understand)

            $tcount = 2;
    
            while (isset($posts["options"][$title])) {
              $title = $title . " ( ".$tcount." )";
              $tcount++;
            }
    
            $max = max( $max, strlen($title) * 8 );
    
            if (!($is_post && $exclude_current && $pid == $the_id)) {

              $items[$title] = $pid;
              $options_attr[] = $attr;

              if (in_array($pid, $value)) {
                $values_options[$pid] = $title; 
                $values_options_attr[$pid] = $attr;
                $selected_values[] = $pid;
    			      $select_attr["data-icon"] = $data_icon;
              } 

            }


          }
        
        } //  // post_type exists

         
      } // foreach $options["post_types"]
         


    } else {
    
      foreach ($options["post_types"] as $post_type) {

        $post_type = $wf->types($post_type);
      
        if ($post_type->exists()) { 
          $wcount++;
        
          $post_type_name = $post_type->name();
          
          $post_type_singular_label = $post_type->singular_label();

          $query = array("cache_results" => 0, "orderby" => "post_date", "order" => "desc", "post_status" => array('publish', 'pending', 'draft', 'future', 'private'));
    
          if (is_numeric($numberposts)) {
            $query["posts_per_page"] = $numberposts;
          }
        
          if (isset($options["taxonomy_query"]) && trim($options["taxonomy_query"]) != "") {
          
            $tq = self::get_tax_query($options["taxonomy_query"]);
          
            if (count($tq)) {
              $query['tax_query'] = $tq;
            }
          
          }
    
          // build an optgroup list
          $posts = array("options" => array(), "options_attr" => array());

          if ($post_type->hierarchical) {
            $all = $post_type->flatten_posts($query);
          } else {
            $all = $post_type->posts($query);
            
            if ($orderbyorder != "post_date,desc") {
              $all = $all->sort($orderby, $order);
            }

          }
        
        
          foreach ($all as $post) {
            $wcount++;
          
            $pid = $post->id();
            
            $pn = self::post_parent_names($post, $result_row_prop, $post_type_name);

            $ptitle = implode(" &rarr; ", $pn);
            
            //$ptitle = self::get_row_prop_title($post, $result_row_prop, $post_type_name);
          
            if ($ptitle == "") {
              $ptitle = __("( no title )", MASTERPRESS_DOMAIN);
            } 
            
            $title = strip_tags($ptitle);
        
	        $attr = array();
			    $data_icon = "mp-icon-post-type mp-icon-post-type-".$post_type_name;

	        // retrieve special properties

			      $attr["data-icon"] = $data_icon;
            
            if ($row_style == "icon_title_excerpt" || $row_style == "image_title_excerpt") {
              $attr = array_merge( $attr, self::get_row_prop_excerpt($post, $result_row_prop, $post_type_name) );
            }

            if ($row_style == "image_title_excerpt" || $row_style == "image_title") {
              $attr = array_merge( $attr, self::get_row_prop_image($post, $result_row_prop, $post_type_name, $row_style == "image_title" ? 40 : 60) );
            }

            
            // ensure there are no duplicate titles (causes bugs which are VERY hard to understand)

            $tcount = 2;
          
            while (isset($posts["options"][$title])) {
              $title = $title . " ( ".$tcount." )";
              $tcount++;
            }
            
            if (!($is_post && $exclude_current && $pid == $the_id)) {
            
              $posts["options"][$title] = $pid;
              $posts["options_attr"][] = $attr;

              if (in_array($pid, $value)) {
                $values_options[$pid] = $title; 
                $values_options_attr[$pid] = $attr;
                $selected_values[] = $pid;
				$select_attr["data-icon"] = $data_icon;
				
              } 
          
              $max = max( $max, strlen($title) * 8 );

            }
          

          }

          if (count($posts)) {
            $posts["optgroup_attr"] = array("label" => $post_type->label(), "data-selection-prefix" => $post_type->singular_label().": ");
            $items[$post_type->label()] = $posts;
          }

        }

      } // end if

    }
    
    self::$values_keys = array_values($values_options);

    $select_style = "";

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
    
    if ($control_style == "list_box_multiple") {
      if (isset($options["height"]) && is_numeric($options["height"])) {
        $select_style .= "height: ".$options["height"]."px;";
      } else {
        $select_style .= "height: 250px;";
      }
    }
    
    $select_attr["class"] = "with-icons";
    
    if ($control_style == "list_box_multiple") {
      $select_attr["multiple"] = "multiple";
      $select_attr["name"] = "{{name}}[]";
    } 

    if ($select_style != "") {
      $select_attr["style"] = $select_style;
    }

    $basic = self::option_value($options, "basic") == "yes";

    if ($control_style == "drop_down_list") {

      $placeholder = self::option_value($options, "placeholder", __("-- Select an Item --", MASTERPRESS_DOMAIN));
    
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
      $select_attr["data-placeholder"] = $placeholder;

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
      $info["select"] = WOOF::render_template(self::select($field->type_options, $values), array("id" => $_REQUEST["id"], "name" => str_replace("[]", "", $_REQUEST["name"])  ) );
      self::ajax_success( $info );
    }
    
  }
    
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  protected $_post;
  protected $_posts;

  public function iterator_items() {
    return $this->posts()->items();
  }

  public function forward($name) {
    return $this->get_delegate()->__get($name);
  }
  
  public function get_delegate() {
    return $this->post();
  }
  
  public function __toString() {
    
    return $this->posts()->flatten("slug");
    /*
    if (isset($this->data()->val) && is_array($this->data()->val)) {
      return array_map(create_function('$item', 'return (int) $item;'), $this->data()->val);
    }
    */
  }
  
  public function count() {
    return count($this->posts());
  }

  public function offsetGet($index) {
    return $this->posts[$index];
  }

  public function offsetExists($index) {
    return $index <= count($this->posts());
  }
  
  public function query($args = array()) {
    return $this->posts($args);
  }
  
  public function posts($args = array()) {
    global $wf;
    
    $post_types = $this->field()->info->type_options["post_types"];
    
    if (WOOF::args_empty($args)) { // we simply want the posts in the UI
      
      if (!isset($this->_posts)) {
        $value = $this->value();
    
        if (!is_array($value)) {
          $value = array($value);
        }
    
        
        $r = wp_parse_args($args);
        $r["cache_results"] = false;
        $r["post_type"] = $post_types;
      
        $posts = $wf->posts_in($value, $r);

        $this->_posts = $posts->sort_to("id", $value);  
      }
    
    } else { // we want a specialisation of the posts in the UI.
    
      $r = wp_parse_args($args);
      
      $value = $this->value();
  
      if (!is_array($value)) {
        $value = array($value);
      }
  
      $r["cache_results"] = false;
      $r["post_type"] = $post_types;

      $posts = $wf->posts_in($value, $r);
      
      if (!isset($r["orderby"])) {
        return $posts->sort_to("id", $value);  
      }
      
      return $posts;
      
    }
    
    return $this->_posts;
  }
  
  public function query_posts($args = array()) {
    $r = wp_parse_args($args);
    $r["query"] = "1";
    return $this->posts($r);
  }
  
  public function is($post, $type = null) {
    return $this->post()->is($post, $type);
  }
  
  public function post() {
    
    global $wf;

    if (!isset($this->_post)) {

      $value = $this->value();
      
      if (!is_array($value)) { // single post relation
        if (!$this->blank()) {
          $this->_post = $wf->post($this->int());
        } else {
          $this->_post = new WOOF_Silent(__("No post has been set for this field", MASTERPRESS_DOMAIN));
        }  
      } else {
        // grab the first post
        
        $posts = $this->posts("post_status=any");

        if ($posts->count()) {
          $this->_post = $posts->first();
        } else {
          $this->_post = new WOOF_Silent(__("No post exists for this field", MASTERPRESS_DOMAIN));
        }
        
        
      }
      
    } 
    
    return $this->_post;    
  }
  
  function col() {
    
    global $wf;
    
    $p = $this->post();
    
    if ($this->is_multi_select()) {
      
      $posts = $this->posts("post_status=any");
        
      if (count($posts)) {
      
        if (count($posts) == 1) {

          return $posts->first()->edit_link();
        } else {
        
          $links = array();
          
          $pbt = $posts->group_by("type_name");

          foreach ($pbt as $type => $items) {
            
            $ids = $items->flatten("id", "sep=,");
        
            $attr = array(
              "href" => admin_url("edit.php?post_type=".$type."&mp_post__in=".$ids."&mp_view=".urlencode( sprintf( __("Related to '%s'", MASTERPRESS_DOMAIN), $wf->the->title() ) ))
            );
            
            $pt = $wf->type($type);
            
            $text = WOOF::items_number(count($items), sprintf( __("No %s", MASTERPRESS_DOMAIN), $pt->plural_label()), sprintf( __("1 %s", MASTERPRESS_DOMAIN), $pt->singular_label()), sprintf( __("%d %s", MASTERPRESS_DOMAIN), count($items), $pt->plural_label() ) );
            $links[] = WOOF_HTML::tag("a", $attr, $text);
          }
          
          return WOOF_HTML::tag("div", "class=mp-col", implode("<br />", $links));
          
        }
      } else {
        return "";
      }
      
    } else {

      if (!is_null($p) && $p->exists()) {
        return $p->edit_link();
      }
      
    }
    
    
    

  }

  public function json() {
    
    $json = array();
    
    if ($this->is_multi_select()) {

      foreach ($this->posts() as $the) {
        $json[] = array("href" => $the->json_href());
      }
      
    } else {
      
      $json["href"] = $this->post->json_href();
      
    }
    
    return $json;
  }
  
  
  public function page() {
    return $this->post();    
  }
  
  
  public function change() {
    unset($this->_posts,$this->_post);
  }
  
  public function value_for_set($value) {
    
    global $wf;

    // make sure this is a valid value for the types available in this control
    
    $post_types = $this->field()->info->type_options["post_types"];

    $values = $this->as_array( $value );

    if ($this->is_multi_select()) {
      
      $ret = array();
      
      foreach ($values as $val) {
        
        foreach ($post_types as $post_type) {
      
          $post = $wf->post($val, $post_type);
      
          if ($post->exists()) {
            // assume the first match is the one
            // also, this control requires a "post_type:id" tuple as the value of the post, so ensure that
            $ret[] = $post->id();
            break 1;
          }
          
        }
      
      }
      
      return $ret;
      
    } 
    else {
      
      $value = $values[0];
      
      foreach ($post_types as $post_type) {
      
        $post = $wf->post($value, $post_type);
      
        if ($post->exists()) {
          // assume the first match is the one
          // also, this control requires a "post_type:id" tuple as the value of the post, so ensure that
          return $post->id();
        }

      }
    
    }

    return "";
    
  }
  



}