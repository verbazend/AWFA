<?php
/*
Plugin Name: Axcelerate Manager
Description: Axcelerate manager to manage online booking process.
Author: Verbazend
Plugin URI: http://www.vbz.com.au/
Author URI: http://www.vbz.com.au/
Version: 1.0.1
*/
        //echo "<h1>Hello world!</h1>";

        add_action('admin_menu', 'am_plugin_setup_menu');
 
function am_plugin_setup_menu(){
        add_menu_page( 'Axcelerate Manager', 'aXcelerate', 'manage_options', 'vbz-axcelerate-manager', 'am_init' ,'','2.5');
}
 
function am_init(){
		
		session_start();
		
		if(is_admin()){
			$_SESSION['vbz_auth_isadmin'] = true;
			global $current_user;
		    get_currentuserinfo();
			$_SESSION['vbz_auth_username'] = $current_user->user_login;
		} else {
			$_SESSION['vbz_auth_isadmin'] = false;
		}
	
        echo "<h1>aXcelerate Web Enrolment Manager <span style='font-weight:normal; font-size:13px;'>By <a href='http://www.vbz.com.au/' target='_blank'>Verbazend</a></span></h1>";
		//Include JS Runner
		echo("<script>");
		include("am_ui.js");
		echo("</script>");
		//echo('<iframe width="100%" height="100%" src="/wp-content/plugins/vbz-axcelerate/manager/index.html"></iframe>');
		
		echo('<section id="about" data-type="background" data-speed="10" class="pages">
		    <iframe width="99%" src="/wp-content/plugins/vbz-axcelerate/manager/index.php" id="demo_frame" align="center" scrolling="no"  frameborder="0" marginheight="0" marginwidth="0"></iframe>
		</section>');

		echo("<script>
		        jQuery('iframe').load(function() {
		            this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
		        });
		</script>");
		
		 

/*
      echo 'Username: ' . $current_user->user_login . "\n";
      echo 'User email: ' . $current_user->user_email . "\n";
      echo 'User first name: ' . $current_user->user_firstname . "\n";
      echo 'User last name: ' . $current_user->user_lastname . "\n";
      echo 'User display name: ' . $current_user->display_name . "\n";
      echo 'User ID: ' . $current_user->ID . "\n";
 * 
 */
} 
?>