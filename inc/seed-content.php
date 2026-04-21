<?php
/**
 * Block-first seed markup for demo pages (home feed, taxonomy, explore, search, profile, how).
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map sentiment to preset color for left border.
 *
 * @param string $sentiment negative|positive|mixed.
 * @return string CSS var.
 */
function wpis_theme_seed_sentiment_border( $sentiment ) {
	switch ( $sentiment ) {
		case 'positive':
			return 'var(--positive)';
		case 'mixed':
			return 'var(--mixed)';
		default:
			return 'var(--negative)';
	}
}

/**
 * One quote row: stacked group (border) + excerpt + meta row with link.
 *
 * @param array<string,string> $q Keys: sentiment, claim, count, text (inner HTML for <p>), url.
 * @return string
 */
function wpis_theme_seed_quote_stack( array $q ) {
	$border = wpis_theme_seed_sentiment_border( $q['sentiment'] );
	$claim  = isset( $q['claim'] ) ? esc_html( $q['claim'] ) : '';
	$count  = isset( $q['count'] ) ? esc_html( $q['count'] ) : '';
	$url    = isset( $q['url'] ) ? esc_url( $q['url'] ) : '/quote/sample/';
	$text   = isset( $q['text'] ) ? $q['text'] : '';

	return '
<!-- wp:group {"style":{"border":{"left":{"color":"' . $border . '","width":"3px","style":"solid"},"bottom":{"color":"var(--line)","width":"1px","style":"solid"}},"spacing":{"padding":{"top":"1.5rem","bottom":"1.5rem","left":"0.875rem"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"flex-start","flexWrap":"nowrap"}} -->
<!-- wp:paragraph {"className":"wpis-quote-excerpt","style":{"typography":{"fontSize":"1.375rem","lineHeight":"1.3","letterSpacing":"-0.01em","fontWeight":"400"},"color":{"text":"var(--ink)"}}} -->
<p>' . $text . '</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap","verticalAlignment":"center"},"style":{"spacing":{"blockGap":"0.75rem"}}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p><span style="border:1px solid var(--line);padding:3px 8px;color:var(--ink);font-weight:500">' . $claim . '</span> <span style="background:var(--ink);color:var(--bg);padding:3px 8px;font-weight:500">' . $count . '</span> <a href="' . $url . '" style="color:var(--accent);text-decoration:none;border-bottom:1px dotted var(--accent)">' . esc_html__( 'View quote', 'wpis-theme' ) . '</a></p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:group -->';
}

/**
 * @return list<array<string,string>>
 */
function wpis_theme_seed_home_quotes() {
	return array(
		array(
			'sentiment' => 'negative',
			'claim'     => 'Performance',
			'count'     => '×8',
			'text'      => 'WordPress <span class="is-word">is</span> bloated and slow on shared hosting.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Community',
			'count'     => '×3',
			'text'      => 'WordPress <span class="is-word">is</span> the reason I still have a career in the web.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Security',
			'count'     => '×24',
			'text'      => 'WordPress <span class="is-word">is</span> not secure: too many plugins with backdoors.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Modernity',
			'count'     => '×17',
			'text'      => 'WordPress <span class="is-word">is</span> just for blogs, you can\'t build a real app with it.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Community',
			'count'     => '×6',
			'text'      => 'WordPress <span class="is-word">is</span> more than software. It\'s two decades of people who believed in an open web.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Business viability',
			'count'     => '×11',
			'text'      => 'WordPress <span class="is-word">is</span> fine for small sites but don\'t scale it.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Ecosystem',
			'count'     => '×14',
			'text'      => 'WordPress <span class="is-word">is</span> a plugin graveyard. Half of them are abandoned.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Ease of use',
			'count'     => '×9',
			'text'      => 'WordPress <span class="is-word">is</span> the only CMS my non-technical clients can actually maintain themselves.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Modernity',
			'count'     => '×21',
			'text'      => 'Gutenberg <span class="is-word">is</span> both the best and worst thing that happened to WordPress.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Accessibility',
			'count'     => '×5',
			'text'      => 'WordPress core <span class="is-word">is</span> still shipping accessibility bugs that were reported years ago.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Community',
			'count'     => '×13',
			'text'      => 'Nothing beats the feeling of a WordCamp. The energy, the people, the openness.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Performance',
			'count'     => '×7',
			'text'      => 'WordPress <span class="is-word">is</span> what you get when PHP from 2003 meets a marketing team.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Business viability',
			'count'     => '×4',
			'text'      => 'WordPress <span class="is-word">is</span> a great starting point but you\'ll outgrow it faster than you think.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Ecosystem',
			'count'     => '×8',
			'text'      => 'The WordPress plugin ecosystem <span class="is-word">is</span> unmatched. Whatever you need, someone already built it.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Security',
			'count'     => '×6',
			'text'      => 'Every other week there\'s another WordPress plugin 0-day. At some point it\'s the platform\'s fault.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Modernity',
			'count'     => '×10',
			'text'      => 'Headless WordPress <span class="is-word">is</span> quietly powering more serious apps than people realize.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Ease of use',
			'count'     => '×5',
			'text'      => 'WordPress <span class="is-word">is</span> easy until you need it to do something specific. Then it\'s a nightmare.',
		),
	);
}

/**
 * Home page block content (hero + feed shell + quote stacks).
 *
 * @return string
 */
function wpis_theme_build_home_seed() {
	$hero = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-hero","layout":{"type":"constrained","contentSize":"1200px"}} -->
<!-- wp:paragraph {"className":"is-style-wpis-eyebrow"} -->
<p>A database of claims</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"hero-title"} -->
<h1 class="wp-block-heading hero-title">WordPress<br>is<span class="dots">…</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"hero-intro"} -->
<p class="hero-intro">A large slice of the web runs on it. It's one of the largest open-source projects in existence. Yet what people say about it ranges from <em>life-changing</em> to <em>worthless</em>. This is where those statements live: the good, the bad, the contradictory. Context always matters.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
    <div class="hero-stats">
      <div><strong>2,847</strong>quotes collected</div>
      <div><strong>12</strong>platforms sourced</div>
      <div><strong>6</strong>languages</div>
      <div><strong>47</strong>pending moderation</div>
    </div>
<!-- /wp:html -->
<!-- /wp:group -->

WPIS;

	$prefix = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-feed","layout":{"type":"constrained","contentSize":"1200px"}} -->
<!-- wp:html -->
    <div class="feed-header">
      <div class="feed-title">The feed <button type="button" class="filter-show-toggle" id="filterShowToggle" aria-expanded="true">Hide filters</button></div>
      <div class="feed-sort" id="feedSort">
        <button type="button" class="active" data-sort="recent">Recent</button>
        <button type="button" data-sort="repeated">Most repeated</button>
        <button type="button" data-sort="random">Random</button>
      </div>
    </div>

    <div class="filter-selects" id="filterSelects">
      <div class="filter-select-wrap">
        <select class="filter-select" id="filterSentiment">
          <option value="">All sentiments</option>
          <option value="negative">Negative (1247)</option>
          <option value="positive">Positive (892)</option>
          <option value="mixed">Mixed (512)</option>
          <option value="neutral">Neutral (196)</option>
        </select>
      </div>
      <div class="filter-select-wrap">
        <select class="filter-select" id="filterClaim">
          <option value="">All claim types</option>
          <option value="Performance">Performance (423)</option>
          <option value="Security">Security (287)</option>
          <option value="Ease of use">Ease of use (341)</option>
          <option value="Community">Community (512)</option>
          <option value="Ecosystem">Ecosystem (398)</option>
          <option value="Business viability">Business viability (214)</option>
          <option value="Accessibility">Accessibility (89)</option>
          <option value="Modernity">Modernity (503)</option>
        </select>
      </div>
      <div class="filter-select-wrap">
        <select class="filter-select" id="filterPlatform">
          <option value="">All platforms</option>
          <option value="Mastodon">Mastodon (841)</option>
          <option value="LinkedIn">LinkedIn (623)</option>
          <option value="Reddit">Reddit (412)</option>
          <option value="Bluesky">Bluesky (287)</option>
          <option value="X">X (284)</option>
          <option value="Blog">Blog (202)</option>
          <option value="YouTube">YouTube (198)</option>
          <option value="HN">HN (87)</option>
        </select>
      </div>
    </div>

<!-- /wp:html -->

<!-- wp:group {"anchor":"feedList","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0"}}} -->

