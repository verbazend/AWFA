<?php

class MEOW_REST_API extends WOOF_Wrap {
  
  public $content_type_json = "application/json";
  public $content_type_html = "text/html";

  public $post_types = true;
  public $taxonomies = true;
  public $slug = "api";
  public $exists = true;
  
  public $post_limit = 50;
  public $post_type_limits = array();

  public $term_limit = 50;
  public $taxonomy_limits = array();
  
  protected $base_accepted_args = array("context", "!pretty", "callback", "fields", "expand");
  protected $endpoints = array();
  

  public function __construct( ) {
  
    $this->content_type_json = apply_filters("mp_rest_content_type_json", $this->content_type_json );
    $this->content_type_html = apply_filters("mp_rest_content_type_html", $this->content_type_html );
    $this->slug = apply_filters("mp_rest_entry_slug", "api");
    
    // setup the list of post types we want to allow
    
    $this->set_post_types( apply_filters( "mp_rest_post_types", "*" ) );
    $this->set_taxonomies( apply_filters( "mp_rest_taxonomies", "*" ) );
    
    $this->post_limit = apply_filters( "mp_rest_post_limit", 50 );
    $this->term_limit = apply_filters( "mp_rest_term_limit", 50 );
    
    
  }
  
  public function set_taxonomies($taxonomies) {
   
    if ($taxonomies != "*") {

      $this->taxonomies = array();
      
      if (!is_array($taxonomies)) {
        $taxonomies = explode(",", $taxonomies);
      }
      
      foreach ($taxonomies as $tax) {
        $t = trim($tax);
        $this->taxonomies[] = $t;
        $this->taxonomy_limits[$tax] = $archive_limit = apply_filters( "mp_rest_{$tax}_limit", 50 );
      }

    }
      
  }
  
  public function set_post_types($post_types) {
    if ($post_types != "*") {
      
      $this->post_types = array();

      if (!is_array($post_types)) {
        $post_types = explode(",", $post_types);
      }

      foreach ($post_types as $type) {
        $t = trim($type);
        $this->post_types[] = $t;
        $this->post_type_limits[$type] = $archive_limit = apply_filters( "mp_rest_{$type}_limit", $this->post_limit );
      }

    }
  }
  
  
  public function exists() {
    return true;
  }
  
  public function slug() {
    return $this->slug;
  }
  
  public function has_post_type($type) {
    return $this->post_types === true || in_array($type, $this->post_types); 
  }

  public function has_taxonomy( $tax ) {
    return $this->taxonomies === true || in_array($tax, $this->taxonomies); 
  }

  public function parse_callback($callback) {
    
    if ($callback == "") {
      return false;
    }
    
    if (function_exists($callback)) {
      return $callback;
    }
    
    if (is_array($callback)) {
      
      if (count($callback) == 2) {

        if (method_exists($callback[0], $callback[1])) {
          return array($callback[0], $callback[1]);
        }

      } 
      
      return false;
      
    } 
    
    if (preg_match("/([A-Za-z\_0-9]+)(?:\:\:|\.|\-\>)([A-Za-z\_0-9]+)/", $callback, $matches)) {
      $class = $matches[1];
      $method = $matches[2];
      
      if (method_exists($class, $method)) {
        return array($class, $method);
      }
      
    } 
      
    return false;
    
  }
  
  public function endpoint_key($url) {
    return "ep_custom_" . preg_replace( "/\/\-/", "_", $url);
  }
  
  public function endpoint_url($url) {
    global $wf;
    return $wf->site->url() . trim( $this->slug(), "/") . "/" . trim($url, "/");
  }
  
