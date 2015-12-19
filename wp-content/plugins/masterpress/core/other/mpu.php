<?php

// MasterPress Utility Class

class MPU {

  public static function current_url() {
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
  }
  
  public static function in_csv($val, $csv) {
    return in_array($val, explode(",", $csv));
  }

  public static function path($file) {
    return MASTERPRESS_DIR.$file;
  }

  public static function options_menu($key, $label) {
    return '<span class="mpv-options-menu mpv-options-menu-'.$key.'">'.$label.'</span>';  
  }
  
  public static function file_list($dir) {
    
    $list = array();
    
    if (is_dir($dir)) {

      $iterator = new DirectoryIterator($dir);

      foreach ($iterator as $fileinfo) {
        if ($fileinfo->isDir() && !$fileinfo->isDot()) {
          $list[] = $fileinfo->getFilename();
        }
      }

    }
    
    return $list;
    
  }
  
  public static function url($file) {
    return MASTERPRESS_URL.$file;
  }

  public static function type_class($type) {
    return "MPFT_".self::title_case($type, true);
  }
    
  public static function incl_type($type) {
    if (!$result = self::incl_extension_type($type)) {
      $result = self::incl_core_type($type);
    }
    
    return $result;
  }

  public static function incl_core_type($type, $base = "core/field-types/") {
    $path = self::core_type_path($type);
    $exists = file_exists($path);

    if ($exists) {
      include_once($path);
    }
    
    return $exists;
  }
  
  
  public static function incl_extension_type($type) {
    $path = self::extension_type_path($type);
  
    $exists = file_exists($path);

    if ($exists) {
      include_once($path);
    }
    
    return $exists;
  }

  public static function db_decode($val) {
    // loads a value from the database, json decoding it if the string "json:" prepends it.

    if (substr($val, 0, 5) == "json:") {
      return json_decode(preg_replace("/^json:/", "", $val), true);
    } 
    
    return $val;
    
  }
  
  public static function db_encode($val) {
    if (is_array($val) || is_object($val)) {
      return "json:".json_encode($val);
    }

    return $val;
  }
  

  public static function core_type_dir($type = "", $base = "core/field-types/") {
    return MPU::path($base.( $type ? $type."/" : "" ));
  }

  public static function extension_type_dir($type = "", $base = MASTERPRESS_EXTENSIONS_FIELD_TYPES_DIR) {
    return $base.( $type ? $type."/" : "" );
  }

  public static function core_type_path($type) {
    return self::core_type_dir($type)."mpft-".$type.".php";
  }

  public static function extension_type_path($type) {
    return self::extension_type_dir($type)."mpft-".$type.".php";
  }

  public static function type_exists($type) {
    return MPU::core_type_exists($type) || MPU::extension_type_exists($type);
  }

  public static function core_type_exists($type) {
    return file_exists(self::core_type_path($type));
  }

  public static function extension_type_exists($type) {
    return file_exists(self::extension_type_path($type));
  }

  public static function incl($file, $base = "core/") {
    include_once(MPU::path($base.$file));
  }

  public static function incl_controller($file, $base = "core/controller/mpc-") {
    include_once(MPU::path($base.$file).".php");
  }

  public static function incl_lib($file, $base = "lib/") {
    include_once(MPU::path($base.$file));
  }

  public static function incl_model($file, $base = "core/model/mpm-") {
    include_once(MPU::path($base.$file).".php");
  }
  
  public static function img_url($file, $base = "images/") {
    return MPU::url($base.$file);
  }

  public static function img_path($file, $base = "images/") {
    return MPU::path($base.$file);
  }

  public static function plugin_image($file) {
    $url = MPU::img_url($file);
    $path = MPU::img_path($file);
    
    return new WOOF_Image($path, $url);
  }

  public static function content_url() {
    return MASTERPRESS_CONTENT_URL;
  }

  public static function p($mixed) {
    return self::print_r($mixed);
  }
  
  public static function print_r($mixed) {
    echo "<code>";
    echo "<pre>";
    print_r($mixed);
    echo "</pre>";
    echo "</code>";
  }

  public static function __items($count, $singular, $plural, $none = null) {
    
    if ($count == 0 && !is_null($none)) {
      return sprintf( $none, $count );
    }
    
    if ($count == 1) {
      return sprintf( $singular, $count );
    } 
    
    return sprintf( $plural, $count );
    
  }

