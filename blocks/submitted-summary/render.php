<?php
/**
 * Confirmation after submit (?t= token).
 *
 * @package WPIS
 */

$token = isset( $_GET['t'] ) ? sanitize_text_field( wp_unslash( $_GET['t'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( '' === $token ) {
	echo '<div class="wpis-confirm"><p class="wpis-confirm__sub">' . esc_html__( 'Thanks for contributing.', 'wpis-theme' ) . '</p></div>';
	return;
}

$pid = get_transient( 'wpis_submit_' . $token );
if ( ! $pid ) {
	echo '<div class="wpis-confirm"><p class="wpis-confirm__sub">' . esc_html__( 'Submission confirmation expired or invalid.', 'wpis-theme' ) . '</p></div>';
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
<div class="wpis-confirm">
	<div class="wpis-confirm__mark" aria-hidden="true">✓</div>
	<h1 class="wpis-confirm__title"><?php esc_html_e( 'Received', 'wpis-theme' ); ?></h1>
	<p class="wpis-confirm__sub"><?php esc_html_e( 'Thanks, your submission is being reviewed.', 'wpis-theme' ); ?></p>
	<p class="wpis-confirm__queue"><?php echo esc_html( sprintf( /* translators: %d pending */ __( 'There are currently %d submissions pending moderation.', 'wpis-theme' ), (int) $pending ) ); ?></p>
	<div class="wpis-confirm__preview"><?php echo wp_kses_post( wpautop( $post->post_content ) ); ?></div>
</div>