WPIS;

	$suffix = <<<'WPIS'

<!-- /wp:group -->
<!-- wp:html -->
    <div class="no-results-msg hidden" id="noResults" role="status" aria-live="polite">No quotes match these filters yet.</div>
    <button type="button" class="load-more-btn" id="loadMoreBtn">Load more</button>
<!-- /wp:html -->
<!-- /wp:group -->

WPIS;

	$rows = '';
	foreach ( wpis_theme_seed_home_quotes() as $q ) {
		$q['url'] = '/quote/sample/';
		$rows    .= wpis_theme_seed_quote_stack( $q );
	}

	return $hero . $prefix . $rows . $suffix;
}

/**
 * Taxonomy security demo page.
 *
 * @return string
 */
function wpis_theme_build_security_seed() {
	$hero = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-tax-hero","layout":{"type":"constrained","contentSize":"1200px"}} -->
<!-- wp:paragraph {"className":"is-style-wpis-eyebrow"} -->
<p>Claim type</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"clamp(2.25rem, 10vw, 3.5rem)","fontWeight":"800","lineHeight":"1.05","letterSpacing":"-0.03em"}}} -->
<h1 class="wp-block-heading">Security</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem","lineHeight":"1.5"},"color":{"text":"var(--ink)"},"spacing":{"margin":{"bottom":"0"}}}} -->
<p>Claims about how safe, exposed or resilient WordPress is.</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

WPIS;

	$sub = <<<'WPIS'
<!-- wp:html -->
  <div class="subcat-bar-wrap">
    <div class="subcat-label">Narrow by sub-topic <span style="text-transform: none; font-style: italic; opacity: 0.7;">(these are child terms of the Security taxonomy. If a category has no children, this bar is hidden.)</span></div>
    <nav class="subcat-bar" aria-label="Security sub-topics">
      <a href="/taxonomy/security/" class="subcat-chip active">All <span class="chip-count">287</span></a>
      <a href="/taxonomy/security/" class="subcat-chip">Plugins <span class="chip-count">134</span></a>
      <a href="/taxonomy/security/" class="subcat-chip">Core <span class="chip-count">62</span></a>
      <a href="/taxonomy/security/" class="subcat-chip">E-commerce <span class="chip-count">41</span></a>
      <a href="/taxonomy/security/" class="subcat-chip">Updates <span class="chip-count">28</span></a>
      <a href="/taxonomy/security/" class="subcat-chip">Hosting <span class="chip-count">22</span></a>
    </nav>
  </div>
