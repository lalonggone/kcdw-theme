<?php
/**
 * SCF (Secure Custom Fields) — Local Field Group Registration
 *
 * Only three field groups live here:
 *
 *   1. Site Options  — alert bar + petition count (options page)
 *   2. Press Coverage — fields on the press_coverage CPT
 *
 * Everything else is hardcoded in PHP templates.
 *
 * @package CassidyDC\BlockTheme\Fields
 */

declare( strict_types = 1 );
namespace CassidyDC\BlockTheme;

add_action( 'init', function () {

	if ( ! \function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Options page.
	if ( \function_exists( 'acf_add_options_page' ) ) {
		\acf_add_options_page( [
			'page_title' => 'KCDW Site Options',
			'menu_title' => 'Site Options',
			'menu_slug'  => 'kcdw-site-options',
			'capability' => 'manage_options',
			'position'   => 30,
			'icon_url'   => 'dashicons-admin-site',
			'redirect'   => false,
		] );
	}

	// -------------------------------------------------------------------------
	// 1. Site Options — alert bar + petition count
	// -------------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_site_options',
		'title'    => 'KCDW Site Options',
		'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'kcdw-site-options' ] ] ],
		'fields'   => [

			[
				'key'           => 'field_kcdw_alert_enabled',
				'label'         => 'Alert Bar Enabled',
				'name'          => 'alert_bar_enabled',
				'type'          => 'true_false',
				'default_value' => 0,
				'ui'            => 1,
				'instructions'  => 'Toggle the alert strip across the top of every page.',
			],
			[
				'key'          => 'field_kcdw_alert_text',
				'label'        => 'Alert Text',
				'name'         => 'alert_bar_text',
				'type'         => 'text',
				'instructions' => 'Keep it short. Court date, vote, deadline.',
				'maxlength'    => 160,
			],
			[
				'key'   => 'field_kcdw_alert_url',
				'label' => 'Alert Link URL',
				'name'  => 'alert_bar_url',
				'type'  => 'url',
			],
			[
				'key'          => 'field_kcdw_petition_count',
				'label'        => 'Petition Signature Count',
				'name'         => 'petition_count',
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'instructions' => 'Update manually after exporting from the petition platform.',
			],

		],
	] );

	// -------------------------------------------------------------------------
	// 2. Press Coverage — structured fields on the press_coverage CPT
	// -------------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_press_coverage',
		'title'    => 'Press Coverage',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'press_coverage' ] ] ],
		'fields'   => [

			[
				'key'          => 'field_kcdw_press_publication',
				'label'        => 'Publication',
				'name'         => 'press_publication',
				'type'         => 'text',
				'instructions' => 'e.g. "Salt Lake Tribune", "High Country News"',
			],
			[
				'key'   => 'field_kcdw_press_author',
				'label' => 'Author',
				'name'  => 'press_author',
				'type'  => 'text',
			],
			[
				'key'            => 'field_kcdw_press_date',
				'label'          => 'Published Date',
				'name'           => 'press_date',
				'type'           => 'date_picker',
				'display_format' => 'F j, Y',
				'return_format'  => 'F j, Y',
				'first_day'      => 1,
			],
			[
				'key'          => 'field_kcdw_press_url',
				'label'        => 'Article URL',
				'name'         => 'press_url',
				'type'         => 'url',
				'instructions' => 'Link to the original article.',
			],
			[
				'key'          => 'field_kcdw_press_excerpt',
				'label'        => 'Excerpt',
				'name'         => 'press_excerpt',
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => 'Pull quote or key sentence from the piece.',
			],

		],
	] );

}, 20 );
