<?php
/**
 * Template: Default Page
 *
 * @package KCDW\Theme
 */

get_header();
?>

<main id="main-content" class="page-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="page-content__inner">
			<?php the_content(); ?>
		</div>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
