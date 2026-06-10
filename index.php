<?php
/**
 * Template: Index (required fallback)
 *
 * WordPress requires index.php as the catch-all template for classic themes.
 *
 * @package KCDW\Theme
 */

get_header();
?>

<main id="main-content" class="page-content">
	<div class="page-content__inner">
		<?php if ( have_posts() ) : ?>
			<div class="news-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'parts/card', 'news' ); ?>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p>Nothing found.</p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
