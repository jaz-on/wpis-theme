/**
 * Home feed demo: show/hide filters, sort, filter selects, load more (static HTML cards).
 */
(function () {
	'use strict';

	/* Block anchors are passed through sanitize_title() in WordPress, so camelCase becomes lowercase. */
	var feedList =
		document.getElementById('feedlist') ||
		document.getElementById('feedList');

	var showToggle = document.getElementById('filterShowToggle');
	var filterSelects = document.getElementById('filterSelects');
	if (showToggle && filterSelects) {
		showToggle.addEventListener('click', function () {
			var hidden = filterSelects.classList.toggle('hidden');
			showToggle.textContent = hidden ? 'Show filters' : 'Hide filters';
			showToggle.setAttribute('aria-expanded', (!hidden).toString());
		});
	}

	if (!feedList) {
		return;
	}

	function getWrappers() {
		return Array.prototype.slice.call(feedList.children).filter(function (el) {
			return el.classList && el.classList.contains('wp-block-html');
		});
	}

	function getCard(wrap) {
		return wrap ? wrap.querySelector('a.quote-card') : null;
	}

	var sortWrap = document.getElementById('feedSort');
	var sortButtons = sortWrap ? sortWrap.querySelectorAll('button') : [];
	var filterSentiment = document.getElementById('filterSentiment');
	var filterClaim = document.getElementById('filterClaim');
	var filterPlatform = document.getElementById('filterPlatform');
	var noResults = document.getElementById('noResults');
	var loadMoreBtn = document.getElementById('loadMoreBtn');

	var extraQuotes = [
		{ text: 'WordPress <span class="is-word">is</span> a Swiss Army knife. Use the right blade.', sent: 'mixed', claim: 'Ease of use', platforms: 'Mastodon,Blog', count: '7', date: '2026-04-03' },
		{ text: 'Every time I try to leave WordPress, I come back six months later.', sent: 'mixed', claim: 'Community', platforms: 'Reddit,HN', count: '12', date: '2026-04-02' },
		{ text: 'WordPress <span class="is-word">is</span> a dinosaur that refuses to die and that\'s kind of beautiful.', sent: 'positive', claim: 'Community', platforms: 'Mastodon,Bluesky', count: '5', date: '2026-04-01' },
		{ text: 'You cannot build a modern web app with WordPress in 2026. Move on.', sent: 'negative', claim: 'Modernity', platforms: 'X,HN', count: '19', date: '2026-03-31' },
		{ text: 'WordPress has saved more small businesses than any VC-backed SaaS ever will.', sent: 'positive', claim: 'Business viability', platforms: 'LinkedIn,Blog,Mastodon', count: '15', date: '2026-03-30' },
		{ text: 'WordPress updates break my site every other month. Cool ecosystem.', sent: 'negative', claim: 'Ecosystem', platforms: 'Reddit,X', count: '11', date: '2026-03-29' },
		{ text: 'WordPress <span class="is-word">is</span> the accessible web\'s best friend, if only the community prioritized it.', sent: 'mixed', claim: 'Accessibility', platforms: 'Mastodon,Blog', count: '6', date: '2026-03-28' },
		{ text: 'The WordPress admin UI feels like a museum exhibit.', sent: 'negative', claim: 'Ease of use', platforms: 'X,HN,Reddit', count: '14', date: '2026-03-27' },
		{ text: 'WordPress <span class="is-word">is</span> proof that open source wins in the long run.', sent: 'positive', claim: 'Community', platforms: 'Mastodon,Blog,LinkedIn', count: '22', date: '2026-03-26' },
		{ text: 'WordPress security <span class="is-word">is</span> a shared responsibility people keep forgetting about.', sent: 'mixed', claim: 'Security', platforms: 'Blog,Mastodon', count: '8', date: '2026-03-25' },
		{ text: 'Nothing scales quite like a well-architected WordPress setup behind Cloudflare.', sent: 'positive', claim: 'Performance', platforms: 'Blog,Reddit', count: '9', date: '2026-03-24' },
		{ text: 'WordPress <span class="is-word">is</span> the last place where the independent web still breathes.', sent: 'positive', claim: 'Community', platforms: 'Mastodon,Blog', count: '18', date: '2026-03-23' },
		{ text: 'I\'ve seen more WordPress sites hacked than I can count. The pattern repeats.', sent: 'negative', claim: 'Security', platforms: 'LinkedIn,Reddit', count: '13', date: '2026-03-22' },
		{ text: 'WordPress <span class="is-word">is</span> easy to start, hard to master: like any serious tool.', sent: 'mixed', claim: 'Ease of use', platforms: 'Blog,Mastodon', count: '7', date: '2026-03-21' },
		{ text: 'WooCommerce on WordPress still beats Shopify for anyone who wants to own their data.', sent: 'positive', claim: 'Business viability', platforms: 'Blog,LinkedIn,Mastodon', count: '16', date: '2026-03-20' },
		{ text: 'WordPress <span class="is-word">is</span> what you make of it. Stop blaming the tool for your choices.', sent: 'mixed', claim: 'Ecosystem', platforms: 'Reddit,X,Mastodon', count: '10', date: '2026-03-19' },
	];

	var extraIndex = 0;
	var currentSort = 'recent';

	function createQuoteCard(q) {
		var claim = q.claim || '';
		var count = q.count || '0';
		var countNum = String(count).replace(/\D/g, '') || '0';
		var a = document.createElement('a');
		a.href = '/quote/sample/';
		a.className = 'quote-card sent-' + q.sent;
		a.setAttribute('data-sentiment', q.sent);
		a.setAttribute('data-claim', claim);
		a.setAttribute('data-platforms', q.platforms || '');
		a.setAttribute('data-date', q.date || '');
		a.setAttribute('data-count', countNum);
		a.setAttribute('aria-label', 'View quote');
		a.innerHTML =
			'<span class="quote-text">' + q.text + '</span>' +
			'<span class="quote-footer">' +
			'<span class="claim-tag">' + claim + '</span>' +
			'<span class="count-badge">×' + countNum + '</span>' +
			'</span>';
		var wrap = document.createElement('div');
		wrap.className = 'wp-block-html';
		wrap.appendChild(a);
		return wrap;
	}

	function applySort() {
		if (!sortButtons.length) {
			return;
		}
		var sortKey = null;
		Array.prototype.forEach.call(sortButtons, function (b) {
			if (b.classList.contains('active') && b.dataset.sort) {
				sortKey = b.dataset.sort;
			}
		});
		if (!sortKey && sortButtons[0] && sortButtons[0].dataset.sort) {
			sortKey = sortButtons[0].dataset.sort;
		}
		if (!sortKey) {
			sortKey = 'recent';
		}
		currentSort = sortKey;

		var wrappers = getWrappers();
		var sorted = wrappers.slice();
		sorted.sort(function (a, b) {
			var ca = getCard(a);
			var cb = getCard(b);
			if (!ca || !cb) {
				return 0;
			}
			if (currentSort === 'recent') {
				return (cb.dataset.date || '').localeCompare(ca.dataset.date || '');
			}
			if (currentSort === 'repeated') {
				return parseInt(cb.dataset.count || '0', 10) - parseInt(ca.dataset.count || '0', 10);
			}
			return Math.random() - 0.5;
		});
		sorted.forEach(function (w) {
			feedList.appendChild(w);
		});
	}

	function applyFilter() {
		var s = filterSentiment ? filterSentiment.value : '';
		var c = filterClaim ? filterClaim.value : '';
		var p = filterPlatform ? filterPlatform.value : '';
		var visibleCount = 0;

		getWrappers().forEach(function (wrap) {
			var card = getCard(wrap);
			if (!card) {
				return;
			}
			var matchS = !s || card.dataset.sentiment === s;
			var matchC = !c || card.dataset.claim === c;
			var plat = card.dataset.platforms || '';
			var matchP = !p || plat.split(',').map(function (x) { return x.trim(); }).indexOf(p) !== -1;
			var visible = matchS && matchC && matchP;
			card.classList.toggle('hidden', !visible);
			if (visible) {
				visibleCount++;
			}
		});

		if (noResults) {
			noResults.classList.toggle('hidden', visibleCount > 0);
		}

		[filterSentiment, filterClaim, filterPlatform].forEach(function (sel) {
			if (sel) {
				sel.classList.toggle('has-value', !!sel.value);
			}
		});
	}

	if (sortButtons.length) {
		Array.prototype.forEach.call(sortButtons, function (btn) {
			btn.addEventListener('click', function () {
				Array.prototype.forEach.call(sortButtons, function (b) {
					b.classList.remove('active');
				});
				btn.classList.add('active');
				applySort();
			});
		});
	}

	[filterSentiment, filterClaim, filterPlatform].forEach(function (sel) {
		if (sel) {
			sel.addEventListener('change', applyFilter);
		}
	});

	if (loadMoreBtn) {
		loadMoreBtn.addEventListener('click', function () {
			var batchSize = 6;
			for (var i = 0; i < batchSize; i++) {
				var q = extraQuotes[extraIndex % extraQuotes.length];
				extraIndex++;
				feedList.appendChild(createQuoteCard(q));
			}
			applySort();
			applyFilter();
		});
	}

	applySort();
	applyFilter();
})();
