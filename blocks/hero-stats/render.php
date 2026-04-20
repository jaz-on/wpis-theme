<?php
/**
 * Hero statistics row.
 *
 * @package WPIS
 */

$counts = wp_count_posts( 'quote' );
$pub    = isset( $counts->publish ) ? (int) $counts->publish : 0;

$terms = get_terms(
	array(
		'taxonomy'   => 'claim_type',
		'hide_empty' => false,
	)
);
$claim_n = is_array( $terms ) && ! is_wp_error( $terms ) ? count( $terms ) : 0;

$langs = 1;
if ( function_exists( 'pll_languages_list' ) ) {
	$list = pll_languages_list();
	$langs = is_array( $list ) ? count( $list ) : 1;
}

?>
<div class="wpis-hero-stats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;padding:1rem 0;border-top:1px dashed currentColor;font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.75rem;color:var(--wp--preset--color--muted);">
	<div><strong style="display:block;font-size:1.5rem;font-family:var(--wp--preset--font-family--fraunces);color:var(--wp--preset--color--ink);"><?php echo esc_html( (string) $pub ); ?></strong><?php esc_html_e( 'Quotes', 'wpis-theme' ); ?></div>
	<div><strong style="display:block;font-size:1.5rem;font-family:var(--wp--preset--font-family--fraunces);color:var(--wp--preset--color--ink);"><?php echo esc_html( (string) $claim_n ); ?></strong><?php esc_html_e( 'Topics', 'wpis-theme' ); ?></div>
	<div><strong style="display:block;font-size:1.5rem;font-family:var(--wp--preset--font-family--fraunces);color:var(--wp--preset--color--ink);"><?php echo esc_html( (string) $langs ); ?></strong><?php esc_html_e( 'Languages', 'wpis-theme' ); ?></div>
</div>