  public static function imagecreatefromauto($file) {
    $pi = pathinfo($file);
    $ext = strtolower($pi["extension"]);
    
    if ($ext == "png") {
      return imagecreatefrompng($file);
    } else if ($ext == "jpg" || $ext == "jpeg") {
      return imagecreatefromjpeg($file);
    } else if ($ext == "gif") {
      return imagecreatefromgif($file);
    }
    
    return false;
  }

  public static function imageext($image, $file, $ext) {
    
    $ext = strtolower($ext);
    
    if ($ext == "png") {
      imagesavealpha($image, true);
      return imagepng($image, $file);
    } else if ($ext == "jpg" || $ext == "jpeg") {
      return imagejpeg($image, $file);
    } else if ($ext == "gif") {
      return imagegif($image, $file);
    }
    
    return false;

  }

  public static function sprite_filename($file) {
    if (isset($file) && trim($file) != "") {
      $pi = pathinfo($file);
      $ext = $pi["extension"];
      return $pi["filename"].'.sprite.'.$ext;
    }
  }
  
  public static function file_exists($file, $path) {
    if ($file == "") {
      return false;
    } 
    
    return file_exists($path);
  }
  
  public static function create_icon_sprite($color_icon, $color_icon_2x = "", $overwrite = false, $tints = array("#0F191F", "#0F1900" ) ) {

    if (!is_admin()) {
      return new WOOF_Silent( __("Cannot create icon sprites outside of admin", MASTERPRESS_DOMAIN) );
    }
    
    if (is_object($color_icon) && get_class($color_icon) == "WOOF_Image") {
      $name = $color_icon->basename();
      $color_icon_1x_path = $color_icon->path;
    } else {
      $name = $color_icon;
      $color_icon_1x_path = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$color_icon;
    }

    if (is_object($color_icon_2x) && get_class($color_icon_2x) == "WOOF_Image") {
      $color_icon_2x_path = $color_icon_2x->path;
    } else {
      $color_icon_2x_path = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$color_icon_2x;
    }

    $sprite_icon = self::sprite_filename($name);
    

    $sprite_icon_path = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$sprite_icon;
    $sprite_icon_url = MASTERPRESS_CONTENT_MENU_ICONS_URL.$sprite_icon;
    
    $exists = self::file_exists($color_icon, $color_icon_1x_path);
    $exists_2x = self::file_exists($color_icon_2x, $color_icon_2x_path);
    
    if ( ( $exists || $exists_2x) && ( $overwrite || (!file_exists($sprite_icon_path) ) ) )  {
      
      // determine the mode - if we have both icons, we'll use both, otherwise we'll the one we have up or down
      
      if ( $exists && $exists_2x ) {

        $mode = "both";
        $src_icon_1x = $color_icon_1x_path;
        $src_icon_2x = $color_icon_2x_path;
      } else if ( $exists_2x ) {

        $mode = "2x";
        $src_icon_1x = $color_icon_2x_path;
        $src_icon_2x = $color_icon_2x_path;
      } else {
        $mode = "1x";
        $src_icon_1x = $color_icon_1x_path;
        $src_icon_2x = $color_icon_1x_path;
      }
      
      $size_1x = getimagesize($src_icon_1x);
      $size_2x = getimagesize($src_icon_2x);
      
      
      // create color handles
      
      $color_1x = self::imagecreatefromauto($src_icon_1x);
      $color_2x = self::imagecreatefromauto($src_icon_2x);
      
      // create grayscale images
      
      $grayscale_1x = self::imagecreatefromauto($src_icon_1x);
      $grayscale_2x = self::imagecreatefromauto($src_icon_2x);
      
      imagefilter($grayscale_1x, IMG_FILTER_GRAYSCALE);
      imagefilter($grayscale_2x, IMG_FILTER_GRAYSCALE);

      // create image tints
      
      $tinted_1x = array();
      $tinted_2x = array();
      
      foreach ($tints as $tint) {

        $tint_1x = self::imagecreatefromauto($src_icon_1x);
        $tint_2x = self::imagecreatefromauto($src_icon_2x);
      
        $ci = WOOF_Image::parse_color($tint);
        
        $r = $ci["rgb"][0];
        $g = $ci["rgb"][1];
        $b = $ci["rgb"][2];
        
        imagefilter($tint_1x, IMG_FILTER_GRAYSCALE);
        imagefilter($tint_1x, IMG_FILTER_COLORIZE, $r, $g, $b);
        imagefilter($tint_2x, IMG_FILTER_GRAYSCALE);
        imagefilter($tint_2x, IMG_FILTER_COLORIZE, $r, $g, $b);
        
        $tinted_1x[$tint] = $tint_1x;
        $tinted_2x[$tint] = $tint_2x;
        
      }
      
      // now compose the sprite
      
      
      // calculate dimensions

      
      $spacing = 300;

      $width = 32 + $spacing + 32 + ( count($tints) * (32 + $spacing) );
      $height = 16 + 64 + 32;
      
      
      // create the sprite handle
      
      $sprite = imagecreatetruecolor( $width, $height );
      $transparent = imagecolorallocatealpha( $sprite, 0, 0, 0, 127 ); 
      imagefill( $sprite, 0, 0, $transparent ); 

      $x = 0;

      // place the grayscale 1x and 2x

      imagecopyresampled($sprite, $grayscale_1x, $x * $spacing, 0,  0, 0, 16, 16, $size_1x[0], $size_1x[1]); 
      imagecopyresampled($sprite, $grayscale_2x, $x * $spacing, 64, 0, 0, 32, 32, $size_2x[0], $size_2x[1]); 
       
      // place the color 1x and 2x

      $x++;
       
      imagecopyresampled($sprite, $color_1x, $x * $spacing, 0,  0, 0, 16, 16, $size_1x[0], $size_1x[1]); 
      imagecopyresampled($sprite, $color_2x, $x * $spacing, 64, 0, 0, 32, 32, $size_2x[0], $size_2x[1]); 
      
      // place the tints
      
      foreach ($tints as $tint) {
        $x++;
        imagecopyresampled($sprite, $tinted_1x[$tint], $x * $spacing, 0,  0, 0, 16, 16, $size_1x[0], $size_1x[1]); 
        imagecopyresampled($sprite, $tinted_2x[$tint], $x * $spacing, 64, 0, 0, 32, 32, $size_2x[0], $size_2x[1]); 
      }
      
      // finally save the image
      
      imagesavealpha($sprite, true);
      imagepng($sprite, $sprite_icon_path);

    }

    return new WOOF_Image( $sprite_icon_path, $sprite_icon_url ); 
    

  }
  


