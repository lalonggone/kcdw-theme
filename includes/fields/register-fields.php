<?php
/**
 * SCF (Secure Custom Fields) — Local Field Group Registration
 *
 * All field groups are registered in PHP so they live in version control,
 * deploy with the theme, and are never lost from a DB wipe.
 *
 * Assign templates to pages in the block editor (Page → Template dropdown)
 * to activate the corresponding field group:
 *
 *   template-issue    → The Fight sub-pages
 *   template-lawsuit  → Our Lawsuits sub-pages
 *   template-about    → About page
 *   template-action   → Take Action sub-pages
 *   template-donate   → Donate page
 *
 * Global options (alert bar, next hearing, petition count, social URLs)
 * are under Appearance → Site Options in wp-admin.
 *
 * @package CassidyDC\BlockTheme\Fields
 */

declare( strict_types = 1 );
namespace CassidyDC\BlockTheme;

// =========================================================================
// Register options page + all field groups
// Hooked to 'init' priority 20 — after SCF's own init at priority 5.
// function_exists check is inside the callback, not at file-load time,
// so it runs after SCF has fully initialised its API.
// All SCF calls use the \acf_ global-namespace prefix to avoid any
// namespace resolution ambiguity from the CassidyDC\BlockTheme namespace.
// =========================================================================

