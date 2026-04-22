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
		'mastodon' => '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false" fill="' . esc_attr( $stroke ) . '"><path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.03-1.52.03-1.67.002-.512.167-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z"/></svg>',
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
