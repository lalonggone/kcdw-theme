/**
 * KCDW Theme — Main JS
 * Animates elements when they scroll into the viewport.
 *
 * @package KCDW/Theme
 */

document.addEventListener( 'DOMContentLoaded', runThemeScripts, { once: true } );

function runThemeScripts() {
	animateOnView();
}

function animateOnView( { selector = '.animate', once = true } = {} ) {
	const observer = new IntersectionObserver(
		( entries ) => {
			for ( const entry of entries ) {
				if ( ! entry.isIntersecting ) {
					continue;
				}
				const el = entry.target;
				const list = el.classList.contains( 'animate-list' );

				if ( list ) {
					const children = el.children;
					const enteringFromBottomHalf = -entry.boundingClientRect.top > entry.boundingClientRect.bottom;

					Array.from( children ).forEach( ( child, index ) => {
						const animateIndex = enteringFromBottomHalf ? children.length - 1 - index : index;
						child.style.setProperty( '--animate-i', animateIndex );
						child.classList.add( 'animate-item' );
					} );
				}

				el.classList.replace( 'animate', 'animated' );

				if ( once ) {
					observer.unobserve( el );
				}
			}
		},
		{
			threshold: 0,
			rootMargin: '-50px 0px',
		}
	);

	const targets = [ ...document.querySelectorAll( selector ) ];

	if ( ! targets.length ) {
		return () => {};
	}

	targets.forEach( ( el ) => {
		observer.observe( el );
	} );

	return () => observer.disconnect();
}
