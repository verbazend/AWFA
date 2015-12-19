<?php

class MPV {

  protected static $notifications = array();
  protected static $errors = array();
  protected static $warnings = array();
  protected static $successes = array();
  
  public $auto_form = false;
  public $method;
  public $method_args = array();
  public $title_args = array();
  
  protected $bucket = array();
  
  public function __get($name) {
    
    if (isset($this->bucket[$name])) {
      return $this->bucket[$name];
    }
    
    return null;
    
  }
  
  public function __set($name, $value) {
    $this->bucket[$name] = $value;
  }
  
  public static function err($str) {
    MPV::$errors[] = $str;
  }
  
  public static function notify($str) {
    MPV::$notifications[] = $str;
  }

  public static function success($str) {
    MPV::$successes[] = $str;
  }

  public static function warn($str) {
    MPV::$warnings[] = $str;
  }
  
  public static function field_set_icon_styles($field_sets) {
  ?>
    <style type="text/css">
    
    <?php foreach ($field_sets as $field_set) : ?>
    <?php if ($field_set->icon != "") : ?>
    span.field-set-<?php echo $field_set->id ?> { background-image: url(<?php echo MPU::field_set_icon_url($field_set->icon) ?>) }
    <?php endif; ?>
    <?php endforeach; ?>
    
    <?php MPU::mq2x_start(); ?>

    <?php foreach ($field_sets as $field_set) : ?>
    <?php if ($field_set->icon != "") : ?>
    span.field-set-<?php echo $field_set->id ?> { background-image: url(<?php echo MPU::field_set_icon_2x_url($field_set->icon); ?>); background-size: 16px 16px; }
    <?php endif; ?>
    <?php endforeach; ?>
    
    <?php MPU::mq2x_end(); ?>
    </style>
    
    <?php
  }
  
  public static function read_only_attr($test, $with_title = true) {
    
    if ($test) {
      echo ' readonly="readonly" ';
      
      if ($with_title) {
        echo ' title="'.__("This value cannot be changed", MASTERPRESS_DOMAIN).'" ';
      }
    }
  }

  public static function incl($file, $base = "core/view/mpv-") {
    include_once(MPU::path($base.$file).".php");
  }

  
  public static function read_only_class($test) {
    if ($test) {
      echo ' readonly ';
    }
  }
  
  public static function visibility_mode($vis, $key, $default = "none") {

    $allow = null;
    $deny = null;
    
    if (isset($vis[$key])) {
      $allow = $vis[$key];
    }
    
    if (isset($vis["not_".$key])) {
      $deny = $vis["not_".$key];
    }
  
    if (!is_null($allow) && $allow != "*") {
      return "allow"; 
    } else if (!is_null($deny)) {
      return "deny";
    } else if ($allow == "") {
      return $default;
    }

    return "all";
    
  }
  
