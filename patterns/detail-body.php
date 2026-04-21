<?php
/**
 * Title: Quote detail (page body)
 * Slug: wpis-theme/detail-body
 * Categories: wpis-screens
 * Inserter: yes
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/seed-content.php';
echo wpis_theme_build_sample_seed();