  public static function sprite_menu_icon_url($file, $fallback = true) {
    $sprite_file = MPU::sprite_filename($file);
    $sprite_url = self::menu_icon_url($sprite_file, false);

    if ($sprite_url && $sprite_url != "") {
      return $sprite_url;
    }
    
    if ($fallback) {
      // if the sprite version doesn't exist, return the regular version
      return self::menu_icon_url($file, $fallback);
    }
  
  }

  public static function mq1x_start() {
    echo "\n@media only screen and (-webkit-max-device-pixel-ratio: 1.0), only screen and (max--moz-device-pixel-ratio: 1.0), only screen and (max-device-pixel-ratio: 1.0) { ";
  }

  public static function mq1x_end() {
    echo "} ";
  }

  public static function mq2x_start() {
    echo "\n@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min--moz-device-pixel-ratio: 1.5), only screen and (min-device-pixel-ratio: 1.5) { ";
  }

  public static function mq2x_end() {
    echo "} ";
  }
  
  public static function menu_icon_url($file, $fallback = true, $object_type = "post_type", $object_icon = true) {
    
    $ret = false;
    
    if ($fallback) {
      if ($object_type == "post_type") {
        $ret = MPU::img_url("menu-icon-posts.png");
      } else if ($object_type == "taxonomy") {
        $ret = MPU::img_url("icon-tag.png");
      }
      
    }
    
    if ($file != "") {
      $file_path = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$file;
      
      if (file_exists($file_path)) {
        $ret = MASTERPRESS_CONTENT_MENU_ICONS_URL.$file;
      } else {
        // check in the plug-in images directory
        $file_path = MASTERPRESS_IMAGES_DIR.$file;
        if (file_exists($file_path)) {
          $ret = MASTERPRESS_IMAGES_URL.$file;
        }
        
      }
      
    }
    
    return $ret;
  }
  
  public static function field_set_icon_url($file) {
    return self::menu_icon_url($file, false, "field_set");
  }

