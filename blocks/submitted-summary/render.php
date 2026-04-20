<?php
/**
 * Confirmation after submit (?t= token).
 *
 * @package WPIS
 */

$token = isset( $_GET['t'] ) ? sanitize_text_field( wp_unslash( $_GET['t'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( '' === $token ) {
	echo '<p>' . esc_html__( 'Thanks for contributing.', 'wpis-theme' ) . '</p>';
	return;
}

$pid = get_transient( 'wpis_submit_' . $token );
if ( ! $pid ) {
	echo '<p>' . esc_html__( 'Submission confirmation expired or invalid.', 'wpis-theme' ) . '</p>';
	return;
}

$post = get_post( (int) $pid );
if ( ! $post ) {
	return;
}

$pending = ( new WP_Query(
	array(
		'post_type'      => 'quote',
		'post_status'    => 'pending',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
) )->found_posts;

?>
<div class="wpis-submitted-summary">
	<p><?php esc_html_e( 'Thanks, your submission is being reviewed.', 'wpis-theme' ); ?></p>
	<p><?php echo esc_html( sprintf( /* translators: %d pending */ __( 'There are currently %d submissions pending moderation.', 'wpis-theme' ), (int) $pending ) ); ?></p>
	<blockquote style="border-left:3px solid currentColor;padding-left:1rem;"><?php echo wp_kses_post( wpautop( $post->post_content ) ); ?></blockquote>
</div>
