<?php
/**
 * Theme Functions
 *
 * @package KCDW\Theme\Functions
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @version 1.0.0
 */

declare( strict_types = 1 );
namespace KCDW\Theme;

/**
 * Create theme constants.
 *
 * @since 1.0.0
 */
define( 'THEME_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'HANDLE_PREFIX', 'kcdw' );

/**
 * Classic theme setup — menus, post thumbnails, title tag, HTML5 markup.
 *
 * @since 1.0.0
 */
add_action( 'after_setup_theme', function (): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

	register_nav_menus( [
		'primary'      => __( 'Primary Navigation', 'kcdw' ),
		'footer-issues' => __( 'Footer: The Issues', 'kcdw' ),
		'footer-action' => __( 'Footer: Take Action', 'kcdw' ),
	] );
} );

/**
 * Enqueue theme assets
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/enqueue/assets.php' );

/**
 * Enqueue Google Fonts
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/enqueue/google-fonts.php' );

/**
 * Register SCF field groups
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/fields/register-fields.php' );

/**
 * Register Press Coverage custom post type
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/core/press-cpt.php' );

/**
 * Modify WordPress admin bar
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/core/admin-bar.php' );

/**
 * Modify WordPress permissions
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/core/admin-permissions.php' );

/**
 * Removes unwanted Core WordPress features
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/core/wp-cleanup.php' );

/**
 * Add Favicons to HTML head
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/head/favicons.php' );

/**
 * Create [year] shortcode
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/shortcodes/year.php' );

/**
 * Create [press_url] shortcode for single press coverage template
 *
 * @since 1.0.0
 */
require_once get_theme_file_path( 'includes/shortcodes/press-url.php' );
