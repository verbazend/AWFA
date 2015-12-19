<?php


class MPM extends WOOF_Wrap {

  protected $data = array();
  protected $format;
  protected $errors = array();
  protected $field_errors = array();
  protected $metadata = array();
  
  public static function incl($file, $base = "core/model/mpm-") {
    include_once(MPU::path($base.$file).".php");
  }

  public static function array_stripslashes($value) {
      $value = is_array($value) ?
                  array_map(array("MPM", 'array_stripslashes'), $value) :
                  stripslashes($value);

      return $value;
  }

  public static function csv_sql($csv) {
    
    $new_vals = array();
    
    $vals = explode(",", $csv);
    
    foreach ($vals as $val) {
      $new_vals[] = "'".addslashes($val)."'";
    }

    return implode(",", $new_vals);

  }
  
  public static function table($model) {
    return MPU::table( str_replace("-", "_", $model) );
  }
  
  public function as_array($name) {
    $val = $this->__get($name);
    
    if (!is_array($val)) {
      return array();
    } 
    
    return $val;
  }
  
  
  public static function model($model, $row, $table = null) {

    $model_class = MasterPress::model_class($model);
    
    if ($row) {
      $m = new $model_class;
      $m->set_from_row($row);
      return $m;
    }
    
    return false;
  }
  
  public static function models($model, $results, $table = null) {
    
    $model_class = MasterPress::model_class($model);
    
    $models = array();
    
    foreach ($results as $result) {
      $m = new $model_class;
      $m->set_from_row($result);
      
      $models[] = $m;
    }
    
    return $models;
  }
  
  
  public static function get_model($model, $sql, $table = null) {
    global $wpdb;
    
    $result = $wpdb->get_row($sql);
    
    return MPM::model($model, $result, $table);
  }
  
  public static function get_models($model, $sql, $table = null) {
    global $wpdb;
    
    $wpdb->show_errors();
    
    $results = $wpdb->get_results($sql);
    
    if ($results) {
      return MPM::models($model, $results, $table);
    }
    
    return array();
  }
  
  public static function find_by_id($model, $id, $table = null) {
    if (!$table) {
      $table = MPM::table($model);
    }
    
    return MPM::get_model($model, "SELECT * FROM `".$table."` WHERE id = {$id}", $table);
  }

  public static function delete_by_id($model, $id, $table = null) {
    global $wpdb;
    $wpdb->query("DELETE FROM `".$table."` WHERE `id` = ".$id);
  }


  public static function find($model, $args = array(), $table = null) {
    global $wpdb;
    
    $where = "";
    
    if (!$table) {
      $table = MPM::table($model);
    }


    $items = array();
    
    $defaults = array(
      "select" => "*",
      "where" => false,
      "orderby" => "id ASC"
    );
    
    $r = wp_parse_args($args, $defaults);
    
    if ($r["where"]) {
      $where = " WHERE ".$r["where"];
    }
    
    return MPM::get_models($model, sprintf("SELECT %s FROM %s %s ORDER BY %s", $r["select"], $table, $where, $r["orderby"]), $table);
  }
  
  public static function find_by($model, $field, $value, $format = "%s", $table = null) {
    
    $model_class = MasterPress::model_class($model);
    global $wpdb;

    if (!$table) {
      $table = MPM::table($model);
    }
    
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `".$table."` WHERE {$field} = {$format}", $value));
    
    if ($row) {
      
      $m = new $model_class();
      $m->set_from_row($row);
      
      return $m;
    }
    
    return false;

  }
  
  public static function find_by_in($model, $field, $values, $format = "%s", $table = null) {
    
    global $wpdb;

    if (!$table) {
      $table = MPM::table($model);
    }

    if (!is_array($values)) {
      if (!$values || $values == "") {
        return array();
      } else {
        $values = array( $values );
      }
      
    } else if (!count($values)) {
      return array();
    } 
    
    $model_class = MasterPress::model_class($model);
    
    if (!is_array($format)) {
      $format = array_fill(0, count($values), $format);
    }
    
    // setup args for prepare
    
    $prepare_args = array_merge( array("SELECT * FROM `".$table."` WHERE {$field} IN (".implode(",", $format).")"), $values );
    
    $sql = call_user_func_array( array($wpdb, "prepare"), $prepare_args );

    $results = $wpdb->get_results($sql);
    
    $models = array();
    
    foreach ($results as $result) {
      $m = new $model_class();
      $m->set_from_row($result);
      $models[] = $m;
    }
    
    return $models;
  }
  
