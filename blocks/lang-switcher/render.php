<?php
/**
 * Polylang language links or static fallback.
 *
 * @package WPIS
 */

if ( function_exists( 'pll_the_languages' ) ) {
	echo '<div class="wpis-lang-switcher" style="display:flex;gap:0.25rem;font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;text-transform:uppercase;">';
	pll_the_languages(
		array(
			'show_flags'       => 0,
			'show_names'       => 1,
			'display_names_as' => 'slug',
		)
	);
	echo '</div>';
} else {
	echo '<div class="wpis-lang-switcher" style="font-family:var(--wp--preset--font-family--jetbrains-mono);font-size:0.625rem;"><span>EN</span></div>';
}
