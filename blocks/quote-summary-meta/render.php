<?php
/**
 * Quote card footer meta (counter, platform) inside query loops.
 *
 * @package WPIS
 */

$post_id = get_the_ID();
if ( ! $post_id || 'quote' !== get_post_type( $post_id ) ) {
	return;
}

$counter = (int) get_post_meta( $post_id, '_wpis_counter', true );
if ( $counter < 1 ) {
	$counter = 1;
}
$plat = (string) get_post_meta( $post_id, '_wpis_source_platform', true );

?>
<div class="wpis-quote-card__footer wpis-quote-card__footer--static">
	<span class="wpis-quote-card__badge" aria-label="<?php esc_attr_e( 'Echo count', 'wpis-theme' ); ?>">×<?php echo esc_html( (string) $counter ); ?></span>
	<?php if ( '' !== $plat ) : ?>
		<span class="wpis-quote-card__plat"><?php echo esc_html( strtoupper( $plat ) ); ?></span>
	<?php endif; ?>
</div>
