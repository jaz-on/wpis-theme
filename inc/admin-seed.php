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
	$method = '';
	if ( isset( $_SERVER['REQUEST_METHOD'] ) ) {
		$method = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) );
	}
	if ( 'POST' !== $method || empty( $_POST['wpis_seed_action'] ) ) {
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
					'page'        => WPIS_THEME_IMPORT_PAGE,
					'wpismsg'     => 'reset',
					'wpiscount'   => (string) (int) $count,
					'wpiscleaned' => (string) (int) $cleaned,
					'wpisforce'   => $force ? '1' : '0',
				),
				admin_url( 'themes.php' )
			);
			break;
		case 'quote_seed_starter':
		case 'quote_erase_starter':
		case 'quote_reset_starter':
		case 'quote_seed_demo':
		case 'quote_erase_demo':
		case 'quote_reset_demo':
			$redirect = wpis_theme_handle_quote_seed_action( $action );
			break;
		default:
			return;
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'load-appearance_page_' . WPIS_THEME_IMPORT_PAGE, 'wpis_theme_handle_seed_admin_post' );

/**
 * Dispatch starter/demo sample quote actions to the wpis-core seeder classes.
 *
 * Returns the URL to redirect to with the appropriate notice flag. Falls back
 * to a plugin_inactive notice when the seeders are not loaded.
 *
 * @param string $action One of quote_seed_starter, quote_erase_starter, quote_seed_demo, quote_erase_demo.
 * @return string Admin URL with query args.
 */
function wpis_theme_handle_quote_seed_action( $action ) {
	$base = admin_url( 'themes.php' );
	$args = array( 'page' => WPIS_THEME_IMPORT_PAGE );

	$starter = '\WPIS\Core\CLI\StarterSeeder';
	$demo    = '\WPIS\Core\CLI\DemoSeeder';
	if ( ! class_exists( $starter ) || ! class_exists( $demo ) ) {
		$args['wpismsg'] = 'plugin_inactive';
		return add_query_arg( $args, $base );
	}

	$count = 0;
	$msg   = '';
	switch ( $action ) {
		case 'quote_seed_starter':
			$count = (int) call_user_func( array( $starter, 'seed' ) );
			$msg   = 'starter_seeded';
			break;
		case 'quote_erase_starter':
			$count = (int) call_user_func( array( $starter, 'erase' ) );
			$msg   = 'starter_erased';
			break;
		case 'quote_reset_starter':
			call_user_func( array( $starter, 'erase' ) );
			$count = (int) call_user_func( array( $starter, 'seed' ) );
			$msg   = 'starter_reset';
			break;
		case 'quote_seed_demo':
			$count = (int) call_user_func( array( $demo, 'seed' ) );
			$msg   = 'demo_seeded';
			break;
		case 'quote_erase_demo':
			$count = (int) call_user_func( array( $demo, 'erase' ) );
			$msg   = 'demo_erased';
			break;
		case 'quote_reset_demo':
			call_user_func( array( $demo, 'erase' ) );
			$count = (int) call_user_func( array( $demo, 'seed' ) );
			$msg   = 'demo_reset';
			break;
	}

	$args['wpismsg']   = $msg;
	$args['wpiscount'] = (string) $count;
	return add_query_arg( $args, $base );
}

/**
 * @return void
 */
