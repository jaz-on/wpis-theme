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
		'mastodon' => '<svg viewBox="0 0 74 79" width="14" height="14" aria-hidden="true" focusable="false" fill="' . esc_attr( $stroke ) . '"><path d="M73.7014 17.4323C72.5616 9.05152 65.1774 2.4469 56.424 1.1671C54.9472 0.950843 49.3518 0.163818 36.3901 0.163818H36.2933C23.3281 0.163818 20.5465 0.950843 19.0697 1.1671C10.56 2.41145 2.78877 8.34604 0.903306 16.826C-0.00357854 21.0022 -0.100361 25.6322 0.068112 29.8793C0.308275 35.9699 0.354874 42.0498 0.91406 48.1156C1.30064 52.1448 1.97502 56.1419 2.93215 60.0769C4.72441 67.3445 11.9795 73.3925 19.0876 75.86C26.6979 78.4332 34.8821 78.8603 42.724 77.0937C43.5866 76.8952 44.4398 76.6647 45.2833 76.4024C47.1867 75.8033 49.4199 75.1332 51.0616 73.9562C51.0841 73.9397 51.1026 73.9184 51.1156 73.8938C51.1286 73.8693 51.1359 73.8421 51.1368 73.8144V67.9366C51.1364 67.9107 51.1302 67.8852 51.1186 67.862C51.1069 67.8388 51.0902 67.8184 51.0695 67.8025C51.0489 67.7865 51.0249 67.7753 50.9994 67.7696C50.9738 67.764 50.9473 67.7641 50.9218 67.7699C45.8976 68.9569 40.7491 69.5519 35.5836 69.5425C26.694 69.5425 24.3031 65.3699 23.6184 63.6327C23.0681 62.1314 22.7186 60.5654 22.5789 58.9744C22.5775 58.9477 22.5825 58.921 22.5934 58.8965C22.6043 58.8721 22.621 58.8505 22.6419 58.8336C22.6629 58.8167 22.6876 58.8049 22.714 58.7992C22.7404 58.7934 22.7678 58.794 22.794 58.8007C27.7345 59.9796 32.799 60.5746 37.8813 60.5733C39.1036 60.5733 40.3223 60.5733 41.5447 60.5414C46.6562 60.3996 52.0437 60.1408 57.0728 59.1694C57.1983 59.1446 57.3237 59.1233 57.4313 59.0914C65.3638 57.5847 72.9128 52.8555 73.6799 40.8799C73.7086 40.4084 73.7803 35.9415 73.7803 35.4523C73.7839 33.7896 74.3216 23.6576 73.7014 17.4323ZM61.4925 47.3144H53.1514V27.107C53.1514 22.8528 51.3591 20.6832 47.7136 20.6832C43.7061 20.6832 41.6988 23.2499 41.6988 28.3194V39.3803H33.4078V28.3194C33.4078 23.2499 31.3969 20.6832 27.3894 20.6832C23.7654 20.6832 21.9552 22.8528 21.9516 27.107V47.3144H13.6176V26.4937C13.6176 22.2395 14.7157 18.8598 16.9118 16.3545C19.1772 13.8552 22.1488 12.5719 25.8373 12.5719C30.1064 12.5719 33.3325 14.1955 35.4832 17.4394L37.5587 20.8853L39.6377 17.4394C41.7884 14.1955 45.0145 12.5719 49.2765 12.5719C52.9614 12.5719 55.9329 13.8552 58.2055 16.3545C60.4017 18.8574 61.4997 22.2371 61.4997 26.4937L61.4925 47.3144Z"/></svg>',
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
