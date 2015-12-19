<?php

class MPFT_VisualEditor extends MPFT {
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: JavaScript Enqueues - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: enqueue 
      A callback to enqueue any JavaScript dependencies for this field type. 
      Field-type specific javascript files must be placed directly in the folder for this field type.
      
    Example Implementation:
      > wp_enqueue_script( 'jquery-some-plugin', plugins_url('jquery.some-plugin.js', __FILE__), array("jquery") );
      
  */
  
  public static function enqueue() {
    MasterPress::enqueue_codemirror();
  }




  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Visual / HTML Editor", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Visual / HTML Editors", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A Visual / HTML editor for large content blocks, like the main post content area", MASTERPRESS_DOMAIN);
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
      $defaults = array("default_tab" => "visual", "height" => 250, "html_editor" => "cm", "cm_theme" => "sunburst");
    }

    $options = wp_parse_args( $options, $defaults );

    $p = self::type_prefix(__CLASS__);

    $height = self::option_value($options, "height");
    $mce_blockformats = self::option_value($options, "mce_blockformats");
    $mce_styles = self::option_value($options, "mce_styles");
    $default_tab = self::option_value($options, "default_tab");
    $cm_theme = self::option_value($options, "cm_theme");

    $height_label = __("Editor Height:", MASTERPRESS_DOMAIN);
    $height_note = __("(pixels)", MASTERPRESS_DOMAIN);

    $html_editor_label = __("HTML Editor:", MASTERPRESS_DOMAIN);
    $html_editor_note = __('<a href="http://codemirror.net" target="_blank">CodeMirror 2</a> is a wonderful JavaScript text editor that support live syntax-coloring, among other features.', MASTERPRESS_DOMAIN);
    $html_editor_select = WOOF_HTML::select(
      array("id" => $p."html-editor", "name" => "type_options[html_editor]"), 
      array(
        "CodeMirror 2" => "cm", 
        "Standard Textarea" => "textarea" 
      ),
      self::option_value($options, "html_editor")
      
    );

    $cm_theme_label = __("CodeMirror Theme:", MASTERPRESS_DOMAIN);
    $cm_theme_select = WOOF_HTML::select(
      array("id" => $p."cm-theme", "name" => "type_options[cm_theme]"), 
      array(
        "Default" => "default",
        "Cobalt" => "cobalt",
        "Elegant" => "elegant",
        "Neat" => "neat",
        "Night" => "night",
        "Sunburst" => "sunburst"
      ),
      $cm_theme
    );

    $mce_blockformats_label = __("Block Formats:", MASTERPRESS_DOMAIN);
    $mce_blockformats_note = __('Specify the block-level tags which appear in the <em>Format</em> dropdown list in TinyMCE.<br />Leave this value empty to use the same formats as the WordPress content editor. <a target="_blank" href="http://www.tinymce.com/wiki.php/Configuration:theme_advanced_blockformats">More Info</a>', MASTERPRESS_DOMAIN);

      
    
    $mce_styles_label = __("Styles:", MASTERPRESS_DOMAIN);
    $mce_styles_note = __('Specify the style names and the underlying CSS classes for these styles, in the form <span class="tt">label = css_class</span> separated by semi-colons (;). Leave this value empty to use the same formats as the WordPress content editor. <a target="_blank" href="http://www.tinymce.com/wiki.php/Configuration:theme_advanced_styles">More Info</a>', MASTERPRESS_DOMAIN);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

    $default_tab_label = __("Default Tab:", MASTERPRESS_DOMAIN);

    $default_tab_select = WOOF_HTML::input_radio_group(
      "type_options[default_tab]", $p."default-tab-", 
      array( __("Visual <span>( TinyMCE )</span>", MASTERPRESS_DOMAIN) => "visual", __("HTML", MASTERPRESS_DOMAIN) => "html" ),
      $default_tab,
      WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")
    );