  public function setup_rules(&$rules) {
    
    global $wf;
    
    $top = $this->slug();
    
    $rules[$top . "/?$"] = 'index.php?mp_rest_endpoint=ep_entry';
    
    foreach ($this->endpoints as $name => $info) {  
      $rules[trim( $this->slug(), "/") . "/" . trim($info["url"], "/") . "/?$"] = 'index.php?mp_rest_endpoint=' . $name;
    }
    
    foreach ($wf->post_types as $post_type) {
      
      $ps = $post_type->rewrite["slug"];
      $pn = $post_type->name;

      if ( $this->has_post_type($pn) ) {
    
        if ($pn == "page") {
          $ps = "name";
        }
      
        if ($ps == "") {
          $ps = $pn;
        }

        $this->add_post_type_rule($rules, $ps, $pn);

      }
    
    } // post_types
  
    foreach ($wf->taxonomies as $tax) {
    
      $ts = $tax->rewrite["slug"];
      $tn = $tax->name;
      
      if ( $this->has_taxonomy($tn) ) {
    
        if ($ts == "") {
          $ts = $tn;
        }

        $this->add_taxonomy_rule($rules, $ts, $tn);
        
      }
      
    } // taxonomies
    
  } 


  
  public function add_taxonomy_rule(&$rules, $slug, $taxonomy_name) {
   
    global $wf;
    
    $tax = $wf->taxonomy($taxonomy_name);

    $top = $this->slug();
    
    if ($tax->exists()) {
      
      // format pattern part
      
      $tn = $taxonomy_name;
      
      // add ".json,.xml" support to URLs
      
      $rules[$top . "/" . $slug."/?$"] = 'index.php?taxonomy='.$tn.'&mp_rest_endpoint=ep_taxonomy';
      $rules[$top . "/" . $slug."/([^/]+)/?$"] = 'index.php?taxonomy='.$tn.'&'.$tn.'=$matches[1]&mp_rest_endpoint=ep_term';

    }
    
  }
  
  public function add_post_type_rule(&$rules, $slug, $post_type_name) {
   
    global $wf;
    
    $post_type = $wf->type($post_type_name);

    $top = $this->slug();
    
    if ($post_type->exists() && $post_type->publicly_queryable) {
      
      // format pattern part
      
      $pn = $post_type_name;
      
      // add ".json,.xml" support to URLs
      
      if ($pn != "attachment" && $slug != "") {
        
        $rules[$top . "/" . $slug."/?$"] = 'index.php?post_type='.$pn.'&mp_rest_endpoint=ep_archive';

        foreach ($post_type->taxonomies() as $taxonomy) {
          $ts = $taxonomy->rewrite["slug"];
    
          if ($pn != "" && $ts != "") {
            $rules[$top . "/" . $slug . "/".$ts."/([^/]+)/?$"] = 'index.php?mp_rest_endpoint=ep_term&'.$taxonomy->name.'=$matches[1]&post_type='.$post_type_name;
          }
        }

        
        if ($pn == "post") {
          $rules[$top . "/" . $slug."/([^/]+)/?$"] = 'index.php?name=$matches[1]&mp_rest_endpoint=ep_single';
        } else {
          $rules[$top . "/" . $slug."/([^/]+)/?$"] = 'index.php?'.$pn.'=$matches[1]&mp_rest_endpoint=ep_single';
        }
        
      } // !attachment

    }
    
  }
  
  
  
  
  
  // ---- used by all MEOW objects to delegate to a field set if present

  public function eval_field($obj, $name, $fields) {  
    
    global $wf;
    
    if (count($fields)) {
      
      // check if this $name corresponds to a field set
      
      $set = $obj->set($name);
      
      if ($set->exists()) {
      
        $val = array();
         
        if ($set->info->allow_multiple) {
          
          foreach ($set as $item) {
            
            $ival = array();
            
            foreach ($fields as $field) {
              $n = $field["name"];
              
              $set_field = $item->field($n);
              
              if ($set_field->exists()) {
                $ival[$n] = $set_field->json();
              } else {
                $ival[$n] = $wf->eval_expression( $n, $item );
              }
            
            }
            
            $val[] = $ival;
            
          }
          
          
        } else {
          
          foreach ($fields as $field) {
            
            $n = $field["name"];

            $set_field = $set->field($n);
            
            if ($set_field->exists()) {
              $val[$n] = $set_field->json();
            } else {
              
              $val[$n] = $wf->eval_expression( $n, $set );
            }

          }
          
        }
      
        return $val;
        
      } 
      
    }
      
    return false;
    
    
  }
  
  
  // ----- dispatches to the correct endpoint
  
