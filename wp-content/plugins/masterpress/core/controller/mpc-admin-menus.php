<?php

class MPC_AdminMenus extends MPC {

  public static $key = "admin-menus";
  
  public function manage_actions() {
    MPV::incl("page-links");
    MPV::incl("post-links");
    
    return array(
      MPV::action_button("admin-menus", "create-page-link", MPV::__create( MPV_PageLinks::__s() ) ),
      MPV::action_button("admin-menus", "create-post-link", MPV::__create( MPV_PostLinks::__s() ) )
    );
  }
  

  /* -- Post Link Actions -- */
  
  public function create_post_link() {
    $this->create("view=post-links");
  }

  public function edit_post_link($id) {
    $this->edit("view=post-links");
  }

  /* -- Page Link Actions -- */


  public function create_page_link() {
    $this->create("view=page-links");
  }

  public function edit_page_link($id) {
    $this->edit("view=page-links");
  }


}