<!-- /wp:html -->

WPIS;

	$feed_open = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-feed","layout":{"type":"constrained","contentSize":"1200px"}} -->
<!-- wp:html -->
    <div class="feed-header">
      <div class="feed-title">287 quotes in this category</div>
      <div class="feed-sort">
        <button type="button" class="active">Most repeated</button>
        <button type="button">Recent</button>
        <button type="button">Random</button>
      </div>
    </div>

<!-- /wp:html -->

<!-- wp:group {"anchor":"feedList","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0"}}} -->

WPIS;

	$feed_close = <<<'WPIS'

<!-- /wp:group -->
<!-- /wp:group -->

WPIS;

	$quotes = array(
		array(
			'sentiment' => 'negative',
			'claim'     => 'Security',
			'count'     => '×24',
			'text'      => 'WordPress <span class="is-word">is</span> not secure: too many plugins with backdoors.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Security',
			'count'     => '×12',
			'text'      => 'WordPress <span class="is-word">is</span> as secure as you configure it to be: that\'s the point of open source.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Security',
			'count'     => '×18',
			'text'      => 'WordPress <span class="is-word">is</span> the most hacked CMS on the planet.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Security',
			'count'     => '×7',
			'text'      => 'WordPress <span class="is-word">is</span> secure if you know what you\'re doing, which most people don\'t.',
		),
	);
	$rows   = '';
	foreach ( $quotes as $q ) {
		$q['url'] = '/quote/sample/';
		$rows    .= wpis_theme_seed_quote_stack( $q );
	}

	return $hero . $sub . $feed_open . $rows . $feed_close;
}

/**
 * Search demo page.
 *
 * @return string
 */
function wpis_theme_build_search_demo_seed() {
	$top = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-search","layout":{"type":"constrained","contentSize":"900px"}} -->
<!-- wp:html -->
  <div class="search-bar">
    <span class="search-icon">⌕</span>
    <input type="text" value="bloated" placeholder="Search the database…">
  </div>
  <div class="search-summary"><strong>18 quotes</strong> match "bloated"</div>
<!-- /wp:html -->

WPIS;

	$quotes = array(
		array(
			'sentiment' => 'negative',
			'claim'     => 'Performance',
			'count'     => '×8',
			'text'      => 'WordPress <span class="is-word">is</span> <mark>bloated</mark> and slow on shared hosting.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Performance',
			'count'     => '×5',
			'text'      => 'Every <mark>bloated</mark> WordPress site I\'ve inherited had 40+ plugins. That\'s the real problem.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Performance',
			'count'     => '×4',
			'text'      => 'WordPress <span class="is-word">is</span> <mark>bloated</mark> only if you treat it like an app framework.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Modernity',
			'count'     => '×11',
			'text'      => 'Gutenberg made WordPress even more <mark>bloated</mark> than before.',
		),
	);
	$rows   = '';
	foreach ( $quotes as $q ) {
		$q['url'] = '/quote/sample/';
		$rows    .= wpis_theme_seed_quote_stack( $q );
	}

	return $top . $rows . "\n<!-- /wp:group -->\n";
}

/**
 * Explore page: hero + sections built from data.
 *
 * @return string
 */
function wpis_theme_build_explore_seed() {
	$hero = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-explore-hero","layout":{"type":"constrained","contentSize":"1200px"}} -->
<!-- wp:paragraph {"className":"is-style-wpis-eyebrow"} -->
<p>Explore · </p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"clamp(2.25rem, 10vw, 3.5rem)","fontWeight":"800","lineHeight":"1.05","letterSpacing":"-0.03em"}}} -->
<h1 class="wp-block-heading">The map of the conversation</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem","lineHeight":"1.5"},"color":{"text":"var(--ink)"},"spacing":{"margin":{"bottom":"0"}}}} -->
<p>All the claims people make about WordPress, grouped by what they're actually talking about. Pick a theme to dive into the arguments and counter-arguments.</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

WPIS;

	$cards = array(
		array( 'Security', '287 quotes', 'How safe, exposed or resilient WordPress is. Mostly about the plugin ecosystem, not core.' ),
		array( 'Performance', '423 quotes', 'Speed, weight, responsiveness. Often about hosting setup as much as WordPress itself.' ),
		array( 'Community', '512 quotes', 'The people, the WordCamps, the ecosystem of contributors. Often the most emotional category.' ),
		array( 'Modernity', '503 quotes', 'Is WordPress keeping up? Gutenberg, block editor, headless use: the most contested territory.' ),
		array( 'Ecosystem', '398 quotes', 'Plugins, themes, integrations. The breadth vs quality debate lives here.' ),
		array( 'Ease of use', '341 quotes', 'Who is WordPress for? The tension between accessibility for beginners and power for devs.' ),
		array( 'Business viability', '214 quotes', 'Can you build a real business on WordPress? Scale, pricing, long-term bets.' ),
		array( 'Accessibility', '89 quotes', 'Smaller category, big stakes. How WordPress handles disability, a11y standards and inclusion.' ),
	);

	$section_open  = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-explore-section","layout":{"type":"constrained","contentSize":"1200px"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem","textTransform":"uppercase","letterSpacing":"0.12em"},"color":{"text":"var(--muted)"}}} -->
