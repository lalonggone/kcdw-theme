<?php
/**
 * Template: 404 Not Found
 *
 * @package KCDW\Theme
 */

get_header();
?>

<main id="main-content" class="error-404">
	<div class="error-404__inner">
		<p class="section__eyebrow">404</p>
		<h1>Page Not Found</h1>
		<p>The page you're looking for doesn't exist or has been moved.</p>
		<a class="btn btn--sienna" href="<?php echo esc_url( home_url( '/' ) ); ?>">Return Home</a>
	</div>
</main>

<?php get_footer(); ?>
