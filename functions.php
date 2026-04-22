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
 * Read a seed file from content/html/ (patterns and optional CLI/demo import).
 *
 * @param string $filename Basename, e.g. home.html.
 * @return string File contents or empty string.
 */
function wpis_theme_get_content_html( $filename ) {
	$path = get_template_directory() . '/content/html/' . ltrim( $filename, '/' );
	if ( ! is_readable( $path ) ) {
		return '';
	}
	$raw = file_get_contents( $path );
	return is_string( $raw ) ? $raw : '';
}

require_once get_template_directory() . '/inc/theme-setup.php';
require_once get_template_directory() . '/inc/languages.php';
require_once get_template_directory() . '/inc/wpis-block-template-utils.php';
require_once get_template_directory() . '/inc/register-patterns.php';
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/admin-seed.php';
}

/**
 * Skip link targeting #wpis-main (main group anchor in templates).
 *
 * Keeping this out of parts/header.html avoids core/html validation failures in the Site Editor
 * (serialization and whitespace must match exactly or blocks show as invalid).
 */
function wpis_theme_skip_link() {
	echo '<a class="skip-link" href="#wpis-main">' . esc_html__( 'Skip to content', 'wpis-theme' ) . '</a>';
}
add_action( 'wp_body_open', 'wpis_theme_skip_link', 5 );

/**
 * Rewrite preset color CSS variables in rendered block HTML to semantic aliases from wpis-global.css.
 *
 * Saved post content still references --wp--preset--color--* (light palette). --ink, --muted, etc.
 * follow prefers-color-scheme and data-theme so feeds and cards stay readable in dark mode.
 *
 * @param string $content Post content.
 * @return string
 */
function wpis_theme_semantic_colors_in_content( $content ) {
	if ( ! is_string( $content ) || '' === $content || false === strpos( $content, '--wp--preset--color-' ) ) {
		return $content;
	}
	$map = array(
		'var(--wp--preset--color--accent-soft)' => 'var(--accent-soft)',
		'var(--wp--preset--color--positive)'    => 'var(--positive)',
		'var(--wp--preset--color--negative)'    => 'var(--negative)',
		'var(--wp--preset--color--mixed)'       => 'var(--mixed)',
		'var(--wp--preset--color--paper)'       => 'var(--paper)',
		'var(--wp--preset--color--muted)'       => 'var(--muted)',
		'var(--wp--preset--color--bg)'          => 'var(--bg)',
		'var(--wp--preset--color--accent)'      => 'var(--accent)',
		'var(--wp--preset--color--ink)'         => 'var(--ink)',
	);
	return str_replace( array_keys( $map ), array_values( $map ), $content );
}
add_filter( 'the_content', 'wpis_theme_semantic_colors_in_content', 5 );

/**
 * Theme supports: menus, editor styles.
 */
function wpis_theme_register_theme_support() {
	register_nav_menus(
		array(
			'primary' => __( 'WPIS Primary', 'wpis-theme' ),
		)
	);
	add_editor_style( 'assets/css/wpis-global.css' );
}
add_action( 'after_setup_theme', 'wpis_theme_register_theme_support' );

/**
 * Block styles backed by theme.json (layout shells).
 */
