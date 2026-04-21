<?php
/**
 * WPIS block theme bootstrap.
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/class-theme-setup.php';

/**
 * Path to a single HTML seed under content/html/ (no duplicate copies elsewhere).
 *
 * @param string $filename Basename, e.g. home.html.
 */
function wpis_theme_get_content_html( string $filename ): string {
	$path = get_template_directory() . '/content/html/' . ltrim( $filename, '/' );
	if ( ! is_readable( $path ) ) {
		return '';
	}
	$raw = file_get_contents( $path );
	return is_string( $raw ) ? $raw : '';
}

/**
 * Register navigation location and run idempotent setup when the theme is activated.
 */
function wpis_theme_setup_theme() {
	register_nav_menus(
		array(
			'primary' => __( 'WPIS Primary', 'wpis-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'wpis_theme_setup_theme' );

/**
 * Create seeded pages, reading options and primary menu once per site rules.
 */
function wpis_theme_after_switch_theme() {
	WPIS_Theme_Setup::run();
}
add_action( 'after_switch_theme', 'wpis_theme_after_switch_theme' );

/**
 * Enqueue front-end and editor styles; theme toggle script.
 */
function wpis_theme_enqueue_assets() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	$css_path = $theme_dir . '/assets/css/wpis-chrome.css';
	if ( is_readable( $css_path ) ) {
		wp_enqueue_style(
			'wpis-chrome',
			$theme_uri . '/assets/css/wpis-chrome.css',
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
			false
		);
	}
}
add_action( 'wp_enqueue_scripts', 'wpis_theme_enqueue_assets' );

/**
 * Load front-end chrome styles in the block editor.
 */
function wpis_theme_editor_styles() {
	add_editor_style( 'assets/css/wpis-chrome.css' );
}
add_action( 'after_setup_theme', 'wpis_theme_editor_styles' );

/**
 * Register core block variations aligned with layout utility classes.
 */
function wpis_theme_register_block_variations() {
	if ( ! function_exists( 'register_block_variation' ) ) {
		return;
	}

	$variations = array(
		array(
			'block'  => 'core/group',
			'name'   => 'wpis-hero',
			'title'  => __( 'WPIS hero', 'wpis-theme' ),
			'class'  => 'hero',
		),
		array(
			'block'  => 'core/group',
			'name'   => 'wpis-feed',
			'title'  => __( 'WPIS feed', 'wpis-theme' ),
			'class'  => 'feed',
		),
		array(
			'block'  => 'core/group',
			'name'   => 'wpis-detail-container',
			'title'  => __( 'WPIS detail container', 'wpis-theme' ),
			'class'  => 'detail-container',
		),
		array(
			'block'  => 'core/paragraph',
			'name'   => 'wpis-eyebrow',
			'title'  => __( 'WPIS eyebrow', 'wpis-theme' ),
			'class'  => 'eyebrow',
		),
		array(
			'block'  => 'core/heading',
			'name'   => 'wpis-hero-title',
			'title'  => __( 'WPIS hero title', 'wpis-theme' ),
			'class'  => 'hero-title',
			'level'  => 1,
		),
		array(
			'block'  => 'core/heading',
			'name'   => 'wpis-detail-quote',
			'title'  => __( 'WPIS detail quote', 'wpis-theme' ),
			'class'  => 'detail-quote',
			'level'  => 1,
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
 * Pattern category for full-page body patterns and utilities.
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
