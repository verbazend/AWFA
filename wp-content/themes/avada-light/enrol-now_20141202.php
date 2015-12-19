<?php
// Template Name: Enrol Now

if(isset($_GET['campaign'])){
	$campaign = urlencode($_GET['campaign']);
	$campaignaparamstring = "&campaign=".$campaign;
} else {
	$campaign = "";
	$campaignaparamstring = "";
}

//Loads data from Axcelerate and places it into a JS Format for our JS scripts to then handle.
$courseData = file_get_contents('https://www.australiawidefirstaid.com.au/booking-manager/?rq=checkInstance&instanceID='.urlencode($_GET['cid']).$campaignaparamstring);

if($courseData=="false" || $courseData==""){

   header( 'Location: /enrol-now/course-not-found/' ) ;
}
$courseData = str_replace("\\n","<br>",$courseData);

$_SESSION['enrol_Enrolment'] = false;
$_SESSION['enrol_Contact'] = false;

get_header(); 

?>
<div class="enrolment-form">
	<h1 class="font-opensans font-normal enrol-page-title"><?php the_title(); ?></h1>
	<div id="content" style="float:left;">
		<?php while(have_posts()): the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			
			<span class="vcard" style="display: none;"><span class="fn"><?php the_author_posts_link(); ?></span></span>
			<?php global $data; if(!$data['featured_images_pages'] && has_post_thumbnail()): ?>
			<div class="image">
				<?php the_post_thumbnail('full'); ?>
			</div>
			<?php endif; ?>
			<div class="post-content">
				<div class="enrol-section-1">
					<span class="section-title"><span class="numberincircle-red">1</span> Your Information</span>
					<div class="inputitem"><span>First Name *</span><input type="text" name="firstname" placeholder="First Name *"><span class="enrolmain-validationerror en_firstname">Please enter a First Name</span></div>
					<div class="inputitem"><span>Last Name *</span><input type="text" name="lastname" placeholder="Last Name *"><span class="enrolmain-validationerror en_lastname">Please enter a Last Name</span></div>
					<div class="inputitem"><span>Mobile *</span><input type="text" name="mobile" placeholder="Mobile *"><span class="enrolmain-validationerror en_mobile">Please enter a Mobile</span></div>
					<div class="inputitem"><span>Email *</span><input type="email" name="email" placeholder="Email *"><span class="enrolmain-validationerror en_email">Please enter a valid Email</span></div>
					<div class="inputspacer"></div>
					<div class="inputitem"><span>Address *</span><input type="text" name="address" placeholder="Address *"><span class="enrolmain-validationerror en_address">Please enter an Address</span></div>
					<div class="inputitem"><span>Suburb *</span><input type="text" name="suburb" placeholder="Suburb *"><span class="enrolmain-validationerror en_suburb">Please enter a Suburb</span></div>
					<div class="inputitem"><span>Postcode *</span><input type="text" name="postcode" placeholder="Postcode *" class="halfsize"><span class="enrolmain-validationerror en_postcode">Please enter a Postcode/Zip</span></div>
					<div class="inputspacer"></div>
					<div class="inputitem"><span>Workplace</span><input type="text" name="workplace" placeholder="Workplace"></div>
					<div class="inputitem"><span>How did you find us?</span><div class="selector"><select name="source" class="select-howdidyoufindus"></select></div></div>
					<div class="inputitem fullwidth"><span>Additional info (including if you require learning support)</span>
						<textarea rows="8" name="additionalinfo" placeholder="Additional info (including if you require learning support)"></textarea>
					</div>
					<div class="inputspacer"></div>
					<div class="inputitem"><label><input type="checkbox" name="tacagree" value="1"  checked="checked"> <div class="checkbox-text">I agree to the <a href="/terms-conditions/" target="_blank">terms &amp; conditions</a> *</div></label><span class="enrolmain-validationerror en_tacagree">You must agree to the terms and conditions</span></div>
					<div class="inputitem" style="margin-right:0px; width:49%;"><label><input type="checkbox" name="sendreminders" value="1" checked="checked"> <div class="checkbox-text">send me re-book reminders &amp; newsletters</div></label></div>
					<div class="backNextButtons nobackbutton"></div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputline"></div>
					<div class="inputspacer">&nbsp;</div>
				</div>
				
				<div class="enrol-section-2">
					<span class="section-title"><span class="numberincircle-red">2</span> Payment</span><span class="enrolmain-validationerror validation-payment en_paymenttype">You must select a Payment option</span>
					<div class="inputspacer"></div>
					<div class="info-title">How do you want to pay?</div>
					
					<div class="inputspacer-half"></div>
					<div class="radioitem radiolarge"><label><input type="radio" name="paymenttype" value="payment-card"> <div><img src="/wp-content/uploads/2014/07/credit-cards_awfa.png"></div></label></div>
					<div class="radioitem payment-paypal"><label><input type="radio" name="paymenttype" value="payment-paypal"> <div><img src="/wp-content/uploads/2014/06/paypal.png"></div></label></div>
					<div class="radioitem" style="display:none;"><label><input type="radio" name="paymenttype" value="payment-ontheday"> <div>I'll pay on the day</div></label></div>
					<div class="radioitem"><label><input type="radio" name="paymenttype" value="payment-other"> <div>Other</div></label></div>
					
					<div class="payment-type-wrapper payment-type-card">
						<div class="inputspacer"></div>
						
						<div class="inputitem"><span>Card number *</span><input type="text" name="cardnumber" placeholder="Card number *"><span class="enrolmain-validationerror en_cardnumber">You must enter a Valid card number</span></div>
						<div style="display:none;" class="inputitem inputitem-third"><span>Postal/Zip *</span><input name="cardpostcode" type="text" value="0000" placeholder="Postal/Zip *"><span class="enrolmain-validationerror en_cardpostcode">You must enter the cards Billing postcode</span></div>
						<div class="inputitem inputitem-third"><span>CCV *</span><input name="cardccv" type="text" placeholder="CCV *"><span class="enrolmain-validationerror en_cardccv">You must enter the cards CVV (last 3 digits on the back)</span></div>
						
						<div class="inputitem"><span>Expiry Month</span><div class="selector"><select name="cardexpirymonth" class="select-expirymonth"></select><span class="enrolmain-validationerror">You must select an Expiry Month</span></div></div>
						<div class="inputitem"><span>Expiry Year</span><div class="selector"><select name="cardexpiryyear" class="select-expiryyear"></select><span class="enrolmain-validationerror">You must select an Expiry Year</span></div></div>
						<div class="inputspacer"></div>
					</div>
					<div class="payment-type-wrapper payment-type-paypal">
						<div class="inputspacer"></div>
						<div>When you click "<span class="bolder">Enrol Now</span>" below we'll take you to PayPal's site to process your payment securely.</div>
					</div>
					<div class="payment-type-wrapper payment-type-ontheday">
						<?php //No info for this item. Place text here about what todo about paying on the day :) ?>
					</div>
					<div class="payment-type-wrapper payment-type-other">
						<?php //No info for this item. Place text here about what todo about paying on the day :) ?>
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Other Payment Options</span><div class="selector"><select name="otherpaymentselection" class="select-otherpaymentoptions"></select> <span class="enrolmain-validationerror en_otherpaymentselection">You must select a Payment Option</span></div></div>
					</div>
					<div class="backNextButtons"></div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputline"></div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer"><img src="/images/order_ssl_b.png"></div>
				</div>
				
				<div class="enrol-section-3">
					<span class="section-title"><span class="numberincircle-red">3</span> Confirm</span>
					<div class="inputspacer"></div>
					<div class="info-title">That's it! just double check your details below are right</div>
					<div class="inputspacer-half"></div>
					<div class="enrol-details-wrapper">
						<div class="enrol-details-item">
							<div class="enrol-details-item-title">First name</div>
							<div class="enrol-details-item-value value-firstname">--</div>
						</div>
						<div class="enrol-details-item">
							<div class="enrol-details-item-title">Last name</div>
							<div class="enrol-details-item-value value-lastname">--</div>
						</div>
						<div class="enrol-details-item">
							<div class="enrol-details-item-title">Mobile</div>
							<div class="enrol-details-item-value value-mobile">--</div>
						</div>
						<div class="enrol-details-item">
							<div class="enrol-details-item-title">Email</div>
							<div class="enrol-details-item-value value-email">--</div>
						</div>
						<div class="inputspacer"></div>
						
						<div class="enrol-details-item">
							<div class="enrol-details-item-title">Address</div>
							<div class="enrol-details-item-value value-address">--</div>
						</div>
						<div class="enrol-details-item">
							<div class="enrol-details-item-title">Suburb</div>
							<div class="enrol-details-item-value value-suburb">--</div>
						</div>
						<div class="enrol-details-item">
							<div class="enrol-details-item-title">Postcode</div>
							<div class="enrol-details-item-value value-postcode">--</div>
						</div>
					</div>
					<div class="backNextButtons nonextbutton backfloatleft"></div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer"></div>
					<div class="enrol-error-message"><div class="enrol-error-sadface">&#9785;</div><div class="error-header">Something went wrong</div> <div class="enrol-error-text"></div></div>
					<a class="enrol-now-button-large" style="float:right; margin-top:-42px;" onclick="enrolNow();" href="javascript:void(0);">Enrol Now</a>
					<div class="inputspacer" style="margin-top:5px;"><br><br><img  style="display:block; float:right;" class="secure-inline" src="/wp-content/uploads/2014/06/secure.png"></div>
					
					<div class="inputspacer"><br><br><br></div>
					<div class="inputspacer"><img style="margin-top:15px;" src="/images/order_ssl_b.png"></div>
				</div>
			</div>

		</div>
		<?php endwhile; ?>
	</div>
	<div id="sidebar" style="float:right;">
		<div id="enrolment-selection">
			<h2>Your selected course</h2>
			<div><span class="enrol-sidebar-title">Course:</span> <span class="enrol-coursename">--</span></div>
			<div><span class="enrol-sidebar-title">Date:</span> <span class="enrol-coursedate">--</span></div>
			<div><span class="enrol-sidebar-title">Time:</span> <span class="enrol-coursetime">--</span></div> 
			<div><span class="enrol-sidebar-title">Location:</span> <span class="enrol-courselocation">--</span></div>
			<div class="promotionname"><span class="enrol-sidebar-title">Promotion:</span> <span class="enrol-promotion">--</span></div>
			<div><span class="enrol-sidebar-title">Total cost:</span> <span class="enrol-coursecost">--</span></div>
			
			<img class="secure" src="/wp-content/uploads/2014/06/secure.png">
		</div>
		<a href="javascript:void(0);" class="sidebartoggle"></a>
	</div>
	<script>
		var courseData = '<?php echo $courseData; ?>';
		
		jQuery(function($){
			$(".promotionname").hide();
			loadCourseDataToSidebar(QueryString.cid);
		});
	</script>
	<input type="hidden" id="enrol-courseID" name="enrol-courseID" value="">
	<input type="hidden" id="campaign" name="campaign" value="<?php echo $campaign; ?>">