  public static function find_by_id_in($model, $values, $table = null) {
    return self::find_by_in($model, "id", $values, "%d", $table);
  }

  public static function visibility_rlike($key, $value, $table_prefix = "") {
    return " ( ".$table_prefix."visibility RLIKE '\"".$key."\"\s?:\s?\"[^:]*([[:<:]]".$value."[[:>:]]|\\\\*)(\"|,)' OR ( ".$table_prefix."visibility RLIKE '\"not_".$key."\"' AND ".$table_prefix."visibility NOT RLIKE '\"not_".$key."\"\s?:\s?\"[^:]*[[:<:]]".$value."[[:>:]](\"|,)' ) ) ";
  }
  
  public function vis($key) {
    $vis = $this->visibility;
    
    if (isset($vis[$key])) {
      return $vis[$key];
    }
    
    return "";
    
  }

  public function label($key) {
    $labels = $this->labels;
    
    if (isset($labels, $labels[$key])) {
      return $labels[$key];
    }
    
    return "";
  }
  
  
  public function sites() {
    
    global $wf;
    
    $args = array("public_only" => false);

    $vis = $this->visibility;
    
    if (isset($vis)) {
    
      $info = $this->visibility_clause("sites", "id", "false", " WHERE ", "object");
      
      if ($info["not"]) {
        $args["exclude_id"] = $info["values"];
      } else if (!$info["all"]) {
        $args["include_id"] = $info["values"];
      }
    
    }
  
    return $wf->sites($args);

  }
  
  public function in_current_site() {
    global $blog_id;

    if (!is_multisite()) {
      return true;
    }
    
    $vis = $this->visibility;
    
    if (isset($vis)) {
      $info = $this->visibility_clause("sites", "id", "false", " WHERE ", "object");
      
      if ($info["not"]) {
        return !MPU::in_csv($blog_id, $info["values"]);
      } else if (!$info["all"]) {
        return MPU::in_csv($blog_id, $info["values"]);
      }
      
    }

    return true;
  }
  
  public function visibility_clause($key, $field, $string = false, $keyword = " WHERE ", $return = "string") {
    
    $clause = "";
    $not = false;
    $all = true;
    $values = "";
    $csv = "";
    
    $vis = $this->visibility;
    
    if (isset($vis)) {
      
      if (isset($vis[$key])) {

        $vt = $vis[$key];

        if ($vt == "") {
          $clause = $keyword." 0 = 1 "; // don't want any values
        }
      
        if ($vt != "*") { // if it is a wildcard we don't want a WHERE clause, just return all of them
          
          $values = $vt;
          
          if ($string) {
            $csv = MPM::csv_sql($vt);
          } else {
            $csv = $vt;
          }
          
          $all = false;
          
          $clause = $keyword." `".$field."` IN ( ".$csv." ) ";
        }
      } else {
      
        if (isset($vis["not_".$key])) {
          
          $vtn = $vis["not_".$key];
        
          if ($vtn != "") { // we don't need a where clause if the denial is blank

            $values = $vtn;

            if ($string) {
              $csv = MPM::csv_sql($vtn);
            } else {
              $csv = $vtn;
            }
        
            $all = false;
        
            $not = true;
        
            $clause = $keyword." `".$field."` NOT IN ( ".$csv." ) ";
          }
        }
      }
  
    }
    
    if ($return == "string") {
      return $clause;
    } else {
      return array(
        "clause" => $clause,
        "not" => $not,
        "all" => $all,
        "values" => $values,
        "csv" => $csv
      );
    }
  
  }
  
  
  public function tbl() {
    return MPM::table($this->key());
  } 

  public function delete($table = null) {
    global $wpdb;
    $wpdb->query("DELETE FROM `".$this->tbl()."` WHERE `id` = ".$this->id);
  }
  
  
  public function insert() {
    global $wpdb;
    
    $this->validate("insert");
    
    if ($this->is_valid()) {
      $result = $wpdb->insert( $this->tbl(), $this->prepared_data(), $this->format() ); 

      if ($result) {
        $this->id = $wpdb->insert_id;
        return true;
      }
      
    }
    
    return false;
  }

