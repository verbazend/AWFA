<?php
// Template Name: Enrol Now Multi

if(isset($_GET['campaign'])){
	$campaign = urlencode($_GET['campaign']);
	$campaignaparamstring = "&campaign=".$campaign;
	
	$cid = urlencode($_GET['cid']);
	
	//header( 'Location: /enrol-now/?cid='.$cid.'&campaign='.$campaign.'' ) ;
	
} else {
	$campaign = "";
	$campaignaparamstring = "";
}

//Loads data from Axcelerate and places it into a JS Format for our JS scripts to then handle.
$courseData = file_get_contents('https://www.australiawidefirstaid.com.au/booking-manager/?rq=checkInstance&instanceID='.urlencode($_GET['cid']).$campaignaparamstring);

//var_dump($courseData);

if($courseData=="false" || $courseData==""){
//echo "NO DATA";
   header( 'Location: /enrol-now/course-not-found/' ) ;
}
$courseData = str_replace("\\n","<br>",$courseData);

$_SESSION['enrol_Enrolment'] = false;
$_SESSION['enrol_Contact'] = false;

//$showWarningMessage = "Multi Enrol is currently Disabled for Production Migration Testing as of 9/10/2015 - Mark";

$allowSingleInvoiceForEachTrainee = false;

get_header(); 

