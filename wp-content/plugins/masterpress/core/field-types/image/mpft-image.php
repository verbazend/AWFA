<?php

class MPFT_Image extends MPFT_FileBase {


  function __construct( $info, $field ) {
    
    global $wf;
    $ic = $wf->get_image_class();
    
    $this->info = $info;
    $this->field = $field;
    
    $data = $this->data();
    
    // infer the path, url to the image.
    
    if (isset($data)) {

      if (isset($data->val) && $data->val != "") {
        $url = $data->val;
        $path = WOOF_File::infer_content_path($url);
    
        $attr = array(
          "alt" => $this->prop("alt"),
          "title" => $this->prop("title")
        );
            
        $this->file = new $ic( $path, $url, $attr );
        
        $this->valid = FALSE;
        
        if ($this->file->exists()) {

          $this->valid = TRUE;

        } else {
          // try to access the file at the URL 

          if ($path == "" && defined("MASTERPRESS_RESOLVE_EXTERNAL_URLS")) {

            /* 
            
            We should ONLY if the file could not exist on this server
            if we try to hit a file that could exist on this server but doesn't, this can cause an infinite httpd loop
            where a 404 causes another WP page load, which causes additional 404s, which cause more page loads ...
            
            Note that infer_content_path above will return an empty string if the URL is external
            
            */
            
            $this->file = $wf->image_from_url($url);
            $this->valid = $this->file->exists();
          }
        }
        
        if (!$this->valid) {
          $this->file = new WOOF_Silent(__("The image could not be found", MASTERPRESS_DOMAIN));
        }
        
      } else {
        $this->file = new WOOF_Silent(__("no file path has been set", MASTERPRESS_DOMAIN));
      }
      
    
    }
    
    

  } 

  public static function supports_image() {
    return true;
  }

  public static function supports_text() {
    return false;
  }
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Image", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Images", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("An image uploader, with thumbnail preview and the ability to generate thumbnails of any size", MASTERPRESS_DOMAIN);
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

    $mfs = WOOF_File::to_bytes(ini_get('upload_max_filesize'));
    $mps = WOOF_File::to_bytes(ini_get('post_max_size'));

    $html = "";
    
    $limit = self::get_filesize_limit();

    $limit_mb = WOOF_File::format_filesize($limit, "MB", false);

    $defaults = array(
      "filename_case" => "lowercase",
      "filename_sanitize" => "dashes",
      "filename_dashes_underscores" => "dashes",
      "allowed_maxsize" => $limit_mb,
      "allowed_types" => array("jpg","jpeg","png","gif")
    );

    $options = wp_parse_args( $options, $defaults );
    $p = self::type_prefix(__CLASS__);

    $allowed_types_label = __("Allowed File Types:", MASTERPRESS_DOMAIN);
    $allowed_maxsize_label = __("Maximum Size:", MASTERPRESS_DOMAIN);

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

    $allowed_types_note = __("The image field type is intended only for images that are displayable on a website.<br />For other image types, use the <em>file</em> field type instead", MASTERPRESS_DOMAIN);

    MPFT::incl("file");

    foreach (array("jpg","jpeg","png","gif") as $ext) {
      $file_types_items[ MPFT_File::file_type_label($ext) ] = $ext;
    }

    $allowed_types_checkboxes = WOOF_HTML::input_checkbox_group( "type_options[allowed_types][]", $p."allowed-types-", $file_types_items, $options["allowed_types"], WOOF_HTML::open("div", "class=fwi"), WOOF_HTML::close("div")); 

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
        <input id="{$p}allowed_maxsize" name="type_options[allowed_maxsize]" type="text" maxlength="4" value="{$options['allowed_maxsize']}" class="text" /><span class="note">{$allowed_maxsize_note}</span>
        <p class="note">{$allowed_maxsize_blank_note}</p>
      </div>
    </div>
    <!-- /.f -->

    <div class="f f-allowed-types">
      <p class="label">{$allowed_types_label}</p>
      <div class="fw">
      {$allowed_types_checkboxes}
      <div class="controls"><button type="button" class="button button-small select-all">Select All</button><button type="button" class="button button-small select-none">Select None</button></div>
      <p class="note">{$allowed_types_note}</p>

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

  /*
    Static Method: ui_prop 
      Returns an array of keys describing additional data properties to store against the core field value
      
    Returns:
      Array - of string keys for properties required. 
  */

