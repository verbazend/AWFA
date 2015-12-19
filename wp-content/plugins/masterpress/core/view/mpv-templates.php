<?php

class MPV_Templates extends MPV {

  public static function __s() {
    return __("Template", MASTERPRESS_DOMAIN);
  }

  public static function __p() {
    return __("Templates", MASTERPRESS_DOMAIN);
  }

  
  
  public function grid($id = null) {

    MPV::incl("field-sets");
    MPV::incl("fields");
    MPV::incl("taxonomies");
    MPC::incl("taxonomies");
    

    $has_actions = MasterPress::current_user_can("edit_templates,delete_templates,manage_template_field_sets");
    $can_edit = MasterPress::current_user_can("edit_templates");
    $can_delete = MasterPress::current_user_can("delete_templates");
    $can_create = MasterPress::current_user_can("create_templates");
    $can_manage_field_sets = MasterPress::current_user_can("manage_template_field_sets");

    $colspan = $has_actions ? 8 : 7;

    
  ?>


  <?php MPV::messages(); ?>
  
  <table cellspacing="0" class="grid grid-post-types">
    <thead>
    <tr>
      <th class="first file-name front-end-name"><i class="script-php"></i><span><?php _e("File Name", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="name"><i class="template"></i><span><?php _e("Template Name", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="field-sets"><i class="metaboxes"></i><span><?php _e("Field Sets", MASTERPRESS_DOMAIN) ?></span></th>
      <th class="post-types <?php echo $has_actions ? "" : "last" ?>"><i class="pins"></i><span><?php _e("Post Types", MASTERPRESS_DOMAIN) ?></span></th>
      <?php if ($has_actions) : ?>
      <th class="actions last"><i class="buttons"></i><span><?php _e("Actions", MASTERPRESS_DOMAIN) ?></span></th>
      <?php endif; ?>
    </tr>
    </thead>
    <tbody>
     
    <?php $count = 0; $disabled = ""; ?>
    
    <?php $templates = get_page_templates();  ?>
    
    <?php foreach ($templates as $template => $file) : $count++; $first = $count == 1 ? 'first' : ''; $model = MPM_Template::find_by_id($file);
    
    
    $editable_class = $can_edit ? " editable " : "";
    $meta = $can_edit ? "{ href: '".MasterPress::admin_url("templates", "edit", "id=".$file)."' }" : "";


    ?>
    
    <tr class="<?php echo $first ?> <?php echo $editable_class.$disabled ?> <?php echo MPV::updated_class("edit,create", $file) ?> <?php echo $count % 2 == 0 ? "even" : "" ?> sub <?php echo $meta ?>">
      <td class="first file-name"><strong><span class="tt"><?php echo $file ?></span></strong></td>
      <td class="name"><?php echo $template ?></td>
      <td class="field-sets <?php echo $can_manage_field_sets ? "manage" : "" ?>">
        <?php if ($can_manage_field_sets) : ?>
        <a href="<?php echo MasterPress::admin_url( "templates", "manage-field-sets", "parent=".$file)?>">
		<i class="go"></i>
        <?php endif; ?>
        
        <?php 
        
        $field_sets = MPM_TemplateFieldSet::find_by_template( $file ); 

        $field_set_display = MPV::note_none();
        
        if (count($field_sets)) {
          $field_set_links = array();
          
          foreach ($field_sets as $field_set) {
            if (!$field_set->is_shared()) {
              $field_set_links[] =  $field_set->display_label();
            }
          }

          if (count($field_set_links)) {
            $field_set_display = implode($field_set_links, ", ");
          }
        
        }
        
        echo $field_set_display;
        ?>
        <?php if ($can_manage_field_sets) : ?>
        </a>
        <?php endif; ?>

      </td>
      
      <td class="post-types <?php echo $has_actions ? "" : "last" ?>">
        
        <?php 
        
        $vis = $model->visibility;
        
        $vis_post_types = "";

        if (isset($vis["post_types"])) {
          $vis_post_types = $vis["post_types"];
        }
        
        if ($vis_post_types == "*") {

          $post_type_display = '<span class="inherit">( '.__("All", MASTERPRESS_DOMAIN).' )</span>';

        } else {
          
          if (is_null($vis_post_types) || $vis_post_types == "") {
          
            $post_type_display = __("( none )");
          
          } else {
            $post_types = $model->post_types(); 
          
            $post_type_display = MPV::note_none();
        
            if (count($post_types)) {
              $post_type_links = array();
          
              foreach ($post_types as $post_type) {
                $post_type_links[] = $post_type->labels["name"];
              }

              $post_type_display = implode($post_type_links, ", ");
            }
          }
        }
        
        echo $post_type_display;
          
        ?>
        
      </td>

      <?php if ($has_actions) : ?>

      <td class="actions last">
      <div>
        <?php if ($can_edit) : ?>
          <?php echo MPV::action_button("templates", "edit", self::__edit( ), "id=".$file, array("class" => "button button-edit")); ?>
        <?php endif; ?>

        <?php if ($can_manage_field_sets) : ?>
          <?php echo MPV::action_button("templates", "manage-field-sets", self::__manage( MPV_FieldSets::__p_short() ), "parent=".$file, array("class" => "button button-manage")); ?>
        <?php endif; ?>  
      </div>
      </td>

      <?php endif; // has_actions ?>

    </tr>
    
    <?php endforeach; ?>

    </tbody>
    </table>
    
    <?php
    
  } // end grid()
  
  
  public function form($type) {
    global $wf;
    $model = MasterPress::$model;
  ?>

    <?php MPV::messages(); ?>
  

    <div class="f">
      <label for="file-name" class="icon"><i class="script-php"></i><?php _e("File Name", MASTERPRESS_DOMAIN)?>:</label>
      <div class="fw">
        <input id="file-name" name="file_name" type="text" readonly="readonly" class="readonly text mono key" maxlength="20" value="<?php echo $model->id ?>" />
      </div>
    </div>
    <!-- /.f -->
    
    <div class="fs fs-supports">
    
      <div class="fst">
      <div class="fstb">
        <h3><i class="gear"></i><strong><?php _e("Supports", MASTERPRESS_DOMAIN) ?></strong> - <?php _e("controls the user interface for creating and editing posts based on this template", MASTERPRESS_DOMAIN) ?></h3>
        <div class="buttons">
          <button class="button button-small button-select-all" type="button"><?php _e('Select <strong class="all">All</strong>', MASTERPRESS_DOMAIN) ?></button>
          <button class="button button-small button-select-none" type="button"><?php _e('Select <strong class="none">None</strong>', MASTERPRESS_DOMAIN) ?></button>
        </div>
      </div>
      </div>
    
      <div class="fsc">
      <div class="fscb">
        
        <input id="supports_pb" name="supports_pb" type="hidden" value="true" />

        <div class="f f-supports_type">
          
          <?php
          
          $checked = WOOF_HTML::checked_attr($model->supports == "*");
          
          ?>
          
          <div class="fw">
            <input id="supports_type_inherit" name="supports_type" value="inherit" type="radio" <?php echo $checked ?> class="radio"  />
            <label for="supports_type_inherit" class="radio"><?php _e("Use the same features as the associated post type(s)", MASTERPRESS_DOMAIN) ?></label>
          </div>

          <?php
          
          $checked = WOOF_HTML::checked_attr($model->supports != "*");
          
          ?>

          <div class="fw">
            <input id="supports_type_custom" name="supports_type" value="custom" type="radio" <?php echo $checked ?> class="radio" />
            <label for="supports_type_custom" class="radio"><?php _e("Use custom features", MASTERPRESS_DOMAIN) ?></label>
          </div>
        </div>
        <!-- /.f -->
        
        
        <div id="fs-supports-custom">
          
          <div id="fs-supports-1">
          
            <div class="fw">
              <input id="supports_title" name="supports[]" value="title" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("title", $model->supports) ) ?> type="checkbox" class="checkbox { tags: ['title'] }" />
              <label for="supports_title" class="checkbox"><?php _e("Title", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show a text input to edit the post title", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_editor" name="supports[]" value="editor" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("editor", $model->supports) ) ?> type="checkbox" class="checkbox { tags: ['auto_excerpt'] }" />
              <label for="supports_editor" class="checkbox"><?php _e("Editor", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show the main content box", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_comments" name="supports[]" value="comments" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("comments", $model->supports) ) ?> type="checkbox" class="checkbox { tags: ['comment_count'] }" />
              <label for="supports_comments" class="checkbox"><?php _e("Comments", MASTERPRESS_DOMAIN); ?><span> - <?php _e("the <em>ability</em> to allow / disallow comments on posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_trackbacks" name="supports[]" value="trackbacks" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("trackbacks", $model->supports) ) ?> type="checkbox" class="checkbox { tags: ['trackback_count'] }" />
              <label for="supports_trackbacks" class="checkbox"><?php _e("Trackbacks", MASTERPRESS_DOMAIN); ?><span> - <?php _e("the <em>ability</em> to allow / disallow trackbacks to posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_post_formats" name="supports[]" value="post-formats" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("post-formats", $model->supports)) ?> type="checkbox" class="checkbox { tags: ['post_format'] }" />
              <label for="supports_post_formats" class="checkbox"><?php _e("Post Formats", MASTERPRESS_DOMAIN); ?><span> - <?php _e("used by themes to change the display of posts of this type", MASTERPRESS_DOMAIN); ?></span></label>
            </div>
            <!-- /.fw -->
          
          </div>
          <!-- /.fs-supports-1 -->


          <div id="fs-supports-2">

            <div class="fw">
              <input id="supports_revisions" name="supports[]" value="revisions" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("revisions", $model->supports)) ?> type="checkbox" class="checkbox" />
              <label for="supports_revisions" class="checkbox"><?php _e("Revisions", MASTERPRESS_DOMAIN); ?><span> - <?php _e("allow revisions for posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_author" name="supports[]" value="author" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("author", $model->supports)) ?> class="checkbox" />
              <label for="supports_author" class="checkbox"><?php _e("Author", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show a select box for changing the author", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_excerpt" name="supports[]" value="excerpt" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("excerpt", $model->supports)) ?> class="checkbox { tags: ['excerpt'] }" />
              <label for="supports_excerpt" class="checkbox"><?php _e("Excerpt", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show a text area for writing a custom excerpt", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_page_attributes" name="supports[]" value="page-attributes" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("page-attributes", $model->supports) ) ?> class="checkbox" />
              <label for="supports_page_attributes" class="checkbox"><?php _e("Type Attributes", MASTERPRESS_DOMAIN); ?><span> - <?php _e("Show the UI for editing the Template, Menu Order and Parent", MASTERPRESS_DOMAIN); ?></span></label>
            </div>
            <!-- /.fw -->

            <div class="fw">
              <input id="supports_thumbnail" name="supports[]" value="thumbnail" type="checkbox" <?php echo WOOF_HTML::checked_attr( MPV::in_csv("thumbnail", $model->supports) ) ?> class="checkbox { tags: ['thumbnail'] }" />
              <label for="supports_thumbnail" class="checkbox"><?php _e("Thumbnail", MASTERPRESS_DOMAIN); ?><span> - <?php _e("show the <em>standard</em> thumbnail upload for posts of this type", MASTERPRESS_DOMAIN) ?></span></label>
            </div>
            <!-- /.fw -->
          
          </div>
          <!-- /.fs-supports-2 -->
        
        </div>
        <!-- /#fs-supports-custom -->
          
        
      
      </div>
      </div>

    </div>
    <!-- /.fs -->

    <?php
    
    $args = array();

    $args["supports"] = array();
    
    $args["supports"] = array("multisite");
    $args["supports"][] = "post_types";

    $args["labels"] = array(
      "title" =>  __("control the visibility of this template in WordPress", MASTERPRESS_DOMAIN),   
      "title_multisite" =>  __("specify the sites in the multisite network that this template is available in", MASTERPRESS_DOMAIN),   
      "title_post_types" =>  __("specify the post types that this template can be used by", MASTERPRESS_DOMAIN)
    );

    $args["defaults"] = array(
      "post_types" => "page"
    );
    
        
    MPV::fs_visibility( $model, $args ); 
    
  } // end form
  
  
}