  public static function field_set_icon_2x_url($file) {
    $icon = self::menu_icon($file, false);
    
    if ($icon && $icon->exists()) {
      return $icon->resize("w=32&h=32")->url();
    }
    
    return "";
  }

  public static function field_icon_url($file) {
    return self::menu_icon_url($file, false, "field");
  }
  
  
  public static function field_icon_2x_url($file) {
    $icon = self::menu_icon($file, false);
    
    if ($icon && $icon->exists()) {
      return $icon->resize("w=32&h=32")->url();
    }
    
    return "";
  }
  
  
  public static function menu_icon_exists($file) {
    
    if ($file != "") {
      $file_path = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$file;
      
      if (file_exists($file_path)) {
        return true;
      } else {
        // check in the plug-in images directory
        $file_path = MASTERPRESS_IMAGES_DIR.$file;
        if (file_exists($file_path)) {
          return true;
        }
        
      }
      
    }
    
    return false;
  }
  
  
  public static function menu_icon($file, $fallback = true) {
    
    global $wf;

    if (!is_admin()) {
      return new WOOF_Image( MPU::img_path("menu-icon-generic.png"), MPU::img_url("menu-icon-generic.png"));
    }
    
    $ret = new WOOF_Silent(__("The icon is not specified, or does not exist", MASTERPRESS_DOMAIN) );
    
    if ($fallback) {
      $ret = new WOOF_Image( MPU::img_path("menu-icon-generic.png"), MPU::img_url("menu-icon-generic.png"));
    }
    
    if ($file != "") {
      $file_path = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$file;
      
      if (file_exists($file_path)) {
        $ret = new WOOF_Image($file_path, MASTERPRESS_CONTENT_MENU_ICONS_URL.$file);
      } else {
        // check in the plug-in images directory
        $file_path = MASTERPRESS_IMAGES_DIR.$file;
        if (file_exists($file_path)) {
          $ret = new WOOF_Image($file_path, MASTERPRESS_IMAGES_URL.$file);
        }
        
      }
      
    }
    
    return $ret;
  }
  
  
  public static function type_icon_url($type, $color = true) {
    if (MPU::extension_type_exists($type)) {
      return MPU::extension_type_icon_url($type, $color);
    } else {
      if (MPU::core_type_exists($type)) {
        return MPU::core_type_icon_url($type, $color);
      }
    }
  }
  
  public static function type_file_url($type, $file) {
    if (MPU::extension_type_exists($type)) {
      return MPU::extension_type_file_url($type, $file);
    } else {
      if (MPU::core_type_exists($type)) {
        return MPU::core_type_file_url($type, $file);
      }
    }
  }

  public static function type_file_path($type, $file) {
    if (MPU::core_type_exists($type)) {
      $base = MPU::core_type_dir($type);
      return $base.$file;
    } else {
      if (MPU::extension_type_exists($type)) {
        $base = MPU::extension_type_dir($type);
        return $base.$file;
      }
    }
  }
  
  public static function type_image($type, $file, $base = "images/") {
    
    $path = self::type_file_path($type, $base.$file);
    $url = self::type_file_url($type, $base.$file);
    
    return new WOOF_Image($path, $url);
  }

  public static function type_file($type, $file) {
    
    $path = self::type_file_path($type, $file);
    $url = self::type_file_url($type, $file);
    
    return new WOOF_File($path, $url);
  }
  
  public static function core_type_icon_url($type, $color = true, $base = "core/field-types/") {
    return MPU::url($base.$type."/icon-".( $color ? "color" : "gray" ).".png");
  }

  public static function core_type_file_url($type, $name, $base = "core/field-types/") {
    return MPU::url($base.$type."/".$name);
  }

  public static function extension_type_icon_url($type, $color = true, $base = MASTERPRESS_EXTENSIONS_FIELD_TYPES_URL) {
    return $base.$type."/icon-".( $color ? "color" : "gray" ).".png";
  }

  public static function extension_type_file_url($type, $name, $base = MASTERPRESS_EXTENSIONS_FIELD_TYPES_URL) {
    return $base.$type."/".$name;
  }

  public static function js_url($file, $base = "js/") {
    return MPU::url($base.$file);
  }

  public static function site_table($name, $prefix = "mp_") {
    return self::table($name, $prefix, false);
  }
  