function wpis_theme_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-hero',
			'label' => __( 'WPIS hero', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-feed',
			'label' => __( 'WPIS feed', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-prose',
			'label' => __( 'WPIS prose (narrow)', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-detail',
			'label' => __( 'WPIS quote detail', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-submit',
			'label' => __( 'WPIS submit shell', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-tax-hero',
			'label' => __( 'WPIS taxonomy hero', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-explore-hero',
			'label' => __( 'WPIS explore hero', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-explore-section',
			'label' => __( 'WPIS explore section', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-search',
			'label' => __( 'WPIS search shell', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-how',
			'label' => __( 'WPIS how-it-works', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-profile',
			'label' => __( 'WPIS profile', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-empty-state',
			'label' => __( 'WPIS empty state', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-confirm',
			'label' => __( 'WPIS confirmation', 'wpis-theme' ),
		)
	);
	/* Site chrome (header / footer parts): named styles in the Site Editor. */
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-header',
			'label' => __( 'WPIS site header', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-header-right',
			'label' => __( 'WPIS header right column', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-header-utils',
			'label' => __( 'WPIS header tools row', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-site-footer',
			'label' => __( 'WPIS site footer', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-footer-top',
			'label' => __( 'WPIS footer top row', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-header-site-title',
			'label' => __( 'WPIS site title', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-footer-top-left',
			'label' => __( 'WPIS footer byline', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-footer-top-right',
			'label' => __( 'WPIS footer quick links', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-footer-trademark',
			'label' => __( 'WPIS footer legal note', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/button',
		array(
			'name'  => 'wpis-header-theme-toggle',
			'label' => __( 'WPIS header theme button', 'wpis-theme' ),
		)
	);
	/* Page content (feed, search, explore, how-it-works): is-style-wpis-* in block markup. */
	register_block_style(
		'core/heading',
		array(
			'name'  => 'wpis-hero-title',
			'label' => __( 'WPIS hero title', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/heading',
		array(
			'name'  => 'wpis-detail-quote',
			'label' => __( 'WPIS detail quote', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-hero-intro',
			'label' => __( 'WPIS hero intro', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-lang-switcher',
			'label' => __( 'WPIS language switcher (shortcode)', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-hero-stats',
			'label' => __( 'WPIS hero stats row', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-feed-header',
			'label' => __( 'WPIS feed header', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-feed-title',
			'label' => __( 'WPIS feed title', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-no-results',
			'label' => __( 'WPIS empty state message', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-search-container',
			'label' => __( 'WPIS search body', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-search-summary',
			'label' => __( 'WPIS search result summary', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-tax-grid',
			'label' => __( 'WPIS claim-type grid', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-tax-card',
			'label' => __( 'WPIS claim-type card', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-tax-card-head',
			'label' => __( 'WPIS claim-type card head', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-tax-count',
			'label' => __( 'WPIS tax card count', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-tax-desc',
			'label' => __( 'WPIS tax card description', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-explore-section-title',
			'label' => __( 'WPIS explore section title', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-platform-grid',
			'label' => __( 'WPIS platform grid', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-platform-card',
			'label' => __( 'WPIS platform card', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-platform-count',
			'label' => __( 'WPIS platform count', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-how-step-list',
			'label' => __( 'WPIS how-to step list', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-how-step',
			'label' => __( 'WPIS how-to step', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-quote-card',
			'label' => __( 'WPIS quote feed card (article shell)', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'wpis-quote-footer',
			'label' => __( 'WPIS quote card meta row', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-count-badge',
			'label' => __( 'WPIS repeat count badge', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/post-title',
		array(
			'name'  => 'wpis-quote-excerpt',
			'label' => __( 'WPIS quote excerpt title', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/post-terms',
		array(
			'name'  => 'wpis-claim-tag',
			'label' => __( 'WPIS claim type chip', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-eyebrow',
			'label' => __( 'WPIS eyebrow', 'wpis-theme' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'wpis-pull-quote',
			'label' => __( 'WPIS pull quote', 'wpis-theme' ),
		)
	);
}
add_action( 'init', 'wpis_theme_register_block_styles', 19 );

if ( defined( 'WP_CLI' ) && constant( 'WP_CLI' ) ) {
	require_once get_template_directory() . '/inc/wpis-cli-seed.php';
}

/**
 * Front-end assets.
 */
function wpis_theme_enqueue_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	$css_path = $theme_dir . '/assets/css/wpis-global.css';
	if ( is_readable( $css_path ) ) {
		wp_enqueue_style(
			'wpis-global',
			$theme_uri . '/assets/css/wpis-global.css',
			array(),
			(string) filemtime( $css_path )
		);
	}

	$js_path = $theme_dir . '/assets/js/theme-toggle.js';
	if ( is_readable( $js_path ) ) {
		wp_enqueue_script(
			'wpis-theme-toggle',
			$theme_uri . '/assets/js/theme-toggle.js',
			array(),
			(string) filemtime( $js_path ),
			true
		);
	}

	$feed_js = $theme_dir . '/assets/js/feed-demo.js';
	// home.html (front page) uses a block Query on quote; the demo script targets legacy HTML in content/html (see security), not the block feed.
	$load_feed = is_readable( $feed_js ) && is_page( 'security' );
	if ( $load_feed && ( ! defined( 'WPIS_DISABLE_FEED_DEMO' ) || ! WPIS_DISABLE_FEED_DEMO ) ) {
		wp_enqueue_script(
			'wpis-feed-demo',
			$theme_uri . '/assets/js/feed-demo.js',
			array(),
			(string) filemtime( $feed_js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'wpis_theme_enqueue_assets' );

/**
 * Block variations for layout utility classes (WordPress 6.5+).
 */
function wpis_theme_register_block_variations() {
	if ( ! function_exists( 'register_block_variation' ) ) {
		return;
	}

	$variations = array(
		array(
			'block' => 'core/group',
			'name'  => 'wpis-detail-container',
			'title' => __( 'WPIS detail container', 'wpis-theme' ),
			'class' => 'is-style-wpis-detail',
		),
	);

	foreach ( $variations as $def ) {
		$attrs = array( 'className' => $def['class'] );
		if ( isset( $def['level'] ) ) {
			$attrs['level'] = $def['level'];
		}
		register_block_variation(
			$def['block'],
			array(
				'name'       => $def['name'],
				'title'      => $def['title'],
				'attributes' => $attrs,
				'scope'      => array( 'inserter', 'block', 'transform' ),
			)
		);
	}
}
add_action( 'init', 'wpis_theme_register_block_variations', 20 );

/**
 * Register WPIS site header and site footer as core/group block variations (native blocks, not HTML).
 * Uses {@see get_block_type_variations} so the structure comes from the same part files as the theme.
 *
 * @param array<int, array<string, mixed>> $variations  Registered variations.
 * @param \WP_Block_Type                    $block_type Block type.
 * @return array<int, array<string, mixed>>
 */
function wpis_theme_add_group_shell_variations( $variations, $block_type ) {
	if ( 'core/group' !== ( $block_type->name ?? '' ) ) {
		return $variations;
	}
	if ( ! is_array( $variations ) ) {
		$variations = array();
	}
	$dir = get_template_directory() . '/parts/';

	$header = wpis_get_group_variation_data_from_part_file( $dir . 'header.html' );
	if ( is_array( $header ) && isset( $header['attrs'], $header['inner'] ) ) {
		$variations[] = array(
			'name'        => 'wpis-site-header',
			'title'       => __( 'WPIS site header', 'wpis-theme' ),
			'description' => __( 'Site header: title, language switcher, theme toggle and primary navigation. Same blocks as the header template part.', 'wpis-theme' ),
			'attributes'  => $header['attrs'],
			'innerBlocks' => $header['inner'],
			'scope'       => array( 'inserter', 'block', 'transform' ),
			'isActive'    => array( 'className' ),
		);
	}

	$footer = wpis_get_group_variation_data_from_part_file( $dir . 'footer.html' );
	if ( is_array( $footer ) && isset( $footer['attrs'], $footer['inner'] ) ) {
		$variations[] = array(
			'name'        => 'wpis-site-footer',
			'title'       => __( 'WPIS site footer', 'wpis-theme' ),
			'description' => __( 'Site footer: two rows and a trademark line. Same blocks as the footer template part.', 'wpis-theme' ),
			'attributes'  => $footer['attrs'],
			'innerBlocks' => $footer['inner'],
			'scope'       => array( 'inserter', 'block', 'transform' ),
			'isActive'    => array( 'className' ),
		);
	}

	return $variations;
}
add_filter( 'get_block_type_variations', 'wpis_theme_add_group_shell_variations', 20, 2 );

/**
 * Pattern category for bundled patterns (PHP-registered bodies + patterns/*.php fragments).
 */
function wpis_theme_register_pattern_category() {
	if ( class_exists( 'WP_Block_Pattern_Categories_Registry' ) && WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( 'wpis-screens' ) ) {
		return;
	}
	register_block_pattern_category(
		'wpis-screens',
		array(
			'label' => __( 'WPIS screens', 'wpis-theme' ),
		)
	);
}
add_action( 'init', 'wpis_theme_register_pattern_category' );
add_action( 'init', 'wpis_theme_register_screen_patterns', 15 );

/**
 * Add sentiment stripe class to quote feed cards in the main query (template part).
 *
 * @param string $content Rendered block HTML.
 * @param array  $block  Parsed block.
 * @return string
 */
function wpis_theme_quote_feed_card_sentiment( $content, $block ) {
	if ( ( $block['blockName'] ?? '' ) !== 'core/group' ) {
		return $content;
	}
	if ( ( $block['attrs']['metadata']['name'] ?? '' ) !== 'Quote feed card' ) {
		return $content;
	}
	if ( ! in_the_loop() || 'quote' !== get_post_type() ) {
		return $content;
	}
	$terms = get_the_terms( get_the_ID(), 'sentiment' );
	$slug  = '';
	if ( is_array( $terms ) && ! is_wp_error( $terms ) && isset( $terms[0] ) && $terms[0] instanceof \WP_Term ) {
		$s = (string) $terms[0]->slug;
		if ( in_array( $s, array( 'positive', 'negative', 'mixed' ), true ) ) {
			$slug = $s;
		}
	}
	if ( '' === $slug || ! is_string( $content ) || '' === $content ) {
		return $content;
	}
	if ( ! str_contains( $content, 'is-style-wpis-quote-card' ) || preg_match( '/\bwpis-sent-(?:positive|negative|mixed)\b/', $content ) ) {
		return $content;
	}
	$add      = 'wpis-sent-' . $slug;
	$replaced = preg_replace( '/(class=")([^"]*?\bis-style-wpis-quote-card\b)([^"]*")/i', '$1$2 ' . $add . '$3', $content, 1 );
	return is_string( $replaced ) ? $replaced : $content;
}
add_filter( 'render_block', 'wpis_theme_quote_feed_card_sentiment', 10, 2 );

/**
 * Replace placeholder counter in quote feed card when a quote post exposes _wpis_counter.
 *
 * @param string $content Rendered block HTML.
 * @param array  $block  Parsed block.
 * @return string
 */
function wpis_theme_quote_card_counter( $content, $block ) {
	if ( ( $block['blockName'] ?? '' ) !== 'core/paragraph' ) {
		return $content;
	}
	$cname = (string) ( $block['attrs']['className'] ?? '' );
	if ( ! str_contains( $cname, 'is-style-wpis-count-badge' ) && ! str_contains( $cname, 'wpis-count-badge' ) ) {
		return $content;
	}
	if ( ! in_the_loop() || 'quote' !== get_post_type() ) {
		return $content;
	}
	$n = (int) get_post_meta( get_the_ID(), '_wpis_counter', true );
	if ( $n < 1 ) {
		$n = 1;
	}
	$replaced = preg_replace( '/(<p[^>]+>)([^<]+)(<\/p>)/', '${1}×' . esc_html( (string) (int) $n ) . '${3}', $content, 1 );
	return is_string( $replaced ) ? $replaced : $content;
}
add_filter( 'render_block', 'wpis_theme_quote_card_counter', 10, 2 );
