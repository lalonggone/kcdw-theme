<?php
/**
 * WP-CLI Seed Script: KCDW Local Database Content
 *
 * Populates SCF option fields and page fields with real KCDW content.
 * Also creates any pages in the nav structure that do not yet exist.
 *
 * Usage:
 *   wp eval-file wp-content/themes/kcdw/_dev-docs/seed-content.php        # dry run
 *   wp eval-file wp-content/themes/kcdw/_dev-docs/seed-content.php write  # write to DB
 *
 * Always backup first:
 *   wp db export backup-pre-seed-$(date +%Y%m%d).sql
 */

if ( ! class_exists( 'WP_CLI' ) ) {
	die( 'Run via WP-CLI.' );
}

if ( ! function_exists( 'update_field' ) ) {
	WP_CLI::error( 'SCF/ACF is not active. Activate the Secure Custom Fields plugin first.' );
}

$write_mode = in_array( 'write', $args );

$stats = [
	'options'       => 0,
	'fields'        => 0,
	'pages_created' => 0,
	'skipped'       => 0,
];

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Set a single ACF option field. Logs the value; writes if in write mode.
 */
function kcdw_opt( string $name, $value, bool $write, array &$stats ): void {
	$display = is_array( $value ) ? '(array, ' . count( $value ) . ' rows)' : ( is_bool( $value ) ? ( $value ? 'true' : 'false' ) : $value );
	WP_CLI::log( "  " . WP_CLI::colorize( "%9{$name}%n" ) . ": {$display}" );
	if ( $write ) {
		update_field( $name, $value, 'option' );
		$stats['options']++;
	}
}

/**
 * Set a single ACF field on a post. Logs the value; writes if in write mode.
 */
function kcdw_field( string $name, $value, int $post_id, bool $write, array &$stats ): void {
	$display = is_array( $value ) ? '(array, ' . count( $value ) . ' rows)' : $value;
	WP_CLI::log( "  " . WP_CLI::colorize( "%9{$name}%n" ) . ": {$display}" );
	if ( $write ) {
		update_field( $name, $value, $post_id );
		$stats['fields']++;
	}
}

/**
 * Ensure a page exists with the given slug. Creates it if missing.
 * Returns the page ID (real or 0 in dry-run when it doesn't exist yet).
 */
function kcdw_ensure_page( string $title, string $slug, int $parent_id, string $template, bool $write, array &$stats ): int {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $existing ) {
		WP_CLI::log( WP_CLI::colorize( "  %8EXISTS%n  [{$existing->ID}] {$title}" ) );
		// Ensure template meta is set even on existing pages.
		if ( $write && get_post_meta( $existing->ID, '_wp_page_template', true ) !== $template ) {
			update_post_meta( $existing->ID, '_wp_page_template', $template );
		}
		return (int) $existing->ID;
	}

	WP_CLI::log( WP_CLI::colorize( "  %GCREATE%n  {$title} (/{$slug}/, template: {$template})" ) );

	if ( ! $write ) {
		$stats['pages_created']++;
		return 0; // dry-run: no real ID
	}

	$id = wp_insert_post( [
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_parent'  => $parent_id,
		'meta_input'   => [ '_wp_page_template' => $template ],
	], true );

	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "Failed to create '{$title}': " . $id->get_error_message() );
		$stats['skipped']++;
		return 0;
	}

	$stats['pages_created']++;
	WP_CLI::success( "Created [{$id}] {$title}" );
	return $id;
}

// ---------------------------------------------------------------------------
// Header
// ---------------------------------------------------------------------------

WP_CLI::line( '' );
WP_CLI::log( WP_CLI::colorize( '%BKane Creek Development Watch — Content Seed%n' ) );
WP_CLI::log( WP_CLI::colorize( $write_mode
	? '%YWRITE MODE — changes will be saved to the DB%n'
	: '%GDRY-RUN MODE — no changes will be written%n'
) );
WP_CLI::line( '' );

// ===========================================================================
// 1. GLOBAL SITE OPTIONS
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BSite Options%n' ) );

