<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * Hardcoded content — edit the variables below to update copy.
 * Coalition members are a plain PHP array; add or remove entries as needed.
 */

declare( strict_types = 1 );

if ( have_posts() ) {
	the_post();
}

$headline = 'We Are Kane Creek Development Watch';

$intro = 'KCDW is a coalition of Moab residents, river advocates, Indigenous community members, housing organizers, and outdoor recreationists united around a single goal: stopping the Echo Canyon luxury resort development on the Colorado River corridor. We are not anti-development. We are anti-this-development, in this place, for these reasons.';

$mission = 'The canyon is not for sale. The river is not a resource to be auctioned to the highest bidder. We intend to keep it that way.';

$coalition = [
	[
		'name'  => 'Living Rivers',
		'org'   => 'Living Rivers',
		'title' => 'Co-plaintiff, water rights litigation',
		'bio'   => 'Living Rivers has advocated for the Colorado River ecosystem for over 25 years, working to restore and protect river flows throughout the Colorado Basin. They are co-plaintiffs in the water rights lawsuit challenging the Echo Canyon diversion permit.',
	],
	[
		'name'  => 'Grand Canyon Trust',
		'org'   => 'Grand Canyon Trust',
		'title' => 'Coalition partner',
		'bio'   => 'The Grand Canyon Trust works to protect and restore the Colorado Plateau\'s canyon country. They have provided technical support and public advocacy for the KCDW campaign.',
	],
	[
		'name'  => 'Canyonlands Watershed Council',
		'org'   => 'Canyonlands Watershed Council',
		'title' => 'Fiscal sponsor',
		'bio'   => 'CWC is the fiscal sponsor for Kane Creek Development Watch. CWC EIN: 87-0637713. All donations to KCDW are processed through CWC and are tax-deductible.',
	],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'template-about' ); ?>>
<?php wp_body_open(); ?>

<?php block_template_part( 'header' ); ?>

<main id="main-content">

	<!-- ABOUT HERO -->
	<section class="about-hero">
		<div class="about-hero__inner">
			<h1><?php echo esc_html( $headline ); ?></h1>
			<p class="about-hero__intro"><?php echo esc_html( $intro ); ?></p>
		</div>
	</section>

	<!-- MISSION STATEMENT -->
	<section class="about-mission">
		<div class="about-mission__inner">
			<blockquote>
				<p><?php echo esc_html( $mission ); ?></p>
			</blockquote>
		</div>
	</section>

	<!-- COALITION MEMBERS -->
	<section class="about-coalition">
		<div class="about-coalition__inner">
			<h2>The Coalition</h2>
			<ul class="coalition-grid">
				<?php foreach ( $coalition as $member ) : ?>
					<li class="coalition-member">
						<p class="coalition-member__name"><?php echo esc_html( $member['name'] ); ?></p>
						<?php if ( $member['org'] && $member['org'] !== $member['name'] ) : ?>
							<p class="coalition-member__org"><?php echo esc_html( $member['org'] ); ?></p>
						<?php endif; ?>
						<p class="coalition-member__title"><?php echo esc_html( $member['title'] ); ?></p>
						<p class="coalition-member__bio"><?php echo esc_html( $member['bio'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

</main>

<?php block_template_part( 'footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
