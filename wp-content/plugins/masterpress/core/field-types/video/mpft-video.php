<?php

class MPFT_Video extends MPFT {
  
  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: JavaScript Enqueues - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  /*
    Static Method: enqueue 
      A callback to enqueue any JavaScript dependencies for this field type. 
      Field-type specific javascript files must be placed directly in the folder for this field type.
      
    Example Implementation:
      > wp_enqueue_script( 'jquery-some-plugin', plugins_url('jquery.some-plugin.js', __FILE__), array("jquery") );
      
  */
  
  public static function enqueue() {

  }


  // - - - - - - - - - - - - - - - - - - - - - - - - MasterPress: Documentation / i18n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  /*
    Static Method: __s 
      Returns the singular form of the name of this field type, ready for translation
  */

  public static function __s() {
    return __("Video", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __p 
      Returns the plural form of the name of this field type, ready for translation
  */

  public static function __p() {
    return __("Videos", MASTERPRESS_DOMAIN);
  }

  /*
    Static Method: __description 
      Displayed in the tooltip for this field type in the "Field Type" dropdown list of the Create / Edit Field UI
      and underneath the list when this field type is the selected type
  */

  public static function __description() {
    return __("Link to hosted online videos from popular online video services", MASTERPRESS_DOMAIN);
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




  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post (AJAX) - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public static function overlay($img) {
    return $img->watermark(MPU::type_image("video", "play-overlay.png"), "at=c");
  }
  
  
  public static function fetch_video_info($url, $encode = true) {
    
    $result = array("error" => false);
    
    global $wf;
    
    $nurl = $url; // normalized URL

    $video_id = "";
    
    $host = false;
    
    $matches = false;
    
    // YouTube (Page) - http://www.youtube.com?/watch?v=VIDEO_ID

    if (!$matches) {
      if (preg_match("/youtube.com\/watch\?v=([^&]+)/", $url, $matches)) {
        $host = "youtube";
      }
    }
  
    // YouTube (Player) - http://www.youtube.com/v/VIDEO_ID

    if (!$matches) {
      if (preg_match("/youtube.com\/v\/([^&\?]+)/", $url, $matches)) {
        $host = "youtube";
      }
    }

    // YouTube (Embed) - http://www.youtube.com/embed/VIDEO_ID

    if (!$matches) {
      if (preg_match("/youtube.com\/embed\/([^&\?]+)/", $url, $matches)) {
        $host = "youtube";
      }
    }

    // YouTube (Page - Shortened) - http://youtu.be/VIDEO_ID

    // YOUTUBE - http://www.youtube.com?/watch?v=VIDEO_ID

    if (!$matches) {
      if (preg_match("/youtu\.be\/([^&\?]+)/", $url, $matches)) {
        $host = "youtube";
      }
    }
  
    if (!$matches) {
      if (preg_match("/vimeo.com\/([^&\?]+)/", $url, $matches)) {
        $host = "vimeo";
      }
    }

    if (!$matches) {
      if (preg_match("/player.vimeo.com\/video\/([^&\?]+)/", $url, $matches)) {
        $host = "vimeo";
      }
    }
    
    if ($host) {
      
      $video_id = $matches[1];
      $nurl = "http://youtu.be/" + $video_id;
      
      $result["host"] = $host;  
      $result["video_id"] = $video_id;  
      $result["url"] = $nurl;
     
                    
      list($watch_url, $video_url) = self::urls($video_id, $host);
      
      $result["url"] = $watch_url;
    }
    
    if ($host == "youtube") {
      
      $data = wp_remote_get("http://gdata.youtube.com/feeds/api/videos/$video_id?v=2&alt=json");

      if (!is_wp_error($data)) {
        if (@$data["response"]["code"] == 200) {

          $body = @$data["body"];
      
        
          $info = json_decode($body, true);

          // extract the more useful info out of the result data
      
          $entry = $info["entry"];
          $mg = @$info["entry"]['media$group'];
      
          if (isset($entry["published"])) {
            $result["published"] = @$entry["published"]['$t'];
          }

          if (isset($entry["updated"])) {
            $result["updated"] = @$entry["updated"]['$t'];
          }
          
          $result["title"] = @$mg['media$title']['$t'];
          $result["description"] = @$mg['media$description']['$t'];
          $result["keywords"] = @$mg['media$keywords']['$t'];
          $result["aspect_ratio"] = @$mg['yt$aspectRatio']['$t'];
          $result["duration"] = @$mg['yt$duration']['seconds'];

          $tc = array();
        
          $categories = @$mg['media$category'];
        
          if (is_array($categories)) {
        
            foreach ($categories as $cat) {
              if (isset($cat['label'])) {
                $tc[$cat['$t']] = $cat['label'];
              }
          
            }
        
          }
      
        
          if ($encode) {
            $result["categories"] = json_encode($tc);
          } else {
            $result["categories"] = $tc;
          }
          
        
            
          $thumbnails = $mg['media$thumbnail'];
      
          $ta = array();
        
          $max_width = 0;
        
          if (is_array($thumbnails)) {
        
            foreach ($thumbnails as $thumb) {
            
              $ti = array(
                "url" => isset($thumb["url"]) ? $thumb["url"] : "",
                "name" => isset($thumb['yt$name']) ? $thumb['yt$name'] : "",
                "time" => isset($thumb['time']) ? $thumb['time'] : ""
              );
            
              if (@$thumb["width"] > $max_width) {
                $preview_thumb = $ti;
                $max_width = @$thumb["width"];
              }
            

              $ta[$thumb['yt$name']] = $ti;
          
            }
        
            if ($encode) {
              $result["thumbnails"] = json_encode($ta);
            } else {
              $result["thumbnails"] = $ta;
            }
          
            if (isset($preview_thumb)) {
              $img = $wf->image_from_url(@$preview_thumb["url"]);
            
              if ($img && $img->exists()) {
                self::process_thumbnail_info($img, $result);
 
                $result["thumbnail"] = @$preview_thumb["url"]; // store the best thumbnail as the primary one
              }
            
            }
          
          
          
          }
        
        
        } else {
        
          $result["error"] = __("A video could not be found on YouTube at the specified URL", MASTERPRESS_DOMAIN);
        
        } 
      
      } else {
        $result["error"] = $data->get_error_message();
      }
      
      
    } else if ($host == "vimeo") {
      
      
      $data = wp_remote_get("http://vimeo.com/api/v2/video/".$video_id.".json");

      if (!is_wp_error($data)) {

        if ($data["response"]["code"] == 200) {

          $body = @$data["body"];
      
        
          $info = json_decode($body, true);

          // extract the more useful info out of the result data
      
          $entry = $info[0];
      
          $result["published"] = @$entry["upload_date"];
          $result["title"] = @$entry['title'];
          $result["description"] = htmlentities(@$entry['description']);
          $result["keywords"] = @$entry['tags'];
          $result["width"] = @$entry['width'];
          $result["height"] = @$entry['height'];
          $result["duration"] = @$entry['duration'];
          $result["user_name"] = @$entry['user_name'];
          $result["user_url"] = @$entry['user_url'];

          $result["stats"] = json_encode( array(
            "number_of_likes" => @$entry['stats_number_of_likes'],
            "number_of_plays" => @$entry['stats_number_of_plays'],
            "number_of_comments" => @$entry['stats_number_of_comments']
          ));
          
          $ta = array();
        
          $max_width = 0;
        
          $thumbnails = array(
            "small" => @$entry['thumbnail_small'],
            "medium" => @$entry['thumbnail_medium'],
            "large" => @$entry['thumbnail_large'],
            "user_small" => @$entry['user_portrait_small'],
            "user_medium" => @$entry['user_portrait_medium'],
            "user_large" => @$entry['user_portrait_large'],
            "user_huge" => @$entry['user_portrait_huge']
          );
        
          foreach ($thumbnails as $name => $url) {
          
            $ti = array(
              "url" => $url,
              "name" => $name
            );

            $ta[$name] = $ti;
        
          }
      
          if ($encode) {
            $result["thumbnails"] = json_encode($ta);
          } else {
            $result["thumbnails"] = $ta;
          }
        
          $img = $wf->image_from_url(@$entry["thumbnail_large"]);
        
          if ($img && $img->exists()) {
            self::process_thumbnail_info($img, $result);
            $result["thumbnail"] = @$entry["thumbnail_large"]; // store the best thumbnail as the primary one
          }
        
        } else {
        
          $result["error"] = __("A video could not be found on Vimeo at the specified URL", MASTERPRESS_DOMAIN);
        
        }
      
      } else {
        $result["error"] = $data->get_error_message();
      }
      
    }
    
    return $result;
    
  }
  
  public static function process_thumbnail_info($img, &$result) {
    list($tw, $th) = array(120, 90);
    list($stw, $sth) = array(84, 63);
    
    $thumb = self::overlay($img->resize("w=".($tw * 2)."&h=".($th * 2)."&crop=true&q=90&up=0"));
    $summary_thumb = $img->resize("w=".($stw * 2)."&h=".($sth * 2)."&crop=true&q=90&up=0");

    $result["thumb"] = $thumb->url();
    $result["summary_thumb"] = $summary_thumb->url();

    $result["thumb_width"] = $tw;
    $result["thumb_height"] = $th;

    $result["width"] = $tw * 2;
    $result["height"] = $th * 2;

    $result["summary_thumb_width"] = $stw;
    $result["summary_thumb_height"] = $sth;
  }
   
  public static function video_info($encode = true) {
    
    global $wf;
    
    $video_id = @$_GET["video_id"];
    $host = @$_GET["host"];
    
    $result = array();
    list($watch_url, $video_url) = self::urls($video_id, $host);

    $result["url"] = $watch_url;
      

    if ($host == "youtube") {
      $data = wp_remote_get("http://gdata.youtube.com/feeds/api/videos/$video_id?v=2&alt=json");

      if (!is_wp_error($data)) {
        if (@$data["response"]["code"] == 200) {

          $body = @$data["body"];
      
        
          $info = json_decode($body, true);

          // extract the more useful info out of the result data
      
          $entry = $info["entry"];
          $mg = @$info["entry"]['media$group'];
        
          if (isset($entry["published"])) {
            $result["published"] = @$entry["published"]['$t'];
          }

          if (isset($entry["updated"])) {
            $result["updated"] = @$entry["updated"]['$t'];
          }
          
          $result["title"] = @$mg['media$title']['$t'];
          $result["description"] = str_replace("\n", "<br />", @$mg['media$description']['$t']);
          $result["keywords"] = @$mg['media$keywords']['$t'];
          $result["aspect_ratio"] = @$mg['yt$aspectRatio']['$t'];
          $result["duration"] = @$mg['yt$duration']['seconds'];

          $tc = array();
        
          $categories = @$mg['media$category'];
        
          if (is_array($categories)) {
        
            foreach ($categories as $cat) {
              if (isset($cat['label'])) {
                $tc[$cat['$t']] = $cat['label'];
              }
          
            }
        
          }
      
        
          if ($encode) {
            $result["categories"] = json_encode($tc);
          }
        
            
          $thumbnails = $mg['media$thumbnail'];
      
          $ta = array();
        
          $max_width = 0;
        
          if (is_array($thumbnails)) {
        
            foreach ($thumbnails as $thumb) {
            
              $ti = array(
                "url" => isset($thumb["url"]) ? $thumb["url"] : "",
                "name" => isset($thumb['yt$name']) ? $thumb['yt$name'] : "",
                "time" => isset($thumb['time']) ? $thumb['time'] : ""
              );
            
              if (@$thumb["width"] > $max_width) {
                $preview_thumb = $ti;
                $max_width = @$thumb["width"];
              }
            

              $ta[$thumb['yt$name']] = $ti;
          
            }
        
            if ($encode) {
              $result["thumbnails"] = json_encode($ta);
            }
          
            if (isset($preview_thumb)) {
              $img = $wf->image_from_url(@$preview_thumb["url"]);
            
              if ($img && $img->exists()) {
                self::process_thumbnail_info($img, $result);
                $result["thumbnail"] = @$preview_thumb["url"]; // store the best thumbnail as the primary one
              }
            
            }
          
          
          
          }
        
          self::ajax_success($result);
          exit();
        
        } else {
        
          return self::ajax_error(__("A video could not be found on YouTube at the specified URL", MASTERPRESS_DOMAIN));
        
        } 
      
      } else {
        return self::ajax_error(__($data->get_error_message()));
      }
      
      
    } else if ($host == "vimeo") {
      
      
      $data = wp_remote_get("http://vimeo.com/api/v2/video/".$video_id.".json");

      if (!is_wp_error($data)) {

        if ($data["response"]["code"] == 200) {

          $body = @$data["body"];
      
        
          if ($encode) {
            $info = json_decode($body, true);
          }
        
          // extract the more useful info out of the result data
      
          $entry = $info[0];
        
          $result["published"] = @$entry["upload_date"];
          $result["title"] = @$entry['title'];
          
          $result["description"] = @htmlentities($entry['description'], ENT_IGNORE, 'UTF-8', false);
          $result["keywords"] = @$entry['tags'];
          $result["width"] = @$entry['width'];
          $result["height"] = @$entry['height'];
          $result["duration"] = @$entry['duration'];
          $result["user_name"] = @$entry['user_name'];
          $result["user_url"] = @$entry['user_url'];

          $result["stats"] = json_encode( array(
            "number_of_likes" => @$entry['stats_number_of_likes'],
            "number_of_plays" => @$entry['stats_number_of_plays'],
            "number_of_comments" => @$entry['stats_number_of_comments']
          ));
          
          
          $ta = array();
        
          $max_width = 0;
        
          $thumbnails = array(
            "small" => @$entry['thumbnail_small'],
            "medium" => @$entry['thumbnail_medium'],
            "large" => @$entry['thumbnail_large'],
            "user_small" => @$entry['user_portrait_small'],
            "user_medium" => @$entry['user_portrait_medium'],
            "user_large" => @$entry['user_portrait_large'],
            "user_huge" => @$entry['user_portrait_huge']
          );
        
          foreach ($thumbnails as $name => $url) {
          
            $ti = array(
              "url" => $url,
              "name" => $name
            );

            $ta[$name] = $ti;
        
          }
      
          if ($encode) {
            $result["thumbnails"] = json_encode($ta);
          }
        
          $img = $wf->image_from_url(@$entry["thumbnail_large"]);
        
          
          if ($img && $img->exists()) {
            self::process_thumbnail_info($img, $result);
            $result["thumbnail"] = @$entry["thumbnail_large"]; // store the best thumbnail as the primary one
          }
                  
          self::ajax_success($result);
          exit();
        
        } else {
        
          return self::ajax_error(__("A video could not be found on Vimeo at the specified URL", MASTERPRESS_DOMAIN));
        
        }
      
      } else {
        return self::ajax_error(__($data->get_error_message()));
      }
      
    }
    
    
  }


  // - - - - - - - - - - - - - - - - - - - - - - - - - WordPress: Create / Edit Post - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

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
    Static Method: ui_prop 
      Returns an array of keys describing additional data properties to store against the core field value
      
    Returns:
      Array - of string keys for properties required. 
  */

  public static function ui_prop() {
    return "host,video_id,updated,published,duration,title,description,thumbnail,thumbnails,keywords,categories,aspect_ratio,width,height,user_name,stats";
  }
  

 /*
    Static Method: ui_lang 
      Returns an array of language strings accessable via the "lang" property of the mpft-based JavaScript widget 
      
    Returns:
      array - an array of strings. These strings should be prepared for i18n with Wordpress' __() function
  */
   
  public static function ui_lang() {
    return array(
      "youtube" => __("YouTube", MASTERPRESS_DOMAIN),
      "vimeo" => __("Vimeo", MASTERPRESS_DOMAIN),
      "error_invalid_url" => __("The URL entered does not appear to be valid. Please enter a valid URL for a video hosted on YouTube or Vimeo", MASTERPRESS_DOMAIN)
    );
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
    return array("services");
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
    return __("( none )", MASTERPRESS_DOMAIN);
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
    // here we'd show the thumbnail if available

    global $wf;
    
    $summary_thumb = "";
    $empty = "";
    $summary_display_title = "";
    $host_name = "";
    $prop_thumbnail = "";
    $prop_duration = "";
    $prop_host = "";
    $no_url = __("( no URL )", MASTERPRESS_DOMAIN);

    $empty = "empty";

    list($stw, $sth) = array(84, 63);

    $summary_thumb = WOOF_Image::empty_mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_url, "class" => "summary-thumb") );

    if (!$field->blank()) {
      
      $prop_title = $field->prop("title");
      $prop_thumbnail = $field->prop_val("thumbnail");
      $prop_video_id = $field->prop("video_id");
      $prop_host = $field->prop("host");
      $prop_duration = $field->prop("duration");

      $summary_display_title = WOOF::truncate_advanced($prop_title, 50, $etc = ' &hellip; ', false, true);

      $host_name = self::host_name($prop_host);
      
      if ($prop_thumbnail && $prop_thumbnail != "") {
        $img = $wf->image_from_url($prop_thumbnail);
      
        if ($img && $img->exists()) {
          $summary_thumb = $img->mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_url, "thumb_only" => true, "class" => "summary-thumb"));
        }
    
      }
      
    }

