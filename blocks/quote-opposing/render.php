<?php
/**
 * Linked opposing quote (single quote context).
 *
 * @package WPIS
 */

if ( ! is_singular( 'quote' ) ) {
	return;
}

$post_id = get_the_ID();
if ( ! $post_id ) {
	return;
}

$opp_id = (int) get_post_meta( $post_id, '_wpis_opposing_quote_id', true );
if ( $opp_id <= 0 ) {
	return;
}

$opp = get_post( $opp_id );
if ( ! $opp || 'quote' !== $opp->post_type ) {
	return;
}

$link = get_permalink( $opp_id );

$opp_claim = '';
$terms     = get_the_terms( $opp, 'claim_type' );
if ( is_array( $terms ) && ! empty( $terms ) && $terms[0] instanceof WP_Term ) {
	$opp_claim = $terms[0]->name;
}
$opp_counter = (int) get_post_meta( $opp_id, '_wpis_counter', true );
if ( $opp_counter < 1 ) {
	$opp_counter = 1;
}
$opp_plat = (string) get_post_meta( $opp_id, '_wpis_source_platform', true );

?>
<div class="wpis-against-block">
	<div class="wpis-against-label"><?php esc_html_e( 'Someone disagrees', 'wpis-theme' ); ?></div>
	<div class="wpis-against-quote"><?php echo wp_kses_post( wpautop( $opp->post_content ) ); ?></div>
	<div class="wpis-against-meta">
		<?php if ( '' !== $opp_claim ) : ?>
			<span class="wpis-against-claim"><?php echo esc_html( $opp_claim ); ?></span>
		<?php endif; ?>
		<span class="wpis-quote-card__badge" aria-label="<?php esc_attr_e( 'Echo count', 'wpis-theme' ); ?>">×<?php echo esc_html( (string) $opp_counter ); ?></span>
		<?php if ( '' !== $opp_plat ) : ?>
			<span class="wpis-against-plat"><?php echo esc_html( strtoupper( $opp_plat ) ); ?></span>
		<?php endif; ?>
		<a class="wpis-against-more" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Open opposing quote', 'wpis-theme' ); ?></a>
	</div>
</div>
