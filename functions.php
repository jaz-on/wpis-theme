<?php
/**
 * WPIS block theme bootstrap.
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve permalink for a published page by slug (Polylang-aware).
 *
 * @param string      $slug          Page slug (e.g. submit).
 * @param string|null $lang_override Polylang language slug, or null for current language.
 * @return string|null
 */
function wpis_theme_resolve_page_permalink( string $slug, ?string $lang_override = null ): ?string {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( ! $page instanceof WP_Post ) {
		return null;
	}
	$lang = $lang_override;
	if ( null === $lang || '' === $lang ) {
		if ( function_exists( 'pll_current_language' ) ) {
			$cur  = pll_current_language();
			$lang = is_string( $cur ) ? $cur : '';
		} else {
			$lang = '';
		}
	}
	if ( function_exists( 'pll_get_post' ) && '' !== $lang ) {
		$translated = pll_get_post( (int) $page->ID, $lang );
		if ( $translated ) {
			$link = get_permalink( (int) $translated );
			return is_string( $link ) ? $link : null;
		}
	}
	$link = get_permalink( $page );
	return is_string( $link ) ? $link : null;
}

/**
 * Front URL for a page slug (falls back to home_url if the page is missing).
 *
 * @param string $slug Page slug.
 * @return string
 */
function wpis_theme_page_url( string $slug ): string {
	$resolved = wpis_theme_resolve_page_permalink( $slug, null );
	if ( null !== $resolved ) {
		return $resolved;
	}
	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Allowed _wpis_source_platform values (mirrors wpis-plugin).
 *
 * @return string[]
 */
function wpis_theme_source_platform_slugs(): array {
	return array( 'mastodon', 'bluesky', 'linkedin', 'youtube', 'reddit', 'blog', 'x', 'hn', 'other' );
}

/**
 * Human labels for source platforms (feed filter UI).
 *
 * @return array<string, string>
 */
function wpis_theme_platform_labels(): array {
	return array(
		'mastodon'  => 'Mastodon',
		'bluesky'   => 'Bluesky',
		'linkedin'  => 'LinkedIn',
		'youtube'   => 'YouTube',
		'reddit'    => 'Reddit',
		'blog'      => 'Blog',
		'x'         => 'X',
		'hn'        => 'HN',
		'other'     => __( 'Other', 'wpis-theme' ),
	);
}

/**
 * Canonical URL for the main quote feed (posts page, else quote archive, else home).
 *
 * @return string
 */
function wpis_theme_feed_url(): string {
	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page > 0 ) {
		$link = get_permalink( $posts_page );
		if ( is_string( $link ) ) {
			return $link;
		}
	}
	$arch = get_post_type_archive_link( 'quote' );
	return is_string( $arch ) && '' !== $arch ? $arch : home_url( '/' );
}

/**
 * Primary sentiment slug for a quote (for card border class).
 *
 * @param int $post_id Post ID.
 * @return string positive|negative|neutral|mixed
 */
function wpis_theme_get_quote_sentiment_slug( int $post_id ): string {
	$terms = get_the_terms( $post_id, 'sentiment' );
	if ( ! is_array( $terms ) || array() === $terms ) {
		return 'neutral';
	}
	$term = array_shift( $terms );
	if ( ! $term instanceof WP_Term ) {
		return 'neutral';
	}
	$slug = $term->slug;
	return in_array( $slug, array( 'positive', 'negative', 'neutral', 'mixed' ), true ) ? $slug : 'neutral';
}

/**
 * Add sentiment and card classes to quote posts in loops.
 *
 * @param string[] $classes Classes.
 * @param string[] $class   Extra class names.
 * @param int      $post_id Post ID.
 * @return string[]
 */
function wpis_theme_quote_post_class( array $classes, array $class, int $post_id ): array {
	unset( $class );
	if ( 'quote' !== get_post_type( $post_id ) ) {
		return $classes;
	}
	$classes[] = 'wpis-quote-card';
	$classes[] = 'wpis-sentiment-' . wpis_theme_get_quote_sentiment_slug( $post_id );
	return $classes;
}
add_filter( 'post_class', 'wpis_theme_quote_post_class', 10, 3 );

/**
 * Primary navigation (mockup IA: Feed, Explore, About, How it works, Submit, My profile).
 *
 * @return string
 */
