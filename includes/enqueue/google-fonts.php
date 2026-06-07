<?php
/**
 * Enqueue Google Fonts: Barlow Condensed, Barlow, IBM Plex Mono
 *
 * @package CassidyDC\BlockTheme\Functions
 */

declare( strict_types = 1 );
namespace CassidyDC\BlockTheme;

add_action( 'wp_enqueue_scripts',      __NAMESPACE__ . '\\enqueue_google_fonts' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_google_fonts' );
add_action( 'wp_head',                 __NAMESPACE__ . '\\google_fonts_preconnect', 1 );

function enqueue_google_fonts(): void {
	wp_enqueue_style(
		'kcdw-google-fonts',
		'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@900&family=Barlow:wght@400;600&family=IBM+Plex+Mono:wght@400;500&display=swap',
		[],
		null
	);
}

function google_fonts_preconnect(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
