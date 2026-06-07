<?php
/**
 * Enqueue theme assets directly (no build step)
 *
 * @package KCDW\Theme\Functions
 */

declare( strict_types = 1 );
namespace KCDW\Theme;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_theme_assets' );

function enqueue_theme_assets(): void {
	wp_enqueue_style(
		'kcdw-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		[],
		THEME_VERSION
	);

	wp_enqueue_script(
		'kcdw-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		[],
		THEME_VERSION,
		[ 'strategy' => 'defer' ]
	);
}
