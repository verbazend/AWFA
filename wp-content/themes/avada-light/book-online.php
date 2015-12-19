<?php
// Template Name: Book Online - Mini
get_header(); global $data; ?>
<div class="book-online-mini">
	<div id="content" style="<?php echo $content_css; ?>">
		<?php while(have_posts()): the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-content">
				<?php the_content(); ?>
				<?php dynamic_sidebar('enrol form'); ?>
			</div>

		</div>
		<?php endwhile; ?>
	</div>
</div>
<?php get_footer(); ?>