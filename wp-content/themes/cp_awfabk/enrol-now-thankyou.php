<?php
// Template Name: Enrol Now - Thankyou

//Loads data from Axcelerate and places it into a JS Format for our JS scripts to then handle.
$courseData = file_get_contents('http://www.australiawidefirstaid.com.au/booking-new/axcelerate/courses/?cid='.urlencode($_GET['cid']));
if($courseData=="false" || $courseData==""){
   header( 'Location: /enrol-now/course-not-found/' ) ;
}

get_header(); global $data; ?>

	<div id="content" style="<?php echo $content_css; ?>">
		<?php while(have_posts()): the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-content" class="enrol-thankyou">
				<?php the_content(); ?>
				
				<div id="enrolment-selection">
					<h2>Your selected course</h2>
					<div><span class="enrol-sidebar-title">Course:</span> <span class="enrol-coursename">--</span></div>
					<div><span class="enrol-sidebar-title">Date:</span> <span class="enrol-coursedate">--</span></div>
					<div><span class="enrol-sidebar-title">Time:</span> <span class="enrol-coursetime">--</span></div> 
					<div><span class="enrol-sidebar-title">Location:</span> <span class="enrol-courselocation">--</span></div>
					<div><span class="enrol-sidebar-title">Total cost:</span> <span class="enrol-coursecost">--</span></div>
					
				</div>
			</div>
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
	<script>
		var courseData = '<?php echo $courseData; ?>';
		
		jQuery(function($){
			loadCourseDataToSidebar(QueryString.cid);
			
			
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
			  'name': courseData.courseName,    // Product name. Required.
			  'sku': '',                 // SKU/code.
			  'category': 'Course',         // Category or variation.
			  'price': courseData.courseTotalCost,                 // Unit price.
			  'quantity': '1'                   // Quantity.
			});
			
			ga('ecommerce:send');
		});
	</script>
<?php get_footer(); ?>