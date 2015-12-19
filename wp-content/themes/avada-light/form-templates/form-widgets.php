<?php
// Creating the widget 
class od_cform_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'od_cform_widget', 

// Widget name will appear in UI
__('AWFA Mini Booking Form', 'od_cform_widget_domain'), 

// Widget description
array( 'description' => __( 'Mini booking form displayed in the sidebar of pages and slider area of the home page.', 'od_cform_widget_domain' ), ) 
);
if(isset($_GET['ab'])){
	wp_enqueue_script( 'od_cform_js', get_stylesheet_directory_uri() . '/form-templates/form-V2.10.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.0.0', false );
} else {
	//if(isset($_GET['cd']) && isset($_GET['auth'])){
		if(!isset($_GET['sb'])){
			wp_enqueue_script( 'od_cform_js', get_stylesheet_directory_uri() . '/form-templates/form-V1.95.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.0.0', false );
			wp_enqueue_script( 'leanModal', get_stylesheet_directory_uri() . '/form-templates/js-plugins/jquery.leanModal.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.0.0', false );
		} else {
			wp_enqueue_script( 'od_cform_js', get_stylesheet_directory_uri() . '/form-templates/form-V1.90.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.0.0', false );
		}
	//} else {
	//	wp_enqueue_script( 'od_cform_js', get_stylesheet_directory_uri() . '/form-templates/form-V1.90.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.0.0', false );	
	//}
		
}
wp_enqueue_script( 'jquery.sidr', get_stylesheet_directory_uri() . '/form-templates/js-plugins/sidr/jquery.sidr.min.js', array( 'jquery' ), '1.0.0', false );
wp_enqueue_script( 'jquery.browser', get_stylesheet_directory_uri() . '/form-templates/js-plugins/browser/jquery.browser.min.js', array( 'jquery' ), '1.0.0', false );

}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output
include("sidebar-miniform.php");

//echo __( include("sidebar-miniform.php"), 'od_cform_widget_domain' );
echo $args['after_widget'];
}

//Footer hook is used to load the HTML into the footer hooks so we can load it into the homepage slider area.
public function cd_form_footer_hook(){
 
	echo("<div id='od_cform_footerhooked'>");
	include("sidebar-miniform.php");
	echo('</div><input class="axcelerateCourseQTY" type="hidden" name="axcelerateCourseQTY" value="1" /><input class="axcelerateCourseID" type="hidden" name="courseid" value="" /><input type="hidden" name="form_id" value="axcelerate_booking_form_form" />');
	include("mobile-window-bookonline.php");
	
	
	//if(isset($_GET['cd'])){
		if(!isset($_GET['sb'])){
			wp_enqueue_style( 'od_cform_css', get_stylesheet_directory_uri() . '/form-templates/form-v1.9-css.css' );
		} else {
			wp_enqueue_style( 'od_cform_css', get_stylesheet_directory_uri() . '/form-templates/form-C-css.css' );
		}
	//} else {
	//	wp_enqueue_style( 'od_cform_css', get_stylesheet_directory_uri() . '/form-templates/form-C-css.css' );	
	//}
	
	wp_enqueue_style( 'jquery.sidr', get_stylesheet_directory_uri() . '/form-templates/js-plugins/sidr/stylesheets/jquery.sidr.dark.css' );
	
}

		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'od_cform_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class od_cform_widget ends here

// Register and load the widget
function od_cform_load_widget() {
	register_widget( 'od_cform_widget' );
}
add_action( 'widgets_init', 'od_cform_load_widget' );

//Hook for front page into footer
add_action('wp_footer', 'od_cform_widget::cd_form_footer_hook');

//Not usable at current due to caching
/*
//Browser detection function for enrol form. (available site wide);
add_filter('body_class','browser_body_class');
function browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'browser-lynx';
	elseif($is_gecko) $classes[] = 'browser-gecko';
	elseif($is_opera) $classes[] = 'browser-opera';
	elseif($is_NS4) $classes[] = 'browser-ns4';
	elseif($is_safari) $classes[] = 'browser-safari';
	elseif($is_chrome) $classes[] = 'browser-chrome';
	elseif($is_IE) $classes[] = 'browser-ie';
	else $classes[] = 'browser-unknown';

	if($is_iphone) $classes[] = 'browser-iphone';
	return $classes;
}
*/
?>