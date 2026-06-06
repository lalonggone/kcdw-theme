<?php
/**
 * WP-CLI Migration Script: Divi Shortcodes → Gutenberg Block Markup
 *
 * Finds every published page whose post_content contains Divi shortcodes,
 * converts the readable content to block markup, and either previews the
 * result or writes it to the DB.
 *
 * Usage:
 *   wp eval-file _dev-docs/migrate-divi-to-blocks.php            # preview
 *   wp eval-file _dev-docs/migrate-divi-to-blocks.php -- --write # write to DB
 *
 * Always backup first:
 *   wp db export backup-pre-migration-$(date +%Y%m%d).sql
 */

if ( ! class_exists( 'WP_CLI' ) ) {
	die( 'This script must be run via WP-CLI.' );
}

// ---------------------------------------------------------------------------
// Config
// ---------------------------------------------------------------------------

$write_mode = in_array( '--write', $args ?? [], true );

$stats = [
	'processed' => 0,
	'had_divi'  => 0,
	'converted' => 0,
	'skipped'   => 0,
];

// Divi structural wrappers — strip the tags but keep inner content.
const DIVI_STRUCTURAL = [
	'et_pb_section',
	'et_pb_row',
	'et_pb_row_inner',
	'et_pb_column',
	'et_pb_column_inner',
];

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Parse key="value" attribute pairs from a shortcode attribute string.
 */