  public static function ui_prop() {
    return "source_url,attachment_id,alt,title";
  }

  protected static function summary_thumb_wh($width, $height) {
    if ($height == $width) {
      return array(63, 63);
    }

    if ($height >= $width) {
      return array(47, 63);
    }

    return array(84, 63);
  }

  protected static function thumb_wh($width, $height) {
    if ($height == $width) {
      return array(120, 120);
    }

    if ($height >= $width) {
      return array(90, 120);
    }

    return array(160, 120);
  }


  /*
    Static Method: ui_lang 
      Returns an array of language strings accessable via the "lang" property of the mpft-based JavaScript widget 
      
    Returns:
      array - an array of strings. These strings should be prepared for i18n with Wordpress' __() function
  */
   
  public static function ui_lang() {
    return array(
      "confirm_clear" => __("Clear Image: Are you Sure?\n\nNote: the image file still will remain on the server", MASTERPRESS_DOMAIN),
      "no_image" => __("( no image )", MASTERPRESS_DOMAIN)
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

    
    $fileurl = "";
    $thumb = "";
    $width = "";
    $height = "";
    $filesize = "";
    $summary_thumb = "";
    $filename = "";
    $filetype = "";
    $empty = "";
    $orientation = "";
    
    $no_image = __("( no image )", MASTERPRESS_DOMAIN);

    $view_image = __("View Image", MASTERPRESS_DOMAIN);

    
    // setup defaults for an empty image

    list($tw, $th) = array(120, 90);
    list($stw, $sth) = array(82, 39);

    $empty = "empty";
    $orientation = "square";

    $thumb = WOOF_Image::empty_mp_thumb(array("w" => $tw, "h" => $th, "no_image" => $no_image, "class" => "managed thumb") );
    $summary_thumb = WOOF_Image::empty_mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_image, "class" => "managed summary-thumb" ) );
        