kcdw_opt( 'alert_bar_enabled', 1,                                                         $write_mode, $stats );
kcdw_opt( 'alert_bar_text',    '⚠ Active lawsuit filed June 28, 2025 — water rights challenge', $write_mode, $stats );
kcdw_opt( 'alert_bar_url',     '/our-lawsuits/',                                          $write_mode, $stats );
kcdw_opt( 'petition_count',    17850,                                                     $write_mode, $stats );
kcdw_opt( 'next_hearing_date', '',                                                        $write_mode, $stats );
kcdw_opt( 'next_hearing_location', '',                                                    $write_mode, $stats );
kcdw_opt( 'social_facebook_url',  'https://facebook.com/kanecreekwatch',                 $write_mode, $stats );
kcdw_opt( 'social_instagram_url', 'https://instagram.com/kanecreekwatch',                $write_mode, $stats );

WP_CLI::line( '' );

// ===========================================================================
// 2. HOME PAGE (ID 6) — Front Page Hero
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BHome Page — Hero [ID 6]%n' ) );

kcdw_field( 'hero_eyebrow',             'Moab, Utah — Colorado River Corridor',           6, $write_mode, $stats );
kcdw_field( 'hero_headline',            'Stop Echo Canyon.',                              6, $write_mode, $stats );
kcdw_field( 'hero_body',
	'A developer is pushing to build a 188-unit luxury resort on the Colorado River corridor outside Moab — in an active floodplain, over ancestral lands, with a water diversion permit that threatens the most over-allocated river in the American West. We are fighting to stop it.',
	6, $write_mode, $stats );
kcdw_field( 'hero_cta_primary_label',   'Sign the Petition',                             6, $write_mode, $stats );
kcdw_field( 'hero_cta_primary_url',     '/take-action/sign-the-petition/',               6, $write_mode, $stats );
kcdw_field( 'hero_cta_secondary_label', 'See the Lawsuits',                              6, $write_mode, $stats );
kcdw_field( 'hero_cta_secondary_url',   '/our-lawsuits/',                                6, $write_mode, $stats );

WP_CLI::line( '' );

// ===========================================================================
// 3. ABOUT PAGE (ID 68)
// Template: template-about
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BAbout Page [ID 68]%n' ) );

if ( $write_mode ) {
	update_post_meta( 68, '_wp_page_template', 'template-about' );
}

kcdw_field( 'about_headline', 'We Are Kane Creek Development Watch',                     68, $write_mode, $stats );
kcdw_field( 'about_intro',
	'KCDW is a coalition of Moab residents, river advocates, Indigenous community members, housing organizers, and outdoor recreationists united around a single goal: stopping the Echo Canyon luxury resort development on the Colorado River corridor. We are not anti-development. We are anti-this-development, in this place, for these reasons.',
	68, $write_mode, $stats );
kcdw_field( 'mission_statement',
	'The canyon is not for sale. The river is not a resource to be auctioned to the highest bidder. We intend to keep it that way.',
	68, $write_mode, $stats );
kcdw_field( 'coalition_members', [
	[
		'member_name'         => 'Living Rivers',
		'member_organization' => 'Living Rivers',
		'member_title'        => 'Co-plaintiff, water rights litigation',
		'member_bio'          => 'Living Rivers has advocated for the Colorado River ecosystem for over 25 years, working to restore and protect river flows throughout the Colorado Basin. They are co-plaintiffs in the water rights lawsuit challenging the Echo Canyon diversion permit.',
		'member_photo'        => null,
	],
	[
		'member_name'         => 'Grand Canyon Trust',
		'member_organization' => 'Grand Canyon Trust',
		'member_title'        => 'Coalition partner',
		'member_bio'          => 'The Grand Canyon Trust works to protect and restore the Colorado Plateau\'s canyon country. They have provided technical support and public advocacy for the KCDW campaign.',
		'member_photo'        => null,
	],
	[
		'member_name'         => 'Canyonlands Watershed Council',
		'member_organization' => 'Canyonlands Watershed Council',
		'member_title'        => 'Fiscal sponsor',
		'member_bio'          => 'CWC is the fiscal sponsor for Kane Creek Development Watch. CWC EIN: 87-0637713. All donations to KCDW are processed through CWC and are tax-deductible.',
		'member_photo'        => null,
	],
], 68, $write_mode, $stats );

WP_CLI::line( '' );

// ===========================================================================
// 4. DONATE PAGE (ID 135)
// Template: template-donate
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BDonate Page [ID 135]%n' ) );

if ( $write_mode ) {
	update_post_meta( 135, '_wp_page_template', 'template-donate' );
}

