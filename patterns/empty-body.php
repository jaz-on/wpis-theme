<?php
/**
 * Title: Empty state / 404 (page body)
 * Slug: wpis-theme/empty-body
 * Categories: wpis-screens
 * Inserter: no
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
echo wpis_theme_get_content_html( 'empty.html' );
