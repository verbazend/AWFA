<?php

class MPFT_CodeEditor extends MPFT {
  
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
    return __("Code Editor (CodeMirror)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Code Editor (CodeMirror)", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A syntax-colored code editor, built with CodeMirror v2", MASTERPRESS_DOMAIN);
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
    return "Text Content (Specialized)";
  }


  public static function modes() {
    $arr = array(
      
      "Popular Web Formats" => array(
        "GitHub-Flavored Markdown"  => "gfm", 
        "HTML mixed-mode"           => "htmlmixed", 
        "CSS"                       => "css", 
        "LESS"                      => "less",
        "JavaScript"                => "javascript", 
        "CoffeeScript"              => "coffeescript", 
        "PHP"                       => "php", 
        "Ruby"                      => "ruby", 
        "Python"                    => "python", 
        "MySQL"                     => "mysql", 
        "YAML"                      => "yaml",
        "XML"                       => "xml", 
        "Markdown"                  => "markdown", 
        "diff"                      => "diff"
      ),
      
      "Others" => array(
        "C, C++, C#"                => "clike", 
        "Clojure"                   => "clojure", 
        "Groovy"                    => "groovy", 
        "Haskell"                   => "haskell", 
        "Jinja2"                    => "jinja2", 
        "Lua"                       => "lua", 
        "NTriples"                  => "ntriples", 
        "Pascal"                    => "pascal", 
        "Perl"                      => "perl", 
        "PL/SQL"                    => "plsql", 
        "Properties Files"          => "properties", 
        "R"                         => "r", 
        "reStructuredText"          => "rst", 
        "Rust"                      => "rust", 
        "Scheme"                    => "scheme", 
        "Smalltalk"                 => "smalltalk", 
        "Smarty"                    => "smarty", 
        "SPARQL"                    => "sparql", 
        "sTeX, LaTeX"               => "stex", 
        "Tiddlywiki"                => "tiddlywiki", 
        "VBScript"                  => "vbscript", 
        "Velocity"                  => "velocity", 
        "Verilog"                   => "verilog", 
        "XQuery"                    => "xquery"
      )
    );
    
    return $arr;
    
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
      $defaults = array("minheight" => 100, "theme" => "sunburst", "mode" => "htmlmixed", "modeselect" => "yes");
    }

    $options = wp_parse_args( $options, $defaults );

    $maxheight = self::option_value($options, "maxheight");
    $mode = self::option_value($options, "mode", "gfm");
    $theme = self::option_value($options, "theme");
    $minheight = self::option_value($options, "minheight");

    $p = self::type_prefix(__CLASS__);

    $minheight_label = __("Minimum Height:", MASTERPRESS_DOMAIN);
    $minheight_note = __("(pixels)", MASTERPRESS_DOMAIN);

    $maxheight_label = __("Maximum Height:", MASTERPRESS_DOMAIN);
    $maxheight_note = __("(pixels)", MASTERPRESS_DOMAIN);


    $maxheight_grow_note = __("The editor will automatically grow to accommodate its content up to this maximum value.", MASTERPRESS_DOMAIN);

    $hiddenmode = $mode;
    
    $modeselect_label = __("Show mode selector (allows mode to be changed when entering content)", MASTERPRESS_DOMAIN);
    $modeselect_checked_attr = WOOF_HTML::checked_attr(self::option_value($options, "modeselect") == "yes");

    $mode_label = __("Syntax Mode:", MASTERPRESS_DOMAIN);
    $mode_select = WOOF_HTML::select(
      array("id" => $p."mode", "name" => "type_options[mode]"), 
      self::modes(),
      $mode
    );

    $theme_label = __("Theme:", MASTERPRESS_DOMAIN);
    $theme_select = WOOF_HTML::select(
      array("id" => $p."theme", "name" => "type_options[theme]"), 
      array(
        "Default" => "default",
        "Cobalt" => "cobalt",
        "Eclipse" => "eclipse",
        "Elegant" => "elegant",
        "Lesser Dark" => "lesser-dark",
        "Monokai" => "monokai",
        "Neat" => "neat",
        "Night" => "night",
        "Ruby Blue" => "rubyblue",
        "Sunburst" => "sunburst",
        "XQuery Dark" => "xq-dark"
      ),
      $theme
    );

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)

