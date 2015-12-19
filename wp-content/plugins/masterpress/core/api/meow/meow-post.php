<?php

class MEOW_Post extends WOOF_Post { 

  protected $incoming;
  
	public function set($name) {
    global $wf;
    return $wf->set($name, $this);
  }
  
  public function field($name, $set_index = 1) {
    global $wf;
    return $wf->field($name, $set_index, $this);
  }
  
  public function s($name) {
    return $this->set($name);
  }
  
  public function f($name, $set_index = 1) {
    return $this->field($name, $set_index);
  }
  
  public function blank() {
    return $this->id == -1;
  }
  
  
  public function posts($r = array()) {
    
    global $wf;
    
    $r = wp_parse_args($r, array());
    
    $r["meta_query"] = array(
      array(
        "key" => "_mp_master",
        "value" => $this->id()
      )
    );

    return $wf->posts($r);

  }
  

  public function incoming($args = array()) {
    global $meow_provider, $wf;
      
    $post_ids = $meow_provider->incoming($this, WOOF::array_arg($args, "for"));
    
    $this->incoming = array();

    if (count($post_ids)) {
      // if there are no matching post ids, don't query, return an empty array
    
      $defaults = array(
        "post_type" => "any",
        "nopaging" => true,
        "posts_per_page" => -1
      );
  
      $r = wp_parse_args( $args, $defaults );
      $r = array_merge( $r , array("post__in" => $post_ids));
    
      return $wf->posts($r);
  
    } else {
      return new WOOF_Collection( array() );
    }
    
    
  }
  
  public function incoming_terms($taxonomy, $args = array()) {
    global $meow_provider, $wf;
      
    $term_ids = $meow_provider->incoming($this, WOOF::array_arg($args, "for"), "term");
    
    $this->incoming = array();

    if (count($term_ids)) {
      // if there are no matching term ids, don't query, return an empty array
    
      $defaults = array(
        
      );
  
      $r = wp_parse_args( $args, $defaults );
      $r = array_merge( $r , array("include" => $term_ids));
    
      return $wf->terms( $taxonomy, $r );
  
    } else {
      return new WOOF_Collection( array() );
    }
    
  }


  public function incoming_users($args = array()) {
    
    global $meow_provider, $wf;
      
    $user_ids = $meow_provider->incoming($this, WOOF::array_arg($args, "for"), "user");
    
    $this->incoming = array();

    if (count($user_ids)) {
      // if there are no matching user ids, don't query, return an empty array
    
      $defaults = array(
        
      );
  
      $r = wp_parse_args( $args, $defaults );
      $r = array_merge( $r , array("include" => $user_ids));
    
      return $wf->users( $r );
  
    } else {
      return new WOOF_Collection( array() );
    }
    
  }
  
  
  public function query_incoming($args = array()) {
    $r = wp_parse_args($args);
    $r["query"] = "1";
    return $this->incoming($r);
  }
  
  public function has_field($name) {
    $field = $this->field($name);
    
    if (!$field->exists() || $field->blank()) {
      return false;
    }
    
    return $field;
  }
  
  public function has_set($name) {
    $set = $this->set($name);

    if (!$set->exists() || $set->blank()) {
      return false;
    }
    
    return $set;
  }
  
  public function has($name) {

    if ($set = $this->has_set($name)) {
      return $set;
    } else {
      if ($field = $this->has_field($name)) {
        return $field;
      }
    } 
    
    return false;
  }

  public function __call($name, $arguments = array()) {

    global $wf;

    if (preg_match("/incoming\_([a-z0-9\_]+)/", $name, $matches)) {

      // look for "incoming" post types, with arguments, so that we can call incoming_cars("orderby=title") for example

      $pt_name = $matches[1];
      $singular = WOOF_Inflector::singularize($pt_name);
      
      foreach ($wf->types() as $type) {
        if ($type->name == $pt_name) {
          return $this->incoming("post_type=".$pt_name)->first(); // return the first incoming post (ignore the args)
        } else if ($type->name == $singular) {
          
          $args = array("post_type" => $singular);
           
          if (isset($arguments[0])) {
            $args = wp_parse_args( $arguments[0] );
            $args["post_type"] = $singular;
          }
          
          return $this->incoming($args);
        } 
      }
      
      // next we'll try taxonomy names
      
      foreach ($wf->taxonomies() as $tax) {
        if ($tax->name == $singular) {
          
          if (isset($arguments[0])) {
            $args = wp_parse_args( $arguments[0] );
          }
          
          return $this->incoming_terms($tax, $args);
        }
      }
      
      
    } else {
      
      $singular = WOOF_Inflector::singularize($name);
      
      foreach ($wf->types() as $type) {
        if ($type->name == $name) {
          return $this->posts("post_type=".$singular)->first(); // return the first post (ignore the args)
        } else if ($type->name == $singular) {
          
          $args = array("post_type" => $singular);
           
          if (isset($arguments[0])) {
            $args = wp_parse_args( $arguments[0] );
            $args["post_type"] = $singular;
          }
          
          return $this->posts($args);
        } 
      }
      
    }
    
    return parent::__call($name, $arguments);

  }
  