kcdw_field( 'donate_headline', 'Fund the Fight',                                         135, $write_mode, $stats );
kcdw_field( 'donate_body',
	"KCDW's lawsuits don't pay for themselves. Attorney fees, court costs, expert witnesses, hydrology reports, environmental analysis — this is expensive. Every dollar you give goes directly toward the legal action keeping Echo Canyon in court.\n\nWe are an all-volunteer organization. No salaries. No overhead. When you donate, the money fights.",
	135, $write_mode, $stats );
kcdw_field( 'donate_fiscal_sponsor_note',
	'The fiscal sponsor for Kane Creek Development Watch (KCDW) is Canyonlands Watershed Council (CWC). CWC EIN: 87-0637713. Donations are tax-deductible to the extent allowed by law.',
	135, $write_mode, $stats );

WP_CLI::line( '' );

// ===========================================================================
// 5. OUR LAWSUITS — parent + two lawsuit pages
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BOur Lawsuits%n' ) );

$lawsuits_parent = kcdw_ensure_page( 'Our Lawsuits', 'our-lawsuits', 0, 'page', $write_mode, $stats );

// — Water Rights Lawsuit —
WP_CLI::log( WP_CLI::colorize( '%8  Water Rights Lawsuit%n' ) );
$water_rights_id = kcdw_ensure_page( 'Water Rights Lawsuit', 'water-rights-lawsuit', $lawsuits_parent, 'template-lawsuit', $write_mode, $stats );
if ( $water_rights_id || $write_mode === false ) {
	$id = $water_rights_id ?: 0;
	kcdw_field( 'lawsuit_status',              'active',                                  $id ?: 1, $write_mode, $stats );
	kcdw_field( 'lawsuit_plaintiffs',          'Living Rivers, Kane Creek Development Watch', $id ?: 1, $write_mode, $stats );
	kcdw_field( 'lawsuit_filed_date',          '20250628',                                $id ?: 1, $write_mode, $stats );
	kcdw_field( 'lawsuit_court',               'Utah Seventh District Court, Grand County', $id ?: 1, $write_mode, $stats );
	kcdw_field( 'lawsuit_case_number',         '270700892',                               $id ?: 1, $write_mode, $stats );
	kcdw_field( 'lawsuit_summary',
		'KCDW and Living Rivers filed suit challenging the Utah Division of Water Rights\' approval of a diversion permit for the Echo Canyon resort development. The permit would authorize withdrawal of up to 550 acre-feet per year from the Colorado River — water that does not exist in a system already overallocated by more than 20% of average annual flow.',
		$id ?: 1, $write_mode, $stats );
	kcdw_field( 'lawsuit_latest_update',
		'The developer\'s motion to dismiss was denied in full. Discovery is underway. The court has set a scheduling conference for briefing on the merits.',
		$id ?: 1, $write_mode, $stats );
	kcdw_field( 'lawsuit_latest_update_date',  '20250915',                                $id ?: 1, $write_mode, $stats );
}

// — SB 258 Challenge (existing page ID 839) —
WP_CLI::log( WP_CLI::colorize( '%8  SB 258 Challenge [ID 839]%n' ) );

if ( $write_mode ) {
	// Re-parent to Our Lawsuits and assign template.
	wp_update_post( [ 'ID' => 839, 'post_parent' => $lawsuits_parent ] );
	update_post_meta( 839, '_wp_page_template', 'template-lawsuit' );
}

kcdw_field( 'lawsuit_status',             'active',                                       839, $write_mode, $stats );
kcdw_field( 'lawsuit_plaintiffs',         'Kane Creek Development Watch',                 839, $write_mode, $stats );
kcdw_field( 'lawsuit_filed_date',         '20250401',                                     839, $write_mode, $stats );
kcdw_field( 'lawsuit_court',              'Utah Seventh District Court, Grand County',    839, $write_mode, $stats );
kcdw_field( 'lawsuit_case_number',        '270700771',                                    839, $write_mode, $stats );
kcdw_field( 'lawsuit_summary',
	'SB 258 was signed into Utah law in March 2025, creating a special land use designation for the Echo Canyon parcel that bypassed standard Grand County zoning review and environmental impact assessment requirements. KCDW filed suit immediately, arguing the bill constitutes legislative spot-zoning and violates the Utah Constitution\'s uniform operation of laws clause.',
	839, $write_mode, $stats );
kcdw_field( 'lawsuit_latest_update',
	'Briefing on cross-motions for summary judgment is ongoing. The court denied the developer\'s motion to strike KCDW\'s expert witness testimony.',
	839, $write_mode, $stats );
kcdw_field( 'lawsuit_latest_update_date', '20251101',                                    839, $write_mode, $stats );

