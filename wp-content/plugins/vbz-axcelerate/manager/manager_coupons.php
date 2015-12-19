<div style="min-height:800px;">
	<div class="row">
        <div class="col-xs-12">
          <h2 class="h2small">Coupon / Campaign Manager</h2>
          <p>Use this page to add/edit coupons & campaigns.<br>Any changes made will only affect future orders.</p>
         
        </div>
        
     </div>
     <div class="row">
     	<div class="col-xs-12">
     	<br>
          <div id="couponListWrapper">
          		<table id="couponList" class="table" style="width:100%;">
				  <thead>
				    <th data-dynatable-column="ID">ID</th>
				    <th data-dynatable-column="Name">Name</th>
				    <th data-dynatable-column="Code">Code</th>
				    <th data-dynatable-column="Type">Type</th>
				    <th data-dynatable-column="Amount">Amount</th>
				    
				    <th data-dynatable-column="EmployerName">Employer Name</th>
				    <th data-dynatable-column="edit" style="width:60px;">&nbsp;</th>
		    
				  </thead>
				  <tbody>
				  </tbody>
				</table>
          </div>
         </div>
     </div>
     <br>
     
     
     
<br><br><br>
</div>
<script>
	
	jQuery(function ($) {
		
		//$('#couponList').dynatable();
		
		// $('#couponList').dynatable({
		  // dataset: {
		    // ajax: false,
		    // ajaxUrl: '/booking-manager/?rq=vbzaxman_getCoupons',
		    // ajaxOnLoad: true,
		    // records: []
		  // }
		// });
		
		$.ajax({
		  url: '/booking-manager/?rq=vbzaxman_getCoupons',
		  success: function(data){
		    $('#couponList').dynatable({
		      dataset: {
		        records: data
		      }
		    });
		  }
		});
		
		//jQuery(".step2").hide();
		//jQuery(".courseClosedDetails").hide();
		//jQuery(".confirmOpenButton").hide();
	});
	
	function editCoupon(couponID){
		
		document.location="?page=coupons_edit&cid="+couponID;
		
	}
	
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