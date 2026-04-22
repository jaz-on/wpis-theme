<?php
/**
 * Block template helpers: convert theme part files to variation innerBlocks.
 *
 * @package WPIS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recursively turn a block from {@see parse_blocks()} into the array format expected for
 * block variations `innerBlocks` (Gutenberg "block template" tuple form).
 *
 * @param array<string,mixed> $block Parsed block.
 * @return array<int|string,mixed>|null Null if the block is a parser fragment without a name.
 */
function wpis_parsed_block_to_block_template( $block ) {
	if ( ! is_array( $block ) || empty( $block['blockName'] ) || ! is_string( $block['blockName'] ) ) {
		return null;
	}

	$name  = $block['blockName'];
	$attrs = ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) ? $block['attrs'] : array();
	$attrs = wpis_block_template_fill_sourced_content_attrs( $name, $attrs, $block );
	$inner = isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ? $block['innerBlocks'] : array();
	$inner = array_values(
		array_filter(
			array_map( 'wpis_parsed_block_to_block_template', $inner )
		)
	);

	if ( $inner ) {
		return array( $name, $attrs, $inner );
	}

	return array( $name, $attrs );
}

/**
 * Ensure `content` / `text` (and similar) are present for blocks where the part file
 * stores the body in innerHTML.
 *
 * @param string $name Block name.
 * @param array  $attrs Block attributes.
 * @param array  $block Full parsed block.
 * @return array
 */
function wpis_block_template_fill_sourced_content_attrs( $name, $attrs, $block ) {
	$inner = isset( $block['innerHTML'] ) && is_string( $block['innerHTML'] ) ? $block['innerHTML'] : '';

	if ( ( 'core/paragraph' === $name || 'core/heading' === $name ) && ( ! isset( $attrs['content'] ) || '' === (string) $attrs['content'] ) && $inner ) {
		$attrs['content'] = $inner;
	}

	if ( 'core/shortcode' === $name && ( ! isset( $attrs['text'] ) || '' === (string) $attrs['text'] ) && $inner ) {
		$attrs['text'] = trim( $inner );
	}

	return $attrs;
}

/**
 * Read a template part (single root block) and return attributes plus inner block templates
 * for {@see get_block_type_variations()}.
 *
 * @param string $path Absolute path to `parts/*.html` file.
 * @return array{attrs: array<string,mixed>, inner: list<array<int|string,mixed>>}|null
 */
function wpis_get_group_variation_data_from_part_file( $path ) {
	if ( ! is_string( $path ) || ! is_readable( $path ) || ! function_exists( 'parse_blocks' ) ) {
		return null;
	}

	$raw = file_get_contents( $path );
	if ( ! is_string( $raw ) || '' === trim( $raw ) ) {
		return null;
	}

	$tree = parse_blocks( $raw );
	if ( ! is_array( $tree ) ) {
		return null;
	}

	foreach ( $tree as $node ) {
		if ( ! is_array( $node ) || 'core/group' !== ( $node['blockName'] ?? '' ) ) {
			continue;
		}
		$attrs = ( isset( $node['attrs'] ) && is_array( $node['attrs'] ) ) ? $node['attrs'] : array();
		$inner = array();
		foreach ( (array) ( $node['innerBlocks'] ?? array() ) as $child ) {
			$tpl = wpis_parsed_block_to_block_template( $child );
			if ( null !== $tpl ) {
				$inner[] = $tpl;
			}
		}
		return array(
			'attrs' => $attrs,
			'inner' => $inner,
		);
	}

	return null;
}
