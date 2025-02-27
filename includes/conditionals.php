<?php
/**
 * Conditionals functions for RiseCheckout.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if the current page is the checkout page.
 *
 * @return bool True if checkout page, false otherwise.
 */
function risecheckout_is_checkout() {
	return function_exists( 'is_checkout' ) && is_checkout();
}

/**
 * Check if the current page is the order received page.
 *
 * @return bool True if order received page, false otherwise.
 */
function risecheckout_is_order_received_page() {
	return function_exists( 'is_order_received_page' ) && is_order_received_page();
}

/**
 * Check if the current theme supports WooCommerce.
 *
 * @return bool True if the theme supports WooCommerce, false otherwise.
 */
function risecheckout_current_theme_supports_woocommerce() {
	return (bool) current_theme_supports( 'woocommerce' );
}
