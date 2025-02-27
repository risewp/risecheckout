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
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'RISECHECKOUT_PLUGIN_FILE' ) ) {
	define( 'RISECHECKOUT_PLUGIN_FILE', __FILE__ );
}

/**
 * Includes necessary plugin files.
 *
 * @return void
 */
function risecheckout_includes() {
	if ( ! defined( 'RISECHECKOUT_ABSPATH' ) ) {
		define( 'RISECHECKOUT_ABSPATH', dirname( RISECHECKOUT_PLUGIN_FILE ) . '/' );
	}

	require RISECHECKOUT_ABSPATH . 'includes/constants.php';
	require RISECHECKOUT_ABSPATH . 'includes/i18n.php';
	require RISECHECKOUT_ABSPATH . 'includes/locations.php';
	require RISECHECKOUT_ABSPATH . 'includes/fields.php';
	require RISECHECKOUT_ABSPATH . 'includes/conditionals.php';
	require RISECHECKOUT_ABSPATH . 'includes/options.php';

	if ( is_admin() ) {
		require RISECHECKOUT_ABSPATH . 'includes/admin.php';
		require RISECHECKOUT_ABSPATH . 'includes/settings.php';
	}

	if ( risecheckout_is_request_frontend() || risecheckout_is_rest_api_request() ) {
		risecheckout_frontend_includes();
	}

	require RISECHECKOUT_ABSPATH . 'includes/theme-support.php';
}
risecheckout_includes();

/**
 * Includes frontend-related files.
 *
 * @return void
 */
function risecheckout_frontend_includes() {
	require RISECHECKOUT_ABSPATH . 'includes/template.php';
	require RISECHECKOUT_ABSPATH . 'includes/frontend-scripts.php';

	if ( 'yes' === get_option( 'risecheckout_multistep' ) ) {
		require RISECHECKOUT_ABSPATH . 'includes/multistep.php';
	}
}

/**
 * Determines if the current request is a REST API request.
 *
 * @return bool
 */
function risecheckout_is_rest_api_request() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return false;
	}

	$rest_prefix = trailingslashit( rest_get_url_prefix() );
	return ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
}

/**
 * Determines if the current request is a frontend request.
 *
 * @return bool
 */
function risecheckout_is_request_frontend() {
	return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! risecheckout_is_rest_api_request();
}
