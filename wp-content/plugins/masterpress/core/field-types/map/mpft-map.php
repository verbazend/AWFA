<?php

class MPFT_Map extends MPFT {
  
  
  /*
    Static Method: enqueue 
      A callback to enqueue any JavaScript dependencies for this field type. 
      Field-type specific javascript files must be placed directly in the folder for this field type.
      
    Example Implementation:
      > wp_enqueue_script( 'jquery-some-plugin', plugins_url('jquery.some-plugin.js', __FILE__), array("jquery") );
      
  */
  
  public static function enqueue() {
    wp_enqueue_script('mpft-gmaps', 'http://maps.googleapis.com/maps/api/js?sensor=false' . self::key() );
  }
  
  public static function key() {
    if (defined("MPFT_MAP_API_KEY")) {
      return "&key=" . MPFT_MAP_API_KEY;
    } 
    
    return "";
    
  }
  
  public static function __s() {
    return __("Map", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("Maps", MASTERPRESS_DOMAIN);
  }

  public static function __description() {
    return __("A Google Maps&trade; control allowing interactive selection of a geo-coordinate", MASTERPRESS_DOMAIN);
  }

  public static function __category() {
    return "Value-Based Content (Specialized)";
  }


  // -- MasterPress Admin UI methods

  public static function ui_prop() {
    return "zoom";
  }
  
  
  /*
    Static Method: ui_lang 
      Returns an array of language strings accessable via the "lang" property of the mpft-based JavaScript widget 
      
    Returns:
      array - an array of strings. These strings should be prepared for i18n with Wordpress' __() function
  */
   
  public static function ui_lang() {
    return array(
      "enter_search_term" => __("Please enter a search term", MASTERPRESS_DOMAIN)
    );
  }


  public static function summary( MEOW_Field $field ) {
  
    $url = "";
    
    $img = '<div class="none">'. __("No Map", MASTERPRESS_DOMAIN) . '</div>';
    
    if (!$field->blank()) {
      $zoom = $field->prop("zoom");
    
      if (!$zoom) {
        $zoom = 7;
      } else {
        $zoom = max(1, $zoom - 1);
      }
      
      $url = 'http://maps.googleapis.com/maps/api/staticmap?center=' . $field->value() . '&zoom=' . $zoom . '&size=236x136&maptype=roadmap&sensor=false' . self::key();
      $img = '<img src="' . $url . '" />';
    }
      
    $html = <<<HTML
    
    <div class="map">
      $img      
    </div>
      
HTML;

    return $html;
    
  }

  
  

  public static function empty_summary( MEOW_Field $field ) {
    return __("( no location )", MASTERPRESS_DOMAIN);
  }
  
  public static function ui( MEOW_Field $field ) {

    $value = $field->value();

    $zoom = $field->prop("zoom");

    if (!$zoom) {
      $zoom = 1; // needs a default setting
    }

    $state = "";
    
    if ($field->blank()) {
      $state = "empty";
    }
    
    $no_location = __("( No Location Set )", MASTERPRESS_DOMAIN);
    
    $label_button_restore = __("Restore", MASTERPRESS_DOMAIN);
    $label_button_clear = __("Clear", MASTERPRESS_DOMAIN);

    $title_button_restore = __("Restore the location back to the currently stored latitude, longitude, and zoom level", MASTERPRESS_DOMAIN);
    $title_button_clear = __("Clear the location", MASTERPRESS_DOMAIN);
    
    $label_search = __("Use the map to set a new location, or enter a place to search for:", MASTERPRESS_DOMAIN);
    $label_search_empty = __("Click the map to begin location set up or search for a location below:", MASTERPRESS_DOMAIN);
    $label_button_search = __("Search", MASTERPRESS_DOMAIN);
    
    $label_lat = __("Latitude:", MASTERPRESS_DOMAIN);
    $label_lng = __("Longitude:", MASTERPRESS_DOMAIN);
    $label_zoom = __("Zoom:", MASTERPRESS_DOMAIN);
    
    $click_to_begin = __("Set Location", MASTERPRESS_DOMAIN);
    
    $editable = $field->is_editable();
    
    $search_html = "";
    $buttons_html = "";

    $data_readonly = ' data-readonly="true" ';
    
    $begin_html = <<<HTML
    <div class="map-obscure"></div>
HTML;
    
    if ( $editable ) {
      
      $begin_html = <<<HTML
      <a href="#" class="map-begin"><span>{$click_to_begin}</span></a>
HTML;

      $data_readonly = "";
    
      $buttons_html = <<<HTML
      <div class="buttons">
        <button type="button" title="{$title_button_restore}" class="button button-small restore">{$label_button_restore}</button>
        <button type="button" title="{$title_button_clear}" class="button button-small clear">{$label_button_clear}</button>
      </div>
HTML;
      
      $search_html = <<<HTML
      <div class="map-search">
        <p class="label-search">{$label_search}</p>
        <p class="label-search-empty">{$label_search_empty}</p>
        <input id="{{id}}-search" name="map_search" type="text" class="text search" />
        <button type="button" class="button button-small">{$label_button_search}</button>
      </div>
HTML;

    }
    
    $html = <<<HTML

    <input type="hidden" name="{{prop_name}}[zoom]" id="{{prop_id}}-{{id}}" value="{$zoom}" class="prop-zoom" />
    <input type="hidden" name="{{name}}" id="{{id}}" value="{$value}" class="value" />
    
    <div class="state {$state}">
      
      <div class="location clearfix">
        <i></i>
        <span class="no-location">{$no_location}</span>

        <ul>
          <li class="lat"><span>{$label_lat}<b class="value"></b></span></li>
          <li class="lng"><span>{$label_lng}<b class="value"></b></span></li>
          <li class="zoom"><span>{$label_zoom}<b class="value"></b></span></li>
        </ul>
    
        {$buttons_html}
      
      </div>
    
      {$search_html}
      
      <div class="map-wrap">
        <div class="map-canvas-wrap">
        <div class="map-canvas" {$data_readonly}>
      
        </div>
        </div>

        {$begin_html}

        
      </div>
    
    </div>
  
HTML;

    return $html;

    
  }


  public function lat_lng() {
    
    $ret = array();
    
    if (!$this->blank()) {
      
      $ll = explode(",", $this->value());
      $ret = array("lat" => $ll[0], "lng" => $ll[1]);

    }
    
    return $ret;
    
  } 

  public function lat() {
    
    if (!$this->blank()) {
      extract ($this->lat_lng());
        
      if (isset($lat)) {
        return $lat;
      }

    }
    
    return false;
    
  }

  public function lng() {
    
    if (!$this->blank()) {
      extract ($this->lat_lng());
        
      if (isset($lng)) {
        return $lng;
      }

    }
    
    return false;

  }
  
  public function image() {

    global $wf;
    
    if ($this->blank()) {
      return new WOOF_Silent( __( "Cannot show an image - the location for this map field has not been set", MASTERPRESS_DOMAIN ) );
    } else {

      $zoom = $this->prop("zoom");

      if (!$zoom) {
        $zoom = 7;
      } else {
        $zoom = max(1, $zoom - 1);
      }
      
      $url = 'http://maps.googleapis.com/maps/api/staticmap?center=' . $this->value() . '&zoom=' . $zoom . '&size=640x640&scale=2&maptype=roadmap&sensor=false';
      return $wf->image_from_url( $url );

    }
    
  }
  
  public function embed($attr = array(), $params = array()) {
    
    if ($this->blank()) {

      return new WOOF_Silent( __( "Cannot show an image - the location for this map field has not been set", MASTERPRESS_DOMAIN ) );

    } else {
      $r = wp_parse_args( 
        $attr,
        array(
          "width" => "100%", 
          "height" => "300", 
          "frameborder" => "0",
          "scrolling" => "no",
          "marginheight" => "0",
          "marginwidth" => "0"
        )
      );

      $p = wp_parse_args(
        $params,
        array(
          "oe" => "UTF-8",
          "ie" => "UTF8",
          "t" => "m",
          "output" => "embed"
        )
      );
    
      $zoom = $this->prop("zoom");

      if (!$zoom) {
        $zoom = 7;
      } else {
        $zoom = max(1, $zoom - 1);
      }
      
      $p["z"] = $zoom;
      $p["ll"] = $this->value();
    
      $r["src"] = "http://maps.google.com/maps?" . build_query($p);
    
      return WOOF_HTML::tag("iframe", $r, "");  
    
    }
    
  }
  
  
  public function col() {

    global $wf;
    
    if ($this->blank()) {
      $html = "";
    } else {

      $url = "";

      $img = '<div class="none">'. __("No Map", MASTERPRESS_DOMAIN) . '</div>';

      $zoom = $this->prop("zoom");

      if (!$zoom) {
        $zoom = 7;
      } else {
        $zoom = max(1, $zoom - 1);
      }
      
      
      $url = 'http://maps.googleapis.com/maps/api/staticmap?center=' . $this->value() . '&zoom=' . $zoom . '&size=200x200&scale=2maptype=roadmap&sensor=false';

      $large_url = 'http://maps.googleapis.com/maps/api/staticmap?center=' . $this->value() . '&zoom=' . $zoom . '&size=640x640&scale=2&maptype=roadmap&sensor=false';
      
      $image = $wf->image_from_url( $large_url );
      $large_url = $image->url();

      $img = '<img src="' . $url . '" />';

      $html = <<<HTML

      <div class="mp-thumb" style="width: 100px; height: 100px;">
        <a href="{$large_url}" class="thumbnail">$img</a>      
      </div>

HTML;

    }

    return $html;

  }
  
  public function __toString() {
    $embed = $this->embed();
    
    if (!is_woof_silent($embed)) {
      return $embed;
    }
    
    return "";
  }

}

?>