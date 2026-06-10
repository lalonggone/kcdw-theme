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
						<?php get_template_part( 'parts/card', 'news' ); ?>
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
