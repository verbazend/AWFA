<?php

class MPC_Post extends MPC {
  
  public static function get_sets() {


    global $wf, $meow_provider;
    
    $object_id = $_REQUEST["object_id"];
    
    $post = $wf->post($object_id);

    $template = $_REQUEST["template"];
    
    if ($template != "") {
      // switch the template in the $meow_provider
      $meow_provider->template_switch = $template;
    }

    $info = array();
    $info["sets"] = array();
    
    $set_ids = $_REQUEST["set_ids"];
    
    if (isset($_REQUEST["set_ids"])) {
      
      foreach ($set_ids as $set_id) {
      
        if (trim($set_id) != "") {
          $is = array();
      
          $set = MPM_FieldSet::find_by_id($set_id);
    
          if ($set) {
            $is["title"] = $set->display_label();
            $is["id"] = $set_id;    
            $meow_set = $post->set($set->name);

            $is["html"] = MPV_Meta::get_set($post, $meow_set);
            $is["templates"] = MPV_Meta::get_set_templates($post, $set);
      
            $info["sets"][] = $is;
          }
        
        }
        
      }
    
      
    }
    
    unset($meow_provider->template_switch);
  
    self::ajax_success($info);
  }
  
  protected static $manage_post_type;
  
  public static function inline_head() {
    global $wf;
    MPV_Meta::inline_head(self::assigned_field_sets(), $wf->the);
  }
  
  public static function assigned_field_sets($post = null) {
    global $meow_provider;
    
    $post = self::get_post($post);
  
    if ($post) {

      $post_type_name = $post->post_type->name;
    
      // get the model object for the post type
      // we need the cached version here, since we want all properties to be cached on that if possible
      $post_type = MasterPress::$post_types[$post_type_name];
    
      return $meow_provider->post_type_field_sets($post->type_name(), $post->template());
      
    }
    
    return array();
  }

  public static function assigned_field_types($post = null) {
    global $meow_provider;

    $post = self::get_post($post);

    if ($post) {

      $post_type_name = $post->post_type->name;
    
      // get the model object for the post type
      // we need the cached version here, since we want all properties to be cached on that if possible
      $post_type = MasterPress::$post_types[$post_type_name];
    
      return $meow_provider->post_type_field_types($post->type_name(), $post->template());
    
    }
  
  }

  protected static function get_post($post = null) {
    global $wf;

    // loop over the sets, loading the sets available to the current post type

    if (is_null($post)) {
      $post = $wf->the;
    } 
  
    return $post;
    
  }
  
  public static function post_editor_meta_boxes() {
    global $wf;
  
    // loop over the sets, loading the field sets available to the current post type, and passing it to the content function
    foreach (self::assigned_field_sets() as $set) {
      if ($set->current_user_can_see()) {
        
        $go = "";
        
        if ($set->current_user_can_manage()) {
          $mu = $set->manage_url();
          
          if ($mu) {
            $go = '<a href="'.$mu.'" class="mp-go with-mptt" data-tooltip="'.__("Manage Field Set", MASTERPRESS_DOMAIN).'">'.__("Manage", MASTERPRESS_DOMAIN).'</a>';  
          }
        }
        
        add_meta_box( "field-set-".$set->html_id(), '<em>'.$set->display_label().'</em>'.$go, array("MPC_Post", "field_set_meta_box_content"), $wf->the->type->name, $set->sidebar ? "side" : "normal", "high", array("set" => $set) );
      }
    }

    $types = apply_filters( "mp_detail_post_types", array(), $wf->the->type_name() );
    
    foreach ($types as $type) {
      $post_type = $wf->type($type);
      add_meta_box( "detail-post-type-" . $type , '<em>'.$post_type->plural_label().'</em>', array("MPC_Post", "post_type_meta_box_content"), $wf->the->type->name, "normal", "high", array( "post_type" => $post_type ) );
    }
    
    if ($wf->the->type->supports("mp-page-attributes")) {
      add_meta_box( "mp-attributes", __($wf->the->type->singular_label . " Attributes"), array("MPC_Post", "attributes_meta_box_content"), $wf->the->type->name, "side", "default" );
    }

  }

