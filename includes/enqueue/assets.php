<?php
/**
 * Enqueue theme assets directly (no build step)
 *
 * @package KCDW\Theme\Functions
 */

declare( strict_types = 1 );
namespace KCDW\Theme;

add_action( 'wp_enqueue_scripts',      __NAMESPACE__ . '\\enqueue_theme_assets' );
add_action( 'after_setup_theme',       __NAMESPACE__ . '\\register_editor_styles' );
add_action( 'init',                    __NAMESPACE__ . '\\register_block_pattern_categories' );

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

function register_editor_styles(): void {
	add_editor_style( 'assets/css/editor.css' );
}

function register_block_pattern_categories(): void {
	$categories = [
		'boxes'    => [ 'label' => __( 'Boxes', 'kcdw-theme' ) ],
		'heroes'   => [ 'label' => __( 'Heroes', 'kcdw-theme' ) ],
		'layouts'  => [ 'label' => __( 'Layouts', 'kcdw-theme' ) ],
		'sections' => [ 'label' => __( 'Sections', 'kcdw-theme' ) ],
	];
	foreach ( $categories as $name => $props ) {
		register_block_pattern_category( $name, $props );
	}
}