</div>	
<?php
	if($_GET['AB']=="progressdemo"){
		?>
		<div id="enrolloading" style="z-index:999999;">
			<div class="procenroltitle">Processing Enrolment</div><br><br><br><br>
			<div id="progresswrapper">
				<div class="progress-label" style="text-align:center; color:#fff;">Processing...</div>
  				<div id="bookingProgressBar"></div>
  			</div>
		</div>
		<script>

			jQuery(function($){
				$("#enrolloading").show(100);
			});
			
			currentProgress = 0;
			timeToWait = 30;
			qtypersecond = 100/timeToWait;
		    qtyperhundredth = qtypersecond/10;
		    qtyperhundredthslow = qtypersecond/100;
		    
			jQuery(function($){
				
				
			    $( "#bookingProgressBar" ).progressbar({
			      value: 0
			    });
			    
			    
			    
			    window.setTimeout("addTime("+qtypersecond+","+qtyperhundredth+","+timeToWait+");", 50);
			    
			    
			    
			});
			 
			function setProgress(newValue){
				if(newValue<=100){
			    	progressbar = jQuery( "#bookingProgressBar" );
					progressbar.progressbar( "option", "value", newValue );
				}
			}
			function addTime(qtypersecond,qtyperhundredth,timeToWait){
				//console.log(qtypersecond);
				//console.log(qtyperhundredth);
				//console.log(timeToWait);
				//console.log(currentProgress);
				//console.log("------------------");
				if(currentProgress<=70){
					currentProgress = currentProgress + qtyperhundredth;
				} else {
					currentProgress = currentProgress + qtyperhundredthslow;
				}
				setProgress(currentProgress);
				if(currentProgress<=100){
					window.setTimeout("addTime("+qtypersecond+","+qtyperhundredth+","+timeToWait+");", 50);
				} else {
					jQuery("#bookingProgressBar").hide();
					jQuery(".progress-label").html("Finalising Enrolment, Please wait.");
				}
			}
			
			
		</script>
		<style>
		 #progresswrapper {
		 	text-align:center;
		 	width:350px;
		 	margin-left:auto;
		 	margin-right:auto;
		 	margin-top:100px;
		 }
		  #bookingProgressBar {
		    margin-top: 20px;

		    height:10px;
		    
		  }
		 .ui-progressbar-value {
		 	height:10px;
		 	border:0px;
		 }
		 #bookingProgressBar .ui-widget-header{
		 	border:none;
		 }
		  .progress-label {
		    font-weight: bold;
		    
		  }
		 
		  .ui-dialog-titlebar-close {
		    display: none;
		  }
		  #enrolloading {
		  	background-image:none !important;
		  }
		  </style>
		<?php
	} else {
		?>
		<div id="enrolloading">
			<div class="procenroltitle">Processing Enrolment</div>
		</div>
		<?php
	}
?>
	
	<style>
		#enrolloading {
			position:fixed;
			left:0px;
			right:0px;
			top:0px;
			bottom:0px;
			height:100%;
			width:100%;
			background-color:#b0163c;
			display:inline-block;
			background-image:url(/wp-content/themes/avada-light/form-templates/images/loading-trn.gif);
			background-repeat:no-repeat;
			background-position:center center;
		}
		.procenroltitle {
			text-align:center;
			font-size:24px;
			font-weight:bold;
			color:#fff;
			margin-top:40px;
		}
	</style>

<!-- Google Code for Enquire Now Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 995774513;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "1fljCIe61AoQsaDp2gM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/995774513/?label=1fljCIe61AoQsaDp2gM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>


<?php get_footer(); ?>