  public static function post_type_meta_box_content($post, $r) {
    
    global $wf;
    
    $args = $r["args"];
    $post_type = $args["post_type"];
    
    $the_post = $wf->post($post->ID);
    
    MPV_Meta::detail_post_type($the_post, $post_type);

    
    
  } 

  public static function field_set_meta_box_content($post, $r) {
    global $wf;
    
    $args = $r["args"];
    $set = $args["set"];

    $the_post = $wf->post($post->ID);

    MPV_Meta::set($the_post, $set);
  }

  protected static function post_options($posts, $value, $post__not_in = array(), $level = 0) {
    global $wf;
    $the = $wf->the();

    $class = ' class="level-'.$level.'"';
    
    foreach ($posts as $post) : 
      $id = $post->id(); $selected_attr = WOOF_HTML::selected_attr($id == $value); 
      
      if ($the->id != $id) : 
      ?>
      <option <?php echo $selected_attr." ".$class ?> value="<?php echo $id ?>"><?php echo $post->title() ?></option>
      <?php 
      self::post_options($post->children(array("orderby" => "menu_order", "order" => "asc", "post__not_in" => $post__not_in)), $value, $post__not_in, $level + 1);
      endif;
      
    endforeach;
  }
  
  protected static function flatten_assigned($sets) {
    $ret = array();
    
    foreach ($sets as $set) {
      $ret[] = $set->id;
    }
    
    return implode(",", $ret);
  }
  
  public static function attributes_meta_box_content($post, $r) {
    
    // custom attributes metabox that mimics the core box, 
    // but allows templates to be assigned to other post types 
    
    global $wf, $meow_provider, $wpdb;
    $the = $wf->the();
    
    $type = $the->type();

    $type_name = $type->name;
    
    $type_model = MPM_PostType::find_by_name($type_name);

    $my_templates = array();
    
    // prime the templates
    
    $models = array();
     
    foreach (get_page_templates() as $template => $file) {
      $models[$file] = MPM_Template::find_by_id($file);
    }
    
    
    foreach ($type_model->templates() as $template) {
      $my_templates[] = $template->id;
    }

    
    $children = $the->children->extract("id");

    
    if (is_array($children) && count($children)) {
      $post__not_in = array_unshift($children, $the->id);
    } else {
      $post__not_in = array($the->id);
    }
  
    $meta_template = $the->meta("_wp_page_template", true);


    if (!is_array($post__not_in)) {
      $post__not_in = array($post__not_in);
    }
    
    if (count($post__not_in)) {
      $posts = $type->top_posts(array("cache_results" => false, "orderby" => "menu_order", "order" => "asc", "post__not_in" => $post__not_in));
    } else {
      $posts = $type->top_posts(array("cache_results" => false, "orderby" => "menu_order", "order" => "asc"));
    }
    
    $parent = apply_filters("mp_edit_post_parent", $the->post_parent, $the);
    
    if ($parent == 0) {
      $parent = apply_filters("mp_edit_post_default_parent", $parent, $the->id, $the);
    }
    
    
    
    $menu_order = $the->menu_order;
    
    ?>
    
    <?php if ($type->hierarchical() && count($posts)) : ?>
    <p><strong><?php _e("Parent") ?></strong></p>
    
    <select name="parent_id" id="parent_id">
      <option><?php _e("(no parent)") ?></option>
      <?php self::post_options($posts, $parent, $post__not_in ); ?>
    </select>
    <?php endif; ?>
    
    <?php if (count($my_templates)) : ?>
      
    <?php
      
    // get the fields for the current template

    $default_template = $the->default_template();
    
    $tm = MPM_Template::find_by_id($default_template);
    $data_sets = self::flatten_assigned($meow_provider->post_type_field_sets($type_name, $default_template));
    $supports = $tm->supports;
    
    $templates = get_page_templates();
      
    ksort($templates);
    
    
    ?>
    
    <p class="label_page_template"><strong><?php _e("Template") ?></strong></p>
    
    <select name="page_template" id="page_template" autocomplete="off">
      <option value="default" data-supports="<?php echo $supports ?>" data-sets="<?php echo $data_sets ?>"><?php _e( "( Default Template )", MASTERPRESS_DOMAIN) ?></option>
      <?php foreach ($templates as $template => $file) : $selected_attr = WOOF_HTML::selected_attr($meta_template == $file); ?>
      <?php if (in_array($file, $my_templates)) : ?>
      
      <?php 
      $tm = $models[$file];
      $data_sets = self::flatten_assigned($meow_provider->post_type_field_sets($type_name, $file)); 
      $supports = $tm->supports;
      ?>
      
      <option <?php echo $selected_attr ?> data-supports="<?php echo $supports ?>" data-sets="<?php echo $data_sets ?>" value="<?php echo $file ?>"><?php echo $template ?></option> 
      <?php endif; ?>
      <?php endforeach; ?>
    </select>

    
    <p id="mp-templates-loading-fields"><?php _e("Loading additional fields. Please wait&hellip;"); ?></p>
    
    <?php endif; ?>
    
    <p><strong><?php _e("Order") ?></strong></p>
    <p><label class="screen-reader-text" for="mp_menu_order"><?php _e("Order") ?></label>
    <input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $menu_order ?>"></p>    

    <?php

  }
  


