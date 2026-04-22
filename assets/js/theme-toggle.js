/**
 * Light / dark theme: sets data-theme on documentElement (semantic tokens in wpis-global.css).
 */
(function () {
	'use strict';

	const root = document.documentElement;
	const storageKey = 'wpis-theme';

	function readStored() {
		try {
			return localStorage.getItem( storageKey );
		} catch {
			return null;
		}
	}

	function writeStored( value ) {
		try {
			localStorage.setItem( storageKey, value );
		} catch {
			/* ignore */
		}
	}

	function applyFromStorage() {
		const stored = readStored();
		if ( stored === 'light' || stored === 'dark' ) {
			root.setAttribute( 'data-theme', stored );
			return;
		}
		root.removeAttribute( 'data-theme' );
	}

	function effectiveTheme() {
		const attr = root.getAttribute( 'data-theme' );
		if ( attr === 'light' || attr === 'dark' ) {
			return attr;
		}
		return window.matchMedia( '(prefers-color-scheme: dark)' ).matches
			? 'dark'
			: 'light';
	}

	const TOGGLE_SELECTOR = '.wpis-theme-toggle, .site-theme-toggle';

	function toggleControl( el ) {
		if ( ! el ) {
			return null;
		}
		return el.matches( 'a, button' ) ? el : el.querySelector( 'a, button' );
	}

	function syncToggleIcons() {
		const icon = effectiveTheme() === 'dark' ? '\u2600' : '\u263e';
		document.querySelectorAll( TOGGLE_SELECTOR ).forEach( function ( el ) {
			const t = toggleControl( el );
			if ( t ) {
				t.textContent = icon;
			}
		} );
	}

	applyFromStorage();
	syncToggleIcons();

	document.addEventListener( 'click', function ( e ) {
		const wrap = e.target.closest( TOGGLE_SELECTOR );
		if ( ! wrap ) {
			return;
		}
		const btn = toggleControl( wrap );
		if ( ! btn ) {
			return;
		}
		e.preventDefault();
		const next = effectiveTheme() === 'dark' ? 'light' : 'dark';
		root.setAttribute( 'data-theme', next );
		writeStored( next );
		syncToggleIcons();
	} );

	window
		.matchMedia( '(prefers-color-scheme: dark)' )
		.addEventListener( 'change', function () {
			if ( ! readStored() ) {
				root.removeAttribute( 'data-theme' );
				syncToggleIcons();
			}
		} );
} )();