  public function insert_id() {
    global $wpdb;

    return $wpdb->insert_id;
  }
  
  public function update($id = null) {
    global $wpdb;
    
    if ($id) {
      $this->id = $id;
    } 

    $this->validate("update");

    if ($this->is_valid()) {
    
      $result = $wpdb->update( $this->tbl(), $this->prepared_data(), array( "id" => $this->id ), $this->format(), $this->where_format() );
      
      if ($result) {
        return $this->id;
      }
    }
    
    return false;
  }


  public static function where_format() {
    return '%d';
  }

  public static function k($class) {
    return WOOF_Inflector::underscore( WOOF_Inflector::pluralize( str_replace("MPM_", "", $class ) ) );
  }


  public function key() {
    return self::k(get_class($this));
  }

  public function is_valid() {
    return $this->error_count() == 0;
  }
 
  public function set($data, $merge = false) {
    if ($merge) {
      $this->data = array_merge($this->data, $data);
    } else {
      $this->data = $data;
    }
  
  }

  public function set_from_row($row) {
    $this->set($this->data_from_row($row));
  }
  
  public function err($str, $field = "") {
    if ($field == "") {
      $this->errors[] = $str;
    } else {
      $this->field_errors[$field] = $str;
    }
  }
  
  public function error_count() {
    return count($this->errors) + count($this->field_errors);
  }
  
  public function visible_in($type, $value) {
    $vis = $this->visibility;
    
    if (isset($vis)) {
      
      if (isset($vis[$type])) {
        $vt = $vis[$type];
        
        if ($vt == "*") {
          return true;
        } else {
          return in_array($value, explode(",", $vt));
        } 
        
      } else {
        
        if (isset($vis["not_".$type])) {
          $vtn = $vis["not_".$type];

          return !in_array($value, explode(",", $vtn));
        }
        
        return true;
      }
      
      
    }
    
    return false;
  }
  
  public function rep() {
    return $this->data();
  }

  public function to_json($pretty = false) {
    $json = json_encode($this->rep());
    
    if ($pretty) {
      $json = WOOF::json_indent($json);
    }
    
    return $json;
  }
  
  public function format() {
    
    if (!isset($this->format)) {
      
      $this->format = array();
      
      foreach ($this->data as $key => $val) {
        
        if (is_float($val)) {
          $this->format[] = "%f";
        } else if (is_numeric($val) || is_bool($val)) {
          $this->format[] = "%d";
        } else {
          $this->format[] = "%s";
        }
      
      }

    }
    
    return $this->format;
    
  }
  
  public function validate($op) {
    return true;
  }



  protected function data_from_row($row) {
    $data = array();
    
    foreach ($row as $key => $val) {
      $data[$key] = MPU::db_decode($val);
    }
    
    return $data;
  }
  
  
  
  protected function prepared_data() {
    
    $ed = array();
    
    foreach ($this->data as $key => $val) {
      $ed[$key] = MPU::db_encode($val);
    }
    
    return $ed;
    
  }
  
  public function data() {
    return $this->data;
  }
  
  public function __set($name, $value) {
    $this->data[$name] = $value;
  }
  
  public function __get($name) {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
  }

  public function get($name, $fallback) {
    $ret = $this->data[$name];
    
    if (is_null($ret) || $ret == "") {
      return $fallback;
    }
    
    return $ret;
  }
  
  public function meta($property, $value = null) {
    if ($value) {
      $this->metadata[$property] = $value; 
    } else {
      if (isset($this->metadata[$property])) {
        return $this->metadata[$property];
      }
      
      return "";
    }
  }
  
  public function bubble_errors(&$errors, &$field_errors) {
    $errors = $this->errors;
    $field_errors = $this->field_errors;
  }
  
  public function dump($exit = false) {

    echo "<h3>Errors:</h3>";

    echo "<pre>";
    print_r($this->errors);
    echo "</pre>";

    echo "<h3>Field Errors:</h3>";

    echo "<pre>";
    print_r($this->field_errors);
    echo "</pre>";


    echo "<h3>Data:</h3>";

    echo "<pre>";
    print_r($this->data);
    echo "</pre>";
  
    if ($exit) {
      exit();
    }
  }
  
}


?>