  public function dispatch() {
    
    global $wf;
    
    $data = null;
    
    $ep = $wf->qv("mp_rest_endpoint");
    
    
    if ($ep) {
      if (method_exists($this, $ep)) {
        $data = call_user_func(array($this, $ep)); 
      } else {
        
        if (isset( $this->endpoints[$ep], $this->endpoints[$ep]["callback"] ) && $this->endpoints[$ep]["callback"] ) {
          $obj = $this->endpoints[$ep];
          $args = wp_parse_args( $obj["args"], array("url" => $obj["url"] ) );
          $json = array("href" => $this->endpoint_url($obj["url"]));
          
          $call_args = array( $args );
          
          $type = $obj["type"];
          
          if ($type == "json") {
            $call_args[] = $json;
          }
          
          $data = call_user_func_array( $obj["callback"], $call_args );

          if ($data) {
            if ($type == "html") {
              header("Content-Type: $this->content_type_html");
              echo $data;
            } else {
              header("Content-Type: $this->content_type_json");
              $this->output_json($data);
            } 
            
            exit;
            
          }
          
        }
        
      }
    }
    
    if ($data) {
      
      header("Content-Type: $this->content_type_json");
      $this->output_json( $data );
      
      // don't render anything else
      exit;
      
    }   
    
  }
  
  public function output_json($data) {
       
    global $wf;
    
    $out = $data;
    
    $callback = $wf->has_get("callback");
    
    if (!is_string($data)) {
      
      if (WOOF::is_true_arg($_GET, "pretty")) {
        $out = $wf->json_indent( json_encode($data) );
      } else {
        $out = json_encode($data);
      }
      
    } 
    
    // now check for the callback
    
    if ($callback) {
      
      // check for valid chars (security)
      
      if ( preg_match("/^[a-z\_0-9]+$/", $callback ) ) {
        $out = $callback . "(" . $out . ");";
      }

    }
    
    echo $out;
    
  }
  
  
  public function add_endpoint($name, $url, $callback = "", $args = array()) {
    $this->endpoints[$name] = array( "url" => $url, "type" => "json", "callback" => $this->parse_callback( $callback ), "args" => $args );
  }

  public function add_html_endpoint($name, $url, $callback = "", $args = array()) {
    $this->endpoints[$name] = array( "url" => $url, "type" => "html", "callback" => $this->parse_callback( $callback ), "args" => $args );
  }
  
  
  
  public function accepted_args($args) {
    
    if (!is_array($args)) {
      $args = array_map("trim", explode(",", $args));
    }
    
    return array_merge($args, $this->base_accepted_args);
  }
  
  public function parse_type( $chr ) {
    
    if ($chr == "#") {
      return "int";
    } else if ($chr == "@") {
      return "date";
    } else if ($chr == "!") {
      return "boolean";
    } else if ($chr == "?") {
      return "null";
    } else if ($chr == "$") {
      return "float";
    }
    
    return "string";
    
  }
  
  public function arg_types($args) {
    //"!@#"
    
    $info = array();
    
    foreach ($args as $arg) {
      preg_match( "/(\$|\?|@|\#|\!)?([a-z_]*)/", $arg, $matches );
      
      $count = count($matches);
      
      $key = null;
      
      if ($count == 3) {
        $type = $this->parse_type( $matches[1] );
        $key = $matches[2];
      } else if ($count == 2) {
        $key = $matches[1];
        $type = "string";
      } 
      
      if (isset($key)) {
        $info[$key] = $type;
      }
      
    }
    
    return $info;
    
  }
     
  public function check_arg($value, $type) {
    
    return 
      ($type == "string") ||
      ($type == "int" && is_numeric($value)) ||
      ($type == "float" && is_numeric($value)) ||
      ($type == "date" && strtotime($value)) ||
      ($type == "boolean" && ( $value == "0" || $value == "false" || $value == "1" || $value == "true"));
    
  }
  
  public function typed_val($value, $type) {
    
    if ($type == "int") {
      return (int) $value;
    } else if ($type == "float") {
      return (float) $value;
    } else if ($type == "date") {
      return date("c", strtotime($value));
    } else if ($type == "boolean") {
      return ($value != "0" || $value != "false");
    }
    
    return $value;

  }
  
