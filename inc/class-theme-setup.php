<?php
/**
 * Idempotent theme activation: pages, reading, primary menu.
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on after_switch_theme.
 */
final class WPIS_Theme_Setup {

	/**
	 * @return array<string, int> Slug => page ID for manifest pages.
	 */
	public static function run(): array {
		$ids = self::ensure_pages();
		self::ensure_reading( $ids );
		self::ensure_menu( $ids );
		return $ids;
	}

	/**
	 * Manifest order: parents before children.
	 *
	 * @return list<array{slug:string,title:string,parent_slug:string,file:string}>
	 */
	private static function manifest(): array {
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
	 * @return array<string, int>
	 */
	private static function ensure_pages(): array {
		$ids_by_slug = array();
		foreach ( self::manifest() as $row ) {
			$parent_id = self::resolve_parent_id( $row['parent_slug'], $ids_by_slug );
			$existing  = self::get_page_by_slug_and_parent( $row['slug'], $parent_id );
			if ( $existing ) {
				$ids_by_slug[ $row['slug'] ] = (int) $existing->ID;
				continue;
			}

			$path  = get_template_directory() . '/content/html/' . $row['file'];
			$inner = is_readable( $path ) ? file_get_contents( $path ) : '';
			$inner = is_string( $inner ) ? $inner : '';
			// Avoid breaking the core/html block when raw HTML contains CDATA end.
			$inner   = str_replace( ']]>', ']]]]><![CDATA[>', $inner );
			$content = "<!-- wp:html -->\n" . $inner . "\n<!-- /wp:html -->";

			$new_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $row['title'],
					'post_name'    => $row['slug'],
					'post_parent'  => $parent_id,
					'post_content' => $content,
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
	 * @param array<string, int> $ids_by_slug
	 */
	private static function resolve_parent_id( string $parent_slug, array &$ids_by_slug ): int {
		if ( $parent_slug === '' ) {
			return 0;
		}
		if ( isset( $ids_by_slug[ $parent_slug ] ) ) {
			return (int) $ids_by_slug[ $parent_slug ];
		}
		$found = self::get_page_by_slug_and_parent( $parent_slug, 0 );
		if ( $found ) {
			$ids_by_slug[ $parent_slug ] = (int) $found->ID;
			return (int) $found->ID;
		}
		return 0;
	}

	private static function get_page_by_slug_and_parent( string $slug, int $parent_id ): ?WP_Post {
		$q = new WP_Query(
			array(
				'post_type'              => 'page',
				'post_status'            => array( 'publish', 'draft', 'pending', 'private', 'future' ),
				'name'                   => $slug,
				'post_parent'            => $parent_id,
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		if ( $q->have_posts() ) {
			return $q->posts[0];
		}
		return null;
	}

	/**
	 * @param array<string, int> $ids_by_slug
	 */
	private static function ensure_reading( array $ids_by_slug ): void {
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
	 * Nav menu helpers live in a file that is not loaded on every bootstrap; theme activation can run before it.
	 */
	private static function load_nav_menu_api_if_needed(): void {
		if ( function_exists( 'wp_update_nav_menu_item' ) ) {
			return;
		}
		if ( ! is_string( ABSPATH ) || ABSPATH === '' ) {
			return;
		}
		$file = ABSPATH . 'wp-admin/includes/nav-menu.php';
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * @param array<string, int> $ids_by_slug
	 */
	private static function ensure_menu( array $ids_by_slug ): void {
		self::load_nav_menu_api_if_needed();
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
						'menu-item-title'     => $item['label'],
						'menu-item-object'    => 'page',
						'menu-item-object-id'   => (int) $ids_by_slug[ $item['slug'] ],
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => ++$pos,
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
}
