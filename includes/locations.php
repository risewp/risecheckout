<?php
/**
 * Locations file for RiseCheckout plugin.
 *
 * @package RiseCheckout
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the plugin URL without trailing slash.
 *
 * @return string Plugin URL.
 */
function risecheckout_plugin_url() {
	return untrailingslashit( plugins_url( '/', RISECHECKOUT_PLUGIN_FILE ) );
}

/**
 * Get the plugin directory path without trailing slash.
 *
 * @return string Plugin directory path.
 */
function risecheckout_plugin_path() {
	return untrailingslashit( plugin_dir_path( RISECHECKOUT_PLUGIN_FILE ) );
}
