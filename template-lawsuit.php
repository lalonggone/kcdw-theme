<?php
/**
 * Template Name: Lawsuit
 * Template Post Type: page
 *
 * Renders SCF lawsuit fields: status badge, plaintiffs, court meta, summary,
 * latest update with date, optional court document link.
 * Block editor content renders below for additional legal detail.
 *
 * SCF fields used: lawsuit_status, lawsuit_plaintiffs, lawsuit_filed_date,
 *   lawsuit_court, lawsuit_case_number, lawsuit_summary, lawsuit_latest_update,
 *   lawsuit_latest_update_date, lawsuit_document_url
 */

declare( strict_types = 1 );

if ( have_posts() ) {
	the_post();
}

$status      = get_field( 'lawsuit_status' )              ?: 'active';
$plaintiffs  = get_field( 'lawsuit_plaintiffs' );
$filed       = get_field( 'lawsuit_filed_date' );
$court       = get_field( 'lawsuit_court' );
$case_num    = get_field( 'lawsuit_case_number' );
$summary     = get_field( 'lawsuit_summary' );
$update      = get_field( 'lawsuit_latest_update' );
$update_date = get_field( 'lawsuit_latest_update_date' );
$doc_url     = get_field( 'lawsuit_document_url' );
$has_content = trim( strip_tags( get_the_content() ) ) !== '';

$status_labels = [
	'active'    => 'Active',
	'pending'   => 'Pending',
	'settled'   => 'Settled',
	'dismissed' => 'Dismissed',
];
$status_label = $status_labels[ $status ] ?? ucfirst( $status );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'template-lawsuit' ); ?>>
<?php wp_body_open(); ?>

<?php block_template_part( 'header' ); ?>

<main id="main-content">

	<!-- LAWSUIT HEADER ----------------------------------------------------- -->
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

	<!-- SUMMARY ------------------------------------------------------------ -->
	<?php if ( $summary ) : ?>
		<section class="lawsuit-summary">
			<div class="lawsuit-summary__inner">
				<h2>What We're Challenging</h2>
				<p><?php echo wp_kses_post( $summary ); ?></p>
			</div>
		</section>
	<?php endif; ?>

	<!-- LATEST UPDATE ------------------------------------------------------ -->
	<?php if ( $update ) : ?>
		<section class="lawsuit-update">
			<div class="lawsuit-update__inner">

				<?php if ( $update_date ) : ?>
					<p class="lawsuit-update__date">Updated <?php echo esc_html( $update_date ); ?></p>
				<?php endif; ?>

				<h2>Latest Update</h2>
				<p><?php echo wp_kses_post( $update ); ?></p>

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

	<!-- BLOCK EDITOR CONTENT ----------------------------------------------- -->
	<?php if ( $has_content ) : ?>
		<section class="lawsuit-content">
			<div class="lawsuit-content__inner">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

</main>

<?php block_template_part( 'footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
