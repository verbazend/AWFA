<?php
if(!isset($_GET['sb'])){

?>
<div class='courseSelectionWrapperDiv'>
	<div class='center'>
		<div class='courseSelectionWrapper'>
			<span class='closebutton'>
				<a href='#'><img src='/images/close.png'></a>
			</span>
			<div class="sourceSelectorHeader">
				<div class="headertext" style="display:inline-block; float:left; max-width:500px;">
					<h2>Available Courses in <span class="selectedLocationName"></span> for <span class="selectCourseName"></span></h2>
					
				</div> 
				<div class="courseSelectorButton floatright closebutton">Close</div>
			</div>
			<div style="margin-top:80px;">
				<div class="popwindow_changelocation_button enrolbuttonmini">Change Location or Course</div>
			</div>
			<div style="margin-top:-10px;" class='courseselectionlist'>
					
			</div>
		</div>
		
	</div>
</div>

<div class="courseChangeDetails remifdup">
	

	<div class="firstaid-minibookingform">
		
		<h3 style="margin-left:20px;">Please make your selection below</h3>

		<div class="firstaid-minibookingform-formwrapper">
			<div class="enrol-mini-location">
				<label><span>Location</span> <select class="axcelerateLocation"><?php //function to collect Location ?></select></label>
			</div>
			<div class="enrol-mini-course">
				<label><span>Course</span> <select class="axcelerateCourse"><?php //function to collect Course ?></select></label>
			</div>
		</div>
		
	</div>
	
</div>

<style>
.courseChangeDetails .firstaid-minibookingform {
	width:100% !important;
}

.courseChangeDetails label span {
	display:inline-block;
	width:70px;
}
.courseChangeDetails div {
	margin-bottom:10px;
}
.popwindow_changelocation_button, .popwindow_changelocation_button {
	display:inline-block;
	margin-right:20px;
	max-width:190px;
	cursor: pointer; cursor: hand;
}
.ui-widget-header {
	background-image:none !important;
} 
.ui-dialog-titlebar-close {
	display:none !important;
}
.courseChangeDetails {

}

@media screen and (max-width: 500px) {
	.headertext {
		max-width:300px !important;
	}
	.courseselectionlist {
		padding-left:2px;
		padding-right:2px;
	}
	.selector_remseats {
		display:none;
		
	}
	.hidemobile {
		display:none;
	}
	.courselocationdetails {
		display:none;
	}
	.closebutton {
		float: none !important;
		margin-left: 40px;
	}
	.courseSelectorButton.floatright {
		
	}
	
	
	
	.manager-singleperson {
		width:100px;
		border:1px solid #dddddd;
		height:150px;
		background-color:#eeeeee;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		margin-bottom:0px;
		display:inline-block;
		margin-right:10px;
		-webkit-border-bottom-right-radius: 0px;
		-webkit-border-bottom-left-radius: 0px;
		-moz-border-radius-bottomright: 0px;
		-moz-border-radius-bottomleft: 0px;
		border-bottom-right-radius: 0px;
		border-bottom-left-radius: 0px;
		border-bottom:2px solid #fff;
		vertical-align:top;
	}
	.manager-addperson {
		width:80px;
		border:1px solid #dddddd;
		height:150px;
		background-color:#eeeeee;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		margin-bottom:0px;
		display:inline-block;
		margin-right:10px;
		-webkit-border-bottom-right-radius: 0px;
		-webkit-border-bottom-left-radius: 0px;
		-moz-border-radius-bottomright: 0px;
		-moz-border-radius-bottomleft: 0px;
		border-bottom-right-radius: 0px;
		border-bottom-left-radius: 0px;
		border-bottom:2px solid #fff;
		vertical-align:top;
		background-image:url(/wp-content/uploads/2015/07/addmore.png);
		background-repeat:no-repeat;
		background-position:center center;
	}
	.manager-nextstep {
		width:100px;
		float:right;
		background-image:url(/wp-content/uploads/2015/07/nextarrow.png);
		background-repeat:no-repeat;
		background-position:center 10px;
	}
	.manager-button-edit {
		display:none;
	}
	.manager-nextstep {
		display:none;
	}
	.float-left {
		float:center;
	}
	.inputitem {
		width:97% !important;
	}

}
@media screen and (max-width: 400px) {
	.headertext {
		max-width:240px !important;
	}
	.courseselectionlist {
		padding-left:2px;
		padding-right:2px;
	}
	.selector_remseats {
		display:none;
		
	}
	.hidemobile {
		display:none;
	}
	.courselocationdetails {
		display:none;
	}
	.closebutton {
		float: none !important;
		margin-left: 20px;
	}
	.courseSelectorButton.floatright {
		
	}
}


body .courseChangeDetails .firstaid-minibookingform-formwrapper {
	border-left:0px !important;
	border-right:0px !important;
}
</style>

<script>
	
jQuery(function($) {
	

	$( ".courseChangeDetails" ).dialog({
      autoOpen: false,
      height: 250,
      width: 370,
      modal: true,
	  draggable: false,
	  resizable: false,
	  appendTo: ".courseSelectionWrapper",
    });
    
    
    $(".popwindow_changelocation_button").on("click", function(){
    	$( ".courseChangeDetails" ).dialog( "open" );	
    });
    
    
    
});	


</script>
<?php } ?>