<p>By claim type</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"grid","columnCount":2},"style":{"spacing":{"blockGap":"0.75rem"}}} -->

WPIS;
	$section_close = <<<'WPIS'

<!-- /wp:group -->
<!-- /wp:group -->

WPIS;

	$card_blocks = '';
	foreach ( $cards as $c ) {
		$title = esc_html( $c[0] );
		$meta  = esc_html( $c[1] );
		$desc  = esc_html( $c[2] );
		$card_blocks .= <<<CARD
<!-- wp:group {"style":{"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"1.25rem","right":"1.25rem","bottom":"1.25rem","left":"1.25rem"}},"color":{"background":"var(--paper)"}},"layout":{"type":"default"}} -->
<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"baseline","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"0.75rem"}}} -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">{$title}</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p>{$meta}</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:paragraph {"style":{"color":{"text":"var(--muted)"},"typography":{"fontSize":"0.875rem","lineHeight":"1.5"}}} -->
<p>{$desc}</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<!-- wp:button {"width":100,"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/taxonomy/security/">Browse {$title}</a></div>
<!-- /wp:button -->
<!-- /wp:buttons -->
<!-- /wp:group -->

CARD;
	}

	$platforms = array(
		array( 'Mastodon', '841' ),
		array( 'LinkedIn', '623' ),
		array( 'Reddit', '412' ),
		array( 'Bluesky', '287' ),
		array( 'X', '284' ),
		array( 'Blog', '202' ),
		array( 'YouTube', '198' ),
		array( 'HN', '87' ),
	);
	$plat_open   = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-explore-section","layout":{"type":"constrained","contentSize":"1200px"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem","textTransform":"uppercase","letterSpacing":"0.12em"},"color":{"text":"var(--muted)"}}} -->
<p>By source platform</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"grid","columnCount":2},"style":{"spacing":{"blockGap":"0.625rem"}}} -->

WPIS;
	$plat_close  = <<<'WPIS'

<!-- /wp:group -->
<!-- /wp:group -->

WPIS;

	$plat_blocks = '';
	foreach ( $platforms as $p ) {
		$n  = esc_html( $p[0] );
		$ct = esc_html( $p[1] );
		$plat_blocks .= <<<PLAT
<!-- wp:group {"style":{"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0.875rem","right":"0.875rem","bottom":"0.875rem","left":"0.875rem"}},"color":{"background":"var(--paper)"}},"layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"baseline"}} -->
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"0.9375rem","fontWeight":"500"}}} -->
<h4 class="wp-block-heading">{$n}</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem"},"color":{"text":"var(--muted)"}}} -->
<p>{$ct}</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

PLAT;
	}

	return $hero . $section_open . $card_blocks . $section_close . $plat_open . $plat_blocks . $plat_close;
}

/**
 * How it works: steps from data.
 *
 * @return string
 */
function wpis_theme_build_how_seed() {
	$steps = array(
		array(
			'num'   => '1',
			'title' => 'You spot a claim',
			'body'  => 'Someone online says something about WordPress. A tweet, a blog post, a Reddit thread, a video comment. You think it belongs here.',
			'note'  => '→ Or one of our bots finds it on Mastodon or Bluesky automatically.',
		),
		array(
			'num'   => '2',
			'title' => 'You submit it',
			'body'  => 'Paste the text, drop a screenshot or share the link. No account needed. We extract what\'s useful: the claim itself, the platform, the language, and strip out personal details.',
			'note'  => '→ Screenshots are deleted after extraction. Only the domain is stored, never the full URL.',
		),
		array(
			'num'   => '3',
			'title' => 'AI pre-tags it',
			'body'  => 'An AI suggests a sentiment, a claim type and a translation. It also looks for similar claims already in the database, to avoid duplicates. None of this is final: it\'s a starting point.',
			'note'  => '',
		),
		array(
			'num'   => '4',
			'title' => 'A human validates',
			'body'  => 'Every submission: yours, a bot\'s, someone else\'s: lands in a moderation queue. A human reads it, checks the AI\'s work, merges duplicates, edits if needed.',
			'note'  => '→ Yes, one person does this for now. Yes, it\'s slow on purpose.',
		),
		array(
			'num'   => '5',
			'title' => 'It goes live',
			'body'  => 'Once validated, the claim joins the database. It gets a counter that rises each time someone resubmits the same idea. It\'s linked to opposing views from other contributors, so readers see the full tension.',
			'note'  => '',
		),
		array(
			'num'   => '6',
			'title' => 'You can track it',
			'body'  => 'If you created an account, you\'ll see your submissions in your private profile: validated, merged, rejected or still pending. No public profiles, no leaderboards, no social clout.',
			'note'  => '',
		),
	);

	$open = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-how","layout":{"type":"constrained","contentSize":"860px"}} -->
<!-- wp:paragraph {"className":"is-style-wpis-eyebrow","style":{"spacing":{"margin":{"bottom":"1rem"}}}} -->
<p>Contribute</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">How a claim gets in.</h1>
<!-- /wp:heading -->

WPIS;

	$close = <<<'WPIS'

<!-- /wp:group -->

WPIS;

	$mid = '';
	foreach ( $steps as $s ) {
		$note_block = '';
		if ( '' !== $s['note'] ) {
			$note_block = '<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem"},"color":{"text":"var(--muted)"}}} -->
<p>' . esc_html( $s['note'] ) . '</p>
<!-- /wp:paragraph -->';
		}
		$body_p     = wp_kses_post( $s['body'] );
		$title_e    = esc_html( $s['title'] );
		$num_e      = esc_html( $s['num'] );
		$mid       .= <<<STEP
<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"flex-start"},"style":{"spacing":{"blockGap":"1rem","margin":{"bottom":"2rem"},"padding":{"bottom":"1.75rem"}},"border":{"bottom":{"color":"var(--ink)","width":"1px","style":"solid"}}}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--fraunces)","fontSize":"3rem","fontWeight":"400","lineHeight":"1"},"color":{"text":"var(--accent)"}}} -->
<p>{$num_e}</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.5rem"}}} -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.1875rem","fontWeight":"600","letterSpacing":"-0.01em","lineHeight":"1.25"}}} -->
<h3 class="wp-block-heading">{$title_e}</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.55"}}} -->
<p>{$body_p}</p>
<!-- /wp:paragraph -->
{$note_block}
<!-- /wp:group -->
<!-- /wp:group -->

