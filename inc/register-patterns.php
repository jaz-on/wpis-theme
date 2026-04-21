<?php
/**
 * Register WPIS block patterns that mirror content/html (avoids duplicate pattern PHP files).
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Screen body patterns: same markup as files under content/html/.
 *
 * @return list<array{slug:string,title:string,file:string}>
 */
function wpis_theme_get_screen_pattern_definitions(): array {
	return array(
		array(
			'slug'  => 'home-body',
			'title' => __( 'Home (page body)', 'wpis-theme' ),
			'file'  => 'home.html',
		),
		array(
			'slug'  => 'taxonomy-body',
			'title' => __( 'Taxonomy (page body)', 'wpis-theme' ),
			'file'  => 'security.html',
		),
		array(
			'slug'  => 'explore-body',
			'title' => __( 'Explore (page body)', 'wpis-theme' ),
			'file'  => 'explore.html',
		),
		array(
			'slug'  => 'profile-body',
			'title' => __( 'Profile (page body)', 'wpis-theme' ),
			'file'  => 'profile.html',
		),
		array(
			'slug'  => 'search-body',
			'title' => __( 'Search (page body)', 'wpis-theme' ),
			'file'  => 'search-demo.html',
		),
		array(
			'slug'  => 'how-body',
			'title' => __( 'How it works (page body)', 'wpis-theme' ),
			'file'  => 'how-it-works.html',
		),
		array(
			'slug'  => 'submit-body',
			'title' => __( 'Submit (page body)', 'wpis-theme' ),
			'file'  => 'submit.html',
		),
		array(
			'slug'  => 'detail-body',
			'title' => __( 'Quote detail (page body)', 'wpis-theme' ),
			'file'  => 'sample.html',
		),
		array(
			'slug'  => 'about-body',
			'title' => __( 'About (page body)', 'wpis-theme' ),
			'file'  => 'about.html',
		),
		array(
			'slug'  => 'confirm-body',
			'title' => __( 'Submitted (page body)', 'wpis-theme' ),
			'file'  => 'submitted.html',
		),
		array(
			'slug'  => 'empty-body',
			'title' => __( 'Empty state (page body)', 'wpis-theme' ),
			'file'  => 'empty.html',
		),
	);
}

/**
 * Register programmatic screen patterns (init).
 */
function wpis_theme_register_screen_patterns(): void {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	foreach ( wpis_theme_get_screen_pattern_definitions() as $def ) {
		$content = wpis_theme_get_content_html( $def['file'] );
		if ( '' === $content ) {
			continue;
		}
		register_block_pattern(
			'wpis-theme/' . $def['slug'],
			array(
				'title'      => $def['title'],
				'categories' => array( 'wpis-screens' ),
				'content'    => $content,
				'inserter'   => true,
			)
		);
	}
}