?>
<div class="enrolment-form">
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
				<?php if($showWarningMessage!=""){ ?>
				<div class="enrol-error-message formheadermessage" style="display: block;">
					<div class="enrol-error-sadface">!<br>&nbsp<br>&nbsp<br></div>
					<div class="error-header">Important Notice</div> 
					<div class="enrol-error-text"><?php echo($showWarningMessage); ?></div>
				</div>
			<?php } ?>
				<div class="enrol-section-1">
					<span class="section-title"><span class="numberincircle-red">1</span> Your Groups Information</span>
					
					<div class="personmanager">
							<div class="manager-singleperson manager-person1" onclick="editmultiperson(1);">
									<div>
												<div onclick="editmultiperson(1);" class="manager-button manager-button-edit floatright manager-edit-person1">Edit</div>
									</div>
								  <div class="manager-person-name">
								  		Click to Edit Details
								  </div>
							</div>
							<div class="manager-singleperson manager-person2 manager-person-selected" onclick="editmultiperson(2);">
									<div>
												<div onclick="editmultiperson(2);" class="manager-button manager-button-edit floatright manager-edit-person2">Edit</div>
												<div onclick="removeperson(2);" class="manager-button manager-button-remove floatleft manager-edit-person2">Remove</div>
								  </div>
								  <div class="manager-person-name">
								  		Click to Edit Details
								  </div>
							</div>
							<div class="manager-singleperson manager-person3" onclick="editmultiperson(3);">
									<div>
												<div onclick="editmultiperson(3);" class="manager-button manager-button-edit floatright manager-edit-person3">Edit</div>
												<div onclick="removeperson(3);" class="manager-button manager-button-remove floatleft manager-edit-person3">Remove</div>
								  </div>
								  <div class="manager-person-name">
								  		Click to Edit Details
								  </div>
							</div>
							<div class="manager-addperson" onclick="addperson();">
									
								  <div class="manager-addpersontext">
								  		Add More People
								  </div>
							</div>
							<?php if(1==2){ ?>
							<div class="manager-singleperson manager-nextstep">
									
								  <div class="manager-nexsteptext">
								  		Next step
								  </div>
							</div>
							<?php } ?>
					</div>
					
					<div class="booking-person booking-person1">
						<span class="section-title" style="margin-top:10px;">Editing Person 1 (Firstname Lastname)</span>
						<div class="inputitem"><span>First Name *</span><input type="text" name="firstname1" placeholder="First Name *"><span class="enrolmain-validationerror en_firstname1">Please enter a First Name</span></div>
						<div class="inputitem"><span>Last Name *</span><input type="text" name="lastname1" placeholder="Last Name *"><span class="enrolmain-validationerror en_lastname1">Please enter a Last Name</span></div>
						<div class="inputitem"><span>Mobile *</span><input type="text" name="mobile1" placeholder="Mobile *"><span class="enrolmain-validationerror en_mobile1">Please enter a Mobile</span></div>
						<div class="inputitem"><span>Email *</span><input type="email" name="email1" placeholder="Email *"><span class="enrolmain-validationerror en_email1">Please enter a valid Email</span></div>
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Address *</span><input type="text" name="address1" placeholder="Address *"><span class="enrolmain-validationerror en_address1">Please enter an Address</span></div>
						<div class="inputitem"><span>Suburb *</span><input type="text" name="suburb1" placeholder="Suburb *"><span class="enrolmain-validationerror en_suburb1">Please enter a Suburb</span></div>
						<div class="inputitem"><span>Postcode *</span><input type="text" name="postcode1" placeholder="Postcode *" class="halfsize"><span class="enrolmain-validationerror en_postcode1">Please enter a Postcode/Zip</span></div>
						<div class="inputitem usiinputitem"><span>USI</span><input type="text" name="usi1" placeholder="USI" style="width:90%"> [<a href="http://www.usi.gov.au" target="_blank">?</a>]</div>
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Workplace</span><input type="text" name="workplace1" placeholder="Workplace"></div>
						<div class="inputitem fullwidth"><span>Additional info (including if you require learning support)</span>
							<textarea rows="8" name="additionalinfo1" placeholder="Additional info (including if you require learning support)"></textarea>
						</div>
					</div>
					
					<div class="booking-person booking-person2">
						<span class="section-title" style="margin-top:10px;">Editing Person 2 (Firstname Lastname)</span>
						<div class="inputitem"><span>First Name *</span><input type="text" name="firstname2" placeholder="First Name *"><span class="enrolmain-validationerror en_firstname2">Please enter a First Name</span></div>
						<div class="inputitem"><span>Last Name *</span><input type="text" name="lastname2" placeholder="Last Name *"><span class="enrolmain-validationerror en_lastname2">Please enter a Last Name</span></div>
						<div class="inputitem"><span>Mobile *</span><input type="text" name="mobile2" placeholder="Mobile *"><span class="enrolmain-validationerror en_mobile2">Please enter a Mobile</span></div>
						<div class="inputitem"><span>Email *</span><input type="email" name="email2" placeholder="Email *"><span class="enrolmain-validationerror en_email2">Please enter a valid Email</span></div>
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Address *</span><input type="text" name="address2" placeholder="Address *"><span class="enrolmain-validationerror en_address2">Please enter an Address</span></div>
						<div class="inputitem"><span>Suburb *</span><input type="text" name="suburb2" placeholder="Suburb *"><span class="enrolmain-validationerror en_suburb2">Please enter a Suburb</span></div>
						<div class="inputitem"><span>Postcode *</span><input type="text" name="postcode2" placeholder="Postcode *" class="halfsize"><span class="enrolmain-validationerror en_postcode2">Please enter a Postcode/Zip</span></div>
						<div class="inputitem usiinputitem"><span>USI</span><input type="text" name="usi2" placeholder="USI" style="width:90%"> [<a href="http://www.usi.gov.au" target="_blank">?</a>]</div>
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Workplace</span><input type="text" name="workplace2" placeholder="Workplace"></div>
						<div class="inputitem fullwidth"><span>Additional info (including if you require learning support)</span>
							<textarea rows="8" name="additionalinfo2" placeholder="Additional info (including if you require learning support)"></textarea>
						</div>
					</div>
					
					<div class="booking-person booking-person3">
						<span class="section-title" style="margin-top:10px;">Editing Person 3 (Firstname Lastname)</span>
						<div class="inputitem"><span>First Name *</span><input type="text" name="firstname3" placeholder="First Name *"><span class="enrolmain-validationerror en_firstname3">Please enter a First Name</span></div>
						<div class="inputitem"><span>Last Name *</span><input type="text" name="lastname3" placeholder="Last Name *"><span class="enrolmain-validationerror en_lastname3">Please enter a Last Name</span></div>
						<div class="inputitem"><span>Mobile *</span><input type="text" name="mobile3" placeholder="Mobile *"><span class="enrolmain-validationerror en_mobile3">Please enter a Mobile</span></div>
						<div class="inputitem"><span>Email *</span><input type="email" name="email3" placeholder="Email *"><span class="enrolmain-validationerror en_email3">Please enter a valid Email</span></div>
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Address *</span><input type="text" name="address3" placeholder="Address *"><span class="enrolmain-validationerror en_address3">Please enter an Address</span></div>
						<div class="inputitem"><span>Suburb *</span><input type="text" name="suburb3" placeholder="Suburb *"><span class="enrolmain-validationerror en_suburb3">Please enter a Suburb</span></div>
						<div class="inputitem"><span>Postcode *</span><input type="text" name="postcode3" placeholder="Postcode *" class="halfsize"><span class="enrolmain-validationerror en_postcode3">Please enter a Postcode/Zip</span></div>
						<div class="inputitem usiinputitem"><span>USI</span><input type="text" name="usi3" placeholder="USI" style="width:90%"> [<a href="http://www.usi.gov.au" target="_blank">?</a>]</div>
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Workplace</span><input type="text" name="workplace3" placeholder="Workplace"></div>
						<div class="inputitem fullwidth"><span>Additional info (including if you require learning support)</span>
							<textarea rows="8" name="additionalinfo3" placeholder="Additional info (including if you require learning support)"></textarea>
						</div>
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
					<?php if($allowSingleInvoiceForEachTrainee){ ?>
					<div class="inputspacer"></div>
					<div class="info-title">Do you need a seperate invoice for each attendee?</div>
					<div class="radioitem radiolarge payment-singleinvoice"><label><input type="checkbox" name="single invoice" value="1" id="payment_singleinvoice"> <div>Yes, Send me an invoice for each user (Leave Blank to be invoiced only once)</div></label></div>
					<?php } ?>
					
					<div class="inputspacer"></div>
					<div class="info-title">How do you want to pay?</div>
					
					<div class="inputspacer-half"></div>
					<div class="radioitem radiolarge payment-creditcard"><label><input type="radio" name="paymenttype" value="payment-card" id="payment_cc_rb"> <div><img src="/wp-content/uploads/2014/07/awfa_payment_cards.png"></div></label></div>
					<div class="radioitem payment-paypal"><label><input type="radio" name="paymenttype" value="payment-paypal"> <div><img src="/wp-content/uploads/2014/06/paypal.png"></div></label></div>
					<div class="radioitem" style="display:none;"><label><input type="radio" name="paymenttype" value="payment-ontheday"> <div>I'll pay on the day</div></label></div>
					<div class="radioitem payment-other"><label><input type="radio" name="paymenttype" value="payment-other"> <div>Other</div></label></div>
					<div class="radioitem payment-type-employer"><label><input type="radio" name="paymenttype" value="payment-employer" id="payment_employer"> <div>Employer</div></label></div>
					
					<div id="creditcard-wrapper" class="payment-type-wrapper payment-type-card ">
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
						<div class="inputitem"><span>Other Payment Options</span>
							
							<div class="info-title" style="width:500px !important;"><b>For offline payments, please call 1300 33 66 13</b></div>
							<div class="inputspacer"></div>
						</div>
					</div>
					<div class="payment-type-wrapper payment-type-employer">
						
						<div class="inputspacer"></div>
						<div class="inputitem"><span>Employer Payment</span>
							
							<div class="info-title" style="width:500px !important;"><b>Your employer is paying for this course on your behalf.</b></div>
							<div class="inputspacer"></div>
						</div>
					</div>
					<div class="backNextButtons"></div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputline"></div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer">
						<div class="info-title">Your payment is secure</div>
						<br>
						<img width="462" src="/wp-content/uploads/2014/07/secure_paments.png">
						<br><br>
						Your credit card details are secured and encrypted by RapidSSL (GeoTrust).<br>
						Payments are processed securely with NAB.<br><br><br>
					</div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer">&nbsp;</div>
				</div>
				
				<div class="enrol-section-3" style="margin-top:100px;">
					<span class="section-title"><span class="numberincircle-red">3</span> Confirm</span>
					<div class="inputspacer"></div>
					<div class="info-title">That's it! just double check your details below are correct</div>
					<div class="inputspacer-half"></div>
					<div class="enrol-details-wrapper">
						
						<div class="confirm-person1">
							<div class="info-title" style="margin-top:10px; margin-bottom:5px;">Person 1</div>
							
						
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">First name</div>
								<div class="enrol-details-item-value value-firstname1">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Last name</div>
								<div class="enrol-details-item-value value-lastname1">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Mobile</div>
								<div class="enrol-details-item-value value-mobile1">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Email</div>
								<div class="enrol-details-item-value value-email1">--</div>
							</div>
							<div class="inputspacer"></div>
							
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Address</div>
								<div class="enrol-details-item-value value-address1">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Suburb</div>
								<div class="enrol-details-item-value value-suburb1">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Postcode</div>
								<div class="enrol-details-item-value value-postcode1">--</div>
							</div>
							
						</div>
						
						
						
						<div class="confirm-person2">
							<div class="info-title" style="margin-top:10px; margin-bottom:5px;">Person 2</div>
							
						
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">First name</div>
								<div class="enrol-details-item-value value-firstname2">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Last name</div>
								<div class="enrol-details-item-value value-lastname2">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Mobile</div>
								<div class="enrol-details-item-value value-mobile2">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Email</div>
								<div class="enrol-details-item-value value-email2">--</div>
							</div>
							<div class="inputspacer"></div>
							
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Address</div>
								<div class="enrol-details-item-value value-address2">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Suburb</div>
								<div class="enrol-details-item-value value-suburb2">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Postcode</div>
								<div class="enrol-details-item-value value-postcode2">--</div>
							</div>
							
						</div>
						
						
						
						<div class="confirm-person3">
							<div class="info-title" style="margin-top:10px; margin-bottom:5px;">Person 3</div>
							
						
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">First name</div>
								<div class="enrol-details-item-value value-firstname3">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Last name</div>
								<div class="enrol-details-item-value value-lastname3">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Mobile</div>
								<div class="enrol-details-item-value value-mobile3">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Email</div>
								<div class="enrol-details-item-value value-email3">--</div>
							</div>
							<div class="inputspacer"></div>
							
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Address</div>
								<div class="enrol-details-item-value value-address3">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Suburb</div>
								<div class="enrol-details-item-value value-suburb3">--</div>
							</div>
							<div class="enrol-details-item">
								<div class="enrol-details-item-title">Postcode</div>
								<div class="enrol-details-item-value value-postcode3">--</div>
							</div>
							
						</div>
						
						
					</div>
					<div class="backNextButtons nonextbutton backfloatleft"></div>
					<div class="inputspacer">&nbsp;</div>
					<div class="inputspacer"></div>
					<div id="enrolerrormessage" class="enrol-error-message modalmessage"><div class="enrol-error-sadface">&#9785;</div><div class="error-header">Something went wrong</div> <div class="enrol-error-text"></div><div class="enrol-error-closebutton modal_close" onclick="close_modaled('enrolerrormessage');">OK</div></div>
					<a class="enrol-now-button-large" style="float:right; margin-top:-42px;" onclick="enrolNow();" href="javascript:void(0);">Enrol Now</a>
					<div class="inputspacer" style="margin-top:5px;"><br><br><img  style="display:block; float:right;" class="secure-inline" src="/wp-content/uploads/2014/06/secure.png"></div>
					
					<div class="inputspacer"><br><br><br></div>
					<div class="inputspacer">
						<div class="info-title">Your payment is secure</div>
						<br>
						<img width="462" src="/wp-content/uploads/2014/07/secure_paments.png">
						<br><br>
						Your credit card details are secured and encrypted by RapidSSL (GeoTrust).<br>
						Payments are processed securely with NAB.<br><br><br>
					</div>
				</div>
			</div>
		<br><br><br><br><br><br><br><br>
		</div>
		<?php endwhile; ?>
	</div>
	<div id="sidebar" style="float:right;">
		<div id="enrolment-selection">
			<h2 style="color:#b0163c;">Your selected course</h2>
			<div><span class="enrol-sidebar-title">Course:</span> <span class="enrol-coursename">--</span></div>
			<div><span class="enrol-sidebar-title">Date:</span> <span class="enrol-coursedate">--</span></div>
			<div><span class="enrol-sidebar-title">Time:</span> <span class="enrol-coursetime">--</span></div> 
			<div><span class="enrol-sidebar-title">Location:</span> <span class="enrol-courselocation">--</span></div>
			<div class="promotionname"><span class="enrol-sidebar-title">Promotion:</span> <span class="enrol-promotion">--</span></div>
			<div><span class="enrol-sidebar-title">Cost Per Person:</span> <span class="enrol-coursecost">--</span></div>
			<div><span class="enrol-sidebar-title">Total cost:</span> <span class="enrol-coursecost-total">--</span></div>
			
			<img class="secure" src="/wp-content/uploads/2014/06/secure.png">
		</div>
		<a href="javascript:void(0);" class="sidebartoggle"></a>
	</div>
	<script>
		var courseData = '<?php echo $courseData; ?>';
		
		jQuery(function($){
			$(".promotionname").hide();
			loadCourseDataToSidebar(QueryString.cid);
			setmultienrolup(QueryString.qty);
			
		});
	</script>
	<input type="hidden" id="enrol-courseID" name="enrol-courseID" value="">
	<input type="hidden" id="campaign" name="campaign" value="<?php echo $campaign; ?>">
