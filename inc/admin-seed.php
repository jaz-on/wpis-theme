<?php
/**
 * Admin screen: import or remove manifest pages (same flow as `wp wpis-seed`).
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slug for Appearance → WPIS import (`themes.php?page=...`).
 */
if ( ! defined( 'WPIS_THEME_IMPORT_PAGE' ) ) {
	define( 'WPIS_THEME_IMPORT_PAGE', 'wpis-import' );
}

/**
 * Admin URL for the WPIS import screen.
 *
 * @return string
 */
function wpis_theme_import_admin_url() {
	return admin_url( 'themes.php?page=' . WPIS_THEME_IMPORT_PAGE );
}

/**
 * @return void
 */
function wpis_theme_register_seed_admin_page() {
	add_theme_page(
		__( 'WPIS import', 'wpis-theme' ),
		__( 'WPIS import', 'wpis-theme' ),
		'manage_options',
		WPIS_THEME_IMPORT_PAGE,
		'wpis_theme_render_seed_admin_page'
	);
}
add_action( 'admin_menu', 'wpis_theme_register_seed_admin_page' );

/**
 * @return void
 */
function wpis_theme_handle_seed_admin_post() {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || empty( $_POST['wpis_seed_action'] ) ) {
		return;
	}
	$pg = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( WPIS_THEME_IMPORT_PAGE !== $pg ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'wpis-theme' ) );
	}
	check_admin_referer( 'wpis_theme_seed' );

	$action   = sanitize_key( (string) wp_unslash( $_POST['wpis_seed_action'] ) );
	$redirect = wpis_theme_import_admin_url();

	switch ( $action ) {
		case 'import':
			$ids      = wpis_theme_setup_run(
				array(
					'sync_content' => ! empty( $_POST['wpis_sync'] ),
					'set_reading'  => ! empty( $_POST['wpis_reading'] ),
				)
			);
			$count    = is_array( $ids ) ? count( $ids ) : 0;
			$redirect = add_query_arg(
				array(
					'page'      => WPIS_THEME_IMPORT_PAGE,
					'wpismsg'   => 'imported',
					'wpiscount' => (string) (int) $count,
				),
				admin_url( 'themes.php' )
			);
			break;
		case 'clean':
			$force   = ! empty( $_POST['wpis_force'] );
			$removed = wpis_theme_setup_clean_manifest_pages( $force );
			wpis_theme_setup_reset_reading_after_clean();
			$redirect = add_query_arg(
				array(
					'page'      => WPIS_THEME_IMPORT_PAGE,
					'wpismsg'   => 'cleaned',
					'wpiscount' => (string) (int) $removed,
					'wpisforce' => $force ? '1' : '0',
				),
				admin_url( 'themes.php' )
			);
			break;
		case 'reset':
			$force   = ! empty( $_POST['wpis_force_reset'] );
			$cleaned = wpis_theme_setup_clean_manifest_pages( $force );
			wpis_theme_setup_reset_reading_after_clean();
			$ids      = wpis_theme_setup_run(
				array(
					'sync_content' => ! empty( $_POST['wpis_reset_sync'] ),
					'set_reading'  => ! empty( $_POST['wpis_reset_reading'] ),
				)
			);
			$count    = is_array( $ids ) ? count( $ids ) : 0;
			$redirect = add_query_arg(
				array(
					'page'        => 'wpis-theme-seed',
					'wpismsg'     => 'reset',
					'wpiscount'   => (string) (int) $count,
					'wpiscleaned' => (string) (int) $cleaned,
					'wpisforce'   => $force ? '1' : '0',
				),
				admin_url( 'themes.php' )
			);
			break;
		default:
			return;
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'load-appearance_page_' . WPIS_THEME_IMPORT_PAGE, 'wpis_theme_handle_seed_admin_post' );

/**
 * @return void
 */
