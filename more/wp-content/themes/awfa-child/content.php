<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">

		<?php if ( is_single() ) : 
    if ( function_exists('yoast_breadcrumb') ) {
      	yoast_breadcrumb('<p id="breadcrumbs">','</p>');
    }
		?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php else : ?>
		<h1 class="entry-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
		<?php endif; // is_single() ?>

		<div class="entry-meta">
			<?php twentythirteen_entry_meta(); ?>
			<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
	<?php 
	if ( is_search() || is_author() || is_tag() || is_archive() || is_home () ) {
	
    the_post_thumbnail(array(100,100), array('class' => 'alignleft'));
	  the_excerpt();
	  ?>
		<p><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">Read More</a></p>
    <?php
	 }
	?>
		<?php 
		if ( is_single() || is_page() ) {

		the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentythirteen' ) );
		}
		?>
		<?//php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentythirteen' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
		<div class="page-links"><?php wp_link_pages('before= &after= &next_or_number=next &previouspagelink= Previous	&nextpagelink= Next'); ?>
		<?php global $multipage, $numpages, $page;
		if( $multipage && $page == $numpages ) {
		
			echo '<a href="../">Return to First Page</a>';
		
		} ?></div>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<footer class="entry-meta">
		<?php 
		if ( is_single() ) {
		  if ( comments_open() && ! is_single() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a comment', 'twentythirteen' ) . '</span>', __( 'One comment so far', 'twentythirteen' ), __( 'View all % comments', 'twentythirteen' ) ); ?>
			</div><!-- .comments-link -->
		<?php endif; // comments_open()
		}
		 ?>

		<?php if ( is_single() && get_the_author_meta( 'description' ) && is_multi_author() ) : ?>
			<?php get_template_part( 'author-bio' ); ?>
		<?php endif; ?>
	</footer><!-- .entry-meta -->
</article><!-- #post -->
