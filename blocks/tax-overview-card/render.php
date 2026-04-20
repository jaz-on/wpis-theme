<?php
/**
 * Single taxonomy term card with count.
 *
 * @package WPIS
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

$taxonomy = isset( $attributes['taxonomy'] ) ? sanitize_key( $attributes['taxonomy'] ) : 'claim_type';
$term_id  = isset( $attributes['termId'] ) ? (int) $attributes['termId'] : 0;

if ( $term_id <= 0 ) {
	return;
}

$term = get_term( $term_id, $taxonomy );
if ( ! $term || is_wp_error( $term ) ) {
	return;
}

$link = get_term_link( $term );
if ( is_wp_error( $link ) ) {
	$link = '#';
}

?>
<article class="wpis-tax-card" style="border:1px solid currentColor;padding:1.25rem;">
	<h3 style="margin-top:0;font-size:1.25rem;"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
	<p style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;color:var(--wp--preset--color--muted);">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %d quote count */
				_n( '%d quote', '%d quotes', (int) $term->count, 'wpis-theme' ),
				(int) $term->count
			)
		);
		?>
	</p>
</article>
