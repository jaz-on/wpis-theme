#!/usr/bin/env php
<?php
/**
 * Static checks on block-theme markup (no WordPress bootstrap).
 *
 * - Pairs of Custom HTML block comments (<!-- wp:html --> … <!-- /wp:html -->).
 * - Inside each Custom HTML fragment: <a> must not wrap block-level elements
 *   (p, div, headings, lists, section, etc.) — browsers rewrite the DOM and
 *   Gutenberg block validation breaks.
 *
 * Usage:
 *   php tools/verify-markup.php
 *
 * Strict: flag any <a> that wraps block-level tags (also catches tax-card, platform-card, etc.):
 *   WPIS_VERIFY_MARKUP_STRICT=1 php tools/verify-markup.php
 *
 * Scans `templates/`, `parts/`, `content/html/`, `patterns/*.php`, and `inc/register-patterns.php`.
 *
 * Optional (parse_blocks() smoke test; requires WordPress):
 *   WP_LOAD_PATH=/path/to/wordpress/wp-load.php php tools/verify-markup.php
 *
 * @package WPIS
 */

declare( strict_types=1 );

$theme_root = dirname( __DIR__ );
$errors     = array();
$warnings   = array();
$scanned    = 0;
$strict     = filter_var( getenv( 'WPIS_VERIFY_MARKUP_STRICT' ), FILTER_VALIDATE_BOOLEAN );

/**
 * Collect files under a directory with given suffixes.
 *
 * @param string   $dir  Absolute directory.
 * @param list<string> $suffixes File suffixes including dot.
 * @return list<string>
 */
function wpis_verify_collect_files( string $dir, array $suffixes ): array {
	if ( ! is_dir( $dir ) ) {
		return array();
	}
	$out = array();
	$it  = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS )
	);
	foreach ( $it as $f ) {
		if ( ! $f->isFile() ) {
			continue;
		}
		$p = $f->getPathname();
		foreach ( $suffixes as $suf ) {
			if ( str_ends_with( $p, $suf ) ) {
				$out[] = $p;
				break;
			}
		}
	}
	return $out;
}

/**
 * @return list<string>
 */
function wpis_verify_extract_custom_html_fragments( string $content ): array {
	$fragments = array();
	if ( preg_match_all( '/<!--\s*wp:html\s*-->\s*(.*?)\s*<!--\s*\/wp:html\s*-->/s', $content, $m ) ) {
		foreach ( $m[1] as $inner ) {
			$fragments[] = $inner;
		}
	}
	return $fragments;
}

/**
 * @return list<string> Issue messages.
 */
function wpis_verify_anchor_block_level_inside( string $html, bool $strict ): array {
	$issues = array();
	// Per-link segments (seeds do not nest <a>).
	if ( ! preg_match_all( '/<a\s[^>]*>.*?<\/a>/is', $html, $matches ) ) {
		return array();
	}
	foreach ( $matches[0] as $segment ) {
		if ( ! preg_match( '/<a\s([^>]*)>(.*)<\/a>/is', $segment, $in ) ) {
			continue;
		}
		$attrs = $in[1];
		$inner = $in[2];
		if ( ! $strict ) {
			// Default: targets the pattern that broke core/html validation (whole-card <a> + p/div).
			$is_quote_card = (bool) preg_match( '/class\s*=\s*["\'][^"\']*quote-card/i', $attrs );
			if ( ! $is_quote_card ) {
				continue;
			}
		}
		if ( preg_match( '/<\s*(p|div|h[1-6]|ul|ol|section|article|header|footer|table|form|figure|blockquote)\b/i', $inner ) ) {
			$issues[] = '<a> contains block-level markup (p, div, heading, list, etc.). Use only phrasing content inside links, or use strict mode to audit card patterns (WPIS_VERIFY_MARKUP_STRICT=1).';
		}
	}
	return $issues;
}

/**
 * @param list<string> $files
 */
function wpis_verify_file_list( array $files, string $theme_root, array &$errors, array &$warnings, int &$scanned, bool $strict ): void {
	foreach ( $files as $file ) {
		++$scanned;
		$raw = file_get_contents( $file );
		if ( ! is_string( $raw ) || '' === $raw ) {
			continue;
		}
		$rel = str_replace( $theme_root . '/', '', $file );

		$opens  = (int) preg_match_all( '/<!--\s*wp:html\s*-->/', $raw );
		$closes = (int) preg_match_all( '/<!--\s*\/wp:html\s*-->/', $raw );
		if ( $opens !== $closes ) {
			$errors[] = "{$rel}: Custom HTML block mismatch ({$opens} open, {$closes} close).";
		}

		foreach ( wpis_verify_extract_custom_html_fragments( $raw ) as $i => $frag ) {
			foreach ( wpis_verify_anchor_block_level_inside( $frag, $strict ) as $msg ) {
				$errors[] = "{$rel}: fragment #" . ( $i + 1 ) . " — {$msg}";
			}
		}
	}
}

$file_groups = array(
	wpis_verify_collect_files( $theme_root . '/templates', array( '.html' ) ),
	wpis_verify_collect_files( $theme_root . '/parts', array( '.html' ) ),
	wpis_verify_collect_files( $theme_root . '/content/html', array( '.html' ) ),
	wpis_verify_collect_files( $theme_root . '/patterns', array( '.php' ) ),
	array( $theme_root . '/inc/register-patterns.php' ),
);

foreach ( $file_groups as $group ) {
	wpis_verify_file_list( $group, $theme_root, $errors, $warnings, $scanned, $strict );
}

$wp_load = getenv( 'WP_LOAD_PATH' );
if ( is_string( $wp_load ) && '' !== $wp_load && is_readable( $wp_load ) ) {
	require_once $wp_load;
	if ( function_exists( 'parse_blocks' ) ) {
		$parse_files = array_merge(
			wpis_verify_collect_files( $theme_root . '/templates', array( '.html' ) ),
			wpis_verify_collect_files( $theme_root . '/parts', array( '.html' ) ),
			wpis_verify_collect_files( $theme_root . '/content/html', array( '.html' ) )
		);
		foreach ( $parse_files as $file ) {
			$rel = str_replace( $theme_root . '/', '', $file );
			$raw = file_get_contents( $file );
			if ( ! is_string( $raw ) || ! str_contains( $raw, '<!-- wp:' ) ) {
				continue;
			}
			$blocks = parse_blocks( $raw );
			if ( array() === $blocks && str_contains( $raw, '<!-- wp:' ) ) {
				$errors[] = "{$rel}: parse_blocks() returned empty though file contains block comments.";
			}
		}
		$warnings[] = 'WordPress parse_blocks() sanity check ran on templates/parts/content html (no deep validation).';
	}
}

foreach ( $warnings as $w ) {
	fwrite( STDERR, "Notice: {$w}\n" );
}

if ( array() !== $errors ) {
	fwrite( STDERR, "verify-markup.php failed ({$scanned} files scanned):\n" );
	foreach ( $errors as $e ) {
		fwrite( STDERR, "  - {$e}\n" );
	}
	exit( 1 );
}

echo 'OK: verified ' . $scanned . ' files (Custom HTML pairs + link/phrasing rules' . ( $strict ? ', strict' : ', quote-card focus' ) . ").\n";
exit( 0 );
