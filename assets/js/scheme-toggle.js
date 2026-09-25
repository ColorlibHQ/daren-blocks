/**
 * The dark-mode switch.
 *
 * The preference is applied in the head, before paint, by the inline script in
 * inc/scheme.php. This file only handles the click and keeps the label honest,
 * so it can be deferred without the page flashing.
 *
 * @package Daren
 */

( function () {
	'use strict';

	var KEY = 'daren-scheme';
	var root = document.documentElement;
	var strings = window.darenScheme || { toDark: 'Switch to dark mode', toLight: 'Switch to light mode' };

	function isDark() {
		return root.classList.contains( 'daren-dark' );
	}

	function label( button ) {
		var dark = isDark();
		button.setAttribute( 'aria-pressed', dark ? 'true' : 'false' );
		button.setAttribute( 'aria-label', dark ? strings.toLight : strings.toDark );
	}

	function apply( dark ) {
		root.classList.add( 'daren-scheme-switching' );
		root.classList.toggle( 'daren-dark', dark );
		window.requestAnimationFrame( function () {
			window.requestAnimationFrame( function () {
				root.classList.remove( 'daren-scheme-switching' );
			} );
		} );
		root.style.colorScheme = dark ? 'dark' : 'light';
		try {
			localStorage.setItem( KEY, dark ? 'dark' : 'light' );
		} catch ( e ) {}
	}

	function init() {
		// The class is on the wrapper the editor writes; the control is the
		// link inside it. Binding the wrapper as well would attach two
		// listeners, and one click would toggle twice and land back where it
		// started.
		var buttons = document.querySelectorAll( '.daren-scheme-toggle .wp-block-button__link' );

		if ( ! buttons.length ) {
			return;
		}

		Array.prototype.forEach.call( buttons, function ( button ) {
			label( button );

			button.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				apply( ! isDark() );
				Array.prototype.forEach.call( buttons, label );
			} );
		} );

		// Follow the system until someone chooses for themselves.
		try {
			if ( ! localStorage.getItem( KEY ) && window.matchMedia ) {
				window.matchMedia( '(prefers-color-scheme: dark)' ).addEventListener( 'change', function ( e ) {
					if ( localStorage.getItem( KEY ) ) {
						return;
					}
					root.classList.toggle( 'daren-dark', e.matches );
					Array.prototype.forEach.call( buttons, label );
				} );
			}
		} catch ( e ) {}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