  public static function table($name, $prefix = "mp_", $global = true) {
    global $wpdb;
    
    $wp_prefix = $wpdb->prefix;
    
    if ($global && MASTERPRESS_MULTISITE_SHARING) {
      $wp_prefix = $wpdb->base_prefix;
    }
    
    return $wp_prefix.$prefix.str_replace("-", "_", $name);
  }

  public static function dasherize($str, $delim = "-") {
    return strtolower(preg_replace('/(\B[A-Z])(?=[a-z])|(?<=[a-z])([A-Z])/sm', "$delim$1$2", $str));  
  }
  
  public static function title_case($str, $strip_spaces = false) {
    $ret = ucwords(preg_replace('/[\-\_]/sm', " ", $str));  
    
    if ($strip_spaces) {
      $ret = str_replace(" ", "", $ret);
    }
    
    return $ret;
  }

  public static function is_reserved($word) {
    return in_array(
      $word,
      array("attachment","attachment_id","author","author_name","calendar","cat","category__and","category__in","category__not_in","category_name","comments_per_page","comments_popup","cpage","day","debug","error","exact","feed","hour","link","minute","monthnum","more","name","nav_menu","nopaging","offset","order","orderby","p","page","page_id","paged","pagename","pb","perm","post","post_format","post_mime_type","post_status","post_type","post_type","posts","posts_per_archive_page","posts_per_page","preview","robots","s","search","second","sentence","showposts","static","subpost","subpost_id","tag","tag__and","tag__in","tag__not_in","tag_id","tag_slug__and","tag_slug__in","taxonomy","tb","term","type","w","withcomments","withoutcomments","year")
    );
  }

  
  


  function type_styles_file_url() {
  
    $file = MASTERPRESS_CONTENT_MPFT_CACHE_DIR."mpft-all.css";
    
    if (file_exists($file)) {
      return MASTERPRESS_CONTENT_MPFT_CACHE_URL."mpft-all.css?".filemtime($file);
    }
    
    return false;
    
  }
  

  function type_scripts_file_url() {
  
    $file = MASTERPRESS_CONTENT_MPFT_CACHE_DIR."mpft-all.js";
    
    if (file_exists($file)) {
      return MASTERPRESS_CONTENT_MPFT_CACHE_URL."mpft-all.js?".filemtime($file);
    }
    
    return false;
    
  }
  
  
  
  
    /**
   * Copy a file, or recursively copy a folder and its contents
   *
   * @author      Aidan Lister <aidan@php.net>
   * @version     1.0.1
   * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
   * @param       string   $source    Source path
   * @param       string   $dest      Destination path
   * @return      bool     Returns TRUE on success, FALSE on failure
   */
  static function copyr($source, $dest)
  {
      // Check for symlinks
      if (is_link($source)) {
          return symlink(readlink($source), $dest);
      }
 
      // Simple copy for a file
      if (is_file($source)) {
          return copy($source, $dest);
      }
 
      // Make destination directory
      if (!is_dir($dest)) {
          mkdir($dest);
      }
 
      // Loop through the folder
      $dir = dir($source);
      while (false !== $entry = $dir->read()) {
          // Skip pointers
          if ($entry == '.' || $entry == '..') {
              continue;
          }
 
          // Deep copy directories
          self::copyr($source.WOOF_DIR_SEP.$entry, $dest.WOOF_DIR_SEP.$entry);
      }
 
      // Clean up
      $dir->close();
      return true;
  }



  public static function rmdir_r($directory, $empty=FALSE) {
    // with thanks to this post http://lixlpixel.org/recursive_function/php/recursive_directory_delete/
    
    // if the path has a slash at the end we remove it here
    if(substr($directory,-1) == '/')
    {
        $directory = substr($directory,0,-1);
    }

    // if the path is not valid or is not a directory ...
    if(!file_exists($directory) || !is_dir($directory))
    {
        // ... we return false and exit the function
        return FALSE;

    // ... if the path is not readable
    }elseif(!is_readable($directory))
    {
        // ... we return false and exit the function
        return FALSE;

    // ... else if the path is readable
    }else{

        // we open the directory
        $handle = opendir($directory);

        // and scan through the items inside
        while (FALSE !== ($item = readdir($handle)))
        {
            // if the filepointer is not the current directory
            // or the parent directory
            if($item != '.' && $item != '..')
            {
                // we build the new path to delete
                $path = $directory.'/'.$item;

                // if the new path is a directory
                if(is_dir($path)) 
                {
                    // we call this function with the new path
                    self::rmdir_r($path);

                // if the new path is a file
                }else{
                    // we remove the file
                    unlink($path);
                }
            }
        }
        // close the directory
        closedir($handle);

        // if the option to empty is not set to true
        if($empty == FALSE)
        {
            // try to delete the now empty directory
            if(!rmdir($directory))
            {
                // return false if not possible
                return FALSE;
            }
        }
        // return success
        return TRUE;
    }
  }

  
  // This is required since our dear friend Internet Explorer has a 31 Stylesheet limit. Fab!!! :(
  