STEP;
	}

	return $open . $mid . $close;
}

/**
 * Profile page rows.
 *
 * @return string
 */
function wpis_theme_build_profile_seed() {
	$items = array(
		array( 'WordPress <span class="is-word">is</span> a mess but I love it anyway.', 'Validated', 'April 14', 'background:var(--positive);color:var(--bg);' ),
		array( 'WordPress <span class="is-word">is</span> not a real developer tool.', 'Merged ×11', 'April 12', 'background:var(--muted);color:var(--bg);' ),
		array( 'WordPress <span class="is-word">est</span> ma plateforme de choix depuis 2012.', 'Pending', 'April 11', 'background:var(--mixed);color:var(--bg);' ),
		array( 'WordPress <span class="is-word">is</span> single-handedly why the web isn\'t just Facebook.', 'Validated', 'April 8', 'background:var(--positive);color:var(--bg);' ),
		array( 'WordPress <span class="is-word">is</span> dead.', 'Rejected', 'April 5', 'background:var(--negative);color:var(--bg);' ),
	);

	$open = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-profile","layout":{"type":"constrained","contentSize":"900px"}} -->
<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"1.75rem"},"padding":{"bottom":"1rem"}},"border":{"bottom":{"color":"var(--ink)","width":"1px","style":"solid"}}}} -->
<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"2rem","fontWeight":"800","letterSpacing":"-0.02em","lineHeight":"1.05"}}} -->
<h1 class="wp-block-heading">Your contributions</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p>Private · visible only to you · member since March 2026</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"grid","columnCount":2},"style":{"spacing":{"blockGap":"0.75rem","margin":{"bottom":"2.25rem"}}}} -->
<!-- wp:group {"style":{"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}},"color":{"background":"var(--paper)"}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.1em"},"color":{"text":"var(--muted)"}}} -->
<p>Total submitted</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"2.125rem","fontWeight":"600","letterSpacing":"-0.02em","lineHeight":"1"}}} -->
<p>42</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:group {"style":{"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}},"color":{"background":"var(--paper)"}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.1em"},"color":{"text":"var(--muted)"}}} -->
<p>Validated</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"2.125rem","fontWeight":"600","letterSpacing":"-0.02em","lineHeight":"1"}}} -->
<p>34</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:group {"style":{"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}},"color":{"background":"var(--paper)"}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.1em"},"color":{"text":"var(--muted)"}}} -->
<p>Acceptance rate</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"2.125rem","fontWeight":"600","letterSpacing":"-0.02em","lineHeight":"1"}}} -->
<p>81<span style="font-size:1rem;color:var(--muted);margin-left:2px">%</span></p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:group {"style":{"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}},"color":{"background":"var(--paper)"}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.1em"},"color":{"text":"var(--muted)"}}} -->
<p>Pending</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"2.125rem","fontWeight":"600","letterSpacing":"-0.02em","lineHeight":"1"}}} -->
<p>3</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:group -->

<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem","textTransform":"uppercase","letterSpacing":"0.1em"},"color":{"text":"var(--muted)"},"spacing":{"margin":{"bottom":"1rem"}}}} -->
<p>Your recent submissions</p>
<!-- /wp:paragraph -->

WPIS;

	$close = <<<'WPIS'

<!-- /wp:group -->

WPIS;

	$mid = '';
	foreach ( $items as $it ) {
		$text_raw    = wp_kses_post( $it[0] );
		$label       = esc_html( $it[1] );
		$date        = esc_html( $it[2] );
		$badge_style = $it[3];
		$mid        .= <<<ROW
<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"flex-start"},"style":{"spacing":{"blockGap":"0.625rem","padding":{"top":"1rem","bottom":"1rem"}},"border":{"bottom":{"color":"var(--ink)","width":"1px","style":"solid"}}}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem","lineHeight":"1.35"}}} -->
<p>{$text_raw}</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap","verticalAlignment":"center"},"style":{"spacing":{"blockGap":"0.625rem"}}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<p><span style="padding:4px 8px;display:inline-block;{$badge_style}">{$label}</span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem"},"color":{"text":"var(--muted)"}}} -->
<p>{$date}</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:group -->

