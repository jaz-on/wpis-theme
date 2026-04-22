<?php
/**
 * Manifest pages and reading (WP-CLI `wp wpis-seed` or Appearance → WPIS import only).
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Import or update manifest pages, then optionally reading settings.
 *
 * @param array<string, bool> $args {
 *     @type bool $sync_content Sync post_content from content/html for existing pages.
 *     @type bool $set_reading  Set static front page to the seeded `home` page.
 * }
 * @return array<string, int> Slug => page ID.
 */
function wpis_theme_setup_run( array $args = array() ): array {
	$args = wp_parse_args(
		$args,
		array(
			'sync_content' => false,
			'set_reading'  => true,
		)
	);
	$ids = wpis_theme_setup_upsert_pages( (bool) $args['sync_content'] );
	if ( $args['set_reading'] ) {
		wpis_theme_setup_ensure_reading( $ids );
	}
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
			'slug'        => 'home',
			'title'       => 'Home',
			'parent_slug' => '',
			'file'        => 'home.html',
		),
		array(
			'slug'        => 'explore',
			'title'       => 'Explore',
			'parent_slug' => '',
			'file'        => 'explore.html',
		),
		array(
			'slug'        => 'about',
			'title'       => 'About',
			'parent_slug' => '',
			'file'        => 'about.html',
		),
		array(
			'slug'        => 'how-it-works',
			'title'       => 'How it works',
			'parent_slug' => '',
			'file'        => 'how-it-works.html',
		),
		array(
			'slug'        => 'submit',
			'title'       => 'Submit',
			'parent_slug' => '',
			'file'        => 'submit.html',
		),
		array(
			'slug'        => 'submitted',
			'title'       => 'Submitted',
			'parent_slug' => '',
			'file'        => 'submitted.html',
		),
		array(
			'slug'        => 'profile',
			'title'       => 'My profile',
			'parent_slug' => '',
			'file'        => 'profile.html',
		),
	);
}

/**
 * Build hierarchical path for get_page_by_path().
 *
 * @param string $slug        Page slug.
 * @param string $parent_slug Parent slug or empty.
 * @return string Path like "parent/child" or "home".
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
 * Manifest files under content/html/ should always be block-first; the core/html branch is a legacy fallback.
 *
 * @param string $raw File contents.
 * @return string
 */
function wpis_theme_setup_seed_post_content( $raw ) {
	$raw  = is_string( $raw ) ? $raw : '';
	$raw  = str_replace( ']]>', ']]]]><![CDATA[>', $raw );
	$trim = trim( $raw );
	if ( '' !== $trim && str_starts_with( $trim, '<!-- wp:' ) ) {
		return $raw;
	}
	return wpis_theme_setup_html_block_content( $raw );
}

/**
 * Create missing manifest pages; optionally overwrite content from seed files.
 *
 * @param bool $sync_content When true, updates post_content (and parent) for pages that already exist.
 * @return array<string, int> Slug => page ID.
 */
function wpis_theme_setup_upsert_pages( bool $sync_content = false ): array {
	$ids_by_slug = array();

	foreach ( wpis_theme_setup_get_manifest() as $row ) {
		$path     = wpis_theme_setup_page_path( $row['slug'], $row['parent_slug'] );
		$existing = get_page_by_path( $path, OBJECT, 'page' );

		$parent_id = 0;
		if ( '' !== $row['parent_slug'] ) {
			$parent = get_page_by_path( $row['parent_slug'], OBJECT, 'page' );
			if ( $parent instanceof WP_Post ) {
				$parent_id = (int) $parent->ID;
			}
		}

		$inner   = wpis_theme_get_content_html( $row['file'] );
		$content = wpis_theme_setup_seed_post_content( $inner );

		if ( $existing instanceof WP_Post ) {
			$ids_by_slug[ $row['slug'] ] = (int) $existing->ID;
			if ( $sync_content ) {
				wp_update_post(
					array(
						'ID'           => (int) $existing->ID,
						'post_content' => $content,
						'post_parent'  => $parent_id,
					)
				);
			} elseif ( $parent_id !== (int) $existing->post_parent ) {
				wp_update_post(
					array(
						'ID'          => (int) $existing->ID,
						'post_parent' => $parent_id,
					)
				);
			}
			continue;
		}

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
 * Paths for all manifest pages, deepest first (safe order for delete/trash).
 *
 * @return list<string>
 */
function wpis_theme_setup_get_manifest_paths_deepest_first(): array {
	$paths = array();
	foreach ( wpis_theme_setup_get_manifest() as $row ) {
		$paths[] = wpis_theme_setup_page_path( $row['slug'], $row['parent_slug'] );
	}
	usort(
		$paths,
		static function ( string $a, string $b ): int {
			return substr_count( $b, '/' ) <=> substr_count( $a, '/' );
		}
	);
	return $paths;
}

/**
 * Trash or delete every page that matches the demo manifest (by path).
 *
 * @param bool $force When true, bypass trash (wp_delete_post second arg).
 * @return int Number of posts removed.
 */
function wpis_theme_setup_clean_manifest_pages( bool $force = false ): int {
	$removed = 0;
	foreach ( wpis_theme_setup_get_manifest_paths_deepest_first() as $path ) {
		$post = get_page_by_path( $path, OBJECT, 'page' );
		if ( ! $post instanceof WP_Post ) {
			continue;
		}
		wp_delete_post( (int) $post->ID, $force );
		++$removed;
	}
	return $removed;
}

/**
 * If the static front page points to a missing or trashed page, fall back to latest posts.
 */
function wpis_theme_setup_reset_reading_after_clean(): void {
	$front_id = (int) get_option( 'page_on_front', 0 );
	if ( $front_id <= 0 ) {
		return;
	}
	$post = get_post( $front_id );
	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type || in_array( $post->post_status, array( 'trash', 'draft', 'pending' ), true ) ) {
		update_option( 'show_on_front', 'posts' );
		update_option( 'page_on_front', 0 );
	}
}

/**
 * Whether a post ID is a published page (usable as static front page).
 *
 * @param int $page_id Post ID.
 * @return bool
 */
function wpis_theme_setup_is_published_page( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return false;
	}
	$post = get_post( $page_id );
	return $post instanceof WP_Post && 'page' === $post->post_type && 'publish' === $post->post_status;
}

/**
 * Set static front page to the seeded Home page (run after import).
 *
 * @param array<string, int> $ids_by_slug Slug => page ID.
 */
function wpis_theme_setup_ensure_reading( $ids_by_slug ) {
	if ( empty( $ids_by_slug['home'] ) ) {
		return;
	}
	$home_id = (int) $ids_by_slug['home'];
	if ( ! wpis_theme_setup_is_published_page( $home_id ) ) {
		return;
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
}
