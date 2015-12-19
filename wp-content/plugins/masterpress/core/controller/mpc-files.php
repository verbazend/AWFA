<?php

class MPC_Files extends MPC {

  public static function upload_menu_icon() {

    if (is_user_logged_in()) {
      $uploader = new qqFileUploader(
        array(
          "dir" => MASTERPRESS_CONTENT_MENU_ICONS_DIR,
          "sub_dir" => MASTERPRESS_CONTENT_MENU_ICONS_FOLDER."/",
          "allowed_extensions" => array("jpg", "jpeg", "gif", "png"),
          "filename_case" => "lowercase",
          "filename_sanitize" => "dashes"
        )
      );
      
      $result = $uploader->handleUpload();

      // to pass data through iframe you will need to encode all html tags
      echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    } else {
      self::ajax_error(__("you do not have permission to upload", MASTERPRESS_DOMAIN));
    }
    
  }

  public static function import_masterplan() {

    if (is_user_logged_in()) {
      $uploader = new qqFileUploader(
        array(
          "dir" => MASTERPRESS_TMP_DIR,
          "sub_dir" => "",
          "allowed_extensions" => array("zip"),
          "filename_case" => "lowercase",
          "filename_sanitize" => "dashes",
          "overwrite" => true
        )
      );
      
      $result = $uploader->handleUpload();

      // to pass data through iframe you will need to encode all html tags
      echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
      
    } else {
      self::ajax_error(__("you do not have permission to upload", MASTERPRESS_DOMAIN));
    }
    
  }
  
  public static function delete_file() {
    
    global $wf;
    
    if (is_user_logged_in()) {
      
      $url = $_GET["url"];

      $base = $wf->wp_content_url();
    
      $is_wp_content = stripos( $url, $base ) !== FALSE;

      if ($is_wp_content) {
        $path = str_replace($base, "", $url);
        $file = $wf->content_file($path);

        // TODO - update this to check for other images linking to this file

        if (unlink($file->path())) {
          return self::ajax_success();
        } else {
          return self::ajax_error("Could not delete file");
        }
        

      }
      
    }
    
    
  }
  
  public static function upload_dir($field) {
    
    global $wf;
    
    $dir = MASTERPRESS_CONTENT_DIR;

    $object_id = $_GET["object_id"];
    $object_type = $_GET["object_type"];
    $object_type_name = $_GET["object_type_name"];
    
    $sub = MASTERPRESS_CONTENT_UPLOADS_FOLDER.WOOF_DIR_SEP;
    
    if ($object_type == "post") {

      $object = $wf->post($object_id);

      if ($object) {
        $sub .= WOOF_Inflector::pluralize($object->type->name).WOOF_DIR_SEP;
      }
      
    } else if ($object_type == "term") {
      
      $object = $wf->term_by_id($object_id);
      
      if ($object) {
        
        if ($object_type_name != "") { 
          $sub .= WOOF_Inflector::pluralize($object_type_name).WOOF_DIR_SEP;
        } else if ($taxonomy = $object->taxonomy()) {
          $sub .= WOOF_Inflector::pluralize($taxonomy->name).WOOF_DIR_SEP;
        }
      }
      
    } else if ($object_type == "user") {

      $object = $wf->user($object_id);
      
      if ($object) {
        if ($role = $object->role()) {
          $sub .= strtolower(WOOF_Inflector::pluralize($role->id())).WOOF_DIR_SEP;
        }
      }

    } else if ($object_type == "site") {

      $sub .= "site".WOOF_DIR_SEP;
    }
    
    
    if ($field) {
      $sub .= WOOF_Inflector::pluralize($field->type).WOOF_DIR_SEP;
    }

    $dir .= $sub;

    if (!file_exists($dir)) {
      if (!wp_mkdir_p($dir)) {
        $dir = MASTERPRESS_CONTENT_UPLOADS_DIR; // fallback to top level
      }
    }
            
    return array($dir, $sub);
    
  }
  
  public static function upload_field() {
    global $wf;
    
    $user = $wf->the_user();
    
    if (is_user_logged_in()) {
      // work out the directory - for now, we'll store files in a directory that's named after the post type, but we'll make this more
      // flexible later on
      
      // check if this user has permission to uplaod
      
      if (!$user->can("upload_files")) {
        self::ajax_error(__("You do not have permission to upload files", MASTERPRESS_DOMAIN));
      }
      
      MPM::incl("field");

      $model_id = $_GET["model_id"];
      $field = MPM_Field::find_by_id($model_id);
      
      list($dir, $sub) = self::upload_dir($field);
    
      $type_options = $field->type_options;
      
      $options = array(
        "sub_dir" => $sub,
        "dir" => $dir,
        "allowed_extensions" => $type_options["allowed_types"],
        "filename_case" => $type_options["filename_case"],
        "filename_sanitize" => $type_options["filename_sanitize"]
      );
      
      if ($type_options["allowed_maxsize"] != "") {
        $options["size_limit"] = $type_options["allowed_maxsize"]."M";
      }
      
      $uploader = new qqFileUploader($options);
      
      $result = $uploader->handleUpload();

      // to pass data through iframe you will need to encode all html tags
      echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    } else {
      self::ajax_error(__("You do not have permission to upload", MASTERPRESS_DOMAIN));
    }
    
  }
  
