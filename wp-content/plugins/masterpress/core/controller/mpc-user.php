<?php

class MPC_User extends MPC {
  
  protected static $user;
  
  public static function inline_head() {
    global $wf;
    $user = self::get_user();
    MPV_Meta::inline_head(self::assigned_field_sets(), $user);
  }
  

  public static function assigned_field_sets() {
    global $meow_provider;

    if ($user = self::get_user()) { 
      return $meow_provider->role_field_sets($user->role()->name());
    }
    
    return array();
  }

  public static function assigned_field_types() {
    global $meow_provider;
    
    if ($user = self::get_user()) { 
      return $meow_provider->role_field_types($user->role->id());
    }
  
    return array();
  }
  
  public function get_user() {
    
    global $wf;
    
    if (!isset(self::$user)) {
    
      if (isset($_GET["user_id"])) {
        $user_id = $_GET["user_id"];
        self::$user = $wf->user($user_id);
      } else {
        self::$user = $wf->the_user();
      }

    }
    
    return self::$user;
      
  }
  
  public static function field_sets($t) {
    global $wf;
    $user = self::get_user();
    $sets = self::assigned_field_sets();

    ?>
    
    <tr class="meta-boxes">
      <td colspan="2">
      
      
      <?php foreach ($sets as $set) : ?>

      <?php if ($set->current_user_can_see()) : ?>

      <div id="field-set-<?php echo $set->html_id() ?>" class="postbox nodrag">
        <h3 class="hndle"><em><?php echo $set->display_label() ?></em>
        
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
        <?php MPV_Meta::set($user, $set); ?>
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