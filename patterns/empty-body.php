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

require_once get_template_directory() . '/inc/seed-content.php';
echo wpis_theme_build_empty_seed();