  public function parse_args($args, $defaults = array(), $accepted = array(), $normalized = array(), $noget = array()) {
    
    global $wf;
    
    $all_accepted = $this->arg_types( $this->accepted_args($accepted) );
    $all_args = wp_parse_args( $args, $defaults );
    
    $g = $_GET;
    
    foreach ($this->to_array($noget) as $ng) {
      unset($g[$ng]);
    }
       
    if (count($g)) {
      $all_args_get = wp_parse_args( $g, $all_args ); 
    } else {
      $all_args_get = $all_args;
    }
  
    $ret = array();
    
    if (count($normalized)) {
      $norm = wp_parse_args($normalized);
    }
    
    // now white-list and check the types are valid
    
    foreach ($all_args_get as $key => $value) {
      
      // check if there is a get value, which trumps
      
      $val = $value;

      if ( isset( $all_accepted[$key] ) ) {
        $valid = false;
        
        $type = $all_accepted[$key];
        
        if ($this->check_arg( $val, $type ) ) {
          $valid = true;
        } else {
          // check if the standard arg is valid
          if (isset($all_args[$key]) && $this->check_arg( $all_args[$key], $type ) ) {
            $valid = true;
            $val = $all_args[$key];
          }
           
        }

        $val = $this->typed_val($val, $type);
        
        if ($valid) {
          if (isset($norm[$key])) {
            $ret[$norm[$key]] = $val;
          } else {
            $ret[$key] = $val;
          }
        }
      }
      
    }
    
    return $ret;

  }

  public function to_array($list) {
    if (!is_array($list)) {
      $list = explode(",", $list);
    }
    
    return array_map("trim", $list);
  }
  
  
  public function build_query($args, $overrides = array(), $remove = array()) {
    
    $r = $args;
    
    // remove params
    foreach ($this->to_array($remove) as $rem) {
      unset($r[$rem]);
    }
    
    $ov = wp_parse_args($overrides);
    
    foreach ($ov as $key => $value) {
      $r[$key] = $value;
    }
    
    return build_query($r);
    
  }
  
  public function remap_args( $r, $map = array() ) {
    
    $ret = $r;
    $m = wp_parse_args($map);
    
    foreach ($m as $key => $new_key) {
      if (isset($ret[$key])) {
        $ret[$new_key] = $ret[$key];
        unset($ret[$key]);
      }
    }
    
    return $ret;
    
  }
  
