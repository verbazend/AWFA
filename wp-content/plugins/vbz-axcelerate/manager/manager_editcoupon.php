<?php
if(isset($_GET['cid'])){
	$couponID = $_GET['cid'];
} else {
	die("No ID Found");
}

$couponID = db::esc($couponID);
$couponData = db::runQuery("select * from coupons where ID = '$couponID'");

if(!$couponData){
	die("ID not Found");
}
$couponData = $couponData[0];
?>
<div style="min-height:800px;">
	<div class="row">
        <div class="col-xs-12">
          <h2 class="h2small">Edit Coupon/Campaign: <?php echo $couponData['campaignName']; ?></h2>      
          
        </div>
        
     </div>
     <div class="row">
     	<div class="col-xs-12">
     	<br>
          <div id="couponDetails">
          		<table width="100%">
          			<tr height="40">
          				<th width="200">Campaign Name</th>
          				<td><input id="campaignName" type="text" value="<?php echo $couponData['campaignName']; ?>"></td>
          			</tr>
          			<tr height="40">
          				<th>Campaign Code</th>
          				<td><input id="campaignName" type="text" value="<?php echo $couponData['couponCode']; ?>"></td>
          			</tr>
          			<tr height="40">
          				<th>Discount Type</th>
          				<td></td>
          			</tr>
          			<tr height="40">
          				<th>Discount Amount</th>
          				<td><input id="campaignName" type="text" value="<?php echo $couponData['discountAmount']; ?>"></td>
          			</tr>
          			<tr height="40">
          				<th>Restrict Courses</th>
          				<td></td>
          			</tr>
          			<tr height="40">
          				<th>Allow Offline Payments</th>
          				<td></td>
          			</tr>
          			<tr height="40">
          				<th>Force Offline Payments</th>
          				<td></td>
          			</tr>
          			<tr height="40">
          				<th>Don't Invoice Trainee</th>
          				<td></td>
          			</tr>
          			<tr height="40">
          				<th>Address To Invoice</th>
          				<td><input id="campaignName" type="text" value="<?php echo $couponData['addressToInvoice']; ?>"></td>
          			</tr>
          			<tr height="40">
          				<th>Employer Name</th>
          				<td><input id="campaignName" type="text" value="<?php echo $couponData['employerName']; ?>"></td>
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
		//jQuery(".step2").hide();
		//jQuery(".courseClosedDetails").hide();
		//jQuery(".confirmOpenButton").hide();
	});
	
	
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