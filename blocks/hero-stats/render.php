<?php
/**
 * Hero statistics row (mockup: quotes, topics, languages, pending, platforms).
 *
 * @package WPIS
 */

$counts = wp_count_posts( 'quote' );
$pub    = isset( $counts->publish ) ? (int) $counts->publish : 0;
$pend   = isset( $counts->pending ) ? (int) $counts->pending : 0;

$terms = get_terms(
	array(
		'taxonomy'   => 'claim_type',
		'hide_empty' => false,
	)
);
$claim_n = is_array( $terms ) && ! is_wp_error( $terms ) ? count( $terms ) : 0;

$langs = 1;
if ( function_exists( 'pll_languages_list' ) ) {
	$list  = pll_languages_list();
	$langs = is_array( $list ) ? count( $list ) : 1;
}

global $wpdb;
$nplat = 0;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- lightweight aggregate for hero.
$row = $wpdb->get_row(
	$wpdb->prepare(
		"SELECT COUNT(DISTINCT pm.meta_value) AS n FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE p.post_type = %s AND p.post_status = %s AND pm.meta_key = %s AND pm.meta_value != ''",
		'quote',
		'publish',
		'_wpis_source_platform'
	),
	OBJECT
);
if ( $row && isset( $row->n ) ) {
	$nplat = (int) $row->n;
}

?>
<div class="wpis-hero-stats">
	<div class="wpis-hero-stats__cell">
		<strong class="wpis-hero-stats__num"><?php echo esc_html( (string) $pub ); ?></strong>
		<span class="wpis-hero-stats__label"><?php esc_html_e( 'Quotes collected', 'wpis-theme' ); ?></span>
	</div>
	<div class="wpis-hero-stats__cell">
		<strong class="wpis-hero-stats__num"><?php echo esc_html( (string) $claim_n ); ?></strong>
		<span class="wpis-hero-stats__label"><?php esc_html_e( 'Topics', 'wpis-theme' ); ?></span>
	</div>
	<div class="wpis-hero-stats__cell">
		<strong class="wpis-hero-stats__num"><?php echo esc_html( (string) $langs ); ?></strong>
		<span class="wpis-hero-stats__label"><?php esc_html_e( 'Languages', 'wpis-theme' ); ?></span>
	</div>
	<div class="wpis-hero-stats__cell">
		<strong class="wpis-hero-stats__num"><?php echo esc_html( (string) $pend ); ?></strong>
		<span class="wpis-hero-stats__label"><?php esc_html_e( 'Pending moderation', 'wpis-theme' ); ?></span>
	</div>
	<div class="wpis-hero-stats__cell">
		<strong class="wpis-hero-stats__num"><?php echo esc_html( (string) max( 0, $nplat ) ); ?></strong>
		<span class="wpis-hero-stats__label"><?php esc_html_e( 'Platforms sourced', 'wpis-theme' ); ?></span>
	</div>
</div>
