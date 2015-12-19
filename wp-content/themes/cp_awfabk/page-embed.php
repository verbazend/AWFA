<?php get_header(); ?>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="<?php echo site_url() ?>/embed-codes/script.js" type="text/javascript"></script>

	<div id="content" class="full-width">
		<?php while(have_posts()): the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<span class="entry-title" style="display: none;"><?php the_title(); ?></span>
			<span class="vcard" style="display: none;"><span class="fn"><?php the_author_posts_link(); ?></span></span>
			<?php global $data; if(!$data['featured_images_pages'] && has_post_thumbnail()): ?>
			<div class="image">
				<?php the_post_thumbnail('full'); ?>
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
			<?php if($data['comments_pages']): ?>
				<?php wp_reset_query(); ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		</div>
		<?php endwhile; ?>
	</div>
<?php get_footer(); ?>