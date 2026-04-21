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
 * Sentiment → quote-card modifier class (matches mockup).
 *
 * @param string $sentiment negative|positive|mixed.
 * @return string
 */
function wpis_theme_seed_quote_card_class( $sentiment ) {
	switch ( $sentiment ) {
		case 'positive':
			return 'sent-positive';
		case 'mixed':
			return 'sent-mixed';
		default:
			return 'sent-negative';
	}
}

/**
 * One feed row: single clickable anchor like the mockup (whole card is the link).
 *
 * @param array<string,string> $q Keys: sentiment, claim, count, text, url, optional platforms, date (Y-m-d for client sort/filter).
 * @return string
 */
function wpis_theme_seed_quote_stack( array $q ) {
	$sent  = isset( $q['sentiment'] ) ? $q['sentiment'] : 'negative';
	$class = wpis_theme_seed_quote_card_class( $sent );
	$claim = isset( $q['claim'] ) ? esc_html( $q['claim'] ) : '';
	$count = isset( $q['count'] ) ? esc_html( $q['count'] ) : '';
	$url   = isset( $q['url'] ) ? esc_url( $q['url'] ) : '/quote/sample/';
	$text  = isset( $q['text'] ) ? wp_kses_post( $q['text'] ) : '';
	$label = wp_strip_all_tags( $text );
	if ( strlen( $label ) > 140 ) {
		$label = substr( $label, 0, 137 ) . '…';
	}
	$aria            = esc_attr( sprintf( __( 'View quote: %s', 'wpis-theme' ), $label ) );
	$platforms       = isset( $q['platforms'] ) ? esc_attr( $q['platforms'] ) : '';
	$date            = isset( $q['date'] ) ? esc_attr( $q['date'] ) : '';
	$count_str       = isset( $q['count'] ) ? $q['count'] : '';
	$count_num_match = array();
	preg_match( '/\d+/', (string) $count_str, $count_num_match );
	$count_num = ! empty( $count_num_match[0] ) ? $count_num_match[0] : '0';
	$data_attr = ' data-sentiment="' . esc_attr( $sent ) . '" data-claim="' . esc_attr( isset( $q['claim'] ) ? $q['claim'] : '' ) . '" data-platforms="' . $platforms . '" data-date="' . $date . '" data-count="' . esc_attr( $count_num ) . '"';

	return '
<!-- wp:html -->
<a href="' . $url . '" class="quote-card ' . esc_attr( $class ) . '"' . $data_attr . ' aria-label="' . $aria . '">
<span class="quote-text">' . $text . '</span>
<span class="quote-footer">
<span class="claim-tag">' . $claim . '</span>
<span class="count-badge">' . $count . '</span>
</span>
</a>
<!-- /wp:html -->';
}

/**
 * @return list<array<string,string>>
 */
