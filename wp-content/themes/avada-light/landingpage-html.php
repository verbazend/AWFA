<?php
// Template Name: HTML Landing Page


//get_header(); global $data; ?>
<?php while(have_posts()): the_post(); ?>
<?php echo do_shortcode(the_content()); ?>
<?php endwhile; ?>