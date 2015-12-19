<div class="firstaid-minibookingform">
	
	<div class="homepage-firstaid-minibookingform-header">
		<h1>Book Online here</h1>
		First Aid Course Selector
	</div>
	
	<div class="firstaid-minibookingform-formwrapper">
		<div class="enrol-mini-state">
			<label>State <span class="enrol-validationerror">Please select a State</span><select class="axcelerateState"><option value="0">Select State</option><option value="QLD">QLD</option><option value="NSW">NSW</option><option value="VIC">VIC</option><option value="SA">SA</option><option value="WA">WA</option></select></label>
		</div>
		<div class="enrol-mini-location">
			<label>Location <span class="enrol-validationerror">Please select a Location</span><select class="axcelerateLocation"><?php //function to collect Location ?></select></label>
		</div>
		<div class="enrol-mini-course">
			<label>Course <span class="enrol-validationerror">Please select a Course</span><select class="axcelerateCourse"><?php //function to collect Course ?></select></label>
		</div>
		<div class="enrol-mini-date">
			<label>Date<span class="color-red">*</span> <span class="enrol-validationerror">Please select a Date</span><input placeholder="Select Date" class="axcelerateCourseDate" type="text" /></label>
		</div>
		<div><a class="enrolbutton minienrolbuton" onclick="miniEnrolNow();" href="javascript:void(0);"><strong>Enrol Now</strong></a></div>
	</div>
	
</div>

<?php
//JS Functions to adjust look 'n feel based on homepage or not
?>
<style>
body.home .firstaid-minibookingform-formwrapper {
	border-left:3px solid #b0163c;
	border-right:3px solid #b0163c;
}

</style>