function wpis_theme_seed_home_quotes() {
	return array(
		array(
			'sentiment'  => 'negative',
			'claim'      => 'Performance',
			'count'      => '×8',
			'platforms'  => 'Mastodon,LinkedIn,Reddit',
			'date'       => '2026-04-20',
			'text'       => 'WordPress <span class="is-word">is</span> bloated and slow on shared hosting.',
		),
		array(
			'sentiment'  => 'positive',
			'claim'      => 'Community',
			'count'      => '×3',
			'platforms'  => 'Bluesky,LinkedIn',
			'date'       => '2026-04-19',
			'text'       => 'WordPress <span class="is-word">is</span> the reason I still have a career in the web.',
		),
		array(
			'sentiment'  => 'negative',
			'claim'      => 'Security',
			'count'      => '×24',
			'platforms'  => 'Reddit,YouTube,X,Mastodon',
			'date'       => '2026-04-18',
			'text'       => 'WordPress <span class="is-word">is</span> not secure: too many plugins with backdoors.',
		),
		array(
			'sentiment'  => 'negative',
			'claim'      => 'Modernity',
			'count'      => '×17',
			'platforms'  => 'X,YouTube,HN',
			'date'       => '2026-04-17',
			'text'       => 'WordPress <span class="is-word">is</span> just for blogs, you can\'t build a real app with it.',
		),
		array(
			'sentiment'  => 'positive',
			'claim'      => 'Community',
			'count'      => '×6',
			'platforms'  => 'Mastodon,Blog',
			'date'       => '2026-04-16',
			'text'       => 'WordPress <span class="is-word">is</span> more than software. It\'s two decades of people who believed in an open web.',
		),
		array(
			'sentiment'  => 'mixed',
			'claim'      => 'Business viability',
			'count'      => '×11',
			'platforms'  => 'LinkedIn,HN,YouTube',
			'date'       => '2026-04-15',
			'text'       => 'WordPress <span class="is-word">is</span> fine for small sites but don\'t scale it.',
		),
		array(
			'sentiment'  => 'negative',
			'claim'      => 'Ecosystem',
			'count'      => '×14',
			'platforms'  => 'Reddit,X',
			'date'       => '2026-04-14',
			'text'       => 'WordPress <span class="is-word">is</span> a plugin graveyard. Half of them are abandoned.',
		),
		array(
			'sentiment'  => 'positive',
			'claim'      => 'Ease of use',
			'count'      => '×9',
			'platforms'  => 'Blog,LinkedIn,Mastodon',
			'date'       => '2026-04-13',
			'text'       => 'WordPress <span class="is-word">is</span> the only CMS my non-technical clients can actually maintain themselves.',
		),
		array(
			'sentiment'  => 'mixed',
			'claim'      => 'Modernity',
			'count'      => '×21',
			'platforms'  => 'HN,X,Reddit',
			'date'       => '2026-04-12',
			'text'       => 'Gutenberg <span class="is-word">is</span> both the best and worst thing that happened to WordPress.',
		),
		array(
			'sentiment'  => 'negative',
			'claim'      => 'Accessibility',
			'count'      => '×5',
			'platforms'  => 'Blog,Mastodon',
			'date'       => '2026-04-11',
			'text'       => 'WordPress core <span class="is-word">is</span> still shipping accessibility bugs that were reported years ago.',
		),
		array(
			'sentiment'  => 'positive',
			'claim'      => 'Community',
			'count'      => '×13',
			'platforms'  => 'Mastodon,Bluesky,LinkedIn',
			'date'       => '2026-04-10',
			'text'       => 'Nothing beats the feeling of a WordCamp. The energy, the people, the openness.',
		),
		array(
			'sentiment'  => 'negative',
			'claim'      => 'Performance',
			'count'      => '×7',
			'platforms'  => 'HN,X',
			'date'       => '2026-04-09',
			'text'       => 'WordPress <span class="is-word">is</span> what you get when PHP from 2003 meets a marketing team.',
		),
		array(
			'sentiment'  => 'mixed',
			'claim'      => 'Business viability',
			'count'      => '×4',
			'platforms'  => 'LinkedIn,Blog',
			'date'       => '2026-04-08',
			'text'       => 'WordPress <span class="is-word">is</span> a great starting point but you\'ll outgrow it faster than you think.',
		),
		array(
			'sentiment'  => 'positive',
			'claim'      => 'Ecosystem',
			'count'      => '×8',
			'platforms'  => 'Reddit,Mastodon',
			'date'       => '2026-04-07',
			'text'       => 'The WordPress plugin ecosystem <span class="is-word">is</span> unmatched. Whatever you need, someone already built it.',
		),
		array(
			'sentiment'  => 'negative',
			'claim'      => 'Security',
			'count'      => '×6',
			'platforms'  => 'X,YouTube',
			'date'       => '2026-04-06',
			'text'       => 'Every other week there\'s another WordPress plugin 0-day. At some point it\'s the platform\'s fault.',
		),
		array(
			'sentiment'  => 'positive',
			'claim'      => 'Modernity',
			'count'      => '×10',
			'platforms'  => 'Blog,Mastodon,LinkedIn',
			'date'       => '2026-04-05',
			'text'       => 'Headless WordPress <span class="is-word">is</span> quietly powering more serious apps than people realize.',
		),
		array(
			'sentiment'  => 'mixed',
			'claim'      => 'Ease of use',
			'count'      => '×5',
			'platforms'  => 'Reddit,HN',
			'date'       => '2026-04-04',
			'text'       => 'WordPress <span class="is-word">is</span> easy until you need it to do something specific. Then it\'s a nightmare.',
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
<!-- wp:group {"className":"is-style-wpis-hero","layout":{"type":"constrained","contentSize":"1320px"}} -->
<!-- wp:paragraph {"className":"is-style-wpis-eyebrow"} -->
<p>A database of claims</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"hero-title"} -->
<h1 class="wp-block-heading hero-title">WordPress <span class="is-word">is</span><span class="dots">…</span></h1>
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
<!-- wp:group {"className":"is-style-wpis-feed","layout":{"type":"constrained","contentSize":"1320px"}} -->
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

<!-- wp:group {"anchor":"feedlist","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0"}}} -->

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
<!-- wp:group {"className":"is-style-wpis-tax-hero","layout":{"type":"constrained","contentSize":"1320px"}} -->
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
<!-- wp:group {"className":"is-style-wpis-feed","layout":{"type":"constrained","contentSize":"1320px"}} -->
<!-- wp:html -->
    <div class="feed-header">
      <div class="feed-title">287 quotes in this category</div>
      <div class="feed-sort" id="feedSort">
        <button type="button" class="active" data-sort="repeated">Most repeated</button>
        <button type="button" data-sort="recent">Recent</button>
        <button type="button" data-sort="random">Random</button>
      </div>
    </div>

