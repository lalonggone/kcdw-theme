<?php
/**
 * [press_url] shortcode — renders the press_url SCF field as a link.
 * Used in the single-press_coverage block template.
 *
 * @package KCDW\Theme\Shortcodes
 */

declare( strict_types = 1 );
namespace KCDW\Theme;

add_shortcode( 'press_url', function (): string {
	$url = \get_field( 'press_url' );
	if ( ! $url ) {
		return '';
	}
	$label = __( 'Read the original article', 'kcdw-theme' );
	return '<a class="press-single__link" href="' . \esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . \esc_html( $label ) . ' &rarr;</a>';
} );
