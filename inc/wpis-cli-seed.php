<?php
/**
 * WP-CLI commands for WPIS demo content (content/html/).
 *
 * @package WPIS
 */

if ( ! defined( 'WP_CLI' ) || ! constant( 'WP_CLI' ) ) {
	return;
}

WP_CLI::add_command(
	'wpis-seed',
	/**
	 * @param list<string>            $args       Positional: clean | import | reset.
	 * @param array<string, bool|null> $assoc_args Flags.
	 */
	function ( array $args, array $assoc_args ): void {
		$sub = isset( $args[0] ) ? $args[0] : '';
		switch ( $sub ) {
			case 'clean':
				$force = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );
				$n     = wpis_theme_setup_clean_manifest_pages( $force );
				wpis_theme_setup_reset_reading_after_clean();
				$verb = $force ? 'Deleted' : 'Trashed';
				WP_CLI::success( sprintf( '%s %d demo page(s).', $verb, $n ) );
				break;

			case 'import':
				$no_sync    = \WP_CLI\Utils\get_flag_value( $assoc_args, 'no-sync', false );
				$no_reading = \WP_CLI\Utils\get_flag_value( $assoc_args, 'no-reading', false );
				$no_menu    = \WP_CLI\Utils\get_flag_value( $assoc_args, 'no-menu', false );
				$ids        = wpis_theme_setup_run(
					array(
						'sync_content' => ! $no_sync,
						'set_reading'  => ! $no_reading,
						'ensure_menu'  => ! $no_menu,
					)
				);
				WP_CLI::success( sprintf( 'Demo import finished (%d slugs).', count( $ids ) ) );
				break;

			case 'reset':
				$force = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );
				$n     = wpis_theme_setup_clean_manifest_pages( $force );
				wpis_theme_setup_reset_reading_after_clean();
				WP_CLI::log( sprintf( 'Removed %d demo page(s).', $n ) );

				$no_sync    = \WP_CLI\Utils\get_flag_value( $assoc_args, 'no-sync', false );
				$no_reading = \WP_CLI\Utils\get_flag_value( $assoc_args, 'no-reading', false );
				$no_menu    = \WP_CLI\Utils\get_flag_value( $assoc_args, 'no-menu', false );
				$ids        = wpis_theme_setup_run(
					array(
						'sync_content' => ! $no_sync,
						'set_reading'  => ! $no_reading,
						'ensure_menu'  => ! $no_menu,
					)
				);
				WP_CLI::success( sprintf( 'Reset finished (%d slugs).', count( $ids ) ) );
				break;

			default:
				WP_CLI::error( 'Usage: wp wpis-seed clean|import|reset [--force] [--no-sync] [--no-reading] [--no-menu]' );
		}
	},
	array(
		'shortdesc' => 'Import or remove WPIS demo pages from content/html/.',
		'synopsis'  => array(
			array(
				'type'        => 'positional',
				'name'        => 'action',
				'description' => 'clean, import, or reset',
				'optional'    => false,
			),
			array(
				'type'        => 'flag',
				'name'        => 'force',
				'description' => 'With clean/reset: permanently delete pages instead of trash.',
			),
			array(
				'type'        => 'flag',
				'name'        => 'no-sync',
				'description' => 'With import/reset: do not overwrite post_content for existing pages.',
			),
			array(
				'type'        => 'flag',
				'name'        => 'no-reading',
				'description' => 'Do not set static front page to Home.',
			),
			array(
				'type'        => 'flag',
				'name'        => 'no-menu',
				'description' => 'Do not rebuild WPIS Primary menu.',
			),
		),
	)
);
