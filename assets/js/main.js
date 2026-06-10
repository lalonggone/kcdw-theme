/**
 * KCDW Theme — Main JS
 * Mobile navigation toggle.
 *
 * @package KCDW/Theme
 */

document.addEventListener( 'DOMContentLoaded', runThemeScripts, { once: true } );

function runThemeScripts() {
	mobileNavToggle();
}

function mobileNavToggle() {
	const toggle = document.querySelector( '.site-header__menu-toggle' );
	const nav    = document.getElementById( 'site-primary-nav' );
	if ( ! toggle || ! nav ) return;

	toggle.addEventListener( 'click', () => {
		const isOpen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', String( isOpen ) );
	} );

	document.addEventListener( 'click', ( e ) => {
		if ( ! toggle.contains( e.target ) && ! nav.contains( e.target ) ) {
			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		}
	} );
}