  public static function manage_posts_views($views) {
    global $wf, $wp_query;
    
    
    if (isset($_REQUEST["mp_view"])) {
    
      if (isset($views["all"])) {
        $views["all"] = str_replace('class="current"', "", $views["all"]);
      }

      $views = $views + array( "filtered" => '<span class="list-filter-current"><b>'. stripslashes(urldecode( esc_attr( $_REQUEST["mp_view"] ))) .'</b><span class="count">&nbsp;('.$wp_query->post_count.')</span></span>' ); 

    }
    
    $view_labels = array();
    
    foreach (MasterPress::$taxonomies as $tax) {
      
      if (isset($_REQUEST[$tax->name])) {
        
        if (trim($_REQUEST[$tax->name]) != "") {
        
          $term = $wf->term($_REQUEST[$tax->name], $tax->name);
          $view_labels[] = $tax->labels["singular_name"].": ".$term->name;
          
        }
        
      }
      

    }
    
    if (count($view_labels)) {
      
      if (isset($views["all"])) {
        $views["all"] = str_replace('class="current"', "", $views["all"]);
      }
    
      $post_type = $wf->types($_REQUEST["post_type"]);
    
      $term = $wf->term($_REQUEST["term"], $_REQUEST["taxonomy"]);
    
      if ($term) {
        $taxonomy = $term->taxonomy();
        $views = $views + array( "filtered" => '<span class="list-filter-current">'. sprintf( __( '<b>%s</b>&nbsp;<span class="count">(%d)</span>', MASTERPRESS_DOMAIN ), implode(",&nbsp;", $view_labels), $wp_query->post_count ) .'</span>' ); 
      }
    
    }
      
    
    return $views;
    
  }
  

  public static function filter_manage_posts($query) {

    // allows the filter boxes in the manage posts view to work correctly.
    
    global $wf;
    
    $qv = &$query->query_vars;
    
    if (isset($_REQUEST["mp_post__in"])) {
      
      $in = explode(",", $_REQUEST["mp_post__in"]);
      
      if (count($in)) {
        $qv["post__in"] = $in;
        $qv["posts_per_page"] = -1;
      }
      
    }
    
    foreach ($wf->taxonomies() as $tax) {
      
      if (isset($_REQUEST[$tax->name])) {
        $qv['term'] = $_REQUEST[$tax->name];
        $qv['taxonomy'] = $tax->name;
      }
      
    }
    
    // we only want this filter ONCE!
    remove_filter('parse_query', array("MPC_Post", "filter_manage_posts"));

  }
    
