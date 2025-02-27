<?php
/**
 * Plugin Name: Rise Checkout
 * Description: Enhanced WooCommerce checkout with a seamless single-page multi-step with tracking for abandoned carts.
 * Version: 1.0.0-dev
 * Author: RiseWP
 * Author URI: https://risewp.github.io
 * Text Domain: risecheckout
 * Domain Path: /languages/
 * Requires at least: 6.7
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'RISECHECKOUT_PLUGIN_FILE' ) ) {
	define( 'RISECHECKOUT_PLUGIN_FILE', __FILE__ );
}

function risecheckout_includes() {
	require __DIR__ . '/includes/constants.php';
	require __DIR__ . '/includes/i18n.php';
	require __DIR__ . '/includes/locations.php';
	require __DIR__ . '/includes/fields.php';
	require __DIR__ . '/includes/conditionals.php';

	if ( is_admin() ) {
		require __DIR__ . '/includes/admin.php';
		require __DIR__ . '/includes/settings.php';
	}

	if ( risecheckout_is_request_frontend() || risecheckout_is_rest_api_request() ) {
		risecheckout_frontend_includes();
	}

	require __DIR__ . '/includes/theme-support.php';
}
risecheckout_includes();

function risecheckout_frontend_includes() {
	require __DIR__ . '/includes/template.php';
	require __DIR__ . '/includes/frontend-scripts.php';
}

function risecheckout_is_rest_api_request() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return false;
	}

	$rest_prefix = trailingslashit( rest_get_url_prefix() );
	return ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
}

function risecheckout_is_request_frontend() {
	return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! risecheckout_is_rest_api_request();
}
