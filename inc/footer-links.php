<?php
/**
 * Footer utility links: About / How it works / Privacy plus RSS, Bluesky,
 * Mastodon as tiny SVG icons so the footer aligns with the header chrome.
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline SVG icons used in the footer. Small paths, no external requests.
 *
 * @return array<string, string>
 */
function wpis_theme_footer_icon_svgs() {
	$stroke = 'currentColor';
	return array(
		'rss'      => '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false" fill="none" stroke="' . esc_attr( $stroke ) . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1.5" fill="' . esc_attr( $stroke ) . '" stroke="none"/></svg>',
		'bluesky'  => '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false" fill="' . esc_attr( $stroke ) . '"><path d="M6.3 4.6c2.6 2 5.4 6 6.4 8.1 1-2.2 3.8-6.2 6.4-8.1 1.9-1.4 5-2.5 5 1 0 .7-.4 5.9-.7 6.7-.9 3-4 3.8-6.7 3.3 4.8.8 6 3.5 3.4 6.2-5 5.2-7.2-1.3-7.7-3l-.1-.4-.1.4C11.8 20.7 9.6 27.2 4.6 22c-2.6-2.7-1.4-5.4 3.4-6.2-2.7.5-5.8-.3-6.7-3.3C1 11.7.6 6.5.6 5.8c0-3.5 3.1-2.4 5 0 .2.2.4.4.7.8z"/></svg>',
		'mastodon' => '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false" fill="' . esc_attr( $stroke ) . '"><path d="M21.3 6.8c-.3-2.3-2.3-4.1-4.7-4.4C16.2 2.3 14.2 2 11.9 2h-.1c-2.3 0-4.3.3-4.7.4-2.4.3-4.4 2.1-4.7 4.4-.3 2.1-.3 4.3 0 6.4.4 3 3.1 5.2 6.2 5.5 1 .1 1.8.1 2.4.1.6 0 1.5 0 2.4-.1l.1 1.8c1-.2 1.9-.7 2.7-1.3.3-.2.6-.4.9-.7.2-.2.2-.6-.1-.7l-.4-.1c-.7 0-1.4.2-2.1.3-1.1.2-2.2.2-3.3.1-.2 0-.4-.1-.4-.3 0-.2.1-.4.3-.4 1.1-.1 2.2-.2 3.2-.4 1.7-.3 3.4-.8 4.1-2.9.2-.7.3-1.4.4-2.1.1-1.5.1-3.2-.2-4.9zM17.5 12h-1.7V8.1c0-.8-.3-1.3-1-1.3-.8 0-1.1.5-1.1 1.5V10h-1.7V8.3c0-1-.4-1.5-1.1-1.5-.7 0-1 .5-1 1.3V12H8V8c0-.8.2-1.5.6-1.9.4-.5 1-.7 1.7-.7.8 0 1.5.3 1.9.9l.4.7.4-.7c.4-.6 1-.9 1.9-.9.7 0 1.3.2 1.7.7.4.5.6 1.1.6 1.9V12z"/></svg>',
	);
}

/**
 * @return string
 */
function wpis_theme_footer_links_shortcode() {
	$icons = wpis_theme_footer_icon_svgs();
	$links = array(
		array(
			'url'   => '/about/',
			'label' => __( 'About', 'wpis-theme' ),
			'icon'  => '',
			'rel'   => '',
		),
		array(
			'url'   => '/how-it-works/',
			'label' => __( 'How it works', 'wpis-theme' ),
			'icon'  => '',
			'rel'   => '',
		),
		array(
			'url'   => '/privacy/',
			'label' => __( 'Privacy', 'wpis-theme' ),
			'icon'  => '',
			'rel'   => '',
		),
		array(
			'url'   => '/feed/',
			'label' => __( 'RSS', 'wpis-theme' ),
			'icon'  => $icons['rss'],
			'rel'   => 'alternate',
		),
		array(
			'url'   => 'https://bsky.app/profile/wpis.bsky.social',
			'label' => __( 'Bluesky', 'wpis-theme' ),
			'icon'  => $icons['bluesky'],
			'rel'   => 'me noopener',
			'blank' => true,
		),
		array(
			'url'   => 'https://mastodon.social/@wpis',
			'label' => __( 'Mastodon', 'wpis-theme' ),
			'icon'  => $icons['mastodon'],
			'rel'   => 'me noopener',
			'blank' => true,
		),
	);

	$items = array();
	foreach ( $links as $link ) {
		$is_icon = '' !== $link['icon'];
		$classes = 'wpis-footer-link' . ( $is_icon ? ' wpis-footer-link--icon' : '' );
		$target  = ! empty( $link['blank'] ) ? ' target="_blank"' : '';
		$rel     = '' !== $link['rel'] ? ' rel="' . esc_attr( (string) $link['rel'] ) . '"' : '';
		$label   = esc_html( (string) $link['label'] );
		$inner   = $is_icon
			? ( '<span class="wpis-footer-link-icon" aria-hidden="true">' . $link['icon'] . '</span><span class="screen-reader-text">' . $label . '</span>' )
			: $label;
		$items[] = sprintf(
			'<a class="%1$s" href="%2$s"%3$s%4$s%5$s>%6$s</a>',
			esc_attr( $classes ),
			esc_url( (string) $link['url'] ),
			$target,
			$rel,
			$is_icon ? ' title="' . esc_attr( (string) $link['label'] ) . '"' : '',
			$inner
		);
	}

	return '<p class="is-style-wpis-footer-top-right wpis-footer-links">' . implode( '', $items ) . '</p>';
}
add_shortcode( 'wpis_footer_links', 'wpis_theme_footer_links_shortcode' );
