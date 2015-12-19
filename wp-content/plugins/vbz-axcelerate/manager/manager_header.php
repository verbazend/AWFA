<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>VBZ - Axcelerate Web Enrolment Manager</title>
    
    <meta name="viewport" content="width=1000, initial-scale=1.0, maximum-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="dist/css/vendor/bootstrap.min.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="dist/css/flat-ui.css" rel="stylesheet">
    <link href="docs/assets/css/demo.css" rel="stylesheet">
    <link href="css/jquery.dynatable.css" rel="stylesheet">

    <link rel="shortcut icon" href="img/favicon.ico">
	
	<script type='text/javascript' src='/wp-admin/load-scripts.php?c=1&amp;load%5B%5D=jquery-core,jquery-migrate,jquery-ui-core,jquery-ui-datepicker,utils,jquery-ui-widget&amp;ver=4.1'></script>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="dist/js/vendor/html5shiv.js"></script>
      <script src="dist/js/vendor/respond.min.js"></script>
    <![endif]-->

	<script src="js/highcharts.js"></script>
	<script src="js/modules/exporting.js"></script>
	<script src="js/jquery.dynatable.js"></script>

    <style>
    	.selected {
    		color: #1abc9c !important;
    	}
    	.h2small {
    		font-size:24px !important;
    		margin-top:0px;
    	}
    </style>
  </head>
  <body><br>
  	<div class="container">
  		
		<div class="row demo-row">
	        <div class="col-xs-12">
	          <nav class="navbar navbar-inverse navbar-embossed" role="navigation">
	            <div class="collapse navbar-collapse" id="navbar-collapse-01">
	              <ul class="nav navbar-nav navbar-left">
	                <li><a href="?page=home" <?php if($page=="home"){ ?>class="selected"<?php } ?>>Overview</a></li>
	                <li class="dropdown">
	                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
	                  <span class="dropdown-arrow"></span>
	                  <ul class="dropdown-menu">
	                    <li><a href="?page=na">Courses</a></li>
	                    <li><a href="?page=na">Course Events</a></li>
	                    <li><a href="?page=na">Locations</a></li>
	                    <li class="divider"></li>
	                    <li><a href="?page=na">Client Bookings</a></li>
	                    <li class="divider"></li>
	                    <li><a href="?page=coupons">Coupons/Campaigns</a></li>
	                  </ul>
	                </li>
	                <li><a href="?page=quickclose" <?php if($page=="quickclose"){ ?>class="selected"<?php } ?>>Quick Close</a></li>
	                <li><a href="?page=active_processing">Live View</a></li>
	                <li><a href="?page=na">Run Sync</a></li>
	               </ul>
	               <form class="navbar-form navbar-right" action="#" role="search">
	                <div class="form-group">
	                  <div class="input-group">
	                    <input disabled="disabled" class="form-control" id="navbarInput-01" type="search" placeholder="Search">
	                    <span class="input-group-btn">
	                      <button disabled="disabled" type="submit" class="btn"><span class="fui-search"></span></button>
	                    </span>
	                  </div>
	                </div>
	              </form>
	            </div><!-- /.navbar-collapse -->
	          </nav><!-- /navbar -->
	        </div>
	      </div> <!-- /row -->