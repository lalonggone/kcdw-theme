<?php
/**
 * Template: Header
 *
 * Outputs the opening HTML document structure and sticky site header.
 * Called via get_header() in every template.
 *
 * @package KCDW\Theme
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
	<div class="site-header__inner">

		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			// Output the logo image directly rather than the_custom_logo(), which
			// would wrap the image in its own <a> and nest anchors inside this link.
			// A Customizer logo (custom_logo theme mod) takes precedence if set;
			// otherwise fall back to the logo bundled with the theme so the brand
			// mark ships with the code and renders on every environment.
			$kcdw_logo_id = get_theme_mod( 'custom_logo' );
			if ( $kcdw_logo_id ) :
				echo wp_get_attachment_image(
					$kcdw_logo_id,
					'full',
					false,
					[
						'class' => 'site-header__logo-img',
						'alt'   => esc_attr( get_bloginfo( 'name' ) ),
					]
				);
			else : ?>
				<img
					class="site-header__logo-img"
					src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logos/kane-creek-development-watch.png' ) ); ?>"
					width="768"
					height="201"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				>
			<?php endif; ?>
		</a>

		<button class="site-header__menu-toggle" aria-expanded="false" aria-controls="site-primary-nav" aria-label="Toggle navigation">
			<span></span><span></span><span></span>
		</button>

		<nav id="site-primary-nav" class="site-header__nav" aria-label="Primary navigation">
			<?php wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__menu',
				'fallback_cb'    => false,
			] ); ?>
		</nav>

		<div class="site-header__cta">
			<a class="btn btn--sienna" href="<?php echo esc_url( home_url( '/donate/' ) ); ?>">Donate</a>
		</div>

	</div>
</header>
