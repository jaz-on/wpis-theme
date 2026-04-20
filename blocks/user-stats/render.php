<?php
/**
 * Contributor stats grid.
 *
 * @package WPIS
 */

if ( ! is_user_logged_in() || ! function_exists( 'wpis_get_user_stats' ) ) {
	return;
}

$stats = wpis_get_user_stats( get_current_user_id() );

?>
<div class="wpis-user-stats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:1rem;">
	<div class="wpis-stat-card" style="border:1px solid currentColor;padding:1rem;">
		<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;color:var(--wp--preset--color--muted);"><?php esc_html_e( 'Total', 'wpis-theme' ); ?></div>
		<div style="font-size:1.75rem;"><?php echo esc_html( (string) $stats['total_submitted'] ); ?></div>
	</div>
	<div class="wpis-stat-card" style="border:1px solid currentColor;padding:1rem;">
		<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;color:var(--wp--preset--color--muted);"><?php esc_html_e( 'Validated', 'wpis-theme' ); ?></div>
		<div style="font-size:1.75rem;"><?php echo esc_html( (string) $stats['validated'] ); ?></div>
	</div>
	<div class="wpis-stat-card" style="border:1px solid currentColor;padding:1rem;">
		<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;color:var(--wp--preset--color--muted);"><?php esc_html_e( 'Rate %', 'wpis-theme' ); ?></div>
		<div style="font-size:1.75rem;"><?php echo esc_html( (string) $stats['acceptance_rate'] ); ?></div>
	</div>
	<div class="wpis-stat-card" style="border:1px solid currentColor;padding:1rem;">
		<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;color:var(--wp--preset--color--muted);"><?php esc_html_e( 'Pending', 'wpis-theme' ); ?></div>
		<div style="font-size:1.75rem;"><?php echo esc_html( (string) $stats['pending'] ); ?></div>
	</div>
</div>