$html = <<<HTML

    <div class="f">
      <label for="{$p}minheight">{$minheight_label}</label>
      <div id="fw-{$p}minheight" class="fw">
        <input id="{$p}minheight" type="text" name="type_options[minheight]" value="{$minheight}" class="text" /><span class="note">{$minheight_note}</span>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}maxheight">{$maxheight_label}</label>
      <div id="fw-{$p}maxheight" class="fw">
        <input id="{$p}maxheight" type="text" name="type_options[maxheight]" value="{$maxheight}" class="text" /><span class="note">{$maxheight_note}</span>
        <p class="note">{$maxheight_grow_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}mode">{$mode_label}</label>
      <div id="fw-{$p}mode" class="fw">
        {$mode_select}
        <input type="hidden" name="hiddenmode" id="{$p}hiddenmode" class="hiddenmode" value="{$hiddenmode}" />

        <div id="{$p}modeselect-wrap">
          <input id="{$p}modeselect" type="checkbox" name="type_options[modeselect]" {$modeselect_checked_attr} value="yes" class="checkbox" />
          <label for="{$p}modeselect" class="checkbox">{$modeselect_label}</label>
        </div>

      </div>



    </div>
    <!-- /.f -->

    <div id="{$p}theme-f" class="f">
      <label for="{$p}theme">{$theme_label}</label>
      <div id="fw-{$p}theme" class="fw">
        {$theme_select}
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
    return array("minheight", "maxheight", "theme", "mode");
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
    Static Method: ui_prop 
      Returns an array of keys describing additional data properties to store against the core field value
      
    Returns:
      Array - of string keys for properties required. 
  */

  public static function ui_prop() {
    return "mode";
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
      return htmlspecialchars($field->raw());
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

    $value = "";

    if (!$field->blank()) {
      $value = htmlspecialchars($field->raw());
      
      $hiddenmode = $field->prop("mode");

    } else {
      $hiddenmode = self::option_value($options, "mode");
    }

    
    
    $class = "";
    
    
    $mode_select = '';

    if (isset($options["modeselect"]) && $options["modeselect"] == "yes") {
      $mode_select = WOOF_HTML::open("div", array("class" => "modeselect-wrap"));
      $mode_select .= WOOF_HTML::tag("label", array(), __("Syntax Mode: ", MASTERPRESS_DOMAIN));
      
      $class = " editor-ui-with-modeselect";
      
      $attr = array("id" => "{{prop_id}}-mode", "name" => "{{prop_name}}[mode]", "class" => "modeselect");
      
      if ($readonly) {
        $attr["readonly"] = "readonly";
      }
      
      $mode_select .= WOOF_HTML::select(
        $attr,
        self::modes(),
        $hiddenmode
      );
    
      $mode_select .= WOOF_HTML::close("div");
      
    }
    
    $html = <<<HTML

    <div class="editor-ui{$class}">
    <textarea id="{{id}}" $readonly name="{{name}}">{$value}</textarea>
    <input type="hidden" name="hiddenmode" id="{{id}}-hiddenmode" class="hiddenmode" value="{$hiddenmode}" />
      {$mode_select}
    </div>

HTML;

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
    return __("( no content )", MASTERPRESS_DOMAIN);
  }
  
  
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public function mode() {
    
    $options = $this->info->type_options;
    
    if (isset($options["modeselect"]) && $options["modeselect"] == "yes") {
      return $this->prop("mode");
    } else {
      return $options["mode"];
    }
    
  }
  
  public function value($args = array()) {

    global $wf;

    $mode = $this->mode();
    
    $r = wp_parse_args(
      $args,
      array(
        "smart_shortcodes" => true,
        "markdown_filtered" => true,
        "filtered" => false
      )
    );
  
    $html = $this->data->val;
    
    if ( in_array($mode, array("markdown", "gfm")) ) {
      $html = $wf->markdown( $html );
      
      if ( WOOF::is_true_arg( $r, "smart_shortcodes" ) ) {
        $html = preg_replace( "/\{(\/?[^\}]*\/?)\}/", "[$1]", $html );
      }

      if (WOOF::is_true_arg( $r, "markdown_filtered" ) ) {
        $html = apply_filters( "the_content", $html );
      }

    } 

    if (WOOF::is_true_arg( $r, "filtered" ) ) {
      $html = apply_filters( "the_content", $html );
    }
      
    return $html;
    
  }
  
  function col() {
    global $wf;
    
    return $wf->truncate($this->raw(), "length=200"); 
  }
  
  function __toString() {
    return $this->value();
  } 

}