WP_CLI::line( '' );

// ===========================================================================
// 6. THE FIGHT — parent + issue sub-pages
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BThe Fight%n' ) );

$fight_parent = kcdw_ensure_page( 'The Fight', 'the-fight', 0, 'page', $write_mode, $stats );

$issues = [
	[
		'title'    => 'Water Rights',
		'slug'     => 'water-rights',
		'eyebrow'  => 'Water Rights',
		'headline' => 'They want to drain a dying river.',
		'body'     => 'The Colorado River no longer reliably reaches the sea. It is overallocated, over-dammed, and over-stressed by a century of water law written before climate change was a factor. The Echo Canyon resort\'s diversion permit would pull up to 550 acre-feet per year from this system — water that does not exist. We are challenging this permit in court.',
		'stat_val' => '550',
		'stat_lbl' => 'acre-feet per year the developer wants to divert from the Colorado River',
		'cta'      => 'Support the lawsuit',
		'cta_url'  => '/our-lawsuits/water-rights-lawsuit/',
	],
	[
		'title'    => 'Floodplain & Flood Risk',
		'slug'     => 'floodplain-flood-risk',
		'eyebrow'  => 'Floodplain & Flood Risk',
		'headline' => 'They want to build in a floodway.',
		'body'     => 'The Echo Canyon site sits within a designated FEMA floodplain on the Colorado River. The river floods. It floods reliably, cyclically, and with increasing intensity as snowpack patterns shift. Building permanent structures — luxury villas, parking, infrastructure — in this zone is not just bad planning. It is a liability that Grand County taxpayers will ultimately bear when the river reclaims the land.',
		'stat_val' => '100-yr',
		'stat_lbl' => 'flood zone classification for the proposed development site',
		'cta'      => 'Read the floodplain analysis',
		'cta_url'  => '/the-fight/floodplain-flood-risk/',
	],
	[
		'title'    => 'The Fake Town',
		'slug'     => 'the-fake-town',
		'eyebrow'  => 'The Fake Town',
		'headline' => 'It\'s not a resort. It\'s a private city.',
		'body'     => 'The Echo Canyon development is not a hotel. The developer\'s plans describe a 188-unit residential resort community with private roads, private amenities, and a governance structure that functions as a private municipality. Grand County has no precedent for this kind of development, no infrastructure to support it, and no obligation to subsidize it. The developer\'s framing as an "eco-resort" does not survive contact with the actual permit applications.',
		'stat_val' => '188',
		'stat_lbl' => 'residential units in what the developer calls an "eco-resort"',
		'cta'      => 'See the permit applications',
		'cta_url'  => '/the-fight/the-fake-town/',
	],
	[
		'title'    => 'Affordable Housing',
		'slug'     => 'affordable-housing',
		'eyebrow'  => 'Affordable Housing',
		'headline' => 'Moab has a housing crisis. This makes it worse.',
		'body'     => 'Moab\'s workforce housing shortage is already severe. Teachers, nurses, guides, and service workers commute 45 minutes or more because they cannot afford to live in the town where they work. The Echo Canyon development adds 188 luxury units that will sit mostly empty while driving up land values and property taxes for existing residents. It contributes nothing to the workforce housing stock and worsens every metric that already makes Moab unaffordable.',
		'stat_val' => '45 min',
		'stat_lbl' => 'average commute for Moab service workers priced out of local housing',
		'cta'      => 'Read about the housing crisis',
		'cta_url'  => '/the-fight/affordable-housing/',
	],
	[
		'title'    => 'Cultural Resources',
		'slug'     => 'cultural-resources',
		'eyebrow'  => 'Cultural Resources',
		'headline' => 'The site contains irreplaceable ancestral history.',
		'body'     => 'The Echo Canyon corridor contains documented archaeological sites including rock art panels, lithic scatters, and structural remains associated with ancestral Puebloan occupation spanning thousands of years. These resources are protected under federal law, but the developer\'s footprint encroaches on areas that have not been fully surveyed. Ground disturbance cannot be undone. Destruction of these sites is permanent.',
		'stat_val' => '5,000+',
		'stat_lbl' => 'years of documented human occupation in the Echo Canyon corridor',
		'cta'      => 'Sign the petition',
		'cta_url'  => '/take-action/sign-the-petition/',
	],
	[
		'title'    => 'Meet the Developer',
		'slug'     => 'meet-the-developer',
		'eyebrow'  => 'Meet the Developer',
		'headline' => 'Who is behind Echo Canyon?',
		'body'     => 'The Echo Canyon development has changed hands and rebranded multiple times since its initial proposal. The current ownership structure involves out-of-state investors with no prior ties to Moab or Grand County. Their public communications emphasize sustainability and community benefit. Their permit applications tell a different story. We are documenting the gap between what they say and what they are asking for.',
		'stat_val' => '0',
		'stat_lbl' => 'affordable units in a development marketed as a "community benefit"',
		'cta'      => 'See the developer\'s permit record',
		'cta_url'  => '/the-fight/meet-the-developer/',
	],
];

