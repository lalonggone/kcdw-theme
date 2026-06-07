<?php
/**
 * Template Routing — FSE Block Theme Override
 *
 * FSE block themes hook template_include at priority 20 to swap in their
 * block renderer. We run at 99 so our PHP page templates win for pages that
 * have a custom template assigned.
 *
 * @package KCDW\Theme
 */

declare( strict_types = 1 );
namespace KCDW\Theme;

add_filter( 'template_include', function ( string $template ): string {

	if ( ! \is_singular( 'page' ) ) {
		return $template;
	}

	$slug = \get_page_template_slug(); // e.g. 'template-issue.php'
	if ( ! $slug ) {
		return $template;
	}

	$path = \get_theme_file_path( $slug );
	if ( \file_exists( $path ) ) {
		return $path;
	}

	return $template;

}, 99 );
