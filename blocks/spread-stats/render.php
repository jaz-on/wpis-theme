<?php
/**
 * Simple spread / distribution stats (placeholder-friendly).
 *
 * @package WPIS
 */

$counts = wp_count_posts( 'quote' );
$pub    = isset( $counts->publish ) ? (int) $counts->publish : 0;
$pend   = isset( $counts->pending ) ? (int) $counts->pending : 0;

$sent = get_terms( array( 'taxonomy' => 'sentiment', 'hide_empty' => false ) );
$sent_n = is_array( $sent ) && ! is_wp_error( $sent ) ? count( $sent ) : 0;

?>
<div class="wpis-spread-stats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;">
	<div style="border:1px solid currentColor;padding:1rem;">
		<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;"><?php esc_html_e( 'Published', 'wpis-theme' ); ?></div>
		<div style="font-size:1.5rem;"><?php echo esc_html( (string) $pub ); ?></div>
	</div>
	<div style="border:1px solid currentColor;padding:1rem;">
		<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;"><?php esc_html_e( 'Pending', 'wpis-theme' ); ?></div>
		<div style="font-size:1.5rem;"><?php echo esc_html( (string) $pend ); ?></div>
	</div>
	<div style="border:1px solid currentColor;padding:1rem;">
		<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;"><?php esc_html_e( 'Sentiment labels', 'wpis-theme' ); ?></div>
		<div style="font-size:1.5rem;"><?php echo esc_html( (string) $sent_n ); ?></div>
	</div>
</div>
