<?php get_header(); ?>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="<?php echo site_url() ?>/embed-codes/script.js" type="text/javascript"></script>

	<?php $wp_query = $backup_wp_query; ?>
	<?php
	$content_css = '';
	$sidebar_css = '';
	if(get_post_meta($post->ID, 'pyre_full_width', true) == 'yes') {
		$content_css = 'width:100%';
		$sidebar_css = 'display:none';
	}
	elseif(get_post_meta($post->ID, 'pyre_sidebar_position', true) == 'left') {
		$content_css = 'float:right;';
		$sidebar_css = 'float:left;';
	} elseif(get_post_meta($post->ID, 'pyre_sidebar_position', true) == 'right') {
		$content_css = 'float:left;';
		$sidebar_css = 'float:right;';
	} elseif(get_post_meta($post->ID, 'pyre_sidebar_position', true) == 'default') {
		if($data['default_sidebar_pos'] == 'Left') {
			$content_css = 'float:right;';
			$sidebar_css = 'float:left;';
		} elseif($data['default_sidebar_pos'] == 'Right') {
			$content_css = 'float:left;';
			$sidebar_css = 'float:right;';
		}
	}
	if(class_exists('Woocommerce')) {
		if(is_cart() || is_checkout() || is_account_page() || (get_option('woocommerce_thanks_page_id') && is_page(get_option('woocommerce_thanks_page_id')))) {
			$content_css = 'width:100%';
			$sidebar_css = 'display:none';
		}
	}
	?>
	<div id="content" style="<?php echo $content_css; ?>">
		<?php
		global $query_string;
		query_posts($query_string);

		if(function_exists('barley_wrap_the_title') && current_user_can('edit_post') && is_page()):
			$wp_query = $backup_wp_query;
		endif;

		if(have_posts()): the_post();
		?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<span class="entry-title" style="display: none;"><?php the_title(); ?></span>
			<span class="vcard" style="display: none;"><span class="fn"><?php the_author_posts_link(); ?></span></span>
			<?php global $data; if(!$data['featured_images_pages'] && has_post_thumbnail()): ?>
			<div class="image">
				<?php the_post_thumbnail('blog-large'); ?>
			</div>
			<?php endif; ?>
			<div class="post-content">
				<?php the_content(); ?>
			
				<div class="input">
					<input type="text" name="name" id="name" value="My Company"><br/>
					<input type="radio" class="scheme" name="scheme" id="light" value="light" checked>
					<label for="light">Light</label>
					<input type="radio" class="scheme" name="scheme" id="dark" value="dark">
					<label for="dark">Dark</label>
					<input type="checkbox" class="background" name="background" id="transparent" value="transparent">
					<label for="transparent">Transparent Background</label>
				</div>
				
				<div class="container">
					<input type="radio" class="badge-select" name="badge-select" id="badge-one" value="badge-one">
					<label for="badge-one">
						<div class="badge one selected">
							<img src="/embed-codes/images/badge-1.png" class="badge-img">
							<span class="name"></span>
							<span class="label">First Aid Certified</span>
						</div>
					</label>
				</div>
				
				<div class="container">
					<input type="radio" class="badge-select" name="badge-select" id="badge-two" value="badge-two">
					<label for="badge-two">
						<div class="badge two">
							<img src="/embed-codes/images/badge-2.png" class="badge-img">
							<span class="name"></span>
							<span class="label">First Aid Certified</span>
						</div>
					</label>
				</div>
				
				<div class="container">		
					<input type="radio" class="badge-select" name="badge-select" id="badge-three" value="badge-three">
					<label for="badge-three">
						<div class="badge three">
						<table><tr><td>
							<img src="/embed-codes/images/badge-3.png" class="badge-img">
						</td><td>
							<span class="name"></span>
							<span class="label">First Aid Certified</span>
						</td></tr></table>
						</div>
					</label>
				</div>
				
				<div class="container">		
					<input type="radio" class="badge-select" name="badge-select" id="badge-four" value="badge-four">
					<label for="badge-four">		
						<div class="badge four">
						<table><tr><td>
							<img src="/embed-codes/images/badge-4.png" class="badge-img">
						</td><td>
							<span class="name"></span>
							<span class="label">First Aid Certified</span>
						</td></tr></table>
						</div>
					</label>
				</div>
				
				<textarea class="output" disabled></textarea>




				<?php wp_link_pages(); ?>
			</div>
			<?php if(class_exists('Woocommerce')): ?>
			<?php if($data['comments_pages'] && !is_cart() && !is_checkout() && !is_account_page() && !is_page(get_option('woocommerce_thanks_page_id'))): ?>
				<?php wp_reset_query(); ?>
				<?php comments_template(); ?>
			<?php endif; ?>
			<?php else: ?>
			<?php if($data['comments_pages']): ?>
				<?php wp_reset_query(); ?>
				<?php comments_template(); ?>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<div id="sidebar" style="<?php echo $sidebar_css; ?>"><?php generated_dynamic_sidebar(); ?></div>
<?php get_footer(); ?>