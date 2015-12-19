<?php

class MPFT_FileBase extends MPFT {
  
  protected $file;
  protected $valid;

  function __construct( $info, $field ) {
    
    global $wf;
    
    parent::__construct( $info, $field );
    
    // infer the path, url to the file.

    $data = $this->data();
    
    if (isset($data)) {
    
      $this->valid = false;

      if (isset($data->val)) {
    
        if ($data->val == "") {
          $this->valid = false;
        } else {
          $this->valid = true;
        }

        $url = $data->val;
        $path = WOOF_File::infer_content_path($url);

        $fc = $wf->get_file_class();
        
        $this->file = new $fc( $path, $url );
    
        if ($this->file->exists()) {
          $this->valid = TRUE;
        } else {
          
          // try to access the file at the URL 

          if ($path == "" && defined("MASTERPRESS_RESOLVE_EXTERNAL_URLS")) {

            /* 
            
            We should ONLY access the URL if this file could not possibly exist on this server.
            If we try to hit a file that could exist on this server but doesn't, this can cause an infinite httpd loop
            where a 404 causes another WP page load, which causes additional 404s, which cause more page loads ...
            
            Note that infer_content_path above will return an empty string if the URL is external
            
            */
            
            $this->file = $wf->file_from_url($url);
            $this->valid = $this->file->exists();
          }
        }
        
        if (!$this->valid) {
          $this->file = new WOOF_Silent(__("The file could not be found", MASTERPRESS_DOMAIN));
        }
        
        
      
      } else {
        $this->file = new WOOF_Silent(__("no file path has been set", MASTERPRESS_DOMAIN));
      }

    }
  
    
  } 

  public function valid() {
    $file = $this->file();
    
    if (isset($file)) {
      if (!$file->exists() || !$this->valid) {
        return false;
      }
    } else {
      return false;
    }
  
    return true;
  }


  public function blank() {
    
    // mark as blank if the file doesn't exist
    
    $file = $this->file();
    
    if (!$file->exists()) {
      return true;
    }
    
    return parent::blank();

  }

  public static function normalize_options($options) {
    
    $ret = $options;
    
    foreach ($options as $key => $value) {
    
      if ($key == "allowed_maxsize") {
        $limit = (int) WOOF_File::format_filesize(self::get_filesize_limit(), "MB", false);

        if (!isset($value) || $value == "") {
          $value = $limit;
        } else {
          if (is_numeric($value)) {
            $value = (int) $value;
          } else {
            $value = $limit;
          }
        }
        
        $ret[$key] = min($limit, $value);
      }

    }
    
    return $ret;
  }


  public static function get_filesize_limit() {
    $mfs = WOOF_File::to_bytes(ini_get('upload_max_filesize'));
    $mps = WOOF_File::to_bytes(ini_get('post_max_size'));

    $limit = min($mfs, $mps);

    return $limit;
  }
  
  public static function verify_attachment() {
    global $wf;
    
    if (isset($_REQUEST["id"]) && isset($_REQUEST["model_id"])) {
      
      $id = $_REQUEST["id"];
      $model_id = $_REQUEST["model_id"];
      
      if ($id != "") {

        $field = MPM_Field::find_by_id($model_id);
        
        if ($field) {

          $type_options = $field->type_options;

          $extensions = $type_options["allowed_types"];

          $attachment = $wf->attachment($id);
      	
          if ($attachment->exists()) {
            
            $extension = $attachment->extension();
            
            if (!in_array(strtolower($extension), $extensions)) {
              self::ajax_error( sprintf( __("This Media Library item cannot be used, as this field only allows the file types %s", MASTERPRESS_DOMAIN ), implode(", ", $extensions)));  
            }
			
            self::ajax_success( array("url" => $attachment->file()->permalink() ) );

          }
        
        }
        
      } else {

        self::ajax_error(sprintf("Could not find attachment with id %s", $id));

      }
    
    }
  
    self::ajax_error(sprintf("Cannot find the field you are trying to edit", $id));
    
  }
  
  
  public static function do_download_file($type) {
    
    global $wf;
    
    // used by the "From URL" control 
    
    if (isset($_REQUEST["url"])) {
      
      $url = trim($_REQUEST["url"]);
    
      if ($url) {
        
        MPC::incl("files");

        $model_id = $_REQUEST["model_id"];
        
        // need to check the extensions
        
        $pi = pathinfo(urldecode($url));
        
        $field = MPM_Field::find_by_id($model_id);
        
        if ($field) {

          $type_options = $field->type_options;

          $extensions = $type_options["allowed_types"];
          
          if (!in_array(strtolower($pi["extension"]), $extensions)) {
            self::ajax_error( sprintf( __("Cannot download %s. This field only allows the file types %s", MASTERPRESS_DOMAIN ), $type, implode(", ", $extensions)));  
          }
          
          list($dir, $sub) = MPC_Files::upload_dir($field);
          
          $name = MPC_Files::sanitize_filename($pi["filename"], $type_options).".".md5($url);
          
          if ($type == "image") {
            $file = $wf->image_from_url($url, $name, $dir);
          } else {
            $file = $wf->file_from_url($url, $name, $dir);
          }
        
          if ($file->exists()) {
            // check the file size 
            
            $limit = self::get_filesize_limit();

            if (isset($type_options["allowed_maxsize"])) {
              if (is_numeric($type_options["allowed_maxsize"])) {
                $limit = WOOF_File::to_bytes($type_options["allowed_maxsize"]."M");
              }
            }
          
            if ($file->filesizeinbytes() > $limit) {
              $file->delete();
              self::ajax_error( sprintf( __("The %s was downloaded, but it could not saved as it was too large. This field only allows files up to %s", MASTERPRESS_DOMAIN ), $type, WOOF_File::format_filesize($limit, "MB", TRUE, $sep = " ")));  
            }
            
            $info = array( "url" => $file->permalink() );
            self::ajax_success($info);
          } else {
            self::ajax_error( sprintf( __("The %s could not be downloaded. Please check the URL is valid and try again", MASTERPRESS_DOMAIN ), $type ) );  
          }
      
        } else {
          self::ajax_error( sprintf( __( "This %s field could not be found in the database to check the validity of this download.", MASTERPRESS_DOMAIN ), $type ) );  
        }
        
      }
    
    }
    
    self::ajax_error(__("No URL specified", MASTERPRESS_DOMAIN));  
  }
  

  public static function delete_file() {
    MPC::incl("files");
    MPC_Files::delete_file();
  }

  public function file() {
    return $this->file;
  }
  
  public function value_for_set($value) {
    
    global $wf;
    
    if (WOOF::is_or_extends($value, "WOOF_Image")) {
      
      return $value->permalink();
      
    } else if ($att = $wf->attachment($value)->exists()) {
      
      return $att->file->permalink();

    } 
    
    return $value;

  }
  
  public function get_delegate() {
    return $this->file;
  }

  public function call($name, $arguments) {
    
    if (method_exists($this->file, $name)) {
      return call_user_func_array (array($this->file, $name), $arguments); 
    }
    
    return parent::call($name, $arguments);
  }
     
}