foreach ( $issues as $issue ) {
	WP_CLI::log( WP_CLI::colorize( "%8  {$issue['title']}%n" ) );
	$issue_id = kcdw_ensure_page( $issue['title'], $issue['slug'], $fight_parent, 'template-issue', $write_mode, $stats );
	$pid = $issue_id ?: 1; // use 1 as placeholder in dry-run so fields log without writing
	kcdw_field( 'issue_eyebrow',          $issue['eyebrow'],  $pid, $write_mode, $stats );
	kcdw_field( 'issue_intro_headline',   $issue['headline'], $pid, $write_mode, $stats );
	kcdw_field( 'issue_intro_body',       $issue['body'],     $pid, $write_mode, $stats );
	kcdw_field( 'issue_stat_value',       $issue['stat_val'], $pid, $write_mode, $stats );
	kcdw_field( 'issue_stat_label',       $issue['stat_lbl'], $pid, $write_mode, $stats );
	kcdw_field( 'issue_cta_label',        $issue['cta'],      $pid, $write_mode, $stats );
	kcdw_field( 'issue_cta_url',          $issue['cta_url'],  $pid, $write_mode, $stats );
}

WP_CLI::line( '' );

// ===========================================================================
// 7. TAKE ACTION — parent + sub-pages
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BTake Action%n' ) );

$action_parent = kcdw_ensure_page( 'Take Action', 'take-action', 0, 'page', $write_mode, $stats );

// — Sign the Petition —
WP_CLI::log( WP_CLI::colorize( '%8  Sign the Petition%n' ) );
$petition_id = kcdw_ensure_page( 'Sign the Petition', 'sign-the-petition', $action_parent, 'template-action', $write_mode, $stats );
$pid = $petition_id ?: 1;
kcdw_field( 'action_headline', 'Add Your Name',                                           $pid, $write_mode, $stats );
kcdw_field( 'action_intro',
	'Over 17,000 people have already signed. Grand County commissioners are watching this number. Every signature is a constituent on record. Add yours.',
	$pid, $write_mode, $stats );

// — Show Up —
WP_CLI::log( WP_CLI::colorize( '%8  Show Up%n' ) );
$show_up_id = kcdw_ensure_page( 'Show Up', 'show-up', $action_parent, 'template-action', $write_mode, $stats );
$pid = $show_up_id ?: 1;
kcdw_field( 'action_headline', 'Be in the Room',                                          $pid, $write_mode, $stats );
kcdw_field( 'action_intro',
	'Public hearings are where this fight is won or lost. Commissioners notice when the room is full. They notice even more when it\'s full of the same faces, meeting after meeting. Show up.',
	$pid, $write_mode, $stats );
kcdw_field( 'events', [
	[
		'event_title'       => 'Grand County Commission — Regular Meeting',
		'event_date'        => '20261015',
		'event_time'        => '4:00 PM MT',
		'event_location'    => 'Grand County Courthouse, 125 E. Center St, Moab UT 84532',
		'event_description' => 'Echo Canyon is on the agenda. Public comment period opens at 5:00 PM. Come early.',
		'event_rsvp_url'    => '',
	],
], $pid, $write_mode, $stats );

// — Contact Officials —
WP_CLI::log( WP_CLI::colorize( '%8  Contact Officials%n' ) );
$contact_id = kcdw_ensure_page( 'Contact Officials', 'contact-officials', $action_parent, 'template-action', $write_mode, $stats );
$pid = $contact_id ?: 1;
kcdw_field( 'action_headline', 'Make the Call',                                           $pid, $write_mode, $stats );
kcdw_field( 'action_intro',
	'Grand County commissioners make the final land use decision. They need to hear from constituents — not just at hearings, but by phone and email, consistently, over time. We make it easy.',
	$pid, $write_mode, $stats );