  // --------------------- Utility methods to output standard WordPress objects
  
  
  public function posts($args = array(), $noget = array("tax_query")) {
    
    global $wf;
    
    $aa = wp_parse_args($args);
    
    $r = $this->parse_args( 
      $args, 
      array(
        "orderby" => "title",
        "order" => "asc",
        "limit" => $this->post_limit,
        "offset" => 0,
        "type" => "post"
      ),
      "#offset,#limit,#posts_per_page,order_by,orderby,order,type,@before,@after,status,post_status,#author,search,s,tag,category,tax_query,ep",
      array(
        "limit"   => "posts_per_page",
        "order_by" => "orderby",
        "type"    => "post_type",
        "status" => "post_status",
        "search" => "s"
      ),
      $noget
    );

    $ng = $this->to_array($noget);
    
    $type = $r["post_type"];
    
    // set more appropriate ordering defaults if no order was explicitly specified
    if (!isset($args["orderby"]) && !isset($args["order_by"]) && !isset($args["order"])) {
    
      if ($type == "post") {
        $r["orderby"] = "post_date";
        $r["order"] = "desc";
      } else if ($type == "page") {
        $r["orderby"] = "menu_order";
        $r["order"] = "asc";
      }
      
      // ... but also allow developers to override
      
      $r["orderby"] = apply_filters("mp_rest_archive_order_by", $r["orderby"], $type);
      $r["order"] = apply_filters("mp_rest_archive_order", $r["order"], $type);
      
    }
    
    $limit = $this->post_limit;
    
    if (isset($this->post_limits[$type])) {   
      $limit = $this->post_limits[$type];
    }
    
    $post_type = $wf->types($type);
    
    if ($r["posts_per_page"] != -1) {
      $use_limit = max(1, min($limit, $r["posts_per_page"]));
    } else {
      $use_limit = max(1, $limit);
    }
    
    $use_offset = $r["offset"];

    $r["posts_per_page"] = $use_limit;
    
    $page = floor( $use_offset / $use_limit ) + 1;
    
    if (isset($r["ep"])) {
      $ep = $r["ep"];
    } else {
      $ep = $this->endpoint_url( $post_type->rewrite_slug(true) );
    }
  
    unset($r["ep"]);
    
    $pr = $r;
    
    unset($pr["fields"]);
    
    $query = new WP_Query( $pr );

    
    $posts = $wf->wrap( $query->posts );
    
    $found = (int) $query->found_posts;
      
    $mod = floor( $found % $use_limit );

    $final_offset = ( $found ) - $mod;
    
    if ($mod == 0) {
      $final_offset = $final_offset - $use_limit;
    }
    
    $r = $this->remap_args($r, "posts_per_page=limit&post_type=type&post_status=status&s=search");
    
    
    // remove any keys that cannot be overridden with a $_GET
    
    // this is a sub data key
    $sub = WOOF::is_true_arg($aa, "sub");
   

    WOOF::array_remove_keys($r, $ng);
    
    $pages = ceil( $found / $use_limit );
    
    $data = array();
    
    if (!$sub) {
      $data["href"] = $wf->current_url();
    }
    
    $data["found"] = $found;
    $data["limit"] = $use_limit;
    $data["offset"] = $use_offset;
    $data["page"] = $page;
    $data["pages"] = $pages;
    $data["first"] = array( "href" => $ep . "?" . $this->build_query($r, "limit=$use_limit", "offset" ) );
    $data["previous"] = null;
    $data["next"] = null;
    $data["last"] = array( "href" => $ep . "?" . $this->build_query($r, "limit=$use_limit&offset=$final_offset" ) );
    
    if ($page != 1) {
      $data["previous"] = array( "href" => $ep . "?" . $this->build_query($r, "limit=$use_limit&offset=" . ( ( $page - 2 ) * $use_limit ) ) );
    }
    
    if ($found > ($use_offset + $use_limit)) {
      $data["next"] = array( "href" => $ep. "?" . $this->build_query($r, "limit=$use_limit&offset=" . ($page * $use_limit ) ) );
    }
  
    
    $fields = apply_filters( "mp_rest_archive_fields", "", $type );
    
    if ($expand = $wf->has_get("expand")) {
      $expand_fields = $wf->parse_field_list($expand);
      
      if (isset($expand_fields["posts"])) {
        
        $def = MEOW_Post::json_fields();
        
        if (is_array($expand_fields["posts"])) {
          $def = $expand_fields["posts"];
        }
        
        $fields = apply_filters( "mp_rest_single_fields", $def, $type );
      }
      
    }
    
    
    if ($wf->has_get("fields")) {
      $fields = $wf->filter_field_list( apply_filters( "mp_rest_archive_fields_allowed", $fields, $type ) );
    }
  
    // need to allow query params here, and default limits etc

    $data["posts"] = array();

    foreach ( $posts as $post ) {
      $data["posts"][] = apply_filters("mp_rest_archive_json", $post->json($fields), $post );
    }
      
    $json = apply_filters("mp_rest_archive_json", $data, $type );

    return $data;

  }
  
  
  
  public function terms($args) {
    
    
  }
  
  
  // --------------------- Built-in API end-points
  
  
  public function ep_entry() {
  
    global $wf;
  
    $json["href"] = $wf->site->url . $this->slug;
    
    foreach ($this->endpoints as $name => $info) {
      $json[$name] = array("href" => $this->endpoint_url($info["url"]));
    }
    
    
    
    foreach ($wf->post_types() as $post_type) {
      
      if ($post_type->publicly_queryable && $this->has_post_type($post_type->name) ) {
        
    
        $url = $post_type->json_href();
        
        if ($url) {
        
          $slug = $post_type->rewrite_slug(true);

          if (!isset($json["post_types"])) {
            $json["post_types"] = array();
          }

          $json["post_types"][$slug] = array("href" => $url);
          
        }
      
      }
      
    }

    foreach ($wf->taxonomies() as $tax) {
      
      $url = $tax->json_href();
      
      if ( $this->has_taxonomy($tax->name) ) {

        if ($url) {
          
          if (!isset($json["taxonomies"])) {
            $json["taxonomies"] = array();
          }

          $slug = $tax->rewrite_slug(true);
          $json["taxonomies"][$slug] = array("href" => $url); 
        }

      }
      
    }
    
    return $json;
    
  } // ep_entry()
  
  
  
