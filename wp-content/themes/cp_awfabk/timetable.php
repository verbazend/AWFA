<?php
// Template Name: Timetable

//Loads data from Axcelerate and places it into a JS Format for our JS scripts to then handle.
$courseData = file_get_contents('http://www.australiawidefirstaid.com.au/booking-new/axcelerate/courses/?cid='.urlencode($_GET['cid']));
if($courseData=="false" || $courseData==""){
   //header( 'Location: /enrol-now/course-not-found/' ) ;
}

get_header(); global $data; ?>
	<link href='<?php echo get_stylesheet_directory_uri(); ?>/form-templates/js-plugins/fullcalender/fullcalendar.css' rel='stylesheet' />
	<link href='<?php echo get_stylesheet_directory_uri(); ?>/form-templates/js-plugins/fullcalender/fullcalendar.print.css' rel='stylesheet' media='print' />
	<script src='<?php echo get_stylesheet_directory_uri(); ?>/form-templates/js-plugins/fullcalender/lib/moment.min.js'></script>
	<?php /*<script src='<?php echo get_stylesheet_directory_uri(); ?>/form-templates/js-plugins/fullcalender/lib/jquery.min.js'></script> */ ?>
	<script src='<?php echo get_stylesheet_directory_uri(); ?>/form-templates/js-plugins/fullcalender/lib/jquery-ui.custom.min.js'></script>
	<script src='<?php echo get_stylesheet_directory_uri(); ?>/form-templates/js-plugins/fullcalender/fullcalendar.min.js'></script>
	
	<style>

	body {
		margin: 0;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}

	#script-warning {
		display: none;
		background: #eee;
		border-bottom: 1px solid #ddd;
		padding: 0 10px;
		line-height: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
		color: red;
	}

	#loading {
		display: none;
		position: absolute;
		top: 10px;
		right: 10px;
	}

	#calendar {
		width: 900px;
		margin: 40px auto;
	}
	
	.fc-event {
		color:#fff !important;
	}
	</style>
	
	<script>

		jQuery(document).ready(function($) {
		
			$('#calendar').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				defaultDate: '2014-07-12',
				editable: false,
				events: {
					url: 'http://australiawidefirstaid.com.au/booking-new/axcelerate/courses/?cal=true',
					error: function() {
						$('#script-warning').show();
					}
				},
				loading: function(bool) {
					$('#loading').toggle(bool);
				}
			});
			
		});
	
	</script>
	
	<div id="content" style="<?php echo $content_css; ?>">
		<?php while(have_posts()): the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-content" class="enrol-thankyou">
				<?php the_content(); ?>
				
				<div id='script-warning'>
					<code>php/get-events.php</code> must be running.
				</div>
			
				<div id='loading'>loading...</div>
			
				<div id='calendar'></div>

			</div>
		</div>
		<?php endwhile; ?>
	</div>
	<div id="sidebar" style="<?php echo $sidebar_css; ?>"><?php //generated_dynamic_sidebar(); ?></div>
	
	<script>
		var courseData = '<?php echo $courseData; ?>';
		
		jQuery(function($){
			//loadCourseDataToSidebar(QueryString.cid);
		});
	</script>
<?php get_footer(); ?>