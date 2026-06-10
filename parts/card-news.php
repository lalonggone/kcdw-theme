<?php
/**
 * Partial: News Card
 *
 * Renders one post as a news card. Single source of truth for the news-card
 * markup shared by the latest-news loops in `front-page.php`, `archive.php`,
 * and `index.php`.
 *
 * MUST be called inside a post loop — `the_post()` has to have run so the
 * template tags (the_permalink(), the_title(), etc.) resolve to the current
 * post. On the front page that's the custom `$kcdw_news` WP_Query loop; on the
 * archive/index it's the main loop.
 *
 * Args (passed via the 3rd `get_template_part()` parameter, read from $args):
 *   - heading_level (string) Title heading tag. 'h2' (default) on the archive
 *     and index; 'h3' inside the front-page "Latest Updates" section, where the
 *     section already owns the <h2>. Whitelisted to h2/h3.
 *
 * @package KCDW\Theme
 */

$kcdw_heading = isset( $args['heading_level'] ) ? $args['heading_level'] : 'h2';
if ( ! in_array( $kcdw_heading, array( 'h2', 'h3' ), true ) ) {
	$kcdw_heading = 'h2';
}
?>
<article <?php post_class( 'news-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="news-card__image-link" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'medium_large', array( 'class' => 'news-card__image', 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>
	<div class="news-card__body">
		<time class="news-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php the_date(); ?></time>
		<<?php echo esc_html( $kcdw_heading ); ?> class="news-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo esc_html( $kcdw_heading ); ?>>
		<?php the_excerpt(); ?>
	</div>
</article>
