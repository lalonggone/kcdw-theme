<?php
/**
 * Template Name: Issue Page
 * Template Post Type: page
 *
 * One template serves all six "The Fight" sub-pages. Content is keyed by
 * post slug — edit the $issues array below to update copy.
 * Block editor content (the_content) renders below for long-form supporting
 * text, sources, or embedded documents.
 */

declare( strict_types = 1 );

if ( have_posts() ) {
	the_post();
}

$issues = [

	'water-rights' => [
		'eyebrow'   => 'Water Rights',
		'headline'  => 'They want to drain a dying river.',
		'body'      => 'The Colorado River no longer reliably reaches the sea. It is overallocated, over-dammed, and over-stressed by a century of water law written before climate change was a factor. The Echo Canyon resort\'s diversion permit would pull up to 550 acre-feet per year from this system — water that does not exist. We are challenging this permit in court.',
		'stat_val'  => '550',
		'stat_lbl'  => 'acre-feet per year the developer wants to divert from the Colorado River',
		'cta_label' => 'Support the lawsuit',
		'cta_url'   => '/our-lawsuits/water-rights-lawsuit/',
	],

	'floodplain-flood-risk' => [
		'eyebrow'   => 'Floodplain & Flood Risk',
		'headline'  => 'They want to build in a floodway.',
		'body'      => 'The Echo Canyon site sits within a designated FEMA floodplain on the Colorado River. The river floods. It floods reliably, cyclically, and with increasing intensity as snowpack patterns shift. Building permanent structures — luxury villas, parking, infrastructure — in this zone is not just bad planning. It is a liability that Grand County taxpayers will ultimately bear when the river reclaims the land.',
		'stat_val'  => '100-yr',
		'stat_lbl'  => 'flood zone classification for the proposed development site',
		'cta_label' => 'Read the floodplain analysis',
		'cta_url'   => '/the-fight/floodplain-flood-risk/',
	],

	'the-fake-town' => [
		'eyebrow'   => 'The Fake Town',
		'headline'  => 'It\'s not a resort. It\'s a private city.',
		'body'      => 'The Echo Canyon development is not a hotel. The developer\'s plans describe a 188-unit residential resort community with private roads, private amenities, and a governance structure that functions as a private municipality. Grand County has no precedent for this kind of development, no infrastructure to support it, and no obligation to subsidize it. The developer\'s framing as an "eco-resort" does not survive contact with the actual permit applications.',
		'stat_val'  => '188',
		'stat_lbl'  => 'residential units in what the developer calls an "eco-resort"',
		'cta_label' => 'See the permit applications',
		'cta_url'   => '/the-fight/the-fake-town/',
	],

	'affordable-housing' => [
		'eyebrow'   => 'Affordable Housing',
		'headline'  => 'Moab has a housing crisis. This makes it worse.',
		'body'      => 'Moab\'s workforce housing shortage is already severe. Teachers, nurses, guides, and service workers commute 45 minutes or more because they cannot afford to live in the town where they work. The Echo Canyon development adds 188 luxury units that will sit mostly empty while driving up land values and property taxes for existing residents. It contributes nothing to the workforce housing stock and worsens every metric that already makes Moab unaffordable.',
		'stat_val'  => '45 min',
		'stat_lbl'  => 'average commute for Moab service workers priced out of local housing',
		'cta_label' => 'Read about the housing crisis',
		'cta_url'   => '/the-fight/affordable-housing/',
	],

	'cultural-resources' => [
		'eyebrow'   => 'Cultural Resources',
		'headline'  => 'The site contains irreplaceable ancestral history.',
		'body'      => 'The Echo Canyon corridor contains documented archaeological sites including rock art panels, lithic scatters, and structural remains associated with ancestral Puebloan occupation spanning thousands of years. These resources are protected under federal law, but the developer\'s footprint encroaches on areas that have not been fully surveyed. Ground disturbance cannot be undone. Destruction of these sites is permanent.',
		'stat_val'  => '5,000+',
		'stat_lbl'  => 'years of documented human occupation in the Echo Canyon corridor',
		'cta_label' => 'Sign the petition',
		'cta_url'   => '/take-action/sign-the-petition/',
	],

	'meet-the-developer' => [
		'eyebrow'   => 'Meet the Developer',
		'headline'  => 'Who is behind Echo Canyon?',
		'body'      => 'The Echo Canyon development has changed hands and rebranded multiple times since its initial proposal. The current ownership structure involves out-of-state investors with no prior ties to Moab or Grand County. Their public communications emphasize sustainability and community benefit. Their permit applications tell a different story. We are documenting the gap between what they say and what they are asking for.',
		'stat_val'  => '0',
		'stat_lbl'  => 'affordable units in a development marketed as a "community benefit"',
		'cta_label' => 'See the developer\'s permit record',
		'cta_url'   => '/the-fight/meet-the-developer/',
	],

];

$slug        = get_post_field( 'post_name' );
$issue       = $issues[ $slug ] ?? [];
$eyebrow     = $issue['eyebrow']   ?? '';
$headline    = $issue['headline']  ?? get_the_title();
$body        = $issue['body']      ?? '';
$stat_val    = $issue['stat_val']  ?? '';
$stat_lbl    = $issue['stat_lbl']  ?? '';
$cta_label   = $issue['cta_label'] ?? 'Sign the Petition';
$cta_url     = $issue['cta_url']   ?? '/take-action/sign-the-petition/';
$has_content = trim( strip_tags( get_the_content() ) ) !== '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'template-issue' ); ?>>
<?php wp_body_open(); ?>

<?php block_template_part( 'header' ); ?>

<main id="main-content">

	<!-- ISSUE HERO -->
	<section class="issue-hero">
		<div class="issue-hero__inner">

			<?php if ( $eyebrow ) : ?>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h1><?php echo esc_html( $headline ); ?></h1>

			<?php if ( $body ) : ?>
				<p class="issue-hero__body"><?php echo esc_html( $body ); ?></p>
			<?php endif; ?>

			<?php if ( $stat_val && $stat_lbl ) : ?>
				<div class="issue-stat">
					<span class="issue-stat__value"><?php echo esc_html( $stat_val ); ?></span>
					<span class="issue-stat__label"><?php echo esc_html( $stat_lbl ); ?></span>
				</div>
			<?php endif; ?>

			<p class="issue-hero__cta">
				<a class="wp-element-button" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_label ); ?>
				</a>
			</p>

		</div>
	</section>

	<!-- BLOCK EDITOR CONTENT (long-form supporting text, sources, docs) -->
	<?php if ( $has_content ) : ?>
		<section class="issue-content">
			<div class="issue-content__inner">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- TAKE ACTION STRIP -->
	<section class="issue-action-strip">
		<div class="issue-action-strip__inner">
			<p>Stop Echo Canyon. Add your name.</p>
			<div class="issue-action-strip__buttons">
				<a class="wp-element-button issue-action-strip__primary"
				   href="/take-action/sign-the-petition/">Sign the Petition</a>
				<a class="wp-element-button issue-action-strip__secondary"
				   href="/donate/">Donate</a>
			</div>
		</div>
	</section>

</main>

<?php block_template_part( 'footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