  public function __get($name) {
  
    global $wf;
    
    // is we're accessing the set named "content" and it exists, prioritise this first
    // this allows us to call "content()" to get the standard content FIELD.
    
    if ($name == "content") {
      $set = $this->set("content");
      
      if ($set->exists()) {
        return $set;
      }
    }
    
    // fallback to WOOF_Wrap's get FIRST.
    
    $value = $this->get($name);
    
    if (is_woof_silent($value)) {
      $value = $this->set($name);
    }

    if (!is_woof_silent($value)) {
      return $value;
    }

    // look for "incoming" post types, so that we can get "incoming_cars" for example
    
    if (preg_match("/incoming\_([a-z0-9\_]+)/", $name, $matches)) {
    
      $pt_name = $matches[1];
      $singular = WOOF_Inflector::singularize($pt_name);
      
      foreach ($wf->types() as $type) {
        if ($type->name == $pt_name) {
          return $this->incoming("post_type=".$pt_name)->first(); // return the first incoming post
        } else if ($type->name == $singular) {
          return $this->incoming("post_type=".$singular);
        }
      }
      
      
      // next we'll try taxonomy names
      
      foreach ($wf->taxonomies() as $tax) {
        if ($tax->name == $singular) {
          return $this->incoming_terms($tax);
        } 
      }
      
      
    } else {

      $singular = WOOF_Inflector::singularize($name);

      foreach ($wf->types() as $type) {
        if ($type->name == $name) {
          return $this->posts("post_type=".$name)->first(); // return the first post (ignore the args)
        } else if ($type->name == $singular) {
          return $this->posts("post_type=" . $singular);
        } 
      }

    }
    
    // fallback to the parent method
    
    return parent::__get($name);
    
  }
  
  public function set_names() {
    global $meow_provider;
    
    $sets = array();
    // returns an array of field set names applicable to this post
    
    $assigned_sets = $meow_provider->post_type_field_sets($this->type_name(), $this->template());
    
    foreach ($assigned_sets as $set) {
      $sets[] = $set->name;
    }
    
    return $sets;
    
  }
  
  public function update($include_sets = true) {
    
    global $wf;
    
    parent::update();
    
    // also checks through the field sets for this post and updates those
    $sets = $this->set_names();
    
    if ($include_sets) {
      foreach ($sets as $name) {
        
        $set = $this->set($name);
      
        if ($set->is_dirty()) {
          $set->update();
        } 
      
      }
    }

    return $this;
  }
  
  public function eval_json_field($name, $fields) {
    
    global $wf;

    $result = $wf->eval_json_field($this, $name, $fields);
    
    if ($result !== false) {
      return $result;
    }

    return parent::eval_json_field($name);
  
  }
  
  public function terms($taxonomy, $fields = array()) {
    
    // allows custom field based related terms to be included in queries
    
    global $wf, $meow_provider;

    $terms = parent::terms($taxonomy);
    
    if ($wf->regard_field_terms()) {
      
      $ids = $meow_provider->outgoing_terms($this, $fields);
      $field_terms = $wf->terms_by_id($ids, $taxonomy);

      if (count($field_terms)) {
        $terms->merge($field_terms, false);
        $terms->dedupe();
      }
      
    }
    
    return $terms;
    
  }
  
  public function json_href() {
    global $wf;
    
    $url = $this->url(true);
    
    if ($this->item->post_type == "post") {
      $url = "post/" . $this->slug();
    }
    
    return rtrim( $this->site->url(), "/" ) . "/" . trim( $wf->rest_api_slug(), "/" ) . "/" . trim( $url, "/" );
  }
  
  public static function json_fields() {
    return "id,author,date,modified,title,url,content,excerpt,slug,status,type,comments_open,pings_open,comment_count,featured_image,format";
  }
  
  public function json($fields) {
    
    global $wf;
    
    $type = $this->type_name();

    $json = array();
    
    if ( apply_filters("mp_rest_post_href_field", true ) ) {
      $json["href"] = $this->json_href();
    }

    if (!is_array($fields)) {
      $fields = $wf->parse_field_list( $fields );
    } 
    
    if (!isset($fields)) {
      $fields = $wf->parse_field_list( self::json_fields() );
    }
    
    MasterPress::$context = "json";
    
    foreach ($fields as $name => $sub_fields) {
      
      $val = null;

      switch ($name) {

        case "id" : 
        
          $val = (int) $this->id();
          break;
        
        case "author" :

          $val = $this->author->json();

          break;
        case "type" :

          $val = $this->type_name;
          break;

        case "date" :

          $val = $this->date("c");
          break;

        case "modified" :

          $val = $this->item->post_modified;
          
          if ($val == 0) {
            $val = null;
          } else {
            $val = $this->modified("c");
          }
          break;

        case "comments_open" :

          $val = $this->comment_status == "open";
          break;

        case "pings_open" :

          $val = $this->ping_status == "open";
          break;

        case "comment_count" : 
        
          $val = (int) $this->comment_count();
          break;
         
        default: {
          $val = $this->eval_json_field($name, $sub_fields); 
        }

      }

      $json[$name] = $val;
    }
    
    MasterPress::$context = "string";
    
    return $json;
    
  }
  
  public function debug_data() {
    return $this->field_debug_data();
  }
  
}
