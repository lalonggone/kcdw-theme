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
					<article <?php post_class( 'news-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="news-card__image-link" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'medium_large', [ 'class' => 'news-card__image', 'loading' => 'lazy' ] ); ?>
							</a>
						<?php endif; ?>
						<div class="news-card__body">
							<time class="news-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php the_date(); ?></time>
							<h2 class="news-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p>Nothing found.</p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
