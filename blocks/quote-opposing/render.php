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

?>
<div class="wpis-opposing" style="margin-top:2rem;padding:1rem;border:1px dashed currentColor;">
	<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;color:var(--wp--preset--color--muted);"><?php esc_html_e( 'Opposing view', 'wpis-theme' ); ?></div>
	<blockquote style="margin:0.5rem 0 0;font-size:1rem;">
		<?php echo wp_kses_post( wpautop( $opp->post_content ) ); ?>
	</blockquote>
	<p style="margin:0.75rem 0 0;"><a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Open quote', 'wpis-theme' ); ?></a></p>
</div>
