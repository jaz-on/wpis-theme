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
 * Read a seed file from content/html/ (single source for patterns and activation).
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
		'var(--wp--preset--color--positive)'     => 'var(--positive)',
		'var(--wp--preset--color--negative)'   => 'var(--negative)',
		'var(--wp--preset--color--mixed)'       => 'var(--mixed)',
		'var(--wp--preset--color--paper)'       => 'var(--paper)',
		'var(--wp--preset--color--muted)'       => 'var(--muted)',
		'var(--wp--preset--color--bg)'          => 'var(--bg)',
		'var(--wp--preset--color--accent)'     => 'var(--accent)',
		'var(--wp--preset--color--ink)'        => 'var(--ink)',
	);
	return str_replace( array_keys( $map ), array_values( $map ), $content );
}
add_filter( 'the_content', 'wpis_theme_semantic_colors_in_content', 5 );

/**
 * Theme supports: menus, editor styles.
 */
function wpis_theme_setup() {
	register_nav_menus(
		array(
			'primary' => __( 'WPIS Primary', 'wpis-theme' ),
		)
	);
	add_editor_style( 'assets/css/wpis-global.css' );
}
add_action( 'after_setup_theme', 'wpis_theme_setup' );

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

/**
 * Seed pages, reading options and menu when the theme is activated.
 */
function wpis_theme_after_switch_theme() {
	wpis_theme_setup_run();
}
add_action( 'after_switch_theme', 'wpis_theme_after_switch_theme' );

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
	if ( is_readable( $feed_js ) ) {
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
		array(
			'block' => 'core/heading',
			'name'  => 'wpis-hero-title',
			'title' => __( 'WPIS hero title', 'wpis-theme' ),
			'class' => 'hero-title',
			'level' => 1,
		),
		array(
			'block' => 'core/heading',
			'name'  => 'wpis-detail-quote',
			'title' => __( 'WPIS detail quote', 'wpis-theme' ),
			'class' => 'detail-quote',
			'level' => 1,
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
 * Pattern category for bundled patterns (see patterns/*.php).
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
