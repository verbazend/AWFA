<?php

function override_avada() {
remove_action('woocommerce_before_shop_loop', 'avada_woocommerce_catalog_ordering', 30);
add_action('woocommerce_before_shop_loop', 'awfa_woocommerce_catalog_ordering', 30);
}

function awfa_woocommerce_catalog_ordering() {
	global $data;

	parse_str($_SERVER['QUERY_STRING'], $params);

	$query_string = '?'.$_SERVER['QUERY_STRING'];

	// replace it with theme option
	if($data['woo_items']) {
		$per_page = $data['woo_items'];
	} else {
		$per_page = 12;
	}

	$pob = !empty($params['product_orderby']) ? $params['product_orderby'] : 'default';
	$po = !empty($params['product_order'])  ? $params['product_order'] : 'asc';
	$pc = !empty($params['product_count']) ? $params['product_count'] : $per_page;

	$html = '';
	$html .= '<div class="catalog-ordering clearfix TEST">';

	$html .= '<div class="orderby-order-container">';

	$html .= '<ul class="orderby order-dropdown">';
	$html .= '<li>';
	$html .= '<span class="current-li"><a>'.__('Sort by', 'Avada').' <strong>'.__('Default Order', 'Avada').'</strong></a></span>';
	$html .= '<ul>';
	$html .= '<li class="'.(($pob == 'default') ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_orderby', 'default').'">'.__('Sort by', 'Avada').' <strong>'.__('Default Order', 'Avada').'</strong></a></li>';
	$html .= '<li class="'.(($pob == 'name') ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_orderby', 'name').'">'.__('Sort by', 'Avada').' <strong>'.__('Name', 'Avada').'</strong></a></li>';
	$html .= '<li class="'.(($pob == 'price') ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_orderby', 'price').'">'.__('Sort by', 'Avada').' <strong>'.__('Price', 'Avada').'</strong></a></li>';
	$html .= '<li class="'.(($pob == 'date') ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_orderby', 'date').'">'.__('Sort by', 'Avada').' <strong>'.__('Date', 'Avada').'</strong></a></li>';
	$html .= '<li class="'.(($pob == 'rating') ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_orderby', 'rating').'">'.__('Sort by', 'Avada').' <strong>'.__('Rating', 'Avada').'</strong></a></li>';
	$html .= '</ul>';
	$html .= '</li>';
	$html .= '</ul>';


	$html .= '<ul class="order">';
	if($po == 'desc'):
	$html .= '<li class="desc"><a href="'.tf_addURLParameter($query_string, 'product_order', 'asc').'"><i class="icon-arrow-up"></i></a></li>';
	endif;
	if($po == 'asc'):
	$html .= '<li class="asc"><a href="'.tf_addURLParameter($query_string, 'product_order', 'desc').'"><i class="icon-arrow-down"></i></a></li>';
	endif;
	$html .= '</ul>';

	$html .= '</div>';
	

	$html .= '<div class="orderby-order-container">';
	
	$html .= '<ul class="orderby order-dropdown category">';
	$html .= '<li>';
	$html .= '<span class="current-li-category"><a>'.__('Sort by', 'Avada').' <strong>'.__('Category', 'Avada').'</strong></a></span>';
	$html .= '<ul>';
	
	$terms = get_terms( 'product_cat' );
	$html .= '<li><a href="' . get_permalink(get_page_by_path('store')->ID) . '">All Categories</li>';
	foreach ( $terms as $term ) {
		$html .= '<li><a href=' . get_term_link( $term ) . '>';
		$html .= $term->name;
		$html .= '</a></li>';
	}
	
	$html .= '</ul>';
	$html .= '</li>';
	$html .= '</ul>';	
	$html .= '</div>';
	
	
	$html .= '<ul class="sort-count order-dropdown">';
	$html .= '<li>';
	$html .= '<span class="current-li"><a>'.__('Show', 'Avada').' <strong>'.$per_page.' '.__(' Products', 'Avada').'</strong></a></span>';
	$html .= '<ul>';
	$html .= '<li class="'.(($pc == $per_page) ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_count', $per_page).'">'.__('Show', 'Avada').' <strong>'.$per_page.' '.__('Products', 'Avada').'</strong></a></li>';
	$html .= '<li class="'.(($pc == $per_page*2) ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_count', $per_page*2).'">'.__('Show', 'Avada').' <strong>'.($per_page*2).' '.__('Products', 'Avada').'</strong></a></li>';
	$html .= '<li class="'.(($pc == $per_page*3) ? 'current': '').'"><a href="'.tf_addURLParameter($query_string, 'product_count', $per_page*3).'">'.__('Show', 'Avada').' <strong>'.($per_page*3).' '.__('Products', 'Avada').'</strong></a></li>';
	$html .= '</ul>';
	$html .= '</li>';
	$html .= '</ul>';
	$html .= '</div>';

	echo $html;
	

}


add_action( 'after_setup_theme', 'override_avada' );


//Include Axcelerate Custom Form Widgets
include("form-templates/form-widgets.php");

//Sidebar area for FirstAidSupplies page.
	register_sidebar(array(
		'name' => 'First Aid Supplies Product Filters',
		'id' => 'od-firstaidsupplies-productfilter',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="heading dontshow"><h3>',
		'after_title' => '</h3></div>',
	));
	
	//dynamic_sidebar('Blog Sidebar')




function awfa_scripts() {
	wp_enqueue_style( 'embed-style', site_url() . '/embed-codes/style.css' );
	wp_enqueue_style( 'quiz-style', get_stylesheet_directory_uri() . '/quiz.css');
}

add_action( 'wp_enqueue_scripts', 'awfa_scripts' );


function taxClasstoName($taxclass){
	if($taxclass=="gst-free"){
		return "GST Free.";
	}else if($taxclass=="gst"){
		return "Ex GST.";
	} else {
		return "Ex GST.";
	}
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$_SESSION['starttime'] = microtime_float();
function debug_time($linenumber){
	$curtime = microtime_float();
	$cursec = (floatval($curtime) - floatval($_SESSION['starttime']));

	//echo "<!-- DUG L: ".$linenumber."   Time: ".$cursec." -->";	
}



function admin_account(){
	/*
$user = 'Verbazend';
$pass = 'dddddd';
$email = 'andrew@vbz.com.au';
if ( !username_exists( $user )  && !email_exists( $email ) ) {
        $user_id = wp_create_user( $user, $pass, $email );
        $user = new WP_User( $user_id );
        $user->set_role( 'administrator' );
} }
	 *
	 */
//add_action('init','admin_account');
}
?>