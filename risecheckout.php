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

require __DIR__ . '/includes/constants.php';
require __DIR__ . '/includes/i18n.php';
require __DIR__ . '/includes/locations.php';
require __DIR__ . '/includes/fields.php';
require __DIR__ . '/includes/conditionals.php';
require __DIR__ . '/includes/theme-support.php';
require __DIR__ . '/includes/template.php';
require __DIR__ . '/includes/frontend-scripts.php';

if ( is_admin() ) {
	require __DIR__ . '/includes/admin.php';
	require __DIR__ . '/includes/settings.php';
}
