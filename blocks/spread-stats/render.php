<?php
/**
 * Spread stats: archive summary or single-quote detail.
 *
 * @package WPIS
 */

if ( is_singular( 'quote' ) ) {
	$post_id = get_the_ID();
	$counter = $post_id ? (int) get_post_meta( $post_id, '_wpis_counter', true ) : 1;
	if ( $counter < 1 ) {
		$counter = 1;
	}
	$plat = $post_id ? (string) get_post_meta( $post_id, '_wpis_source_platform', true ) : '';
	$lang_n = 1;
	if ( $post_id && function_exists( 'pll_get_post_translations' ) ) {
		$map = pll_get_post_translations( $post_id );
		$lang_n = is_array( $map ) ? count( $map ) : 1;
	}
	?>
	<div class="wpis-spread-section">
		<div class="wpis-spread-title"><?php esc_html_e( 'How this claim spreads', 'wpis-theme' ); ?></div>
		<div class="wpis-spread-grid">
			<div class="wpis-spread-item">
				<span class="wpis-spread-big"><?php echo esc_html( (string) $counter ); ?></span>
				<span class="wpis-spread-small"><?php esc_html_e( 'Echo count', 'wpis-theme' ); ?></span>
			</div>
			<div class="wpis-spread-item">
				<span class="wpis-spread-big"><?php echo esc_html( '' !== $plat ? '1' : '—' ); ?></span>
				<span class="wpis-spread-small"><?php esc_html_e( 'Source platform', 'wpis-theme' ); ?></span>
			</div>
			<div class="wpis-spread-item">
				<span class="wpis-spread-big"><?php echo esc_html( (string) max( 1, $lang_n ) ); ?></span>
				<span class="wpis-spread-small"><?php esc_html_e( 'Languages linked', 'wpis-theme' ); ?></span>
			</div>
		</div>
	</div>
	<?php
	return;
}

$counts = wp_count_posts( 'quote' );
$pub    = isset( $counts->publish ) ? (int) $counts->publish : 0;
$pend   = isset( $counts->pending ) ? (int) $counts->pending : 0;

$sent = get_terms( array( 'taxonomy' => 'sentiment', 'hide_empty' => false ) );
$sent_n = is_array( $sent ) && ! is_wp_error( $sent ) ? count( $sent ) : 0;

?>
<div class="wpis-spread-stats wpis-spread-stats--archive">
	<div class="wpis-spread-item">
		<div class="wpis-spread-small"><?php esc_html_e( 'Published', 'wpis-theme' ); ?></div>
		<div class="wpis-spread-big"><?php echo esc_html( (string) $pub ); ?></div>
	</div>
	<div class="wpis-spread-item">
		<div class="wpis-spread-small"><?php esc_html_e( 'Pending', 'wpis-theme' ); ?></div>
		<div class="wpis-spread-big"><?php echo esc_html( (string) $pend ); ?></div>
	</div>
	<div class="wpis-spread-item">
		<div class="wpis-spread-small"><?php esc_html_e( 'Sentiment labels', 'wpis-theme' ); ?></div>
		<div class="wpis-spread-big"><?php echo esc_html( (string) $sent_n ); ?></div>
	</div>
</div>
