/**
 * Client-side wiring for the WPIS home feed:
 * - Filter show/hide toggle (`[data-wpis-toggle-filters]`).
 * - Three `<select data-wpis-filter="sentiment|claim|platform">` that mark
 *   non-matching `<article data-sentiment data-claim-type data-platform>` as
 *   `.hidden` (CSS handles the display).
 * - Sort tabs (`[data-sort="recent|repeated|random"]`) reorder articles in
 *   place using `data-repeat-count` and DOM order as fallback.
 * - Load-more button (`[data-wpis-load-more]`) reveals cards in batches
 *   defined by `data-step` (default 10).
 *
 * All controls are optional; if the markup is absent the script exits
 * quietly. No accessibility-breaking reshuffling: buttons have `aria-pressed`
 * updated on sort, the no-results message lives in `[role=status]`.
 */
(function () {
  'use strict';

  function onReady(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn, { once: true });
    }
  }

  function getFeedContainer(controls) {
    // The post-template is the nearest sibling ancestor that holds articles.
    var scope = controls.closest('.is-style-wpis-feed') || document;
    return scope.querySelector('.wp-block-post-template') || scope;
  }

  function getCards(container) {
    // Support the theme-file markup (article.is-style-wpis-quote-card) and the
    // stale Site Editor markup where WordPress renders the template-part as
    // <article class="wp-block-template-part"> with no custom class.
    var direct = container.querySelectorAll(
      'article.is-style-wpis-quote-card, article.wpis-quote-card'
    );
    if (direct.length) {
      return Array.prototype.slice.call(direct);
    }
    return Array.prototype.slice.call(
      container.querySelectorAll('article.wp-block-template-part')
    );
  }

  function matchesFilters(card, filters) {
    if (filters.sentiment && card.dataset.sentiment !== filters.sentiment) return false;
    if (filters.claim) {
      var claims = (card.dataset.claimType || '').split(/\s+/);
      if (claims.indexOf(filters.claim) === -1) return false;
    }
    if (filters.platform && card.dataset.platform !== filters.platform) return false;
    return true;
  }

  function applyFilters(container, filters, step, visibleCount) {
    var cards = getCards(container);
    var matched = [];
    cards.forEach(function (card) {
      if (matchesFilters(card, filters)) {
        matched.push(card);
      } else {
        card.classList.add('hidden');
      }
    });
    matched.forEach(function (card, idx) {
      if (idx < visibleCount) {
        card.classList.remove('hidden', 'hidden-batch');
      } else {
        card.classList.remove('hidden');
        card.classList.add('hidden-batch');
      }
    });
    return { matched: matched.length, cards: matched };
  }

  function updateLoadMore(loadMoreBtn, matched, visibleCount, step) {
    if (!loadMoreBtn) return;
    var remaining = matched - visibleCount;
    if (remaining <= 0) {
      loadMoreBtn.setAttribute('hidden', 'hidden');
      return;
    }
    loadMoreBtn.removeAttribute('hidden');
    var countEl = loadMoreBtn.querySelector('.wpis-load-more-count');
    if (countEl) {
      countEl.textContent = '(+' + Math.min(step, remaining) + ')';
    }
  }

  function updateEmpty(controls, matched) {
    var msg = controls.querySelector('.wpis-no-results-msg');
    if (!msg) return;
    if (matched === 0) {
      msg.removeAttribute('hidden');
    } else {
      msg.setAttribute('hidden', 'hidden');
    }
  }

  function sortCards(container, mode) {
    var parent = container;
    var cards = getCards(parent);
    if (mode === 'random') {
      for (var i = cards.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = cards[i];
        cards[i] = cards[j];
        cards[j] = tmp;
      }
    } else if (mode === 'repeated') {
      cards.sort(function (a, b) {
        return (parseInt(b.dataset.repeatCount || '0', 10) || 0) - (parseInt(a.dataset.repeatCount || '0', 10) || 0);
      });
    } else {
      // recent = original document order (preserved via data-original-index).
      cards.sort(function (a, b) {
        return (parseInt(a.dataset.originalIndex || '0', 10) || 0) - (parseInt(b.dataset.originalIndex || '0', 10) || 0);
      });
    }
    cards.forEach(function (card) {
      parent.appendChild(card);
    });
  }

  function stampOriginalOrder(container) {
    getCards(container).forEach(function (card, idx) {
      if (!card.dataset.originalIndex) {
        card.dataset.originalIndex = String(idx);
      }
    });
  }

  onReady(function () {
    var controls = document.querySelector('[data-wpis-feed-controls]');
    if (!controls) return;
    var container = getFeedContainer(controls);
    if (!container) return;

    var loadMoreBtn = document.querySelector('[data-wpis-load-more]');
    var step = loadMoreBtn ? parseInt(loadMoreBtn.dataset.step || '10', 10) || 10 : 10;
    var visibleCount = step;

    var filters = { sentiment: '', claim: '', platform: '' };
    stampOriginalOrder(container);

    function rerender() {
      var result = applyFilters(container, filters, step, visibleCount);
      updateLoadMore(loadMoreBtn, result.matched, visibleCount, step);
      updateEmpty(controls, result.matched);
    }

    var toggle = controls.querySelector('[data-wpis-toggle-filters]');
    var selectsWrap = controls.querySelector('.wpis-filter-selects');
    if (toggle && selectsWrap) {
      toggle.addEventListener('click', function () {
        var open = selectsWrap.getAttribute('data-open') !== 'false';
        selectsWrap.setAttribute('data-open', open ? 'false' : 'true');
        toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
        toggle.textContent = open ? 'Show filters' : 'Hide filters';
      });
    }

    controls.querySelectorAll('[data-wpis-filter]').forEach(function (select) {
      select.addEventListener('change', function () {
        var key = select.dataset.wpisFilter;
        filters[key] = select.value;
        if (select.value) {
          select.classList.add('has-value');
        } else {
          select.classList.remove('has-value');
        }
        visibleCount = step;
        rerender();
      });
    });

    var sortButtons = controls.querySelectorAll('.wpis-feed-sort-btn');
    sortButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var mode = btn.dataset.sort || 'recent';
        sortButtons.forEach(function (b) {
          b.classList.toggle('is-active', b === btn);
          b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
        });
        sortCards(container, mode);
        visibleCount = step;
        rerender();
      });
    });

    if (loadMoreBtn) {
      loadMoreBtn.addEventListener('click', function () {
        visibleCount += step;
        rerender();
      });
    }

    rerender();
  });
})();
