<?php

class MPC_Term extends MPC {
  
  protected static $term;
  protected static $manage_post_type;
  protected static $manage_taxonomy;
  
  public static function inline_head() {
    global $wf;
    $term = self::get_term();
    MPV_Meta::inline_head(self::assigned_field_sets(), $term);
  }
  

  public static function assigned_field_sets() {
    // for now, we'll ignore the term, but we may use it later
    global $meow_provider;
    return $meow_provider->taxonomy_field_sets($_GET["taxonomy"]);
  }

  public static function assigned_field_types() {
    // for now, we'll ignore the term, but we may use it later
    global $meow_provider;
    return $meow_provider->taxonomy_field_types($_GET["taxonomy"]);
  }
  
  public function get_term() {
    
    global $wf;
    
    if (!isset(self::$term)) {
      $tax_name = $_GET["taxonomy"];
      
      if (isset($_GET["tag_ID"])) {
        $term_id = $_GET["tag_ID"];
        self::$term = $wf->term_by_id($term_id, $tax_name);
      } else {
        self::$term = $wf->taxonomy($tax_name)->create();
      }
    
    }
    
    return self::$term;
      
  }


  public static function filter_manage_terms($args) {
  
    if (isset($_REQUEST["mp_term__in"])) {
      
      $in = explode(",", $_REQUEST["mp_term__in"]);
      
      if (count($in)) {
        $args["include"] = $in;
      }
      
    }

    return $args;

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
  
  public static function manage_taxonomy() {
    
    $taxonomy = "post_tag";

    if (!isset(self::$manage_taxonomy)) {
      if (isset($_GET["taxonomy"])) {
        $taxonomy = $_GET["taxonomy"];
      }

      self::$manage_taxonomy = MPM_Taxonomy::find_by_name($taxonomy);
    }
    
    return self::$manage_taxonomy;
  }


  public static function define_term_columns( $columns ) {

    $tax = self::manage_taxonomy();
    $pt = self::manage_post_type();
     
    $posts_label = "Posts";
    
    if ($pt) {
      $posts_label = $pt->labels["name"];
    }
    
    if ($tax) {
      
      $columns = array();
    
      foreach ($tax->columns() as $column) {
      
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
            } elseif ($key == "posts") {
              $title = $posts_label;
            }


          }
      
          $columns[$key] = $title;
        }
      
      }

      return $columns;
    
    }
    
  }

  
  public static function term_column_content( $out, $key, $term) {

    global $taxonomy, $wf;
    
    $mode = "list";
    
    if (isset($_GET["mode"])) {
      $mode = $_GET["mode"];
    } 

    $the_term = $wf->term_by_id($term, $taxonomy);

    $tax = self::manage_taxonomy();
    
    $columns = $tax->columns_by_key();
    $column = $columns[$key];
    
    if (!isset($column["core"])) {
      
      // we only need to output custom columns
      
      if (isset($column["content"])) {
        $content = stripslashes($column["content"]);
        MasterPress::$context = "col";
        $out = WOOF::eval_expression($content, $the_term);
        MasterPress::$context = "";
        echo $out;
      }
      
    }
    
  }
  
  
  
  public static function field_sets($t) {
    
    global $wf;
    $term = self::get_term();
    $sets = self::assigned_field_sets();

    ?>
    
    <tr class="meta-boxes">
      <td colspan="2">
      
      
      <?php foreach ($sets as $set) : ?>

      <?php if ($set->current_user_can_see()) : ?>

      <div id="field-set-<?php echo $set->html_id() ?>" class="postbox nodrag">
        <h3 class="hndle">
          <em><?php echo $set->display_label() ?></em>
          <?php

          if ($set->current_user_can_manage()) { 
            $mu = $set->manage_url();
            
            if ($mu) { ?>
              <a href="<?php echo $mu ?>" class="mp-go with-mptt" data-tooltip="<?php _e("Manage Field Set", MASTERPRESS_DOMAIN) ?>"><?php _e("Manage", MASTERPRESS_DOMAIN) ?></a>  
            <?php
            }

          }
          ?>  
        </h3>
        <div class="inside">
        <?php MPV_Meta::set($term, $set); ?>
        </div>
      </div>
      
      <?php endif; ?>
      
      <?php endforeach; ?>
          
      </td>
    
    </tr>

    <?php

  }
  
  

}

?>