<?php
/**
 * Polylang language switcher (header chrome).
 *
 * Compatible with Polylang (free): uses `pll_the_languages()` on the real frontend. That API often
 * returns an empty list in the Site Editor and other REST contexts; we then fall back to
 * `pll_languages_list()` + `pll_home_url()` so the control still shows language links in the editor
 * (home URL per language only in that case — correct translation URLs on the live site).
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build language rows when `pll_the_languages( raw )` is empty (admin, REST, Site Editor preview).
 *
 * @return list<array{slug:string,url:string,current_lang?:bool}>
 */
function wpis_theme_polylang_switcher_raw_fallback() {
	if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_home_url' ) ) {
		return array();
	}

	$slugs = pll_languages_list( array( 'hide_empty' => 0 ) );
	if ( ! is_array( $slugs ) || array() === $slugs ) {
		return array();
	}

	$current = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : '';
	$rows    = array();

	foreach ( $slugs as $slug ) {
		if ( is_array( $slug ) && isset( $slug['slug'] ) ) {
			$slug = (string) $slug['slug'];
		}
		if ( ! is_string( $slug ) || '' === $slug ) {
			continue;
		}
		$rows[] = array(
			'slug'         => $slug,
			'url'          => pll_home_url( $slug ),
			'current_lang' => ( $slug === $current ),
		);
	}

	return $rows;
}

/**
 * HTML for the site language control: Polylang when active, a minimal home link otherwise.
 *
 * @return string
 */
function wpis_theme_get_lang_switcher_html() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return wpis_theme_get_lang_switcher_fallback_html();
	}

	$raw = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_no_translation' => 0,
			'hide_if_empty'          => 0,
			'echo'                   => 0,
		)
	);
	if ( ! is_array( $raw ) || array() === $raw ) {
		$raw = wpis_theme_polylang_switcher_raw_fallback();
	}
	if ( ! is_array( $raw ) || array() === $raw ) {
		return wpis_theme_get_lang_switcher_fallback_html();
	}

	$current = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : '';
	$parts   = array();

	foreach ( $raw as $lang ) {
		if ( ! is_array( $lang ) || empty( $lang['url'] ) ) {
			continue;
		}
		$slug = isset( $lang['slug'] ) ? (string) $lang['slug'] : '';
		if ( '' === $slug ) {
			continue;
		}

		$label = strtoupper( $slug );
		if ( strlen( $label ) > 3 ) {
			$label = strtoupper( mb_substr( $slug, 0, 2 ) );
		}

		$is_current = ( $current === $slug )
			|| ( ! empty( $lang['current_lang'] ) )
			|| ( ! empty( $lang['active'] ) )
			|| ( ! empty( $lang['current'] ) );
		$attrs      = $is_current ? ' class="active" aria-current="true"' : '';

		$parts[] = sprintf(
			'<a href="%1$s"%2$s>%3$s</a>',
			esc_url( (string) $lang['url'] ),
			$attrs,
			esc_html( $label )
		);
	}

	if ( array() === $parts ) {
		return wpis_theme_get_lang_switcher_fallback_html();
	}

	return '<p class="is-style-wpis-lang-switcher">' . implode( '', $parts ) . '</p>';
}

/**
 * When Polylang is inactive or not configured, keep a single home link so layout stays valid.
 *
 * @return string
 */
function wpis_theme_get_lang_switcher_fallback_html() {
	$url   = home_url( '/' );
	$label = 'EN';
	$local = get_bloginfo( 'language' );
	if ( is_string( $local ) && preg_match( '/^[a-z]{2}/i', $local, $m ) ) {
		$label = strtoupper( $m[0] );
	}

	return '<p class="is-style-wpis-lang-switcher"><a href="' . esc_url( $url ) . '" class="active" aria-current="true">' . esc_html( $label ) . '</a></p>';
}

/**
 * Shortcode: language switcher for parts/header (Polylang).
 *
 * @return string
 */
function wpis_theme_lang_switcher_shortcode() {
	return wpis_theme_get_lang_switcher_html();
}
add_shortcode( 'wpis_lang_switcher', 'wpis_theme_lang_switcher_shortcode' );
