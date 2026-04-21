#!/usr/bin/env php
<?php
/**
 * Demo seed runner without WP-CLI (requires WordPress loaded).
 *
 * Usage:
 *   WP_LOAD_PATH=/path/to/wordpress/wp-load.php php tools/seed-demo.php clean [--force]
 *   WP_LOAD_PATH=... php tools/seed-demo.php import [--no-sync] [--no-reading] [--no-menu]
 *   WP_LOAD_PATH=... php tools/seed-demo.php reset [--force] [--no-sync] [--no-reading] [--no-menu]
 *
 * @package WPIS
 */

declare( strict_types=1 );

$wp_load = getenv( 'WP_LOAD_PATH' );
if ( ! is_string( $wp_load ) || '' === $wp_load || ! is_readable( $wp_load ) ) {
	fwrite( STDERR, "Set WP_LOAD_PATH to a readable wp-load.php (WordPress root).\n" );
	exit( 1 );
}

require_once $wp_load;

if ( ! function_exists( 'wpis_theme_setup_run' ) ) {
	fwrite( STDERR, "Active theme must be wpis-theme (wpis_theme_setup_run missing).\n" );
	exit( 1 );
}

$argv_rest = array_slice( $argv, 1 );
$sub       = $argv_rest[0] ?? '';
$flags     = array(
	'force'      => in_array( '--force', $argv_rest, true ),
	'no_sync'    => in_array( '--no-sync', $argv_rest, true ),
	'no_reading' => in_array( '--no-reading', $argv_rest, true ),
	'no_menu'    => in_array( '--no-menu', $argv_rest, true ),
);

switch ( $sub ) {
	case 'clean':
		$n = wpis_theme_setup_clean_manifest_pages( $flags['force'] );
		wpis_theme_setup_reset_reading_after_clean();
		$verb = $flags['force'] ? 'Deleted' : 'Trashed';
		echo "{$verb} {$n} demo page(s).\n";
		exit( 0 );

	case 'import':
		$ids = wpis_theme_setup_run(
			array(
				'sync_content' => ! $flags['no_sync'],
				'set_reading'  => ! $flags['no_reading'],
				'ensure_menu'  => ! $flags['no_menu'],
			)
		);
		echo 'Demo import finished (' . count( $ids ) . " slugs).\n";
		exit( 0 );

	case 'reset':
		$n = wpis_theme_setup_clean_manifest_pages( $flags['force'] );
		wpis_theme_setup_reset_reading_after_clean();
		echo "Removed {$n} demo page(s).\n";
		$ids = wpis_theme_setup_run(
			array(
				'sync_content' => ! $flags['no_sync'],
				'set_reading'  => ! $flags['no_reading'],
				'ensure_menu'  => ! $flags['no_menu'],
			)
		);
		echo 'Reset finished (' . count( $ids ) . " slugs).\n";
		exit( 0 );

	default:
		fwrite( STDERR, "Usage: WP_LOAD_PATH=/path/to/wp-load.php php tools/seed-demo.php clean|import|reset [flags]\n" );
		fwrite( STDERR, "Flags: --force --no-sync --no-reading --no-menu\n" );
		exit( 1 );
}
