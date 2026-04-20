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
 * Require login on profile page.
 *
 * @return void
 */
function wpis_theme_profile_gate(): void {
	if ( ! is_page() ) {
		return;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	if ( 'profile' !== $slug && 'my-profile' !== $slug ) {
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
