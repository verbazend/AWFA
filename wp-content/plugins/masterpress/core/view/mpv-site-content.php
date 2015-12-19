<?php

class MPV_SiteContent extends MPV { 
  
  
  function manage() {
    
    global $wf;
    
    $info = MasterPress::$view;
    $site = $wf->site();

    if (isset($_GET["updated"])) {
      MPV::notify(__("Content Updated", MASTERPRESS_DOMAIN));
    }
    
    ?>
    
    <?php MPV::messages() ?>

    <?php MPV::form_open() ?>


    <div id="site-content">

    <button id="bt-save-changes-top" type="submit" class="simple-primary"><?php _e("Save Changes", MASTERPRESS_DOMAIN) ?></button>
      
    <?php foreach ($info->sets as $set) : ?>

    <?php if ($set->current_user_can_see()) : ?>

    <input type="hidden" name="mp_meta[__present]" value="1" />
      
    <div id="field-set-<?php echo $set->html_id() ?>" class="postbox nodrag">
      <h3 class="hndle"><em><?php echo $set->display_label() ?></em>
      <?php
      
      if (current_user_can("manage_options")) { ?>
        <a href="<?php echo $set->manage_url() ?>" class="mp-go" title="<?php _e("Manage Field Set") ?>">Manage</a>  
      <?php
      }
      
      ?>  
      </h3>
      <div class="inside">
      <?php MPV_Meta::set($site, $set); ?>
      </div>
    </div>
    
    <?php endif; ?>
  
    <?php endforeach; ?>
      
    </div>
  
    <button type="submit" class="button button-primary"><?php _e("Save Changes", MASTERPRESS_DOMAIN) ?></button>
    <?php MPV::form_close() ?>

    
    
    <?php
  }
}

?>