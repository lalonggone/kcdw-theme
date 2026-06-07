<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * Renders SCF about fields: headline, intro, mission statement pull-quote,
 * and coalition_members repeater (name, organization, title, bio, photo).
 * Block editor content renders below for additional org history or detail.
 *
 * SCF fields used: about_headline, about_intro, mission_statement,
 *   coalition_members (repeater: member_name, member_organization,
 *   member_title, member_bio, member_photo)
 */

declare( strict_types = 1 );

if ( have_posts() ) {
	the_post();
}

$headline    = get_field( 'about_headline' )     ?: get_the_title();
$intro       = get_field( 'about_intro' );
$mission     = get_field( 'mission_statement' );
$members     = get_field( 'coalition_members' )  ?: [];
$has_content = trim( strip_tags( get_the_content() ) ) !== '';
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

	<!-- ABOUT HERO --------------------------------------------------------- -->
	<section class="about-hero">
		<div class="about-hero__inner">
			<h1><?php echo esc_html( $headline ); ?></h1>
			<?php if ( $intro ) : ?>
				<p class="about-hero__intro"><?php echo wp_kses_post( $intro ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<!-- MISSION STATEMENT -------------------------------------------------- -->
	<?php if ( $mission ) : ?>
		<section class="about-mission">
			<div class="about-mission__inner">
				<blockquote>
					<p><?php echo esc_html( $mission ); ?></p>
				</blockquote>
			</div>
		</section>
	<?php endif; ?>

	<!-- COALITION MEMBERS -------------------------------------------------- -->
	<?php if ( $members ) : ?>
		<section class="about-coalition">
			<div class="about-coalition__inner">
				<h2>The Coalition</h2>
				<ul class="coalition-grid">
					<?php foreach ( $members as $member ) :
						$name  = $member['member_name']         ?? '';
						$org   = $member['member_organization'] ?? '';
						$title = $member['member_title']        ?? '';
						$bio   = $member['member_bio']          ?? '';
						$photo = $member['member_photo']        ?? null;
						if ( ! $name ) continue;
					?>
						<li class="coalition-member">

							<?php if ( $photo ) : ?>
								<img class="coalition-member__photo"
								     src="<?php echo esc_url( $photo['url'] ); ?>"
								     alt="<?php echo esc_attr( $photo['alt'] ?: $name ); ?>"
								     width="80" height="80">
							<?php endif; ?>

							<p class="coalition-member__name"><?php echo esc_html( $name ); ?></p>

							<?php if ( $org ) : ?>
								<p class="coalition-member__org"><?php echo esc_html( $org ); ?></p>
							<?php endif; ?>

							<?php if ( $title ) : ?>
								<p class="coalition-member__title"><?php echo esc_html( $title ); ?></p>
							<?php endif; ?>

							<?php if ( $bio ) : ?>
								<p class="coalition-member__bio"><?php echo esc_html( $bio ); ?></p>
							<?php endif; ?>

						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<!-- BLOCK EDITOR CONTENT ----------------------------------------------- -->
	<?php if ( $has_content ) : ?>
		<section class="about-content">
			<div class="about-content__inner">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

</main>

<?php block_template_part( 'footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
