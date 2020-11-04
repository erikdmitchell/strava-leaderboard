<?php get_header(); ?>
<?php $data = check_acf(get_the_ID()); ?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			slwp_get_template_part( 'leaderboard', $data['content_type'], $data );

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
