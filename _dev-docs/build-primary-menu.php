<?php
/**
 * WP-CLI Script: Build the KCDW Primary Navigation Menu
 *
 * Rebuilds the menu assigned to the `primary` theme location to the canonical
 * KCDW information architecture: four section parents with dropdown children,
 * plus About and Donate. Pages are resolved BY PATH (slug), never by ID, so the
 * same script produces the correct menu on local, staging, and production even
 * though post IDs differ between environments.
 *
 * Idempotent: re-running rebuilds the menu to the exact same state. Existing
 * items in the target menu are removed and recreated from the spec below.
 *
 * Usage:
 *   wp eval-file wp-content/themes/kcdw/_dev-docs/build-primary-menu.php        # dry run
 *   wp eval-file wp-content/themes/kcdw/_dev-docs/build-primary-menu.php write  # write to DB
 *
 * Always backup first:
 *   wp db export backup-pre-menu-$(date +%Y%m%d).sql
 */

if ( ! class_exists( 'WP_CLI' ) ) {
	die( 'Run via WP-CLI.' );
}

$write_mode    = in_array( 'write', $args, true );
$location      = 'primary';
$menu_fallback = 'Primary Navigation'; // Used only if no menu is assigned to the location yet.

/**
 * Canonical menu tree. Each entry: 'path' (hierarchical page path / slug used by
 * get_page_by_path), 'label' (nav title), and optional 'children'.
 */
$tree = [
	[ 'path' => 'the-fight', 'label' => 'The Fight', 'children' => [
		[ 'path' => 'the-fight/water-rights',          'label' => 'Water Rights' ],
		[ 'path' => 'the-fight/floodplain-flood-risk', 'label' => 'Floodplain & Flood Risk' ],
		[ 'path' => 'the-fight/the-fake-town',         'label' => 'The Fake Town' ],
		[ 'path' => 'the-fight/affordable-housing',    'label' => 'Affordable Housing' ],
		[ 'path' => 'the-fight/cultural-resources',    'label' => 'Cultural Resources' ],
		[ 'path' => 'the-fight/meet-the-developer',    'label' => 'Meet the Developer' ],
	] ],
	[ 'path' => 'our-lawsuits', 'label' => 'Our Lawsuits', 'children' => [
		[ 'path' => 'our-lawsuits/water-rights-lawsuit',   'label' => 'Water Rights Lawsuit' ],
		[ 'path' => 'our-lawsuits/latest-information-2',    'label' => 'SB258' ],
	] ],
	[ 'path' => 'take-action', 'label' => 'Take Action', 'children' => [
		[ 'path' => 'take-action/sign-the-petition',  'label' => 'Sign the Petition' ],
		[ 'path' => 'take-action/contact-officials',  'label' => 'Contact Officials' ],
		[ 'path' => 'take-action/show-up',            'label' => 'Show Up' ],
		[ 'path' => 'take-action/spread-the-word',    'label' => 'Spread the Word' ],
	] ],
	[ 'path' => 'in-the-news', 'label' => 'In the News', 'children' => [
		[ 'path' => 'in-the-news/press-coverage',     'label' => 'Press Coverage' ],
		[ 'path' => 'in-the-news/newsletter-archive', 'label' => 'Newsletter Archive' ],
	] ],
	[ 'path' => 'about',  'label' => 'About' ],
	// Donate is intentionally omitted — the header has a dedicated Donate button.
];

$missing = [];

/**
 * Add one nav item (and recurse into children). Resolves the page by path;
 * records and skips any page that cannot be found so the menu degrades cleanly.
 *
 * @return int Number of items written.
 */
function kcdw_add_menu_item( int $menu_id, array $node, int $parent_item_id, bool $write, array &$missing, int $depth = 0 ): int {
	$indent = str_repeat( '    ', $depth );
	$page   = get_page_by_path( $node['path'], OBJECT, 'page' );

	if ( ! $page ) {
		WP_CLI::warning( "{$indent}MISSING page for path '{$node['path']}' — skipped." );
		$missing[] = $node['path'];
		return 0;
	}

	WP_CLI::log( $indent . WP_CLI::colorize( "%g{$node['label']}%n" ) . " → [{$page->ID}] /{$node['path']}" );

	$count   = 0;
	$item_id = 0;

	if ( $write ) {
		$item_id = wp_update_nav_menu_item( $menu_id, 0, [
			'menu-item-title'     => $node['label'],
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $page->ID,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent_item_id,
		] );
		if ( is_wp_error( $item_id ) ) {
			WP_CLI::warning( "{$indent}Failed to add '{$node['label']}': " . $item_id->get_error_message() );
			return 0;
		}
		$count++;
	}

	foreach ( $node['children'] ?? [] as $child ) {
		$count += kcdw_add_menu_item( $menu_id, $child, $item_id, $write, $missing, $depth + 1 );
	}

	return $count;
}

// ---------------------------------------------------------------------------
// Resolve (or create) the menu assigned to the primary location.
// ---------------------------------------------------------------------------

$locations = get_nav_menu_locations();
$menu_id   = isset( $locations[ $location ] ) ? (int) $locations[ $location ] : 0;
$menu_obj  = $menu_id ? wp_get_nav_menu_object( $menu_id ) : false;

if ( $menu_obj ) {
	WP_CLI::log( "Target menu: '{$menu_obj->name}' (id {$menu_id}), assigned to location '{$location}'." );
} else {
	WP_CLI::log( "No menu assigned to location '{$location}'." );
	if ( $write_mode ) {
		$menu_id = wp_create_nav_menu( $menu_fallback );
		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::error( 'Could not create menu: ' . $menu_id->get_error_message() );
		}
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		WP_CLI::log( "Created menu '{$menu_fallback}' (id {$menu_id}) and assigned it to '{$location}'." );
	} else {
		WP_CLI::log( "(dry run) Would create '{$menu_fallback}' and assign it to '{$location}'." );
	}
}

// ---------------------------------------------------------------------------
// Clear existing items, then rebuild from the spec.
// ---------------------------------------------------------------------------

if ( $menu_id ) {
	$existing = wp_get_nav_menu_items( $menu_id ) ?: [];
	WP_CLI::log( ( $write_mode ? 'Removing' : '(dry run) Would remove' ) . ' ' . count( $existing ) . ' existing item(s).' );
	if ( $write_mode ) {
		foreach ( $existing as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}
}

WP_CLI::log( '' );
WP_CLI::log( WP_CLI::colorize( '%9Building primary menu:%n' ) );

$written = 0;
foreach ( $tree as $node ) {
	$written += kcdw_add_menu_item( $menu_id, $node, 0, $write_mode, $missing );
}

// ---------------------------------------------------------------------------
// Summary
// ---------------------------------------------------------------------------

WP_CLI::log( '' );
if ( $missing ) {
	WP_CLI::warning( count( $missing ) . ' page(s) not found by path: ' . implode( ', ', $missing ) );
}

if ( $write_mode ) {
	WP_CLI::success( "Primary menu rebuilt: {$written} items written." );
} else {
	WP_CLI::log( WP_CLI::colorize( '%3DRY RUN%n — no changes written. Re-run with `write` to apply.' ) );
}
