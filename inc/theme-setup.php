<?php
/**
 * One-time setup on theme switch: pages, reading, primary menu.
 *
 * Uses core APIs: get_page_by_path(), wp_insert_post(), serialize_block(), nav menus.
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run all setup steps (called from after_switch_theme).
 *
 * @return array<string, int> Slug => page ID for manifest pages.
 */
function wpis_theme_setup_run() {
	$ids = wpis_theme_setup_ensure_pages();
	wpis_theme_setup_ensure_reading( $ids );
	wpis_theme_setup_ensure_menu( $ids );
	return $ids;
}

/**
 * Manifest order: parents before children (paths use get_page_by_path segments).
 *
 * @return array<int, array{slug:string,title:string,parent_slug:string,file:string}>
 */
function wpis_theme_setup_get_manifest() {
	return array(
		array(
			'slug'         => 'taxonomy',
			'title'        => 'Taxonomy',
			'parent_slug'  => '',
			'file'         => 'taxonomy.html',
		),
		array(
			'slug'         => 'quote',
			'title'        => 'Quote',
			'parent_slug'  => '',
			'file'         => 'quote.html',
		),
		array(
			'slug'         => 'security',
			'title'        => 'Security',
			'parent_slug'  => 'taxonomy',
			'file'         => 'security.html',
		),
		array(
			'slug'         => 'sample',
			'title'        => 'Sample',
			'parent_slug'  => 'quote',
			'file'         => 'sample.html',
		),
		array(
			'slug'         => 'home',
			'title'        => 'Home',
			'parent_slug'  => '',
			'file'         => 'home.html',
		),
		array(
			'slug'         => 'explore',
			'title'        => 'Explore',
			'parent_slug'  => '',
			'file'         => 'explore.html',
		),
		array(
			'slug'         => 'about',
			'title'        => 'About',
			'parent_slug'  => '',
			'file'         => 'about.html',
		),
		array(
			'slug'         => 'how-it-works',
			'title'        => 'How it works',
			'parent_slug'  => '',
			'file'         => 'how-it-works.html',
		),
		array(
			'slug'         => 'submit',
			'title'        => 'Submit',
			'parent_slug'  => '',
			'file'         => 'submit.html',
		),
		array(
			'slug'         => 'submitted',
			'title'        => 'Submitted',
			'parent_slug'  => '',
			'file'         => 'submitted.html',
		),
		array(
			'slug'         => 'profile',
			'title'        => 'My profile',
			'parent_slug'  => '',
			'file'         => 'profile.html',
		),
		array(
			'slug'         => 'search-demo',
			'title'        => 'Search demo',
			'parent_slug'  => '',
			'file'         => 'search-demo.html',
		),
	);
}

/**
 * Build hierarchical path for get_page_by_path().
 *
 * @param string $slug        Page slug.
 * @param string $parent_slug Parent slug or empty.
 * @return string Path like "quote/sample" or "home".
 */
function wpis_theme_setup_page_path( $slug, $parent_slug ) {
	if ( '' === $parent_slug ) {
		return $slug;
	}
	return $parent_slug . '/' . $slug;
}

/**
 * Wrap raw HTML as a single core/html block for post_content.
 *
 * @param string $inner Raw HTML from seed file.
 * @return string Serialized block markup.
 */
function wpis_theme_setup_html_block_content( $inner ) {
	$inner = str_replace( ']]>', ']]]]><![CDATA[>', $inner );

	if ( function_exists( 'serialize_block' ) ) {
		return serialize_block(
			array(
				'blockName'    => 'core/html',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => $inner,
				'innerContent' => array( $inner ),
			)
		);
	}

	return "<!-- wp:html -->\n" . $inner . "\n<!-- /wp:html -->";
}

/**
 * Build post_content from a seed file: block markup if the file starts with a block comment, else core/html.
 *
 * @param string $raw File contents.
 * @return string
 */
function wpis_theme_setup_seed_post_content( $raw ) {
	$raw = is_string( $raw ) ? $raw : '';
	$raw = str_replace( ']]>', ']]]]><![CDATA[>', $raw );
	$trim = trim( $raw );
	if ( '' !== $trim && str_starts_with( $trim, '<!-- wp:' ) ) {
		return $raw;
	}
	return wpis_theme_setup_html_block_content( $raw );
}