function wpis_theme_primary_nav_shortcode(): string {
	$feed   = wpis_theme_feed_url();
	$expl   = wpis_theme_page_url( 'explore' );
	$about  = wpis_theme_page_url( 'about' );
	$how    = wpis_theme_page_url( 'how-it-works' );
	$submit = wpis_theme_page_url( 'submit' );
	$prof   = wpis_theme_page_url( 'profile' );

	$feed_a = ( is_home() || is_post_type_archive( 'quote' ) || is_singular( 'quote' ) || is_tax( array( 'claim_type', 'sentiment' ) ) || is_search() ) ? ' is-active' : '';
	$exp_a  = is_page( 'explore' ) ? ' is-active' : '';
	$ab_a   = is_page( 'about' ) ? ' is-active' : '';
	$how_a  = is_page( 'how-it-works' ) ? ' is-active' : '';
	$sub_a  = is_page( 'submit' ) ? ' is-active' : '';
	$pr_a   = wpis_theme_is_profile_page() ? ' is-active' : '';

	ob_start();
	?>
	<nav class="wpis-primary-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'wpis-theme' ); ?>">
		<a class="wpis-primary-nav__link<?php echo esc_attr( $feed_a ); ?>" href="<?php echo esc_url( $feed ); ?>"><?php esc_html_e( 'Feed', 'wpis-theme' ); ?></a>
		<a class="wpis-primary-nav__link<?php echo esc_attr( $exp_a ); ?>" href="<?php echo esc_url( $expl ); ?>"><?php esc_html_e( 'Explore', 'wpis-theme' ); ?></a>
		<a class="wpis-primary-nav__link<?php echo esc_attr( $ab_a ); ?>" href="<?php echo esc_url( $about ); ?>"><?php esc_html_e( 'About', 'wpis-theme' ); ?></a>
		<a class="wpis-primary-nav__link<?php echo esc_attr( $how_a ); ?>" href="<?php echo esc_url( $how ); ?>"><?php esc_html_e( 'How it works', 'wpis-theme' ); ?></a>
		<a class="wpis-primary-nav__link<?php echo esc_attr( $sub_a ); ?>" href="<?php echo esc_url( $submit ); ?>"><?php esc_html_e( 'Submit', 'wpis-theme' ); ?></a>
		<a class="wpis-primary-nav__link<?php echo esc_attr( $pr_a ); ?>" href="<?php echo esc_url( $prof ); ?>"><?php esc_html_e( 'My profile', 'wpis-theme' ); ?></a>
	</nav>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_primary_nav', 'wpis_theme_primary_nav_shortcode' );

/**
 * Sticky sentiment strip on feed views (mockup dark toolbar).
 *
 * @return string
 */
function wpis_theme_sticky_feed_filters_shortcode(): string {
	if ( is_admin() ) {
		return '';
	}
	if ( ! is_home() && ! is_post_type_archive( 'quote' ) ) {
		return '';
	}
	$base      = wpis_theme_feed_url();
	$cur_sent  = isset( $_GET['sentiment'] ) ? sanitize_title( wp_unslash( $_GET['sentiment'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$feed_all  = remove_query_arg( array( 'sentiment', 'claim_type', 'platform', 'paged' ), $base );
	$neg       = add_query_arg( array( 'sentiment' => 'negative' ), $base );
	$pos       = add_query_arg( array( 'sentiment' => 'positive' ), $base );
	$mix       = add_query_arg( array( 'sentiment' => 'mixed' ), $base );
	$neu       = add_query_arg( array( 'sentiment' => 'neutral' ), $base );
	$explore   = wpis_theme_page_url( 'explore' );

	ob_start();
	?>
	<div class="wpis-sticky-feed-bar" role="region" aria-label="<?php esc_attr_e( 'Filter quotes by sentiment', 'wpis-theme' ); ?>">
		<span class="wpis-sticky-feed-bar__label"><?php esc_html_e( 'Sentiment', 'wpis-theme' ); ?></span>
		<a class="wpis-sticky-feed-bar__btn<?php echo '' === $cur_sent ? ' is-active' : ''; ?>" href="<?php echo esc_url( $feed_all ); ?>"><?php esc_html_e( 'All', 'wpis-theme' ); ?></a>
		<a class="wpis-sticky-feed-bar__btn<?php echo 'negative' === $cur_sent ? ' is-active' : ''; ?>" href="<?php echo esc_url( $neg ); ?>"><?php esc_html_e( 'Negative', 'wpis-theme' ); ?></a>
		<a class="wpis-sticky-feed-bar__btn<?php echo 'positive' === $cur_sent ? ' is-active' : ''; ?>" href="<?php echo esc_url( $pos ); ?>"><?php esc_html_e( 'Positive', 'wpis-theme' ); ?></a>
		<a class="wpis-sticky-feed-bar__btn<?php echo 'mixed' === $cur_sent ? ' is-active' : ''; ?>" href="<?php echo esc_url( $mix ); ?>"><?php esc_html_e( 'Mixed', 'wpis-theme' ); ?></a>
		<a class="wpis-sticky-feed-bar__btn<?php echo 'neutral' === $cur_sent ? ' is-active' : ''; ?>" href="<?php echo esc_url( $neu ); ?>"><?php esc_html_e( 'Neutral', 'wpis-theme' ); ?></a>
		<a class="wpis-sticky-feed-bar__explore" href="<?php echo esc_url( $explore ); ?>"><?php esc_html_e( 'Explore topics', 'wpis-theme' ); ?></a>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_sticky_feed_filters', 'wpis_theme_sticky_feed_filters_shortcode' );

/**
 * Feed controls: sort pills + filter form (GET).
 *
 * @return string
 */
function wpis_theme_feed_controls_shortcode(): string {
	if ( is_admin() ) {
		return '';
	}
	if ( ! is_home() && ! is_post_type_archive( 'quote' ) ) {
		return '';
	}
	$base = wpis_theme_feed_url();
	$sort = isset( $_GET['wpis_sort'] ) ? sanitize_key( wp_unslash( $_GET['wpis_sort'] ) ) : 'date'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$link_date    = add_query_arg( wpis_theme_feed_preserve_query( array( 'wpis_sort' => 'date' ) ), $base );
	$link_counter = add_query_arg( wpis_theme_feed_preserve_query( array( 'wpis_sort' => 'counter' ) ), $base );

	$sent_terms = get_terms( array( 'taxonomy' => 'sentiment', 'hide_empty' => false ) );
	$claim_terms = get_terms( array( 'taxonomy' => 'claim_type', 'hide_empty' => false ) );
	$cur_sent    = isset( $_GET['sentiment'] ) ? sanitize_title( wp_unslash( $_GET['sentiment'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$cur_claim   = isset( $_GET['claim_type'] ) ? sanitize_title( wp_unslash( $_GET['claim_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$cur_plat    = isset( $_GET['platform'] ) ? sanitize_key( wp_unslash( $_GET['platform'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	ob_start();
	?>
	<div class="wpis-feed-controls">
		<div class="wpis-feed-controls__title">
			<span><?php esc_html_e( 'The feed', 'wpis-theme' ); ?></span>
		</div>
		<div class="wpis-feed-sort" role="group" aria-label="<?php esc_attr_e( 'Sort quotes', 'wpis-theme' ); ?>">
			<a class="wpis-feed-sort__btn<?php echo 'counter' !== $sort ? ' is-active' : ''; ?>" href="<?php echo esc_url( $link_date ); ?>"><?php esc_html_e( 'Recent', 'wpis-theme' ); ?></a>
			<a class="wpis-feed-sort__btn<?php echo 'counter' === $sort ? ' is-active' : ''; ?>" href="<?php echo esc_url( $link_counter ); ?>"><?php esc_html_e( 'Most echoed', 'wpis-theme' ); ?></a>
		</div>
	</div>
	<form class="wpis-feed-filters" method="get" action="<?php echo esc_url( $base ); ?>">
		<?php if ( 'counter' === $sort ) : ?>
			<input type="hidden" name="wpis_sort" value="counter" />
		<?php endif; ?>
		<label class="screen-reader-text" for="wpis-filter-sentiment"><?php esc_html_e( 'Sentiment', 'wpis-theme' ); ?></label>
		<select class="wpis-feed-filters__select" name="sentiment" id="wpis-filter-sentiment" onchange="this.form.submit()">
			<option value=""><?php esc_html_e( 'All sentiments', 'wpis-theme' ); ?></option>
			<?php
			if ( is_array( $sent_terms ) ) {
				foreach ( $sent_terms as $term ) {
					if ( ! $term instanceof WP_Term ) {
						continue;
					}
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						esc_attr( $term->slug ),
						esc_html( $term->name ),
						selected( $cur_sent, $term->slug, false )
					);
				}
			}
			?>
		</select>
		<label class="screen-reader-text" for="wpis-filter-claim"><?php esc_html_e( 'Claim type', 'wpis-theme' ); ?></label>
		<select class="wpis-feed-filters__select" name="claim_type" id="wpis-filter-claim" onchange="this.form.submit()">
			<option value=""><?php esc_html_e( 'All claim types', 'wpis-theme' ); ?></option>
			<?php
			if ( is_array( $claim_terms ) ) {
				foreach ( $claim_terms as $term ) {
					if ( ! $term instanceof WP_Term ) {
						continue;
					}
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						esc_attr( $term->slug ),
						esc_html( $term->name ),
						selected( $cur_claim, $term->slug, false )
					);
				}
			}
			?>
		</select>
		<label class="screen-reader-text" for="wpis-filter-platform"><?php esc_html_e( 'Platform', 'wpis-theme' ); ?></label>
		<select class="wpis-feed-filters__select" name="platform" id="wpis-filter-platform" onchange="this.form.submit()">
			<option value=""><?php esc_html_e( 'All platforms', 'wpis-theme' ); ?></option>
			<?php
			$labels = wpis_theme_platform_labels();
			foreach ( wpis_theme_source_platform_slugs() as $plat ) {
				$label = $labels[ $plat ] ?? $plat;
				printf(
					'<option value="%1$s"%3$s>%2$s</option>',
					esc_attr( $plat ),
					esc_html( $label ),
					selected( $cur_plat, $plat, false )
				);
			}
			?>
		</select>
	</form>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_feed_controls', 'wpis_theme_feed_controls_shortcode' );

/**
 * Preserve current filter query args when building sort links.
 *
 * @param array<string, string> $override Keys to set.
 * @return array<string, string>
 */
function wpis_theme_feed_preserve_query( array $override ): array {
	$out = array();
	if ( isset( $_GET['sentiment'] ) && '' !== $_GET['sentiment'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$out['sentiment'] = sanitize_title( wp_unslash( $_GET['sentiment'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( isset( $_GET['claim_type'] ) && '' !== $_GET['claim_type'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$out['claim_type'] = sanitize_title( wp_unslash( $_GET['claim_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( isset( $_GET['platform'] ) && '' !== $_GET['platform'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$out['platform'] = sanitize_key( wp_unslash( $_GET['platform'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	return array_merge( $out, $override );
}

/**
 * Breadcrumb on single quote.
 *
 * @return string
 */
function wpis_theme_quote_breadcrumb_shortcode(): string {
	if ( ! is_singular( 'quote' ) ) {
		return '';
	}
	$feed = wpis_theme_feed_url();
	$ct   = get_the_terms( get_the_ID(), 'claim_type' );
	$mid  = '';
	if ( is_array( $ct ) && ! empty( $ct ) && $ct[0] instanceof WP_Term ) {
		$t    = $ct[0];
		$link = get_term_link( $t );
		if ( ! is_wp_error( $link ) ) {
			$mid = '<span class="wpis-breadcrumb__sep">/</span><a class="wpis-breadcrumb__a" href="' . esc_url( $link ) . '">' . esc_html( $t->name ) . '</a>';
		}
	}
	ob_start();
	?>
	<div class="wpis-breadcrumb">
		<a class="wpis-breadcrumb__a" href="<?php echo esc_url( $feed ); ?>"><?php esc_html_e( 'Feed', 'wpis-theme' ); ?></a>
		<?php echo $mid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="wpis-breadcrumb__sep">/</span>
		<span class="wpis-breadcrumb__here"><?php esc_html_e( 'This quote', 'wpis-theme' ); ?></span>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_quote_breadcrumb', 'wpis_theme_quote_breadcrumb_shortcode' );

/**
 * Editorial note from post meta.
 *
 * @return string
 */
function wpis_theme_editorial_note_shortcode(): string {
	if ( ! is_singular( 'quote' ) ) {
		return '';
	}
	$note = get_post_meta( get_the_ID(), '_wpis_editorial_note', true );
	if ( ! is_string( $note ) || '' === trim( $note ) ) {
		return '';
	}
	ob_start();
	?>
	<div class="wpis-editorial-note">
		<div class="wpis-editorial-note__label"><?php esc_html_e( 'A note from the editor', 'wpis-theme' ); ?></div>
		<div class="wpis-editorial-note__body"><?php echo wp_kses_post( wpautop( $note ) ); ?></div>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_editorial_note', 'wpis_theme_editorial_note_shortcode' );

/**
 * Translation siblings as “variants” (Polylang).
 *
 * @return string
 */
function wpis_theme_quote_variants_shortcode(): string {
	if ( ! is_singular( 'quote' ) || ! function_exists( 'pll_get_post_translations' ) ) {
		return '';
	}
	$map = pll_get_post_translations( get_the_ID() );
	if ( ! is_array( $map ) || count( $map ) < 2 ) {
		return '';
	}
	ob_start();
	?>
	<div class="wpis-quote-variants">
		<div class="wpis-quote-variants__title"><?php esc_html_e( 'A few of the variants', 'wpis-theme' ); ?></div>
		<?php
		foreach ( $map as $lang => $pid ) {
			$pid = (int) $pid;
			if ( $pid === (int) get_the_ID() ) {
				continue;
			}
			$p = get_post( $pid );
			if ( ! $p ) {
				continue;
			}
			$plat = (string) get_post_meta( $pid, '_wpis_source_platform', true );
			$yr   = get_the_date( 'Y', $p );
			?>
			<div class="wpis-quote-variants__line">
				<p class="wpis-quote-variants__text"><?php echo esc_html( wp_strip_all_tags( (string) $p->post_content ) ); ?></p>
				<div class="wpis-quote-variants__meta">
					<?php echo esc_html( strtoupper( (string) $lang ) ); ?>
					<?php if ( '' !== $plat ) : ?>
						· <?php echo esc_html( strtoupper( $plat ) ); ?>
					<?php endif; ?>
					<?php if ( $yr ) : ?>
						· <?php echo esc_html( $yr ); ?>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_quote_variants', 'wpis_theme_quote_variants_shortcode' );

/**
 * Rewrite root-relative /slug/ hrefs in rendered block HTML for the active language.
 *
 * @param string $block_content HTML.
 * @param array  $block         Block.
 * @return string
 */
function wpis_theme_localize_static_hrefs_in_block( string $block_content, array $block ): string {
	unset( $block );
	if ( is_admin() ) {
		return $block_content;
	}
	$map = array(
		'explore'         => wpis_theme_page_url( 'explore' ),
		'submit'          => wpis_theme_page_url( 'submit' ),
		'profile'         => wpis_theme_page_url( 'profile' ),
		'my-profile'      => wpis_theme_page_url( 'my-profile' ),
		'submitted'       => wpis_theme_page_url( 'submitted' ),
		'privacy-policy'  => wpis_theme_page_url( 'privacy-policy' ),
		'about'           => wpis_theme_page_url( 'about' ),
		'how-it-works'    => wpis_theme_page_url( 'how-it-works' ),
	);
	foreach ( $map as $slug => $url ) {
		$href = 'href="' . esc_url( $url ) . '"';
		$block_content = str_replace( array( 'href="/' . $slug . '/"', 'href="/' . $slug . '"' ), array( $href, $href ), $block_content );
	}
	$block_content = str_replace( 'href="/"', 'href="' . esc_url( home_url( '/' ) ) . '"', $block_content );
	return $block_content;
}
add_filter( 'render_block', 'wpis_theme_localize_static_hrefs_in_block', 9, 2 );

/**
 * Enqueue front-end assets.
 *
 * @return void
 */
function wpis_theme_enqueue_assets(): void {
	$ver = wp_get_theme()->get( 'Version' );
	wp_enqueue_script(
		'wpis-theme-toggle',
		get_template_directory_uri() . '/assets/js/theme-toggle.js',
		array(),
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'wpis_theme_enqueue_assets' );

/**
 * WP_Query args for published quote feeds (sort and filters from the current request).
 *
 * @param int $per_page Posts per page.
 * @param int $paged    Page number.
 * @return array<string, mixed>
 */
function wpis_theme_get_quote_feed_query_args( int $per_page, int $paged ): array {
	$args = array(
		'post_type'           => 'quote',
		'post_status'         => 'publish',
		'posts_per_page'      => $per_page,
		'paged'               => max( 1, $paged ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => false,
	);
	return wpis_theme_apply_quote_feed_args( $args, 'default' );
}

/**
 * Enqueue “load more” for quote feeds (index, blog home, quote archive).
 *
 * @return void
 */
function wpis_theme_enqueue_feed_load_more(): void {
	if ( is_admin() ) {
		return;
	}
	if ( ! is_home() && ! is_post_type_archive( 'quote' ) ) {
		return;
	}

	$per_page = 10;
	$count_q  = new WP_Query( wpis_theme_get_quote_feed_query_args( $per_page, 1 ) );
	$total    = max( 1, (int) $count_q->max_num_pages );
	wp_reset_postdata();

	$paged = max( 1, (int) get_query_var( 'paged' ) );
	$ord   = isset( $_GET['wpis_order'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_GET['wpis_order'] ) ) ) : 'DESC'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $ord, array( 'ASC', 'DESC' ), true ) ) {
		$ord = 'DESC';
	}

	wp_enqueue_script(
		'wpis-feed-load-more',
		get_template_directory_uri() . '/assets/js/feed-load-more.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script(
		'wpis-feed-load-more',
		'wpisFeedLoadMore',
		array(
			'restUrl'      => rest_url( 'wpis/v1/quote-feed' ),
			'perPage'      => $per_page,
			'totalPages'   => $total,
			'currentPaged' => $paged,
			'sort'         => isset( $_GET['wpis_sort'] ) ? sanitize_key( wp_unslash( $_GET['wpis_sort'] ) ) : 'date', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'order'        => $ord,
			'sentiment'    => isset( $_GET['sentiment'] ) ? sanitize_title( wp_unslash( $_GET['sentiment'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'claimType'    => isset( $_GET['claim_type'] ) ? sanitize_title( wp_unslash( $_GET['claim_type'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'platform'     => isset( $_GET['platform'] ) ? sanitize_key( wp_unslash( $_GET['platform'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'lang'         => function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '',
			'i18n'         => array(
				'loadMore' => __( 'Load more quotes', 'wpis-theme' ),
				'loading'  => __( 'Loading...', 'wpis-theme' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'wpis_theme_enqueue_feed_load_more', 20 );

/**
 * Register dynamic blocks.
 *
 * @return void
 */
function wpis_theme_register_blocks(): void {
	$blocks = array( 'hero-stats', 'lang-switcher', 'user-stats', 'user-submissions', 'tax-overview-card', 'quote-opposing', 'spread-stats', 'submitted-summary', 'quote-summary-meta' );
	foreach ( $blocks as $block ) {
		register_block_type( get_template_directory() . '/blocks/' . $block );
	}
}
add_action( 'init', 'wpis_theme_register_blocks' );

/**
 * Whether the main query is a profile page (any Polylang translation of profile / my-profile).
 *
 * @return bool
 */
function wpis_theme_is_profile_page(): bool {
	if ( ! is_page() ) {
		return false;
	}
	$qid  = get_queried_object_id();
	$slug = get_post_field( 'post_name', $qid );
	if ( in_array( $slug, array( 'profile', 'my-profile' ), true ) ) {
		return true;
	}
	$base = get_page_by_path( 'profile', OBJECT, 'page' );
	if ( ! $base instanceof WP_Post ) {
		$base = get_page_by_path( 'my-profile', OBJECT, 'page' );
	}
	if ( ! $base instanceof WP_Post ) {
		return false;
	}
	if ( function_exists( 'pll_get_post_translations' ) ) {
		$translations = pll_get_post_translations( (int) $base->ID );
		if ( is_array( $translations ) ) {
			foreach ( $translations as $tid ) {
				if ( (int) $tid === (int) $qid ) {
					return true;
				}
			}
		}
	}
	return false;
}

/**
 * Require login on profile page.
 *
 * @return void
 */
function wpis_theme_profile_gate(): void {
	if ( ! wpis_theme_is_profile_page() ) {
		return;
	}
	if ( is_user_logged_in() ) {
		return;
	}
	auth_redirect();
}
add_action( 'template_redirect', 'wpis_theme_profile_gate' );

/**
 * Submit form shortcode for use in Submit page.
 *
 * @return string
 */
function wpis_theme_submit_form_shortcode(): string {
	$action = esc_url( admin_url( 'admin-post.php' ) );
	$nonce  = wp_nonce_field( 'wpis_submit_quote', 'wpis_nonce', true, false );
	ob_start();
	?>
	<form class="wpis-submit-form" method="post" action="<?php echo esc_url( $action ); ?>" enctype="multipart/form-data">
		<input type="hidden" name="action" value="wpis_submit_quote" />
		<?php
		if ( function_exists( 'pll_current_language' ) ) {
			$pll_lang = pll_current_language();
			if ( is_string( $pll_lang ) && '' !== $pll_lang ) {
				echo '<input type="hidden" name="wpis_pll_lang" value="' . esc_attr( $pll_lang ) . '" />';
			}
		}
		?>
		<?php echo $nonce; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p class="wpis-honeypot" style="display:none"><label><?php esc_html_e( 'Leave empty', 'wpis-theme' ); ?><input type="text" name="wpis_hp" value="" tabindex="-1" autocomplete="off" /></label></p>
		<div class="wpis-form-group">
			<label class="wpis-form-label" for="wpis_quote"><?php esc_html_e( 'Quote text', 'wpis-theme' ); ?> <span class="wpis-required">*</span></label>
			<textarea class="wpis-form-control" id="wpis_quote" name="wpis_quote" rows="6" maxlength="1000" required></textarea>
			<p class="wpis-form-hint"><?php esc_html_e( 'Paste the exact wording. Max 1000 characters.', 'wpis-theme' ); ?></p>
		</div>
		<div class="wpis-form-group">
			<label class="wpis-form-label" for="wpis_source_url"><?php esc_html_e( 'Source URL', 'wpis-theme' ); ?></label>
			<input class="wpis-form-control" type="url" id="wpis_source_url" name="wpis_source_url" placeholder="https://" />
			<p class="wpis-form-hint"><?php esc_html_e( 'Link to the post, toot, or article (optional but helpful).', 'wpis-theme' ); ?></p>
		</div>
		<div class="wpis-form-group">
			<label class="wpis-form-label" for="wpis_screenshot"><?php esc_html_e( 'Screenshot', 'wpis-theme' ); ?></label>
			<div class="wpis-upload-zone"><input type="file" id="wpis_screenshot" name="wpis_screenshot" accept="image/*" /></div>
		</div>
		<div class="wpis-rgpd-notice">
			<strong><?php esc_html_e( 'Consent', 'wpis-theme' ); ?></strong>
			<label><input type="checkbox" name="wpis_rgpd" value="1" required /> <?php esc_html_e( 'I agree submissions may be published and attributed per site policy.', 'wpis-theme' ); ?></label>
		</div>
		<p><button type="submit" class="wpis-btn-primary"><?php esc_html_e( 'Submit quote', 'wpis-theme' ); ?></button></p>
	</form>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_submit_form', 'wpis_theme_submit_form_shortcode' );

/**
 * Thank-you URL in the correct Polylang translation.
 *
 * @param string $url        Default URL.
 * @param int    $post_id    New quote ID (unused).
 * @param string $lang_hint  Language from hidden form field.
 * @return string
 */
function wpis_theme_submission_redirect_url( string $url, int $post_id, string $lang_hint = '' ): string {
	unset( $post_id );
	$lang_pass = ( '' !== $lang_hint ) ? $lang_hint : null;
	$resolved  = wpis_theme_resolve_page_permalink( 'submitted', $lang_pass );
	return null !== $resolved ? $resolved : $url;
}
add_filter( 'wpis_submission_redirect_url', 'wpis_theme_submission_redirect_url', 10, 3 );

/**
 * Submitted page summary shortcode (?t= token).
 *
 * @return string
 */
function wpis_theme_submitted_shortcode(): string {
	$token = isset( $_GET['t'] ) ? sanitize_text_field( wp_unslash( $_GET['t'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '' === $token ) {
		return '<p>' . esc_html__( 'Thanks for contributing.', 'wpis-theme' ) . '</p>';
	}
	$pid = get_transient( 'wpis_submit_' . $token );
	if ( ! $pid ) {
		return '<p>' . esc_html__( 'Submission confirmation expired or invalid.', 'wpis-theme' ) . '</p>';
	}
	$post = get_post( (int) $pid );
	if ( ! $post ) {
		return '';
	}
	$pending = ( new WP_Query(
		array(
			'post_type'      => 'quote',
			'post_status'    => 'pending',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	) )->found_posts;
	ob_start();
	?>
	<p><?php esc_html_e( 'Thanks, your submission is being reviewed.', 'wpis-theme' ); ?></p>
	<p><?php echo esc_html( sprintf( /* translators: %d pending count */ __( 'There are currently %d submissions pending moderation.', 'wpis-theme' ), (int) $pending ) ); ?></p>
	<blockquote><?php echo wp_kses_post( wpautop( $post->post_content ) ); ?></blockquote>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_submitted_summary', 'wpis_theme_submitted_shortcode' );

/**
 * Short blurbs for claim types (explore cards; extend with real term descriptions in admin when ready).
 *
 * @return array<string, string>
 */
function wpis_theme_claim_type_blurbs(): array {
	return array(
		'performance'        => __( 'Speed, weight, responsiveness. Often about hosting as much as WordPress itself.', 'wpis-theme' ),
		'security'           => __( 'How safe or exposed WordPress is. Often the plugin ecosystem, not only core.', 'wpis-theme' ),
		'ease-of-use'        => __( 'Who WordPress is for: beginners versus power users.', 'wpis-theme' ),
		'community'          => __( 'People, events, and contributor culture around the project.', 'wpis-theme' ),
		'ecosystem'          => __( 'Plugins, themes, and integrations: breadth versus quality.', 'wpis-theme' ),
		'business-viability' => __( 'Whether you can build a serious business on WordPress long term.', 'wpis-theme' ),
		'accessibility'      => __( 'Standards, themes, and inclusion for people with disabilities.', 'wpis-theme' ),
		'modernity'          => __( 'Gutenberg, APIs, and whether WordPress “keeps up” with the web.', 'wpis-theme' ),
	);
}

/**
 * Explore page: claim type grid (mockup-style cards).
 *
 * @return string
 */
function wpis_theme_explore_terms_shortcode(): string {
	$terms = get_terms(
		array(
			'taxonomy'   => 'claim_type',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '<p>' . esc_html__( 'No topics yet.', 'wpis-theme' ) . '</p>';
	}
	$blurbs = wpis_theme_claim_type_blurbs();
	ob_start();
	echo '<div class="wpis-explore-grid">';
	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}
		$desc = term_description( $term, 'claim_type' );
		if ( ! is_string( $desc ) || '' === trim( wp_strip_all_tags( $desc ) ) ) {
			$desc = $blurbs[ $term->slug ] ?? '';
		}
		$counts = wpis_theme_sentiment_counts_for_claim( $term->slug );
		$total  = max( 1, array_sum( $counts ) );
		$pn     = (int) round( 100 * ( $counts['positive'] / $total ) );
		$nn     = (int) round( 100 * ( $counts['negative'] / $total ) );
		$mn     = max( 0, 100 - $pn - $nn );
		?>
		<a class="wpis-tax-card" href="<?php echo esc_url( $link ); ?>">
			<div class="wpis-tax-card__head">
				<h3 class="wpis-tax-card__title"><?php echo esc_html( $term->name ); ?></h3>
				<span class="wpis-tax-card__count"><?php echo esc_html( sprintf( /* translators: %d: quote count */ _n( '%d quote', '%d quotes', (int) $term->count, 'wpis-theme' ), (int) $term->count ) ); ?></span>
			</div>
			<?php if ( '' !== $desc ) : ?>
				<p class="wpis-tax-card__desc"><?php echo esc_html( wp_strip_all_tags( $desc ) ); ?></p>
			<?php endif; ?>
			<div class="wpis-tax-card__bar" aria-hidden="true">
				<span class="wpis-tax-card__bar-seg wpis-tax-card__bar-seg--neg" style="width:<?php echo esc_attr( (string) $nn ); ?>%"></span>
				<span class="wpis-tax-card__bar-seg wpis-tax-card__bar-seg--pos" style="width:<?php echo esc_attr( (string) $pn ); ?>%"></span>
				<span class="wpis-tax-card__bar-seg wpis-tax-card__bar-seg--mix" style="width:<?php echo esc_attr( (string) $mn ); ?>%"></span>
			</div>
			<div class="wpis-tax-card__breakdown">
				<span><span class="wpis-dot wpis-dot--neg"></span><?php echo esc_html( (string) $counts['negative'] ); ?> <?php esc_html_e( 'critical', 'wpis-theme' ); ?></span>
				<span><span class="wpis-dot wpis-dot--pos"></span><?php echo esc_html( (string) $counts['positive'] ); ?> <?php esc_html_e( 'supportive', 'wpis-theme' ); ?></span>
				<span><span class="wpis-dot wpis-dot--mix"></span><?php echo esc_html( (string) $counts['mixed'] ); ?> <?php esc_html_e( 'mixed', 'wpis-theme' ); ?></span>
			</div>
		</a>
		<?php
	}
	echo '</div>';
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_explore_terms', 'wpis_theme_explore_terms_shortcode' );

/**
 * Sentiment counts for quotes in a claim type term.
 *
 * @param string $claim_slug Claim type slug.
 * @return array{positive: int, negative: int, mixed: int, neutral: int}
 */
function wpis_theme_sentiment_counts_for_claim( string $claim_slug ): array {
	$out = array(
		'positive' => 0,
		'negative' => 0,
		'mixed'    => 0,
		'neutral'  => 0,
	);
	foreach ( array_keys( $out ) as $sent ) {
		$q = new WP_Query(
			array(
				'post_type'           => 'quote',
				'post_status'         => 'publish',
				'posts_per_page'      => 1,
				'fields'              => 'ids',
				'no_found_rows'       => false,
				'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					'relation' => 'AND',
					array(
						'taxonomy' => 'claim_type',
						'field'    => 'slug',
						'terms'    => $claim_slug,
					),
					array(
						'taxonomy' => 'sentiment',
						'field'    => 'slug',
						'terms'    => $sent,
					),
				),
				'ignore_sticky_posts' => true,
			)
		);
		$out[ $sent ] = (int) $q->found_posts;
		wp_reset_postdata();
	}
	return $out;
}

/**
 * Explore: platform counts linking to the feed with ?platform=.
 *
 * @return string
 */
function wpis_theme_explore_platforms_shortcode(): string {
	global $wpdb;
	$feed = wpis_theme_feed_url();
	$sql  = $wpdb->prepare(
		"SELECT pm.meta_value AS platform, COUNT(*) AS c FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE p.post_type = %s AND p.post_status = %s AND pm.meta_key = %s AND pm.meta_value != ''
		GROUP BY pm.meta_value ORDER BY c DESC",
		'quote',
		'publish',
		'_wpis_source_platform'
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- $sql from prepare().
	$rows = $wpdb->get_results( $sql, ARRAY_A );
	if ( empty( $rows ) ) {
		return '<p class="wpis-explore-platforms-empty">' . esc_html__( 'No platform data yet.', 'wpis-theme' ) . '</p>';
	}
	$labels = wpis_theme_platform_labels();
	ob_start();
	echo '<div class="wpis-platform-grid">';
	foreach ( $rows as $row ) {
		$slug = isset( $row['platform'] ) ? sanitize_key( (string) $row['platform'] ) : '';
		$c    = isset( $row['c'] ) ? (int) $row['c'] : 0;
		if ( '' === $slug ) {
			continue;
		}
		$label = $labels[ $slug ] ?? $slug;
		$url   = add_query_arg( 'platform', $slug, $feed );
		?>
		<a class="wpis-platform-card" href="<?php echo esc_url( $url ); ?>">
			<h4 class="wpis-platform-card__title"><?php echo esc_html( $label ); ?></h4>
			<span class="wpis-platform-card__count"><?php echo esc_html( (string) $c ); ?></span>
		</a>
		<?php
	}
	echo '</div>';
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_explore_platforms', 'wpis_theme_explore_platforms_shortcode' );

/**
 * Sort keys from URL (?wpis_sort=date|counter&wpis_order=ASC|DESC).
 *
 * @return array{orderby: string, order: string, meta_key?: string}
 */
function wpis_theme_get_quote_sort_from_request(): array {
	$sort = isset( $_GET['wpis_sort'] ) ? sanitize_key( wp_unslash( $_GET['wpis_sort'] ) ) : 'date'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$ord  = isset( $_GET['wpis_order'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_GET['wpis_order'] ) ) ) : 'DESC'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $ord, array( 'ASC', 'DESC' ), true ) ) {
		$ord = 'DESC';
	}

	if ( 'counter' === $sort ) {
		return array(
			'orderby'  => 'meta_value_num',
			'meta_key' => '_wpis_counter',
			'order'    => $ord,
		);
	}

	return array(
		'orderby' => 'date',
		'order'   => $ord,
	);
}

/**
 * Apply URL sort/filter params to a quote WP_Query args array.
 *
 * @param array<string, mixed> $args    Query args.
 * @param string               $context default|tax_claim|tax_sentiment: skip redundant tax filters on term archives.
 * @return array<string, mixed>
 */
function wpis_theme_apply_quote_feed_args( array $args, string $context = 'default' ): array {
	$sort_args = wpis_theme_get_quote_sort_from_request();
	$args      = array_merge( $args, $sort_args );

	$tax_query = isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) ? $args['tax_query'] : array();

	if ( 'tax_sentiment' !== $context && ! empty( $_GET['sentiment'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$slug = sanitize_title( wp_unslash( $_GET['sentiment'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '' !== $slug ) {
			$tax_query[] = array(
				'taxonomy' => 'sentiment',
				'field'    => 'slug',
				'terms'    => $slug,
			);
		}
	}

	if ( 'tax_claim' !== $context && ! empty( $_GET['claim_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$slug = sanitize_title( wp_unslash( $_GET['claim_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '' !== $slug ) {
			$tax_query[] = array(
				'taxonomy' => 'claim_type',
				'field'    => 'slug',
				'terms'    => $slug,
			);
		}
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query;
	}

	$meta_query = isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ? $args['meta_query'] : array();
	if ( ! empty( $_GET['platform'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$plat = sanitize_key( wp_unslash( $_GET['platform'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( in_array( $plat, wpis_theme_source_platform_slugs(), true ) ) {
			$meta_query[] = array(
				'key'   => '_wpis_source_platform',
				'value' => $plat,
			);
		}
	}
	if ( count( $meta_query ) > 1 ) {
		$meta_query['relation'] = 'AND';
	}
	if ( ! empty( $meta_query ) ) {
		$args['meta_query'] = $meta_query;
	}

	return $args;
}

/**
 * Whether query args target the quote post type.
 *
 * @param array<string, mixed> $query Query args.
 * @return bool
 */
function wpis_theme_query_is_quote_feed( array $query ): bool {
	$pt = $query['post_type'] ?? null;
	if ( 'quote' === $pt ) {
		return true;
	}
	if ( is_array( $pt ) && in_array( 'quote', $pt, true ) ) {
		return true;
	}
	return false;
}

/**
 * Query Loop block: sort/filter for quote feeds.
 *
 * @param array<string, mixed> $query Query vars.
 * @param \WP_Block            $block Block instance.
 * @return array<string, mixed>
 */
function wpis_theme_query_loop_block_query_vars( array $query, \WP_Block $block ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	unset( $block );
	if ( ! wpis_theme_query_is_quote_feed( $query ) ) {
		return $query;
	}
	return wpis_theme_apply_quote_feed_args( $query, 'default' );
}
add_filter( 'query_loop_block_query_vars', 'wpis_theme_query_loop_block_query_vars', 10, 2 );

/**
 * Main query: same sort/filter on quote archives and tax archives.
 *
 * @param \WP_Query $query Query.
 * @return void
 */
function wpis_theme_pre_get_posts_quote_feeds( \WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_search() ) {
		$query->set( 'post_type', 'quote' );
		return;
	}

	if ( $query->is_post_type_archive( 'quote' ) ) {
		$args = wpis_theme_apply_quote_feed_args( array( 'post_type' => 'quote' ), 'default' );
		foreach ( array( 'orderby', 'order', 'meta_key', 'meta_query' ) as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$query->set( $key, $args[ $key ] );
			}
		}
		if ( ! empty( $args['tax_query'] ) ) {
			$query->set( 'tax_query', $args['tax_query'] );
		}
		return;
	}

	// Taxonomy archives: only adjust sort so we do not override Core term resolution.
	if ( $query->is_tax( 'claim_type' ) || $query->is_tax( 'sentiment' ) ) {
		$sort_args = wpis_theme_get_quote_sort_from_request();
		foreach ( array( 'orderby', 'order', 'meta_key' ) as $key ) {
			if ( isset( $sort_args[ $key ] ) ) {
				$query->set( $key, $sort_args[ $key ] );
			}
		}
	}
}
add_action( 'pre_get_posts', 'wpis_theme_pre_get_posts_quote_feeds' );

// Webhook delivery smoke test (no runtime effect).