    if (!$field->blank()) {

      $image = $field->file();

      if ($image->exists()) {

        $empty = "";
         
        $width = $image->width();
        $height = $image->height();

        $orientation =  $height == $width ? "square" : ( $height > $width ? "portrait" : "landscape" );

        list($tw, $th) = self::thumb_wh($width, $height);
        list($stw, $sth) = self::summary_thumb_wh($width, $height);
        
        $filesize   = $image->filesize();
        $filename   = $image->basename();
        $filetype   = $image->filetype();
        
        if ($image->is_external()) {
          $fileurl    = $field->value();
          $source_url = $fileurl;
        } else {
          $fileurl    = $image->permalink();
        }

        $thumb = $image->mp_thumb(array("w" => $tw, "h" => $th, "no_image" => $no_image, "class" => "thumb managed", "href" => $fileurl));
        $summary_thumb = $image->mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_image, "class" => "managed summary-thumb", "thumb_only" => true ) );

      } 
          
    } else {
      $empty = "empty";
      $orientation = "square";
    }

    if (isset($source_url)) {
      $prop_source_url = $source_url;
    } else {
      $prop_source_url = $field->prop("source_url");
    }
    
    $prop_alt = esc_attr( $field->prop("alt") );
    $prop_title = esc_attr( $field->prop("title") );
      
    $replace_image_label = __("Replace Image:", MASTERPRESS_DOMAIN);
    $choose_image_label = __("Choose Image:", MASTERPRESS_DOMAIN);

    $attributes_label = __("Attributes", MASTERPRESS_DOMAIN);
    $attributes_title = __("Edit HTML attributes (alternate text / title)", MASTERPRESS_DOMAIN);

    $url_label = __("Enter Image URL:", MASTERPRESS_DOMAIN);

    $alt_label = __("Alternate Text:", MASTERPRESS_DOMAIN);
    $title_label = __("Title:", MASTERPRESS_DOMAIN);

    $attributes_title = __("Image Tag Attributes", MASTERPRESS_DOMAIN);

    $button_replace_label = __("Replace Image&hellip;", MASTERPRESS_DOMAIN);

    $button_from_computer_label = __("From Computer&hellip;", MASTERPRESS_DOMAIN);
    $button_from_media_library_label = __("From Media Library&hellip;", MASTERPRESS_DOMAIN);
    $button_from_url_label = __("From URL&hellip;", MASTERPRESS_DOMAIN);
    $button_from_existing_uploads_label = __("From Existing Uploads&hellip;", MASTERPRESS_DOMAIN);

    $button_from_media_library_data_library = MasterPress::use_new_media() ? "new" : "legacy";

    $button_clear_label = __("Clear", MASTERPRESS_DOMAIN);
    $button_delete_label = __("Delete", MASTERPRESS_DOMAIN);

    $button_download_label = __("Download", MASTERPRESS_DOMAIN);
    $button_cancel_label = __("Cancel", MASTERPRESS_DOMAIN);
    
    $button_clear_title = __("Clear field without deleting the image file", MASTERPRESS_DOMAIN);
    $button_delete_title = __("Clear field AND delete the image file", MASTERPRESS_DOMAIN);

    $w_times_h = __("width &times; height", MASTERPRESS_DOMAIN);

    $drop_label = __("Drop image<br />file here<br />to upload", MASTERPRESS_DOMAIN);

    $base_url = MASTERPRESS_CONTENT_URL;
    $dir = 'uploads/';

    $media_library_progress_message = __("Fetching URL from Media Library...", MASTERPRESS_DOMAIN);
    $upload_progress_message = __("Uploading...", MASTERPRESS_DOMAIN);
    $fetching_info_message = __("Fetching Image Information...", MASTERPRESS_DOMAIN);

    $dowloading_progress_message = __("Downloading Image...", MASTERPRESS_DOMAIN);

    $href = ' href="'.$fileurl.'" ';
      
    if ($fileurl == "") {
      $href = "";
    }
    
    // populate the media library dialog
    // $media_library_dialog 

    $upload_html = "";
    $download_html = "";
    $clear_html = "";
    $labels_html = "";
    $ml_html = "";
    
    $choose_html = "";
    
    $readonly = WOOF_HTML::readonly_attr( !$field->is_editable() );
    
    if ($wf->the_user->can("upload_files") && $field->is_editable()) {
      
      $choose_html = <<<HTML
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
          <button type="button" class="button button-small button-from-media-library" data-library="{$button_from_media_library_data_library}">{$button_from_media_library_label}</button>

        </div>
      
      </div>
      <!-- /.choose-controls -->
HTML;
   
    $clear_html = <<<HTML
    <ul class="controls">
      <li><button title="{$button_clear_title}" type="button" class="text with-icon clear">{$button_clear_label}</button></li>
    </ul>
HTML;

    } else {

      $choose_html = <<<HTML
<input id="{{id}}" name="{{name}}" value="{$fileurl}" type="hidden" class="value" autocomplete="off" />
HTML;

    }

    $html = <<<HTML

    <div class="ui-state {$empty}">
      

      {$prop_inputs}
    
      <div class="thumbs {$orientation}">

        <div id="{{id}}_drop_area" class="drop-area">{$drop_label}</div>

        {$summary_thumb}
        {$thumb}

      </div>
      <!-- /.thumbs -->

      <div class="info-controls">

        <div class="name-controls">
          <h5><a target="_blank" href="{$fileurl}" tabindex="-1" title="{$filename}" class="fancybox file-link with-icon"><span class="filename">{$view_image}</span></a></h5>
          {$clear_html}
        </div>

        <ul class="prop">
          <li class="filetype">{$filetype}</li>
          <li class="dimensions" title="{$w_times_h}"><span class="width">{$width}</span> &times; <span class="height">{$height}</span></li>
          <li class="filesize">{$filesize}</li>
          <li class="attributes"><button type="button" class="text with-icon attributes" title="{$attributes_title}">{$attributes_label}</button></li>
        </ul>

        {$choose_html}

        <div class="media-library-progress">
          <span class="fetching-message progress-message">{$media_library_progress_message}</span>  
          <span class="fetching-info-message progress-message">{$fetching_info_message}</span>  
        </div>
        <!-- /.media-library-progress -->
      
      
        <div class="upload-progress">

          <span class="name"></span>

          <div class="progress-bar-with-val">
            <div class="progress-bar"><div class="border"><div><span class="bar">&nbsp;</span></div></div></div>
            <div class="val">0%</div>
          </div>

          <span class="uploading-message progress-message">{$upload_progress_message}</span>  
          <span class="fetching-info-message progress-message">{$fetching_info_message}</span>  

        </div>
        <!-- /.upload-progress -->


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
      <!-- /.info-controls -->

      <div class="dialogs">
        
        <div class="mpft-image-attributes-dialog mp-dialog mpv" data-title="{$attributes_title}">
          <div class="pad">
          
          <div class="f">
            <label for="{{prop_id}}-alt">{$alt_label}</label>
            <div class="fw">
              <input id="{{prop_id}}-alt" $readonly name="{{prop_name}}[alt]" value="{$prop_alt}" type="text" class="text alt" />
            </div>
            <!-- /.fw -->
          </div>
          <!-- /.f -->
        
          <div class="f">
            <label for="{{prop_id}}-title">{$title_label}</label>
            <div class="fw">
              <input id="{{prop_id}}-title" $readonly name="{{prop_name}}[title]" value="{$prop_title}" type="text" class="text title"  />
            </div>
            <!-- /.fw -->
          </div>
          <!-- /.f -->
        
          </div>
          <!-- /.pad -->
        </div>
    
      </div>
      <!-- /.dialogs -->
      
      
    </div>
    <!-- /.ui-state -->

