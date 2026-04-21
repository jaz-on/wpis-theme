<?php
/**
 * Title: Taxonomy archive (page body)
 * Slug: wpis-theme/taxonomy-body
 * Categories: wpis-screens
 * Inserter: yes
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once get_template_directory() . '/inc/seed-content.php';
echo wpis_theme_build_security_seed();
