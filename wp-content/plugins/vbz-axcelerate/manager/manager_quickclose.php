<div style="min-height:800px;">
	<div class="row">
        <div class="col-xs-12">
          <h2 class="h2small">Quick Close a Course</h2>
          <p>Use this page to quickly close a specific course on a Specific date.<br>You will need the Instance ID/Workshop ID from aXcelerate or the website.</p>
          <br><br>
          <div class="col-xs-3">
          		<div class="instanceIDinput form-group">
          			<input type="text" id="instanceID" value="" placeholder="Workshop ID" class="form-control">
          		</div>
          </div>
          <div class="col-xs-3">
          		<a href="#" onclick="checkCoarse();" class="btn btn-block btn-lg btn-primary">Find Course</a>
          </div>
          
        </div>
        
     </div>
     <div class="step2 row">
     	<div class="col-xs-12">
     	<br>
          <div id="courseDetails">
          		<table width="100%">
          			<tr height="40">
          				<th width="200">Course Name</th>
          				<td id="courseName"></td>
          			</tr>
          			<tr height="40">
          				<th>Course Date</th>
          				<td id="courseDate"></td>
          			</tr>
          			<tr height="40">
          				<th>Course Time</th>
          				<td id="courseTime"></td>
          			</tr>
          			<tr height="40">
          				<th>Course Location</th>
          				<td id="courseLocation"></td>
          			</tr>
          			<tr height="40">
          				<th>Course Cost</th>
          				<td id="courseCost"></td>
          			</tr>
          			<tr height="40">
          				<th>Course Status</th>
          				<td id="courseStatus"></td>
          			</tr>
          			<tr class="courseClosedDetails" height="40">
          				<th>Course Closed On</th>
          				<td id="courseClosedOn"></td>
          			</tr>
          			<tr class="courseClosedDetails" height="40">
          				<th>Course Closed By</th>
          				<td id="courseClosedBy"></td>
          			</tr>
          		</table>
          </div>
         </div>
     </div>
     <br>
     <div class="step2 confirmCloseButton row">
     	<div class="col-xs-3 col-xs-offset-2">
        	<a href="#" onclick="confirmCloseCourse();" class="btn btn-block btn-lg btn-primary">Confirm Course Close</a>
        </div>
     </div>
     <div class="step2 courseAlreadyClosed row">
     	<div class="col-xs-5 col-xs-offset-2">
        	This course is closed.
        </div>
     </div>
     <div class="confirmOpenButton row">
     	<div class="col-xs-3 col-xs-offset-2">
        	<a href="#" onclick="confirmOpenCourse();" class="btn btn-block btn-lg btn-warning">Re-open Course</a>
        </div>
     </div>
     
     
<br><br><br>
</div>
<script>
	
	jQuery(function ($) {
		jQuery(".step2").hide();
		jQuery(".courseClosedDetails").hide();
		jQuery(".confirmOpenButton").hide();
	});
	
	function checkCoarse(){
		
		//Reset
		jQuery(".has-error").removeClass("has-error");
		jQuery(".step2").hide();
		jQuery(".courseClosedDetails").hide();
		jQuery(".confirmOpenButton").hide();
		
		//Run
		instanceID = jQuery("#instanceID").val();
		
		if(instanceID==""){
			jQuery(".instanceIDinput").addClass("has-error");
			return false;
		}
		pData = "";
		
		jQuery.post("/booking-manager/?rq=vbzaxman_checkInstance&instanceID="+instanceID, pData, function(rdata){
		    console.log(rdata); // check your console, you should see some output
		    
		    respData = jQuery.parseJSON(rdata);
		    if(respData){
		    	
		    	jQuery(".step2").show(200);
		    	
		    	jQuery("#courseName").html(respData.courseName);
		    	jQuery("#courseDate").html(respData.courseDate);
		    	jQuery("#courseTime").html(respData.courseTimings);
		    	jQuery("#courseLocation").html(respData.courseLocation);
		    	jQuery("#courseCost").html("$"+respData.courseTotalCost);
		    	
		    	if(respData.manualClose=="1"){
			    		jQuery("#courseStatus").html('<div style="padding:5px;" class="btn   btn-danger">Closed</div>');
			    		jQuery("#courseClosedOn").html(respData.courseClosedOn);
			    		jQuery("#courseClosedBy").html(respData.courseClosedBy);
			    		jQuery(".confirmCloseButton").hide();
			    		jQuery(".courseAlreadyClosed").show();
			    		jQuery(".courseClosedDetails").show();
			    		jQuery(".confirmOpenButton").show();
			    } else {
			    	if(respData.enrolmentOpen=="1"){
			    		jQuery("#courseStatus").html('<div style="padding:5px;" class="btn   btn-success">Open</div>');
			    		jQuery(".confirmCloseButton").show();
			    		jQuery(".courseAlreadyClosed").hide();
			    	} else {
			    		jQuery("#courseStatus").html('<div style="padding:5px;" class="btn   btn-danger">Closed</div>');
			    		jQuery(".confirmCloseButton").hide();
			    		jQuery(".courseAlreadyClosed").show();
			    		
			    	}
			    }
			    
			    scrollToElement("#instanceID");

				
		    } else {

		    	cancelLoading();
				alert("Course Not Found!");
		    	return false;
		    }
		    
		});
	}
	function confirmCloseCourse(){
		
		instanceID = jQuery("#instanceID").val();
		
		pData = "";
		jQuery.post("/booking-manager/?rq=vbzaxman_closeInstance&instanceID="+instanceID, pData, function(rdata){
		    console.log(rdata); // check your console, you should see some output
		    
		    respData = jQuery.parseJSON(rdata);
		    if(respData){
		    	checkCoarse();
		    } else {
		    	alert("Something went wrong.\nUnabled to close the course!\n\nPlease contact Verbazend on 07 5353 9107");
		    }
		    
		});
	}
	function confirmOpenCourse(){
		
		instanceID = jQuery("#instanceID").val();
		
		pData = "";
		jQuery.post("/booking-manager/?rq=vbzaxman_openInstance&instanceID="+instanceID, pData, function(rdata){
		    console.log(rdata); // check your console, you should see some output
		    
		    respData = jQuery.parseJSON(rdata);
		    if(respData){
		    	checkCoarse();
		    } else {
		    	alert("Something went wrong.\nUnabled to open the course!\n\nPlease contact Verbazend on 07 5353 9107");
		    }
		    
		});
	}
	
	function startLoading(){
		jQuery("#enrolloading").show(200);
	}
	function cancelLoading(){
		jQuery("#enrolloading").hide(200);
	}
	function scrollToElement(elementselector){
		jQuery('html, body').animate({
	        scrollTop: jQuery(elementselector).offset().top-80
	    }, 800);
	}
</script>