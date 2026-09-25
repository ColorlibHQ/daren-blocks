/**
 * Daren: the sticky header's shadow, and the Share links.
 *
 * Both are enhancements. Without this file the header still sticks (that is
 * CSS) and every Share link is an ordinary link to its story.
 */
( function () {
	'use strict';

	var strings = window.darenStrings || { copied: 'Link copied' };

	/*
	 * The template fixes its header once the page has scrolled 50px and gives
	 * it a shadow. position: sticky does the fixing; this adds the shadow, and
	 * only flips a class when the answer changes.
	 */
	function stickyHeader() {
		var header = document.querySelector( '.daren-site-header' );
		if ( ! header ) {
			return;
		}

		var stuck = null;
		var ticking = false;

		function update() {
			ticking = false;
			var now = window.scrollY > 50;
			if ( now !== stuck ) {
				stuck = now;
				header.classList.toggle( 'is-stuck', now );
			}
		}

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}, { passive: true } );

		update();
	}

	function announce( text ) {
		var note = document.createElement( 'div' );
		note.className = 'daren-copied';
		note.setAttribute( 'role', 'status' );
		note.textContent = text;
		document.body.appendChild( note );
		window.setTimeout( function () {
			note.remove();
		}, 2200 );
	}

	/*
	 * Share opens the system share sheet where there is one (phones, Safari,
	 * Edge), and copies the story's address where there is not.
	 */
	function share() {
		document.addEventListener( 'click', function ( event ) {
			var link = event.target.closest && event.target.closest( 'a[data-daren-share]' );
			if ( ! link ) {
				return;
			}

			var data = { title: link.getAttribute( 'data-daren-share' ), url: link.href };

			if ( navigator.share ) {
				event.preventDefault();
				navigator.share( data ).catch( function () {} );
				return;
			}

			if ( navigator.clipboard && window.isSecureContext ) {
				event.preventDefault();
				navigator.clipboard.writeText( data.url ).then( function () {
					announce( strings.copied );
				}, function () {
					window.location.href = data.url;
				} );
			}
		} );
	}

	function init() {
		stickyHeader();
		share();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
