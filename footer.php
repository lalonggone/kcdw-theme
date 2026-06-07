<?php
/**
 * Template: Footer
 *
 * Two zones: main footer band (kcdw-forest bg) + bottom bar (kcdw-midnight bg).
 * Called via get_footer() in every template.
 *
 * @package KCDW\Theme
 */
?>

<footer class="site-footer" role="contentinfo">

	<div class="site-footer__main">
		<div class="site-footer__main-inner">

			<div class="site-footer__brand">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
					<a class="site-footer__site-name" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<?php endif; ?>
				<p class="site-footer__tagline">Fighting to stop the Echo Canyon luxury resort development on the Colorado River corridor near Moab, Utah.</p>
				<ul class="site-footer__social">
					<li><a href="#" aria-label="Facebook">Facebook</a></li>
					<li><a href="#" aria-label="Instagram">Instagram</a></li>
				</ul>
			</div>

			<div class="site-footer__col">
				<h6 class="site-footer__col-heading">The Issues</h6>
				<?php wp_nav_menu( [
					'theme_location' => 'footer-issues',
					'container'      => false,
					'menu_class'     => 'site-footer__nav-list',
					'fallback_cb'    => false,
					'depth'          => 1,
				] ); ?>
			</div>

			<div class="site-footer__col">
				<h6 class="site-footer__col-heading">Take Action</h6>
				<?php wp_nav_menu( [
					'theme_location' => 'footer-action',
					'container'      => false,
					'menu_class'     => 'site-footer__nav-list',
					'fallback_cb'    => false,
					'depth'          => 1,
				] ); ?>
			</div>

			<div class="site-footer__col">
				<h6 class="site-footer__col-heading">Donate</h6>
				<p class="site-footer__donate-text">Your support funds legal action, public outreach, and the fight to protect the canyon.</p>
				<a class="btn btn--sienna" href="<?php echo esc_url( home_url( '/donate/' ) ); ?>">Donate Now</a>
			</div>

		</div>
	</div>

	<div class="site-footer__bottom">
		<div class="site-footer__bottom-inner">
			<p>The fiscal sponsor for Kane Creek Development Watch (KCDW) is Canyonlands Watershed Council (CWC). CWC EIN: 87-0637713. &copy; <?php echo esc_html( (string) gmdate( 'Y' ) ); ?>, Kane Creek Development Watch. All rights reserved.</p>
		</div>
	</div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