kcdw_field( 'officials', [
	[
		'official_name'        => 'Grand County Commission',
		'official_title'       => 'Grand County Commission',
		'official_email'       => 'commission@grandcountyutah.net',
		'official_phone'       => '(435) 259-1321',
		'official_contact_url' => 'https://www.grandcountyutah.net/215/Commission',
	],
], $pid, $write_mode, $stats );
kcdw_field( 'talking_points', [
	[ 'talking_point' => 'I am a Grand County resident and I am asking you to deny the Echo Canyon resort development application. The site is in an active FEMA floodplain and any future flood damage becomes a public liability.' ],
	[ 'talking_point' => 'The water diversion permit being challenged in court would pull 550 acre-feet per year from the Colorado River — a system already overallocated. Approving this development before the legal challenge is resolved is premature.' ],
	[ 'talking_point' => 'Moab\'s workforce housing shortage is severe. This development adds 188 luxury units and zero affordable units. It worsens every housing metric that already makes Grand County unlivable for working families.' ],
	[ 'talking_point' => 'The ancestral sites in the Echo Canyon corridor have not been fully surveyed. Ground disturbance before a complete cultural resource survey is complete would be irreversible.' ],
], $pid, $write_mode, $stats );

// — Spread the Word —
WP_CLI::log( WP_CLI::colorize( '%8  Spread the Word%n' ) );
$spread_id = kcdw_ensure_page( 'Spread the Word', 'spread-the-word', $action_parent, 'template-action', $write_mode, $stats );
$pid = $spread_id ?: 1;
kcdw_field( 'action_headline', 'Tell Everyone',                                           $pid, $write_mode, $stats );
kcdw_field( 'action_intro',
	'Most people who would care about this don\'t know it\'s happening yet. Change that.',
	$pid, $write_mode, $stats );
kcdw_field( 'social_share_text',
	"A developer is trying to build a 188-unit luxury resort on the Colorado River outside Moab — in a floodplain, over ancestral lands, with a water diversion permit for water that doesn't exist. KCDW is fighting it in court. Sign the petition: kanecreekwatch.org",
	$pid, $write_mode, $stats );
kcdw_field( 'email_template',
	"Subject: Please sign — stopping a resort development on the Colorado River\n\nHi [Name],\n\nI wanted to let you know about something happening near Moab, Utah that I think you should know about.\n\nA developer is pushing to build a 188-unit luxury resort on the Colorado River corridor — in an active floodplain, over documented ancestral sites, with a water permit that environmental groups are challenging in court. Kane Creek Development Watch (KCDW) is leading that fight.\n\nThey have a petition and the signature count is being watched by Grand County commissioners. If you're willing, adding your name takes 30 seconds:\n\nkanecreekwatch.org/take-action/sign-the-petition\n\nThere are also upcoming public hearings where showing up matters. The site has dates and talking points.\n\nThanks for reading.\n[Your name]",
	$pid, $write_mode, $stats );

WP_CLI::line( '' );

// ===========================================================================
// 8. IN THE NEWS — parent + sub-pages
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%BIn the News%n' ) );

$news_parent    = kcdw_ensure_page( 'In the News',       'in-the-news',       0,           'page', $write_mode, $stats );
$press_id       = kcdw_ensure_page( 'Press Coverage',    'press-coverage',    $news_parent, 'page', $write_mode, $stats );
$newsletter_id  = kcdw_ensure_page( 'Newsletter Archive','newsletter-archive', $news_parent, 'page', $write_mode, $stats );

WP_CLI::line( '' );

// ===========================================================================
// Summary
// ===========================================================================

WP_CLI::log( WP_CLI::colorize( '%B' . str_repeat( '═', 60 ) . '%n' ) );
WP_CLI::log( WP_CLI::colorize( '%WSUMMARY%n' ) );
WP_CLI::log( "  Option fields set : {$stats['options']}" );
WP_CLI::log( "  Post fields set   : {$stats['fields']}" );
WP_CLI::log( "  Pages created     : {$stats['pages_created']}" );
WP_CLI::log( "  Errors / skipped  : {$stats['skipped']}" );

if ( ! $write_mode ) {
	WP_CLI::line( '' );
	WP_CLI::log( WP_CLI::colorize( '%YThis was a dry run. Nothing was written.%n' ) );
	WP_CLI::log( 'To write, run:' );
	WP_CLI::log( '  wp eval-file wp-content/themes/kcdw/_dev-docs/seed-content.php write' );
}

WP_CLI::line( '' );
