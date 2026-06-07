<?php
/**
 * Press Coverage custom post type
 *
 * @package CassidyDC\BlockTheme
 */

declare( strict_types = 1 );
namespace CassidyDC\BlockTheme;

add_action( 'init', function () {

	\register_post_type( 'press_coverage', [
		'labels' => [
			'name'               => 'Press Coverage',
			'singular_name'      => 'Press Item',
			'add_new_item'       => 'Add Press Item',
			'edit_item'          => 'Edit Press Item',
			'new_item'           => 'New Press Item',
			'view_item'          => 'View Press Item',
			'search_items'       => 'Search Press Coverage',
			'not_found'          => 'No press coverage found.',
			'not_found_in_trash' => 'No press coverage in trash.',
		],
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-media-text',
		'menu_position' => 25,
		'supports'     => [ 'title' ],
		'rewrite'      => [ 'slug' => 'press' ],
	] );

} );