    $html = <<<HTML

    <div class="summary-content">
      
      {$summary_thumb}
            
      <div class="summary-info">
        <span class="title">{$summary_display_title}</span>
        <span class="host"><span class="host-type {$prop_host}"><span class="host-name">{$host_name}</span><span class="duration">({$prop_duration})</span></span></span>
      </div>
    
    </div>
      
HTML;

    return $html;

  }

  public static function host_name($host) {
    
    if ($host == "youtube") {
      return __("YouTube", MASTERPRESS_DOMAIN);
    } else if ($host == "vimeo") {
      return __("Vimeo", MASTERPRESS_DOMAIN);
    }
   
    return "";
  }


  public static function urls($video_id, $host) {
    
    if ($host == "youtube") {
      $watch_url = "http://youtube.com/embed/".$video_id."?info=0&controls=0&autoplay=1";
      $video_url = "http://youtu.be/".$video_id;
    } else if ($host == "vimeo") {
      $watch_url = "http://player.vimeo.com/video/".$video_id."?autoplay=1&title=0&byline=0";
      $video_url = "http://vimeo.com/".$video_id;
    }

    return array($watch_url, $video_url);
    
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
    
    // $field here is a MEOW_Field, which is a class that encapsulates the value of a field AND the info associated with it

    $options = $field->info->type_options;

    $readonly = WOOF_HTML::readonly_attr( !$field->is_editable() );

    $no_url = __("( no URL entered )", MASTERPRESS_DOMAIN);
    $url_label = __("URL:", MASTERPRESS_DOMAIN);

    $button_refresh = __("Refresh Info", MASTERPRESS_DOMAIN);
    
    $fetching_info_message = __("Fetching video info&hellip; please wait", MASTERPRESS_DOMAIN);

    $visit_video_title = __("Visit video page", MASTERPRESS_DOMAIN);

    $watch_video_title = __("Watch video", MASTERPRESS_DOMAIN);
    
    $style = "";

    if (isset($options["height"]) && is_numeric($options["height"])) {
      $style .= "height: ".$options["height"]."px;";
    }

    $watch_url = "";
    $video_url = "";
    $host = "";
    $title = "";
    $value = "";
    $video_id = "";
    
    $empty = "empty";
    
    if (!$field->blank()) {
      $value = htmlspecialchars($field->value());
      $empty = "";
      $video_id = $field->prop("video_id");
      $host = $field->prop("host");

      $title = $field->prop("title");
      
      if ($title == "") {
        $empty = "empty";
      }
      
      if ($video_id && $video_id != "") {
        list($watch_url, $video_url) = self::urls($video_id, $host);
      }
      
    }

    $prop_inputs = self::prop_inputs($field, self::ui_prop());

    $summary_thumb = "";
    $thumb = "";
    
    
    $prop_title = $field->prop("title");
    $prop_host = $field->prop("host");
    $prop_duration = $field->prop("duration");
    $prop_published = self::format_date($field->prop("published"));
    $prop_updated = self::format_date($field->prop("updated"));
    $prop_thumbnail = $field->prop_val("thumbnail");
    
    $published_style = "";
    $updated_style = "";
    
    if ($field->prop("published") == "") {
      $published_style = ' style="display:none;" ';
    }

    if ($field->prop("updated") == "") {
      $updated_style = ' style="display:none;" ';
    }
    
    $prop_video_id = $field->prop("video_id");
      
    $display_title = WOOF::truncate_advanced($prop_title, 60, $etc = ' &hellip; ', false, true);
    $summary_display_title = WOOF::truncate_advanced($prop_title, 50, $etc = ' &hellip; ', false, true);


    $host_name = self::host_name($prop_host);
    
    
    list($tw, $th) = array(120, 90);
    list($stw, $sth) = array(84, 63);

    $orientation = "square";

    $thumb = WOOF_Image::empty_mp_thumb(array("w" => $tw, "h" => $th, "no_image" => $no_url, "class" => "managed thumb") );
    $summary_thumb = WOOF_Image::empty_mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_url, "class" => "managed summary-thumb" ) );


    if ($prop_thumbnail && $prop_thumbnail != "") {
      $img = $wf->image_from_url($prop_thumbnail);
      
      if ($img && $img->exists()) {

        $watermark = MPU::type_image("video", "play-overlay.png");
        $watermark_args = array("at" => "c", "h" => "60%");

        $thumb = $img->mp_thumb(array("w" => $tw, "h" => $th, "no_image" => $no_url, "watermark" => $watermark, "watermark_args" => $watermark_args, "link_attr" => array("class" => "thumb iframe", "href" => $watch_url), "class" => "thumb managed") );
        $summary_thumb = $img->mp_thumb(array("w" => $stw, "h" => $sth, "no_image" => $no_url, "class" => "managed summary-thumb", "thumb_only" => true ) );
      }
    
    }
    
    $html = <<<HTML

    <div class="state {$empty}">

      {$prop_inputs}

      <div class="summary-content">
        
        {$summary_thumb}
      
        <div class="summary-info">
          <span class="title">{$summary_display_title}</span>
          <span class="host"><span class="host-type {$prop_host}"><span class="host-name">{$host_name}</span><span class="duration">({$prop_duration})</span></span></span>
        </div>
      
      </div>
      
      <div class="ui-content">
       
      {$thumb}
      
      <div class="url-info">
        
        <div class="f f-url">
          <label for="{{id}}" class="{prop_host}">{$url_label}</label>
          <input id="{{id}}" name="{{name}}" autocomplete="off" {$readonly} type="text" value="{$value}" class="url text" />
        </div>
          
        <div class="info">

          <span class="error-message"><i></i>error</span>  
          
          <span class="fetching-info-message progress-message">{$fetching_info_message}</span>  

          <div class="title">
            <a href="{$video_url}" target="_blank" title="{$visit_video_title}" class="{$prop_host} title-link">{$display_title}</a>
            <button class="text refresh with-icon" title="{$button_refresh}" type="button">{$button_refresh}</button>
          </div>
        
          <div class="prop-wrap">
            
            
            <ul class="prop">
              <li class="duration">{$prop_duration}</li>
              <li class="published" {$published_style}>Published: <span class="val">{$prop_published}</span></li>
              <li class="updated" {$updated_style}>Updated: <span class="val">{$prop_updated}</span></li>
            </ul>
          </div>
        
          
        </div>
        
      </div>
      
      </div>

    </div>
    <!-- /.state -->

