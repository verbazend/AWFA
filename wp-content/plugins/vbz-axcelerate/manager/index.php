<?php
//session_start();

date_default_timezone_set('Australia/Brisbane');

  include("../../../../booking-manager/application.php");
  include("../../../../booking-manager/db.class.php"); 
  include("../../../../booking-manager/functions.php");
  include("../../../../booking-manager/esmtp.class.php");

if($_SESSION['vbz_auth_isadmin']) {
  //do extra stuff here for only admin 
  if(isset($_GET['page'])){
  	$page = $_GET['page'];
  } else {
  	$page = "home";
  }
  
  $userName = $_SESSION['vbz_auth_username'];
  
  include("manager_header.php");
  if($page=="home"){
  	include("manager_home.php");
  }else if($page=="na"){
  	include("manager_notavailable.php");
  }else if($page=="quickclose"){
  	include("manager_quickclose.php");
  }else if($page=="coupons"){
  	include("manager_coupons.php");
  }else if($page=="coupons_edit"){
  	include("manager_editcoupon.php");
  }else if($page=="active_processing"){
  	include("manager_activeprocessing.php");
  } else {
  	include("manager_404.php");
  }
  include("manager_footer.php");
  



} else {
	die("Access Denied!");
}