  public static function fs_visibility($model, $args = array()) {
  
    global $wf;
    
    $first = true;
    
    $r = array(
      "sites" => null,
      "templates" => null,
      "post_types" => null,
      "taxonomies" => null,
      "fields" => null,
      "roles" => null      
    );
    
    $r["labels"] = wp_parse_args(
      
      $args["labels"], 
      array(
          "title" => __("control the visibility of this object in your site", MASTERPRESS_DOMAIN),
          "title_multisite" => __("specify the network sites that this object is available in", MASTERPRESS_DOMAIN),   
          "title_post_types" => __("specify the post types that this object is available in", MASTERPRESS_DOMAIN),   
          "title_templates" => __("specify the templates that this object is available in", MASTERPRESS_DOMAIN),   
          "title_roles" => __("specify the user roles that this object is available in", MASTERPRESS_DOMAIN),   
          "title_taxonomies" => __("specify the taxonomies that this object is available in", MASTERPRESS_DOMAIN),   
          "title_fields" => __("control display of this field based on the value of another field in the set", MASTERPRESS_DOMAIN),   

          "radio_multisite" => __("Make avalable in:", MASTERPRESS_DOMAIN),

          "multisite_all" => __('<em class="all">All</em> sites in the network', MASTERPRESS_DOMAIN),   
          "multisite_allow" => __( '<em class="allow">Include</em> only in specific Sites'),
          "multisite_deny" => __( '<em class="deny">Exclude</em> from specific Sites'),
          "multisite_allow_note" => "",       
          "multisite_deny_note" => "",        

          "post_types_all" => __('<em class="all">All</em> Post Types', MASTERPRESS_DOMAIN),
          "post_types_none" => __('<em class="none">No</em> Post Types', MASTERPRESS_DOMAIN),
          "post_types_allow" => __('<em class="allow">Include</em> only in specific Post Types', MASTERPRESS_DOMAIN),
          "post_types_deny" => __('<em class="deny">Exclude</em> from specific Post Types', MASTERPRESS_DOMAIN),

          "templates_all" => __('<em class="all">All</em> Templates', MASTERPRESS_DOMAIN),
          "templates_allow" => __('<em class="allow">Include</em> only in specific Templates', MASTERPRESS_DOMAIN),
          "templates_deny" => __('<em class="deny">Exclude</em> from specific Templates', MASTERPRESS_DOMAIN),

          "roles_all" => __('<em class="all">All</em> User Roles', MASTERPRESS_DOMAIN),
          "roles_none" => __('<em class="none">No</em> User Roles', MASTERPRESS_DOMAIN),
          "roles_allow" => __('<em class="allow">Include</em> only in specific Roles', MASTERPRESS_DOMAIN),
          "roles_deny" => __('<em class="deny">Exclude</em> from specific Roles', MASTERPRESS_DOMAIN),
          
          "taxonomies_all" => __('<em class="all">All</em> Taxonomies', MASTERPRESS_DOMAIN),
          "taxonomies_none" => __('<em class="none">No</em> Taxonomies', MASTERPRESS_DOMAIN),
          "taxonomies_allow" => __('<em class="allow">Include</em> only in specific Taxonomies', MASTERPRESS_DOMAIN),
          "taxonomies_deny" => __('<em class="deny">Exclude</em> from specific Taxonomies', MASTERPRESS_DOMAIN),
          
          "fields_all" => __('No conditions', MASTERPRESS_DOMAIN),
          "fields_allow" => __('When another field <em class="allow">has a specific value</em>', MASTERPRESS_DOMAIN),
          "fields_deny" => __('When another field <em class="deny">does not have a specific value</em>', MASTERPRESS_DOMAIN)

      )
    );
    
    $r["supports"] = wp_parse_args($args["supports"], array());
    
    $r["defaults"] = wp_parse_args(
      $args["defaults"], 
      array(
        "multisite"   => "all",
        "post_types"  => "none",
        "templates"   => "all",
        "roles"       => "none",
        "taxonomies"  => "none",
        "fields"      => "all"
      )
    );

    
    if (isset($args["sites"])) {
      $r["sites"] = $args["sites"];
    }
    
    if (isset($args["templates"])) {
      $r["templates"] = $args["templates"];
    }
    
    if (isset($args["post_types"])) {
      $r["post_types"] = $args["post_types"];
    }
    
    if (isset($args["taxonomies"])) {
      $r["taxonomies"] = $args["taxonomies"];
    }
  
    if (isset($args["roles"])) {
      $r["roles"] = $args["roles"];
    }
    
    if (isset($args["fields"])) {
      $r["fields"] = $args["fields"];
    }
    
    if (is_array($r["sites"])) {
      $r["sites"] = implode(",", $r["sites"]);
    }

    if (is_array($r["templates"])) {
      //$r["templates"] = implode(",", $r["templates"]);
    }

    $supports = $r["supports"];
    $labels = $r["labels"];
    
    if ($r["supports"] && count($r["supports"])) :
  ?>
  
    <div class="fs fs-visibility clearfix">
      
      <div class="fst">
      <div class="fstb">
        <h3><i class="light-globe"></i><strong><?php _e("Availability", MASTERPRESS_DOMAIN) ?></strong> - <?php echo $labels["title"] ?></h3>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb clearfix">

        <?php if (in_array("fields", $supports)) : ?>
        
        <?php $mode = self::visibility_mode($model->visibility, "fields", $r["defaults"]["fields"]); ?>
        
        <?php
        
        $fo = array("");
        $fo_attr = array(array());
        
        foreach ($r["fields"] as $field) {

          if ($type_class = MPFT::type_class($field->type)) {
            $fo[$field->display_label()] = $field->name;
            $fo_attr[] = array("class" => "mp-icon field-type-".$field->type);
          } 

        }
        
        $fields_select = WOOF_HTML::select(array("style" => "max-width: 280px", "id" => "visibility-fields", "name" => "visibility_fields", "class" => "with-icons select2-source", "data-placeholder" => __("-- Select a Field --", MASTERPRESS_DOMAIN)), $fo, "", $fo_attr);

        ?>

        <div class="fsg fsg-fields <?php echo $first ? "" : "divider" ?>">
              
          <h4><i class="question-octagon"></i><?php _e("Conditional Display", MASTERPRESS_DOMAIN) ?><span>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $labels["title_fields"] ?></span></h4>

          <div class="f clearfix">
            
            <div class="fsg-radios">
              
            <div class="fw fwl fw-all">
              <input id="visibility-type-fields-all" name="visibility_type[fields]" type="radio" value="all" <?php echo WOOF_HTML::checked_attr( $mode == "all" ) ?> class="radio" />
              <label for="visibility-type-fields-all" class="radio"><?php echo $labels["fields_all"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl fw-none">
              <input id="visibility-type-fields-allow" name="visibility_type[fields]" type="radio" value="none" <?php echo WOOF_HTML::checked_attr( $mode == "allow" ) ?> class="radio" />
              <label for="visibility-type-fields-allow" class="radio"><?php echo $labels["fields_allow"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl fw-none">
              <input id="visibility-type-fields-deny" name="visibility_type[fields]" type="radio" value="none" <?php echo WOOF_HTML::checked_attr( $mode == "deny" ) ?> class="radio" />
              <label for="visibility-type-fields-deny" class="radio"><?php echo $labels["fields_deny"] ?></label>
            </div>
            <!-- /.fw -->

            </div>
            <!-- /.fsg-radios -->
          
            <div id="visibility-fields-options">
            
              <div id="fw-field" class="fw">
                <label for="visibility-fields" class="select2"><?php _e("Field:", MASTERPRESS_DOMAIN) ?></label>
                <?php echo $fields_select ?>
              </div>

              <div id="fw-value" class="fw">
                <label for="visibility-fields-value"><?php _e("Value:", MASTERPRESS_DOMAIN) ?></label>
                <textarea name="visibility_fields_value" class="mono"></textarea>
              </div> 
               
            
            </div>
            
          </div>
          <!-- /.f -->
        
        </div>
        <!-- /.fsg -->
        
        
        
        
        <?php $first = false; ?>
        
        <?php endif; ?>
        
        <?php if (in_array("post_types", $supports)) : ?>

          <?php
          
          if (is_null($r["post_types"])) {
            $available_post_types = MPM_PostType::find(array("orderby" => "name ASC"));
          } else {
            $available_post_types = $r["post_types"];
          }
          
          $mode = self::visibility_mode($model->visibility, "post_types", $r["defaults"]["post_types"]);
          
          ?>
          
          <div class="fsg fsg-post-types <?php echo $first ? "" : "divider" ?>">
              
          <h4><i class="pins"></i><?php _e("Post Types", MASTERPRESS_DOMAIN) ?><span>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $labels["title_post_types"] ?></span></h4>

          <div class="f clearfix">
            
            <div class="fsg-radios">
              
            <div class="fw fwl fw-all">
              <input id="visibility-type-post-types-all" name="visibility_type[post_types]" type="radio" value="all" <?php echo WOOF_HTML::checked_attr( $mode == "all" ) ?> class="radio" />
              <label for="visibility-type-post-types-all" class="radio"><?php echo $labels["post_types_all"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl fw-none">
              <input id="visibility-type-post-types-none" name="visibility_type[post_types]" type="radio" value="none" <?php echo WOOF_HTML::checked_attr( $mode == "none" ) ?> class="radio" />
              <label for="visibility-type-post-types-none" class="radio"><?php echo $labels["post_types_none"] ?></label>
            </div>
            <!-- /.fw -->
            
            <?php if (count($available_post_types)) : ?>
        

            <div class="fw fwl">
              <input id="visibility-type-post-types-allow" name="visibility_type[post_types]" type="radio" value="allow" <?php echo WOOF_HTML::checked_attr( $mode == "allow" ) ?> class="radio" />
              <label for="visibility-type-post-types-allow" class="radio"><?php echo $labels["post_types_allow"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl">
              <input id="visibility-type-post-types-deny" name="visibility_type[post_types]" type="radio" value="deny" <?php echo WOOF_HTML::checked_attr( $mode == "deny"  ) ?> class="radio" />
              <label for="visibility-type-post-types-deny" class="radio"><?php echo $labels["post_types_deny"] ?></label>
            </div>
            <!-- /.fw -->

            </div>
            <!-- /.fsg-radios -->

            
            <div id="visibility-post-types-list" class="list checkbox-list">
          
              
              <div class="items">
              <?php foreach ($available_post_types as $post_type) : $disabled = $post_type->disabled ? ' disabled' : ''; $disabled_title = $post_type->disabled ? __("This post type is disabled", MASTERPRESS_DOMAIN) : ''; $builtin = $post_type->_builtin ? '&nbsp;'.__('(Built-in)', MASTERPRESS_DOMAIN) : ''; ?>
              <?php
      
                $linked_to_post_type = false;

                // todo, update this code to use visibility

                if (MPC::is_edit()) {
                  $linked_to_post_type = $model->linked_to_post_type($post_type);
                }

                $checked = $linked_to_post_type;


                if ($mode == "deny") {
                  $checked = !$checked;
                } 
                
              ?>
            
              <?php if ($post_type->still_registered()) : ?>
            
              <div class="fw">
                <input id="visibility-post-types-<?php echo $post_type->name ?>" name="visibility_post_types[]" value="<?php echo $post_type->name ?>" type="checkbox" <?php echo WOOF_HTML::checked_attr( $checked || MPV::in_post_array("post_types", $post_type->name) ) ?> class="checkbox" />
                <label for="visibility-post-types-<?php echo $post_type->name ?>" class="checkbox <?php echo $disabled ?>" title="<?php echo $disabled_title ?>"><?php echo $post_type->labels["name"] ?><span><?php echo $builtin ?></span></label>
              </div>
              <!-- /.fw -->
              
              <?php endif; ?>
            
              <?php endforeach; ?>
      
              </div>
              <!-- /.items -->
              

              <div class="controls">
                <button type="button" class="button list-select-all"><?php _e("Select All", MASTERPRESS_DOMAIN) ?></button>
                <button type="button" class="button list-select-none"><?php _e("Select None", MASTERPRESS_DOMAIN) ?></button>
              </div>
              <!-- /.controls -->
      
          
            </div>
            <!-- /#visibility-post-types -->
              
            <?php endif; ?>
          
          </div>
          <!-- /.f -->
          
          </div>
          <!-- /#fsg-post-types --> 
                  
        <?php $first = false; ?>
          
        
        <?php endif; // supports post_types ?>
        
        
        <?php if (in_array("templates", $supports)) : ?>
          
          <?php
          
          if (is_null($r["templates"])) {
            $available_templates = array_merge( array("Page" => "page.php"), get_page_templates() ); 
          } else {
            $available_templates = $r["templates"];
          }
          
          $mode = self::visibility_mode($model->visibility, "templates", $r["defaults"]["templates"]);
          
          ?>
          
          <div class="fsg fsg-templates <?php echo $first ? "" : "divider" ?>">
              
          <h4><i class="template"></i><?php _e("Templates", MASTERPRESS_DOMAIN) ?><span>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $labels["title_templates"] ?></span></h4>

          <div class="f clearfix">

            <div class="fsg-radios">

            <div class="fw fwl fw-all">
              <input id="visibility-type-templates-all" name="visibility_type[templates]" type="radio" value="all" <?php echo WOOF_HTML::checked_attr( $mode == "all" ) ?> class="radio" />
              <label for="visibility-type-templates-all" class="radio"><?php echo $labels["templates_all"] ?></label>
            </div>
            <!-- /.fw -->
            
            <div class="fw fwl">
              <input id="visibility-type-templates-allow" name="visibility_type[templates]" type="radio" value="allow" <?php echo WOOF_HTML::checked_attr( $mode == "allow" ) ?> class="radio" />
              <label for="visibility-type-templates-allow" class="radio"><?php echo $labels["templates_allow"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl">
              <input id="visibility-type-templates-deny" name="visibility_type[templates]" type="radio" value="deny" <?php echo WOOF_HTML::checked_attr( $mode == "deny" ) ?> class="radio" />
              <label for="visibility-type-templates-deny" class="radio"><?php echo $labels["templates_deny"] ?></label>
            </div>
            <!-- /.fw -->

            </div>
            <!-- /.fsg-radios -->
            
            <div id="visibility-templates-list" class="list checkbox-list">
          
              <?php if (count($available_templates)) : ?>

              <div class="items">
                
              <?php
              $templates = $model->vis("templates");
              $not_templates = $model->vis("not_templates");
              ?>
                
              <?php foreach ($available_templates as $name => $file) : ?>
                
              <?php 
                

              if ($mode == "deny") {
                $checked = MPV::in_csv($file, $not_templates);
              } else {
                $checked = MPV::in_csv($file, $templates);
              }
              
              ?>
              
              <div class="fw">
                <input id="templates_<?php echo WOOF_Inflector::underscore($file) ?>" name="visibility_templates[]" value="<?php echo $file ?>" type="checkbox" <?php echo WOOF_HTML::checked_attr( $checked ) ?> class="checkbox" />
                <label for="templates_<?php echo WOOF_Inflector::underscore($file) ?>" class="checkbox"><span class="tt"><?php echo $file ?></span></label>
              </div>
              <?php endforeach; ?>
      
              </div>
              <!-- /.items -->
              

              <div class="controls">
                <button type="button" class="button list-select-all"><?php _e("Select All", MASTERPRESS_DOMAIN) ?></button>
                <button type="button" class="button list-select-none"><?php _e("Select None", MASTERPRESS_DOMAIN) ?></button>
              </div>
              <!-- /.controls -->
              
              <?php else: ?>
            
              <span class="soft-warning"><i class="warning-triangle"></i><?php sprintf( __("The Field Set <em>%s</em> is not yet associated with <em>any</em> field types, so this option is not yet available", MASTERPRESS_DOMAIN), $parent->display_label() ); ?></span>
            
              <?php endif; ?>
          
            </div>
            <!-- /#visibility-templates -->
          
          </div>
          <!-- /.f -->
          
          </div>
          <!-- /#fsg-templates --> 
                  
        <?php $first = false; ?>
        <?php endif; // supports templates ?>
        
        
        <?php if (in_array("taxonomies", $supports)) : ?>

          <?php
          
          if (is_null($r["taxonomies"])) {
            $available_taxonomies = MPM_Taxonomy::find();
          } else {
            $available_taxonomies = $r["taxonomies"];
          }
          
          $mode = self::visibility_mode($model->visibility, "taxonomies", $r["defaults"]["taxonomies"]);
          
          ?>
          
          <div class="fsg fsg-taxonomies <?php echo $first ? "" : "divider" ?>">
              
          <h4><i class="tags"></i><?php _e("Taxonomies", MASTERPRESS_DOMAIN) ?><span>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $labels["title_taxonomies"] ?></span></h4>

          <div class="f clearfix">

            <div class="fsg-radios">

            <div class="fw fwl fw-all">
              <input id="visibility-type-taxonomies-all" name="visibility_type[taxonomies]" type="radio" value="all" <?php echo WOOF_HTML::checked_attr( $mode == "all" ) ?> class="radio" />
              <label for="visibility-type-taxonomies-all" class="radio"><?php echo $labels["taxonomies_all"] ?></label>
            </div>
            <!-- /.fw -->
            
            <div class="fw fwl fw-all">
              <input id="visibility-type-taxonomies-none" name="visibility_type[taxonomies]" type="radio" value="none" <?php echo WOOF_HTML::checked_attr( $mode == "none" ) ?> class="radio" />
              <label for="visibility-type-taxonomies-none" class="radio"><?php echo $labels["taxonomies_none"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl">
              <input id="visibility-type-taxonomies-allow" name="visibility_type[taxonomies]" type="radio" value="allow" <?php echo WOOF_HTML::checked_attr( $mode == "allow" ) ?> class="radio" />
              <label for="visibility-type-taxonomies-allow" class="radio"><?php echo $labels["taxonomies_allow"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl">
              <input id="visibility-type-taxonomies-deny" name="visibility_type[taxonomies]" type="radio" value="deny" <?php echo WOOF_HTML::checked_attr( $mode == "deny" ) ?> class="radio" />
              <label for="visibility-type-taxonomies-deny" class="radio"><?php echo $labels["taxonomies_deny"] ?></label>
            </div>
            <!-- /.fw -->

            </div>
            <!-- /.fsg-radios -->
            
            <div id="visibility-taxonomies-list" class="list checkbox-list">

              <?php if (count($available_taxonomies)) : ?>

              <?php
              
              $taxonomies = $model->vis("taxonomies");
              $not_taxonomies = $model->vis("not_taxonomies");
              
              ?>
              
              <div class="items">

              <?php foreach ($available_taxonomies as $tax) : ?>
                
              <?php 
              
              $tax_name = $tax->name;
              
              if ($mode == "deny") {
                $checked = MPV::in_csv($tax_name, $not_taxonomies);
              } else {
                $checked = MPV::in_csv($tax_name, $taxonomies);
              }
              
              ?>
              
              <?php if ($tax->still_registered()) : ?>

              <div class="fw">
                <input id="taxonomies_<?php echo WOOF_Inflector::underscore($tax_name) ?>" name="visibility_taxonomies[]" value="<?php echo $tax_name ?>" type="checkbox" <?php echo WOOF_HTML::checked_attr( $checked ) ?> class="checkbox" />
                <label for="taxonomies_<?php echo WOOF_Inflector::underscore($tax_name) ?>" class="checkbox"><span class="tt"><?php echo $tax->display_label() ?></span></label>
              </div>

              <?php endif; ?>
              
              <?php endforeach; ?>
          
              </div>
              <!-- /.items -->
              

              <div class="controls">
                <button type="button" class="button list-select-all"><?php _e("Select All", MASTERPRESS_DOMAIN) ?></button>
                <button type="button" class="button list-select-none"><?php _e("Select None", MASTERPRESS_DOMAIN) ?></button>
              </div>
              <!-- /.controls -->
                
              <?php endif; ?>
          
            </div>
            <!-- /#visibility-taxonomies -->
          
          </div>
          <!-- /.f -->
          
          </div>
          <!-- /#fsg-taxonomies --> 
                  
        <?php $first = false; ?>
        <?php endif; // supports taxonomies ?>
        
        
        <?php if (in_array("roles", $supports)) : ?>

          <?php
          
          if (is_null($r["roles"])) {
            $available_roles = $wf->roles(); 
          } else {
            $available_roles = $r["roles"];
          }
          
          $mode = self::visibility_mode($model->visibility, "roles", $r["defaults"]["roles"]);
          
          ?>
          
          <div class="fsg fsg-roles <?php echo $first ? "" : "divider" ?>">
              
          <h4><i class="user-role"></i><?php _e("User Profiles", MASTERPRESS_DOMAIN) ?><span>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $labels["title_roles"] ?></span></h4>

          <div class="f clearfix">

            <div class="fsg-radios">

            <div class="fw fwl fw-all">
              <input id="visibility-type-roles-all" name="visibility_type[roles]" type="radio" value="all" <?php echo WOOF_HTML::checked_attr( $mode == "all" ) ?> class="radio" />
              <label for="visibility-type-roles-all" class="radio"><?php echo $labels["roles_all"] ?></label>
            </div>
            <!-- /.fw -->
            
            <div class="fw fwl fw-none">
              <input id="visibility-type-roles-none" name="visibility_type[roles]" type="radio" value="none" <?php echo WOOF_HTML::checked_attr( $mode == "none" ) ?> class="radio" />
              <label for="visibility-type-roles-none" class="radio"><?php echo $labels["roles_none"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl">
              <input id="visibility-type-roles-allow" name="visibility_type[roles]" type="radio" value="allow" <?php echo WOOF_HTML::checked_attr( $mode == "allow" ) ?> class="radio" />
              <label for="visibility-type-roles-allow" class="radio"><?php echo $labels["roles_allow"] ?></label>
            </div>
            <!-- /.fw -->

            <div class="fw fwl">
              <input id="visibility-type-roles-deny" name="visibility_type[roles]" type="radio" value="deny" <?php echo WOOF_HTML::checked_attr( $mode == "deny" ) ?> class="radio" />
              <label for="visibility-type-roles-deny" class="radio"><?php echo $labels["roles_deny"] ?></label>
            </div>
            <!-- /.fw -->

            </div>
            <!-- /.fsg-radios -->
            
            <div id="visibility-roles-list" class="list checkbox-list">
            
              <div class="items">

              <?php if (count($available_roles)) : ?>
                
              <?php
              
              $roles = $model->vis("roles");
              $not_roles = $model->vis("not_roles");
              
              ?>
              
              <?php foreach ($available_roles as $role) : ?>
                
              <?php 
              
              $role_name = $role->id();
              
              if ($mode == "deny") {
                $checked = MPV::in_csv($role_name, $not_roles);
              } else {
                $checked = MPV::in_csv($role_name, $roles);
              }
              
              ?>
              
              <div class="fw">
                <input id="roles_<?php echo WOOF_Inflector::underscore($role_name) ?>" name="visibility_roles[]" value="<?php echo $role_name ?>" type="checkbox" <?php echo WOOF_HTML::checked_attr( $checked ) ?> class="checkbox" />
                <label for="roles_<?php echo WOOF_Inflector::underscore($role_name) ?>" class="checkbox"><span class="tt"><?php echo $role_name ?></span></label>
              </div>
              <?php endforeach; ?>
            
              </div>
              <!-- /.items -->
              

              <div class="controls">
                <button type="button" class="button list-select-all"><?php _e("Select All", MASTERPRESS_DOMAIN) ?></button>
                <button type="button" class="button list-select-none"><?php _e("Select None", MASTERPRESS_DOMAIN) ?></button>
              </div>
              <!-- /.controls -->
            
              <?php endif; ?>
          
            </div>
            <!-- /#visibility-roles -->
          
          </div>
          <!-- /.f -->
          
          </div>
          <!-- /#fsg-roles --> 
                  
        <?php $first = false; ?>
        <?php endif; // supports roles ?>
        
        
        
        
        <?php if (in_array("multisite", $supports) && is_multisite() && MASTERPRESS_MULTISITE_SHARING ) : ?>
          
          <?php $sites = $model->vis("sites"); ?>
          <?php $not_sites = $model->vis("not_sites"); ?>
          
          <?php 
        
            $mode = self::visibility_mode($model->visibility, "sites", $r["defaults"]["multisite"]);
    
            global $blog_id;
        
            $sites_args = array("public_only" => false);
          
            if (!is_null($r["sites"])) {
              $sites_args["include_id"] = $r["sites"];
            }
          
            $sites_options = array();
            $sites_options_attr = array();
          
            $the_site = $wf->site();
          
          
            // build the sites options
            foreach ($wf->sites( $sites_args ) as $site) {
            
              $attr = array();
            
              if ($site->id() == $the_site->id()) {
                $attr["class"] = "hl";
                $attr["title"] = __("This is the current site", MASTERPRESS_DOMAIN);
              }
            
              $sites_options_attr[] = $attr;
            
              $sites_options[$site->full_path()] = $site->id();
            }
      
          ?>
            
          <div class="fsg fsg-multisite <?php echo $first ? "" : "divider" ?>">
                
          <h4><i class="globe"></i><?php _e("Multi-site", MASTERPRESS_DOMAIN) ?><span>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $labels["title_multisite"] ?></span></h4>

          <div class="f clearfix">

            <div class="fsg-radios">

            <div class="fw fw-all">
              <input id="visibility-type-sites-all" name="visibility_type[sites]" type="radio" value="all" <?php echo WOOF_HTML::checked_attr( $mode == "all" ) ?> class="radio" />
              <label for="visibility-type-sites-all" class="radio"><?php echo $labels["multisite_all"] ?></label>
            </div>
            
            <div class="fw">
              <input id="visibility-type-sites-allow" name="visibility_type[sites]" type="radio" value="allow" <?php echo WOOF_HTML::checked_attr( $mode == "allow" ) ?> class="radio" />
              <label for="visibility-type-sites-allow" class="radio"><?php echo $labels["multisite_allow"] ?></label>
            </div>
            
            <div class="fw">
              <input id="visibility-type-sites-deny" name="visibility_type[sites]" type="radio" value="deny" <?php echo WOOF_HTML::checked_attr( $mode == "deny" ) ?> class="radio" />
              <label for="visibility-type-sites-deny" class="radio"><?php echo $labels["multisite_deny"] ?></label>
            </div>
            
            </div>
            <!-- /.fsg-radios -->
            
            <div id="visibility-sites-list" class="sites-list">
          
            <?php 
          
              if ($mode == "allow") {
                $selected_sites = explode(",", $sites);
              } else if ($mode == "deny") {
                $selected_sites = explode(",", $not_sites);
              } else {
                $selected_site_objects = $wf->sites(array("public_only" => false));
                $selected_sites = $selected_site_objects->extract("id");
              }

            
              echo WOOF_HTML::select( 
                array("multiple" => "multiple", "id" => "visibility-sites", "name" => "visibility_sites[]"),
                $sites_options,
                $selected_sites,
                $sites_options_attr
              );

            ?>

            <?php if ($labels["multisite_allow_note"] != "") : ?>
            <p class="note"><?php echo $labels["multisite_allow_note"] ?></p>  
            <?php endif; ?>

            </div>
            <!-- /#visibility-sites-list -->

          
          </div>
          <!-- /.f -->
        
          </div>
          <!-- /#fsg-multisite --> 
          
        <?php $first = false; ?>
        <?php endif; // supports multisite ?>
        
        
        

      
      </div>
      </div>

    </div>
    <!-- /.fs -->
    
  <?php
    endif; // count support
  } 
  

  
  public static function icon_select($val, $id, $name, $uploader_id) {
    global $wf;
    
    // check if the value is a library-derived icon
    
    $is_lib = false;
    
    if (preg_match("/lib-(.+)--(.+)/", $val, $matches)) {
      $val = $matches[1].WOOF_DIR_SEP.$matches[2];
      $is_lib = true;
    }

    ?>
    <style type="text/css">
    <?php
  
    //if ($is_lib) {
  //    echo '#'.$uploader_id.' { display: none; } ';
    //}
  
    $iterator = new DirectoryIterator(MASTERPRESS_EXTENSIONS_ICONS_DIR);
    
    foreach ($iterator as $file) {
      
      $file_name = $file->getFileName();
      
      if (substr($file_name, 0, 1) == ".") {
        continue;
      }

      if ($file->isDir()) {

        $icon_select = true;
        
        $sub_iterator = new DirectoryIterator(MASTERPRESS_EXTENSIONS_ICONS_DIR.WOOF_DIR_SEP.$file_name);
        
        foreach ($sub_iterator as $sub) {
          
          $sub_file_name = $sub->getFileName();
          
          $pi = pathinfo($sub_file_name);
          
          $sub_ext = $pi["extension"];
          $sub_base_name = $pi["filename"];

          if (substr($sub_file_name, 0, 1) == ".") {
            continue;
          }

          $url = $wf->root_relative_url(MASTERPRESS_EXTENSIONS_ICONS_URL."/".$file_name."/".$sub_file_name);

          echo ".icon-".sanitize_title_with_dashes($file_name."-".$sub_file_name)." { background-image: url(".$url."); }"; 
        }
      }
    }


    ?>
    </style>
    
    <?php if (isset($icon_select)) : ?>
    
    <div class="icon-select-wrap">  
    <select id="<?php echo $id ?>" name="<?php echo $name ?>" data-uploader="<?php echo $uploader_id ?>" data-no_icon="<?php echo MPU::img_url("icon-no-icon.png") ?>" data-base="<?php echo MASTERPRESS_EXTENSIONS_ICONS_URL ?>" data-placeholder="<?php _e("-- OR select an icon --", MASTERPRESS_DOMAIN) ?>" class="select2-source icon-select with-icons">
    <option value=""></option>
    <?php

      foreach (new DirectoryIterator(MASTERPRESS_EXTENSIONS_ICONS_DIR) as $file) {

        $file_name = $file->getFileName();

        if (substr($file_name, 0, 1) == ".") {
          continue;
        }

        if ($file->isDir()) {
        ?>
        <optgroup label="<?php echo WOOF_Inflector::humanize($file_name) ?>">
        <?php

          foreach (new DirectoryIterator(MASTERPRESS_EXTENSIONS_ICONS_DIR.WOOF_DIR_SEP.$file_name) as $sub) {

            $sub_file_name = $sub->getFileName();

            $pi = pathinfo($sub_file_name);
          
            $sub_ext = $pi["extension"];
            $sub_base_name = $pi["filename"];

            $path = $file_name.WOOF_DIR_SEP.$sub_file_name;

            if (substr($sub_file_name, 0, 1) == ".") {
              continue;
            }

            $selected = WOOF_HTML::selected_attr($val == $path);

            ?>
            <option value="<?php echo $path ?>" <?php echo $selected ?> data-icon="mp-icon icon-<?php echo sanitize_title_with_dashes($file_name."-".$sub_file_name) ?>" class=""><?php echo WOOF_Inflector::titleize($sub_base_name) ?></option>
            <?php
          }
        }
        ?>

        </optgroup>

        <?php
      }

    ?>

    </select>
    </div>
  
    <?php
    endif; 
    
  }
       
  
  public static function messages($show = "all") {
    ?>
    <ul class="mp-messages">
    
    <?php
    
    do_action("mp_pre_messages");

    if (count(array_merge(self::$notifications, self::$successes, self::$errors, self::$warnings) > 0)) {
      
      if ($show == "all") {
        $show = array("errors", "notifications", "successes", "warnings");
      }
    
      if (!is_array($show)) {
        $show = explode(",", $show);
      }
    
      ?>
      
      <?php
    
      if (in_array("successes", $show)) {

        foreach (MPV::$successes as $msg) {
          ?>
          <li class="success"><i class="tick-circle"></i><?php echo $msg ?></li>
          <?php
        }
      
      }
      
      if (in_array("errors", $show)) {

        foreach (MPV::$errors as $msg) {
          ?>
          <li class="error"><i class="error-circle"></i><?php echo $msg ?></li>
          <?php
        }
      
      }
      
      if (in_array("warnings", $show)) {
    
        foreach (MPV::$warnings as $msg) {
          ?>
          <li class="warning"><i class="warning-octagon"></i><?php echo $msg ?></li>
          <?php
        }
      
      }

      if (in_array("notifications", $show)) {
    
        foreach (MPV::$notifications as $msg) {
          ?>
          <li class="notification"><?php echo $msg ?></li>
          <?php
        }
      
      }

      ?>
      <?php
      
      do_action("mp_messages");
      
    }
    
    ?>
      </ul>
    <?php
  }
  
  public function key() {
    return MPU::dasherize( str_replace("MPV_", "", get_class($this) ) );
  }
  
  public function is_postback() {
    return isset($_POST["postback"]);
  }


  public function form_open($action = null) {
    
    if (is_null($action)) {
      $action = MasterPress::$action;
    }
    
    ?>
    <form autocomplete="off" action="<?php echo MPV::form_action(array(), true, $action) ?>" method="post" class="mpv-<?php echo $this->key() ?>-form mpv-<?php echo $this->key() ?>-<?php echo MPC::action_type() ?>">
    <?php 
    wp_nonce_field( $action );
    MPV::id_form_field();
    MPV::parent_form_field();
    MPV::gparent_form_field();
    MPV::postback_form_field();
    MPV::return_form_field();
    MPV::redirect_form_field();
  }

  public function form_buttons($action) {
    
    $actions = array();
    
    if (MPC::is_edit($action)) {
      $actions = array("update");
    } else if (MPC::is_create($action)) {
      $actions = array("save");
    }
    
    return $actions;
  }
  
  public function form_close() {
    $buttons = $this->form_buttons(MasterPress::$action);
    
    ?>
    <div class="form-footer">
    <?php $this->buttons( MasterPress::$controller_key, $buttons); ?>
    </div>
    <!-- /.form-footer -->
    </form>
    <?php
  }
  

  
  public function title() {

    $defaults = array(
      "text" => "",
      "actions" => array(),
      "info_panel" => false,
      "controller" => MasterPress::$controller_key
    );
    
    $r = wp_parse_args( $this->title_args, $defaults );
    
    $actions = $r["actions"];
  
    if (!is_array($actions)) {
      $actions = explode(",", $actions);
    }
    
    ?>
    <div class="mpv-title<?php echo $r["info_panel"] ? " mpv-title-with-info-panel " : "" ?>">

      <div class="icon32"><br /></div>
      <h3><?php echo $r["text"] ?></h3>

      <?php $this->buttons($r["controller"], $actions, true);
    ?>
    </div>
    
    <h2 style="display: none;"></h2>
    <?php
  
  }


  public function buttons($controller, $actions = array(), $title = false) {
    
    $class = get_class($this);
      
    if (count($actions)) { ?>
      <ul class="buttons buttons-<?php echo MasterPress::$action ?>">

      <?php
      foreach ($actions as $action) { 
        
        switch ($action) {
          case "create" :
            if ($title) {
              echo '<li class="create">'. MPV::title_create_button( $controller ).'</li>'; 
            } else {
              echo '<li class="create">'. MPV::create_button( $controller, call_user_func( array( $class, "__s" ) ) ).'</li>'; 
              
            }
          
            break;
          case "edit" :
            echo '<li class="edit">'. MPV::edit_button( $controller, call_user_func( array( $class, "__s" ) ) ).'</li>';
            break;
          case "save" :
            if ($title) {
              echo '<li class="save">'. MPV::title_save_button( $controller ).'</li>';
            } else {
              echo '<li class="save">'. MPV::save_button( $controller, call_user_func( array( $class, "__s" ) ) ).'</li>';
            }
            break;
          case "update" :
            if ($title) {
              echo '<li class="update">'. MPV::title_update_button( $controller ).'</li>';
            } else {
              echo '<li class="update">'. MPV::update_button( $controller, call_user_func( array( $class, "__s" ) ) ).'</li>';
            }
            
            break;
          default: 
            echo '<li>'.$action.'</li>';
        }
        ?><?php
      }
      ?>
      </ul>
      <?php
    } 
  }
  
  /* -- Form Helpers -- */


  public static function id_form_field() {
    if (isset($_REQUEST["id"])) {
    ?><input type="hidden" id="mp-id" name="id" value="<?php echo $_REQUEST["id"] ?>" /><?php 
    }
  }

  public static function parent_form_field() {
    if (isset($_REQUEST["parent"])) {
    ?><input type="hidden" id="mp-parent" name="parent" value="<?php echo $_REQUEST["parent"] ?>" /><?php 
    }
  }

  public static function gparent_form_field() {
    if (isset($_REQUEST["gparent"]) && trim($_REQUEST["gparent"]) != "") {
    ?><input type="hidden" id="mp-gparent" name="gparent" value="<?php echo $_REQUEST["gparent"] ?>" /><?php 
    }
  }

  public static function return_form_field() {
    if (isset($_REQUEST["return"])) {
    ?><input type="hidden" id="mp-return" name="return" value="<?php echo $_REQUEST["return"] ?>" /><?php 
    }
  }

  public static function redirect_form_field() {
    if (isset($_REQUEST["mp_redirect"])) {
    ?><input type="hidden" id="mp-redirect" name="mp_redirect" value="<?php echo urldecode($_REQUEST["mp_redirect"]) ?>" /><?php 
    }
  }

  public static function postback_form_field() {
    ?><input type="hidden" name="postback" value="true" /><?php 
  }


  


  public static function in_csv($val, $csv) {
    return in_array($val, explode(",", $csv));
  }

  public static function in_post_array($name, $value) {
    if (isset($_POST[$name])) {
      if (is_array($_POST[$name]) && in_array($value, $_POST[$name])) {
        return true;
      }
    }
    
    return false;
  }



  public static function form_action( $qs = array(), $entities = true, $action = null ) {
    if (is_null($action)) {
      $action = MasterPress::$action;
    }
    
    return MasterPress::admin_url( MasterPress::$controller_key, $action, $qs, $entities );
  }
  
  public static function action_link( $controller, $action, $text, $qs = array(), $args = array(), $entities = true) {

    $def_args = array();
    $r = wp_parse_args($args, $def_args);

    $def_qs = array();
    $q = wp_parse_args($qs, $def_qs);
    
    $tag = '<a href="'.MasterPress::admin_url( $controller, $action, $qs, $entities ).'"';
    
    foreach ($r as $key => $value) {
      $tag .= ' '.$key.'="'.esc_attr($value).'" ';
    }
    
    if (!isset($r["title"])) {
      $tag .= ' title="'.strip_tags($text).'"';    
    }
    
    $tag .= ">";
    
    $tag .= $text.'</a>';
    
    return $tag;
  }


  public static function edit_button( $controller, $str ) {
    return self::action_button( $controller, "edit", MPV::__edit( $str ) );
  }

  public static function create_button( $controller, $str ) {

    $qs = array();
    
    if (isset(MasterPress::$parent)) {
      $qs["parent"] = MasterPress::$parent;
    }
    
    return self::action_button( $controller, "create", MPV::__create( $str ), $qs, array('class' => 'button-primary button-primary-create') );
  }

  public static function title_create_button( $controller, $str = "" ) {

    $qs = array();
    
    if (isset(MasterPress::$parent)) {
      $qs["parent"] = MasterPress::$parent;
    }
    
    return self::action_button( $controller, "create", MPV::__create( $str ), $qs, array('class' => 'add-new-h2') );
  }

  public static function title_save_button( $controller,  $str = "" ) {
    return '<button class="button button-primary" type="submit">' . MPV::__save( $str ) . '</button>';
  }

  public static function title_update_button( $controller, $str = "" ) {
    return '<button class="button button-primary" type="submit">' . MPV::__update( $str ) . '</button>';
  }
  
  public static function save_button( $controller,  $str = "" ) {
    return '<button class="button button-primary" type="submit">' . MPV::__save( $str ) . '</button>';
  }

  public static function update_button( $controller,  $str = "" ) {
    return '<button class="button button-primary" type="submit">' . MPV::__update( $str ) . '</button>';
  }
     
     
     
  public static function action_button( $controller, $action, $text, $qs = array(), $args = array()) { 
    $def_args = array( "class" => "button button-small" );
    $r = wp_parse_args($args, $def_args);
    return self::action_link( $controller, $action, $text, $qs, $r);
  }

  public static function action_button_primary( $controller, $action, $text, $qs = array(), $args = array()) { 
    $def_args = array( "class" => "button-primary" );
    $r = wp_parse_args($args, $def_args);
    return self::action_link( $controller, $action, $text, $qs, $r);
  }
  
      
  
  public static function tab_content($info) {
    
    return <<<HTML
    
    <div class="view-info">
      <p>
        {$info}
      </p>
    </div>
    
HTML;

  }
  
  public static function overview_tab($content) {
    return self::help_tab( __('Overview', MASTERPRESS_DOMAIN), $content );
  }

  public static function help_tab($title, $content) {
    return array(
      'id'	=> WOOF_Inflector::underscore($title),
      'title'	=> $title,
      'content'	=> self::tab_content( $content )
    );
  }
  
  
  
  /* -- i18n formatter functions -- */
  
  public static function __create($str = "") {
    return sprintf( __('<span class="create">Add New</span>%s', MASTERPRESS_DOMAIN), $str == "" ? "" : " $str" );
  }

  public static function __manage($str = "") {
    return sprintf( __('<span class="manage">Manage</span>%s', MASTERPRESS_DOMAIN), $str == "" ? "" : " $str" );
  }

  public static function __edit($str = "") {
    return sprintf( __('<span class="edit">Edit</span>%s', MASTERPRESS_DOMAIN), $str == "" ? "" : " $str");
  }

  public static function __delete($str = "") {
    return sprintf( __('<span class="delete">Delete</span>%s', MASTERPRESS_DOMAIN), $str == "" ? "" : " $str");
  }

  public static function __save($str = "") {
    return sprintf( __('Save%s', MASTERPRESS_DOMAIN), $str == "" ? "" : " $str");
  }

  public static function __update($str) {
    return sprintf( __('Update %s', MASTERPRESS_DOMAIN), $str);
  }

  public static function note($str) {
    return '<span class="note">'.$str.'</span>';
  }
  
  public static function note_none() {
    return self::note(__("( none )", MASTERPRESS_DOMAIN));
  }

  public static function updated_class($from, $id) {

    if (!is_array($from)) {
      $from = explode(",", $from);
    } 

    if ( ( MasterPress::$id == $id || ( isset($_GET["show"] ) && $_GET["show"] == $id ) ) && in_array(MasterPress::$from, $from)) {
      return ' updated ';
    }

    return '';
  }
  
  public function dump($obj, $label = "", $exit = false) {

    if ($label != "") {
      echo "<h2>$label</h2>";
    }

    echo "<pre>";
    print_r($obj);
    echo "</pre>";

    if ($exit) {
      exit();
    }
  }
  
  
}

?>