HTML;

    return $html;

  }

  // - - - - - - - - - - - - - - - - - - - - MEOW: Property Handling - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  public function get_prop($name) {
    global $wf;
    
    $vid = $this->prop_val("video_id");
    
    if (!$this->blank() && ( is_woof_silent($vid) || $vid == "" )) {

      // WARNING - DON'T check the prop value using "prop" here, as we'll get infinite recursion
      
      // the video URL has been set, but the video id has not
      // this will generally be because the field URL has been set programatically
      
      $info = self::fetch_video_info($this->value(), false);
      
      if (!$info["error"]) {
        $this->fill_prop(__CLASS__, $info);
      }
      
    } 
    
    $val = $this->prop_val($name);
    
    if (isset($val) && !is_woof_silent($val)) {
      
      if ($name == "thumbnail") {
        return $wf->image_from_url($val);
      } else if ($name == "description") {
        return html_entity_decode($val);
      } else if ($name == "duration") {
        
        if (is_numeric($val)) {
          return date("H:i:s", (int) $val);
        }
        
      }
      
      return $val;
      
    }
  
  }
  
  // - - - - - - - - - - - - - - - - - - - - MEOW: API helpers - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

  
  function embed_args($args = array(), $options = null) {
    global $wf;  
    
    $title = $this->prop("title");
    $video_id = $this->prop("video_id");
    
    $id = "player-".md5($video_id);

    $r = wp_parse_args(
      $args,
      array(
        "id" => $id,
        "version" => "8",
        "width" => 640,
        "height" => 360
      )
    );
    
    $id = $r["id"];
    
    $params = array( "allowScriptAccess" => "always", "wmode" => "transparent" );
    $flashvars = array();
    $attr = array( "id" => $id );
    
    if (isset($r["params"])) {
      $params = wp_parse_args( 
        $r["params"],
        $params
      );
    }
    

    if (isset($r["flashvars"])) {
      $flashvars = wp_parse_args( $r["flashvars"], $flashvars );
    }
    
    
    if (isset($r["attr"])) {
      $attr = wp_parse_args( 
        $r["attr"],
        $attr
      );
    }



    if (isset($r["w"])) {
      $r["width"] = $r["w"];
    }

    if (isset($r["h"])) {
      $r["height"] = $r["h"];
    }
    
    $host = $this->prop_val("host");
    
    if (isset($r["options"])) {
      $opts = $r["options"];
    } else {
      $opts = $options;
    }
    
    if (is_null($opts)) {
      $opts = array();
    }


    if ($host == "youtube") {
      
      $options = wp_parse_args( 
        $opts,
        array( 
          "rel" => 0,
          "showinfo" => 0,
          "enablejsapi" => 1,
          "playerapiid" => $id,
          "version" => 3,
          "modestbranding" => 1,
          "controls" => 1
        )
      );
      
      if ($options["controls"] == "1") {
        $r["height"] = $r["height"] + 26;
      }
    
    } else if ($host == "vimeo") {
      
      $options = wp_parse_args( 
        $opts,
        array( 
          "title" => 1,
          "byline" => 1,
          "portrait" => 1,
          "color" => "00ADEF",
          "autoplay" => 0,
          "loop" => 0,
          "api" => 1
        )
      );
      
    }
    
      
    $r["options"] = $options;
    $r["id"] = $id;
    $r["video_id"] = $video_id;
    $r["params"] = $params;
    $r["attr"] = $attr;
    $r["flashvars"] = $flashvars;
      
    return $r;

  }
  

  // - - - - - - - - - - - - - - - - - - - - MEOW: Template API ( all API methods are non-static ) - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
  
  function embed_url($args = array()) {
  
    $r = wp_parse_args(
      $args,
      array(
        "autoplay" => 1
      )
    );  
    
    $host = $this->prop_val("host");
    
    if ($host == "youtube") {
      return "http://www.youtube.com/embed/".$this->prop("video_id")."?".http_build_query($r);
    } else if ($host == "vimeo") {
      return "http://player.vimeo.com/video/".$this->prop("video_id")."?".http_build_query($r);
    }
    
    
  }
  
  function json() {

    $json = array();
    $json["href"] = $this->url();
    
    $json["host"] = $this->prop("host");
    $json["video_id"] = $this->prop("video_id");
    $json["width"] = (int) $this->prop("width");
    $json["height"] = (int) $this->prop("height");
    
    
    return $json;
    
  }
  
  function embed($args = array("params" => array(), "attr" => array(), "flashvars" => array()), $options = null, $get = true) {

    $r = $this->embed_args($args, $options);
    
    $code = "";
    
    if (!is_array($r["options"])) {
      $r["options"] = array();
    }
    
    $host = $this->prop_val("host");
    
    if ($host == "youtube") {
        
      $code .= '<div id="'.$r["id"].'">';
      $code .= '<iframe class="youtube-player" type="text/html" width="'.$r["width"].'" height="'.$r["height"].'" src="http://www.youtube.com/embed/'.$r['video_id'].'?'.http_build_query($r['options']).'" frameborder="0"></iframe>';
      $code .= '</div>';
      $code .= '<script type="text/javascript">';
      $code .= 'swfobject.embedSWF("http://www.youtube.com/v/'.$r['video_id'].'?'.http_build_query($r['options']).'",';
      $code .= '"'.$r["id"].'", "'.$r["width"].'", "'.$r["height"].'", "'.$r["version"].'", null, '.json_encode($r["flashvars"], JSON_FORCE_OBJECT).', '.json_encode($r["params"], JSON_FORCE_OBJECT).', '.json_encode($r["attr"], JSON_FORCE_OBJECT).');';
      $code .= '</script>';
      
    }
    else {

      $code .= '<iframe src="http://player.vimeo.com/video/'.$r['video_id'].'?'.http_build_query($r['options']).'" width="'.$r["width"].'" height="'.$r["height"].'" frameborder="0"></iframe>';
      
    }
    
    if (!$get) {
      echo $code;
    }
    
    return $code;
  }
  
  function col($args = "w=100&q=90") {
    return $this->thumb($args);
  }
  
  function thumb($args = "w=80&h=60&q=90") {
   
    $video_id = $this->prop_val("video_id");
    $host = $this->prop_val("host");
    $img = $this->prop("thumbnail");
    $prop_title = $this->prop("title");
    
    if ($video_id != "" && $img) {
      
      if ($host == "youtube") {
        $watch_url = "http://youtube.com/embed/".$video_id."?info=0&controls=0&autoplay=1";
      } else if ($host == "vimeo") {
        $watch_url = "http://player.vimeo.com/video/".$video_id."?autoplay=1&title=0&byline=0";
      }
    
      $r = wp_parse_args( $args, array() );
      
      $r["watermark"] = MPU::type_image("video", "play-overlay.png");
      $r["watermark_args"] = array("at" => "c", "h" => "60%");
      $r["link_attr"] = array( "href" => $watch_url, "class" => "thumb iframe", "title" => $prop_title );
      
      return $img->mp_thumb($r);

    }
    
  }
    
  function url() {
    return $this->value();
  }
  
  function __toString() {
    // this should be changed back to the embed code below, toString needs some work to fix up.
    return $this->embed(array(), null, true);
    
    //return $this->value();
  } 

}