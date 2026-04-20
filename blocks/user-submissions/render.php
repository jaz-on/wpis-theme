<?php
/**
 * List current user's quotes.
 *
 * @package WPIS
 */

if ( ! is_user_logged_in() ) {
	return;
}

$q = new WP_Query(
	array(
		'post_type'      => 'quote',
		'post_status'    => 'any',
		'author'         => get_current_user_id(),
		'posts_per_page' => 20,
	)
);

if ( ! $q->have_posts() ) {
	echo '<p>' . esc_html__( 'No submissions yet.', 'wpis-theme' ) . '</p>';
	return;
}

echo '<ul class="wpis-user-subs" style="list-style:none;padding:0;">';
while ( $q->have_posts() ) {
	$q->the_post();
	echo '<li style="padding:0.75rem 0;border-bottom:1px solid currentColor;">';
	echo '<div>' . esc_html( wp_strip_all_tags( get_the_content() ) ) . '</div>';
	echo '<div style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;">' . esc_html( get_post_status() ) . ' · ' . esc_html( get_the_date() ) . '</div>';
	echo '</li>';
}
echo '</ul>';
wp_reset_postdata();
