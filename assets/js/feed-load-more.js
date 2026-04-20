/**
 * Append quote cards via wpis/v1/quote-feed; optional auto-load when the button nears the viewport.
 */
(function () {
	'use strict';
	const cfg = window.wpisFeedLoadMore;
	if ( ! cfg || ! cfg.restUrl ) {
		return;
	}
	const root = document.querySelector( '.wpis-quote-feed' );
	if ( ! root ) {
		return;
	}
	const tpl = root.querySelector( '.wp-block-post-template' );
	const btn = root.querySelector( '.wpis-load-more' );
	const pag = root.querySelector( '.wpis-feed-pagination' );
	if ( ! tpl || ! btn ) {
		return;
	}

	document.documentElement.classList.add( 'wpis-feed-js' );

	if ( cfg.currentPaged >= cfg.totalPages ) {
		btn.remove();
		return;
	}

	let scrollObserver = null;

	function stopObserving() {
		if ( scrollObserver ) {
			scrollObserver.disconnect();
			scrollObserver = null;
		}
	}

	if ( pag ) {
		pag.setAttribute( 'aria-hidden', 'true' );
		pag.style.cssText =
			'position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;';
	}

	btn.textContent = cfg.i18n.loadMore;
	btn.hidden = false;
	btn.style.display = '';

	let nextPage = cfg.currentPaged + 1;
	let loading = false;

	function feedUrl( page ) {
		const u = new URL( cfg.restUrl, window.location.origin );
		u.searchParams.set( 'page', String( page ) );
		u.searchParams.set( 'per_page', String( cfg.perPage ) );
		u.searchParams.set( 'wpis_sort', cfg.sort || 'date' );
		u.searchParams.set( 'wpis_order', cfg.order || 'DESC' );
		if ( cfg.sentiment ) {
			u.searchParams.set( 'sentiment', cfg.sentiment );
		}
		if ( cfg.claimType ) {
			u.searchParams.set( 'claim_type', cfg.claimType );
		}
		if ( cfg.platform ) {
			u.searchParams.set( 'platform', cfg.platform );
		}
		if ( cfg.lang ) {
			u.searchParams.set( 'lang', cfg.lang );
		}
		return u.toString();
	}

	function loadNext() {
		if ( loading || nextPage > cfg.totalPages ) {
			return;
		}
		loading = true;
		btn.setAttribute( 'aria-busy', 'true' );
		btn.disabled = true;
		const prevLabel = btn.textContent;
		btn.textContent = cfg.i18n.loading;

		fetch( feedUrl( nextPage ), { credentials: 'same-origin' } )
			.then( function ( r ) {
				if ( ! r.ok ) {
					throw new Error( 'feed request failed' );
				}
				return r.json();
			} )
			.then( function ( data ) {
				const max = data.total_pages || cfg.totalPages;
				if ( data.html ) {
					tpl.insertAdjacentHTML( 'beforeend', data.html );
				}
				if ( nextPage >= max ) {
					stopObserving();
					btn.remove();
					return;
				}
				nextPage += 1;
			} )
			.catch( function () {
				btn.textContent = prevLabel;
			} )
			.finally( function () {
				if ( btn.isConnected ) {
					btn.removeAttribute( 'aria-busy' );
					btn.disabled = false;
					btn.textContent = cfg.i18n.loadMore;
				}
				loading = false;
			} );
	}

	btn.addEventListener( 'click', loadNext );

	if ( 'IntersectionObserver' in window ) {
		scrollObserver = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting && entry.target === btn ) {
						loadNext();
					}
				} );
			},
			{ root: null, rootMargin: '160px 0px', threshold: 0 }
		);
		scrollObserver.observe( btn );
	}
}());
