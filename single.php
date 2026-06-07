<?php
/**
 * Template: Single Post
 *
 * @package KCDW\Theme
 */

get_header();
?>

<main id="main-content" class="single-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'single-content__article' ); ?>>

			<header class="single-content__header">
				<div class="single-content__header-inner">
					<time class="single-content__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php the_date(); ?></time>
					<h1 class="single-content__title"><?php the_title(); ?></h1>
				</div>
			</header>

			<div class="single-content__body">
				<div class="single-content__body-inner">
					<?php the_content(); ?>
				</div>
			</div>

		</article>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