  public static function sanitize_filename($filename, $options) {
  
    $options = wp_parse_args( $options, array(
        "filename_sanitize" => "dashes",
        "filename_case" => "lowercase"
      )
    );
    
    if ($options["filename_sanitize"] != "none") {
      $filename = sanitize_title_with_dashes($filename);
    }

    if ($options["filename_sanitize"] == "underscores") {
      $filename = str_replace("-", "_", $filename);
    }
    
    if ($options["filename_case"] == "titlecase") {
      $filename = WOOF_Inflector::titleize($filename);
      
      if ($options["filename_sanitize"] == "underscores") {
        $filename = str_replace(" ", "_", $filename);
      } else if ($options["filename_sanitize"] == "dashes") {
        $filename = str_replace(" ", "-", $filename);
      }
      
    } else if ($options["filename_case"] == "uppercase") {
      $filename = strtoupper($filename);
      $v = ".V";
    } else if ($options["filename_case"] == "lowercase") {
      $filename = strtolower($filename);
    }

    if ($options["filename_sanitize"] == "underscores") {
      $filename = str_replace("-", "_", $filename);
    } else if ($options["filename_sanitize"] == "dashes") {
      $filename = str_replace("_", "-", $filename);
    }
     
    return $filename;     
  }
  
  
}



/* 
  qqUpload classes part of Valum's File Uploader
  © 2010 Andrew Valums

 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    protected $options;
    protected $file;

    function __construct($options = array()) {        
      
      $this->options = wp_parse_args($options, array("allowed_extensions" => array(), "size_limit" => 0));
       
      if (is_array($this->options["allowed_extensions"])) {
        $this->options["allowed_extensions"] = array_map("strtolower", $this->options["allowed_extensions"]);
      }
       
      $this->options["size_limit"] = $this->toBytes($this->options["size_limit"]);
        
      $this->adjustLimitFromServerSettings();       

      if (isset($_GET['qqfile'])) {
        $this->file = new qqUploadedFileXhr();
      } elseif (isset($_FILES['qqfile'])) {
        $this->file = new qqUploadedFileForm();
      } else {
        $this->file = false; 
      }
    }
    
    private function adjustLimitFromServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        $this->options["size_limit"] = min($postSize, $uploadSize, $this->options["size_limit"]);
        
        if ($this->options["size_limit"] == 0) {
          $this->options["size_limit"] = min($postSize, $uploadSize);
        }
        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload() { 
      $dir = $this->options["dir"];
      
        if (!is_writable($dir)){
          return array('error' => "Sorry, the file could not be uploaded as the upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => __('No files were uploaded.', MASTERPRESS_DOMAIN));
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->options["size_limit"]) {
            return array('error' => sprintf( __('Sorry, the file is too large. The maximum size allowed is %s bytes', MASTERPRESS_DOMAIN), $this->options["size_limit"]));
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->options["allowed_extensions"] && !in_array(strtolower($ext), $this->options["allowed_extensions"])){
            $these = implode(', ', $this->options["allowed_extensions"]);
            return array('error' => sprintf( __('Sorry, you cannot upload a file of this type. Files must have an extension in the following list: %s.', MASTERPRESS_DOMAIN), $these) );
        }
            
        // traversal mod to allow serial numbering for non-replace
        $count = 0;
        
        // mod the file name based on options
        
        $v = ".v";
        
        $filename = MPC_Files::sanitize_filename($filename, $this->options);
        
        if (!isset($this->options["overwrite"])) {
          $basefilename = $filename;
        
          while (file_exists($dir . $filename . '.' . $ext)) {
            $count++;
            $filename = $basefilename.$v.$count;
          }
        }
      
        if ($this->file->save($dir . $filename . '.' . $ext)) {
          // traversal mod to return the eventual filename (why wasn't this included???)
            return array("dir" => $this->options["sub_dir"], 'success' => true, 'filename' => $filename . '.' . $ext);
        } else {
            return array('error'=> __('Could not save uploaded file.', MASTERPRESS_DOMAIN) .
                __('The upload was cancelled, or a server error has occurred.', MASTERPRESS_DOMAIN));
        }
        
    }    
}
