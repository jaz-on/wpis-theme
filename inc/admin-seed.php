<?php
/**
 * Admin screen: import or remove demo pages (same flow as `wp wpis-seed`).
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return void
 */
function wpis_theme_register_seed_admin_page() {
	add_theme_page(
		__( 'Import demo', 'wpis-theme' ),
		__( 'Import demo', 'wpis-theme' ),
		'manage_options',
		'wpis-theme-seed',
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
	if ( 'wpis-theme-seed' !== $pg ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'wpis-theme' ) );
	}
	check_admin_referer( 'wpis_theme_seed' );

	$action   = sanitize_key( (string) wp_unslash( $_POST['wpis_seed_action'] ) );
	$redirect = add_query_arg( 'page', 'wpis-theme-seed', admin_url( 'themes.php' ) );

	switch ( $action ) {
		case 'import':
			$ids      = wpis_theme_setup_run(
				array(
					'sync_content' => ! empty( $_POST['wpis_sync'] ),
					'set_reading'  => ! empty( $_POST['wpis_reading'] ),
					'ensure_menu'  => ! empty( $_POST['wpis_menu'] ),
				)
			);
			$count    = is_array( $ids ) ? count( $ids ) : 0;
			$redirect = add_query_arg(
				array(
					'page'      => 'wpis-theme-seed',
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
					'page'      => 'wpis-theme-seed',
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
					'ensure_menu'  => ! empty( $_POST['wpis_reset_menu'] ),
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
add_action( 'load-appearance_page_wpis-theme-seed', 'wpis_theme_handle_seed_admin_post' );

/**
 * @return void
 */
function wpis_theme_admin_seed_notices() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $pagenow;
	$pg = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( 'themes.php' !== $pagenow || 'wpis-theme-seed' !== $pg ) {
		return;
	}
	$msg = isset( $_GET['wpismsg'] ) ? sanitize_key( (string) wp_unslash( $_GET['wpismsg'] ) ) : '';
	if ( '' === $msg ) {
		return;
	}
	$count = isset( $_GET['wpiscount'] ) ? (int) $_GET['wpiscount'] : 0;
	$cle   = isset( $_GET['wpiscleaned'] ) ? (int) $_GET['wpiscleaned'] : 0;
	$text  = '';
	if ( 'imported' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of pages touched */
			_n( 'Demo import finished (%d page in the manifest).', 'Demo import finished (%d pages in the manifest).', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'cleaned' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of pages removed */
			_n( 'Removed %d demo page from the manifest.', 'Removed %d demo pages from the manifest.', $count, 'wpis-theme' ),
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
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
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
			<?php esc_html_e( 'Create or update the demo pages from the theme files in content/html/. Same operation as: wp wpis-seed import (see README).', 'wpis-theme' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Slugs in this manifest', 'wpis-theme' ); ?>:</strong>
			<?php echo esc_html( implode( ', ', array_filter( $slugs ) ) ); ?>
		</p>

		<h2 class="title"><?php esc_html_e( 'Import', 'wpis-theme' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'themes.php?page=wpis-theme-seed' ) ); ?>">
			<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
			<input type="hidden" name="wpis_seed_action" value="import" />
			<fieldset>
				<p>
					<label>
						<input type="checkbox" name="wpis_sync" value="1" checked />
						<?php esc_html_e( 'Overwrite page content from theme files (sync) for existing pages', 'wpis-theme' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="wpis_reading" value="1" checked />
						<?php esc_html_e( 'Set static front page to the Home page', 'wpis-theme' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="wpis_menu" value="1" checked />
						<?php esc_html_e( 'Rebuild the WPIS Primary menu', 'wpis-theme' ); ?>
					</label>
				</p>
			</fieldset>
			<?php
			submit_button( __( 'Import demo pages', 'wpis-theme' ) );
			?>
		</form>

		<h2 class="title"><?php esc_html_e( 'Remove demo pages', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Puts pages in the trash (or deletes them permanently if you check the box).', 'wpis-theme' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'themes.php?page=wpis-theme-seed' ) ); ?>" onsubmit="return window.confirm( '<?php echo esc_js( __( 'Move manifest pages to the trash (or delete permanently if checked)?', 'wpis-theme' ) ); ?>' );">
			<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
			<input type="hidden" name="wpis_seed_action" value="clean" />
			<p>
				<label>
					<input type="checkbox" name="wpis_force" value="1" />
					<?php esc_html_e( 'Delete permanently (skip trash)', 'wpis-theme' ); ?>
				</label>
			</p>
			<?php
			submit_button( __( 'Remove demo pages', 'wpis-theme' ), 'delete' );
			?>
		</form>

		<h2 class="title"><?php esc_html_e( 'Reset (remove then import again)', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Removes all manifest pages, then runs the import below. Use when you want a full refresh.', 'wpis-theme' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'themes.php?page=wpis-theme-seed' ) ); ?>" onsubmit="return window.confirm( '<?php echo esc_js( __( 'This will remove demo pages and import again. Continue?', 'wpis-theme' ) ); ?>' );">
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
						<?php esc_html_e( 'Overwrite page content on import', 'wpis-theme' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="wpis_reset_reading" value="1" checked />
						<?php esc_html_e( 'Set static front page to Home', 'wpis-theme' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="wpis_reset_menu" value="1" checked />
						<?php esc_html_e( 'Rebuild the WPIS Primary menu', 'wpis-theme' ); ?>
					</label>
				</p>
			</fieldset>
			<?php
			submit_button( __( 'Reset demo', 'wpis-theme' ), 'primary' );
			?>
		</form>
	</div>
	<?php
}