ROW;
	}

	return $open . $mid . $close;
}

/**
 * Quote detail demo (sample child page).
 *
 * @return string
 */
function wpis_theme_build_sample_seed() {
	return <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-detail","layout":{"type":"constrained","contentSize":"900px"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.08em","lineHeight":"1.6"},"color":{"text":"var(--muted)"},"spacing":{"margin":{"bottom":"1.5rem"}}}} -->
<p><a href="/">Feed</a> <span style="margin:0 6px">/</span> <a href="/taxonomy/security/">Security</a> <span style="margin:0 6px">/</span> This quote</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"},"style":{"spacing":{"blockGap":"0.625rem","margin":{"bottom":"1.25rem"}}}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<p><span style="border:1px solid var(--ink);padding:3px 8px;font-weight:500">Security</span> <span style="color:var(--muted)">·</span> <span style="color:var(--muted)">Submitted 24 times since March 2026</span></p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"clamp(1.875rem, 9vw, 3.25rem)","fontWeight":"400","lineHeight":"1.12","letterSpacing":"-0.02em"},"spacing":{"margin":{"bottom":"1.25rem"}}}} -->
<h1 class="wp-block-heading">WordPress <span class="is-word">is</span> not secure: too many plugins with backdoors.</h1>
<!-- /wp:heading -->

<!-- wp:group {"style":{"color":{"background":"var(--paper)"},"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"1.5rem","bottom":"1.5rem","left":"1.25rem","right":"1.25rem"},"margin":{"top":"0","bottom":"2.5rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.15em"},"color":{"text":"var(--accent)"},"spacing":{"margin":{"bottom":"1rem"}}}} -->
<p><span aria-hidden="true">⇄</span> Someone disagrees</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.25rem","lineHeight":"1.3"}}} -->
<p>WordPress core has a stronger security track record than most CMSs. The problems people blame on "WordPress" are almost always plugin-level.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"},"style":{"spacing":{"blockGap":"0.625rem","margin":{"top":"0.875rem"}}}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p><span style="border:1px solid var(--ink);padding:3px 8px;font-size:10px">Security</span> <span style="background:var(--ink);color:var(--bg);padding:3px 8px">×12</span> Seen on Blog, Mastodon <a href="/taxonomy/security/" style="color:var(--accent);text-decoration:none;border-bottom:1px dotted var(--accent);margin-left:auto">See 3 more opposing views →</a></p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"2.5rem","bottom":"2rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem","textTransform":"uppercase","letterSpacing":"0.12em"},"color":{"text":"var(--muted)"},"spacing":{"margin":{"bottom":"1rem"}}}} -->
<p>How this claim spreads</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"isStackedOnMobile":true,"style":{"spacing":{"blockGap":{"top":"0.75rem","left":"0.75rem"}}}}} -->
<!-- wp:column -->
<!-- wp:group {"style":{"color":{"background":"var(--paper)"},"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0.875rem","bottom":"0.875rem","left":"0.875rem","right":"0.875rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.75rem","fontWeight":"600","lineHeight":"1"}}} -->
<p>24</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.08em"},"color":{"text":"var(--muted)"}}} -->
<p>total submissions</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:column -->

<!-- wp:column -->
<!-- wp:group {"style":{"color":{"background":"var(--paper)"},"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0.875rem","bottom":"0.875rem","left":"0.875rem","right":"0.875rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.75rem","fontWeight":"600","lineHeight":"1"}}} -->
<p>4</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.08em"},"color":{"text":"var(--muted)"}}} -->
<p>platforms</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:column -->

<!-- wp:column -->
<!-- wp:group {"style":{"color":{"background":"var(--paper)"},"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0.875rem","bottom":"0.875rem","left":"0.875rem","right":"0.875rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.75rem","fontWeight":"600","lineHeight":"1"}}} -->
<p>3</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.08em"},"color":{"text":"var(--muted)"}}} -->
<p>languages</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:column -->

