<?php
/**
 * Template: Archive
 *
 * @package KCDW\Theme
 */

get_header();
?>

<main id="main-content" class="archive-content">

	<header class="archive-content__header">
		<div class="archive-content__header-inner">
			<h1><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="archive-content__description">', '</div>' ); ?>
		</div>
	</header>

	<div class="archive-content__posts">
		<div class="archive-content__posts-inner">
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
				<?php the_posts_pagination( [ 'class' => 'archive-pagination' ] ); ?>
			<?php else : ?>
				<p>No posts found.</p>
			<?php endif; ?>
		</div>
	</div>

</main>

<?php get_footer(); ?>
