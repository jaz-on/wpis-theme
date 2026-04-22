<?php
/**
 * Title: 404 empty body
 * Slug: wpis-theme/empty-body
 * Inserter: no
 *
 * @package wpis-theme
 */
?>
<!-- wp:group {"tagName":"main","className":"is-style-wpis-empty-state","layout":{"type":"constrained","contentSize":"560px"}} -->
<main class="wp-block-group is-style-wpis-empty-state">
	<!-- wp:paragraph {"className":"wpis-empty-symbol"} -->
	<p class="wpis-empty-symbol">is</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php esc_html_e( 'Nothing here yet', 'wpis-theme' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'This page does not exist, or the quote you are looking for has been removed. Try the feed or submit a new one.', 'wpis-theme' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"0.625rem"}}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/"><?php esc_html_e( 'Back to the feed', 'wpis-theme' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/submit/"><?php esc_html_e( 'Submit a quote', 'wpis-theme' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</main>
<!-- /wp:group -->
