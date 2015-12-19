<?php
// Template Name: Enrol Now - Thankyou


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

get_header(); global $data; ?>

	<div id="content" style="<?php echo $content_css; ?>">
		<?php while(have_posts()): the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-content enrol-thankyou">
				<?php the_content(); ?>
				
				To help with your training, you can get a free copy of our <a href="https://www.australiawidefirstaid.com.au/files/AWFAmanual-ed1-website.pdf" target="_blank">online First Aid e-manual here.</a><br><br>
				
				<div class="enrolcontentbox" style="max-width: none !important; width: 95%; position: relative !important; display: block !important; float: left !important; margin-right: 15px;">
					<h2 style="margin-bottom:1px;">Your Reference Number: <span class="invoicenumber"><?php echo $_GET['inv']; ?></span></h2>
					<div class="enrol-sidebar-title">Your invoice will arive within the next 24 Hours via Email.<br><br></div>
					<h2 style="margin-bottom:1px;">Your selected course</h2>
					<div><span class="enrol-sidebar-title"><b>Course:</b></span> <span class="enrol-coursename">--</span></div>
					<div><span class="enrol-sidebar-title"><b>Date:</b></span> <span class="enrol-coursedate">--</span></div>
					<div><span class="enrol-sidebar-title"><b>Time:</b></span> <span class="enrol-coursetime">--</span></div> 
					<div><span class="enrol-sidebar-title"><br><b>Location</b><br></span> <span class="enrol-courselocation">--</span></div>
					<div style="display:none;"><span class="enrol-sidebar-title"><b>Total cost:</span> <span class="enrol-coursecost">--</span></div>
					
				</div>
				<br><br><br>
				<div class="enrolcontentbox" style="margin-top:30px; max-width: none !important; width: 95%; position: relative !important; display: block !important; float: left !important; margin-right: 15px;">
					<h2 style="color:#b0163c !important; font-weight:bold !important;">Important Information for all Students</h2>
					<div class="enrol-sidebar-title">
					From 1 January 2015, students enrolling in nationally recognised training in Australia will need a Unique Student Identifier (USI).
					A USI gives students access to their online USI account which is made up of ten numbers and letters.  It will look something like this: 3AW88YH9U5.
					<br><br>
					The exclusive 10 digit number consisting of letters and numbers will be used to link a student’s USI account to their training records. <br><br>
					Obtaining a USI is FREE and easy for students. To create your own USI online go to:  <a href="http://www.usi.gov.au" target="_blank">www.usi.gov.au</a><br><br>
					Once you have your USI please bring it with you to your training course and place it on the enrolment form.<br><br> 
					
					<b>NB.  Under the legislation training organisations can only issue certification when they have received your USI.</b>

					</div>
				</div>
				
			</div>
			<br><br><br><br><br><br><br><br><br><br><br><br><br>
			<br><br><br><br><br><br><br><br><br><br><br><br><br>
			<br><br><br><br><br><br><br><br><br><br><br><br><br>
			<br><br><br><br><br><br><br><br><br><br><br><br><br>
			</div>
		
		<?php endwhile; ?>
	</div>
	<div id="sidebar" style="<?php echo $sidebar_css; ?>"><?php generated_dynamic_sidebar(); ?></div>
	
	<?php
	if(isset($_GET['t'])){
		$txn = str_replace("'","",$_GET['t']);
	} else {
		$txn = 0;
	}
	?>
	
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


	<script>
		var courseData = '<?php echo $courseData; ?>';
		
		jQuery(function($){
			loadCourseDataToSidebar(QueryString.cid);
			
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			ga('require', 'ecommerce', 'ecommerce.js');
		
			ga('ecommerce:addTransaction', {
			  'id': '<?php echo $txn; ?>',                     // Transaction ID. Required.
			  'affiliation': 'Agents Portal',   // Affiliation or store name.
			  'revenue': courseData.courseTotalCost,               // Grand Total.
			  'shipping': '0',                  // Shipping.
			  'tax': '0'                     // Tax.
			});
			
			ga('ecommerce:addItem', {
			  'id': '<?php echo $txn; ?>',                     // Transaction ID. Required.
			  'name': courseData.websiteName,    // Product name. Required.
			  'sku': '',                 // SKU/code.
			  'category': 'Course',         // Category or variation.
			  'price': courseData.courseTotalCost,                 // Unit price.
			  'quantity': '1'                   // Quantity.
			});
			
			ga('ecommerce:send');
		});
	</script>
<?php get_footer(); ?>