</div>	
<?php
	if(true){
		?>
		<div id="enrolloading" style="z-index:999999;">
				<div class="procenroltitle">Processing Enrolment</div><br><br><br><br>
				<div id="progresswrapper">
					<div class="progress-label mainmessage" style="text-align:center; color:#fff;">Processing... Step 1 of 4</div>
	  				<div id="bookingProgressBar"></div>
	  				<div class="progress-label submessage" style="text-align:center; color:#fff;"><br>This may take a minute or two</div>
	  			</div>
			</div>
			
			<a id="modaltriger" rel="leanModal" name="enrolerrormessage" href="#enrolerrormessage"></a>
		
		<script>
			

			jQuery(function($){
				//$("#enrolloading").show(100);
					$( "#bookingProgressBar" ).progressbar({
			      value: 0
			    });
			    
				
			    
			});
			
			currentProgress = 0;
			timeToWait = 90;
			qtypersecond = 100/timeToWait;
		    qtyperhundredth = qtypersecond/10;
		    qtyperhundredthslow = qtypersecond/100;
			
			function showLoaderBar(){
				currentProgress = 0;
				setProgress(100);
			    
			    window.setTimeout("addTime("+qtypersecond+","+qtyperhundredth+","+timeToWait+");", 50);
			    
			}
			
			
			
			function hideLoaderBar(){
				currentProgress = 100;
				setProgress(100);
			    
			}
			 
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
					jQuery(".progress-label.mainmessage").html("Finalising Enrolment, Please wait.");
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