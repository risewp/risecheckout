<?php
/**
 * Impreza Theme Support Functions
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues and dequeues styles and scripts for Impreza theme.
 *
 * @return void
 */
function risecheckout_us_theme_styles_scripts() {
	$styles = array(
		'style'  => false,
		'header' => false,
		'theme'  => false,
	);
	risecheckout_us_theme_dequeue_styles( $styles );

	$scripts = array(
		'core',
	);
	risecheckout_us_theme_dequeue_scripts( $scripts );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_us_theme_styles_scripts', 13 );

/**
 * Dequeues specified styles with prefix.
 *
 * @param array $styles List of styles to dequeue.
 * @return void
 */
function risecheckout_us_theme_dequeue_styles( $styles ) {
	risecheckout_dequeue_styles( $styles, $prefix = 'us-fallback-' );
}

/**
 * Dequeues specified scripts with prefix.
 *
 * @param array $scripts List of scripts to dequeue.
 * @return void
 */
function risecheckout_us_theme_dequeue_scripts( $scripts ) {
	risecheckout_dequeue_scripts( $scripts, $prefix = 'us-fallback-' );
}

/**
 * Registers and enqueues custom FontAwesome font.
 *
 * @return void
 */
function risecheckout_us_theme_fontawesome() {
	if ( ! risecheckout_is_checkout() || risecheckout_is_order_received_page() ) {
		return;
	}
	$path = '/fonts/fa-light-300.woff2';
	if ( file_exists( get_template_directory() . $path ) ) {
		wp_register_style( 'rcfontawesome', false, array(), RISECHECKOUT_VERSION );
		wp_add_inline_style(
			'rcfontawesome',
			sprintf(
				"@font-face{font-display:block;font-style:normal;font-family:fontawesome;font-weight:300;src:url('%s') format('woff2')}",
				get_template_directory_uri() . $path
			)
		);
		wp_enqueue_style( 'rcfontawesome' );
	}
}
add_action( 'wp_enqueue_scripts', 'risecheckout_us_theme_fontawesome' );
