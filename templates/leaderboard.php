<?php get_header(); ?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			//print_r($post);
//echo $foo;
			slwp_get_template_part( 'content', 'leaderboard' );

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