add_action( 'init', function () {

	if ( ! \function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Options page (SCF free supports this; no Pro required).
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

	// -----------------------------------------------------------------------
	// 1. Global Site Options
	// -----------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_site_options',
		'title'    => 'KCDW Site Options',
		'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'kcdw-site-options' ] ] ],
		'fields'   => [

			// --- Alert bar ---
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
				'instructions' => 'Keep it short. Start with the key fact — court date, vote, deadline.',
				'maxlength'    => 160,
			],
			[
				'key'  => 'field_kcdw_alert_url',
				'label' => 'Alert Link URL',
				'name' => 'alert_bar_url',
				'type' => 'url',
			],

			// --- Next hearing ---
			[
				'key'            => 'field_kcdw_next_hearing_date',
				'label'          => 'Next Hearing Date',
				'name'           => 'next_hearing_date',
				'type'           => 'date_picker',
				'display_format' => 'F j, Y',
				'return_format'  => 'F j, Y',
				'first_day'      => 1,
				'instructions'   => 'Surfaced in the header and on the lawsuits pages. Clear when the hearing has passed.',
			],
			[
				'key'          => 'field_kcdw_next_hearing_location',
				'label'        => 'Next Hearing Location',
				'name'         => 'next_hearing_location',
				'type'         => 'text',
				'instructions' => 'e.g. "Grand County Courthouse, Courtroom 2, Moab UT"',
			],

			// --- Petition ---
			[
				'key'          => 'field_kcdw_petition_count',
				'label'        => 'Petition Signature Count',
				'name'         => 'petition_count',
				'type'         => 'number',
				'min'          => 0,
				'step'         => 1,
				'instructions' => 'Update manually after exporting from the petition platform.',
			],

			// --- Social ---
			[
				'key'   => 'field_kcdw_social_facebook',
				'label' => 'Facebook URL',
				'name'  => 'social_facebook_url',
				'type'  => 'url',
			],
			[
				'key'   => 'field_kcdw_social_instagram',
				'label' => 'Instagram URL',
				'name'  => 'social_instagram_url',
				'type'  => 'url',
			],
		],
	] );

	// -----------------------------------------------------------------------
	// 2. Front Page Hero
	// -----------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_front_page_hero',
		'title'    => 'Front Page Hero',
		'location' => [ [ [ 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ] ] ],
		'fields'   => [
			[
				'key'          => 'field_kcdw_hero_image',
				'label'        => 'Background Image',
				'name'         => 'hero_background_image',
				'type'         => 'image',
				'return_format' => 'array',
				'preview_size' => 'medium',
				'instructions' => 'Canyon photo. Minimum 2000px wide. Will be dimmed 75%.',
			],
			[
				'key'          => 'field_kcdw_hero_eyebrow',
				'label'        => 'Eyebrow Label',
				'name'         => 'hero_eyebrow',
				'type'         => 'text',
				'default_value' => 'Moab, Utah — Colorado River Corridor',
				'maxlength'    => 80,
			],
			[
				'key'          => 'field_kcdw_hero_headline',
				'label'        => 'Headline',
				'name'         => 'hero_headline',
				'type'         => 'text',
				'default_value' => 'Stop Echo Canyon.',
				'instructions' => 'Short. Declarative. All-caps in the template.',
				'maxlength'    => 60,
			],
			[
				'key'          => 'field_kcdw_hero_body',
				'label'        => 'Body',
				'name'         => 'hero_body',
				'type'         => 'textarea',
				'rows'         => 3,
				'maxlength'    => 300,
				'instructions' => '1–2 sentences. What is being fought and why.',
			],
			[
				'key'   => 'field_kcdw_hero_cta_primary_label',
				'label' => 'Primary CTA Label',
				'name'  => 'hero_cta_primary_label',
				'type'  => 'text',
				'default_value' => 'Sign the Petition',
			],
			[
				'key'   => 'field_kcdw_hero_cta_primary_url',
				'label' => 'Primary CTA URL',
				'name'  => 'hero_cta_primary_url',
				'type'  => 'url',
			],
			[
				'key'   => 'field_kcdw_hero_cta_secondary_label',
				'label' => 'Secondary CTA Label',
				'name'  => 'hero_cta_secondary_label',
				'type'  => 'text',
				'default_value' => 'What We\'re Fighting',
			],
			[
				'key'   => 'field_kcdw_hero_cta_secondary_url',
				'label' => 'Secondary CTA URL',
				'name'  => 'hero_cta_secondary_url',
				'type'  => 'url',
			],
		],
	] );

	// -----------------------------------------------------------------------
	// 3. Issue Page  (template: template-issue)
	// -----------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_issue_page',
		'title'    => 'Issue Page',
		'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'template-issue' ] ] ],
		'fields'   => [
			[
				'key'          => 'field_kcdw_issue_eyebrow',
				'label'        => 'Eyebrow Label',
				'name'         => 'issue_eyebrow',
				'type'         => 'text',
				'instructions' => 'Short category label — e.g. "Water Rights". Appears above the headline.',
				'maxlength'    => 40,
			],
			[
				'key'   => 'field_kcdw_issue_intro_headline',
				'label' => 'Intro Headline',
				'name'  => 'issue_intro_headline',
				'type'  => 'text',
			],
			[
				'key'   => 'field_kcdw_issue_intro_body',
				'label' => 'Intro Body',
				'name'  => 'issue_intro_body',
				'type'  => 'wysiwyg',
				'tabs'  => 'all',
				'toolbar' => 'basic',
				'media_upload' => 0,
				'instructions' => 'Appears above the block editor content area.',
			],
			[
				'key'          => 'field_kcdw_issue_stat_value',
				'label'        => 'Key Stat Value',
				'name'         => 'issue_stat_value',
				'type'         => 'text',
				'instructions' => 'e.g. "47%" or "4,000 acres". Leave blank to hide the stat block.',
				'maxlength'    => 20,
			],
			[
				'key'          => 'field_kcdw_issue_stat_label',
				'label'        => 'Key Stat Label',
				'name'         => 'issue_stat_label',
				'type'         => 'text',
				'instructions' => 'e.g. "of the Colorado River\'s annual flow at risk"',
				'maxlength'    => 80,
			],
			[
				'key'           => 'field_kcdw_issue_featured_image',
				'label'         => 'Featured Image',
				'name'          => 'issue_featured_image',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
			],
			[
				'key'   => 'field_kcdw_issue_cta_label',
				'label' => 'Action CTA Label',
				'name'  => 'issue_cta_label',
				'type'  => 'text',
				'default_value' => 'Sign the Petition',
			],
			[
				'key'   => 'field_kcdw_issue_cta_url',
				'label' => 'Action CTA URL',
				'name'  => 'issue_cta_url',
				'type'  => 'url',
			],
		],
	] );

	// -----------------------------------------------------------------------
	// 4. Lawsuit  (template: template-lawsuit)
	// -----------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_lawsuit',
		'title'    => 'Lawsuit',
		'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'template-lawsuit' ] ] ],
		'fields'   => [
			[
				'key'     => 'field_kcdw_lawsuit_status',
				'label'   => 'Status',
				'name'    => 'lawsuit_status',
				'type'    => 'select',
				'choices' => [
					'active'    => 'Active',
					'pending'   => 'Pending',
					'settled'   => 'Settled',
					'dismissed' => 'Dismissed',
				],
				'default_value' => 'active',
				'return_format' => 'value',
			],
			[
				'key'          => 'field_kcdw_lawsuit_plaintiffs',
				'label'        => 'Plaintiffs',
				'name'         => 'lawsuit_plaintiffs',
				'type'         => 'text',
				'instructions' => 'Comma-separated. e.g. "Living Rivers, Kane Creek Development Watch"',
				'default_value' => 'Living Rivers, Kane Creek Development Watch',
			],
			[
				'key'            => 'field_kcdw_lawsuit_filed_date',
				'label'          => 'Filed Date',
				'name'           => 'lawsuit_filed_date',
				'type'           => 'date_picker',
				'display_format' => 'F j, Y',
				'return_format'  => 'F j, Y',
				'first_day'      => 1,
			],
			[
				'key'   => 'field_kcdw_lawsuit_court',
				'label' => 'Court',
				'name'  => 'lawsuit_court',
				'type'  => 'text',
				'instructions' => 'e.g. "Utah District Court, Seventh Judicial District"',
			],
			[
				'key'   => 'field_kcdw_lawsuit_case_number',
				'label' => 'Case Number',
				'name'  => 'lawsuit_case_number',
				'type'  => 'text',
			],
			[
				'key'          => 'field_kcdw_lawsuit_summary',
				'label'        => 'Summary',
				'name'         => 'lawsuit_summary',
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => '2–3 sentences. What are we challenging and on what grounds.',
			],
			[
				'key'          => 'field_kcdw_lawsuit_latest_update',
				'label'        => 'Latest Update',
				'name'         => 'lawsuit_latest_update',
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => 'What happened most recently. Keep current — this is public.',
			],
			[
				'key'            => 'field_kcdw_lawsuit_update_date',
				'label'          => 'Update Date',
				'name'           => 'lawsuit_latest_update_date',
				'type'           => 'date_picker',
				'display_format' => 'F j, Y',
				'return_format'  => 'F j, Y',
				'first_day'      => 1,
			],
			[
				'key'          => 'field_kcdw_lawsuit_doc_url',
				'label'        => 'Court Document URL',
				'name'         => 'lawsuit_document_url',
				'type'         => 'url',
				'instructions' => 'Optional link to a public filing or court record.',
			],
		],
	] );

	// -----------------------------------------------------------------------
	// 5. About Page  (template: template-about)
	// -----------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_about',
		'title'    => 'About',
		'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'template-about' ] ] ],
		'fields'   => [
			[
				'key'   => 'field_kcdw_about_headline',
				'label' => 'Headline',
				'name'  => 'about_headline',
				'type'  => 'text',
			],
			[
				'key'   => 'field_kcdw_about_intro',
				'label' => 'Intro',
				'name'  => 'about_intro',
				'type'  => 'textarea',
				'rows'  => 4,
			],
			[
				'key'          => 'field_kcdw_mission_statement',
				'label'        => 'Mission Statement',
				'name'         => 'mission_statement',
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => 'Displayed as a pull-quote. One or two punchy sentences.',
			],
			[
				'key'          => 'field_kcdw_coalition_members',
				'label'        => 'Coalition Members',
				'name'         => 'coalition_members',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add Member',
				'sub_fields'   => [
					[
						'key'   => 'field_kcdw_member_name',
						'label' => 'Name',
						'name'  => 'member_name',
						'type'  => 'text',
					],
					[
						'key'          => 'field_kcdw_member_organization',
						'label'        => 'Organization',
						'name'         => 'member_organization',
						'type'         => 'text',
						'instructions' => 'e.g. "Living Rivers", "Grand Canyon Trust", or leave blank for individual.',
					],
					[
						'key'   => 'field_kcdw_member_title',
						'label' => 'Title / Role',
						'name'  => 'member_title',
						'type'  => 'text',
					],
					[
						'key'  => 'field_kcdw_member_bio',
						'label' => 'Bio',
						'name' => 'member_bio',
						'type' => 'textarea',
						'rows' => 3,
					],
					[
						'key'           => 'field_kcdw_member_photo',
						'label'         => 'Photo',
						'name'          => 'member_photo',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
					],
				],
			],
		],
	] );

	// -----------------------------------------------------------------------
	// 6. Take Action Sub-Pages  (template: template-action)
	//    Covers: Sign the Petition, Show Up, Contact Officials, Spread the Word
	// -----------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_take_action',
		'title'    => 'Take Action',
		'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'template-action' ] ] ],
		'fields'   => [

			// --- Shared intro ---
			[
				'key'   => 'field_kcdw_action_headline',
				'label' => 'Headline',
				'name'  => 'action_headline',
				'type'  => 'text',
			],
			[
				'key'  => 'field_kcdw_action_intro',
				'label' => 'Intro',
				'name' => 'action_intro',
				'type' => 'textarea',
				'rows' => 3,
			],

			// --- Sign the Petition ---
			[
				'key'   => 'field_kcdw_petition_tab',
				'label' => 'Sign the Petition',
				'name'  => 'petition_tab',
				'type'  => 'tab',
				'instructions' => 'Only fill this in on the Sign the Petition page.',
			],
			// Petition embed is hardcoded in the template — only headline/body are editable.
			// (Embed code changes break pages; update the template file if the form changes.)

			// --- Show Up ---
			[
				'key'   => 'field_kcdw_events_tab',
				'label' => 'Show Up — Events',
				'name'  => 'events_tab',
				'type'  => 'tab',
				'instructions' => 'Only fill this in on the Show Up page.',
			],
			[
				'key'          => 'field_kcdw_events',
				'label'        => 'Events & Hearings',
				'name'         => 'events',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => 'Add Event',
				'sub_fields'   => [
					[
						'key'   => 'field_kcdw_event_title',
						'label' => 'Title',
						'name'  => 'event_title',
						'type'  => 'text',
					],
					[
						'key'            => 'field_kcdw_event_date',
						'label'          => 'Date',
						'name'           => 'event_date',
						'type'          => 'date_picker',
						'display_format' => 'F j, Y',
						'return_format'  => 'F j, Y',
						'first_day'      => 1,
					],
					[
						'key'          => 'field_kcdw_event_time',
						'label'        => 'Time',
						'name'         => 'event_time',
						'type'         => 'text',
						'instructions' => 'e.g. "6:00 PM MT"',
					],
					[
						'key'   => 'field_kcdw_event_location',
						'label' => 'Location',
						'name'  => 'event_location',
						'type'  => 'text',
					],
					[
						'key'   => 'field_kcdw_event_description',
						'label' => 'Description',
						'name'  => 'event_description',
						'type'  => 'textarea',
						'rows'  => 2,
					],
					[
						'key'   => 'field_kcdw_event_rsvp_url',
						'label' => 'RSVP / Info URL',
						'name'  => 'event_rsvp_url',
						'type'  => 'url',
					],
				],
			],

			// --- Contact Officials ---
			[
				'key'   => 'field_kcdw_officials_tab',
				'label' => 'Contact Officials',
				'name'  => 'officials_tab',
				'type'  => 'tab',
				'instructions' => 'Only fill this in on the Contact Officials page.',
			],
			[
				'key'          => 'field_kcdw_officials',
				'label'        => 'Officials',
				'name'         => 'officials',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => 'Add Official',
				'sub_fields'   => [
					[
						'key'   => 'field_kcdw_official_name',
						'label' => 'Name',
						'name'  => 'official_name',
						'type'  => 'text',
					],
					[
						'key'   => 'field_kcdw_official_title',
						'label' => 'Title',
						'name'  => 'official_title',
						'type'  => 'text',
					],
					[
						'key'   => 'field_kcdw_official_email',
						'label' => 'Email',
						'name'  => 'official_email',
						'type'  => 'email',
					],
					[
						'key'   => 'field_kcdw_official_phone',
						'label' => 'Phone',
						'name'  => 'official_phone',
						'type'  => 'text',
					],
					[
						'key'   => 'field_kcdw_official_contact_url',
						'label' => 'Contact Form URL',
						'name'  => 'official_contact_url',
						'type'  => 'url',
					],
				],
			],
			[
				'key'          => 'field_kcdw_talking_points',
				'label'        => 'Talking Points',
				'name'         => 'talking_points',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add Talking Point',
				'instructions' => 'Pre-written copy for callers. Keep each point to 1–2 sentences.',
				'sub_fields'   => [
					[
						'key'   => 'field_kcdw_talking_point',
						'label' => 'Point',
						'name'  => 'talking_point',
						'type'  => 'textarea',
						'rows'  => 2,
					],
				],
			],

			// --- Spread the Word ---
			[
				'key'   => 'field_kcdw_spread_tab',
				'label' => 'Spread the Word',
				'name'  => 'spread_tab',
				'type'  => 'tab',
				'instructions' => 'Only fill this in on the Spread the Word page.',
			],
			[
				'key'          => 'field_kcdw_social_share_text',
				'label'        => 'Pre-written Social Copy',
				'name'         => 'social_share_text',
				'type'         => 'textarea',
				'rows'         => 4,
				'instructions' => 'Copy-paste ready text for Facebook, Instagram, etc.',
			],
			[
				'key'          => 'field_kcdw_email_template',
				'label'        => 'Email Template',
				'name'         => 'email_template',
				'type'         => 'textarea',
				'rows'         => 8,
				'instructions' => 'Forward-to-a-friend copy. Plain text — no formatting needed.',
			],
			[
				'key'          => 'field_kcdw_downloadable_assets',
				'label'        => 'Downloads',
				'name'         => 'downloadable_assets',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add Download',
				'instructions' => 'Flyers, graphics, fact sheets.',
				'sub_fields'   => [
					[
						'key'   => 'field_kcdw_asset_name',
						'label' => 'Label',
						'name'  => 'asset_name',
						'type'  => 'text',
					],
					[
						'key'           => 'field_kcdw_asset_file',
						'label'         => 'File',
						'name'          => 'asset_file',
						'type'          => 'file',
						'return_format' => 'array',
						'library'       => 'all',
					],
				],
			],
		],
	] );

	// -----------------------------------------------------------------------
	// 7. Donate Page  (template: template-donate)
	// -----------------------------------------------------------------------
	\acf_add_local_field_group( [
		'key'      => 'group_kcdw_donate',
		'title'    => 'Donate',
		'location' => [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'template-donate' ] ] ],
		'fields'   => [
			[
				'key'   => 'field_kcdw_donate_headline',
				'label' => 'Headline',
				'name'  => 'donate_headline',
				'type'  => 'text',
			],
			[
				'key'  => 'field_kcdw_donate_body',
				'label' => 'Body',
				'name' => 'donate_body',
				'type' => 'textarea',
				'rows' => 4,
			],
			[
				'key'          => 'field_kcdw_donate_platform_url',
				'label'        => 'Donation Platform URL',
				'name'         => 'donate_platform_url',
				'type'         => 'url',
				'instructions' => 'PayPal, Stripe, etc. The button in the template links here.',
			],
			[
				'key'          => 'field_kcdw_donate_fiscal_note',
				'label'        => 'Fiscal Sponsor Note',
				'name'         => 'donate_fiscal_sponsor_note',
				'type'         => 'textarea',
				'rows'         => 3,
				'default_value' => 'The fiscal sponsor for Kane Creek Development Watch (KCDW) is Canyonlands Watershed Council (CWC). CWC EIN: 87-0637713.',
			],
		],
	] );

}, 20 );
