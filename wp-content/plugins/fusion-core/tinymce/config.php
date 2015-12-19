<?php
/*-----------------------------------------------------------------------------------*/
/*	Default Options
/*-----------------------------------------------------------------------------------*/

// Number of posts array
function fusion_shortcodes_range ( $range, $all = true, $default = false, $range_start = 1 ) {
	if($all) {
		$number_of_posts['-1'] = 'All';
	}

	if($default) {
		$number_of_posts[''] = 'Default';
	}

	foreach(range($range_start, $range) as $number) {
		$number_of_posts[$number] = $number;
	}

	return $number_of_posts;
}

// Taxonomies
function fusion_shortcodes_categories ( $taxonomy, $empty_choice = false ) {
	if($empty_choice == true) {
		$post_categories[''] = 'Default';
	}

	$get_categories = get_categories('hide_empty=0&taxonomy=' . $taxonomy);

	if( ! array_key_exists('errors', $get_categories) ) {
		if( $get_categories && is_array($get_categories) ) {
			foreach ( $get_categories as $cat ) {
				$post_categories[$cat->slug] = $cat->name;
			}
		}

		if(isset($post_categories)) {
			return $post_categories;
		}
	}
}

$choices = array('yes' => 'Yes', 'no' => 'No');
$reverse_choices = array('no' => 'No', 'yes' => 'Yes');
$dec_numbers = array('0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5', '0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1' => '1' );

// Fontawesome icons list
$pattern = '/\.(icon-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
$fontawesome_path = FUSION_TINYMCE_DIR . '/css/font-awesome.css';
if( file_exists( $fontawesome_path ) ) {
	@$subject = file_get_contents($fontawesome_path);
}

preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

$icons = array();

foreach($matches as $match){
	$icons[$match[1]] = $match[2];
}

$checklist_icons = array ( 'icon-check' => '\f00c', 'icon-star' => '\f006', 'icon-angle-right' => '\f105', 'icon-asterisk' => '\f069', 'icon-remove' => '\f00d', 'icon-plus' => '\f067' );

/*-----------------------------------------------------------------------------------*/
/*	Shortcode Selection Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['shortcode-generator'] = array(
	'no_preview' => true,
	'params' => array(),
	'shortcode' => '',
	'popup_title' => ''
);

/*-----------------------------------------------------------------------------------*/
/*	Alert Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['alert'] = array(
	'no_preview' => true,
	'params' => array(

		'type' => array(
			'type' => 'select',
			'label' => __( 'Alert Type', 'fusion-core' ),
			'desc' => __( 'Select the type of alert message', 'fusion-core' ),
			'options' => array(
				'general' => 'General',
				'error' => 'Error',
				'success' => 'Success',
				'notice' => 'Notice',
			)
		),
		'content' => array(
			'std' => 'Your Content Goes Here',
			'type' => 'textarea',
			'label' => __( 'Alert Content', 'fusion-core' ),
			'desc' => __( 'Insert the alert\'s content', 'fusion-core' ),
		),
		'animation_type' => array(
			'type' => 'select',
			'label' => __( 'Animation Type', 'fusion-core' ),
			'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
			'options' => array(
				'0' => 'None',
				'bounce' => 'Bounce',
				'fade' => 'Fade',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'slide' => 'Slide',
			)
		),
		'animation_direction' => array(
			'type' => 'select',
			'label' => __( 'Direction of Animation', 'fusion-core' ),
			'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
			'options' => array(
				'down' => 'Down',
				'left' => 'Left',
				'right' => 'Right',
				'up' => 'Up',
			)
		),
		'animation_speed' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Speed of Animation', 'fusion-core' ),
			'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
			'options' => $dec_numbers,
		)
	),
	'shortcode' => '[alert type="{{type}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"]{{content}}[/alert]',
	'popup_title' => __( 'Alert Shortcode', 'fusion-core' )
);


/*-----------------------------------------------------------------------------------*/
/*	Blog Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['blog'] = array(
	'no_preview' => true,
	'params' => array(

		'layout' => array(
			'type' => 'select',
			'label' => __( 'Blog Layout', 'fusion-core' ),
			'desc' => __( 'Select the layout for the blog shortcode', 'fusion-core' ),
			'options' => array(
				'large' => 'Large',
				'medium' => 'Medium',
				'large alternate' => 'Large Alternate',
				'medium alternate' => 'Medium Alternate',
				'grid' => 'Grid',
				'timeline' => 'Timeline'
			)
		),
		'posts_per_page' => array(
			'type' => 'select',
			'label' => __( 'Posts Per Page', 'fusion-core' ),
			'desc' => __( 'Select number of posts per page', 'fusion-core' ),
			'options' => fusion_shortcodes_range( 25, true, true )
		),
		'cat_slug' => array(
			'type' => 'multiple_select',
			'label' => __( 'Categories', 'fusion-core' ),
			'desc' => __( 'Select a category or leave blank for all', 'fusion-core' ),
			'options' => fusion_shortcodes_categories( 'category' )
		),
		'title' => array(
			'type' => 'select',
			'label' => __( 'Show Title', 'fusion-core' ),
			'desc' =>  __( 'Display the post title below the featured image', 'fusion-core' ),
			'options' => $choices
		),
		'thumbnail' => array(
			'type' => 'select',
			'label' => __( 'Show Thumbnail', 'fusion-core' ),
			'desc' =>  __( 'Display the post featured image', 'fusion-core' ),
			'options' => $choices
		),
		'excerpt' => array(
			'type' => 'select',
			'label' => __( 'Show Excerpt', 'fusion-core' ),
			'desc' =>  __( 'Show excerpt or choose "no" for full content', 'fusion-core' ),
			'options' => $choices
		),
		'excerpt_words' => array(
			'std' => 35,
			'type' => 'select',
			'label' => __( 'Number of words in Excerpt', 'fusion-core' ),
			'desc' =>  __( 'Insert the number of words you want to show in the excerpt.', 'fusion-core' ),
			'options' => fusion_shortcodes_range( 60, false )
		),
		'meta_all' => array(
			'type' => 'select',
			'label' => __( 'Show Meta Info', 'fusion-core' ),
			'desc' =>  __( 'Choose to show all meta data', 'fusion-core' ),
			'options' => $choices
		),
		'meta_author' => array(
			'type' => 'select',
			'label' => __( 'Show Author Name', 'fusion-core' ),
			'desc' =>  __( 'Choose to show the author', 'fusion-core' ),
			'options' => $choices
		),
		'meta_categories' => array(
			'type' => 'select',
			'label' => __( 'Show Categories', 'fusion-core' ),
			'desc' =>  __( 'Choose to show the categories', 'fusion-core' ),
			'options' => $choices
		),
		'meta_comments' => array(
			'type' => 'select',
			'label' => __( 'Show Comment Count', 'fusion-core' ),
			'desc' =>  __( 'Choose to show the comments', 'fusion-core' ),
			'options' => $choices
		),
		'meta_date' => array(
			'type' => 'select',
			'label' => __( 'Show Date', 'fusion-core' ),
			'desc' =>  __( 'Choose to show the date', 'fusion-core' ),
			'options' => $choices
		),
		'meta_link' => array(
			'type' => 'select',
			'label' => __( 'Show Read More Link', 'fusion-core' ),
			'desc' =>  __( 'Choose to show the link', 'fusion-core' ),
			'options' => $choices
		),
		'paging' => array(
			'type' => 'select',
			'label' => __( 'Show Pagination', 'fusion-core' ),
			'desc' =>  __( 'Show numerical pagination boxes', 'fusion-core' ),
			'options' => $choices
		),
		'scrolling' => array(
			'type' => 'select',
			'label' => __( 'Infinite Scrolling', 'fusion-core' ),
			'desc' =>  __( 'Choose the type of scrolling', 'fusion-core' ),
			'options' => array(
				'pagination' => 'Pagination',
				'infinite' => 'Infinite Scrolling'
			)
		),
		'blog_grid_columns' => array(
			'type' => 'select',
			'label' => __( 'Grid Layout # of Columns', 'fusion-core' ),
			'desc' => __( 'Full width pages only; select whether to display the grid layout in 3 or 4 column.', 'fusion-core' ),
			'options' => array(
				'3' => '3',
				'4' => '4'
			)
		),
		'strip_html' => array(
			'type' => 'select',
			'label' => __( 'Strip HTML from Posts Content', 'fusion-core' ),
			'desc' =>  __( 'Strip HTML from the post excerpt', 'fusion-core' ),
			'options' => $choices
		)
	),
	'shortcode' => '[blog number_posts="{{posts_per_page}}" cat_slug="{{cat_slug}}" title="{{title}}" thumbnail="{{thumbnail}}" excerpt="{{excerpt}}" excerpt_words="{{excerpt_words}}" meta_all="{{meta_all}}" meta_author="{{meta_author}}" meta_categories="{{meta_categories}}" meta_comments="{{meta_comments}}" meta_date="{{meta_date}}" meta_link="{{meta_link}}" paging="{{paging}}" scrolling="{{scrolling}}" strip_html="{{strip_html}}" blog_grid_columns="{{blog_grid_columns}}" layout="{{layout}}"][/blog]',
	'popup_title' => __( 'Blog Shortcode', 'fusion-core')
);

/*-----------------------------------------------------------------------------------*/
/*	Button Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['button'] = array(
	'no_preview' => true,
	'params' => array(

		'url' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Button URL', 'fusion-core'),
			'desc' => __('Add the button\'s url ex: http://example.com', 'fusion-core')
		),
		'style' => array(
			'type' => 'select',
			'label' => __('Button Style', 'fusion-core'),
			'desc' => __('Select the button\'s style, the button\'s colour', 'fusion-core'),
			'options' => array(
				'default' => 'Default',
				'green' => 'Green',
				'darkgreen' => 'Dark Green',
				'orange' => 'Orange',
				'blue' => 'Blue',
				'red' => 'Red',
				'pink' => 'Pink',
				'darkgray' => 'Dark Gray',
				'lightgray' => 'Light Gray',
			)
		),
		'size' => array(
			'type' => 'select',
			'label' => __('Button Size', 'fusion-core'),
			'desc' => __('Select the button\'s size', 'fusion-core'),
			'options' => array(
				'small' => 'Small',
				'large' => 'Large'
			)
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Button Target', 'fusion-core'),
			'desc' => __('_self = open in same window <br />_blank = open in new window', 'fusion-core'),
			'options' => array(
				'_self' => '_self',
				'_blank' => '_blank'
			)
		),
		'title' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Button Title attribute', 'fusion-core'),
			'desc' => __('Set a title attribute for the link the button consists of', 'fusion-core'),
		),
		'content' => array(
			'std' => 'Button Text',
			'type' => 'text',
			'label' => __('Button\'s Text', 'fusion-core'),
			'desc' => __('Add the text that will display in the button', 'fusion-core'),
		),
		'animation_type' => array(
			'type' => 'select',
			'label' => __( 'Animation Type', 'fusion-core' ),
			'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
			'options' => array(
				'0' => 'None',
				'bounce' => 'Bounce',
				'fade' => 'Fade',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'slide' => 'Slide',
			)
		),
		'animation_direction' => array(
			'type' => 'select',
			'label' => __( 'Direction of Animation', 'fusion-core' ),
			'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
			'options' => array(
				'down' => 'Down',
				'left' => 'Left',
				'right' => 'Right',
				'up' => 'Up',
			)
		),
		'animation_speed' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Speed of Animation', 'fusion-core' ),
			'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
			'options' => $dec_numbers,
		)
	),
	'shortcode' => '[button link="{{url}}" color="{{style}}" size="{{size}}" target="{{target}}" title="{{title}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"]{{content}}[/button]',
	'popup_title' => __('Button Shortcode', 'fusion-core')
);

/*-----------------------------------------------------------------------------------*/
/*	Checklist Config
/*-----------------------------------------------------------------------------------*/
$fusion_shortcodes['checklist'] = array(
	'params' => array(

		'icon' => array(
			'type' => 'iconpicker',
			'label' => __('Select Icon', 'fusion-core'),
			'desc' => __('Click an icon to select, click again to deselect', 'fusion-core'),
			'options' => $icons
		),
		'iconcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Icon Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'circle' => array(
			'type' => 'select',
			'label' => __('Icon in Circle', 'fusion-core'),
			'desc' => __('Choose to display the icon in a circle', 'fusion-core'),
			'options' => $choices
		),
	),

	'shortcode' => '[checklist icon="{{icon}}" iconcolor="{{iconcolor}}" circle="{{circle}}"]&lt;ul&gt;{{child_shortcode}}&lt;/ul&gt;[/checklist]',
	'popup_title' => __('Checklist Shortcode', 'fusion-core'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'content' => array(
				'std' => 'Your Content Goes Here',
				'type' => 'textarea',
				'label' => __( 'List Item Content', 'fusion-core' ),
				'desc' => __( 'Add list item content', 'fusion-core' ),
			),
		),
		'shortcode' => '&lt;li&gt;{{content}}&lt;/li&gt;',
		'clone_button' => __('Add New List Item', 'fusion-core')
	)
);


