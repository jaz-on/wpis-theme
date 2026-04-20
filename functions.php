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
		'explore'        => wpis_theme_page_url( 'explore' ),
		'submit'         => wpis_theme_page_url( 'submit' ),
		'profile'        => wpis_theme_page_url( 'profile' ),
		'submitted'      => wpis_theme_page_url( 'submitted' ),
		'privacy-policy' => wpis_theme_page_url( 'privacy-policy' ),
	);
	foreach ( $map as $slug => $url ) {
		$block_content = str_replace( 'href="/' . $slug . '/"', 'href="' . esc_url( $url ) . '"', $block_content );
	}
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
	$blocks = array( 'hero-stats', 'lang-switcher', 'user-stats', 'user-submissions', 'tax-overview-card', 'quote-opposing', 'spread-stats', 'submitted-summary' );
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
		<p style="display:none"><label><?php esc_html_e( 'Leave empty', 'wpis-theme' ); ?><input type="text" name="wpis_hp" value="" tabindex="-1" autocomplete="off" /></label></p>
		<p><label for="wpis_quote"><?php esc_html_e( 'Quote text', 'wpis-theme' ); ?></label><textarea id="wpis_quote" name="wpis_quote" rows="5" maxlength="1000"></textarea></p>
		<p><label for="wpis_source_url"><?php esc_html_e( 'Source URL (optional)', 'wpis-theme' ); ?></label><input type="url" id="wpis_source_url" name="wpis_source_url" /></p>
		<p><label for="wpis_screenshot"><?php esc_html_e( 'Screenshot (optional)', 'wpis-theme' ); ?></label><input type="file" id="wpis_screenshot" name="wpis_screenshot" accept="image/*" /></p>
		<p><label><input type="checkbox" name="wpis_rgpd" value="1" required /> <?php esc_html_e( 'I agree to the privacy terms.', 'wpis-theme' ); ?></label></p>
		<p><button type="submit" class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Submit', 'wpis-theme' ); ?></button></p>
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
 * Explore page: list claim types with counts (shortcode for block templates).
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
	ob_start();
	echo '<ul class="wpis-explore-terms" style="list-style:none;padding:0;display:grid;gap:1rem;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));">';
	foreach ( $terms as $term ) {
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}
		echo '<li style="border:1px solid currentColor;padding:1rem;">';
		echo '<a href="' . esc_url( $link ) . '" style="font-size:1.125rem;">' . esc_html( $term->name ) . '</a>';
		echo '<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;color:var(--wp--preset--color--muted);">';
		echo esc_html( sprintf( /* translators: %d count */ _n( '%d quote', '%d quotes', (int) $term->count, 'wpis-theme' ), (int) $term->count ) );
		echo '</div></li>';
	}
	echo '</ul>';
	return (string) ob_get_clean();
}
add_shortcode( 'wpis_explore_terms', 'wpis_theme_explore_terms_shortcode' );

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
		foreach ( array( 'orderby', 'order', 'meta_key' ) as $key ) {
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