<!-- wp:column -->
<!-- wp:group {"style":{"color":{"background":"var(--paper)"},"border":{"color":"var(--ink)","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0.875rem","bottom":"0.875rem","left":"0.875rem","right":"0.875rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.75rem","fontWeight":"600","lineHeight":"1"}}} -->
<p>11</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.08em"},"color":{"text":"var(--muted)"}}} -->
<p>variants merged</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:column -->
<!-- /wp:columns -->
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"2rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem","textTransform":"uppercase","letterSpacing":"0.12em"},"color":{"text":"var(--muted)"},"spacing":{"margin":{"bottom":"1rem"}}}} -->
<p>A few of the variants</p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"0.75rem","bottom":"0.75rem"}},"border":{"bottom":{"color":"var(--muted)","width":"1px","style":"dashed"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.4"}}} -->
<p>WordPress n'est pas sûr à cause de ses extensions</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p>FR · Mastodon · 2026</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"0.75rem","bottom":"0.75rem"}},"border":{"bottom":{"color":"var(--muted)","width":"1px","style":"dashed"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.4"}}} -->
<p>WordPress has a security problem with its plugin ecosystem</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p>EN · Reddit · 2026</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"0.75rem","bottom":"0.75rem"}},"border":{"bottom":{"color":"var(--muted)","width":"1px","style":"dashed"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.4"}}} -->
<p>WordPress ist unsicher wegen der Plugins</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p>DE · Mastodon · 2026</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"0.75rem","bottom":"0.75rem"}}},"layout":{"type":"default"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.4"}}} -->
<p>WP plugins are a security nightmare</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.5625rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"}}} -->
<p>EN · X · 2026</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:group -->

<!-- wp:group {"style":{"color":{"background":"var(--accent-soft)"},"border":{"left":{"color":"var(--accent)","width":"3px","style":"solid"}},"spacing":{"padding":{"top":"1.5rem","bottom":"1.5rem","left":"1.25rem","right":"1.25rem"},"margin":{"top":"2.5rem"}}}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.12em"},"color":{"text":"var(--accent)"},"spacing":{"margin":{"bottom":"0.75rem"}}}} -->
<p>A note from the editor</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9375rem","lineHeight":"1.55"}}} -->
<p>This claim surfaces often and has real basis: the WordPress plugin ecosystem does produce security incidents because anyone can publish plugins with varying code quality. But WordPress core itself is actively audited and security-patched. The distinction matters: criticizing "WordPress" for plugin vulnerabilities is like criticizing a browser for extension malware. <em style="font-style:italic;font-weight:500;color:var(--ink)">Both the criticism and the counter-argument hold: it depends on how you use it.</em></p>
<!-- /wp:paragraph -->
<!-- /wp:group -->
<!-- /wp:group -->

WPIS;
}

/**
 * About page.
 *
 * @return string
 */
function wpis_theme_build_about_seed() {
	return <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-prose","layout":{"type":"constrained","contentSize":"720px"}} -->
<!-- wp:paragraph {"className":"is-style-wpis-eyebrow"} -->
<p>About this project</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Why this site exists.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Every few days, someone online says something definitive about WordPress. That it's <em>outdated</em>. That it's <em>the best thing that ever happened to the web</em>. That it's <em>bloated</em>. That it's <em>liberating</em>. That it's <em>dying</em>. That it's <em>everywhere</em>.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>All of these statements are true. And all of them are incomplete.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"is-style-wpis-pull-quote"} -->
<p>"WordPress is bad" and "WordPress is good" are rarely the interesting sentences. What's interesting is what comes next.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">What we're doing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This site collects statements about WordPress: from blog posts, social media, forums, interviews, and puts them side by side. Not to win an argument, but to show that the argument itself has been going on for years and isn't going anywhere.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Each claim gets a counter-claim. Each criticism is paired with the position that disagrees with it. Each praise is paired with a critique. The point isn't to balance them mechanically: it's to make the conversation visible.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">What we're not doing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We're not defending WordPress. We're not attacking it either. We're not telling you what to think or which platform to use. If you decide WordPress isn't for you, that's fine. If you decide it's the only thing you'll ever use, that's fine too.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We just think the real conversation: the one that takes context into account: is more useful than the hot takes.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Who runs this</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A community effort started by Jaz and kept alive by people who think open-source deserves a more honest public record. Submissions are open. Moderation is one person's call. Code is open. Biases are documented.</p>
<!-- /wp:paragraph -->
<!-- /wp:group -->

WPIS;
}

/**
 * Empty / 404 style page.
 *
 * @return string
 */
function wpis_theme_build_empty_seed() {
	return <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-empty-state","layout":{"type":"constrained","contentSize":"560px"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--fraunces)","fontSize":"6rem","lineHeight":"1","fontStyle":"italic","fontWeight":"400"},"color":{"text":"var(--accent)"},"spacing":{"margin":{"bottom":"1.25rem"}}}} -->
<p>?</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">WordPress <span class="is-word">is</span>… not here.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"color":{"text":"var(--muted)"}}} -->
<p>Either this claim hasn't been submitted yet or the page you're looking for doesn't exist. Both are genuinely possible.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/explore/">Explore the database</a></div>
<!-- /wp:button -->
<!-- /wp:buttons -->
<!-- /wp:group -->

WPIS;
}

/**
 * Submit quote page (form stays in HTML).
 *
 * @return string
 */