/*-----------------------------------------------------------------------------------*/
/*	Client Slider Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['clientslider'] = array(
	'params' => array(),
	'shortcode' => '[clients]{{child_shortcode}}[/clients]', // as there is no wrapper shortcode
	'popup_title' => __('Client Slider Shortcode', 'fusion-core'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'url' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Client Website Link', 'fusion-core'),
				'desc' => __('Add the url to client\'s website <br />ex: http://example.com', 'fusion-core')
			),
			'target' => array(
				'type' => 'select',
				'label' => __('Link Target', 'fusion-core'),
				'desc' => __('_self = open in same window <br /> _blank = open in new window', 'fusion-core'),
				'options' => array(
					'_self' => '_self',
					'_blank' => '_blank'
				)
			),
			'image' => array(
				'type' => 'uploader',
				'label' => __('Client Image', 'fusion-core'),
				'desc' => __('Upload the client image', 'fusion-core'),
			),
			'alt' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Image Alt Text', 'fusion-core'),
				'desc' => 'The alt attribute provides alternative information if an image cannot be viewed'
			)
		),
		'shortcode' => '[client link="{{url}}" linktarget="{{target}}" image="{{image}}" alt="{{alt}}"]',
		'clone_button' => __('Add New Client Image', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Content Boxes Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['contentboxes'] = array(
	'params' => array(

		'layout' => array(
			'type' => 'select',
			'label' => __( 'Box Layout', 'fusion-core' ),
			'desc' => __('Select the layout for the content box', 'fusion-core'),
			'options' => array(
				'icon-with-title' => 'Icon beside Title',
				'icon-on-top' => 'Icon on Top of Title',
				'icon-on-side' => 'Icon beside Title and Content aligned with Title',
				'icon-boxed' => 'Icon Boxed',

			)
		),
		'backgroundcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Content Box Background Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'iconcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Icon Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'circlecolor' => array(
			'type' => 'colorpicker',
			'label' => __('Icon Circle Background Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'circlebordercolor' => array(
			'type' => 'colorpicker',
			'label' => __('Icon Circle Border Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
	),
	'shortcode' => '[content_boxes layout="{{layout}}" iconcolor="{{iconcolor}}" circlecolor="{{circlecolor}}" circlebordercolor="{{circlebordercolor}}" backgroundcolor="{{backgroundcolor}}"]{{child_shortcode}}[/content_boxes]', // as there is no wrapper shortcode
	'popup_title' => __('Content Boxes Shortcode', 'fusion-core'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Title', 'fusion-core')
			),
			'icon' => array(
				'type' => 'iconpicker',
				'label' => __('Icon', 'fusion-core'),
				'desc' => __('Click an icon to select, click again to deselect', 'fusion-core'),
				'options' => $icons
			),
			'image' => array(
				'type' => 'uploader',
				'label' => __('Icon Image', 'fusion-core'),
				'desc' => 'To upload your own icon image, deselect the icon above and then upload your icon image'
			),
			'image_width' => array(
				'std' => 35,
				'type' => 'text',
				'label' => __('Icon Image Width', 'fusion-core'),
				'desc' => 'If using an icon image, specify the image width in pixels but do not add px, ex: 35'
			),
			'image_height' => array(
				'std' => 35,
				'type' => 'text',
				'label' => __('Icon Image Height', 'fusion-core'),
				'desc' => 'If using an icon image, specify the image height in pixels but do not add px, ex: 35'
			),
			'link' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Read More Link Url', 'fusion-core'),
				'desc' => 'Add the link\'s url ex: http://example.com'

			),
			'linktext' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Read More Link Text', 'fusion-core'),
				'desc' => 'Insert the text to display as the link'

			),
			'target' => array(
				'type' => 'select',
				'label' => __('Read More Link Target', 'fusion-core'),
				'desc' => __('_self = open in same window <br /> _blank = open in new window', 'fusion-core'),
				'options' => array(
					'_self' => '_self',
					'_blank' => '_blank'
				)
			),
			'content' => array(
				'std' => 'Your Content Goes Here',
				'type' => 'textarea',
				'label' => __( 'Content Box Content', 'fusion-core' ),
				'desc' => __( 'Add content for content box', 'fusion-core' ),
			),
			'animation_type' => array(
				'type' => 'select',
				'label' => __( 'Animation Type', 'fusion-core' ),
				'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
				'options' => array(
					'0' => 'None',
					'bounce' => 'Bounce',
					'fade' => 'Fade',
					'flash' => 'Flash',
					'shake' => 'Shake',
					'slide' => 'Slide',
				)
			),
			'animation_direction' => array(
				'type' => 'select',
				'label' => __( 'Direction of Animation', 'fusion-core' ),
				'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
				'options' => array(
					'down' => 'Down',
					'left' => 'Left',
					'right' => 'Right',
					'up' => 'Up',
				)
			),
			'animation_speed' => array(
				'type' => 'select',
				'std' => '',
				'label' => __( 'Speed of Animation', 'fusion-core' ),
				'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
				'options' => $dec_numbers,
			)
		),
		'shortcode' => '[content_box title="{{title}}" icon="{{icon}}" image="{{image}}" image_width="{{image_width}}" image_height="{{image_height}}" link="{{link}}" linktarget="{{target}}" linktext="{{linktext}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"]{{content}}[/content_box]',
		'clone_button' => __('Add New Content Box', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Counters Circle Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['counterscircle'] = array(
	'params' => array(

	),
	'shortcode' => '[counters_circle]{{child_shortcode}}[/counters_circle]', // as there is no wrapper shortcode
	'popup_title' => __('Counters Circle Shortcode', 'fusion-core'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'value' => array(
				'type' => 'select',
				'label' => __('Filled Area Percentage', 'fusion-core'),
				'desc' => __('From 1% to 100%', 'fusion-core'),
				'options' => fusion_shortcodes_range(100, false)
			),
			'filledcolor' => array(
				'type' => 'colorpicker',
				'label' => __('Filled Color', 'fusion-core'),
				'desc' => __('Color for filled in area', 'fusion-core')
			),
			'unfilledcolor' => array(
				'type' => 'colorpicker',
				'label' => __('Unfilled Color', 'fusion-core'),
				'desc' => __('Color for unfilled area', 'fusion-core')
			),
			'size' => array(
				'std' => '220',
				'type' => 'text',
				'label' => __( 'Size of the Counter', 'fusion-core' ),
				'desc' => __( 'Insert size of the counter in px. ex: 220', 'fusion-core' ),
			),
			'speed' => array(
				'std' => '1500',
				'type' => 'text',
				'label' => __( 'Animation Speed', 'fusion-core' ),
				'desc' => __( 'Insert animation speed in milliseconds', 'fusion-core' ),
			),
			'content' => array(
				'std' => 'Text',
				'type' => 'text',
				'label' => __( 'Counter Circle Text', 'fusion-core' ),
				'desc' => __( 'Insert text for counter circle box, keep it short', 'fusion-core' ),
			)
		),
		'shortcode' => '[counter_circle filledcolor="{{filledcolor}}" unfilledcolor="{{unfilledcolor}}" size="{{size}}" speed="{{speed}}" value="{{value}}"]{{content}}[/counter_circle]',
		'clone_button' => __('Add New Counter Circle', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Counters Box Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['countersbox'] = array(
	'params' => array(

	),
	'shortcode' => '[counters_box]{{child_shortcode}}[/counters_box]', // as there is no wrapper shortcode
	'popup_title' => __('Counters Box Shortcode', 'fusion-core'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'value' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Counter Value', 'fusion-core'),
				'desc' => __('The number to which the counter will animate', 'fusion-core')
			),
			'unit' => array(
				'std' => '',
				'type' => 'text',
				'label' => __( 'Counter Box Unit', 'fusion-core' ),
				'desc' => __( 'Insert a unit for the counter. ex %', 'fusion-core' ),
			),
			'content' => array(
				'std' => 'Text',
				'type' => 'text',
				'label' => __( 'Counter Box Text', 'fusion-core' ),
				'desc' => __( 'Insert text for counter box', 'fusion-core' ),
			)
		),
		'shortcode' => '[counter_box value="{{value}}" unit="{{unit}}"]{{content}}[/counter_box]',
		'clone_button' => __('Add New Counter Box', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Dropcap Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['dropcap'] = array(
	'no_preview' => true,
	'params' => array(

		'content' => array(
			'std' => 'A',
			'type' => 'textarea',
			'label' => __( 'Dropcap Letter', 'fusion-core' ),
			'desc' => __( 'Add the letter to be used as dropcap', 'fusion-core' ),
		)

	),
	'shortcode' => '[dropcap]{{content}}[/dropcap]',
	'popup_title' => __( 'Dropcap Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Flexslider Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['flexslider'] = array(
	'no_preview' => true,
	'params' => array(

		'type' => array(
			'type' => 'select',
			'label' => __('Layout', 'fusion-core'),
			'desc' => __('Choose a layout style for Flexslider', 'fusion-core'),
			'options' => array(
				'posts' => 'Posts with Title',
				'posts-with-excerpt' => 'Posts with Title and Excerpt',
				'attachments' => 'Attachment Layout, Only Images Attached to Post/Page'
			)
		),
		'excerpt' => array(
			'std' => 35,
			'type' => 'select',
			'label' => __('Excerpt Number of Words', 'fusion-core'),
			'desc' => __('Insert the number of words you want to show in the excerpt.', 'fusion-core'),
			'options' => fusion_shortcodes_range( 50, false)
		),
		'category' => array(
			'std' => 35,
			'type' => 'select',
			'label' => __('Category', 'fusion-core'),
			'desc' => __('Select a category of posts to display', 'fusion-core'),
			'options' => fusion_shortcodes_categories( 'category', true )
		),
		'limit' => array(
			'std' => 3,
			'type' => 'select',
			'label' => __('Number of Slides', 'fusion-core'),
			'desc' => __('Select the number of slides to display', 'fusion-core'),
			'options' => fusion_shortcodes_range( 10, false )
		),
		'lightbox' => array(
			'type' => 'select',
			'label' => __('Lightbox on Click', 'fusion-core'),
			'desc' => __('Only works on attachment layout', 'fusion-core'),
			'options' => $choices
		),
		'image' => array(
			'type' => 'gallery',
			'label' => __('Attach Images to Post/Page Gallery', 'fusion-core'),
			'desc' => 'Only works for attachments layout'
		),
	),
	'shortcode' => '[flexslider layout="{{type}}" excerpt="{{excerpt}}" category="{{category}}" limit="{{limit}}" id="" lightbox="{{lightbox}}"][/flexslider]',
	'popup_title' => __( 'Flexslider Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	FontAwesome Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['fontawesome'] = array(
	'no_preview' => true,
	'params' => array(

		'icon' => array(
			'type' => 'iconpicker',
			'label' => __('Select Icon', 'fusion-core'),
			'desc' => __('Click an icon to select, click again to deselect', 'fusion-core'),
			'options' => $icons
		),
		'circle' => array(
			'type' => 'select',
			'label' => __('Icon in Circle', 'fusion-core'),
			'desc' => 'Choose to display the icon in a circle',
			'options' => $choices
		),
		'size' => array(
			'type' => 'select',
			'label' => __('Size of Icon', 'fusion-core'),
			'desc' => 'Select the size of the icon',
			'options' => array(
				'large' => 'Large',
				'medium' => 'Medium',
				'small' => 'Small'
			)
		),
		'iconcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Icon Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'circlecolor' => array(
			'type' => 'colorpicker',
			'label' => __('Icon Circle Background Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'circlebordercolor' => array(
			'type' => 'colorpicker',
			'label' => __('Icon Circle Border Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'animation_type' => array(
			'type' => 'select',
			'label' => __( 'Animation Type', 'fusion-core' ),
			'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
			'options' => array(
				'0' => 'None',
				'bounce' => 'Bounce',
				'fade' => 'Fade',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'slide' => 'Slide',
			)
		),
		'animation_direction' => array(
			'type' => 'select',
			'label' => __( 'Direction of Animation', 'fusion-core' ),
			'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
			'options' => array(
				'down' => 'Down',
				'left' => 'Left',
				'right' => 'Right',
				'up' => 'Up',
			)
		),
		'animation_speed' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Speed of Animation', 'fusion-core' ),
			'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
			'options' => $dec_numbers,
		)
	),
	'shortcode' => '[fontawesome icon="{{icon}}" circle="{{circle}}" size="{{size}}" iconcolor="{{iconcolor}}" circlecolor="{{circlecolor}}" circlebordercolor="{{circlebordercolor}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"]',
	'popup_title' => __( 'FontAwesome Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Fullwidth Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['fullwidth'] = array(
	'no_preview' => true,
	'params' => array(
		'backgroundcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Background Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'backgroundimage' => array(
			'type' => 'uploader',
			'label' => __('Backgrond Image', 'fusion-core'),
			'desc' => 'Upload an image to display in the background'
		),
		'backgroundrepeat' => array(
			'type' => 'select',
			'label' => __('Background Repeat', 'fusion-core'),
			'desc' => 'Choose how the background image repeats.',
			'options' => array(
				'no-repeat' => 'No Repeat',
				'repeat' => 'Repeat Vertically and Horizontally',
				'repeat-x' => 'Repeat Horizontally',
				'repeat-y' => 'Repeat Vertically'
			)
		),
		'backgroundposition' => array(
			'type' => 'select',
			'label' => __('Background Position', 'fusion-core'),
			'desc' => 'Choose the postion of the background image',
			'options' => array(
				'left top' => 'Left Top',
				'left center' => 'Left Center',
				'left bottom' => 'Left Bottom',
				'right top' => 'Right Top',
				'right center' => 'Right Center',
				'right bottom' => 'Right Bottom',
				'center top' => 'Center Top',
				'center center' => 'Center Center',
				'center bottom' => 'Center Bottom'
			)
		),
		'backgroundattachment' => array(
			'type' => 'select',
			'label' => __('Background Scroll', 'fusion-core'),
			'desc' => 'Choose how the background image scrolls',
			'options' => array(
				'scroll' => 'Scroll: background scrolls along with the element',
				'fixed' => 'Fixed: background is fixed giving a parallax effect',
				'local' => 'Local: background scrolls along with the element\'s contents'
			)
		),
		'bordersize' => array(
			'std' => 0,
			'type' => 'select',
			'label' => __( 'Border Size', 'fusion-core' ),
			'desc' => __( 'In pixels', 'fusion-core' ),
			'options' => fusion_shortcodes_range( 10, false, false ,0 )
		),
		'bordercolor' => array(
			'type' => 'colorpicker',
			'label' => __('Border Color', 'fusion-core'),
			'desc' => __('Leave blank for default', 'fusion-core')
		),
		'paddingtop' => array(
			'std' => 20,
			'type' => 'select',
			'label' => __( 'Padding Top', 'fusion-core' ),
			'desc' => __( 'In pixels', 'fusion-core' ),
			'options' => fusion_shortcodes_range( 100, false )
		),
		'paddingbottom' => array(
			'std' => 20,
			'type' => 'select',
			'label' => __( 'Padding Bottom', 'fusion-core' ),
			'desc' => __( 'In pixels', 'fusion-core' ),
			'options' => fusion_shortcodes_range( 100, false )
		),
		'content' => array(
			'std' => 'Your Content Goes Here',
			'type' => 'textarea',
			'label' => __( 'Content', 'fusion-core' ),
			'desc' => __( 'Add content', 'fusion-core' ),
		),
	),
	'shortcode' => '[fullwidth backgroundcolor="{{backgroundcolor}}" backgroundimage="{{backgroundimage}}" backgroundrepeat="{{backgroundrepeat}}" backgroundposition="{{backgroundposition}}" backgroundattachment="{{backgroundattachment}}" bordersize="{{bordersize}}px" bordercolor="{{bordercolor}}" paddingTop="{{paddingtop}}px" paddingBottom="{{paddingbottom}}px"]{{content}}[/fullwidth]',
	'popup_title' => __( 'Fullwidth Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Google Map Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['googlemap'] = array(
	'no_preview' => true,
	'params' => array(

		'type' => array(
			'type' => 'select',
			'label' => __('Map Type', 'fusion-core'),
			'desc' => __('Select the type of google map to display', 'fusion-core'),
			'options' => array(
				'roadmap' => 'Roadmap',
				'satellite' => 'Satellite',
				'hybrid' => 'Hybrid',
				'terrain' => 'Terrain'
			)
		),
		'width' => array(
			'std' => '100%',
			'type' => 'text',
			'label' => __('Map Width', 'fusion-core'),
			'desc' => __('Map Width in Percentage or Pixels', 'fusion-core')
		),
		'height' => array(
			'std' => '300px',
			'type' => 'text',
			'label' => __('Map Height', 'fusion-core'),
			'desc' => __('Map Height in Percentage or Pixels', 'fusion-core')
		),
		'zoom' => array(
			'std' => 14,
			'type' => 'select',
			'label' => __('Zoom Level', 'fusion-core'),
			'desc' => 'Higher number will be more zoomed in.',
			'options' => fusion_shortcodes_range( 25, false )
		),
		'scrollwheel' => array(
			'type' => 'select',
			'label' => __('Scrollwheel on Map', 'fusion-core'),
			'desc' => 'Enable zooming using a mouse\'s scroll wheel',
			'options' => $choices
		),
		'scale' => array(
			'type' => 'select',
			'label' => __('Show Scale Control on Map', 'fusion-core'),
			'desc' => 'Display the map scale',
			'options' => $choices
		),
		'zoom_pancontrol' => array(
			'type' => 'select',
			'label' => __('Show Pan Control on Map', 'fusion-core'),
			'desc' => 'Displays pan control button',
			'options' => $choices
		),
		'content' => array(
			'std' => '',
			'type' => 'textarea',
			'label' => __( 'Address', 'fusion-core' ),
			'desc' => __( 'Add address to the location which will show up on map. For multiple addresses, separate addresses by using the | symbol. <br />ex: Address 1|Address 2|Address 3', 'fusion-core' ),
		)
	),
	'shortcode' => '[map address="{{content}}" type="{{type}}" width="{{width}}" height="{{height}}" zoom="{{zoom}}" scrollwheel="{{scrollwheel}}" scale="{{scale}}" zoom_pancontrol="{{zoom_pancontrol}}"][/map]',
	'popup_title' => __( 'Google Map Shortcode', 'fusion-core' ),
);

/*-----------------------------------------------------------------------------------*/
/*	Highlight Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['highlight'] = array(
	'no_preview' => true,
	'params' => array(

		'color' => array(
			'type' => 'colorpicker',
			'label' => __('Highlight Color', 'fusion-core'),
			'desc' => __('Pick a highlight color', 'fusion-core')
		),
		'content' => array(
			'std' => 'Your Content Goes Here',
			'type' => 'textarea',
			'label' => __( 'Content to Higlight', 'fusion-core' ),
			'desc' => __( 'Add your content to be highlighted', 'fusion-core' ),
		)

	),
	'shortcode' => '[highlight color="{{color}}"]{{content}}[/highlight]',
	'popup_title' => __( 'Highlight Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Image Frame Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['imageframe'] = array(
	'no_preview' => true,
	'params' => array(
		'style' => array(
			'type' => 'select',
			'label' => __('Frame style type', 'fusion-core'),
			'desc' => __('Selected the frame style type', 'fusion-core'),
			'options' => array(
				'none' => 'None',
				'border' => 'Border',
				'glow' => 'Glow',
				'dropshadow' => 'Drop Shadow',
				'bottomshadow' => 'Bottom Shadow'
			)
		),
		'bordercolor' => array(
			'type' => 'colorpicker',
			'label' => __( 'Border Color', 'fusion-core' ),
			'desc' => __( 'For border style type only', 'fusion-core' ),
		),
		'bordersize' => array(
			'std' => 0,
			'type' => 'select',
			'label' => __( 'Border Size', 'fusion-core' ),
			'desc' => __( 'In pixels, only for border style type', 'fusion-core' ),
			'options' => fusion_shortcodes_range( 10, false, false, 0 )
		),
		'stylecolor' => array(
			'type' => 'colorpicker',
			'label' => __( 'Style Color', 'fusion-core' ),
			'desc' => __( 'For all style types except border', 'fusion-core' ),
		),
		'align' => array(
			'std' => 'left',
			'type' => 'select',
			'label' => __( 'Align', 'fusion-core' ),
			'desc' => 'Choose how to align the image',
			'options' => array(
				'left' => 'Left',
				'right' => 'Right',
				'center' => 'Center'
			)
		),
		'lightbox' => array(
			'type' => 'select',
			'label' => __('Image lightbox', 'fusion-core'),
			'desc' => __('Show image in Lightbox', 'fusion-core'),
			'options' => $reverse_choices
		),
		'image' => array(
			'type' => 'uploader',
			'label' => __('Image', 'fusion-core'),
			'desc' => 'Upload an image to display in the frame'
		),
		'alt' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Image Alt Text', 'fusion-core'),
			'desc' => 'The alt attribute provides alternative information if an image cannot be viewed'
		),
		'animation_type' => array(
			'type' => 'select',
			'label' => __( 'Animation Type', 'fusion-core' ),
			'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
			'options' => array(
				'0' => 'None',
				'bounce' => 'Bounce',
				'fade' => 'Fade',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'slide' => 'Slide',
			)
		),
		'animation_direction' => array(
			'type' => 'select',
			'label' => __( 'Direction of Animation', 'fusion-core' ),
			'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
			'options' => array(
				'down' => 'Down',
				'left' => 'Left',
				'right' => 'Right',
				'up' => 'Up',
			)
		),
		'animation_speed' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Speed of Animation', 'fusion-core' ),
			'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
			'options' => $dec_numbers,
		)
	),
	'shortcode' => '[imageframe lightbox="{{lightbox}}" style="{{style}}" bordercolor="{{bordercolor}}" bordersize="{{bordersize}}px" stylecolor="{{stylecolor}}" align="{{align}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"]&lt;img alt="{{alt}}" src="{{image}}" /&gt;[/imageframe]',
	'popup_title' => __( 'Image Frame Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Image Carousel Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['imagecarousel'] = array(
	'params' => array(
		'lightbox' => array(
			'type' => 'select',
			'label' => __('Image lightbox', 'fusion-core'),
			'desc' => __('Show Image in Lightbox', 'fusion-core'),
			'options' => $choices
		),
	),
	'shortcode' => '[images lightbox="{{lightbox}}"]{{child_shortcode}}[/images]', // as there is no wrapper shortcode
	'popup_title' => __('Image Carousel Shortcode', 'fusion-core'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'url' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Image Website Link', 'fusion-core'),
				'desc' => __('Add the url to image\'s website. If lightbox option is enabled, you have to add the full image link to show it in the lightbox', 'fusion-core')
			),
			'target' => array(
				'type' => 'select',
				'label' => __('Link Target', 'fusion-core'),
				'desc' => __('_self = open in same window <br />_blank = open in new window', 'fusion-core'),
				'options' => array(
					'_self' => '_self',
					'_blank' => '_blank'
				)
			),
			'image' => array(
				'type' => 'uploader',
				'label' => __('Image', 'fusion-core'),
				'desc' => 'Upload an image to display'
			),
			'alt' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Image Alt Text', 'fusion-core'),
				'desc' => 'The alt attribute provides alternative information if an image cannot be viewed'
			)
		),
		'shortcode' => '[image link="{{url}}" linktarget="{{target}}" image="{{image}}" alt="{{alt}}"]',
		'clone_button' => __('Add New Image', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Lightbox Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['lightbox'] = array(
	'no_preview' => true,
	'params' => array(

		'full_image' => array(
			'type' => 'uploader',
			'label' => __('Full Image', 'fusion-core'),
			'desc' => 'Upload an image that will show up in the lightbox'
		),
		'thumb_image' => array(
			'type' => 'uploader',
			'label' => __('Thumbnail Image', 'fusion-core'),
			'desc' => 'Clicking this image will show lightbox'
		),
		'alt' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Alt Text', 'fusion-core'),
			'desc' => 'The alt attribute provides alternative information if an image cannot be viewed'
		),
		'title' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Lightbox Description', 'fusion-core'),
			'desc' => 'This will show up in the lightbox as a description below the image'
		),
	),
	'shortcode' => '&lt;a title="{{title}}" href="{{full_image}}" rel="prettyPhoto"&gt;&lt;img alt="{{alt}}" src="{{thumb_image}}" /&gt;&lt;/a&gt;',
	'popup_title' => __( 'Lightbox Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Person Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['person'] = array(
	'no_preview' => true,
	'params' => array(

		'picture' => array(
			'type' => 'uploader',
			'label' => __('Picture', 'fusion-core'),
			'desc' => 'Upload an image to display'
		),
		'pic_link' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Picture Link URL', 'fusion-core'),
			'desc' => 'Add the URL the picture will link to, ex: http://example.com'
		),
		'name' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Name', 'fusion-core'),
			'desc' => 'Insert the name of the person'
		),
		'title' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Title', 'fusion-core'),
			'desc' => 'Insert the title of the person'
		),
		'email' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Email Address', 'fusion-core'),
			'desc' => 'Insert an email address to display the email icon'
		),
		'facebook' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Facebook Profile Link', 'fusion-core'),
			'desc' => 'Insert a url to display the facebook icon'
		),
		'twitter' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Twitter Profile Link', 'fusion-core'),
			'desc' => 'Insert a url to display the twitter icon'
		),
		'linkedin' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('LinkedIn Profile Link', 'fusion-core'),
			'desc' => 'Insert a url to display the linkedin icon'
		),
		'dribbble' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Dribbble Profile Link', 'fusion-core'),
			'desc' => 'Insert a url to display the dribbble icon'
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Link Target', 'fusion-core'),
			'desc' => __('_self = open in same window <br /> _blank = open in new window', 'fusion-core'),
			'options' => array(
				'_self' => '_self',
				'_blank' => '_blank'
			)
		),
		'content' => array(
			'std' => '',
			'type' => 'textarea',
			'label' => __('Profile Description', 'fusion-core'),
			'desc' => 'Enter the content to be displayed'
		),
	),
	'shortcode' => '[person name="{{name}}" picture="{{picture}}" pic_link="{{pic_link}}" title="{{title}}" email="{{email}}" facebook="{{facebook}}" twitter="{{twitter}}" linkedin="{{linkedin}}" dribbble="{{dribbble}}" linktarget="{{target}}"]{{content}}[/person]',
	'popup_title' => __( 'Person Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Progress Bar Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['progressbar'] = array(
	'params' => array(

		'value' => array(
			'type' => 'select',
			'label' => __('Filled Area Percentage', 'fusion-core'),
			'desc' => __('From 1% to 100%', 'fusion-core'),
			'options' => fusion_shortcodes_range(100, false)
		),
		'unit' => array(
			'std' => '',
			'type' => 'text',
			'label' => __( 'Progress Bar Unit', 'fusion-core' ),
			'desc' => __( 'Insert a unit for the progress bar. ex %', 'fusion-core' ),
		),
		'filledcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Filled Color', 'fusion-core'),
			'desc' => __('Color for filled in area', 'fusion-core')
		),
		'unfilledcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Unfilled Color', 'fusion-core'),
			'desc' => __('Color for unfilled area', 'fusion-core')
		),
		'content' => array(
			'std' => 'Text',
			'type' => 'text',
			'label' => __( 'Progess Bar Text', 'fusion-core' ),
			'desc' => __( 'Text will show up on progess bar', 'fusion-core' ),
		)
	),
	'shortcode' => '[progress percentage="{{value}}" unit="{{unit}}" filledcolor="{{filledcolor}}" unfilledcolor="{{unfilledcolor}}"]{{content}}[/progress]', // as there is no wrapper shortcode
	'popup_title' => __('Progress Bar Shortcode', 'fusion-core'),
	'no_preview' => true,
);

/*-----------------------------------------------------------------------------------*/
/*	Recent Posts Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['recentposts'] = array(
	'no_preview' => true,
	'params' => array(

		'layout' => array(
			'type' => 'select',
			'label' => __( 'Layout', 'fusion-core' ),
			'desc' => 'Select the layout for the shortcode',
			'options' => array(
				'default' => 'Default',
				'thumbnails-on-side' => 'Thumbnails on Side',
				'date-on-side' => 'Date on Side',
			)
		),
		'columns' => array(
			'type' => 'select',
			'label' => __('Columns', 'fusion-core'),
			'desc' => __('Select the number of columns to display', 'fusion-core'),
			'options' => fusion_shortcodes_range( 4, false )
		),
		'number_posts' => array(
			'std' => 4,
			'type' => 'select',
			'label' => __('Number of Posts', 'fusion-core'),
			'desc' => 'Select the number of posts to display',
			'options' => fusion_shortcodes_range( 12, false )
		),
		'cat_slug' => array(
			'type' => 'multiple_select',
			'label' => __( 'Categories', 'fusion-core' ),
			'desc' => __( 'Select a category or leave blank for all', 'fusion-core' ),
			'options' => fusion_shortcodes_categories( 'category' )
		),
		'exclude_cats' => array(
			'type' => 'multiple_select',
			'label' => __( 'Exclude Categories', 'fusion-core' ),
			'desc' => __( 'Select a category to exclude', 'fusion-core' ),
			'options' => fusion_shortcodes_categories( 'category' )
		),
		'thumbnail' => array(
			'type' => 'select',
			'label' => __('Show Thumbnail', 'fusion-core'),
			'desc' => 'Display the post featured image',
			'options' => $choices
		),
		'title' => array(
			'type' => 'select',
			'label' => __('Show Title', 'fusion-core'),
			'desc' => 'Display the post title below the featured image',
			'options' => $choices
		),
		'meta' => array(
			'type' => 'select',
			'label' => __('Show Meta', 'fusion-core'),
			'desc' => 'Choose to show all meta data',
			'options' => $choices
		),
		'excerpt' => array(
			'type' => 'select',
			'label' => __('Show Excerpt', 'fusion-core'),
			'desc' => 'Choose to display the post excerpt',
			'options' => $choices
		),
		'excerpt_words' => array(
			'std' => 35,
			'type' => 'select',
			'label' => __('Number of Excerpt Words', 'fusion-core'),
			'desc' => 'Insert the number of words you want to show in the excerpt',
			'options' => fusion_shortcodes_range( 60, false )
		),
		'strip_html' => array(
			'type' => 'select',
			'label' => __('Strip HTML', 'fusion-core'),
			'desc' => 'Strip HTML from the post excerpt',
			'options' => $choices
		),
		'animation_type' => array(
			'type' => 'select',
			'label' => __( 'Animation Type', 'fusion-core' ),
			'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
			'options' => array(
				'0' => 'None',
				'bounce' => 'Bounce',
				'fade' => 'Fade',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'slide' => 'Slide',
			)
		),
		'animation_direction' => array(
			'type' => 'select',
			'label' => __( 'Direction of Animation', 'fusion-core' ),
			'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
			'options' => array(
				'down' => 'Down',
				'left' => 'Left',
				'right' => 'Right',
				'up' => 'Up',
			)
		),
		'animation_speed' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Speed of Animation', 'fusion-core' ),
			'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
			'options' => $dec_numbers,
		)
	),
	'shortcode' => '[recent_posts layout="{{layout}}" columns="{{columns}}" number_posts="{{number_posts}}" cat_slug="{{cat_slug}}" exclude_cats="{{exclude_cats}}" thumbnail="{{thumbnail}}" title="{{title}}" meta="{{meta}}" excerpt="{{excerpt}}" excerpt_words="{{excerpt_words}}" strip_html="{{strip_html}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"][/recent_posts]',
	'popup_title' => __( 'Recent Posts Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Recent Works Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['recentworks'] = array(
	'no_preview' => true,
	'params' => array(

		'layout' => array(
			'type' => 'select',
			'label' => __( 'Layout', 'fusion-core' ),
			'desc' => 'Choose the layout for the shortcode',
			'options' => array(
				'carousel' => 'Carousel',
				'grid' => 'Grid',
				'grid-with-excerpts' => 'Grid with Excerpts',
			)
		),
		'filters' => array(
			'type' => 'select',
			'label' => __('Show Filters', 'fusion-core'),
			'desc' => 'Choose to show or hide the category filters',
			'options' => $choices
		),
		'columns' => array(
			'type' => 'select',
			'label' => __('Columns', 'fusion-core'),
			'desc' => __('Select the number of columns to display', 'fusion-core'),
			'options' => fusion_shortcodes_range( 4, false )
		),
		'cat_slug' => array(
			'type' => 'multiple_select',
			'label' => __( 'Categories', 'fusion-core' ),
			'desc' => __( 'Select a category or leave blank for all', 'fusion-core' ),
			'options' => fusion_shortcodes_categories( 'portfolio_category' )
		),
		'number_posts' => array(
			'std' => 4,
			'type' => 'select',
			'label' => __('Number of Posts', 'fusion-core'),
			'desc' => 'Select the number of posts to display',
			'options' => fusion_shortcodes_range( 12, false )
		),
		'excerpt_words' => array(
			'std' => 15,
			'type' => 'select',
			'label' => __('Number of Excerpt Words', 'fusion-core'),
			'desc' => 'Insert the number of words you want to show in the excerpt. Only works with "grid-with-excerpts" layout.',
			'options' => fusion_shortcodes_range( 35, false )
		),
		'animation_type' => array(
			'type' => 'select',
			'label' => __( 'Animation Type', 'fusion-core' ),
			'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
			'options' => array(
				'0' => 'None',
				'bounce' => 'Bounce',
				'fade' => 'Fade',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'slide' => 'Slide',
			)
		),
		'animation_direction' => array(
			'type' => 'select',
			'label' => __( 'Direction of Animation', 'fusion-core' ),
			'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
			'options' => array(
				'down' => 'Down',
				'left' => 'Left',
				'right' => 'Right',
				'up' => 'Up',
			)
		),
		'animation_speed' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Speed of Animation', 'fusion-core' ),
			'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
			'options' => $dec_numbers,
		)
	),
	'shortcode' => '[recent_works layout="{{layout}}" filters="{{filters}}" columns="{{columns}}" cat_slug="{{cat_slug}}" number_posts="{{number_posts}}" excerpt_words="{{excerpt_words}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"][/recent_works]',
	'popup_title' => __( 'Recent Works Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Separator Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['separator'] = array(
	'no_preview' => true,
	'params' => array(

		'style' => array(
			'type' => 'select',
			'label' => __( 'Style', 'fusion-core' ),
			'desc' => 'Choose the separator line style',
			'options' => array(
				'none' => 'No Style',
				'single' => 'Single Border',
				'double' => 'Double Border',
				'dashed' => 'Dashed Border',
				'dotted' => 'Dotted Border',
				'shadow' => 'Shadow'
			)
		),
		'top' => array(
			'std' => 40,
			'type' => 'select',
			'label' => __('Margin Top', 'fusion-core'),
			'desc' => 'Spacing above the separator',
			'options' => fusion_shortcodes_range( 100, false )
		)
	),
	'shortcode' => '[separator top="{{top}}" style="{{style}}"]',
	'popup_title' => __( 'Separator Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Sharing Box Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['sharingbox'] = array(
	'no_preview' => true,
	'params' => array(

		'tagline' => array(
			'std' => 'Share This Story, Choose Your Platform!',
			'type' => 'text',
			'label' => __('Tagline', 'fusion-core'),
			'desc' => 'The title tagline that will display'
		),
		'title' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Title', 'fusion-core'),
			'desc' => 'The post title that will be shared'
		),
		'link' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Link', 'fusion-core'),
			'desc' => 'The link that will be shared'
		),
		'description' => array(
			'std' => '',
			'type' => 'textarea',
			'label' => __('Description', 'fusion-core'),
			'desc' => 'The description that will be shared'
		),
		'link' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Link to Share', 'fusion-core'),
			'desc' => ''
		),
		'pinterest_image' => array(
			'std' => '',
			'type' => 'uploader',
			'label' => __('Choose Image to Share on Pinterest', 'fusion-core'),
			'desc' => ''
		),
		'backgroundcolor' => array(
			'type' => 'colorpicker',
			'label' => __('Background Color', 'fusion-core'),
			'desc' => __('Leave blank for default color', 'fusion-core')
		),
	),
	'shortcode' => '[sharing tagline="{{tagline}}" title="{{title}}" link="{{link}}" description="{{description}}" pinterest_image="{{pinterest_image}}" backgroundcolor="{{backgroundcolor}}"][/sharing]',
	'popup_title' => __( 'Sharing Box Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Slider Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['slider'] = array(
	'params' => array(
		'size' => array(
			'std' => '100%',
			'type' => 'size',
			'label' => __('Image Size (Width/Height)', 'fusion-core'),
			'desc' => __('Width and Height in percentage (%) or pixels (px)', 'fusion-core'),
		),
	),
	'shortcode' => '[slider width="{{size_width}}" height="{{size_height}}"]{{child_shortcode}}[/slider]', // as there is no wrapper shortcode
	'popup_title' => __('Slider Shortcode', 'fusion-core'),
	'no_preview' => true,

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'slider_type' => array(
				'type' => 'select',
				'label' => __('Slide Type', 'fusion-core'),
				'desc' => 'Choose a video or image slide',
				'options' => array(
					'image' => 'Image',
					'video' => 'Video'
				)
			),
			'video_content' => array(
				'std' => '',
				'type' => 'textarea',
				'label' => __('Video Shortcode or Video Embed Code', 'fusion-core'),
				'desc' => 'Click the Youtube or Vimeo Shortcode button below then enter your unique video ID, or copy and paste your video embed code.<a href=\'[youtube id="Enter video ID (eg. Wq4Y7ztznKc)" width="600" height="350"]\' class="fusion-shortcodes-button fusion-add-video-shortcode">Insert Youtube Shortcode</a><a href=\'[vimeo id="Enter video ID (eg. 10145153)" width="600" height="350"]\' class="fusion-shortcodes-button fusion-add-video-shortcode">Insert Vimeo Shortcode</a>'
			),
			'image_content' => array(
				'std' => '',
				'type' => 'uploader',
				'label' => __('Slide Image', 'fusion-core'),
				'desc' => 'Upload an image to display in the slide'
			),
			'image_url' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Full Image Link or External Link', 'fusion-core'),
				'desc' => 'Add the url of where the image will link to. If lightbox option is enabled,and you don\'t add the full image link, lightbox will open slide image'
			),
			'image_target' => array(
				'type' => 'select',
				'label' => __('Link Target', 'fusion-core'),
				'desc' => __('_self = open in same window <br /> _blank = open in new window', 'fusion-core'),
				'options' => array(
					'_self' => '_self',
					'_blank' => '_blank'
				)
			),
			'image_lightbox' => array(
				'type' => 'select',
				'label' => __('Lighbox', 'fusion-core'),
				'desc' => __('Show image in Lightbox', 'fusion-core'),
				'options' => $choices
			),
		),
		'shortcode' => '[slide type="{{slider_type}}" link="{{image_url}}" linktarget="{{image_target}}" lightbox="{{image_lightbox}}"]{{image_content}}[/slide]',
		'clone_button' => __('Add New Slide', 'fusion-core')
	)
);


/*-----------------------------------------------------------------------------------*/
/*	SoundCloud Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['soundcloud'] = array(
	'no_preview' => true,
	'params' => array(

		'url' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('SoundCloud Url', 'fusion-core'),
			'desc' => 'The SoundCloud url, ex: http://api.soundcloud.com/tracks/110813479'
		),
		'comments' => array(
			'type' => 'select',
			'label' => __('Show Comments', 'fusion-core'),
			'desc' => 'Choose to display comments',
			'options' => $choices
		),
		'auto_play' => array(
			'type' => 'select',
			'label' => __('Autoplay', 'fusion-core'),
			'desc' => 'Choose to autoplay the track',
			'options' => $reverse_choices
		),
		'color' => array(
			'type' => 'colorpicker',
			'std' => '#ff7700',
			'label' => __('Color', 'fusion-core'),
			'desc' => 'Select the color of the shortcode'
		),
		'width' => array(
			'std' => '100%',
			'type' => 'text',
			'label' => __('Width', 'fusion-core'),
			'desc' => 'In pixels (px) or percentage (%)'
		),
		'height' => array(
			'std' => '81px',
			'type' => 'text',
			'label' => __('Height', 'fusion-core'),
			'desc' => 'In pixels (px)'
		),
	),
	'shortcode' => '[soundcloud url="{{url}}" comments="{{comments}}" auto_play="{{auto_play}}" color="{{color}}" width="{{width}}" height="{{height}}"]',
	'popup_title' => __( 'Sharing Box Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Social Links Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['sociallinks'] = array(
	'no_preview' => true,
	'params' => array(

		'colorscheme' => array(
			'type' => 'select',
			'label' => __('Color Scheme', 'fusion-core'),
			'desc' => 'Choose the color scheme for the social links',
			'options' => array(
				'' => 'Default',
				'light' => 'Light',
				'dark' => 'Dark'
			)
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Link Target', 'fusion-core'),
			'desc' => __('_self = open in same window <br />_blank = open in new window', 'fusion-core'),
			'options' => array(
				'_self' => '_self',
				'_blank' => '_blank'
			)
		),
		'rss' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('RSS Link', 'fusion-core'),
			'desc' => 'Insert your custom RSS link'
		),
		'facebook' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Facebook Link', 'fusion-core'),
			'desc' => 'Insert your custom Facebook link'
		),
		'twitter' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Twitter Link', 'fusion-core'),
			'desc' => 'Insert your custom Twitter link'
		),
		'dribbble' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Dribbble Link', 'fusion-core'),
			'desc' => 'Insert your custom Dribbble link'
		),
		'google' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Google+ Link', 'fusion-core'),
			'desc' => 'Insert your custom Google+ link'
		),
		'linkedin' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('LinkedIn Link', 'fusion-core'),
			'desc' => 'Insert your custom LinkedIn link'
		),
		'blogger' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Blogger Link', 'fusion-core'),
			'desc' => 'Insert your custom Blogger link'
		),
		'tumblr' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Tumblr Link', 'fusion-core'),
			'desc' => 'Insert your custom Tumblr link'
		),
		'reddit' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Reddit Link', 'fusion-core'),
			'desc' => 'Insert your custom Reddit link'
		),
		'yahoo' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Yahoo Link', 'fusion-core'),
			'desc' => 'Insert your custom Yahoo link'
		),
		'deviantart' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Deviantart Link', 'fusion-core'),
			'desc' => 'Insert your custom Deviantart link'
		),
		'vimeo' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Vimeo Link', 'fusion-core'),
			'desc' => 'Insert your custom Vimeo link'
		),
		'youtube' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Youtube Link', 'fusion-core'),
			'desc' => 'Insert your custom Youtube link'
		),
		'pinterest' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Pinterst Link', 'fusion-core'),
			'desc' => 'Insert your custom Pinterest link'
		),
		'digg' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Digg Link', 'fusion-core'),
			'desc' => 'Insert your custom Digg link'
		),
		'flickr' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Flickr Link', 'fusion-core'),
			'desc' => 'Insert your custom Flickr link'
		),
		'forrst' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Forrst Link', 'fusion-core'),
			'desc' => 'Insert your custom Forrst link'
		),
		'myspace' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Myspace Link', 'fusion-core'),
			'desc' => 'Insert your custom Myspace link'
		),
		'skype' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Skype Link', 'fusion-core'),
			'desc' => 'Insert your custom Skype link'
		),
		'show_custom' => array(
			'type' => 'select',
			'label' => __('Show Custom Social Icon', 'fusion-core'),
			'desc' => __('Show the custom social icon specified in Theme Options', 'fusion-core'),
			'options' => $reverse_choices
		),
	),
	'shortcode' => '[social_links colorscheme="{{colorscheme}}" linktarget="{{target}}" rss="{{rss}}" facebook="{{facebook}}" twitter="{{twitter}}" dribbble="{{dribbble}}" google="{{google}}" linkedin="{{linkedin}}" blogger="{{blogger}}" tumblr="{{tumblr}}" reddit="{{reddit}}" yahoo="{{yahoo}}" deviantart="{{deviantart}}" vimeo="{{vimeo}}" youtube="{{youtube}}" pinterest="{{pinterest}}" digg="{{digg}}" flickr="{{flickr}}" forrst="{{forrst}}" myspace="{{myspace}}" skype="{{skype}}" show_custom="{{show_custom}}"]',
	'popup_title' => __( 'Social Links Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Tabs Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['tabs'] = array(
	'params' => array(

		'layout' => array(
			'type' => 'select',
			'label' => __('Layout', 'fusion-core'),
			'desc' => 'Choose the layout of the shortcode',
			'options' => array(
				'horizontal' => 'Horizontal',
				'vertical' => 'Vertical'
			)
		),
		'backgroundcolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Background Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
		'inactivecolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Inactive Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
	),
	'no_preview' => true,
	'shortcode' => '[fusion_tabs layout="{{layout}}" backgroundcolor="{{backgroundcolor}}" inactivecolor="{{inactivecolor}}"]{{child_shortcode}}[/fusion_tabs]',
	'popup_title' => __('Insert Tab Shortcode', 'fusion-core'),

	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'std' => 'Title',
				'type' => 'text',
				'label' => __('Tab Title', 'fusion-core'),
				'desc' => __('Title of the tab', 'fusion-core'),
			),
			'content' => array(
				'std' => 'Tab Content',
				'type' => 'textarea',
				'label' => __('Tab Content', 'fusion-core'),
				'desc' => __('Add the tabs content', 'fusion-core')
			)
		),
		'shortcode' => '[fusion_tab title="{{title}}"]{{content}}[/fusion_tab]',
		'clone_button' => __('Add Tab', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Tagline Box Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['taglinebox'] = array(
	'no_preview' => true,
	'params' => array(
		'backgroundcolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Background Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
		'shadow' => array(
			'type' => 'select',
			'label' => __('Shadow', 'fusion-core'),
			'desc' => __('Show the shadow below the box', 'fusion-core'),
			'options' => $reverse_choices
		),
		'shadowopacity' => array(
			'type' => 'select',
			'label' => __('Shadow Opacity', 'fusion-core'),
			'desc' => __('Choose the opacity of the shadow', 'fusion-core'),
			'options' => $dec_numbers
		),
		'border' => array(
			'std' => '1px',
			'type' => 'text',
			'label' => __('Border', 'fusion-core'),
			'desc' => 'In pixels (px), ex: 1px'
		),
		'bordercolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Border Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
		'highlightposition' => array(
			'type' => 'select',
			'label' => __('Highlight Border Position', 'fusion-core'),
			'desc' => __('Choose the position of the highlight. This border highlight is from theme options primary color and does not take the color from border color above', 'fusion-core'),
			'options' => array(
				'top' => 'Top',
				'bottom' => 'Bottom',
				'left' => 'Left',
				'right' => 'Right'
			)
		),
		'button' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Button Text', 'fusion-core'),
			'desc' => 'Insert the text that will display in the button'
		),
		'url' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Link', 'fusion-core'),
			'desc' => __('The url the button will link to', 'fusion-core')
		),
		'target' => array(
			'type' => 'select',
			'label' => __('Link Target', 'fusion-core'),
			'desc' => __('_self = open in same window <br /> _blank = open in new window', 'fusion-core'),
			'options' => array(
				'_self' => '_self',
				'_blank' => '_blank'
			)
		),
		'buttoncolor' => array(
			'type' => 'select',
			'label' => __('Button Color', 'fusion-core'),
			'desc' => 'Choose the button color <br />Default uses theme option selection',
			'options' => array(
				'' => 'Default',
				'green' => 'Green',
				'darkgreen' => 'Dark Green',
				'orange' => 'Orange',
				'blue' => 'Blue',
				'red' => 'Red',
				'pink' => 'Pink',
				'darkgray' => 'Dark Gray',
				'lightgray' => 'Light Gray',
			)
		),
		'title' => array(
			'type' => 'textarea',
			'label' => __('Tagline Title', 'fusion-core'),
			'desc' => 'Insert the title text',
			'std' => 'Title'
		),
		'description' => array(
			'std' => '',
			'type' => 'textarea',
			'label' => __('Tagline Description', 'fusion-core'),
			'desc' => 'Insert the description text',
		),
		'animation_type' => array(
			'type' => 'select',
			'label' => __( 'Animation Type', 'fusion-core' ),
			'desc' => __( 'Select the type on animation to use on the shortcode', 'fusion-core' ),
			'options' => array(
				'0' => 'None',
				'bounce' => 'Bounce',
				'fade' => 'Fade',
				'flash' => 'Flash',
				'shake' => 'Shake',
				'slide' => 'Slide',
			)
		),
		'animation_direction' => array(
			'type' => 'select',
			'label' => __( 'Direction of Animation', 'fusion-core' ),
			'desc' => __( 'Select the incoming direction for the animation', 'fusion-core' ),
			'options' => array(
				'down' => 'Down',
				'left' => 'Left',
				'right' => 'Right',
				'up' => 'Up',
			)
		),
		'animation_speed' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Speed of Animation', 'fusion-core' ),
			'desc' => __( 'Type in speed of animation in seconds (0.1 - 1)', 'fusion-core' ),
			'options' => $dec_numbers,
		)
	),
	'shortcode' => '[tagline_box backgroundcolor="{{backgroundcolor}}" shadow="{{shadow}}" shadowopacity="{{shadowopacity}}" border="{{border}}" bordercolor="{{bordercolor}}" highlightposition="{{highlightposition}}" link="{{url}}" linktarget="{{target}}" buttoncolor="{{buttoncolor}}" button="{{button}}" title="{{title}}" description="{{description}}" animation_type="{{animation_type}}" animation_direction="{{animation_direction}}" animation_speed="{{animation_speed}}"][/tagline_box]',
	'popup_title' => __('Insert Tagline Box Shortcode', 'fusion-core')
);

/*-----------------------------------------------------------------------------------*/
/*	Testimonials Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['testimonials'] = array(
	'params' => array(

		'backgroundcolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Background Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
		'textcolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Text Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
	),
	'no_preview' => true,
	'shortcode' => '[testimonials backgroundcolor="{{backgroundcolor}}" textcolor="{{textcolor}}"]{{child_shortcode}}[/testimonials]',
	'popup_title' => __('Insert Testimonials Shortcode', 'fusion-core'),

	'child_shortcode' => array(
		'params' => array(
			'name' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Name', 'fusion-core'),
				'desc' => 'Insert the name of the person',
			),
			'gender' => array(
				'type' => 'select',
				'label' => __('Gender', 'fusion-core'),
				'desc' => 'Choose male or female',
				'options' => array(
					'male' => 'Male',
					'female' => 'Female'
				)
			),
			'company' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Company', 'fusion-core'),
				'desc' => 'Insert the name of the company',
			),
			'link' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Link', 'fusion-core'),
				'desc' => 'Add the url the company name will link to'
			),
			'target' => array(
				'type' => 'select',
				'label' => __('Target', 'fusion-core'),
				'desc' => __('_self = open in same window <br />_blank = open in new window', 'fusion-core'),
				'options' => array(
					'_self' => '_self',
					'_blank' => '_blank'
				)
			),
			'content' => array(
				'std' => '',
				'type' => 'textarea',
				'label' => __('Testimonial Content', 'fusion-core'),
				'desc' => 'Add the testimonial content'
			)
		),
		'shortcode' => '[testimonial name="{{name}}" gender="{{gender}}" company="{{company}}" link="{{link}}" target="{{target}}"]{{content}}[/testimonial]',
		'clone_button' => __('Add Testimonial', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Title Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['title'] = array(
	'no_preview' => true,
	'params' => array(

		'size' => array(
			'type' => 'select',
			'label' => __('Title Size', 'fusion-core'),
			'desc' => 'Choose the title size, H1-H6',
			'options' => fusion_shortcodes_range( 6, false )
		),
		'content' => array(
			'std' => '',
			'type' => 'textarea',
			'label' => __('Title', 'fusion-core'),
			'desc' => 'Insert the title text'
		),
	),
	'shortcode' => '[title size="{{size}}"]{{content}}[/title]',
	'popup_title' => __( 'Sharing Box Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Toggles Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['toggles'] = array(
	'params' => array(

	),
	'no_preview' => true,
	'shortcode' => '[accordian]{{child_shortcode}}[/accordian]',
	'popup_title' => __('Insert Toggles Shortcode', 'fusion-core'),

	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'std' => '',
				'type' => 'text',
				'label' => __('Title', 'fusion-core'),
				'desc' => 'Insert the toggle title',
			),
			'open' => array(
				'type' => 'select',
				'label' => __('Open by Default', 'fusion-core'),
				'desc' => 'Choose to have the toggle open when page loads',
				'options' => $reverse_choices
			),
			'content' => array(
				'std' => '',
				'type' => 'textarea',
				'label' => __('Toggle Content', 'fusion-core'),
				'desc' => 'Insert the toggle content'
			)
		),
		'shortcode' => '[toggle title="{{title}}" open="{{open}}"]{{content}}[/toggle]',
		'clone_button' => __('Add Toggle', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Tooltip Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['tooltip'] = array(
	'no_preview' => true,
	'params' => array(

		'title' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Tooltip Text', 'fusion-core'),
			'desc' => 'Insert the text that displays in the tooltip'
		),
		'content' => array(
			'std' => '',
			'type' => 'textarea',
			'label' => __('Content', 'fusion-core'),
			'desc' => 'Insert the text that will activate the tooltip hover'
		),
	),
	'shortcode' => '[tooltip title="{{title}}"]{{content}}[/tooltip]',
	'popup_title' => __( 'Tooltip Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Vimeo Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['vimeo'] = array(
	'no_preview' => true,
	'params' => array(

		'id' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Video ID', 'fusion-core'),
			'desc' => 'For example the Video ID for <br />https://vimeo.com/75230326 is 75230326'
		),
		'width' => array(
			'std' => '600',
			'type' => 'text',
			'label' => __('Width', 'fusion-core'),
			'desc' => 'In pixels but only enter a number, ex: 600'
		),
		'height' => array(
			'std' => '350',
			'type' => 'text',
			'label' => __('Height', 'fusion-core'),
			'desc' => 'In pixels but enter a number, ex: 350'
		),
		'autoplay' => array(
			'type' => 'select',
			'label' => __( 'Autoplay Video', 'fusion-core' ),
			'desc' =>  __( 'Set to yes to make video autoplaying', 'fusion-core' ),
			'options' => $reverse_choices
		),
	),
	'shortcode' => '[vimeo id="{{id}}" width="{{width}}" height="{{height}}" autoplay="{{autoplay}}"]',
	'popup_title' => __( 'Vimeo Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Woo Featured Slider Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['woofeatured'] = array(
	'no_preview' => true,
	'params' => array(

		'info' => array(
			'std' => 'No settings required. Insert the shortcode and your featured products will be pulled. Featured products are products that you have "Starred" in the WooCommerce settings. To set featured products, please see this post,<br /><a href="http://theme-fusion.com/knowledgebase/how-to-use-woocommerce-featured-products-slider/" target="_blank">Knowledge Base Article for Featured Products</a>',
			'type' => 'info'
		)
	),
	'shortcode' => '[featured_products_slider]',
	'popup_title' => __( 'Woocommerce Featured Products Slider Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Woo Products Slider Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['wooproducts'] = array(
	'params' => array(

		'picture_size' => array(
			'type' => 'select',
			'label' => __('Picture Size', 'fusion-core'),
			'desc' => __('fixed = width and height will be fixed <br />auto = width and height will adjust to the image.', 'fusion-core'),
			'options' => array(
				'fixed' => 'Fixed',
				'auto' => 'Auto'
			)
		),
		'cat_slug' => array(
			'type' => 'multiple_select',
			'label' => __( 'Categories', 'fusion-core' ),
			'desc' => __( 'Select a category or leave blank for all', 'fusion-core' ),
			'options' => fusion_shortcodes_categories( 'product_cat' )
		),
		'number_posts' => array(
			'std' => 5,
			'type' => 'select',
			'label' => __('Number of Products', 'fusion-core'),
			'desc' => 'Select the number of products to display',
			'options' => fusion_shortcodes_range( 20, false )
		),
		'show_cats' => array(
			'type' => 'select',
			'label' => __('Show Categories', 'fusion-core'),
			'desc' => 'Choose to show or hide the categories',
			'options' => $reverse_choices
		),
		'show_price' => array(
			'type' => 'select',
			'label' => __('Show Price', 'fusion-core'),
			'desc' => 'Choose to show or hide the price',
			'options' => $reverse_choices
		),
		'show_buttons' => array(
			'type' => 'select',
			'label' => __('Show Buttons', 'fusion-core'),
			'desc' => 'Choose to show or hide the icon buttons',
			'options' => $reverse_choices
		)
	),
	'shortcode' => '[products_slider picture_size="{{picture_size}}" cat_slug="{{cat_slug}}" number_posts="{{number_posts}}" show_cats="{{show_cats}}" show_price="{{show_price}}" show_buttons="{{show_buttons}}"]',
	'popup_title' => __('Woocommerce Products Slider Shortcode', 'fusion-core'),
	'no_preview' => true,
);

/*-----------------------------------------------------------------------------------*/
/*	Youtube Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['youtube'] = array(
	'no_preview' => true,
	'params' => array(

		'id' => array(
			'std' => '',
			'type' => 'text',
			'label' => __('Video ID', 'fusion-core'),
			'desc' => 'For example the Video ID for <br />http://www.youtube.com/LOfeCR7KqUs is LOfeCR7KqUs'
		),
		'width' => array(
			'std' => '600',
			'type' => 'text',
			'label' => __('Width', 'fusion-core'),
			'desc' => 'In pixels but only enter a number, ex: 600'
		),
		'height' => array(
			'std' => '350',
			'type' => 'text',
			'label' => __('Height', 'fusion-core'),
			'desc' => 'In pixels but only enter a number, ex: 350'
		),
		'autoplay' => array(
			'type' => 'select',
			'label' => __( 'Autoplay Video', 'fusion-core' ),
			'desc' =>  __( 'Set to yes to make video autoplaying', 'fusion-core' ),
			'options' => $reverse_choices
		),
	),
	'shortcode' => '[youtube id="{{id}}" width="{{width}}" height="{{height}}" autoplay="{{autoplay}}"]',
	'popup_title' => __( 'Vimeo Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Columns Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['columns'] = array(
	'shortcode' => ' {{child_shortcode}} ', // as there is no wrapper shortcode
	'popup_title' => __('Insert Columns Shortcode', 'fusion-core'),
	'no_preview' => true,
	'params' => array(),

	// child shortcode is clonable & sortable
	'child_shortcode' => array(
		'params' => array(
			'column' => array(
				'type' => 'select',
				'label' => __('Column Type', 'fusion-core'),
				'desc' => __('Select the width of the column', 'fusion-core'),
				'options' => array(
					'one_third' => 'One Third',
					'two_third' => 'Two Thirds',
					'one_half' => 'One Half',
					'one_fourth' => 'One Fourth',
					'three_fourth' => 'Three Fourth',
				)
			),
			'last' => array(
				'type' => 'select',
				'label' => __('Last Column', 'fusion-core'),
				'desc' => 'Choose if the column is last in a set. This has to be set to "Yes" for the last column in a set',
				'options' => $reverse_choices
			),
			'content' => array(
				'std' => '',
				'type' => 'textarea',
				'label' => __('Column Content', 'fusion-core'),
				'desc' => __('Insert the column content', 'fusion-core'),
			)
		),
		'shortcode' => '[{{column}} last="{{last}}"]{{content}}[/{{column}}] ',
		'clone_button' => __('Add Column', 'fusion-core')
	)
);

/*-----------------------------------------------------------------------------------*/
/*	Pricing Table Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['pricingtable'] = array(
	'no_preview' => true,
	'params' => array(

		'type' => array(
			'type' => 'select',
			'label' => __('Type', 'fusion-core'),
			'desc' => __('Select the type of pricing table', 'fusion-core'),
			'options' => array(
				'1' => 'Style 1 (Supports 5 Columns)',
				'2' => 'Style 2 (Supports 4 Columns)',
			)
		),
		'backgroundcolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Background Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
		'bordercolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Border Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
		'dividercolor' => array(
			'type' => 'colorpicker',
			'std' => '',
			'label' => __('Divider Color', 'fusion-core'),
			'desc' => 'Leave blank for default'
		),
		'columns' => array(
			'type' => 'select',
			'label' => __('Number of Columns', 'fusion-core'),
			'desc' => 'Select how many columns to display',
			'options' => array(
				'&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;' => '1 Column',
				'&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;' => '2 Columns',
				'&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;' => '3 Columns',
				'&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;' => '4 Columns',
				'&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;[pricing_column title=&quot;Standard&quot;][pricing_price currency=&quot;$&quot; price=&quot;15.55&quot; time=&quot;monthly&quot;][/pricing_price][pricing_row]Feature 1[/pricing_row][pricing_footer]Signup[/pricing_footer][/pricing_column]&lt;br /&gt;' => '5 Columns'
			)
		)
	),
	'shortcode' => '[pricing_table type="{{type}}" backgroundcolor="{{backgroundcolor}}" bordercolor="{{bordercolor}}" dividercolor="{{dividercolor}}"]{{columns}}[/pricing_table]',
	'popup_title' => __( 'Pricing Table Shortcode', 'fusion-core' )
);

/*-----------------------------------------------------------------------------------*/
/*	Table Config
/*-----------------------------------------------------------------------------------*/

$fusion_shortcodes['table'] = array(
	'no_preview' => true,
	'params' => array(

		'type' => array(
			'type' => 'select',
			'label' => __('Type', 'fusion-core'),
			'desc' => __('Select the table style', 'fusion-core'),
			'options' => array(
				'1' => 'Style 1',
				'2' => 'Style 2',
			)
		),
		'columns' => array(
			'type' => 'select',
			'label' => __('Number of Columns', 'fusion-core'),
			'desc' => 'Select how many columns to display',
			'options' => array(
				'1' => '1 Column',
				'2' => '2 Columns',
				'3' => '3 Columns',
				'4' => '4 Columns'
			)
		)
	),
	'shortcode' => '',
	'popup_title' => __( 'Table Shortcode', 'fusion-core' )
);