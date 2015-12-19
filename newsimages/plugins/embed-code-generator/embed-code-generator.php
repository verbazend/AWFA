<?php
/*
Plugin Name: Embed Code Generator
Plugin URI: http://infographicjournal.com/embed-code-generator
Description: A plugin to show the embed code for a proper link to a graphic/image
Version: v. 1.1
Author: Infographic Journal & Developed by Justice Solutions LLC 
Author URI: http://infographicjournal.com/embed-code-generator
*/
if (!class_exists("EmbedCodeGenerator")) {
	class EmbedCodeGenerator {
		public function __construct()
	    {
			
			add_action( 'add_meta_boxes',
                        array( &$this, 'add_iecg_meta_box' ) );

			add_action('save_post', 
						array(&$this,'iecg_save_data') ) ;

			//add_action('admin_init', 
				//	   array(&$this, 'iecg_admin_init') );

			add_action('admin_menu', 
					   array(&$this, 'add_iecg_admin_page') );

		}
		

		function iecg_admin_init(){

		}

		function iecg_section_text() {
			echo 
			 '<p>The Embed Code Generator will allow you to create a true embed code within your Wordpress posts, perfect for infographics, photos and images.</p>
<p>To use is pretty simple. If the "source" field is filled out, the embed code will be placed after the content on any of your posts or pages. Leave the "source" field blank and the embed code will not appear.</p>
<p>Embed Code Generator fields are as follows:
<ul><li>Source (URL) - Must include the full URL (including the http://) to the image.</li>
<li>Link Image To - This is where the image will link to when placed on another site. If left blank, it will just link back to your original post itself.</li>
<li>Title (optional) - When populated, it will add in the "title" attribute of the img tag.</li>
<li>Alt Attribute (optional) - When populated, it will add in the "alt" attribute of the img tag.</li>
<li>Width (optional) - Designate the width of the image.</li>
<li>Height (optional) - Designate the height of the image.</li>
<li>Courtesy of [Your Site Name] (optional) - When populated, will display a courtesy link after the image crediting the source of the image.</li>
<li>Courtesy of [Your Site URL] (optional) - If this and the "Courtesy of [Your Site Name]" are populated, a link will be provided to the source of the image.</li></ul></p>';

		}

		function add_iecg_admin_page(){
								/*register_setting( 'iecg_options',
							  '	iecg_title_reply' );*/
							  
								add_settings_section('iecg_main',
								 __('Embed Code Generator','iecg_textdomain'),
								 array(&$this,'iecg_section_text'),
								 plugin_basename(__FILE__));
								
												  
							 	add_options_page('Embed Code Generator Options','Embed Code Generator','edit_posts', plugin_basename(__FILE__), array(&$this,'print_iecg_admin_page'));
							
		}

		function print_iecg_admin_page(){
?>
    <div>
          <form method="post" 
				action="options.php">
         
            <?php settings_fields('iecg_options'); ?>
			<?php do_settings_sections(plugin_basename(__FILE__)); ?>

            
          </form>
    </div>
<?php

    	}
		

	    public function add_iecg_meta_box()
		{	
			$post_types=get_post_types('','names'); 
			foreach ($post_types as $post_type ) {
				//echo '<p>'. $post_type. '</p>';
				add_meta_box(
		         'iecg_meta_box'
		        ,__('Embed Code Generator','iecg_textdomain')
		        ,array( &$this, 'render_iecg_meta_box_content' )
		        ,$post_type		        
		    	);
			}
		    
			/*add_meta_box(
		         'iecg_meta_box'
		        ,__('Embed Code Generator','iecg_textdomain')
		        ,array( &$this, 'render_iecg_meta_box_content' )
		        ,'page'		        
		    );*/
			
		}

		public function render_iecg_meta_box_content( $post )
		{		    
			wp_nonce_field( plugin_basename(__FILE__),
							'iecg_noncename');
?>
		    <label for="iecg__title_reply">
		         <?php _e('Source (URL):' ,'iecg_textdomain')?>
			</label>
		    <input type="text"
		           id="_iecg_source"
		           name="_iecg_source"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_source',
                                                   true)); ?>"
		           size="25" /><br/>
             
              <label for="iecg__title_reply">
		         <?php _e('Link Image To:' ,'iecg_textdomain')?>
			</label>
		    <input type="text"
		           id="_iecg_linkto"
		           name="_iecg_linkto"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_linkto',
                                                   true)); ?>"
		           size="25" /><br/>
                   
              <label for="iecg__title_reply">
		         <?php _e('Title (optional):' ,'iecg_textdomain')?>
			</label>
		    <input type="text"
		           id="_iecg_title"
		           name="_iecg_title"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_title',
                                                   true)); ?>"
		           size="25" /><br/>
             
             <label for="iecg__title_reply">
		         <?php _e('Alt Attribute (optional):' ,'iecg_textdomain')?>
			</label>
		    <input type="text"
		           id="_iecg_altattribute"
		           name="_iecg_altattribute"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_altattribute',
                                                   true)); ?>"
		           size="25" /><br/>
                   <label for="_iecg__title_reply">
		         <?php _e('Width (optional):' ,'_iecg_textdomain')?>
			</label>
		    <input type="text"
		           id="_iecg_width"
		           name="_iecg_width"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_width',
                                                   true)); ?>"
		           size="25" /> pixels<br/>
                   <label for="iecg__title_reply">
		         <?php _e('Height (optional):' ,'iecg_textdomain')?>
			</label>
		    <input type="text"
		           id="_iecg_height"
		           name="_iecg_height"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_height',
                                                   true)); ?>"
		           size="25" /> pixels<br/>
                   
             <label for="iecg__title_reply">
		         <?php _e('Courtesy of [Your Site Name] (optional):' ,'iecg_textdomain')?>
			</label>
		    <input type="text"
		           id="_iecg_sitename"
		           name="_iecg_sitename"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_sitename',
                                                   true)); ?>"
		           size="25" /> <br/>
                   
             <label for="iecg__title_reply">
		         <?php _e('Courtesy of [Your Site URL] (optional):' ,'iecg_siteurl')?>
			</label>
		    <input type="text"
		           id="_iecg_siteurl"
		           name="_iecg_siteurl"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   '_iecg_siteurl',
                                                   true)); ?>"
		           size="25" /> <br/>
             
                   
             
