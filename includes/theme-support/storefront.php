<?php
/**
 * Storefront theme support custom scripts and styles.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues and dequeues Storefront theme scripts and styles.
 */
function risecheckout_storefront_scripts() {
	$styles = array(
		'style' => true,
		'icons' => true,
		'fonts' => false,
	);
	risecheckout_storefront_dequeue_styles( $styles );

	wp_dequeue_script( 'storefront-navigation' );
}
add_action( 'wp_enqueue_scripts', 'risecheckout_storefront_scripts', 11 );

/**
 * Dequeues Storefront styles based on the provided array.
 *
 * @param array $styles List of styles to dequeue.
 */
function risecheckout_storefront_dequeue_styles( $styles ) {
	risecheckout_dequeue_styles( $styles, $prefix = 'storefront-' );
}

/**
 * Dequeues Storefront scripts based on the provided array.
 *
 * @param array $scripts List of scripts to dequeue.
 */
function risecheckout_storefront_dequeue_scripts( $scripts ) {
	risecheckout_dequeue_scripts( $scripts, $prefix = 'storefront-' );
}

/**
 * Dequeues Gutenberg block styles for Storefront.
 */
function risecheckout_storefront_block_assets() {
	$styles = array(
		'gutenberg-blocks' => true,
	);
	risecheckout_storefront_dequeue_styles( $styles );
}
add_action( 'enqueue_block_assets', 'risecheckout_storefront_block_assets', 11 );

/**
 * Dequeues WooCommerce integration scripts and styles for Storefront.
 */
function risecheckout_storefront_wc_integrations_scripts() {
	$styles = array(
		'woocommerce-brands-style' => true,
	);
	risecheckout_storefront_dequeue_styles( $styles );

	$scripts = array(
		'woocommerce-brands',
	);
	risecheckout_storefront_dequeue_scripts( $scripts );
}

/**
 * Dequeues WooCommerce scripts and styles for Storefront.
 */
function risecheckout_storefront_wc_scripts() {
	$styles = array(
		'woocommerce-style' => true,
	);
	risecheckout_storefront_dequeue_styles( $styles );

	$scripts = array(
		'header-cart',
		'handheld-footer-bar',
	);
	risecheckout_storefront_dequeue_scripts( $scripts );
}

/**
 * Initializes Storefront WooCommerce script and style dequeue functions.
 */
function risecheckout_storefront_wc() {
	add_action( 'wp_enqueue_scripts', 'risecheckout_storefront_wc_integrations_scripts', 100 );
	add_action( 'wp_enqueue_scripts', 'risecheckout_storefront_wc_scripts', 21 );
}
risecheckout_storefront_wc();

/**
 * Enqueues WooCommerce styles on the checkout page if necessary.
 *
 * @param array $styles List of styles to enqueue.
 * @return array Filtered list of styles.
 */
function risecheckout_wc_enqueue_styles( $styles ) {
	if ( risecheckout_is_checkout() && ! risecheckout_is_order_received_page() ) {
		remove_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
	}
	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'risecheckout_wc_enqueue_styles', 9 );