function kcdw_parse_attrs( string $raw ): array {
	$attrs = [];
	// Match both single- and double-quoted values.
	preg_match_all( '/(\w+)=["\']([^"\']*)["\']/', $raw, $m, PREG_SET_ORDER );
	foreach ( $m as $match ) {
		$attrs[ $match[1] ] = html_entity_decode( $match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}
	return $attrs;
}

/**
 * Convert a chunk of HTML (from et_pb_text inner content) into block markup.
 * Handles <p>, <h1-h6>, <ul>, <ol>, <blockquote>, <figure>.
 */
function kcdw_html_to_blocks( string $html ): string {
	$html = trim( $html );
	if ( $html === '' ) {
		return '';
	}

	$blocks = [];

	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML(
		'<html><head><meta charset="utf-8"></head><body>' . $html . '</body></html>',
		LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();

	$body = $dom->getElementsByTagName( 'body' )->item( 0 );
	if ( ! $body ) {
		return '';
	}

	foreach ( $body->childNodes as $node ) {
		if ( $node->nodeType !== XML_ELEMENT_NODE ) {
			// Plain text node — wrap in paragraph.
			$text = trim( $node->textContent );
			if ( $text !== '' ) {
				$blocks[] = "<!-- wp:paragraph -->\n<p>{$text}</p>\n<!-- /wp:paragraph -->";
			}
			continue;
		}

		$tag      = strtolower( $node->nodeName );
		$inner    = kcdw_inner_html( $dom, $node );
		$text     = trim( $node->textContent );

		switch ( $tag ) {
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
				$level    = (int) substr( $tag, 1 );
				$blocks[] = "<!-- wp:heading {\"level\":{$level}} -->\n<{$tag} class=\"wp-block-heading\">{$inner}</{$tag}>\n<!-- /wp:heading -->";
				break;

			case 'p':
				if ( $inner !== '' ) {
					$blocks[] = "<!-- wp:paragraph -->\n<p>{$inner}</p>\n<!-- /wp:paragraph -->";
				}
				break;

			case 'ul':
			case 'ol':
				$items   = kcdw_list_items( $dom, $node );
				$ordered = ( $tag === 'ol' ) ? ' {"ordered":true}' : '';
				if ( $items ) {
					$blocks[] = "<!-- wp:list{$ordered} -->\n<{$tag}>" . implode( '', $items ) . "</{$tag}>\n<!-- /wp:list -->";
				}
				break;

			case 'blockquote':
				$blocks[] = "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>{$inner}</p></blockquote>\n<!-- /wp:quote -->";
				break;

			case 'figure':
			case 'img':
				// Images handled separately via et_pb_image; skip orphan figures.
				break;

			default:
				// Divs, spans, etc — pull out the text content as a paragraph.
				if ( $text !== '' ) {
					$blocks[] = "<!-- wp:paragraph -->\n<p>{$text}</p>\n<!-- /wp:paragraph -->";
				}
				break;
		}
	}

	return implode( "\n\n", array_filter( $blocks ) );
}

/** Return the inner HTML of a DOMNode. */
function kcdw_inner_html( DOMDocument $dom, DOMNode $node ): string {
	$html = '';
	foreach ( $node->childNodes as $child ) {
		$html .= $dom->saveHTML( $child );
	}
	return trim( $html );
}

/** Return array of <li> HTML strings from a list node. */
function kcdw_list_items( DOMDocument $dom, DOMNode $list ): array {
	$items = [];
	foreach ( $list->childNodes as $child ) {
		if ( strtolower( $child->nodeName ) === 'li' ) {
			$items[] = '<li>' . trim( kcdw_inner_html( $dom, $child ) ) . '</li>';
		}
	}
	return $items;
}

/**
 * Convert a single Divi content module to one or more block strings.
 * Returns empty string for structural/unknown types.
 */
function kcdw_convert_module( string $type, string $attrs_raw, string $inner ): string {
	$attrs = kcdw_parse_attrs( $attrs_raw );

	switch ( $type ) {

		// Rich-text module — inner content is HTML.
		case 'et_pb_text':
			return kcdw_html_to_blocks( $inner );

		// Heading module — title attr or inline HTML.
		case 'et_pb_heading':
			$title = $attrs['title'] ?? trim( wp_strip_all_tags( $inner ) );
			$level = isset( $attrs['level'] ) ? (int) ltrim( $attrs['level'], 'hH' ) : 2;
			$level = max( 1, min( 6, $level ) );
			if ( $title === '' ) {
				return '';
			}
			return "<!-- wp:heading {\"level\":{$level}} -->\n<h{$level} class=\"wp-block-heading\">{$title}</h{$level}>\n<!-- /wp:heading -->";

		// Image module.
		case 'et_pb_image':
			$src = $attrs['src'] ?? '';
			$alt = $attrs['alt'] ?? $attrs['title_text'] ?? '';
			if ( $src === '' ) {
				return '';
			}
			$id = attachment_url_to_postid( $src );
			$id_attr  = $id ? " {\"id\":{$id},\"sizeSlug\":\"large\",\"linkDestination\":\"none\"}" : ' {"sizeSlug":"large","linkDestination":"none"}';
			$id_class = $id ? " wp-image-{$id}" : '';
			return "<!-- wp:image{$id_attr} -->\n<figure class=\"wp-block-image size-large\"><img src=\"" . esc_url( $src ) . "\" alt=\"" . esc_attr( $alt ) . "\" class=\"wp-image{$id_class}\"/></figure>\n<!-- /wp:image -->";

		// Button module.
		case 'et_pb_button':
			$text = $attrs['button_text'] ?? '';
			$url  = $attrs['button_url'] ?? '#';
			if ( $text === '' ) {
				return '';
			}
			return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">"
				. "<!-- wp:button -->"
				. "<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url( $url ) . "\">{$text}</a></div>"
				. "<!-- /wp:button -->"
				. "</div>\n<!-- /wp:buttons -->";

		// CTA module — title + body + button.
		case 'et_pb_cta':
			$parts = [];
			$title = $attrs['title'] ?? '';
			if ( $title !== '' ) {
				$parts[] = "<!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">{$title}</h2>\n<!-- /wp:heading -->";
			}
			$body = kcdw_html_to_blocks( $inner );
			if ( $body !== '' ) {
				$parts[] = $body;
			}
			$btn_text = $attrs['button_text'] ?? '';
			$btn_url  = $attrs['button_url'] ?? '#';
			if ( $btn_text !== '' ) {
				$parts[] = "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">"
					. "<!-- wp:button -->"
					. "<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url( $btn_url ) . "\">{$btn_text}</a></div>"
					. "<!-- /wp:button -->"
					. "</div>\n<!-- /wp:buttons -->";
			}
			return implode( "\n\n", array_filter( $parts ) );

		// Blurb — title + optional inner text.
		case 'et_pb_blurb':
			$parts = [];
			$title = $attrs['title'] ?? '';
			if ( $title !== '' ) {
				$parts[] = "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$title}</h3>\n<!-- /wp:heading -->";
			}
			$body = kcdw_html_to_blocks( $inner );
			if ( $body !== '' ) {
				$parts[] = $body;
			}
			return implode( "\n\n", array_filter( $parts ) );

		// All other types (structural, third-party, unknown) — discard.
		default:
			return '';
	}
}

/**
 * Main conversion: Divi post_content → Gutenberg block markup.
 *
 * Strategy:
 *   1. Strip structural wrapper tags (section/row/column), keep inner content.
 *   2. Walk content sequentially, converting known content modules.
 *   3. Strip any remaining shortcode-like tokens.
 *   4. Clean up whitespace.
 */
function kcdw_convert_divi_to_blocks( string $content ): string {
	// Step 1 — remove structural wrappers but preserve inner content.
	foreach ( DIVI_STRUCTURAL as $tag ) {
		$content = preg_replace( '/\[' . $tag . '[^\]]*\]/', '', $content );
		$content = preg_replace( '/\[\/' . $tag . '\]/', '', $content );
	}

	// Step 2 — convert content modules in document order.
	// Matches: [module attrs]inner[/module]  or  [module attrs /]
	$pattern = '/\[et_pb_(\w+)([^\]]*?)(?:\/\]|\](.*?)\[\/et_pb_\1\])/si';

	$blocks = [];
	preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

	foreach ( $matches as $m ) {
		$type  = $m[1];
		$attrs = $m[2];
		$inner = $m[3] ?? '';
		$block = kcdw_convert_module( $type, $attrs, $inner );
		if ( $block !== '' ) {
			$blocks[] = $block;
		}
	}

	// Step 3 — if we found nothing via the pattern (edge-case content),
	// attempt to salvage any leftover HTML after stripping all shortcodes.
	if ( empty( $blocks ) ) {
		$stripped = preg_replace( '/\[[^\]]+\]/', '', $content );
		$stripped = trim( wp_strip_all_tags( $stripped ) );
		if ( $stripped !== '' ) {
			$blocks[] = "<!-- wp:paragraph -->\n<p>{$stripped}</p>\n<!-- /wp:paragraph -->";
		}
	}

	// Step 4 — remove any remaining shortcode tokens from individual blocks.
	$output = implode( "\n\n", $blocks );
	$output = preg_replace( '/\[[^\]]+\]/', '', $output ); // strip leftovers
	$output = preg_replace( '/\n{3,}/', "\n\n", $output );  // normalize whitespace

	return trim( $output );
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

WP_CLI::line( '' );
WP_CLI::log( WP_CLI::colorize( '%BKane Creek Development Watch — Divi → Blocks Migration%n' ) );
WP_CLI::log( WP_CLI::colorize( $write_mode ? '%YWRITE MODE — changes will be saved to the DB%n' : '%GPREVIEW MODE — no changes will be written%n' ) );
WP_CLI::line( '' );

global $wpdb;

$pages = $wpdb->get_results(
	"SELECT ID, post_title, post_content
	 FROM {$wpdb->posts}
	 WHERE post_type    = 'page'
	 AND   post_status  = 'publish'
	 ORDER BY post_title ASC"
);

if ( empty( $pages ) ) {
	WP_CLI::warning( 'No published pages found.' );
	exit;
}

foreach ( $pages as $page ) {
	$stats['processed']++;

	$has_divi = strpos( $page->post_content, '[et_pb_' ) !== false;

	if ( ! $has_divi ) {
		$stats['skipped']++;
		WP_CLI::log( WP_CLI::colorize( "%8SKIP%n  [{$page->ID}] {$page->post_title} — no Divi content" ) );
		continue;
	}

	$stats['had_divi']++;
	$converted = kcdw_convert_divi_to_blocks( $page->post_content );

	if ( $converted === '' ) {
		$stats['skipped']++;
		WP_CLI::warning( "[{$page->ID}] {$page->post_title} — conversion produced empty output, skipping" );
		continue;
	}

	// Preview output.
	WP_CLI::line( WP_CLI::colorize( '%B' . str_repeat( '─', 60 ) . '%n' ) );
	WP_CLI::log( WP_CLI::colorize( "%WPAGE%n [{$page->ID}] {$page->post_title}" ) );
	WP_CLI::line( '' );
	WP_CLI::log( WP_CLI::colorize( '%8--- ORIGINAL (first 300 chars) ---%n' ) );
	WP_CLI::log( substr( $page->post_content, 0, 300 ) . '…' );
	WP_CLI::line( '' );
	WP_CLI::log( WP_CLI::colorize( '%G--- CONVERTED ---%n' ) );
	WP_CLI::log( $converted );
	WP_CLI::line( '' );

	if ( $write_mode ) {
		$result = wp_update_post( [
			'ID'           => (int) $page->ID,
			'post_content' => $converted,
		], true );

		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( "Failed to update [{$page->ID}] {$page->post_title}: " . $result->get_error_message() );
			$stats['skipped']++;
		} else {
			WP_CLI::success( "Updated [{$page->ID}] {$page->post_title}" );
			$stats['converted']++;
		}
	} else {
		$stats['converted']++;
	}
}

// ---------------------------------------------------------------------------
// Summary
// ---------------------------------------------------------------------------

WP_CLI::line( WP_CLI::colorize( '%B' . str_repeat( '═', 60 ) . '%n' ) );
WP_CLI::log( WP_CLI::colorize( '%WSUMMARY%n' ) );
WP_CLI::log( "  Pages processed : {$stats['processed']}" );
WP_CLI::log( "  Had Divi content: {$stats['had_divi']}" );
WP_CLI::log( "  Converted       : {$stats['converted']}" );
WP_CLI::log( "  Skipped         : {$stats['skipped']}" );

if ( ! $write_mode ) {
	WP_CLI::line( '' );
	WP_CLI::log( WP_CLI::colorize( '%YReview the output above, then run with --write to commit changes.%n' ) );
	WP_CLI::log( 'wp eval-file _dev-docs/migrate-divi-to-blocks.php -- --write' );
}

WP_CLI::line( '' );