<!-- /wp:html -->

<!-- wp:group {"anchor":"feedlist","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0"}}} -->

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
			'platforms' => 'Reddit,Mastodon',
			'date'      => '2026-04-18',
			'text'      => 'WordPress <span class="is-word">is</span> not secure: too many plugins with backdoors.',
		),
		array(
			'sentiment' => 'positive',
			'claim'     => 'Security',
			'count'     => '×12',
			'platforms' => 'Blog,LinkedIn',
			'date'      => '2026-04-17',
			'text'      => 'WordPress <span class="is-word">is</span> as secure as you configure it to be: that\'s the point of open source.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Security',
			'count'     => '×18',
			'platforms' => 'X,Reddit',
			'date'      => '2026-04-16',
			'text'      => 'WordPress <span class="is-word">is</span> the most hacked CMS on the planet.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Security',
			'count'     => '×7',
			'platforms' => 'Mastodon,HN',
			'date'      => '2026-04-15',
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
			'platforms' => 'Mastodon,Reddit',
			'date'      => '2026-04-20',
			'text'      => 'WordPress <span class="is-word">is</span> <mark>bloated</mark> and slow on shared hosting.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Performance',
			'count'     => '×5',
			'platforms' => 'LinkedIn,X',
			'date'      => '2026-04-19',
			'text'      => 'Every <mark>bloated</mark> WordPress site I\'ve inherited had 40+ plugins. That\'s the real problem.',
		),
		array(
			'sentiment' => 'mixed',
			'claim'     => 'Performance',
			'count'     => '×4',
			'platforms' => 'HN,Blog',
			'date'      => '2026-04-18',
			'text'      => 'WordPress <span class="is-word">is</span> <mark>bloated</mark> only if you treat it like an app framework.',
		),
		array(
			'sentiment' => 'negative',
			'claim'     => 'Modernity',
			'count'     => '×11',
			'platforms' => 'YouTube,Reddit',
			'date'      => '2026-04-17',
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
 * Inner HTML for explore "By claim type" grid (mockup tax-card markup).
 *
 * @return string
 */
function wpis_theme_explore_tax_cards_inner_html() {
	$cards = array(
		array(
			'slug'  => 'security',
			'title' => 'Security',
			'count' => '287 quotes',
			'desc'  => 'How safe, exposed or resilient WordPress is. Mostly about the plugin ecosystem, not core.',
			'bar'   => array( 'neg' => 67, 'pos' => 25, 'mix' => 8 ),
			'rows'  => array(
				array( 'neg', '192 critical' ),
				array( 'pos', '71 supportive' ),
				array( 'mix', '24 mixed' ),
			),
		),
		array(
			'slug'  => 'performance',
			'title' => 'Performance',
			'count' => '423 quotes',
			'desc'  => 'Speed, weight, responsiveness. Often about hosting setup as much as WordPress itself.',
			'bar'   => array( 'neg' => 72, 'pos' => 18, 'mix' => 10 ),
			'rows'  => array(
				array( 'neg', '305 critical' ),
				array( 'pos', '76 supportive' ),
				array( 'mix', '42 mixed' ),
			),
		),
		array(
			'slug'  => 'community',
			'title' => 'Community',
			'count' => '512 quotes',
			'desc'  => 'The people, the WordCamps, the ecosystem of contributors. Often the most emotional category.',
			'bar'   => array( 'pos' => 78, 'neg' => 14, 'mix' => 8 ),
			'rows'  => array(
				array( 'pos', '399 supportive' ),
				array( 'neg', '72 critical' ),
				array( 'mix', '41 mixed' ),
			),
		),
		array(
			'slug'  => 'modernity',
			'title' => 'Modernity',
			'count' => '503 quotes',
			'desc'  => 'Is WordPress keeping up? Gutenberg, block editor, headless use: the most contested territory.',
			'bar'   => array( 'neg' => 48, 'pos' => 32, 'mix' => 20 ),
			'rows'  => array(
				array( 'neg', '241 critical' ),
				array( 'pos', '161 supportive' ),
				array( 'mix', '101 mixed' ),
			),
		),
		array(
			'slug'  => 'ecosystem',
			'title' => 'Ecosystem',
			'count' => '398 quotes',
			'desc'  => 'Plugins, themes, integrations. The breadth vs quality debate lives here.',
			'bar'   => array( 'pos' => 45, 'neg' => 40, 'mix' => 15 ),
			'rows'  => array(
				array( 'pos', '179 supportive' ),
				array( 'neg', '159 critical' ),
				array( 'mix', '60 mixed' ),
			),
		),
		array(
			'slug'  => 'ease-of-use',
			'title' => 'Ease of use',
			'count' => '341 quotes',
			'desc'  => 'Who is WordPress for? The tension between accessibility for beginners and power for devs.',
			'bar'   => array( 'pos' => 52, 'neg' => 30, 'mix' => 18 ),
			'rows'  => array(
				array( 'pos', '177 supportive' ),
				array( 'neg', '102 critical' ),
				array( 'mix', '62 mixed' ),
			),
		),
		array(
			'slug'  => 'business-viability',
			'title' => 'Business viability',
			'count' => '214 quotes',
			'desc'  => 'Can you build a real business on WordPress? Scale, pricing, long-term bets.',
			'bar'   => array( 'mix' => 42, 'pos' => 32, 'neg' => 26 ),
			'rows'  => array(
				array( 'mix', '90 mixed' ),
				array( 'pos', '68 supportive' ),
				array( 'neg', '56 critical' ),
			),
		),
		array(
			'slug'  => 'accessibility',
			'title' => 'Accessibility',
			'count' => '89 quotes',
			'desc'  => 'Smaller category, big stakes. How WordPress handles disability, a11y standards and inclusion.',
			'bar'   => array( 'neg' => 55, 'pos' => 30, 'mix' => 15 ),
			'rows'  => array(
				array( 'neg', '49 critical' ),
				array( 'pos', '27 supportive' ),
				array( 'mix', '13 mixed' ),
			),
		),
	);

	$html = '';
	foreach ( $cards as $c ) {
		$href = esc_url( '/taxonomy/' . $c['slug'] . '/' );
		$html .= '<a href="' . $href . '" class="tax-card">';
		$html .= '<div class="tax-card-head"><h3>' . esc_html( $c['title'] ) . '</h3><span class="tax-count">' . esc_html( $c['count'] ) . '</span></div>';
		$html .= '<p class="tax-desc">' . esc_html( $c['desc'] ) . '</p>';
		$html .= '<div class="tax-bar">';
		foreach ( $c['bar'] as $seg => $pct ) {
			$w = (int) $pct;
			$html .= '<div class="tax-bar-seg ' . esc_attr( $seg ) . '" style="width:' . $w . '%;"></div>';
		}
		$html .= '</div><div class="tax-breakdown">';
		foreach ( $c['rows'] as $row ) {
			$html .= '<span><span class="dot ' . esc_attr( $row[0] ) . '"></span>' . esc_html( $row[1] ) . '</span>';
		}
		$html .= '</div></a>';
	}

	return $html;
}

/**
 * Inner HTML for explore platform grid.
 *
 * @return string
 */
function wpis_theme_explore_platform_grid_inner_html() {
	$items = array(
		array( 'Mastodon', '841' ),
		array( 'LinkedIn', '623' ),
		array( 'Reddit', '412' ),
		array( 'Bluesky', '287' ),
		array( 'X', '284' ),
		array( 'Blog', '202' ),
		array( 'YouTube', '198' ),
		array( 'HN', '87' ),
	);
	$html  = '';
	$href  = esc_url( '/' );
	foreach ( $items as $it ) {
		$html .= '<a href="' . $href . '" class="platform-card"><h4>' . esc_html( $it[0] ) . '</h4><span class="p-count">' . esc_html( $it[1] ) . '</span></a>';
	}
	return $html;
}

/**
 * Explore page: hero + mockup-style tax cards + platform grid.
 *
 * @return string
 */
function wpis_theme_build_explore_seed() {
	$hero = <<<'WPIS'
<!-- wp:group {"className":"is-style-wpis-explore-hero","layout":{"type":"constrained","contentSize":"1320px"}} -->
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

	$tax_inner = wpis_theme_explore_tax_cards_inner_html();
	$plat_inner = wpis_theme_explore_platform_grid_inner_html();

	$section_tax = <<<WPIS
<!-- wp:group {"className":"is-style-wpis-explore-section","layout":{"type":"constrained","contentSize":"1320px"}} -->
<!-- wp:paragraph {"className":"explore-section-title"} -->
<p>By claim type</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="tax-grid">
{$tax_inner}
</div>
<!-- /wp:html -->
<!-- /wp:group -->

WPIS;

	$section_plat = <<<WPIS
<!-- wp:group {"className":"is-style-wpis-explore-section","layout":{"type":"constrained","contentSize":"1320px"}} -->
<!-- wp:paragraph {"className":"explore-section-title"} -->
<p>By source platform</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="platform-grid">
{$plat_inner}
</div>
<!-- /wp:html -->
<!-- /wp:group -->

WPIS;

	return $hero . $section_tax . $section_plat;
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
			'body'  => 'Someone online says something about WordPress. A tweet, a blog post, a Reddit thread, a video comment. You think it <em>belongs here</em>.',
			'note'  => '→ Or one of our bots finds it on Mastodon or Bluesky automatically.',
		),
		array(
			'num'   => '2',
			'title' => 'You submit it',
			'body'  => 'Paste the text, drop a screenshot or share the link. No account needed. We extract what\'s useful: the claim itself, the platform, the language, and strip out <em>personal details</em>.',
			'note'  => '→ Screenshots are deleted after extraction. Only the domain is stored, never the full URL.',
		),
		array(
			'num'   => '3',
			'title' => 'AI pre-tags it',
			'body'  => 'An AI suggests a sentiment, a claim type and a translation. It also looks for similar claims already in the database, to avoid duplicates. None of this is final: <em>it\'s a starting point</em>.',
			'note'  => '',
		),
		array(
			'num'   => '4',
			'title' => 'A human validates',
			'body'  => 'Every submission: yours, a bot\'s, someone else\'s: lands in a moderation queue. A human reads it, checks the AI\'s work, merges <em>duplicates</em>, edits if needed.',
			'note'  => '→ Yes, one person does this for now. Yes, it\'s slow on purpose.',
		),
		array(
			'num'   => '5',
			'title' => 'It goes live',
			'body'  => 'Once validated, the claim joins the database. It gets a counter that rises each time someone resubmits the same idea. It\'s linked to opposing views from other contributors, so readers see <em>the full tension</em>.',
			'note'  => '',
		),
		array(
			'num'   => '6',
			'title' => 'You can track it',
			'body'  => 'If you created an account, you\'ll see your submissions in your <em>private profile</em>: validated, merged, rejected or still pending. No public profiles, no leaderboards, no social clout.',
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

	$html_steps = '';
	foreach ( $steps as $s ) {
		$num       = esc_html( $s['num'] );
		$title     = esc_html( $s['title'] );
		$body      = wp_kses_post( $s['body'] );
		$note_html = '';
		if ( isset( $s['note'] ) && '' !== $s['note'] ) {
			$note_html = '<p class="note">' . esc_html( $s['note'] ) . '</p>';
		}
		$html_steps .= '<div class="how-step"><div class="num">' . $num . '</div><div class="body"><h3>' . $title . '</h3><p>' . $body . '</p>' . $note_html . '</div></div>';
	}

	return $open . '<!-- wp:html --><div class="how-step-list">' . $html_steps . '</div><!-- /wp:html -->' . $close;
}

/**
 * Profile page rows.
 *
 * @return string
 */
function wpis_theme_build_profile_seed() {
	$items = array(
		array(
			'text'   => 'WordPress <span class="is-word">is</span> a mess but I love it anyway.',
			'status' => 'validated',
			'label'  => 'Validated',
			'date'   => 'April 14',
		),
		array(
			'text'   => 'WordPress <span class="is-word">is</span> not a real developer tool.',
			'status' => 'merged',
			'label'  => 'Merged ×11',
			'date'   => 'April 12',
		),
		array(
			'text'   => 'WordPress <span class="is-word">est</span> ma plateforme de choix depuis 2012.',
			'status' => 'pending',
			'label'  => 'Pending',
			'date'   => 'April 11',
		),
		array(
			'text'   => 'WordPress <span class="is-word">is</span> single-handedly why the web isn\'t just Facebook.',
			'status' => 'validated',
			'label'  => 'Validated',
			'date'   => 'April 8',
		),
		array(
			'text'   => 'WordPress <span class="is-word">is</span> dead.',
			'status' => 'rejected',
			'label'  => 'Rejected',
			'date'   => 'April 5',
		),
	);

	$rows = '';
	foreach ( $items as $it ) {
		$text   = wp_kses_post( $it['text'] );
		$label  = esc_html( $it['label'] );
		$date   = esc_html( $it['date'] );
		$status = esc_attr( $it['status'] );
		$rows  .= '<div class="sub-item"><div class="sub-text">' . $text . '</div><div class="sub-meta-line"><span class="status-badge status-' . $status . '">' . $label . '</span><span class="sub-date">' . $date . '</span></div></div>';
	}

	return <<<WPIS
<!-- wp:group {"className":"is-style-wpis-profile","layout":{"type":"constrained","contentSize":"900px"}} -->
<!-- wp:html -->
<div class="wpis-profile">
<div class="profile-header">
<h1>Your contributions</h1>
<p>Private · visible only to you · member since March 2026</p>
</div>
<div class="stats-grid">
<div class="stat-card"><div class="label">Total submitted</div><div class="value">42</div></div>
<div class="stat-card"><div class="label">Validated</div><div class="value">34</div></div>
<div class="stat-card"><div class="label">Acceptance rate</div><div class="value">81<span class="suffix">%</span></div></div>
<div class="stat-card"><div class="label">Pending</div><div class="value">3</div></div>
</div>
<div class="submission-list">
<h2>Your recent submissions</h2>
{$rows}
</div>
</div>
<!-- /wp:html -->
<!-- /wp:group -->

WPIS;
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