<?php
		}

		public function iecg_save_data( $post_id )
		{
			if ( !wp_verify_nonce( $_POST['iecg_noncename'],plugin_basename(__FILE__) ) )
	 	       return $post_id;
			
			$post_types=get_post_types('','names');
			 
			foreach ($post_types as $post_type ) 
			
			{
				if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
					return;
				}
				else if ( $post_type == $_POST['post_type'] && 
						current_user_can( 'edit_page', $post_id ) )
				{
					$ccm = $_POST['_iecg_source'];
					if(!empty($ccm)){
						add_post_meta($post_id, '_iecg_source', $ccm, true)
								or
						update_post_meta($post_id, '_iecg_source', $ccm);
					}else{
						delete_post_meta($post_id, '_iecg_source');
					}
					
					$ccm1 = $_POST['_iecg_linkto'];
					if(!empty($ccm1)){
						add_post_meta($post_id, '_iecg_linkto', $ccm1, true)
								or
						update_post_meta($post_id, '_iecg_linkto', $ccm1);
					}else{
						delete_post_meta($post_id, '_iecg_linkto');
					}
					
					$ccm2 = $_POST['_iecg_altattribute'];
					if(!empty($ccm2)){
						add_post_meta($post_id, '_iecg_altattribute', $ccm2, true)
								or
						update_post_meta($post_id, '_iecg_altattribute', $ccm2);
					}else{
						delete_post_meta($post_id, '_iecg_altattribute');
					}
					
					$ccm3 = $_POST['_iecg_title'];
					if(!empty($ccm3)){
						add_post_meta($post_id, '_iecg_title', $ccm3, true)
								or
						update_post_meta($post_id, '_iecg_title', $ccm3);
					}else{
						delete_post_meta($post_id, '_iecg_title');
					}
					
					$ccm4 = $_POST['_iecg_width'];
					if(!empty($ccm4)){
						add_post_meta($post_id, '_iecg_width', $ccm4, true)
								or
						update_post_meta($post_id, '_iecg_width', $ccm4);
					}else{
						delete_post_meta($post_id, '_iecg_width');
					}
					
					$ccm5 = $_POST['_iecg_height'];
					if(!empty($ccm5)){
						add_post_meta($post_id, '_iecg_height', $ccm5, true)
								or
						update_post_meta($post_id, '_iecg_height', $ccm5);
					}else{
						delete_post_meta($post_id, '_iecg_height');
					}
					$ccm6 = $_POST['_iecg_sitename'];
					if(!empty($ccm6)){
						add_post_meta($post_id, '_iecg_sitename', $ccm6, true)
								or
						update_post_meta($post_id, '_iecg_sitename', $ccm6);
					}else{
						delete_post_meta($post_id, '_iecg_sitename');
					}
					
					$ccm7 = $_POST['_iecg_siteurl'];
					if(!empty($ccm7)){
						add_post_meta($post_id, '_iecg_siteurl', $ccm7, true)
								or
						update_post_meta($post_id, '_iecg_siteurl', $ccm7);
					}else{
						delete_post_meta($post_id, '_iecg_siteurl');
					}
			}
			}
				/*if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
					return;
				}
				else if ( $post_type == $_POST['post_type'] && 
						current_user_can( 'edit_page', $post_id ) )
				{
					$ccm = $_POST['_iecg_source'];
					if(!empty($ccm)){
						add_post_meta($post_id, '_iecg_source', $ccm, true)
								or
						update_post_meta($post_id, '_iecg_source', $ccm);
					}else{
						delete_post_meta($post_id, '_iecg_source');
					}
					
					$ccm1 = $_POST['_iecg_linkto'];
					if(!empty($ccm1)){
						add_post_meta($post_id, '_iecg_linkto', $ccm1, true)
								or
						update_post_meta($post_id, '_iecg_linkto', $ccm1);
					}else{
						delete_post_meta($post_id, '_iecg_linkto');
					}
					
					$ccm2 = $_POST['_iecg_altattribute'];
					if(!empty($ccm2)){
						add_post_meta($post_id, '_iecg_altattribute', $ccm2, true)
								or
						update_post_meta($post_id, '_iecg_altattribute', $ccm2);
					}else{
						delete_post_meta($post_id, '_iecg_altattribute');
					}
					
					$ccm3 = $_POST['_iecg_title'];
					if(!empty($ccm3)){
						add_post_meta($post_id, '_iecg_title', $ccm3, true)
								or
						update_post_meta($post_id, '_iecg_title', $ccm3);
					}else{
						delete_post_meta($post_id, '_iecg_title');
					}
					
					$ccm4 = $_POST['_iecg_width'];
					if(!empty($ccm4)){
						add_post_meta($post_id, '_iecg_width', $ccm4, true)
								or
						update_post_meta($post_id, '_iecg_width', $ccm4);
					}else{
						delete_post_meta($post_id, '_iecg_width');
					}
					
					$ccm5 = $_POST['_iecg_height'];
					if(!empty($ccm5)){
						add_post_meta($post_id, '_iecg_height', $ccm5, true)
								or
						update_post_meta($post_id, '_iecg_height', $ccm5);
					}else{
						delete_post_meta($post_id, '_iecg_height');
					}
					$ccm6 = $_POST['_iecg_sitename'];
					if(!empty($ccm6)){
						add_post_meta($post_id, '_iecg_sitename', $ccm6, true)
								or
						update_post_meta($post_id, '_iecg_sitename', $ccm6);
					}else{
						delete_post_meta($post_id, '_iecg_sitename');
					}
					
					$ccm7 = $_POST['_iecg_siteurl'];
					if(!empty($ccm7)){
						add_post_meta($post_id, '_iecg_siteurl', $ccm7, true)
								or
						update_post_meta($post_id, '_iecg_siteurl', $ccm7);
					}else{
						delete_post_meta($post_id, '_iecg_siteurl');
					}
				}*/
					
				/*}  else if ( 'page' == $_POST['post_type'] && 
					current_user_can( 'edit_page', $post_id ) )
			{
				$ccm = $_POST['_iecg_source'];
				if(!empty($ccm)){
					add_post_meta($post_id, '_iecg_source', $ccm, true)
							or
					update_post_meta($post_id, '_iecg_source', $ccm);
				}else{
					delete_post_meta($post_id, '_iecg_source');
				}
				
				$ccm1 = $_POST['_iecg_linkto'];
				if(!empty($ccm1)){
					add_post_meta($post_id, '_iecg_linkto', $ccm1, true)
							or
					update_post_meta($post_id, '_iecg_linkto', $ccm1);
				}else{
					delete_post_meta($post_id, '_iecg_linkto');
				}
				
				$ccm2 = $_POST['_iecg_altattribute'];
				if(!empty($ccm2)){
					add_post_meta($post_id, '_iecg_altattribute', $ccm2, true)
							or
					update_post_meta($post_id, '_iecg_altattribute', $ccm2);
				}else{
					delete_post_meta($post_id, '_iecg_altattribute');
				}
				
				$ccm3 = $_POST['_iecg_title'];
				if(!empty($ccm3)){
					add_post_meta($post_id, '_iecg_title', $ccm3, true)
							or
					update_post_meta($post_id, '_iecg_title', $ccm3);
				}else{
					delete_post_meta($post_id, '_iecg_title');
				}
				
				$ccm4 = $_POST['_iecg_width'];
				if(!empty($ccm4)){
					add_post_meta($post_id, '_iecg_width', $ccm4, true)
							or
					update_post_meta($post_id, '_iecg_width', $ccm4);
				}else{
					delete_post_meta($post_id, '_iecg_width');
				}
				
				$ccm5 = $_POST['_iecg_height'];
				if(!empty($ccm5)){
					add_post_meta($post_id, '_iecg_height', $ccm5, true)
							or
					update_post_meta($post_id, '_iecg_height', $ccm5);
				}else{
					delete_post_meta($post_id, '_iecg_height');
				}
				
				$ccm6 = $_POST['_iecg_sitename'];
				if(!empty($ccm6)){
					add_post_meta($post_id, '_iecg_sitename', $ccm6, true)
							or
					update_post_meta($post_id, '_iecg_sitename', $ccm6);
				}else{
					delete_post_meta($post_id, '_iecg_sitename');
				}
				
				$ccm7 = $_POST['_iecg_siteurl'];
				if(!empty($ccm7)){
					add_post_meta($post_id, '_iecg_siteurl', $ccm7, true)
							or
					update_post_meta($post_id, '_iecg_siteurl', $ccm7);
				}else{
					delete_post_meta($post_id, '_iecg_siteurl');
				}
			}*/
		}
	}
}

