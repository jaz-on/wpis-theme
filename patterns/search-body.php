<?php
/**
 * Title: Search results demo (page body)
 * Slug: wpis-theme/search-body
 * Categories: wpis-screens
 * Inserter: yes
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once get_template_directory() . '/inc/seed-content.php';
echo wpis_theme_build_search_demo_seed();