function wpis_theme_admin_seed_notices() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $pagenow;
	$pg = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( 'themes.php' !== $pagenow || WPIS_THEME_IMPORT_PAGE !== $pg ) {
		return;
	}
	$msg = isset( $_GET['wpismsg'] ) ? sanitize_key( (string) wp_unslash( $_GET['wpismsg'] ) ) : '';
	if ( '' === $msg ) {
		return;
	}
	$count = isset( $_GET['wpiscount'] ) ? (int) $_GET['wpiscount'] : 0;
	$cle   = isset( $_GET['wpiscleaned'] ) ? (int) $_GET['wpiscleaned'] : 0;
	$text  = '';
	if ( 'plugin_inactive' === $msg ) {
		$text = __( 'The WordPress Is… Core plugin (wpis-plugin) must be active to manage sample quotes.', 'wpis-theme' );
	} elseif ( 'starter_seeded' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of quotes */
			_n( 'Imported %d starter quote (unflagged sample set).', 'Imported %d starter quotes (unflagged sample set).', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'demo_seeded' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of quotes */
			_n( 'Imported %d demo quote (flagged for removal with wp wpis seed_demo --erase or the button below).', 'Imported %d demo quotes (flagged for removal with wp wpis seed_demo --erase or the button below).', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'starter_erased' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of quotes removed */
			_n( 'Removed %d starter quote.', 'Removed %d starter quotes.', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'demo_erased' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of quotes removed */
			_n( 'Removed %d demo quote.', 'Removed %d demo quotes.', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'imported' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of pages touched */
			_n( 'WPIS import finished (%d page in the manifest).', 'WPIS import finished (%d pages in the manifest).', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'cleaned' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of pages removed */
			_n( 'Removed %d manifest page.', 'Removed %d manifest pages.', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'reset' === $msg ) {
		$text = sprintf(
			/* translators: 1: pages removed, 2: re-imported slugs count */
			__( 'Reset finished. Removed %1$d page(s) then re-imported %2$d slugs.', 'wpis-theme' ),
			$cle,
			$count
		);
	}
	if ( '' !== $text ) {
		$notice_class = 'plugin_inactive' === $msg ? 'notice-warning' : 'notice-success';
		printf(
			'<div class="notice %s is-dismissible"><p>%s</p></div>',
			esc_attr( $notice_class ),
			esc_html( $text )
		);
	}
}
add_action( 'admin_notices', 'wpis_theme_admin_seed_notices' );

/**
 * @return void
 */
function wpis_theme_render_seed_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$manifest = wpis_theme_setup_get_manifest();
	$slugs    = array();
	foreach ( $manifest as $row ) {
		$slugs[] = isset( $row['slug'] ) ? (string) $row['slug'] : '';
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p>
			<?php esc_html_e( 'Create or update manifest pages from the theme under content/html/. This matches WP-CLI: wp wpis-seed import, clean, or reset (see the theme README).', 'wpis-theme' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'CLI flags: import uses --no-sync and --no-reading when you turn off the matching checkboxes. clean --force is “Delete permanently”.', 'wpis-theme' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'The site menu is the Navigation block in the Header template part (Appearance → Editor). This theme does not use classic Appearance → Menus.', 'wpis-theme' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Slugs in this manifest', 'wpis-theme' ); ?>:</strong>
			<?php echo esc_html( implode( ', ', array_filter( $slugs ) ) ); ?>
		</p>

		<h2 class="title"><?php esc_html_e( 'Sample quotes (plugin)', 'wpis-theme' ); ?></h2>
		<?php
		$wpis_core = class_exists( '\WPIS\Core\CLI\StarterSeeder' ) && class_exists( '\WPIS\Core\CLI\DemoSeeder' );
		?>
		<?php if ( $wpis_core ) : ?>
			<p class="description">
				<?php esc_html_e( 'These actions call the same code as: wp wpis seed_starter, wp wpis seed_starter --erase, wp wpis seed_demo, wp wpis seed_demo --erase. Starter set is not flagged; demo set uses meta for bulk removal.', 'wpis-theme' ); ?>
			</p>
			<p>
				<form method="post" action="<?php echo esc_url( wpis_theme_import_admin_url() ); ?>" style="display: inline; margin-right: 12px;">
					<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
					<input type="hidden" name="wpis_seed_action" value="quote_seed_starter" />
					<?php submit_button( __( 'Import starter quotes', 'wpis-theme' ), 'secondary', 'submit', false ); ?>
				</form>
				<form method="post" action="<?php echo esc_url( wpis_theme_import_admin_url() ); ?>" style="display: inline; margin-right: 12px;" onsubmit="return window.confirm( '<?php echo esc_js( __( 'Remove all starter-tagged sample quotes? This cannot be undone.', 'wpis-theme' ) ); ?>' );">
					<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
					<input type="hidden" name="wpis_seed_action" value="quote_erase_starter" />
					<?php submit_button( __( 'Remove starter quotes', 'wpis-theme' ), 'delete', 'submit', false, array( 'style' => 'vertical-align: middle;' ) ); ?>
				</form>
			</p>
			<p>
				<form method="post" action="<?php echo esc_url( wpis_theme_import_admin_url() ); ?>" style="display: inline; margin-right: 12px;">
					<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
					<input type="hidden" name="wpis_seed_action" value="quote_seed_demo" />
					<?php submit_button( __( 'Import demo quotes', 'wpis-theme' ), 'secondary', 'submit', false ); ?>
				</form>
				<form method="post" action="<?php echo esc_url( wpis_theme_import_admin_url() ); ?>" style="display: inline;" onsubmit="return window.confirm( '<?php echo esc_js( __( 'Remove all demo-tagged sample quotes? This cannot be undone.', 'wpis-theme' ) ); ?>' );">
					<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
					<input type="hidden" name="wpis_seed_action" value="quote_erase_demo" />
					<?php submit_button( __( 'Remove demo quotes', 'wpis-theme' ), 'delete', 'submit', false, array( 'style' => 'vertical-align: middle;' ) ); ?>
				</form>
			</p>
		<?php else : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'Activate the WordPress Is… Core plugin (wpis-plugin) to import or remove sample quotes here, or use WP-CLI: wp wpis seed_starter, wp wpis seed_demo.', 'wpis-theme' ); ?></p></div>
		<?php endif; ?>

		<h2 class="title"><?php esc_html_e( 'Import', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Default options match: wp wpis-seed import (sync on, reading on).', 'wpis-theme' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( wpis_theme_import_admin_url() ); ?>">
			<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
			<input type="hidden" name="wpis_seed_action" value="import" />
			<fieldset>
				<p>
					<label>
						<input type="checkbox" name="wpis_sync" value="1" checked />
						<?php esc_html_e( 'Sync: overwrite page content from theme files for existing manifest pages (off = wpis-seed import --no-sync)', 'wpis-theme' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="wpis_reading" value="1" checked />
						<?php esc_html_e( 'Set static front page to Home (off = wpis-seed import --no-reading)', 'wpis-theme' ); ?>
					</label>
				</p>
			</fieldset>
			<?php
			submit_button( __( 'Import pages', 'wpis-theme' ) );
			?>
		</form>

		<h2 class="title"><?php esc_html_e( 'Remove manifest pages', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Same as: wp wpis-seed clean. Trash by default, or delete permanently to match --force.', 'wpis-theme' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( wpis_theme_import_admin_url() ); ?>" onsubmit="return window.confirm( '<?php echo esc_js( __( 'Move manifest pages to the trash (or delete permanently if checked)?', 'wpis-theme' ) ); ?>' );">
			<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
			<input type="hidden" name="wpis_seed_action" value="clean" />
			<p>
				<label>
					<input type="checkbox" name="wpis_force" value="1" />
					<?php esc_html_e( 'Delete permanently: wpis-seed clean --force', 'wpis-theme' ); ?>
				</label>
			</p>
			<?php
			submit_button( __( 'Remove pages', 'wpis-theme' ), 'delete' );
			?>
		</form>

		<h2 class="title"><?php esc_html_e( 'Reset (remove then import again)', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Same as: wp wpis-seed reset. Removes all manifest pages, then imports again with the checkboxes below.', 'wpis-theme' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( wpis_theme_import_admin_url() ); ?>" onsubmit="return window.confirm( '<?php echo esc_js( __( 'This will remove manifest pages and import again. Continue?', 'wpis-theme' ) ); ?>' );">
			<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
			<input type="hidden" name="wpis_seed_action" value="reset" />
			<fieldset>
				<p>
					<label>
						<input type="checkbox" name="wpis_force_reset" value="1" />
						<?php esc_html_e( 'Delete permanently when cleaning (not recommended unless you know why)', 'wpis-theme' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="wpis_reset_sync" value="1" checked />
						<?php esc_html_e( 'Sync on re-import (off = --no-sync after clean)', 'wpis-theme' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="wpis_reset_reading" value="1" checked />
						<?php esc_html_e( 'Set static front page to Home (off = --no-reading after import)', 'wpis-theme' ); ?>
					</label>
				</p>
			</fieldset>
			<?php
			submit_button( __( 'Run reset', 'wpis-theme' ), 'primary' );
			?>
		</form>
	</div>
	<?php
}