if (class_exists("EmbedCodeGenerator")) {
	$ccmessage = new EmbedCodeGenerator();
	
	function add_post_content($content) {	
		global $wp_query;
			$post_id = $wp_query->post->ID;
			$source = get_post_meta($post_id, '_iecg_source', true);
			$linkto = get_post_meta($post_id, '_iecg_linkto', true);
			$altattribute = get_post_meta($post_id, '_iecg_altattribute', true);
			$title = get_post_meta($post_id, '_iecg_title', true);
			$width = get_post_meta($post_id, '_iecg_width', true);
			$height = get_post_meta($post_id, '_iecg_height', true);
			$sitename = get_post_meta($post_id, '_iecg_sitename', true);
			$siteurl = get_post_meta($post_id, '_iecg_siteurl', true);
			
			if(!empty($source))
			{
				$content .= '<div style="clear:both"><p><strong>Embed This Image On Your Site</strong> (copy code below):<br/>
<textarea style="width:90%; height:40px; padding:5px;" readonly="readonly"><div style="clear:both"><a href="' . $linkto . '"><img src="' . $source . '"';
				if(!empty($title)){
				$content .= ' title="' . $title . '"';
				}
				if(!empty($altattribute)){
				$content .= ' alt="' . $altattribute . '"';
				}
				if(!empty($width)){
				$content .= ' width="' . $width . '"';
				}
				if(!empty($height)){
				 $content .= ' height="' . $height . '"';
				 }
				 
				 $content .=  ' border="0" /></a></div><div>';
				 
				 if(!empty($sitename)){
				 $content .= 'Courtesy of: <a href="' . $siteurl . '">' . $sitename . '</a>';
				 }
				 $content .= '</div></textarea></p></div>';
			}	
				return $content;
		}
		add_filter('the_content', 'add_post_content');
}



?>