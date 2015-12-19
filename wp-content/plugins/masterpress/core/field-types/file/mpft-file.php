<?php

class MPFT_File extends MPFT_FileBase {
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("File", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Files", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("A file upload control which can be restricted to allow specific file extensions", MASTERPRESS_DOMAIN);
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
    return "Media";
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

    $html = "";
    
    $limit = self::get_filesize_limit();

    $limit_mb = WOOF_File::format_filesize($limit, "MB", false);

    $defaults = array(
      "filename_case" => "lowercase",
      "filename_sanitize" => "dashes",
      "allowed_types" => array("csv","doc","docx","gz","m4a", "md", "pdf","pps","ppt","pptx","psd","rar","rtf","swf","txt","vcf","xls","xlsx","xml","zip")
    );

    $options = wp_parse_args( $options, $defaults );
    $p = self::type_prefix(__CLASS__);

    $allowed_maxsize = self::option_value($options, "allowed_maxsize");
    $allowed_types = self::option_value($options, "allowed_types");

    $allowed_field = implode(",", $allowed_types);

    $allowed_types_label = __("Allowed File Types:", MASTERPRESS_DOMAIN);
    $allowed_maxsize_label = __("Maximum Size:", MASTERPRESS_DOMAIN);

    $allowed_field_label = __("More Allowed Types:", MASTERPRESS_DOMAIN);
    $allowed_field_note = __("Separate file extensions that you would like to allow with commas.<br /><strong>Important:</strong> in the interests of security, take care to avoid file types that are executable on your server that may be exploitable by malicious users.<br /><br />Alternatively, you can populate the list by selecting from the list of typical field types below:", MASTERPRESS_DOMAIN);

    $filename_label = __("File Name Handling", MASTERPRESS_DOMAIN);
    $filename_label_note = __("specify how file names should be sanitized on upload", MASTERPRESS_DOMAIN);

    $filename_sanitize_label = __("Sanitize file name:", MASTERPRESS_DOMAIN);
    $filename_sanitize_note = __("Sanitization removes special characters and replaces word boundaries with the specified character", MASTERPRESS_DOMAIN);

    $filename_case_label = __("Change case to:", MASTERPRESS_DOMAIN);

    $filename_case_select = WOOF_HTML::select(
      array("id" => $p."filename_case", "name" => "type_options[filename_case]"), 
      array(
        "lower-case" => "lowercase", 
        "Title-Case" => "titlecase", 
        "UPPER-CASE" => "uppercase", 
        "Preserve (No Change)" => "none"
      ),
      $options["filename_case"]
    );

    $filename_sanitize_select = WOOF_HTML::select(
      array("id" => $p."filename_sanitize", "name" => "type_options[filename_sanitize]"), 
      array(
        __("With Dashes ( - )", MASTERPRESS_DOMAIN) => "dashes", 
        __("With Underscores ( _ )", MASTERPRESS_DOMAIN) => "underscores", 
        __("None (Don't Sanitize)", MASTERPRESS_DOMAIN) => "none"
      ),
      $options["filename_sanitize"]
    );


    $allowed_maxsize_note = __("( MB )", MASTERPRESS_DOMAIN);
    $allowed_maxsize_blank_note = sprintf(__("This value <strong>cannot exceed</strong> the maximum upload size<br />for your server, which is currently set to <strong>%s</strong>.", MASTERPRESS_DOMAIN), WOOF_File::format_filesize($limit, "MB", true, "&nbsp;"));

    $allowed_label = __("File Restrictions", MASTERPRESS_DOMAIN);
    $allowed_label_note = __("restrict allowable files by type and file size", MASTERPRESS_DOMAIN);

    // setup variables to insert into the heredoc string
    // (this is required where we cannot call functions within heredoc strings)


    $html .= <<<HTML

    <div class="filename-handling-wrap">

    <h4><i class="highlighter"></i>{$filename_label}<span>&nbsp;&nbsp;-&nbsp;&nbsp;{$filename_label_note}</span></h4>

    <div class="f">
      <label for="{$p}filename_sanitize">{$filename_sanitize_label}</label>
      <div id="fw-{$p}filename_sanitize" class="fw">
        {$filename_sanitize_select}
        <p class="note">{$filename_sanitize_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f">
      <label for="{$p}filename_case">{$filename_case_label}</label>
      <div id="fw-{$p}filename_case" class="fw">
        {$filename_case_select}
      </div>
    </div>
    <!-- /.f -->

    </div>


    <div class="allowed-wrap divider">

    <h4><i class="warning-shield"></i>{$allowed_label}<span>&nbsp;&nbsp;-&nbsp;&nbsp;{$allowed_label_note}</span></h4>


    <div class="f">
      <label for="{$p}allowed_maxsize">{$allowed_maxsize_label}</label>
      <div id="fw-{$p}allowed_maxsize" class="fw">
        <input id="{$p}allowed_maxsize" name="type_options[allowed_maxsize]" type="text" maxlength="4" value="{$allowed_maxsize}" class="text" /><span class="note">{$allowed_maxsize_note}</span>
        <p class="note">{$allowed_maxsize_blank_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f f-allowed-types">
      <p class="label">{$allowed_types_label}</p>
      
      <div class="fw">


      <div class="clearfix">
      <textarea id="{$p}allowed_field" class="mono" name="allowed_field">{$allowed_field}</textarea>
      <p id="allowed-field-note" class="note">{$allowed_field_note}</p>
      </div>
    
      

      <div id="{$p}allowed-types-wrap">
HTML;

      foreach (WOOF_File::file_type_categories() as $header => $exts) {

        $html .= '<div class="file-category">';
        $html .= "<h5>".$header."</h5>";

        $file_types_items = array();

        $proxy = "";

        if ($header == "Camera RAW Files") {
          $proxy = "file-type-raw";
        }

        foreach ($exts as $ext) {
          $file_types_items[ self::file_type_label($ext, $proxy) ] = $ext;
        }

        $html .= WOOF_HTML::input_checkbox_group( "type_options[allowed_types][]", $p."allowed-types-", $file_types_items, $allowed_types, WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")); 

        $html .= '<div class="controls"><button type="button" class="button button-small select-all">Select All</button><button type="button" class="button button-small select-none">Select None</button></div>';

        $html .= '</div>';
      }

      $html .= <<<HTML

      </div>
      
      <div id="{$p}allowed-types-custom">
        
      </div>
      
      </div>
    </div>
    <!-- /.f -->



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
    return "allowed_types,allowed_maxsize,filename_case,filename_sanitize";
  }

  public static function file_type_label($ext, $proxy = "") {
    return '<i class="file-type file-type-'.$ext.' '.$proxy.'"></i><strong>.'.$ext.'</strong> ( '.WOOF_File::$short_file_types[$ext]." )";
  }

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
    
    if (!$field->blank()) {
      $file = $field->file();

      if ($file && $file->exists()) {
        return array("file-type file-type-".$file->extension());
      }
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

    $filename = "";
    $filesize = "";
    $filetype = "";
    $filename_trunc = "";
    $filetype_trunc = "";


    if (!$field->blank()) {
      $file = $field->file();

      if ($file && $file->exists()) {

        $filename = $file->basename();
        $filesize = $file->filesize();
        $filetype = $file->filetype();

        $filename_trunc = WOOF::truncate_advanced($filename, 26, $etc = ' &hellip; ', false, true);
        $filetype_trunc = WOOF::truncate_advanced($filetype, 25, $etc = ' &hellip; ', false, true);

      }
    }

    $html = <<<HTML

    <div class="summary">
      <span class="name" title="{$filename}">{$filename_trunc}</span>
      <span class="type-size"><span class="type" title="{$filetype}">{$filetype_trunc}</span>
      <br><span class="size">{$filesize}</span></span>
    </div>

HTML;

    return $html;

  }

  /*
    Static Method: ui_prop 
      Returns an array of keys describing additional data properties to store against the core field value
      
    Returns:
      Array - of string keys for properties required. 
  */

  public static function ui_prop() {
    return "source_url,attachment_id";
  }

  /*
    Static Method: ui_lang 
      Returns an array of language strings accessable via the "lang" property of the mpft-based JavaScript widget 
      
    Returns:
      array - an array of strings. These strings should be prepared for i18n with Wordpress' __() function
  */
   
  public static function ui_lang() {
    return array(
      "confirm_clear" => __("Clear File: Are you Sure?\n\nNote: the file will still remain on the server", MASTERPRESS_DOMAIN),
      "delete_error" => __("Sorry the file could not be deleted. Try using the 'Clear' button instead", MASTERPRESS_DOMAIN),
      "no_file" => __("( no file )", MASTERPRESS_DOMAIN)
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

    global $wf;

    $prop_inputs = self::prop_inputs($field, "attachment_id");

    $prop_source_url = $field->prop("source_url");
    
    $no_file = __("( no file )", MASTERPRESS_DOMAIN);

    $empty = "";
    $filename = "";
    $filesize = "";
    $filetype = "";
    $fileurl = "";
    $filetype_class = "";
    $filename_trunc = "";
    $filetype_trunc = "";

    $summary_filename_trunc = "";
    $summary_filetype_trunc = "";

    
    if (!$field->blank()) {
      $file = $field->file();

      if ($file && $file->exists()) {

        $filename = $file->basename();
        $filesize = $file->filesize();
        $filetype = $file->filetype();

        if ($file->is_external()) {
          $fileurl = $field->value();
        } else {
          $fileurl = $file->permalink();
        }
        
        $filetype_class = "file-type file-type-".$file->extension();

        $filename_trunc = WOOF::truncate_advanced($filename, 37, $etc = ' &hellip; ', false, true);
        $filetype_trunc = WOOF::truncate_advanced($filetype, 36, $etc = ' &hellip; ', false, true);

        $summary_filename_trunc = WOOF::truncate_advanced($filename, 26, $etc = ' &hellip; ', false, true);
        $summary_filetype_trunc = WOOF::truncate_advanced($filetype, 25, $etc = ' &hellip; ', false, true);

      }
    } else {

      $empty = "empty";
      $filename = $no_file;
      $filename_trunc = $no_file;

    }

    $replace_image_label = __("Replace File:", MASTERPRESS_DOMAIN);
    $choose_image_label = __("Choose File:", MASTERPRESS_DOMAIN);

    $url_label = __("Enter File URL:", MASTERPRESS_DOMAIN);

    $button_view_download_label = __("View / Download", MASTERPRESS_DOMAIN);
    $button_delete_label = __("Delete", MASTERPRESS_DOMAIN);

    $button_from_computer_label = __("From Computer&hellip;", MASTERPRESS_DOMAIN);
    $button_from_media_library_label = __("From Media Library&hellip;", MASTERPRESS_DOMAIN);
    $button_from_url_label = __("From URL&hellip;", MASTERPRESS_DOMAIN);
    $button_from_existing_uploads_label = __("From Existing Uploads&hellip;", MASTERPRESS_DOMAIN);

    $button_clear_label = __("Clear", MASTERPRESS_DOMAIN);
    $button_delete_label = __("Delete", MASTERPRESS_DOMAIN);

    $button_download_label = __("Download", MASTERPRESS_DOMAIN);
    $button_cancel_label = __("Cancel", MASTERPRESS_DOMAIN);

    $button_clear_title = __("Clear field without deleting the file", MASTERPRESS_DOMAIN);
    $button_delete_title = __("Clear field and delete the file", MASTERPRESS_DOMAIN);

    $drop_label = __("Drop file here to upload", MASTERPRESS_DOMAIN);

    $base_url = MASTERPRESS_CONTENT_URL;
    $dir = 'uploads/';

    $media_library_progress_message = __("Fetching URL from Media Library...", MASTERPRESS_DOMAIN);
    $upload_progress_message = __("Uploading...", MASTERPRESS_DOMAIN);
    $fetching_info_message = __("Fetching File Information...", MASTERPRESS_DOMAIN);
    $dowloading_progress_message = __("Downloading File...", MASTERPRESS_DOMAIN);

    
    $upload_html = "";
    $download_html = "";
    $file_controls_html = "";
    $labels_html = "";
    $controls_html = "";
    
    $drop_html = "";
    
    if ($wf->the_user->can("upload_files") && $field->is_editable()) {
      
      $controls_html = <<<HTML
      
      <div class="choose-controls">

        <h5 class="replace-label">{$replace_image_label}</h5>
        <h5 class="choose-label">{$choose_image_label}</h5>

        <div class="buttons">

          <div class="file-uploader { input: '#{{id}}', inputName: '{{id}}_file', ids: { drop: '{{id}}_drop_area' }, base_url: '{$base_url}', params: { dir: '{$dir}' }, limit: 1, lang: { buttonChoose: '{$button_from_computer_label}', buttonReplace: '{$button_from_computer_label}' } }">
            <input id="{{id}}" name="{{name}}" value="{$fileurl}" type="hidden" class="value" autocomplete="off" />
            <div class="uploader-ui"></div>
          </div>
          <!-- /.file-uploader -->

          <button type="button" class="button button-small button-from-url">{$button_from_url_label}</button> 
          <button type="button" class="button button-small button-from-media-library">{$button_from_media_library_label}</button>

        </div>

      </div>
      
      
HTML;

      $file_controls_html = <<<HTML
      
      <div class="file-controls">
        <ul>
          <li><button type="button" type="button" class="text with-icon view">{$button_view_download_label}</button></li>
          <li><button type="button" type="button" class="text with-icon clear">{$button_clear_label}</button></li>
        </ul>
      </div>
      
HTML;

      $drop_html = <<<HTML
      <div id="{{id}}_drop_area" class="drop-area">{$drop_label}</div>
HTML;

    } else {
      
      $controls_html = <<<HTML
      <input id="{{id}}" name="{{name}}" value="{$fileurl}" type="hidden" class="value" autocomplete="off" />
HTML;
      
    }
    
    $html = <<<HTML


    <div class="summary">
      <span class="name" title="{$filename}">{$summary_filename_trunc}</span>
      <span class="type-size"><span class="type" title="{$filetype}">{$summary_filetype_trunc}</span>
      <br><span class="size">{$filesize}</span></span>
    </div>

    <div class="ui-state {$empty}">

    {$prop_inputs}

    <a href="{$fileurl}" class="file-info" tabindex="-1" target="_blank">

      {$drop_html}

      <h4 class="name"><i class="{$filetype_class}"></i>{$filename_trunc}</h4>
      <ul class="prop">
        <li class="filetype">{$filetype}</li>
        <li class="filesize">{$filesize}</li>
      </ul>

    </a>
    <!-- /.file-info -->

    {$file_controls_html}

    <div class="media-library-progress">
      <span class="fetching-message progress-message">{$media_library_progress_message}</span>  
      <span class="fetching-info-message progress-message">{$fetching_info_message}</span>  
    </div>
    <!-- /.media-library-progress -->
      
    <div class="upload-progress">

      <div class="upload-progress-well">
        <span class="name"><i></i></span>

        <div class="progress-bar-with-val">
          <div class="progress-bar"><div class="border"><div><span class="bar">&nbsp;</span></div></div></div>
          <div class="val">0%</div>
        </div>

      </div>

      <span class="uploading-message progress-message">{$upload_progress_message}</span>  
      <span class="fetching-info-message progress-message">{$fetching_info_message}</span>  

    </div>
    <!-- /.upload-progress -->

    {$controls_html}



    <div class="from-url-ui">
      <label for="{{id}}-url">{$url_label}</label>
      <input id="{{prop_id}}-source_url" name="{{prop_name}}[source_url]" type="text" value="{$prop_source_url}" class="text url" />

      <div class="buttons">
        <button type="button" class="button button-small download">{$button_download_label}</button>
        <button type="button" class="button button-small cancel">{$button_cancel_label}</button>
      </div>
    </div>
    <!-- /.from-url -->

    <div class="download-progress">
      <span class="downloading-message progress-message">{$dowloading_progress_message}</span>  
      <span class="fetching-info-message progress-message">{$fetching_info_message}</span>  
    </div>
    <!-- /.download-progress -->



    </div>
    <!-- /.ui-state -->

HTML;

    return $html;

  }




  // - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post (AJAX) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public static function upload_info() {

    global $wf;

    $url = trim($_REQUEST["url"]);
    $path = WOOF_File::infer_content_path($url);
    
    if (file_exists($path)) {
    
      $fc = $wf->get_file_class();
      
      $file = new $fc($path, $url);

      if ($file->exists()) {
        
        if ($url && $info = $file->info()) {
          self::ajax_success( $info );
        }
      }
      
    } else {
      
      // pull the image down?
      // this method would need to delete the image after it grabs the info / generates thumbnails,
      // since the whole point of requesting
      // image info from another store would be to avoid having large images on THIS web server.
      
    }
    
  }

  public static function download_file() {
    self::do_download_file("file");
  }


  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public function value() {
    return $this->url();
  }

  public function json() {
    
    global $wf;
    $json = array();
    
    if ($this->file->exists()) {
      $json["href"] = $this->file->url();
    }
    
    $json["size"] = $this->size("AUTO", true, "");
    $json["bytes"] = $this->bytes();
    
    $att_id = $this->prop("attachment_id");
    
    if ($att_id != "") {
      $json["attachment_id"] = $wf->attachment( $att_id )->id();
    }
    
    return $json;
  }
  
    
}