function wpis_theme_admin_seed_notices() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $pagenow;

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Flash query args from our own redirects; user is gated by manage_options and import screen slug below.
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
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$text = '';
	if ( 'plugin_inactive' === $msg ) {
		$text = __( 'The WordPress Is… Core plugin (wpis-core) must be active to manage sample quotes.', 'wpis-theme' );
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
	} elseif ( 'starter_reset' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of quotes re-imported */
			_n( 'Reset starter quotes: re-imported %d quote.', 'Reset starter quotes: re-imported %d quotes.', $count, 'wpis-theme' ),
			$count
		);
	} elseif ( 'demo_reset' === $msg ) {
		$text = sprintf(
			/* translators: %d: number of quotes re-imported */
			_n( 'Reset demo quotes: re-imported %d quote.', 'Reset demo quotes: re-imported %d quotes.', $count, 'wpis-theme' ),
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

	$theme_active     = function_exists( 'wpis_theme_setup_run' );
	$core_installed   = wpis_theme_is_core_plugin_installed();
	$core_active      = class_exists( '\WPIS\Core\CLI\StarterSeeder' ) && class_exists( '\WPIS\Core\CLI\DemoSeeder' );
	$core_status_note = wpis_theme_core_plugin_status_note( $core_installed, $core_active );
	$action_url       = wpis_theme_import_admin_url();
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p>
			<?php esc_html_e( 'Manage WPIS content from one place. Each section below groups actions that work the same way (import, remove, reset) whether they come from the theme (manifest pages under content/html/) or the Core plugin (sample quotes). Blocks are greyed out when the matching package is not active.', 'wpis-theme' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'CLI parity: wp wpis-seed import|clean|reset for manifest pages; wp wpis seed_starter [--erase], wp wpis seed_demo [--erase] for sample quotes.', 'wpis-theme' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'The site menu is the Navigation block in the Header template part (Appearance → Editor). This theme does not use classic Appearance → Menus.', 'wpis-theme' ); ?>
		</p>

		<h2 class="title"><?php esc_html_e( 'Packages', 'wpis-theme' ); ?></h2>
		<ul style="margin-left: 1.5em; list-style: disc;">
			<li>
				<strong><?php esc_html_e( 'Theme (wpis-theme) — manifest pages', 'wpis-theme' ); ?>:</strong>
				<?php
				echo wp_kses_post(
					$theme_active
						? '<span style="color:#1e8a4c;">' . esc_html__( 'active', 'wpis-theme' ) . '</span>'
						: '<span style="color:#a00;">' . esc_html__( 'inactive', 'wpis-theme' ) . '</span>'
				);
				?>
				<br />
				<span class="description">
					<?php esc_html_e( 'Slugs in this manifest', 'wpis-theme' ); ?>:
					<?php echo esc_html( implode( ', ', array_filter( $slugs ) ) ); ?>
				</span>
			</li>
			<li>
				<strong><?php esc_html_e( 'Core plugin (wpis-core) — sample quotes', 'wpis-theme' ); ?>:</strong>
				<?php
				if ( $core_active ) {
					echo '<span style="color:#1e8a4c;">' . esc_html__( 'active', 'wpis-theme' ) . '</span>';
				} elseif ( $core_installed ) {
					echo '<span style="color:#b26200;">' . esc_html__( 'installed but not active', 'wpis-theme' ) . '</span>';
				} else {
					echo '<span style="color:#a00;">' . esc_html__( 'not installed', 'wpis-theme' ) . '</span>';
				}
				?>
				<?php if ( '' !== $core_status_note ) : ?>
					<br /><span class="description"><?php echo esc_html( $core_status_note ); ?></span>
				<?php endif; ?>
			</li>
		</ul>

		<hr />

		<h2 class="title"><?php esc_html_e( 'Import', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Create or update content. Does not delete anything that already exists.', 'wpis-theme' ); ?>
		</p>

		<?php wpis_theme_render_manifest_import_block( $action_url, $theme_active ); ?>
		<?php wpis_theme_render_quotes_block( $action_url, $core_active, 'import' ); ?>

		<hr />

		<h2 class="title"><?php esc_html_e( 'Remove', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Delete previously imported content. Trash by default where applicable; use the permanent option to match --force.', 'wpis-theme' ); ?>
		</p>

		<?php wpis_theme_render_manifest_remove_block( $action_url, $theme_active ); ?>
		<?php wpis_theme_render_quotes_block( $action_url, $core_active, 'remove' ); ?>

		<hr />

		<h2 class="title"><?php esc_html_e( 'Reset', 'wpis-theme' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Remove existing content, then re-import it. Destructive; a confirmation is required.', 'wpis-theme' ); ?>
		</p>

		<?php wpis_theme_render_manifest_reset_block( $action_url, $theme_active ); ?>
		<?php wpis_theme_render_quotes_block( $action_url, $core_active, 'reset' ); ?>
	</div>
	<?php
}

/**
 * Best-effort detection of whether the wpis-core plugin is installed (active or not).
 *
 * @return bool
 */
function wpis_theme_is_core_plugin_installed() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$all = function_exists( 'get_plugins' ) ? get_plugins() : array();
	foreach ( $all as $file => $_data ) {
		if ( false !== strpos( (string) $file, 'wpis-core' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Human-readable status line for the Core plugin state.
 *
 * @param bool $installed Installed on disk.
 * @param bool $active    Loaded in memory.
 * @return string
 */
function wpis_theme_core_plugin_status_note( $installed, $active ) {
	if ( $active ) {
		return '';
	}
	if ( $installed ) {
		return __( 'Activate the plugin to enable the sample quotes actions below.', 'wpis-theme' );
	}
	return __( 'Install and activate the WordPress Is… Core plugin to enable the sample quotes actions below.', 'wpis-theme' );
}

/**
 * Render the Import block for manifest pages (theme).
 *
 * @param string $action_url Form action URL.
 * @param bool   $enabled    Whether the block is interactive.
 * @return void
 */
function wpis_theme_render_manifest_import_block( $action_url, $enabled ) {
	?>
	<h3><?php esc_html_e( 'Manifest pages (theme)', 'wpis-theme' ); ?></h3>
	<p class="description">
		<?php esc_html_e( 'Default options match: wp wpis-seed import (sync on, reading on).', 'wpis-theme' ); ?>
	</p>
	<form method="post" action="<?php echo esc_url( $action_url ); ?>">
		<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
		<input type="hidden" name="wpis_seed_action" value="import" />
		<fieldset <?php disabled( ! $enabled ); ?>>
			<p>
				<label>
					<input type="checkbox" name="wpis_sync" value="1" checked <?php disabled( ! $enabled ); ?> />
					<?php esc_html_e( 'Sync: overwrite page content from theme files for existing manifest pages (off = wpis-seed import --no-sync)', 'wpis-theme' ); ?>
				</label>
			</p>
			<p>
				<label>
					<input type="checkbox" name="wpis_reading" value="1" checked <?php disabled( ! $enabled ); ?> />
					<?php esc_html_e( 'Set static front page to Home (off = wpis-seed import --no-reading)', 'wpis-theme' ); ?>
				</label>
			</p>
			<?php submit_button( __( 'Import pages', 'wpis-theme' ), 'secondary', 'submit', false, array( 'disabled' => ! $enabled ) ); ?>
		</fieldset>
	</form>
	<?php
}

/**
 * Render the Remove block for manifest pages (theme).
 *
 * @param string $action_url Form action URL.
 * @param bool   $enabled    Whether the block is interactive.
 * @return void
 */
function wpis_theme_render_manifest_remove_block( $action_url, $enabled ) {
	?>
	<h3><?php esc_html_e( 'Manifest pages (theme)', 'wpis-theme' ); ?></h3>
	<p class="description">
		<?php esc_html_e( 'Same as: wp wpis-seed clean. Trash by default, or delete permanently to match --force.', 'wpis-theme' ); ?>
	</p>
	<form method="post" action="<?php echo esc_url( $action_url ); ?>" onsubmit="return window.confirm( '<?php echo esc_js( __( 'Move manifest pages to the trash (or delete permanently if checked)?', 'wpis-theme' ) ); ?>' );">
		<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
		<input type="hidden" name="wpis_seed_action" value="clean" />
		<fieldset <?php disabled( ! $enabled ); ?>>
			<p>
				<label>
					<input type="checkbox" name="wpis_force" value="1" <?php disabled( ! $enabled ); ?> />
					<?php esc_html_e( 'Delete permanently: wpis-seed clean --force', 'wpis-theme' ); ?>
				</label>
			</p>
			<?php submit_button( __( 'Remove pages', 'wpis-theme' ), 'delete', 'submit', false, array( 'disabled' => ! $enabled ) ); ?>
		</fieldset>
	</form>
	<?php
}

/**
 * Render the Reset block for manifest pages (theme).
 *
 * @param string $action_url Form action URL.
 * @param bool   $enabled    Whether the block is interactive.
 * @return void
 */
function wpis_theme_render_manifest_reset_block( $action_url, $enabled ) {
	?>
	<h3><?php esc_html_e( 'Manifest pages (theme)', 'wpis-theme' ); ?></h3>
	<p class="description">
		<?php esc_html_e( 'Same as: wp wpis-seed reset. Removes all manifest pages, then imports again with the checkboxes below.', 'wpis-theme' ); ?>
	</p>
	<form method="post" action="<?php echo esc_url( $action_url ); ?>" onsubmit="return window.confirm( '<?php echo esc_js( __( 'This will remove manifest pages and import again. Continue?', 'wpis-theme' ) ); ?>' );">
		<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
		<input type="hidden" name="wpis_seed_action" value="reset" />
		<fieldset <?php disabled( ! $enabled ); ?>>
			<p>
				<label>
					<input type="checkbox" name="wpis_force_reset" value="1" <?php disabled( ! $enabled ); ?> />
					<?php esc_html_e( 'Delete permanently when cleaning (not recommended unless you know why)', 'wpis-theme' ); ?>
				</label>
			</p>
			<p>
				<label>
					<input type="checkbox" name="wpis_reset_sync" value="1" checked <?php disabled( ! $enabled ); ?> />
					<?php esc_html_e( 'Sync on re-import (off = --no-sync after clean)', 'wpis-theme' ); ?>
				</label>
			</p>
			<p>
				<label>
					<input type="checkbox" name="wpis_reset_reading" value="1" checked <?php disabled( ! $enabled ); ?> />
					<?php esc_html_e( 'Set static front page to Home (off = --no-reading after import)', 'wpis-theme' ); ?>
				</label>
			</p>
			<?php submit_button( __( 'Run reset', 'wpis-theme' ), 'primary', 'submit', false, array( 'disabled' => ! $enabled ) ); ?>
		</fieldset>
	</form>
	<?php
}

/**
 * Render the sample-quotes block (plugin) for one of the general sections.
 *
 * @param string $action_url Form action URL.
 * @param bool   $enabled    Whether the block is interactive (plugin active).
 * @param string $section    One of 'import', 'remove', 'reset'.
 * @return void
 */
function wpis_theme_render_quotes_block( $action_url, $enabled, $section ) {
	$definitions = array(
		'import' => array(
			'description' => __( 'Same code as: wp wpis seed_starter, wp wpis seed_demo. Starter set is not flagged; demo set uses meta for bulk removal.', 'wpis-theme' ),
			'buttons'     => array(
				array(
					'action'  => 'quote_seed_starter',
					'label'   => __( 'Import starter quotes', 'wpis-theme' ),
					'style'   => 'secondary',
					'confirm' => '',
				),
				array(
					'action'  => 'quote_seed_demo',
					'label'   => __( 'Import demo quotes', 'wpis-theme' ),
					'style'   => 'secondary',
					'confirm' => '',
				),
			),
		),
		'remove' => array(
			'description' => __( 'Same code as: wp wpis seed_starter --erase, wp wpis seed_demo --erase.', 'wpis-theme' ),
			'buttons'     => array(
				array(
					'action'  => 'quote_erase_starter',
					'label'   => __( 'Remove starter quotes', 'wpis-theme' ),
					'style'   => 'delete',
					'confirm' => __( 'Remove all starter-tagged sample quotes? This cannot be undone.', 'wpis-theme' ),
				),
				array(
					'action'  => 'quote_erase_demo',
					'label'   => __( 'Remove demo quotes', 'wpis-theme' ),
					'style'   => 'delete',
					'confirm' => __( 'Remove all demo-tagged sample quotes? This cannot be undone.', 'wpis-theme' ),
				),
			),
		),
		'reset'  => array(
			'description' => __( 'Erase the matching set, then re-import it (equivalent to running --erase followed by the seed command).', 'wpis-theme' ),
			'buttons'     => array(
				array(
					'action'  => 'quote_reset_starter',
					'label'   => __( 'Reset starter quotes', 'wpis-theme' ),
					'style'   => 'primary',
					'confirm' => __( 'Erase then re-import starter sample quotes. Continue?', 'wpis-theme' ),
				),
				array(
					'action'  => 'quote_reset_demo',
					'label'   => __( 'Reset demo quotes', 'wpis-theme' ),
					'style'   => 'primary',
					'confirm' => __( 'Erase then re-import demo sample quotes. Continue?', 'wpis-theme' ),
				),
			),
		),
	);

	if ( ! isset( $definitions[ $section ] ) ) {
		return;
	}
	$def = $definitions[ $section ];
	?>
	<h3><?php esc_html_e( 'Sample quotes (plugin)', 'wpis-theme' ); ?></h3>
	<p class="description"><?php echo esc_html( $def['description'] ); ?></p>
	<?php if ( ! $enabled ) : ?>
		<p class="description" style="color:#a00;">
			<?php esc_html_e( 'The WordPress Is… Core plugin (wpis-core) is not active — these actions are disabled.', 'wpis-theme' ); ?>
		</p>
	<?php endif; ?>
	<fieldset <?php disabled( ! $enabled ); ?> style="<?php echo $enabled ? '' : 'opacity: 0.55;'; ?>">
		<p>
			<?php foreach ( $def['buttons'] as $btn ) : ?>
				<?php
				$onsubmit = '';
				if ( '' !== $btn['confirm'] ) {
					$onsubmit = "return window.confirm( '" . esc_js( $btn['confirm'] ) . "' );";
				}
				?>
				<form method="post" action="<?php echo esc_url( $action_url ); ?>" style="display: inline; margin-right: 12px;"<?php echo '' !== $onsubmit ? ' onsubmit="' . esc_attr( $onsubmit ) . '"' : ''; ?>>
					<?php wp_nonce_field( 'wpis_theme_seed' ); ?>
					<input type="hidden" name="wpis_seed_action" value="<?php echo esc_attr( $btn['action'] ); ?>" />
					<?php
					submit_button(
						$btn['label'],
						$btn['style'],
						'submit',
						false,
						array(
							'disabled' => ! $enabled,
							'style'    => 'vertical-align: middle;',
						)
					);
					?>
				</form>
			<?php endforeach; ?>
		</p>
	</fieldset>
	<?php
}