  public function ep_single() {
    
    global $wf;
    
    $type = $wf->qv("post_type");
    
    $post = new WOOF_Silent("no post");
    
    if ($wf->has_qv($type)) {
      $slug = $wf->qv($type);
      $post = $wf->post($slug, $type);
    } else if ($wf->has_qv("name")) {
      $slug = $wf->qv("name");
      $post = $wf->post($slug, "post");
    }
    
    if ($post->exists()) {

      $fields = apply_filters( "mp_rest_single_fields", MEOW_Post::json_fields() , $type );

      if ($wf->has_get("fields")) {
        $fields = $wf->filter_field_list( apply_filters( "mp_rest_single_fields_allowed", $fields, $type ) );
      }
      
      $json = apply_filters("mp_rest_single_json", $post->json($fields), $post );
      
      // override the current HREF
      
      $json["href"] = $wf->current_url();
      
      return $json;
    } // post exists
  
  } // ep_single
  
  
  public function ep_archive() {
  
    global $wf;
    
    if ($wf->has_qv("post_type")) {

      $type = $wf->qv("post_type");
      
      $post_type = $wf->type($type);
    
      $args = array("type" => $type);
      
      if ($post_type->exists()) {

        $json = $this->posts($args, "type");
        
        $json["label"] = $post_type->label();
        
        return $json;
        
      } // post exists
    
    } // has_qv type
  
  } // ep_archive
  
  
  public function ep_taxonomy() {
    
    global $wf;
    
    if ($wf->has_qv("taxonomy")) {

      $tax = $wf->taxonomy($wf->qv("taxonomy"));

      if ($tax->exists()) {

        $fields = $wf->parse_field_list( apply_filters( "mp_rest_taxonomy_fields", MEOW_Term::json_fields() , $tax->name ) );
      
        $json = array();
        
        $json["href"] = $wf->current_url();
      
        $json["terms"] = array();
      
        foreach ($tax->terms as $term) {
        
          $term_json = $term->json($fields);
          
          foreach ($tax->post_types as $post_type) {
            $term_json[$post_type->rewrite_slug(true)] = $post_type->json_href . "/" . $tax->rewrite_slug(true) . "/" . $term->slug;
          }
        
          $json["terms"][] = $term_json;
        
        }
      
      
        return $json;
      
      }
    
    } 
    
    
  } // ep_taxonomy
  
  
  public function ep_term() {
    
    global $wf;
    
    if ($wf->has_qv("taxonomy")) {
      $tax = $wf->taxonomy($wf->qv("taxonomy"));

      if ($tax->exists()) {
    
        // check for the term
        $tax_name = $tax->name();
    
        $term_slug = $wf->qv($tax_name);
    
        if ($term_slug) {

          $term = $wf->term($term_slug, $tax_name);
      
          if ($term->exists()) {
      
            $term_fields = $wf->parse_field_list( apply_filters( "mp_rest_term_fields", MEOW_Term::json_fields() , $tax_name ) );

            $json = $term->json($term_fields);
          
            // override the current href
            $json["href"] = $wf->current_url();


            // output the posts
              
            if ($wf->has_qv("post_type")) {
          
              $pt = $wf->qv("post_type");
          
              $json["posts"] = array();
                
              $fields = $wf->parse_field_list( apply_filters( "mp_rest_archive_fields", MEOW_Post::json_fields() , $pt ) );

              $args = $wf->types($pt)->in_the($term->slug, $tax_name, "args=1");
                
              $args["ep"] = $this->endpoint_url( $wf->types($pt)->rewrite_slug(true) . "/" . $tax->rewrite_slug( true ) . "/" . $term_slug );
              $args["sub"] = true;
              
              $json["posts"] = $this->posts( $args, "tax_query,type" );
              
              
            } else {
          
              // TODO, fill in this endpoint
          
            }
        
            return $json;

          }
      
        }
    
      }
      
    }
  
  } // ep_term
  
}
