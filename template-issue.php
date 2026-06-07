<?php
/**
 * Template Name: Issue Page
 * Template Post Type: page
 *
 * Renders SCF issue fields: eyebrow, headline, intro body, key stat, optional
 * featured image as background, CTA. Block editor content renders below for
 * long-form text (evidence, sources, history). Ends with a take-action strip.
 *
 * SCF fields used: issue_eyebrow, issue_intro_headline, issue_intro_body,
 *   issue_stat_value, issue_stat_label, issue_featured_image,
 *   issue_cta_label, issue_cta_url
 */

declare( strict_types = 1 );

// Gather all SCF data before outputting any HTML.
if ( have_posts() ) {
	the_post();
}

$eyebrow     = get_field( 'issue_eyebrow' );
$headline    = get_field( 'issue_intro_headline' ) ?: get_the_title();
$body        = get_field( 'issue_intro_body' );
$stat_val    = get_field( 'issue_stat_value' );
$stat_lbl    = get_field( 'issue_stat_label' );
$img         = get_field( 'issue_featured_image' );
$cta_label   = get_field( 'issue_cta_label' ) ?: 'Sign the Petition';
$cta_url     = get_field( 'issue_cta_url' )   ?: '/take-action/sign-the-petition/';
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

	<!-- ISSUE HERO --------------------------------------------------------- -->
	<section class="issue-hero<?php echo $img ? ' issue-hero--has-image' : ''; ?>"
		<?php if ( $img ) : ?>
			style="background-image:url('<?php echo esc_url( $img['url'] ); ?>')"
		<?php endif; ?>>
		<div class="issue-hero__inner">

			<?php if ( $eyebrow ) : ?>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h1><?php echo esc_html( $headline ); ?></h1>

			<?php if ( $body ) : ?>
				<div class="issue-hero__body"><?php echo wp_kses_post( $body ); ?></div>
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

	<!-- BLOCK EDITOR CONTENT (long-form) ----------------------------------- -->
	<?php if ( $has_content ) : ?>
		<section class="issue-content">
			<div class="issue-content__inner">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- TAKE ACTION STRIP -------------------------------------------------- -->
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