  static function combine_type_styles() {
    
    $files = array();
    
    $type_keys = MPFT::type_keys();

    // files to merge
    
    foreach (MPFT::type_keys() as $type) {
      $base = WOOF::root_relative_url( MPU::type_file_url($type, "") );
      $file = MPU::type_file_path($type, "mpft-$type.css");

      if (file_exists($file)) {
        $files[] = array("base" => $base, "file" => $file);
      }
    }
    
    $image_base = WOOF::root_relative_url( MASTERPRESS_IMAGES_URL );
    // we'll be able to kick this off with an AJAX action in the MasterPress overview
    // the dev guide for field types must point the "REBUILD" process out to developers!
    // we can also run this on install and upgrade, so that if field types change, the files would be rebuilt 
    
    // get code from archive folder if it exists, otherwise grab latest files, merge and save in archive folder
    
    if (file_exists(MASTERPRESS_CONTENT_MPFT_CACHE_DIR) && is_writable ( MASTERPRESS_CONTENT_MPFT_CACHE_DIR ) ) {

      // get and merge code

      $content = '';

      foreach ($files as $file) {
         $file_content = file_get_contents($file["file"]);
     
         // replace the paths 

         // replace any relative references to the image directory with the base url for this file
         $file_content = preg_replace("/url\(images\//", "url(".$file["base"]."images/", $file_content);
         
         $content .= $file_content;
      }

      MPU::incl_lib("minify-css-compressor.php");
      $content = Minify_CSS_Compressor::process($content);


      $handle = fopen(MASTERPRESS_CONTENT_MPFT_CACHE_DIR."mpft-all.css", 'w');

      if ($handle) {
        if (flock($handle, LOCK_EX)) {
          fwrite($handle, $content);
          flock($handle, LOCK_UN);
        }
    
        fclose($handle);
      }
    
    }

  }
  
  
  
  static function combine_type_scripts() {
    
    $files = array();
    
    $type_keys = MPFT::type_keys();

    // files to merge
    
    foreach (MPFT::type_keys() as $type) {
      $base = WOOF::root_relative_url( MPU::type_file_url($type, "") );
      $file = MPU::type_file_path($type, "mpft-$type.js");
      
      if (file_exists($file)) {
        $files[] = array("base" => $base, "file" => $file);
      }
    }
    
    $image_base = WOOF::root_relative_url( MASTERPRESS_IMAGES_URL );
    // we'll be able to kick this off with an AJAX action in the MasterPress overview
    // the dev guide for field types must point the "REBUILD" process out to developers!
    // we can also run this on install and upgrade, so that if field types change, the files would be rebuilt 
    
    // get code from archive folder if it exists, otherwise grab latest files, merge and save in archive folder
    
    if (file_exists(MASTERPRESS_CONTENT_MPFT_CACHE_DIR) && is_writable ( MASTERPRESS_CONTENT_MPFT_CACHE_DIR ) ) {

      // get and merge code

      $content = '';

      foreach ($files as $file) {
         $content .= file_get_contents($file["file"]);
      }

      MPU::incl_lib("jsminplus.php");
      $content = JSMinPlus::minify($content);
      
      $handle = fopen(MASTERPRESS_CONTENT_MPFT_CACHE_DIR."mpft-all.js", 'w');

      if ($handle) {
        if (flock($handle, LOCK_EX)) {
          fwrite($handle, $content);
          flock($handle, LOCK_UN);
        }
    
        fclose($handle);
      }
      
    }

  }
  
  
} // MPU


if (!function_exists("pr")) {
  function pr($var) {
    MPU::print_r($var);
  }
}



?>