  public static function define_post_columns($columns) {
    
    global $wf;
    
    $mode = "list";
    $post_type = "post";
    
    if (isset($_GET["mode"])) {
      $mode = $_GET["mode"];
    } 

    if (isset($_GET["post_type"])) {
      $post_type = $_GET["post_type"];
    } 
    
    $pt = MPM_PostType::find_by_name($post_type);

    if ($pt) {
      
      $columns = array();
      
      foreach ($pt->columns() as $column) {
      
        $title = "";
      
        if (isset($column["title"])) {
          $title = $column["title"];
        }
      
        $key = "custom_".WOOF_Inflector::underscore($title);
      
        $disabled = "";
      
        if (isset($column["disabled"])) {
          $disabled = $column["disabled"];
        }
      
        if ($disabled != "yes") {
        
          if (isset($column["core"])) {
            $key = $column["core"];
        
            if ($key == "cb") {
              $title = "<input type=\"checkbox\" />";
            } else if ($key == "comments") {
              $image = admin_url("images/comment-grey-bubble.png");
              $title = <<<HTML
                <span class="vers"><img alt="Comments" src="{$image}"></span></span>
HTML;

            }
        
          }
      
          $columns[$key] = $title;
        }
      
      }

    }
    
    return $columns;
  }
  
  public static function manage_post_type() {
    
    $post_type = "post";
    
    if (!isset(self::$manage_post_type)) {
      if (isset($_GET["post_type"])) {
        $post_type = $_GET["post_type"];
      }

      self::$manage_post_type = MPM_PostType::find_by_name($post_type);

    }
    
    return self::$manage_post_type;
    
  }
  
  public static function post_column_content($key) {

    global $wf;
    
    $mode = "list";
    $post_type = "post";
    
    if (isset($_GET["mode"])) {
      $mode = $_GET["mode"];
    } 
    
    $pt = self::manage_post_type();

    if ($pt) {
      $columns = $pt->columns_by_key();
      if (isset($columns[$key])) {
        $column = $columns[$key];
    
        if (!isset($column["core"])) {
      
          // we only need to output custom columns
        
          if (isset($column["content"])) {
            $content = stripslashes($column["content"]);
            MasterPress::$context = "col";
            $out = WOOF::eval_expression($content, $wf->the);
            MasterPress::$context = "";
            echo $out;
          }
        }
      }
    }
    
  }
  
  public static function request($vars) {

    global $typenow;

    
    if (!isset($vars["orderby"]) && isset($typenow)) {
    
      // there is currently no sort order applied
      $post_type = MPM_PostType::find_by_name($typenow);

      if ($post_type) {

        if (!$post_type->_external) {
          list($sort_field, $sort_order) = explode("|", $post_type->manage_sort_order);
          $vars["orderby"] = $sort_field;
          $vars["order"] = $sort_order;
        }
    
      }
    
    }

    return $vars;
  }


  public static function manage_posts_link($text = "Manage Posts", $post_type = "post", $taxonomy = "", $term = "", $class = "") {
    
    $link = '<a href="edit.php?';
    
    if ($post_type != "post") {
      $link .= "post_type=".$post_type;
    } 
    
    if ($taxonomy != "" && $term != "") {
      $link .= "&".$taxonomy."=".$term; 
      $link .= "&taxonomy=".$taxonomy."&term=".$term;  // add this so we can hook into it in the "views" (so at least this can be showsn as a view)
    }
    
    $link .= '"';
    
    if ($class != "") {
      $link .= ' class="'.$class.'" ';
    }
    
    $link .= '>'.$text.'</a>';
    
    return $link;
  }
  

}

?>