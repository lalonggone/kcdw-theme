<?php
/**
 * Template Name: Lawsuit
 * Template Post Type: page
 *
 * Content is keyed by post slug — edit the $lawsuits array below to update
 * status, plaintiffs, court meta, summary, and latest update.
 * Block editor content (the_content) renders below for detailed filings,
 * transcripts, or supplemental legal context.
 */

declare( strict_types = 1 );

if ( have_posts() ) {
	the_post();
}

$lawsuits = [

	'water-rights-lawsuit' => [
		'status'      => 'active',
		'plaintiffs'  => 'Living Rivers, Kane Creek Development Watch',
		'filed'       => 'June 28, 2025',
		'court'       => 'Utah Seventh District Court, Grand County',
		'case_num'    => '270700892',
		'summary'     => 'KCDW and Living Rivers filed suit challenging the Utah Division of Water Rights\' approval of a diversion permit for the Echo Canyon resort development. The permit would authorize withdrawal of up to 550 acre-feet per year from the Colorado River — water that does not exist in a system already overallocated by more than 20% of average annual flow.',
		'update'      => 'The developer\'s motion to dismiss was denied in full. Discovery is underway. The court has set a scheduling conference for briefing on the merits.',
		'update_date' => 'September 15, 2025',
		'doc_url'     => '',
	],

	'latest-information-2' => [
		'status'      => 'active',
		'plaintiffs'  => 'Kane Creek Development Watch',
		'filed'       => 'April 1, 2025',
		'court'       => 'Utah Seventh District Court, Grand County',
		'case_num'    => '270700771',
		'summary'     => 'SB 258 was signed into Utah law in March 2025, creating a special land use designation for the Echo Canyon parcel that bypassed standard Grand County zoning review and environmental impact assessment requirements. KCDW filed suit immediately, arguing the bill constitutes legislative spot-zoning and violates the Utah Constitution\'s uniform operation of laws clause.',
		'update'      => 'Briefing on cross-motions for summary judgment is ongoing. The court denied the developer\'s motion to strike KCDW\'s expert witness testimony.',
		'update_date' => 'November 1, 2025',
		'doc_url'     => '',
	],

];

$status_labels = [
	'active'    => 'Active',
	'pending'   => 'Pending',
	'settled'   => 'Settled',
	'dismissed' => 'Dismissed',
];

$slug         = get_post_field( 'post_name' );
$lawsuit      = $lawsuits[ $slug ] ?? [];
$status       = $lawsuit['status']      ?? 'active';
$status_label = $status_labels[ $status ] ?? ucfirst( $status );
$plaintiffs   = $lawsuit['plaintiffs']  ?? '';
$filed        = $lawsuit['filed']       ?? '';
$court        = $lawsuit['court']       ?? '';
$case_num     = $lawsuit['case_num']    ?? '';
$summary      = $lawsuit['summary']     ?? '';
$update       = $lawsuit['update']      ?? '';
$update_date  = $lawsuit['update_date'] ?? '';
$doc_url      = $lawsuit['doc_url']     ?? '';
$has_content  = trim( strip_tags( get_the_content() ) ) !== '';
?>
<?php get_header(); ?>

<main id="main-content">

	<!-- LAWSUIT HEADER -->
	<section class="lawsuit-header">
		<div class="lawsuit-header__inner">

			<span class="lawsuit-status-badge lawsuit-status-badge--<?php echo esc_attr( $status ); ?>">
				<?php echo esc_html( $status_label ); ?>
			</span>

			<h1><?php the_title(); ?></h1>

			<?php if ( $plaintiffs ) : ?>
				<p class="lawsuit-plaintiffs">
					<strong>Plaintiffs:</strong> <?php echo esc_html( $plaintiffs ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $filed || $court || $case_num ) : ?>
				<dl class="lawsuit-meta">
					<?php if ( $filed ) : ?>
						<div class="lawsuit-meta__item">
							<dt class="lawsuit-meta__label">Filed</dt>
							<dd class="lawsuit-meta__value"><?php echo esc_html( $filed ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $court ) : ?>
						<div class="lawsuit-meta__item">
							<dt class="lawsuit-meta__label">Court</dt>
							<dd class="lawsuit-meta__value"><?php echo esc_html( $court ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $case_num ) : ?>
						<div class="lawsuit-meta__item">
							<dt class="lawsuit-meta__label">Case</dt>
							<dd class="lawsuit-meta__value"><?php echo esc_html( $case_num ); ?></dd>
						</div>
					<?php endif; ?>
				</dl>
			<?php endif; ?>

		</div>
	</section>

	<!-- SUMMARY -->
	<?php if ( $summary ) : ?>
		<section class="lawsuit-summary">
			<div class="lawsuit-summary__inner">
				<h2>What We're Challenging</h2>
				<p><?php echo esc_html( $summary ); ?></p>
			</div>
		</section>
	<?php endif; ?>

	<!-- LATEST UPDATE -->
	<?php if ( $update ) : ?>
		<section class="lawsuit-update">
			<div class="lawsuit-update__inner">

				<?php if ( $update_date ) : ?>
					<p class="lawsuit-update__date">Updated <?php echo esc_html( $update_date ); ?></p>
				<?php endif; ?>

				<h2>Latest Update</h2>
				<p><?php echo esc_html( $update ); ?></p>

				<?php if ( $doc_url ) : ?>
					<a class="lawsuit-update__doc-link"
					   href="<?php echo esc_url( $doc_url ); ?>"
					   target="_blank" rel="noopener">
						View court document →
					</a>
				<?php endif; ?>

			</div>
		</section>
	<?php endif; ?>

	<!-- BLOCK EDITOR CONTENT (filings, transcripts, legal detail) -->
	<?php if ( $has_content ) : ?>
		<section class="lawsuit-content">
			<div class="lawsuit-content__inner">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
