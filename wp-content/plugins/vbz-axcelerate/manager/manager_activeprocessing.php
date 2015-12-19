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
				    <th data-dynatable-column="TotalTrainees">Total Trainees</th>
				    <th data-dynatable-column="SingleInvoice">Single Invoice?</th>
				    <th data-dynatable-column="CampaignCode">Campaign</th>
				    <th data-dynatable-column="TotalCost">Cost</th>
				    
				    <th data-dynatable-column="PaymentType">Payment Type</th>
				    <th data-dynatable-column="CurrentStatus">Status</th>

				    <th data-dynatable-column="view" style="width:60px;">&nbsp;</th>
		    
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
		
		//loadDataTable();
		
		var mytable = $("#couponList");
        setInterval(function() {
		
			$.ajax({
			  url: '/booking-manager/?rq=vbzaxman_getActiveprocessing',
			  success: function(data){

			    mytable.dynatable({
			      dataset: {
			        records: data
			      }
			    });
				
			  }
			});
		
		},
        1000);
		//jQuery(".step2").hide();
		//jQuery(".courseClosedDetails").hide();
		//jQuery(".confirmOpenButton").hide();
	});
	
	
	function loadDataTable(){

		jQuery.ajax({
		  url: '/booking-manager/?rq=vbzaxman_getActiveprocessing',
		  success: function(data){
		    jQuery('#couponList').dynatable({
		      dataset: {
		        records: data
		      }
		    });
		  }
		});

		//window.setTimeout("loadDataTable();",1000)
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