function wpis_theme_build_submit_seed() {
	return <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-submit","layout":{"type":"constrained","contentSize":"720px"}} -->
<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"clamp(2rem, 9vw, 2.75rem)","fontWeight":"800","lineHeight":"1.05","letterSpacing":"-0.03em"},"spacing":{"margin":{"bottom":"0.75rem"}}}} -->
<h1 class="wp-block-heading">Seen a "WordPress <span class="is-word">is</span>…" quote somewhere?</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem","lineHeight":"1.5"},"color":{"text":"var(--muted)"},"spacing":{"margin":{"bottom":"1.75rem"}}}} -->
<p>Paste the text, drop a screenshot or share the link. We'll take care of the rest. No account required.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<form>
      <div class="form-group">
        <label>The quote <span class="required">*</span></label>
        <textarea placeholder="Paste the text here: exactly as it was written, in its original language."></textarea>
        <div class="hint">→ At least the text OR a screenshot is required.</div>
      </div>
      <div class="form-group">
        <label>Or upload a screenshot</label>
        <div class="upload-zone">↓ Drag &amp; drop here, or tap to upload</div>
        <div class="hint">→ We'll extract the text automatically. The image is deleted after validation.</div>
      </div>
      <div class="form-group">
        <label>Source URL (if possible)</label>
        <input type="url" placeholder="https://…">
        <div class="hint">→ Helps us detect the platform. Only the domain is stored, never the full URL.</div>
      </div>
      <div class="rgpd-notice">
        <strong>Privacy &amp; data</strong>
        Screenshots are deleted after text extraction. We never store personal identifiers (names, profile URLs or photos). Only the claim itself, the platform domain and the language are kept. Submissions are moderated before appearing on the site.
      </div>
      <button type="button" class="btn-primary" >Submit this quote</button>
      <span class="queue-indicator">47 submissions currently pending moderation</span>
    </form>
<!-- /wp:html -->
<!-- /wp:group -->

WPIS;
}

/**
 * Submitted confirmation page.
 *
 * @return string
 */
function wpis_theme_build_submitted_seed() {
	return <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-confirm","layout":{"type":"constrained","contentSize":"720px"}} -->
<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--fraunces)","fontSize":"5rem","lineHeight":"1","fontWeight":"400"},"color":{"text":"var(--accent)"},"spacing":{"margin":{"bottom":"1.5rem"}}}} -->
<p>✓</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"2rem","fontWeight":"800","letterSpacing":"-0.02em","lineHeight":"1.1"},"spacing":{"margin":{"bottom":"1rem"}}}} -->
<h1 class="wp-block-heading">Thanks. We've got it.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem","lineHeight":"1.5"},"color":{"text":"var(--muted)"},"spacing":{"margin":{"bottom":"1.75rem"}}}} -->
<p>Your submission is in the moderation queue. A human will review it: usually within a few days.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"background":"var(--paper)"},"border":{"color":"var(--ink)","width":"1px","style":"solid"},"typography":{"fontSize":"1.0625rem","lineHeight":"1.4","textAlign":"left"},"spacing":{"padding":{"top":"1.25rem","bottom":"1.25rem","left":"1.25rem","right":"1.25rem"},"margin":{"bottom":"2rem"}}}} -->
<p>"WordPress <span class="is-word">is</span> honestly the most democratic publishing platform we have."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.6875rem","textTransform":"uppercase","letterSpacing":"0.05em"},"color":{"text":"var(--muted)"},"border":{"top":{"color":"var(--muted)","width":"1px","style":"dashed"},"bottom":{"color":"var(--muted)","width":"1px","style":"dashed"}},"spacing":{"padding":{"top":"0.875rem","bottom":"0.875rem"},"margin":{"bottom":"1.75rem"}}}} -->
<p><strong style="font-family:var(--wp--preset--font-family--fraunces);color:var(--ink);font-size:1rem;letter-spacing:normal;font-weight:600">47 submissions</strong> currently pending moderation</p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"color":{"background":"var(--accent-soft)"},"border":{"left":{"color":"var(--accent)","width":"3px","style":"solid"}},"spacing":{"padding":{"top":"1.25rem","bottom":"1.25rem","left":"1.25rem","right":"1.25rem"},"margin":{"bottom":"1.75rem"}},"typography":{"textAlign":"left"}}} -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--jetbrains-mono)","fontSize":"0.625rem","textTransform":"uppercase","letterSpacing":"0.1em"},"color":{"text":"var(--accent)"},"spacing":{"margin":{"bottom":"0.625rem"}}}} -->
<h3 class="wp-block-heading">Want to follow this?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem","lineHeight":"1.5"},"spacing":{"margin":{"bottom":"0.75rem"}}}} -->
<p>Create a free account to see the status of your submission (validated, merged, rejected) and keep track of your contributions. Your profile stays private: only you can see it.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"},"style":{"typography":{"textAlign":"left"}}} -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/profile/">Create an account</a></div>
<!-- /wp:button -->
<!-- /wp:buttons -->
<!-- /wp:group -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"0.625rem"}}} -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/submit/">Submit another</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/">Back to the feed</a></div>
<!-- /wp:button -->
<!-- /wp:buttons -->
<!-- /wp:group -->

WPIS;
}

/**
 * Dispatch seed builder by page slug (matches theme manifest slug).
 *
 * @param string $slug Page slug.
 * @return string|null Full block markup or null to fall back to file.
 */
function wpis_theme_seed_content_for_slug( $slug ) {
	switch ( $slug ) {
		case 'home':
			return wpis_theme_build_home_seed();
		case 'security':
			return wpis_theme_build_security_seed();
		case 'explore':
			return wpis_theme_build_explore_seed();
		case 'search-demo':
			return wpis_theme_build_search_demo_seed();
		case 'how-it-works':
			return wpis_theme_build_how_seed();
		case 'profile':
			return wpis_theme_build_profile_seed();
		case 'sample':
			return wpis_theme_build_sample_seed();
		case 'about':
			return wpis_theme_build_about_seed();
		case 'submit':
			return wpis_theme_build_submit_seed();
		case 'submitted':
			return wpis_theme_build_submitted_seed();
		default:
			return null;
	}
}