/**
 * @return array<string, int>
 */
function wpis_theme_setup_ensure_pages() {
	$ids_by_slug = array();

	foreach ( wpis_theme_setup_get_manifest() as $row ) {
		$path     = wpis_theme_setup_page_path( $row['slug'], $row['parent_slug'] );
		$existing = get_page_by_path( $path, OBJECT, 'page' );

		if ( $existing instanceof WP_Post ) {
			$ids_by_slug[ $row['slug'] ] = (int) $existing->ID;
			continue;
		}

		$parent_id = 0;
		if ( '' !== $row['parent_slug'] ) {
			$parent = get_page_by_path( $row['parent_slug'], OBJECT, 'page' );
			if ( $parent instanceof WP_Post ) {
				$parent_id = (int) $parent->ID;
			}
		}

		$seed_path = get_template_directory() . '/content/html/' . $row['file'];
		$inner     = is_readable( $seed_path ) ? file_get_contents( $seed_path ) : '';
		$inner = is_string( $inner ) ? $inner : '';

		$new_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $row['title'],
				'post_name'    => $row['slug'],
				'post_parent'  => $parent_id,
				'post_content' => wpis_theme_setup_seed_post_content( $inner ),
			),
			true
		);

		if ( ! is_wp_error( $new_id ) && $new_id > 0 ) {
			$ids_by_slug[ $row['slug'] ] = (int) $new_id;
		}
	}

	return $ids_by_slug;
}

/**
 * @param array<string, int> $ids_by_slug Slug => page ID.
 */
function wpis_theme_setup_ensure_reading( $ids_by_slug ) {
	if ( get_option( 'wpis_theme_reading_seeded', false ) ) {
		return;
	}
	if ( empty( $ids_by_slug['home'] ) ) {
		return;
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', (int) $ids_by_slug['home'] );
	update_option( 'wpis_theme_reading_seeded', true );
}

/**
 * Nav menu helpers are not loaded on every request; theme switch can run before they load.
 */
function wpis_theme_setup_load_nav_menu_api_if_needed() {
	if ( function_exists( 'wp_update_nav_menu_item' ) ) {
		return;
	}
	if ( ! is_string( ABSPATH ) || '' === ABSPATH ) {
		return;
	}
	$file = ABSPATH . 'wp-admin/includes/nav-menu.php';
	if ( is_readable( $file ) ) {
		require_once $file;
	}
}

/**
 * @param array<string, int> $ids_by_slug Slug => page ID.
 */
function wpis_theme_setup_ensure_menu( $ids_by_slug ) {
	wpis_theme_setup_load_nav_menu_api_if_needed();
	if ( ! function_exists( 'wp_get_nav_menus' ) || ! function_exists( 'wp_create_nav_menu' ) || ! function_exists( 'wp_update_nav_menu_item' ) ) {
		return;
	}

	$menu_name = 'WPIS Primary';
	$menu_id   = 0;
	foreach ( wp_get_nav_menus() as $menu ) {
		if ( $menu->name === $menu_name ) {
			$menu_id = (int) $menu->term_id;
			break;
		}
	}
	if ( ! $menu_id ) {
		$created = wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $created ) ) {
			return;
		}
		$menu_id = (int) $created;
	}

	$items = wp_get_nav_menu_items( $menu_id );
	if ( empty( $items ) ) {
		$order = array(
			array( 'slug' => 'home', 'label' => 'Feed' ),
			array( 'slug' => 'explore', 'label' => 'Explore' ),
			array( 'slug' => 'about', 'label' => 'About' ),
			array( 'slug' => 'how-it-works', 'label' => 'How it works' ),
			array( 'slug' => 'submit', 'label' => 'Submit' ),
			array( 'slug' => 'profile', 'label' => 'My profile' ),
		);
		$pos = 0;
		foreach ( $order as $item ) {
			if ( empty( $ids_by_slug[ $item['slug'] ] ) ) {
				continue;
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => $item['label'],
					'menu-item-object'   => 'page',
					'menu-item-object-id' => (int) $ids_by_slug[ $item['slug'] ],
					'menu-item-type'     => 'post_type',
					'menu-item-status'   => 'publish',
					'menu-item-position' => ++$pos,
				)
			);
		}
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}
	if ( empty( $locations['primary'] ) ) {
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
