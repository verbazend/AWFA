<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

		</div><!-- #main -->
        </div><!-- #page -->
        <div id="footerWrap">
		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php get_sidebar( 'main' ); ?>

			<div class="site-info">

				<a href="/terms-conditions.html" title="Terms &amp; Conditions">&copy; AUSTRALIA WIDE FIRST AID 2013  |  NATIONAL PROVIDER NUMBER 31961</a>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #footerWrap -->

	<?php wp_footer(); ?>
</body>
</html>