$html = <<<HTML

    <div class="f">
      <label for="{$p}default_tab">{$default_tab_label}</label>
      <div id="fw-{$p}default_tab" class="fw">
        {$default_tab_select}
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}height">{$height_label}</label>
      <div id="fw-{$p}height" class="fw">
        <input id="{$p}height" type="text" name="type_options[height]" value="{$height}" class="text" />
        <span class="note">{$height_note}</span>
      </div>
    </div>
    
    
    <div class="f">
      <label for="{$p}html_editor">{$html_editor_label}</label>
      <div id="fw-{$p}html_editor" class="fw">
        {$html_editor_select}
        <p class="note">{$html_editor_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div id="{$p}cm-theme-f" class="f">
      <label for="{$p}cm_theme">{$cm_theme_label}</label>
      <div id="fw-{$p}cm_theme" class="fw">
        {$cm_theme_select}
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}mce_blockformats">{$mce_blockformats_label}</label>
      <div id="fw-{$p}mce_blockformats" class="fw">
        <input id="{$p}mce_blockformats" type="text" name="type_options[mce_blockformats]" value="{$mce_blockformats}" class="text" />
        <p class="note">{$mce_blockformats_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}mce_styles">{$mce_styles_label}</label>
      <div id="fw-{$p}mce_styles" class="fw">
        <textarea id="{$p}mce_styles" name="type_options[mce_styles]" class="mono">{$mce_styles}</textarea>
        <p class="note">{$mce_styles_note}</p>
      </div>
    </div>
    <!-- /.f -->

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
    return 3;
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
    return array("height", "default_tab", "mce_blockformats", "mce_styles", "html_editor", "cm_theme");
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
    Static Method: summary 
      Returns the HTML to render the NON-EMPTY summary for this field type. The "summary" is the grid block for this field in the collapsed view of the set it belongs to.
      
    Arguments: 
      $field - MEOW_Field, an object containing both the field's value and information about the field - See <http://masterpress.info/api/classes/meow-field>
      
    Returns:
      String - the HTML UI 
  */

  public static function summary( MEOW_Field $field ) {
    if (!$field->blank()) {
      $summary = preg_replace("/\[[^\]]*\]/", '', $field->raw());
      $summary = preg_replace("/<[^>]*>/", "&nbsp;", $summary);
      $summary = preg_replace("/(&nbsp;){2,}/", "&nbsp;", $summary);
      $summary = preg_replace("/^(\s|(&nbsp;))+/", "", $summary);
      
      $summary_content = self::truncate_for_summary(self::summary_width(), $summary);
      
      if ($summary_content == "") {
        $summary_content = $field->raw(); // if there's nothing, bring back some of the content (shortcodes in particular)
      }
      
      return $summary_content;
    }
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

    $readonly = WOOF_HTML::readonly_attr( !$field->is_editable() );

    $tabs_class = "";
    
    if ($readonly) {
      $tabs_class = " readonly";
    }
    
    $visual_current = "current";
    $html_current = "";

    $default_tab = self::option_value($options, "default_tab");

    if ($default_tab == "html" ) {
      $visual_current = "";
      $html_current = "current";
    }

    $style = "";

    if (isset($options["height"]) && is_numeric($options["height"])) {
      $style .= "height: ".$options["height"]."px;";
    }

    $value = "";
    
    if (!$field->blank()) {
      $value = htmlspecialchars($field->clean());
    }

    $html = <<<HTML

    <div id="wp-{{id}}-wrap" class="wp-content-wrap editor-ui">

    <ul class="editor-tabs $tabs_class">
      <li><div class="tab-button visual {$visual_current}">Visual</div></li>
      <li><div class="tab-button html {$html_current}">HTML</div></li>
    </ul>

    <textarea id="{{id}}" name="{{name}}" $readonly class="mce">{$value}</textarea>

    </div>

HTML;

    return $html;

  }

  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  public function value($args = array()) {
    global $wf;
    
    // overridden value method, to provide the content with filters applied
    $data = $this->data();
    
    global $wp_filter;
    
    $r = wp_parse_args( $args,
      array(
        "unwrap_shortcodes" => false,
        "wpautop" => false
      )
    );

    $wpautop = WOOF::is_true_arg( $r, "wpautop" );
    
    if (isset($data->val)) {
    
      $content = $this->data()->val;
    
      if ( !$wpautop ) {
        $wf->disable_filter("the_content", "wpautop");
      }
      
      if (WOOF::is_true_arg( $r, "unwrap_shortcodes" ) ) {
        $pattern = "/<p>[\s\n]*(\[[^\]]*\])(?:\[\/[^\]]*\])?[\s\n]*<\/p>/";
        $content = preg_replace( $pattern, "$1", $content);
      }
    
      $ret = apply_filters('the_content', $content );

      if ( !$wpautop ) {
        $wf->enable_filter("the_content", "wpautop");
      }

      return $ret;
      
    }
    
    return "";
  }

  public function unwrap_shortcodes() {
    return $this->value("unwrap_shortcodes=1");
  }

  function __toString() {
    return $this->value();
  } 
  
  // clean : get the unprocessed value (with short-tags and so on intact)
  public function clean() {
    return $this->data()->val;
  }

}