HTML;

    return $html;

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

    global $wf;

    $image = "";
    
    list($stw, $sth) = array(82, 39);

    $no_image = __("( no image )", MASTERPRESS_DOMAIN);

    $empty = "empty";
    $thumb = WOOF_Image::empty_mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_image, "class" => "summary-thumb") );
    
    if (!$field->blank()) {
      $image = $field->file();

      if ($image && $image->exists()) {
        $width = $image->width();
        $height = $image->height();

        list($stw, $sth) = self::summary_thumb_wh($width, $height);
        
        $thumb = $image->mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_image, "thumb_only" => true, "class" => "summary-thumb"));

      }
    }

    $html = $thumb;

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
    $no_image = __("( no image )", MASTERPRESS_DOMAIN);
    $html = <<<HTML
    <div class="mp-thumb managed empty"><span class="no-image">{$no_image}</span></div>
HTML;

    return $html;
  }
  



  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post (AJAX) - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public static function image_info() {

    global $wf;
    $ic = $wf->get_image_class();

    $url = trim($_REQUEST["url"]);
    $path = WOOF_File::infer_content_path($url);
    
    if (file_exists($path)) {
    
      $image = new $ic($path, $url);

      if ($image->exists()) {
        
        if ($url && $info = $image->info()) {
          // generate the thumbnail

          $width = $info["width"];
          $height = $info["height"];
          
          list($tw, $th) = self::thumb_wh($width, $height);
          list($stw, $sth) = self::summary_thumb_wh($width, $height);

          $thumb = $image->resize("w=".($tw * 2)."&h=".($th * 2)."&crop=true&q=90&up=0");
          $summary_thumb = $image->resize("w=".($stw * 2)."&h=".($sth * 2)."&crop=true&q=90&up=0");

          $info["thumb"] = $thumb->url();
          $info["summary_thumb"] = $summary_thumb->url();

          $info["thumb_width"] = min($tw, $thumb->width());
          $info["thumb_height"] = min($th, $thumb->height());

          $info["summary_thumb_width"] = min($stw, $summary_thumb->width());
          $info["summary_thumb_height"] = min($sth, $summary_thumb->height());

          
          self::ajax_success( $info );
        }
      }
      
    } else {
      
      // self::ajax_error("image not found");
      
      // call an "image from url" method to pull the image down 
      // this method would need to delete the image after it grabs the info / generates thumbnails,
      // since the whole point of requesting
      // image info from another store would be to avoid having large images on THIS web server.
      
    }
    
  }
  
  public static function download_image() {
    self::do_download_file("image");
  }
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  function col($args = "w=100") {
    
    if (!$this->blank()) {
      return $this->file->mp_thumb($args);
    }
  
  }

  function json() {
    
    global $wf;
    
    $json = array();
    
    
    if ($this->file->exists()) {
      $json["href"] = $this->file->url();
    }
    
    $json["alt"] = $this->prop("alt");
    $json["title"] = $this->prop("title");
    $json["width"] = $this->width();
    $json["height"] = $this->height();
    
    $json["size"] = $this->size("AUTO", true, "");
    $json["bytes"] = $this->bytes();
    
    $att_id = $this->prop("attachment_id");
    
    if ($att_id != "") {
      $json["attachment_id"] = $wf->attachment( $att_id )->id();
    }
    
    
    return $json;
  }
  
  public function get_delegate() {
    return $this->file;
  }

  function __toString() {
    
    if ($this->file->exists()) {
      return $this->file->tag();
    }
  
    return "";
  }
  
}